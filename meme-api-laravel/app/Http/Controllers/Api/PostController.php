<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
   public function store(Request $request)
{
   $validated = $request->validate([
        'title' => 'required|string|max:255',
        // Pastikan 'categories' adalah array dan tidak kosong
        'categories' => 'required|array|min:1',
        // Validasi setiap item di dalam array 'categories'
        'categories.*' => 'integer|exists:categories,id',
        'image' => 'required|image|max:2048',
        'is_nsfw' => 'boolean',
    ]);

    $imagePath = $request->file('image')->store('posts', 'public');
    $post = Post::create([
        'user_id' => auth()->id(),
        'title' => $validated['title'],
        'image_path' => $imagePath,
        'is_nsfw' => $request->boolean('is_nsfw'),
    ]);

    $post->categories()->sync($validated['categories']);

    return response()->json(['message' => 'Meme berhasil diunggah!'], 201);
}

     public function index(Request $request)
    {
 $sort = $request->query('sort', 'new');
    $query = Post::with([
        'user',
        'categories',
        'topComment.user:id,name',
        'userVote',// <-- Tambahkan ini
    ]);

    if ($sort === 'top') {
        // Menambahkan kolom virtual 'vote_score' yang berisi jumlah 'type' dari relasi votes
        // Lalu mengurutkan berdasarkan kolom virtual tersebut
        $query->withCount(['votes as vote_score' => function ($query) {
            $query->select(DB::raw("sum(type)"));
        }])->orderByDesc('vote_score');

    } elseif ($sort === 'hot') {
        // Algoritma 'hot' sederhana: skor vote diberi bobot berdasarkan waktu
        // Semakin baru post, semakin tinggi skornya.
        $query->select('posts.*')
              ->leftJoin('votes', 'posts.id', '=', 'votes.post_id')
              ->selectRaw('SUM(votes.type) as score')
              ->selectRaw('posts.created_at as post_time')
              ->groupBy('posts.id')
              // Rumus sederhana: skor + (waktu post dalam detik / faktor pembagi)
              ->orderByRaw('SUM(votes.type) + (UNIX_TIMESTAMP(posts.created_at) / 45000) DESC');
    } else {
        // Default: 'new'
        $query->latest();
    }

    $posts = $query->paginate(10);

    return response()->json($posts);
    }
    public function vote(Request $request, Post $post)
{
    $validator = Validator::make($request->all(), [
        'type' => 'required|in:up,down', // Hanya menerima 'up' atau 'down'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $voteType = ($request->type === 'up') ? 1 : -1;
    $user = auth()->user();

    $vote = $post->votes()->where('user_id', $user->id)->first();

    if ($vote) {
        // Jika user sudah vote dan vote-nya sama, hapus vote (batal vote)
        if ($vote->type == $voteType) {
            $vote->delete();
            return response()->json(['message' => 'Vote dibatalkan.']);
        }
        // Jika user ganti vote (dari up ke down atau sebaliknya)
        $vote->update(['type' => $voteType]);
    } else {
        // Jika user belum vote sama sekali
        $post->votes()->create([
            'user_id' => $user->id,
            'type' => $voteType,
        ]);
    }

    return response()->json(['message' => 'Vote berhasil disimpan.']);
}
 public function show(Post $post)
    {
        // Menggunakan Route Model Binding dari Laravel
        // dan memuat relasi yang dibutuhkan.
         return response()->json($post->load(['user', 'categories', 'votes']));
    }
public function destroy(Post $post)
{
    // 1. Otorisasi menggunakan Policy yang sudah kita buat
    $this->authorize('delete', $post);

    // 2. Hapus file gambar dari storage
    Storage::disk('public')->delete($post->image_path);

    // 3. Hapus data post dari database
    $post->delete();

    return response()->json(['message' => 'Post berhasil dihapus.']);
}
public function search(Request $request)
{
    // Validasi bahwa parameter 'q' (query) ada
    $request->validate(['q' => 'required|string|max:100']);

    $searchTerm = $request->q;

    // Cari post yang judulnya mengandung kata kunci (LIKE query)
    $posts = Post::with(['user', 'categories','userVote'])
                 ->where('title', 'LIKE', "%{$searchTerm}%")
                 ->latest()
                 ->paginate(10);

    return response()->json($posts);
}
}
