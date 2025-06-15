<?php

namespace App\Http\Controllers\V1\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setup\ExpenseTypeStoreRequest;
use App\Http\Requests\V1\Setup\ExpenseTypeUpdateRequest;
use App\Http\Resources\V1\Setup\ExpenseTypeResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\ExpenseType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // $expenseTypes = ExpenseType::select('id', 'name', 'parent_id', 'note', 'active')->paginate(10);

        $query = ExpenseType::query();

        // Filter by active status
        if ($request->has('is_active')) {
            $query->active('is_active', $request->boolean('is_active'));
        }

        // Filter by parent (top-level categories)
        if ($request->has('parent_only') && $request->boolean('parent_only')) {
            $query->whereNull('parent_id');
        }

        // Filter by specific parent
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->get('parent_id'));
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->get('search') . '%');
        }

        // Include soft deleted records
        if ($request->has('with_trashed') && $request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        // Order by name by default
        $query->orderBy('name');

        $expenseTypes = $query->paginate($request->get('per_page', 15));

        return ApiResponse::index('ExpenseTypes List', ExpenseTypeResource::collection($expenseTypes));

    }

    public function store(ExpenseTypeStoreRequest $request): JsonResponse
    {
        ExpenseType::create($request->validated());
        return ApiResponse::store('ExpenseType created successfully');
    }

    public function show(ExpenseType $expenseType): JsonResponse
    {
        return ApiResponse::send('ExpenseType', 200, new ExpenseTypeResource($expenseType->load('children', 'parent')));
    }

    public function update(ExpenseTypeUpdateRequest $request, ExpenseType $expenseType): JsonResponse
    {
        $expenseType->update($request->validated());
        return ApiResponse::update('ExpenseType updated successfully');
    }

    public function destroy(ExpenseType $expenseType): JsonResponse
    {
        if ($expenseType->children()->exists()) {
            return response()->json([
                'message' => 'Cannot delete expense type with child categories'
            ], 422);
        }

        // Check if expense type is being used (you might want to add this check)
        // if ($expenseType->expenses()->exists()) {
        //     return response()->json([
        //         'message' => 'Cannot delete expense type that is being used'
        //     ], 422);
        // }

        $expenseType->delete();
        return ApiResponse::delete('ExpenseType deleted successfully');
    }


    /**
     * Restore a soft deleted expense type.
     */
    public function restore($id): JsonResponse
    {
        $expenseType = ExpenseType::withTrashed()->findOrFail($id);
        
        if (!$expenseType->trashed()) {
            return response()->json([
                'message' => 'Expense type is not deleted'
            ], 422);
        }

        $expenseType->restore();
        return ApiResponse::send('Expense type restored successfully', 200, new ExpenseTypeResource($expenseType));
    }

    /**
     * Permanently delete an expense type.
     */
    public function forceDelete($id): JsonResponse
    {
        $expenseType = ExpenseType::withTrashed()->findOrFail($id);
        
        // Check if expense type has children
        if ($expenseType->children()->withTrashed()->exists()) {
            return response()->json([
                'message' => 'Cannot permanently delete expense type with child categories'
            ], 422);
        }

        $expenseType->forceDelete();
        
        return ApiResponse::send('Expense type permanently deleted');
    }

    /**
     * Get hierarchical tree structure of expense types.
     */
    public function tree(Request $request): JsonResponse
    {
        $query = ExpenseType::query();

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Get all expense types and build tree structure
        $expenseTypes = $query->orderBy('name')->get();
        $tree = $this->buildTree($expenseTypes);

        return response()->json([
            'data' => ExpenseTypeResource::collection($tree)
        ]);
    }

    /**
     * Toggle active status of expense type.
     */
    public function toggleStatus(ExpenseType $expenseType): JsonResponse
    {
        $expenseType->update(['is_active' => !$expenseType->is_active]);
 
        return ApiResponse::send('Expense type status updated successfully', 200, new ExpenseTypeResource($expenseType));
    }

    /**
     * Build hierarchical tree structure from flat collection.
     */
    private function buildTree($expenseTypes, $parentId = null)
    {
        $tree = collect();

        foreach ($expenseTypes as $expenseType) {
            if ($expenseType->parent_id == $parentId) {
                $children = $this->buildTree($expenseTypes, $expenseType->id);
                if ($children->isNotEmpty()) {
                    $expenseType->setRelation('children', $children);
                }
                $tree->push($expenseType);
            }
        }

        return $tree;
    }
}
