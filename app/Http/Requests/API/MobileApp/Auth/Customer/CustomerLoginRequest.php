<?php

namespace App\Http\Requests\API\MobileApp\Auth\Customer;

use App\Http\Requests\API\BaseAPIRequest;

class CustomerLoginRequest extends BaseAPIRequest {

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
            'email' => 'required|exists:customers', //|',
            'password' => 'required'
        ];
    }

    public function messages() {
        return [
            'email.exists' => 'These credentials do not match our records.'
        ];
    }

}
