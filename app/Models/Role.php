<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    //relationship with user model
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'users_roles', 'role_id', 'user_id');
    }

    //relationship with permissions model
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission', 'roles_permissions', 'role_id', 'permission_id');
    }
}
