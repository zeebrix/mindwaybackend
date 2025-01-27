<?php

namespace App\Http\Requests\API\MobileApp\Auth\Customer;

use App\Http\Requests\API\BaseAPIRequest;
use Illuminate\Validation\Rule;

class CustomerGetNotifyRequest extends BaseAPIRequest {

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
        return
        [
            'email' => 'required',
            'notify_time' => "required",
            'notify_day' => 'required',

        ];
    }

}
