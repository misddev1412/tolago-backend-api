<?php

namespace Database\Seeders;

use App\Models\Like;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LikeType;

class LikeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        LikeType::create([
            'name' => 'like',
            'icon' => 'figure/icon/reaction_1.png'
        ]);
        LikeType::create([
            'name' => 'haha',
            'icon' => 'figure/icon/reaction_2.png'
        ]);
        LikeType::create([
            'name' => 'love',
            'icon' => 'figure/icon/reaction_3.png'
        ]);
        LikeType::create([
            'name' => 'care',
            'icon' => 'figure/icon/reaction_4.png'
        ]);
        LikeType::create([
            'name' => 'angry',
            'icon' => 'figure/icon/reaction_5.png'
        ]);
        LikeType::create([
            'name' => 'sad',
            'icon' => 'figure/icon/reaction_6.png'
        ]);

        LikeType::create([
            'name' => 'wow',
            'icon' => 'figure/icon/reaction_7.png'
        ]);
    }
}
