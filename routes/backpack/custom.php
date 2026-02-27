<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', ''),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('users', 'UserCrudController');
    Route::crud('posts', 'PostCrudController');
    Route::crud('comments', 'CommentCrudController');
    Route::crud('likes', 'LikeCrudController');
    Route::crud('roles', 'RoleCrudController');
    Route::crud('permissions', 'PermissionCrudController');
    Route::crud('department', 'DepartmentCrudController');




    Route::get('ldap', [\App\Http\Controllers\Admin\LdapController::class, 'index']);
    Route::post('ldap', [\App\Http\Controllers\Admin\LdapController::class, 'login']);
    
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
