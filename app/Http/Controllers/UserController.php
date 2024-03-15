<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
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
