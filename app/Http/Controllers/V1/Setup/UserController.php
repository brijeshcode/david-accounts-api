<?php

namespace App\Http\Controllers\V1\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setup\UserStoreRequest;
use App\Http\Requests\V1\Setup\UserUpdateRequest;
use App\Http\Resources\V1\Setup\UserResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\User;
use App\Traits\HasPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Advance
 * 
 * @subgroup User
 * @subgroupDescription This is Advance setup api
 */
class UserController extends Controller
{
    use HasPagination;

    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $query = User::query();
        // Add search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Add sorting
        $query->orderBy('created_at', 'desc');
        $users = $this->applyPagination($query, $request);

        return ApiResponse::paginated('Users List', $users, UserResource::class);
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
