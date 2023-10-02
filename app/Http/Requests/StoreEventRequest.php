<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
