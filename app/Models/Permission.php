<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    //relation ship with roles model
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'roles_permissions', 'permission_id', 'role_id');
    }

    //relation ship with users model
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'users_permissions', 'permission_id', 'user_id');
    }
}
