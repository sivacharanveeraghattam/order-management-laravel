<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required'     => 'Name is required',
            'email.required'    => 'Email is required',
            'email.unique'      => 'Email already taken',
            'password.required' => 'Password must be 6+ chars',
            'password.confirmed' => 'Passwords do not match',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return response()->json([
            'message' => 'Registered & logged in',
            'user'    => $user->only(['id', 'name', 'email'])
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Login successful',
                'user'    => Auth::user()->only(['id', 'name', 'email'])
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function me()
    {
        return response()->json(Auth::user());
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }
}
