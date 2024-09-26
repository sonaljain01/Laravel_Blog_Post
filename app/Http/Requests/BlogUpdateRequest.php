<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Blog;

class BlogUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    protected $error = "";
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        $user_id = auth()->user()->id;
        // only authorized user can update notes 
        if(! $this->blog_id){
            $this->error = "please enter blog id";
            return false;
        }
        if(!Blog::where('id', $this->blog_id)->where('user_id', $user_id)->exists()){
            $this->error = "You are not allowed to update other person blog";
            return false;
        }

        return true;
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException($this->error);
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
            'slug' => 'nullable|string|max:255|unique:blogs,slug',
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
        ];
    }
}
