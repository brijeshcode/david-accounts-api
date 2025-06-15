<?php

namespace App\Http\Controllers\V1\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setup\ExpenseArticleStoreRequest;
use App\Http\Requests\V1\Setup\ExpenseArticleUpdateRequest;
use App\Http\Resources\V1\Setup\ExpenseArticleResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\ExpenseArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Setup
 * 
 * @subgroup Expense Article
 * @subgroupDescription This is setup api
 */
class ExpenseArticleController extends Controller
{
    /**
     * Display a listing of the expense articles.
     */
    public function index(Request $request): JsonResponse
    {
        
        $query = ExpenseArticle::with('expenseType');

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by expense type
        if ($request->has('expense_type_id')) {
            $query->where('expense_type_id', $request->expense_type_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $expenseArticles = $query->orderBy('name')->paginate(15);

        return ApiResponse::index('Expense Articles List', ExpenseArticleResource::collection($expenseArticles));
    }

    /**
     * Store a newly created expense article.
     */
    public function store(ExpenseArticleStoreRequest $request): JsonResponse
    {
        ExpenseArticle::create($request->validated());
        return ApiResponse::store('Expense article created successfully');
    }

    /**
     * Display the specified expense article.
     */
    public function show(ExpenseArticle $expenseArticle): JsonResponse
    {
        return ApiResponse::send('Expense article', 200, new ExpenseArticleResource($expenseArticle->load('expenseType')));
    }

    /**
     * Update the specified expense article.
     */
    public function update(ExpenseArticleUpdateRequest $request, ExpenseArticle $expenseArticle): JsonResponse
    {
        $expenseArticle->update($request->validated());
        return ApiResponse::update('Expense article updated successfully');
    }

    /**
     * Remove the specified expense article (soft delete).
     */
    public function destroy(ExpenseArticle $expenseArticle): JsonResponse
    {
        $expenseArticle->delete();
        return ApiResponse::delete('Expense article deleted successfully');
    }

    /**
     * Restore a soft deleted expense article.
     */
    public function restore($id): JsonResponse
    {
        $expenseArticle = ExpenseArticle::withTrashed()->findOrFail($id);
        $expenseArticle->restore();

        return ApiResponse::send('Expense article restored successfully', 200, new ExpenseArticleResource($expenseArticle->load('expenseType')));

    }

    /**
     * Toggle active status of expense article.
     */
    public function toggleStatus(ExpenseArticle $expenseArticle): JsonResponse
    {
        $expenseArticle->update(['is_active' => !$expenseArticle->is_active]);

        return ApiResponse::send('Expense article status updated successfully', 200, new ExpenseArticleResource($expenseArticle->load('expenseType')));
         
    }


    /**
     * Get all active expense articles for dropdown/select.
     */
    public function active(): JsonResponse
    {
        $expenseArticles = ExpenseArticle::active()
            ->with('expenseType')
            ->orderBy('name')
            ->get(['id', 'name', 'unit', 'expense_type_id']);

        return ApiResponse::send('Active expense articles retrieved successfully', 200, new ExpenseArticleResource($expenseArticles));
    }

}
