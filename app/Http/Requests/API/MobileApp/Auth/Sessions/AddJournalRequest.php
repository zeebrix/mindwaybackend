<?php

namespace App\Http\Requests\API\MobileApp\Auth\Sessions;

use App\Http\Requests\API\BaseAPIRequest;
use Illuminate\Validation\Rule;

class AddJournalRequest extends BaseAPIRequest {

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
            'title' => "required",
            'description' => 'required',
            'date' => 'required',
            'emoji_name' => 'required',
            'emoji_image' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

}
