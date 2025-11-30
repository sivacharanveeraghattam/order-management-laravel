<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            /* Log::info('Registration attempt', ['email' => $request->email]); */

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

            /* Log::info('User registered successfully', ['user_id' => $user->id]); */

            $user = Auth::user();

            return response()->json([
                'message' => 'Registered & logged in successfully',
                'user'    => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email
                ],
                'session' => true
            ], 201);
        } catch (ValidationException $e) {
            Log::warning('Validation failed during registration', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Registration failed',
                'error'   => 'Something went wrong'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            Log::info('Login attempt', ['email' => $request->email]);

            if (Auth::check()) {
                $user = Auth::user();

                Log::info('User already logged in', ['user_id' => $user->id]);

                return response()->json([
                    'message' => 'User already logged in',
                    'user'    => [
                        'id'    => $user->id,
                        'name'  => $user->name,
                        'email' => $user->email
                    ],
                    'session' => true
                ]);
            }

            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ], [
                'email.required'    => 'Email is required',
                'email.email'       => 'Valid email required',
                'password.required' => 'Password is required',
            ]);

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                Log::info('Login successful', ['user_id' => $user->id]);

                return response()->json([
                    'message' => 'Login successful',
                    'user'    => [
                        'id'    => $user->id,
                        'name'  => $user->name,
                        'email' => $user->email
                    ],
                    'session' => true
                ]);
            }

            Log::warning('Login failed - Invalid credentials', ['email' => $request->email]);

            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login failed', [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Login failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function loginOld(Request $request)
    {
        try {
            Log::info('Login attempt', ['email' => $request->email]);
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {

                $user = Auth::user();

                Log::info('Login successful', ['user_id' => $user->id]);

                return response()->json([
                    'message' => 'Login successful',
                    'user'    => [
                        'id'    => $user->id,
                        'name'  => $user->name,
                        'email' => $user->email
                    ],
                    'session' => true
                ]);
            }

            Log::warning('Login failed - Invalid credentials', ['email' => $request->email]);

            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login failed', [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Login failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Not authenticated'
                ], 401);
            }

            return response()->json([
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Me endpoint error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Logout failed'], 500);
        }
    }
}
