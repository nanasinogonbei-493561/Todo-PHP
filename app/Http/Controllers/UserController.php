<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // ログインフォーム表示
    public function showLoginForm()
    {
        return  view('auth.login');
    }

    public function login(Request $request)
    {

        
        $email = $request->input('email');


        $password = $request->input('password');

        $user = User::where('email', $email)->first();


        if($user){
            if (Hash::check($password, $user->password)) {
                // ログイン成功
                session()->put('is_login', true);
                session()->put('user_id', $user->id);
                session()->put('user_name', $user->name);


                return redirect()->route('todos.index');
            } else {
                // パスワードが間違っている
                return redirect()->back()->withErrors(['password' => 'Incorrect password']);
            }


        }else{
            return redirect()->back()->withErrors(['email' => 'Email not found']);
        }




    }
}
