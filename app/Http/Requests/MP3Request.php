<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MP3Request extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'sound' => 'required|mimes:audio/mpeg,mp3,mpga|max:200',
        ];
    }
}
