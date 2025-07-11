<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Category::query()->delete();

        $categories = [
            'Random', 'Trending', 'Relatable', 'Dark Humor', 'Wholesome',
            'Satire & Ironi', 'Shitpost', 'Dank Meme', 'Classic Meme',
            'Template Remix', 'AI Generated', 'Anime & Manga', 'Game',
            'Movie & Series', 'K-Pop & J-Pop', 'Western Pop Culture',
            'Programmer Meme', 'Student Life', 'Meme Sejarah',
            'Science & Math Meme', 'Literature / Sastra Meme'
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category)
            ]);
        }
    }
}
