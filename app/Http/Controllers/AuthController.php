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
    /**
     * Register
     * @OA\Post (
     *     path="/api/auth/signin",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"John",
     *                     "email":"john@test.com",
     *                     "password":"johnjohn1"
     *                }
     *             )
     *         )
     *      ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="id", type="string", example="9e0853d3-6a6f-49b9-be87-d42bae6ab84e"),
     *         @OA\Property(property="name", type="string", example="John"),
     *         @OA\Property(property="token", type="string", example="58|gS8QjAU3M7dPNvwDUgzKZRAClpQOVNmP6kdSm6i3518bc42c"),
     *     )
     *   ),
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="Wrong email or password")
     *     )
     * )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = \auth()->user();
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


    /**
     * Register
     * @OA\Post (
     *     path="/api/auth/signup",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"John",
     *                     "email":"john@test.com",
     *                     "password":"johnjohn1"
     *                }
     *             )
     *         )
     *      ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="id", type="string", example="9e0853d3-6a6f-49b9-be87-d42bae6ab84e"),
     *         @OA\Property(property="name", type="string", example="John"),
     *         @OA\Property(property="token", type="string", example="58|gS8QjAU3M7dPNvwDUgzKZRAClpQOVNmP6kdSm6i3518bc42c"),
     *     )
     *   ),
     * @OA\Response(
     *     response=422,
     *     description="Validation error",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="The email has already been taken."),
     *         @OA\Property(property="errors", type="object",
     *             @OA\Property(property="email", type="array",
     *                 @OA\Items(
     *                     type="string",
     *                     example="The email has already been taken."
     *                 )
     *             )
     *         )
     *     )
     * )
     * )
     */
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
