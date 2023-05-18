<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return response()->view('posts.index', [
            'posts' => Post::orderBy('updated_at', 'desc')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return response()->view('posts.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        if ($request->hasFile('featured_image')) {
            $filePath = Storage::disk('public')->put('images/posts/featured_images', request()->file('featured_image'));
            $validated['featured_image'] = $filePath;
        }

        $create = Post::create($validated);

        if ($create) {
            session()->flash('notif.success', 'Berhasil');
            return redirect()->route('posts.index');
        }

        return abort(500);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        return response()->view('posts.show', [
            'post' => Post::findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response
    {
        return response()->view('posts.form', [
            'post' => Post::findOrFail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id): RedirectResponse
    {
        $post = Post::findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('featured_image')) {
            Storage::disk('public')->delete($post->featured_image);

            $filePath = Storage::disk('public')->put('images/posts/featured_images', request()->file('featured_image'));
            $validated['featured_image'] = $filePath;
        }

        $update = $post->update($validated);
        if ($update) {
            session()->flash('notif.success', 'Berhasil');
            return redirect()->route('posts.index');
        }
        return abort(500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $post = Post::findOrFail($id);
        Storage::disk('public')->delete($post->featured_image);

        $delete = $post->delete($id);
        if ($delete) {
            session()->flash('notif.success', 'Berhasil Menghapus');
            return redirect()->route('posts.index');
        }

        return abort(500);
    }
}
