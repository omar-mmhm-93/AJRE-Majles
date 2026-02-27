<?php

namespace App\Services;

use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LdapSyncService
{
    public function sync($ldapUser, $ldapPass)
    {
        if (!str_contains($ldapUser, '@')) {
            $ldapUser .= "@ajre.ajman.ae";
        }

        $ldap = ldap_connect("ldap://192.168.59.70", 389);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 10);

        if (!@ldap_bind($ldap, $ldapUser, $ldapPass)) {
            throw new \Exception(ldap_error($ldap));
        }

        $baseDn = "OU=Users,OU=AJRE,DC=AJRE,DC=Ajman,DC=ae";
        $filter = "(&(objectClass=person)(sAMAccountName=*))";

        $attributes = [
            'samaccountname',
            'mail',
            'l',
            'displayname',
            'department',
            'manager',
            'telephonenumber',
            'thumbnailphoto',
            'distinguishedName',
            'title',
            'streetaddress'
        ];

        $search  = ldap_search($ldap, $baseDn, $filter, $attributes);
        $entries = ldap_get_entries($ldap, $search);

        return DB::transaction(function () use ($entries) {

            $ldapUsers = [];
            $dnToUsername = [];

            for ($i = 0; $i < $entries['count']; $i++) {

                $u = $entries[$i];
                $username = $u['samaccountname'][0] ?? null;
                if (!$username) continue;

                $dn = $u['distinguishedname'][0] ?? null;

                $ldapUsers[$username] = [
                    'username'   => $username,
                    'name_ar'    => $u['l'][0] ?? $username,
                    'name_en'    => $u['displayname'][0] ?? $username,
                    'email'      => $u['mail'][0] ?? null,
                    'phone'      => $u['telephonenumber'][0] ?? null,
                    'department' => trim($u['department'][0] ?? ''),
                    'managerDn'  => $u['manager'][0] ?? null,
                    'photo'      => $u['thumbnailphoto'][0] ?? null,
                    'dn'         => $dn,
                    'title_ar'   => $u['streetaddress'][0] ?? ( $u['title'][0] ?? ''),
                    'title_en'   => $u['title'][0] ?? ($u['streetaddress'][0]  ?? '')
                ];

                if ($dn) {
                    $dnToUsername[$dn] = $username;
                }
            }

            /* ======================
               CREATE DEPARTMENTS
            ====================== */

            $deptMap = [];

            foreach ($ldapUsers as $u) {

                if (!$u['department']) continue;

                $department = Department::firstOrCreate([
                    'name' => $u['department']
                ]);

                $deptMap[$u['department']] = $department->id;
            }

            /* ======================
               CREATE / UPDATE USERS
            ====================== */

            $userIdMap = [];

            foreach ($ldapUsers as $u) {

                $user = User::updateOrCreate(
                    ['username' => $u['username']],
                    [
                        'name_ar'       => $u['name_ar'],
                        'name_en'       => $u['name_en'],
                        'email'         => $u['email'],
                        'mobile'        => $u['phone'],
                        'title_ar'      => $u['title_ar'],
                        'title_en'      => $u['title_en'],
                        'department_id' => $deptMap[$u['department']] ?? null,                        
                    ]
                );

                if ($u['photo']) {
                    $photoPath = $this->savePhoto($user->username, $u['photo']);

                    $user->update([
                        'profile_picture' => $photoPath
                    ]);
                }

                $userIdMap[$u['username']] = $user->id;
            }

            /* ======================
               LINK MANAGERS
            ====================== */

            foreach ($ldapUsers as $u) {

                if (!$u['managerDn']) continue;
                if (!isset($dnToUsername[$u['managerDn']])) continue;

                $managerUsername = $dnToUsername[$u['managerDn']];
                if (!isset($userIdMap[$managerUsername])) continue;

                User::where('username', $u['username'])
                    ->update([
                        'manager_id' => $userIdMap[$managerUsername]
                    ]);
            }


            foreach ($deptMap as $deptName => $deptId) {

                $users = User::where('department_id', $deptId)->get();

                foreach ($users as $u) {

                    if (!$u->manager_id) {
                        Department::where('id', $deptId)
                            ->update(['manager_id' => $u->id]);
                        break;
                    }

                    $manager = User::find($u->manager_id);

                    if (!$manager || $manager->department_id != $deptId) {

                        Department::where('id', $deptId)
                            ->update(['manager_id' => $u->id]);
                        break;
                    }
                }
            }


            Department::query()->update([
                'parent_id' => null
            ]);

            $departments = Department::with('manager.manager')->get();

            foreach ($departments as $department) {

                if (!$department->manager_id) continue;

                $manager = $department->manager;
                if (!$manager || !$manager->manager_id) continue;

                $upperManager = $manager->manager;
                if (!$upperManager) continue;

                if (
                    $upperManager->department_id &&
                    $upperManager->department_id != $department->id
                ) {
                    $department->update([
                        'parent_id' => $upperManager->department_id
                    ]);
                }
            }

            return true;
        });
    }

    private function savePhoto($username, $binary)
    {
        if (!$binary) return null;

        $path = "ldap_photos/{$username}.jpg";

        Storage::disk('public')->put($path, $binary);

        return "{$path}";
    }
}