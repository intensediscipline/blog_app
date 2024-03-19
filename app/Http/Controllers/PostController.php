<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function delete(Post $post) {
        $post->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', 'Post successfully deleted.');
    }

    public function showSinglePost(Post $post) {
        $post['body'] = Str::markdown($post->body, ['<p><ol><ul><em><strong><h1><h2><h3><br>']);
        return view('single-post', ['post' => $post]);
    }

    public function storeNewPost(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        // get rid of tags
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        // get user id
        $incomingFields['user_id'] = auth()->id(); 

        $newPost = Post::create($incomingFields);
        
        return redirect("/post/{$newPost->id}")->with('success', 'New post successfully created.');
    }

    public function showCreateForm() {
        // if (!auth()->check()) {
        //     return redirect('/');
        // }
        return view('create-post');
    }
}
