<?php

namespace App\Http\Controllers\Blog;
use App\Models\Blog;
use App\Http\Requests\BlogDeleteRequest;
use App\Http\Requests\BlogStoreRequest;
use App\Http\Requests\BlogUpdateRequest;
use App\Http\Controllers\Controller;
use Mews\Purifier\Facades\Purifier;
use App\Models\Comment;
use App\Http\Requests\CommentStoreRequest;

class BlogController extends Controller
{
    public function display()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            //every one can see the blog
            $blogs = Blog::with('category')->get();

        } elseif ($user->isWriter()) {
            $blogs = Blog::where('user_id', $user->id)->with('category')->get();
        }

        $blogs->transform(function ($blog) {
            $blog->image = $blog->image ? url($blog->image) : null;
            return $blog;
        });

        return response()->json([
            "status" => true,
            "message" => "Blog fetched successfully",
            "data" => $blogs,
        ]);

    }

    public function store(BlogStoreRequest $request)
    {
        $request->validated();

        $slug = $request->slug ?: \Str::slug($request->title);

        $originalSlug = $slug;
        $counter = 1;
        while (Blog::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $metaTitle = $request->title;
        $metaDescription = substr($request->description, 0, 160);

        if (is_array($request->tags)) {
            $metaKeywords = implode(', ', $request->tags);
        } elseif (is_string($request->tags)) {
            $metaKeywords = $request->tags;
        } else {
            $metaKeywords = '';
        }

        $metaAuthor = auth()->user()->name;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '-' . $request->file('image')->getClientOriginalName();
            $request->image->move(public_path('images/blogs'), $imageName);
            $imagePath = 'images/blogs/' . $imageName;
        }

        $filldata = [
            "user_id" => auth()->user()->id,
            "title" => $request->title,
            "slug" => $slug,
            "description" => Purifier::clean($request->description),
            "image" => $imagePath,
        ];

        // $sendData = [
        //     "subject" => "New blog created",
        //     "title" => $request->title,
        //     "description" => $request->description,
        //     "image" => $imagePath,
        //     "category_id" => $request->category_id,
        // ];
        $blog = Blog::create($filldata);
        if ($request->tags) {
            $tags = explode(',', $request->tags);
            foreach ($tags as $tag) {
                $blog->tags()->create([
                    'name' => $tag
                ]);
            }
        }
        // Http::post("https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTZkMDYzNTA0MzI1MjZlNTUzMDUxMzQi_pc", $sendData);
        return response()->json([
            "status" => true,
            "message" => "Blog created successfully",
            "data" => [
                "title" => $blog->title,
                "slug" => $blog->slug,
                "description" => $blog->description,
                "blog" => $blog->load('tags'),
                "seo" => [
                    "meta_title" => $metaTitle,
                    "meta_description" => $metaDescription,
                    "meta_keywords" => $metaKeywords,
                    "meta_author" => $metaAuthor,
                ]
            ]
        ]);
    }

    public function update(BlogUpdateRequest $request)
    {
        $blog_id = $request->blog_id;

        $metaTitle = $request->title;
        $metaDescription = substr(strip_tags($request->description), 0, 160);

        if (is_array($request->tags)) {
            $metaKeywords = implode(', ', $request->tags);
        } elseif (is_string($request->tags)) {
            $metaKeywords = $request->tags;
        } else {
            $metaKeywords = '';
        }

        $metaAuthor = auth()->user()->name;

        $blog = Blog::find($blog_id);
        if (!$blog) {
            return response()->json([
                "status" => false,
                "message" => "Blog not found",
                "data" => []
            ]);
        }

        $imagePath = $blog->image;
        if ($request->hasFile('image')) {
            if ($blog->image && file_exists(public_path($blog->image))) {
                unlink(public_path($blog->image));
            }

            // Store the new image
            $imageName = time() . '-' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images/blogs'), $imageName);
            $imagePath = 'images/blogs/' . $imageName;
        }

        // Handle slug updation

        $slug = $request->slug ?: \Str::slug($request->title);

        $originalSlug = $slug;
        $counter = 1;
        while(Blog::where('slug', $slug)->where('id', '!=', $blog_id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $filldata = [
            "title" => $request->title,
            "description" => Purifier::clean($request->description),
            "image" => $imagePath,
            "category_id" => $request->category_id,
            "slug" => $slug,
        ];

        $blog->update($filldata);
        if ($request->tags) {
            $tags = explode(',', $request->tags);
            foreach ($tags as $tag) {
                $blog->tags()->create([
                    'name' => $tag
                ]);
            }
        }


        // $sendData = [
        //     "subject" => "Blog with id." . $blog_id . " updated",
        //     "title" => $request->title,
        //     "description" => $request->description,
        //     "image" => $request->image,
        //     "category_id" => $request->category_id,
        // ];
        if (!Blog::where("id", $blog_id)->update($filldata)) {
            return response()->json([
                "status" => false,
                "message" => "Blog not found",
                "data" => []
            ]);
        }
        // Http::post("https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTZkMDYzNTA0MzI1MjZlNTUzMDUxMzQi_pc", $sendData);
        return response()->json([
            "status" => true,
            "message" => "Blog updated successfully",
            "data" => Blog::find($blog_id),
            "seo" => [
                "meta_title" => $metaTitle,
                "meta_description" => $metaDescription,
                "meta_keywords" => $metaKeywords,
                "meta_author" => $metaAuthor,
            ]
        ]);
    }

    public function destroy(BlogDeleteRequest $request)
    {
        $blog = Blog::find($request->blog_id);
        $user = auth()->user();
        \Log::info('Authenticated User:', [$user]);

        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "Unauthorized access",
            ], 401); // Unauthorized
        }

        if (!$blog) {
            return response()->json([
                "status" => false,
                "message" => "Blog not found",
                "data" => []
            ]);
        }

        if ($user->isAdmin()) {
            $blog = delete();
        } elseif ($user->isWriter() && $blog->user_id == $user->id) {
            $blog->delete();
        } else {
            return response()->json([
                "status" => false,
                "message" => "You are not authorized to delete this blog",
            ], 403);
        }

        return response()->json([
            "status" => true,
            "message" => "Blog deleted successfully",
        ]);
    }
}
