<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validate incoming request parameters
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'name' => 'required|string|max:50',
        ]);


        $path = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('users', 'public');
        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $path, // Store the image path in the database
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        // 1. Validate incoming request parameters
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        // 2. Locate the user by email
        $user = User::where('email', $request->email)->first(); // email is existing in the database or not

        // 3. Verify user exists and check password matching
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Logged in successfully.',
            'token' => $token,
            'user' => $user
        ], 200);

    }

    public function logout(Request $request)
    {
        // logout current device 
        // $request->user()->currentAccessToken()->delete();
         $request->user()->tokens()->delete(); // logout all devices


        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.'
        ], 200);
    }
}
