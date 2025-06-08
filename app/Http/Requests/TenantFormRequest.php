<?php

namespace App\Http\Requests;

use App\Traits\CustomFailedValidation;
use App\Traits\SetsTenantConnection;
use Illuminate\Foundation\Http\FormRequest;

class TenantFormRequest extends FormRequest
{
    use CustomFailedValidation;
    use SetsTenantConnection;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    protected function prepareForValidation(): void
    {
        $this->setTenantConnection();
    }
}
