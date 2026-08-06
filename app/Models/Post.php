<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'image', 'user_id'])]


class Post extends Model
{

// protected $with = ['comments']; // eager loading to avoid n+1 problem

     protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d-M-Y H:i:s',
            'updated_at' => 'date:Y-m-d',
            'user_id' => 'integer',
        ];
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
