<?php

namespace App\Http\Controllers\V1\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setup\CustomerStoreRequest;
use App\Http\Requests\V1\Setup\CustomerUpdateRequest;
use App\Http\Resources\V1\Setup\CustomerResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Setup
 * 
 * @subgroup Customer
 * @subgroupDescription This is setup api
 */
class CustomerController extends Controller
{
    /**
     * Get list of customers
     * 
     * list of customer avaialble in the system
     */
    public function index(): JsonResponse
    {
        $customers = Customer::paginate(10);
        return ApiResponse::index('Customers List', CustomerResource::collection($customers));
    }

    public function store(CustomerStoreRequest $request): JsonResponse
    {
        Customer::create($request->validated());

        return ApiResponse::store('Customer created successfully');

    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function update(CustomerUpdateRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());
        return ApiResponse::update('Customer updated successfully');
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();
        return ApiResponse::delete('Customer deleted successfully');
    }
}
