<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/users');
        CRUD::setEntityNameStrings('user', 'users');
    }

    protected function setupShowOperation()
    {
        CRUD::column('username');
        CRUD::column('name_ar');
        CRUD::column('name_en');
        CRUD::column('title_ar');
        CRUD::column('title_en');
        CRUD::column('mobile');
        CRUD::column('status');
        CRUD::column('email');

        CRUD::addColumn([
            'name'      => 'department_id',
            'label'     => 'Department',
            'type'      => 'select',
            'entity'    => 'department',
            'model'     => \App\Models\Department::class,
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name'      => 'manager_id',
            'label'     => 'Manager',
            'type'      => 'select',
            'entity'    => 'manager',
            'model'     => \App\Models\User::class,
            'attribute' => 'name_ar',
        ]);

        CRUD::addColumn([
            'name'  => 'profile_picture',
            'label' => 'Profile Picture',
            'type'  => 'image',
            'disk'  => 'public',
        ]);

        CRUD::column('created_at');
        CRUD::column('updated_at');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('username');
        CRUD::column('name_ar')->label('Name (AR)');
        CRUD::column('name_en')->label('Name (EN)');
        CRUD::column('title_ar')->label('Title');
        CRUD::column('email');
        CRUD::column('mobile');

        CRUD::addColumn([
            'name'      => 'department_id',
            'label'     => 'Department',
            'type'      => 'select',
            'entity'    => 'department',
            'model'     => \App\Models\Department::class,
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name'      => 'manager_id',
            'label'     => 'Manager',
            'type'      => 'select',
            'entity'    => 'manager',
            'model'     => \App\Models\User::class,
            'attribute' => 'name_ar',
        ]);

        CRUD::column('status');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::field('username');

        CRUD::field('name_ar')->label('Name (AR)');
        CRUD::field('name_en')->label('Name (EN)');

        CRUD::field('email');
        CRUD::field('mobile');
        CRUD::field('title_ar')->label('Title (AR)');
        CRUD::field('title_en')->label('Title (EN)');

        CRUD::addField([
            'name'        => 'department_id',
            'label'       => 'Department',
            'type'        => 'select',
            'entity'      => 'department',
            'model'       => \App\Models\Department::class,
            'attribute'   => 'name',
            'placeholder' => '— None —',
        ]);

        CRUD::addField([
            'name'        => 'manager_id',
            'label'       => 'Manager',
            'type'        => 'select',
            'entity'      => 'manager',
            'model'       => \App\Models\User::class,
            'attribute'   => 'name_ar',
            'placeholder' => '— None —',
        ]);

        CRUD::addField([
            'name'  => 'status',
            'type'  => 'select_from_array',
            'options' => [
                'enabled' => 'Active',
                'disabled' => 'Inactive',
            ],
            'default' => 1,
        ]);

        CRUD::addField([
            'name'  => 'profile_picture',
            'label' => 'Profile Picture',
            'type'  => 'upload',
            'upload' => true,
            'disk'   => 'public',
        ]);

        CRUD::addField([
            'name'  => 'password',
            'label' => 'Password',
            'type'  => 'password',
        ]);

        CRUD::addField([
            'label'     => "Roles",
            'type'      => 'checklist',
            'name'      => 'roles',
            'entity'    => 'roles',
            'attribute' => 'name',
            'model'     => \Spatie\Permission\Models\Role::class,
            'pivot'     => true,
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        CRUD::modifyField('password', [
            'hint' => 'Leave blank to keep current password'
        ]);
    }
}
