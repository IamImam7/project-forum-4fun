<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

     protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'image_path',
        'is_nsfw',
    ];

        protected $appends = ['vote_score'];
     public function user()
    {
        return $this->belongsTo(User::class);
    }


   public function categories()
{
    return $this->belongsToMany(Category::class);
}
    public function votes()
{
    return $this->hasMany(Vote::class);
}

    public function getVoteScoreAttribute()
{
     return (int) $this->votes()->sum('type');
}
public function comments()
{
    return $this->hasMany(Comment::class);
}

      public function userVote()
    {
        return $this->hasOne(Vote::class)->where('user_id', auth()->id());
    }
 public function topComment()
    {
        // Mengambil satu (hasOne) komentar (Comment) yang terhubung,
        // lalu mengurutkannya berdasarkan jumlah 'type' dari relasi votes-nya
        // untuk mendapatkan yang tertinggi.
        return $this->hasOne(Comment::class)
                    ->withCount(['votes as vote_score' => function ($query) {
                        $query->select(DB::raw("sum(type)"));
                    }])
                    ->orderByDesc('vote_score');
    }
}
