<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Helpers\StructuredLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ログイン成功を記録
        StructuredLogger::userAction('user_login', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'ip_address' => $request->ip()
        ]);

        return redirect()->intended(route('todos.index', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ログアウト前のユーザー情報を記録
        $userId = Auth::id();
        $userEmail = Auth::user()?->email;

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // ログアウトを記録
        StructuredLogger::userAction('user_logout', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'ip_address' => $request->ip()
        ]);

        return redirect('/login');
    }
}
