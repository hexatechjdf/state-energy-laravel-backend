<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return errorResponse('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken($request->header('User-Agent') ?? 'api-token')->plainTextToken;

        return successResponse([
            'token' => $token,
            'user'  => new UserResource($user),
        ]);
    }
}
