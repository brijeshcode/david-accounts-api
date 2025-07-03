<?php

namespace App\Http\Controllers\V1\Advance;

use App\Http\Controllers\Controller;
use App\Http\Responses\V1\ApiResponse;
use App\Services\TenantFeatureService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')->get();
        return ApiResponse::index('Roles list', ['role' => $roles]);
    }

    public function show(Role $role): JsonResponse
    {
        return ApiResponse::show('Role', ['role' => $role->load('permissions')]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'is_active' => 'boolean',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        

        $role = Role::create([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'],
            'guard_name' => 'api',
        ]);


        $allowedPermissions = Permission::whereIn('group_name', app(TenantFeatureService::class)->getAllowedPermissionGroups())
            ->pluck('name')
            ->toArray();

        $filteredPermissions = collect($validated['permissions'] ?? [])
            ->filter(fn ($p) => in_array($p, $allowedPermissions))
            ->values()
            ->all();

        ;
        
        $role->syncPermissions($filteredPermissions);

        return ApiResponse::store('Role created', compact('role'));
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->update(['name' => $validated['name'], 'is_active' => $validated['is_active']]);

        $allowedPermissions = Permission::whereIn('group_name', app(TenantFeatureService::class)->getAllowedPermissionGroups())
            ->pluck('name')
            ->toArray();

        $filteredPermissions = collect($validated['permissions'] ?? [])
            ->filter(fn ($p) => in_array($p, $allowedPermissions))
            ->values()
            ->all();

        ;
        
        $role->syncPermissions($filteredPermissions);

        return ApiResponse::update('Role updated', compact('role'));
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();
        return ApiResponse::update('Role deleted');
    }

    public function deactivate(Role $role): JsonResponse
    {
        $role->update(['is_active' => false]);
        return ApiResponse::update('Role deactivated');
    }

    public function permissionGroups(): JsonResponse
    {
        $allowedGroups = app(TenantFeatureService::class)->getAllowedPermissionGroups();

        $permissions = Permission::query()
        ->whereIn('group_name', $allowedGroups)
        ->get()
        ->groupBy('group_name');

        return ApiResponse::custom('Permissions List', 200, compact('permissions'));
    }

    public function toggleStatus(Role $role): JsonResponse
    {
        $role->update(['is_active' => !$role->is_active]);
        return ApiResponse::custom('Role status updated successfully', 200, compact('role'));
    }

    public function availableModules(): JsonResponse
    {
        return response()->json(app(TenantFeatureService::class)->getTenantModules());
    }
}