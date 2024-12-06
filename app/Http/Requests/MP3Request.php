<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MP3Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'sound' => 'required|mimes:audio/mpeg,mp3,mpga|max:200',
        ];
    }
}
