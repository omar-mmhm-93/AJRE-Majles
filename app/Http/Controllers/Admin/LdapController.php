<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\LdapSyncService;

class LdapController extends Controller
{
    public function index()
    {
        return view('ldap');
    }

    public function login(Request $request, LdapSyncService $service)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = $request->username;
        $password = $request->password;

        $service->sync($username, $password);

        \Alert::success('LDAP data imported successfully.')->flash();
        return redirect()->back();
    }
}