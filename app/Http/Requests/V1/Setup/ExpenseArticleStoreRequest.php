<?php

namespace App\Http\Requests\V1\Setup;

use App\Http\Requests\TenantFormRequest;

class ExpenseArticleStoreRequest extends TenantFormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'expense_type_id' => 'required|exists:expense_types,id',
            'name' => 'required|string|max:200',
            'unit' => 'required|string|max:10',
            'note' => 'nullable|string',
            'is_active' => 'boolean'
        ];
    }
}
