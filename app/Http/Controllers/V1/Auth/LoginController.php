<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Responses\V1\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        // dd($request->all());
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return ApiResponse::send('Invalid credentials', 401);
        }

        $token = $user->createToken('login')->plainTextToken;

        return ApiResponse::send('Login successful', 200, [
            'user' => $user->only(['id', 'name', 'email']),
            'token' => $token,
        ]);
    }
}
