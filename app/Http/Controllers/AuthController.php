<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect'
            ], 401);
        }

        $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successful',
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'role' => $user->role,
            ]
        ], 200);
    }

    public function register(RegisterRequest $request): JsonResponse
    {

        if (User::where('email', $request->input('email'))->exists()) {
            return response()->json([
                'message' => 'Email already exists'
            ], 409);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        if (!$user) {
            return response()->json([
                'message' => 'Something went wrong during registration.'
            ], 500);
        }

        $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;

        return response()->json([
            'message' => 'Registration Successful',
            'token_type' => 'Bearer',
            'token' => $token
        ], 201);
    }


    public function logout(Request $request)
    {
        $user = User::where('id', '=', $request->user()->id)->first();
        if ($user) {
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Logged out successfully',
            ], 200);
        } else {
        }
    }

    public function profile(Request $request)
    {
        if ($request->user()) {
            return response()->json([
                'message' => 'Profile Fetched',
                'data' => $request->user()
            ], 200);
        } else {
            return response()->json([
                'message' => 'User Not Found'
            ], 404);
        }
    }
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $validatedData = $request->validated();

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            $validatedData['password'] = $user->password;
        }

        $user->update([
            'name' => $validatedData['name'],
            'password' => $validatedData['password'],
        ]);

        return response()->json([
            'message' => 'User updated successfully.',
            'data' => $user,
        ], 200);
    }
}
