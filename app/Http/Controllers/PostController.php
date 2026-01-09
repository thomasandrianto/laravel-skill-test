<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->with('user')
            ->paginate(20);

        return PostResource::collection($posts);
    }
}
