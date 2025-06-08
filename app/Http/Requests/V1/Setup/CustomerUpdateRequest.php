<?php

namespace App\Http\Requests\V1\Setup;

use App\Http\Requests\TenantFormRequest;
use Illuminate\Validation\Rule;

class CustomerUpdateRequest extends TenantFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
 
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150', 'min:2', Rule::unique('customers', 'name')->ignore($this->route('customer'))->whereNull('deleted_at'),],
            'email' => ['sometimes', 'email', 'max:200', 'min:5'],
            'phone' => ['sometimes', 'string', 'max:20', 'min:5'],
            'address' => ['sometimes', 'string', 'max:250'],
            'note' => ['sometimes', 'string'],
            'is_active' => ['boolean']
        ];
    }
}
