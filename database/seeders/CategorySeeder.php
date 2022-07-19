<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

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
            'title' => 'Class A'
        ]);
        
        Category::create([
            'title' => 'Class B'
        ]);

        Category::create([
            'title' => 'Class M'
        ]);
    }
}
