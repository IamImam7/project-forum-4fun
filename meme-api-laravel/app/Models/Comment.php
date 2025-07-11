<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'post_id', 'body', 'parent_id'];
    
    protected $appends = ['vote_score', 'is_hidden'];

public function user()
{
    return $this->belongsTo(User::class);
}
public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function votes()
{
    return $this->hasMany(CommentVote::class);
}

public function getVoteScoreAttribute()
{
    return $this->votes()->sum('type');
}

public function getIsHiddenAttribute()
{
    // Hitung jumlah dislike (-1)
    $dislikes = $this->votes()->where('type', -1)->count();
    return $dislikes >= 5;
}
public function userVote()
    {
        // hasOne akan mengambil satu record saja
        return $this->hasOne(CommentVote::class)->where('user_id', auth()->id());
    }
}
