<?php

namespace App\Http\Requests\V1\Setup;

use App\Http\Requests\TenantFormRequest;
use Illuminate\Validation\Rule;

class BankStoreRequest extends TenantFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 
                'string',
                Rule::unique('banks', 'name')->whereNull('deleted_at'),
            ],
            'starting_balance' => ['sometimes', 'numeric'],
            'balance' => ['sometimes', 'numeric'],
            'address' => ['sometimes', 'string', 'max:250'],
            'account_no' => ['sometimes', 'string', 'max:100'],
            'note' => ['sometimes', 'string'],
            'is_active' => ['boolean']
        ];
    }
}
