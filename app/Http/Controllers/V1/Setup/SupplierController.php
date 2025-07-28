<?php

namespace App\Http\Controllers\V1\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setup\SupplierStoreRequest;
use App\Http\Requests\V1\Setup\SupplierUpdateRequest;
use App\Http\Resources\V1\Setup\SupplierResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\Supplier;
use App\Traits\HasBooleanFilters;
use App\Traits\HasPagination;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @group Setup
 * 
 * @subgroup Supplier
 * @subgroupDescription This is setup api
 */
class SupplierController extends Controller
{
    use HasPagination, HasBooleanFilters;

    public function index(Request $request): JsonResponse
    {
        $query = Supplier::query();
        // Apply sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = [ 'name', 'email', 'phone', 'is_active', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc'); // fallback
        }
        
        // Apply global search across multiple fields
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                ->orWhere('note', 'LIKE', "%{$searchTerm}%")
                ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                ->orWhere('address', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Apply specific field filters
        if ($request->filled('name')) {
            $query->where('name', 'LIKE', "%{$request->input('name')}%");
        }
        
        if ($request->filled('email')) {
            $query->where('email', 'LIKE', "%{$request->input('email')}%");
        }
        
        if ($request->filled('phone')) {
            $query->where('phone', 'LIKE', "%{$request->input('phone')}%");
        }

        if ($request->filled('is_active')) {
            $this->applyBooleanFilter($query, 'is_active', $request->input('is_active'));
        }
        
        // Apply date range filters
        if ($request->filled('created_from')) {
            try {
                $createdFrom = Carbon::parse($request->input('created_from'))->startOfDay();
                $query->where('created_at', '>=', $createdFrom);
            } catch (\Exception $e) {
                // Log invalid date format but continue
                Log::warning('Invalid created_from date format: ' . $request->input('created_from'));
            }
        }
        
        if ($request->filled('created_to')) {
            try {
                $createdTo = Carbon::parse($request->input('created_to'))->endOfDay();
                $query->where('created_at', '<=', $createdTo);
            } catch (\Exception $e) {
                // Log invalid date format but continue
                Log::warning('Invalid created_to date format: ' . $request->input('created_to'));
            }
        }
        
        
        // Apply pagination (handled by your trait)
        $suppliers = $this->applyPagination($query, $request);
        
        return ApiResponse::paginated('Suppliers List', $suppliers, SupplierResource::class);
    }

    public function store(SupplierStoreRequest $request): JsonResponse
    {
        $supplier = Supplier::create($request->validated());
        return ApiResponse::store('Supplier created successfully', $supplier);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return ApiResponse::send('Supplier', 200, new SupplierResource($supplier));
    }

    public function update(SupplierUpdateRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier->update($request->validated());
        return ApiResponse::update('Supplier updated successfully', $supplier);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->delete();
        return ApiResponse::delete('Supplier deleted successfully');
    }

    public function all(Request $request): JsonResponse
    {
        $query = Supplier::query();
        $query->orderBy('name', 'asc');
        $suppliers = $query->get();
        return ApiResponse::index('Suppliers List', SupplierResource::collection($suppliers));
    }

    public function active(Request $request): JsonResponse
    {
        $query = Supplier::query();
        $query->active();
        $query->orderBy('name', 'asc');
        $suppliers = $query->get();
        return ApiResponse::index('Active Suppliers List', SupplierResource::collection($suppliers));
    }

    public function trashed()
    {
        return Supplier::onlyTrashed()->get(); // If using soft deletes
    }


}
