<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name' => 'Category 1',
            'meta_title' => 'Category 1',
            'meta_description' => 'Category 1',
            'meta_keywords' => 'Category 1',
            'status' => 1,
            'type' => 'root',
            'parent_id' => 0,
            'image' => '',
        ]);
        
    }
}
