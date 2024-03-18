<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
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

        Post::create($incomingFields);
        
        return "store new post";
    }

    public function showCreateForm() {
        return view('create-post');
    }
}
