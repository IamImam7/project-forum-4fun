<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    /// Mengambil semua komentar untuk sebuah post
    public function index(Post $post)
    {
          return $post->comments()
                ->whereNull('parent_id')
                // Tambahkan 'userVote' di sini dan untuk replies
                ->with([
                    'user:id,name',
                    'votes',
                    'userVote', // <-- Untuk komentar utama
                    'replies.user:id,name',
                    'replies.votes',
                    'replies.userVote' // <-- Untuk balasan
                ])
                ->latest()
                ->get();
    }

    // Menyimpan komentar baru
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id'
    ]);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->body,
            'parent_id' => $request->parent_id,
        ]);

        // Muat relasi user agar bisa langsung ditampilkan di frontend
        $comment->load('user:id,name');

        return response()->json($comment, 201);
    }
    public function vote(Request $request, Comment $comment)
{
    $request->validate(['type' => 'required|in:up,down']);
    $voteType = ($request->type === 'up') ? 1 : -1;
    $user = auth()->user();

    $vote = $comment->votes()->where('user_id', $user->id)->first();

    if ($vote) {
        if ($vote->type == $voteType) {
            $vote->delete();
        } else {
            $vote->update(['type' => $voteType]);
        }
    } else {
        $comment->votes()->create(['user_id' => $user->id, 'type' => $voteType]);
    }
    return response()->json(['message' => 'Vote berhasil.']);
}
}
