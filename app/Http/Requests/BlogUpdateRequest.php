<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Blog;

class BlogUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        $user_id = auth()->user()->id;
        // only authorized user can update notes 
        if(Blog::where('id', $this->blog_id)->where('user_id', $user_id)->exists()){

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
            'title' => 'string|max:255',
            'description' => 'string',
            'blog_id' => 'required|integer|exists:blogs,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'string',
        ];
    }

    public function message(): array
    {
        return [
            'title.string' => 'Title must be a string',
            'title.max' => 'Title is too long',
            'description.string' => 'Description must be a string',
            'blog_id.required' => 'Blog id is required',
            'blog_id.integer' => 'Blog id must be an integer',
            'blog_id.exists' => 'Blog id does not exist',
            'image.image' => 'Image must be an image',
            'image.mimes' => 'Image must be a jpeg, png, jpg, gif, svg',
            'image.max' => 'Image is too large',
            'tags.string' => 'Tags must be a string',
        ];
    }
}