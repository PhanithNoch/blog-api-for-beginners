<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class PostController extends Controller
{

    public function store(Request $request)
    {
        //1,validate request 
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'user_id' => 'required|exists:users,id'
        ]);

        $image = $request->file('image');
        if ($image) {
            $imagePath = $image->store('posts', 'public');
            $validated['image'] = $imagePath;
        }

        //2,create post
        $post = Post::create($validated); // no need to write sql query use ORM instead 

        //3,return response
        return response()->json($post, 200); // 200 success ,201,created, 401: unauthorized, 404:not found, 500:internal server error
    }

    public function index(Request $request)
    {

        $page = request()->integer('page', 1);

        $cacheKey = "posts_page_{$page}";

        $posts = Cache::remember($cacheKey, 600, function () {
            return Post::select([
                'id',
                'title',
                'image',
                'user_id',
                'created_at'
            ])
                ->with('user:id,name,image')
                ->with([
        'comments' => function ($query) {
            $query->select([
                    'id',
                    'content',
                    'user_id',
                    'post_id',
                    'created_at'
                ])
                ->latest()
                ->limit(3);
        }
    ])
                ->latest()
                ->paginate(10)
                ->toArray();
        });

        return response()->json($posts);

    }

    public function show($id)
    {
        $post = Post::find($id); // get single post from db is existing or not

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($post, 200);
    }


    public function destroy($id)
    {
        $post = Post::find($id); // get from db is existing or not

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->image) {
            // Delete the image file from storage
            \Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
