<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'title' => ['required'],
            'start' => ['required', 'gte:end'],
            'location' => ['required'],
            'secret' => ['required'],
            'description' => ['required'],
            'summary' => ['required'],
            'image' => ['image'],
        ];
    }

    public function messages()
    {
        return [
            'gte' => 'The Event cannot end before it begins!', ];
    }
}
