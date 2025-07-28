<?php

namespace App\Http\Controllers\V1\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setup\CustomerStoreRequest;
use App\Http\Requests\V1\Setup\CustomerUpdateRequest;
use App\Http\Resources\V1\Setup\CustomerResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\Customer;
use App\Traits\HasBooleanFilters;
use App\Traits\HasPagination;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @group Setup
 * 
 * @subgroup Customer
 * @subgroupDescription This is setup api
 */
class CustomerController extends Controller
{
    use HasPagination, HasBooleanFilters;

    /**
     * Get list of customers
     * 
     * list of customer avaialble in the system
     */
    public function index(Request $request): JsonResponse
    {
        $query = Customer::query();
        
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

        // if ($request->filled('is_active')) {
        //     $isActive = $request->boolean('is_active');
        //     $query->where('is_active', $isActive);
        // }

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
        
        
        $customers = $this->applyPagination($query, $request);
        return ApiResponse::paginated('Customers List', $customers, CustomerResource::class);
    }

    public function store(CustomerStoreRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());
        return ApiResponse::store('Customer created successfully', $customer);
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function update(CustomerUpdateRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());
        return ApiResponse::update('Customer updated successfully', $customer);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();
        return ApiResponse::delete('Customer deleted successfully');
    }
}
