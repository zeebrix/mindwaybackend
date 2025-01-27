<?php

namespace App\Http\Requests\API\MobileApp\Auth\Sessions;

use App\Http\Requests\API\BaseAPIRequest;
use Illuminate\Validation\Rule;

class AddSessionRequest extends BaseAPIRequest {

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
            'course_title' => 'required',
            'course_description' => 'required',
            'course_duration' => 'required',
            'course_thumbnail'=> 'required|image|mimes:jpeg,png,jpg|max:2048',

        ];
    }

}
