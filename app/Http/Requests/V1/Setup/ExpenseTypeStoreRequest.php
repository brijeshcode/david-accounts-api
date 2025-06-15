<?php

namespace App\Http\Requests\V1\Setup;

use App\Http\Requests\TenantFormRequest;
use Illuminate\Validation\Rule;

class ExpenseTypeStoreRequest extends TenantFormRequest
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
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:expense_types,name',
            'parent_id' => 'nullable|exists:expense_types,id',
            'note' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Expense type name is required',
            'name.unique' => 'An expense type with this name already exists',
            'parent_id.exists' => 'Selected parent expense type does not exist',
        ];
    }

    
}
