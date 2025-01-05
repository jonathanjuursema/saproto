<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class NarrowCastingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can('board');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'campaign_start' => 'required|date',
            'campaign_end' => 'required|date',
            'slide_duration' => 'integer',
            'image' => 'required_without:youtube_id',
            'youtube_id' => 'string|min:10|max:12|required_without:image',
        ];
    }
}
