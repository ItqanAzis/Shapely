<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // LOGIN FUNCTION
    public function login(Request $request)
    {
        // 1. Validate Input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Check Credentials
        if (Auth::attempt($credentials)) {
            // 3. Generate a Secret Token
            $user = Auth::user();
            $token = $user->createToken('admin-token')->plainTextToken;

            // 4. Send the Token to the Frontend
            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);
        }

        // 5. Fail
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    // LOGOUT FUNCTION
    public function logout(Request $request)
    {
        // Delete the token so it can't be used again
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}