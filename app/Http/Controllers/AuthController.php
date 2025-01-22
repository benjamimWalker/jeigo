<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $user->tokens()->delete();

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'token' => $user->createToken('auth')->plainTextToken
            ]);
        } else {
            return response()->json([
                'message' => 'Wrong email or password'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'token' => $user->createToken('auth')->plainTextToken
        ]);
    }
}
