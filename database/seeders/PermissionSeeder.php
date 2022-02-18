<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'Create Post Permission',
            'Update Post Permission',
            'Delete Post Permission',
            'View Private Post Permission',
            'Create System Post'
        ];

        foreach ($permissions as $permission) {
            $slug = Str::slug($permission, '-');

            if (!Permission::where('slug', $slug)->exists()) {
                Permission::create([
                    'name' => $permission,
                    'slug' => $slug
                ]);
            }
        
        }
        //
    }
}
