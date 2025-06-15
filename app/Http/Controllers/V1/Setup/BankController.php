<?php

namespace App\Http\Controllers\V1\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setup\BankStoreRequest;
use App\Http\Requests\V1\Setup\BankUpdateRequest;
use App\Http\Resources\V1\Setup\BankResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Setup
 * 
 * @subgroup Bank
 * @subgroupDescription This is setup api
 */

 
class BankController extends Controller
{
    
    /**
     * Get list of banks
     * 
     * @queryParam int page 10
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $banks = Bank::paginate(10);
        return ApiResponse::index('Banks List', BankResource::collection($banks));
    }

    /**
     * Create a new bank
     * 
     * @bodyParam name string required Bank name Example: Bank of America
     * @bodyParam account_no string required Bank account number Example: 1234567890
     * @bodyParam balance decimal required Initial balance of the bank Example: 1000.00
     * @bodyParam starting_balance decimal required Starting balance of the bank Example: 0.00
     * @bodyParam note string Bank note Example: This is a test bank
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BankStoreRequest $request): JsonResponse
    {
        Bank::create($request->validated());

        return ApiResponse::store('Bank created successfully');
    }

    public function show(Bank $bank)
    {
        return new BankResource($bank);
    }

    public function update(BankUpdateRequest $request, Bank $bank): JsonResponse
    {
        $bank->update($request->validated());
        return ApiResponse::update('Bank updated successfully');
    }

    public function destroy(Bank $bank): JsonResponse
    {
        $bank->delete();
        return ApiResponse::delete('Bank deleted successfully');
    }
}
