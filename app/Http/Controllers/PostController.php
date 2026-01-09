<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->with('user')
            ->paginate(20);

        return PostResource::collection($posts);
    }

    public function show(Post $post)
    {
        abort_unless($post->isPublished(), 404);

        return new PostResource(
            $post->load('user')
        );
    }

    public function create()
    {
        return 'posts.create';
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_draft' => ['boolean'],
        ]);

        $post = $request->user()->posts()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_draft' => $validated['is_draft'] ?? true,
            'published_at' => $validated['published_at'] ?? null,
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return 'posts.edit';
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_draft' => ['boolean'],
        ]);

        $post->update($validated);

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
}
