<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        // Attempt to authenticate the user
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'Email atau password tidak valid',
                'status_code' => 401,
            ], 401);
        }

        // Retrieve the authenticated user
        $user = Auth::user();

        // Create a new token for the user
        $token = $user->createToken('api-token')->plainTextToken;

        // Return the user data and token
        return response()->json([
            'results' => true,
            'data' => $user,
            'message' => 'Login berhasil',
            'token' => $token,
            'status_code' => 201
        ], 201);
    }
}
