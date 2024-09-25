<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
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
            'blog_id' => 'required|integer|exists:blogs,id',
            'comment' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'blog_id.required' => 'Blog ID is required',
            'blog_id.integer' => 'Blog ID must be an integer',
            'blog_id.exists' => 'Blog ID does not exist',
            'comment.required' => 'Comment is required',
            'comment.string' => 'Comment must be a string',
            'comment.max' => 'Comment length is upto 1000 characters onlys',
        ];
    }
}
