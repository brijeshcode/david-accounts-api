<?php

namespace App\Http\Requests\V1\Setup;

use App\Http\Requests\TenantFormRequest;
use Illuminate\Validation\Rule;

class ExternalServiceStoreRequest extends TenantFormRequest
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
                'max:250',
                'min:2',
                Rule::unique('external_services', 'name')->whereNull('deleted_at'),
            ],
            'note' => ['sometimes', 'string'],
            'is_active' => ['boolean']
        ];
    }
}
