<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class UserController extends Controller
{
    public function storeAvatar(Request $request) {
        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);

        $user = auth()->user();

        $fileName = $user->id . "-" . uniqid() . ".jpg";

        // bring in instance of image composer package
        $manager = new ImageManager(new Driver());

        $image = $manager->read($request->file('avatar'));

        $imgData = $image->cover(120,120)->toJpeg();
        
        Storage::put("public/avatars/" . $fileName, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $fileName;
        // saves to db
        $user->save();

        // delete avatar if image is not fallback
        if ($oldAvatar != "/fallback-avatar.jpg") {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return redirect("/profile/" . auth()->user()->username)->with('success', "Avatar successfully updated");
    }

    public function showAvatarForm() {
        return view("avatar-form");
    }

    public function profile(User $user) {
        $currentlyFollowing = 0;

        // if currently logged in
        if (auth()->check()) {
            // 0 or 1
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
        }

        
        // get all posts belonging to user
        // using a method posts on the user model
        $userPosts = $user->posts()->latest()->get();
        
        return view('profile-posts', [
            'username' => $user->username, 
            'posts' => $userPosts, 
            'avatar' => $user->avatar,
            'currentlyFollowing' => $currentlyFollowing
        ]);
    }


    public function logout() {
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out.');
    }

    public function showCorrectHome() {
        if(auth()->check()) {
            return view('home-feed');
        } else {
            return view('home');
        }
    }

    public function login(Request $request) {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        if (auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']] )) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You have successfully logged in.');
        } else {
            return redirect('/')->with('error', 'Your login details are incorrect.');
        }
    }

    public function register(Request $request) {
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3','max:20', Rule::unique('users', 'username')],
            'email' => ['required','email', Rule::unique('users', 'email')],
            'password' => ['required','min:8', 'confirmed']
        ]);
        
        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);
        //login user automatically
        auth()->login($user);
        return redirect('/')->with('success', 'You are now registerred.');
    }
}
