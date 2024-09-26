<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if(auth()->check()) {
            return true;
        }   

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                'rating' => 'required|numeric|between:1,5',
        ];
    }

    public function messages()
    {
        return [
            'rating.required' => 'Rating is required',
            'rating.float' => 'Rating must be a float',
            'rating.between' => 'Rating must be between 1 and 5',
        ];
    }
}
