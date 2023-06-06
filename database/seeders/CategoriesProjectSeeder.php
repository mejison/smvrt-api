<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::truncate();

        $data = [
            'Category #1',
            'Category #2',
            'Category #3',
            'Category #4',
            'Category #5',
            'Category #6',
        ];

        collect($data)->each(function($category) {
            Category::create([
                'name' => $category
            ]);
        });
    }
}
