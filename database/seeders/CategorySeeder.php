<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fiction',         'slug' => 'fiction',         'description' => 'Novels, short stories, and other fictional works'],
            ['name' => 'Non-Fiction',     'slug' => 'non-fiction',     'description' => 'Biographies, history, science, and essays'],
            ['name' => 'Science Fiction', 'slug' => 'science-fiction', 'description' => 'Sci-fi and speculative fiction'],
            ['name' => 'Fantasy',         'slug' => 'fantasy',         'description' => 'Fantasy and magical realism'],
            ['name' => 'Mystery',         'slug' => 'mystery',         'description' => 'Mystery, thriller, and crime novels'],
            ['name' => 'Romance',         'slug' => 'romance',         'description' => 'Romance novels'],
            ['name' => 'Children',        'slug' => 'children',        'description' => 'Books for children and young adults'],
            ['name' => 'Academic',        'slug' => 'academic',        'description' => 'Textbooks and academic publications'],
            ['name' => 'Self-Help',       'slug' => 'self-help',       'description' => 'Self-improvement and motivational books'],
            ['name' => 'Historical',      'slug' => 'historical',      'description' => 'Historical fiction and non-fiction'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insertOrIgnore($category);
        }
    }
}
