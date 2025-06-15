<?php

namespace App\Http\Requests\V1\Setup;

use App\Http\Requests\TenantFormRequest;
use Illuminate\Validation\Rule;

class ExpenseTypeUpdateRequest extends TenantFormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expense_types', 'name')->ignore($this->expense_type->id)
            ],
            'parent_id' => [
                'nullable',
                'exists:expense_types,id',
                Rule::notIn([$this->expense_type->id]) // Cannot be parent of itself
            ],
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
            'parent_id.not_in' => 'Expense type cannot be parent of itself',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Prevent circular reference
            if ($this->parent_id && $this->wouldCreateCircularReference()) {
                $validator->errors()->add('parent_id', 'This would create a circular reference');
            }
        });
    }

    /**
     * Check if setting this parent would create a circular reference.
     */
    private function wouldCreateCircularReference(): bool
    {
        $expenseType = $this->expense_type;
        $newParentId = $this->parent_id;

        // Get all descendants of current expense type
        $descendants = $this->getDescendants($expenseType->id);

        // Check if new parent is among descendants
        return in_array($newParentId, $descendants);
    }

    /**
     * Get all descendant IDs of an expense type.
     */
    private function getDescendants($parentId): array
    {
        $descendants = [];
        $children = \App\Models\ExpenseType::where('parent_id', $parentId)->pluck('id');

        foreach ($children as $childId) {
            $descendants[] = $childId;
            $descendants = array_merge($descendants, $this->getDescendants($childId));
        }

        return $descendants;
    }
}
