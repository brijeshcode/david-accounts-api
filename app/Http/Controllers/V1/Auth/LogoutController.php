<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{

    /**
     * User Logout
     * 
     * Revoke the current user's access token to log them out.
     * 
     * @authenticated
     * 
     * @response 200 {
     *   "message": "Logout successful",
     *   "status": 200
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated",
     *   "status": 401
     * }
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::send('Logout successful', 200);
    }
}