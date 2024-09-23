<?php

namespace App\Http\Requests;
use App\Models\Blog;
use Illuminate\Foundation\Http\FormRequest;

class BlogDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        return true;
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
        ];
    }

    public function messages()
    {
        return [
            'blog_id.required' => 'Blog ID is required.',
            'blog_id.exists' => 'The specified blog does not exist.',
        ];
    }
}
