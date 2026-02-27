<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Create Permissions
        |--------------------------------------------------------------------------
        */

        Permission::firstOrCreate([
            'name' => 'login_to_admin_panel',
            'guard_name' => 'web'
        ]);

        Permission::firstOrCreate([
            'name' => 'sync_ldap',
            'guard_name' => 'web'
        ]);

        $modules = [    
            'users', 'departments', 'roles', 'permissions', 
            'posts', 'likes', 'comments'
        ];
        $actions = ['list', 'show', 'create', 'update', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => $module . "_" . $action,
                    'guard_name' => 'web'
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Create Admin Role
        |--------------------------------------------------------------------------
        */

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $adminRole->syncPermissions(Permission::all());

        /*
        |--------------------------------------------------------------------------
        | Create Admin User
        |--------------------------------------------------------------------------
        */

        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name_ar' => 'Admin',
                'name_en' => 'Admin',
                'username' => 'admin',
                'password' => 'admin',
            ]
        );

        $admin->assignRole($adminRole);
    }
}