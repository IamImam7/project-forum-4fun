<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
     public function posts(Request $request)
    {
        // Mengambil data user yang sedang otentikasi
        $user = $request->user();

        // Mengambil postingan melalui relasi, memuat relasi lain, dan memberi paginasi
         $posts = $user->posts()
                  // UBAH BARIS INI: Tambahkan 'user' ke dalam with()
                  ->with(['categories', 'user'])
                  ->latest()
                  ->paginate(10);

        return response()->json($posts);
    }
    public function update(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users')->ignore($user->id),
        ],
        'current_password' => 'required_with:password|nullable|string',
        'password' => [
            'nullable',
            'string',
            Password::min(8)->mixedCase()->numbers(),
            'confirmed',
        ],
    ]);

    $user->name = $validated['name'];
    $user->email = $validated['email'];

    if (!empty($validated['password'])) {
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Password saat ini tidak cocok.'], 422);
        }

        // Hapus Hash::make() di sini
        $user->password = $validated['password'];
    }

    $user->save();

    return response()->json($user);
}

      public function publicPosts(User $user)
    {
        // Laravel akan otomatis mencari user berdasarkan 'name' dari URL
        $posts = $user->posts()
                  ->with(['user', 'categories', 'userVote', 'topComment.user:id,name'])
                  ->latest()
                  ->paginate(10);

    // Kembalikan data user DAN postingannya dalam satu respons
    return response()->json([
        'user' => $user,
        'posts' => $posts,
    ]);
    }
}
