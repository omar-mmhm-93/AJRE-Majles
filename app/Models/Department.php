<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'departments';
    protected $primaryKey = 'id';
    public $timestamps = true;
    // protected $guarded = ['id'];
    // protected $hidden = [];
    protected $fillable = [
        'manager_id',
        'parent_id',
        'name',
        'created_at',
        'updated_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::saving(function ($department) {

            $p1 = $department->parent_id;
            $p2 = $department->id;
            if ($p1 == $p2 && $p1 != null) {
                throw new \Exception("Department $p1 cannot be parent of $p2");
            }

        });
    }

    public function __toString()
    {
        return $this->name ?? '';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function manager()
    {
        return $this->belongsTo(\App\Models\User::class, 'manager_id');
    }

    public function users()
    {
        return $this->hasMany(\App\Models\User::class, 'department_id');
    }

    public function parent()
    {
        return $this->belongsTo(\App\Models\Department::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(\App\Models\Department::class, 'parent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
