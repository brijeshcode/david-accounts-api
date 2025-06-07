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

class BankController extends Controller
{
    public function index(): JsonResponse
    {
        $banks = Bank::paginate(10);
        return ApiResponse::index('Banks List', BankResource::collection($banks));
    }

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
