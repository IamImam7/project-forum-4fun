<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
     public function posts(Category $category)
    {
        // Menggunakan Route Model Binding dengan slug
        $posts = $category->posts()
                     ->with(['user', 'categories', 'userVote', 'topComment.user:id,name'])
                     ->latest()
                     ->paginate(10);

    // Kembalikan data kategori DAN postingannya dalam satu respons
    return response()->json([
        'category' => $category,
        'posts' => $posts,
    ]);
    }
}
