<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'required|string',
            'post_id' => 'required|exists:posts,id',
        ]);

        $userId = $request->user()->id;

        $comment = Comment::create([
            'content' => $validatedData['content'],
            'user_id' => $userId,
            'post_id' => $validatedData['post_id'],
        ]);

        return response()->json($comment, 200);
    }
}
