<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Responses\V1\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    /**
     * User Login
     * 
     * Authenticate user with email and password to receive an access token.
     * 
     * @unauthenticated
     * 
     * @bodyParam email string required User's email address. Example: user@example.com
     * @bodyParam password string required User's password. Example: password123
     * 
     * @response 200 {
     *   "message": "Login successful",
     *   "status": 200,
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "user@example.com"
     *     },
     *     "token": "1|abc123def456ghi789..."
     *   }
     * }
     * 
     * @response 401 {
     *   "message": "Invalid credentials",
     *   "status": 401
     * }
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        
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