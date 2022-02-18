<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_permission = Permission::where('slug','create-tasks')->first();
        $admin_permission = Permission::where('slug', 'edit-users')->first();
 
        $user_role = new Role();
        $user_role->slug = 'user';
        $user_role->name = 'User_Name';
        $user_role->save();
        $user_role->permissions()->attach($user_permission);
 
        $admin_role = new Role();
        $admin_role->slug = 'admin';
        $admin_role->name = 'Admin_Name';
        $admin_role->save();
        $admin_role->permissions()->attach($admin_permission);
 
        $user_role = Role::where('slug','user')->first();
        $admin_role = Role::where('slug', 'admin')->first();
 
        $createTasks = new Permission();
        $createTasks->slug = 'create-tasks';
        $createTasks->name = 'Create Tasks';
        $createTasks->save();
        $createTasks->roles()->attach($user_role);
 
        $editUsers = new Permission();
        $editUsers->slug = 'edit-users';
        $editUsers->name = 'Edit Users';
        $editUsers->save();
        $editUsers->roles()->attach($admin_role);
 
        $user_role = Role::where('slug','user')->first();
        $admin_role = Role::where('slug', 'admin')->first();
        $user_perm = Permission::where('slug','create-tasks')->first();
        $admin_perm = Permission::where('slug','edit-users')->first();
    }
}
