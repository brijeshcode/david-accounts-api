<?php

namespace App\Http\Requests\V1\Setup;

use App\Http\Requests\TenantFormRequest;
use Illuminate\Validation\Rule;

class WarehouseStoreRequest extends TenantFormRequest
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
                'required', 'string', 'max:150', 'min:2',
                    Rule::unique('warehouses', 'name')->whereNull('deleted_at'),
                ],
            'phone' => ['sometimes', 'string', 'max:20', 'min:5'],
            'address' => ['sometimes', 'string', 'max:250'],
            'note' => ['sometimes', 'string'],
            'is_active' => ['boolean']
        ];
    }
}
