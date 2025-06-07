<?php

namespace App\Http\Controllers\V1\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setup\UserStoreRequest;
use App\Http\Requests\V1\Setup\UserUpdateRequest;
use App\Http\Resources\V1\Setup\UserResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return ApiResponse::index('Users List', UserResource::collection($users));
    }

    public function store(UserStoreRequest $request)
    {
        User::create($request->validated());

        return ApiResponse::store('User created successfully');
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update($request->validated());
        return ApiResponse::update('User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return ApiResponse::delete('User deleted successfully');
    }
}
