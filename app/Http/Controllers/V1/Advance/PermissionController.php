<?php

namespace App\Http\Controllers\V1\Advance;

use App\Http\Controllers\Controller;
use App\Http\Responses\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        $permissions = Permission::all()->groupBy('group');
        return ApiResponse::index('Permissions list', ['permissions' => $permissions]);
    }

    public function syncPermissions(): JsonResponse
    {
        $permissionsByGroup = [
            'invoice' => [
                'create invoice',
                'edit invoice',
                'delete invoice',
                'view invoice',
            ],
            'purchase' => [
                'create purchase',
                'edit purchase',
                'delete purchase',
                'view purchase',
            ],
        ];

        foreach ($permissionsByGroup as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::updateOrCreate(
                    ['name' => $permission, 'guard_name' => 'api'],
                    ['group_name' => $group]
                );
            }
        }

        return ApiResponse::update('Permissions synced successfully');
    }
}
