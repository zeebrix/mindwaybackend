<?php

namespace App\Http\Requests\API\MobileApp\Auth\Customer;

use App\Http\Requests\API\BaseAPIRequest;
use Illuminate\Validation\Rule;

class CustomerVerifySmsRequest extends BaseAPIRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email' => ["required", "unique:customers,deleted_at,NULL", "numeric", Rule::unique('customers')->where(function ($query) {
                    $query = $query->where('deleted_at', NULL);
                    return $query;
                })],
            'verification_hash' => 'required'
        ];
    }

}
