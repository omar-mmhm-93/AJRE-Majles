<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DepartmentRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DepartmentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DepartmentCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Department::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/department');
        CRUD::setEntityNameStrings('department', 'departments');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name')
            ->label('Department')
            ->limit(9999);

        CRUD::addColumn([
            'name'      => 'manager_id',
            'label'     => 'Manager',
            'type'      => 'select',
            'entity'    => 'manager',
            'model'     => \App\Models\User::class,
            'attribute' => 'name_ar',
        ]);

        CRUD::addColumn([
            'name'      => 'parent_id',
            'label'     => 'Parent Department',
            'type'      => 'select',
            'entity'    => 'parent',
            'model'     => \App\Models\Department::class,
            'attribute' => 'name',
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(DepartmentRequest::class);

        CRUD::field('name')
            ->label('Department Name');

        CRUD::addField([
            'name'        => 'manager_id',
            'label'       => 'Manager',
            'type'        => 'select',
            'entity'      => 'manager',
            'model'       => \App\Models\User::class,
            'attribute'   => 'name_ar',
            'placeholder' => '— No Manager —',
        ]);

        CRUD::addField([
            'name'        => 'parent_id',
            'label'       => 'Parent Department',
            'type'        => 'select',
            'entity'      => 'parent',
            'model'       => \App\Models\Department::class,
            'attribute'   => 'name',
            'placeholder' => '— No Parent —',
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
    }
}
