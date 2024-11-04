<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Models\Category;
use App\Models\Comments;
use App\Models\Post;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Storage;
use Str;

class BlogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Post::query()->where('status', 'published');

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('sort')) {
            switch ($request->input('sort')) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'most_popular':
                    $query->orderBy('views', 'desc');
                    break;
                case 'least_popular':
                    $query->orderBy('views', 'asc');
                    break;
            }
        }

        $posts = $query->get();

        return response()->json([
            'message' => 'Posts fetched successfully.',
            'data' => $posts,
        ], 200);
    }

    public function store(StoreBlogRequest $request): JsonResponse
    {
        $userId = Auth::id();

        if (is_null($userId)) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        $categoryInput = $request->input('category_id');

        if (is_numeric($categoryInput)) {
            $categoryId = $categoryInput;
        } else {
            $category = Category::where('name', $categoryInput)->first();

            if (!$category) {
                return response()->json(['message' => 'Category not found.'], 404);
            }

            $categoryId = $category->id;
        }


        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->move(public_path('images'), $request->file('image')->getClientOriginalName());
            $imageUrl = 'http://localhost:80/images/' . $request->file('image')->getClientOriginalName();
        } else {
            return response()->json(['message' => 'Image not provided.'], 400);
        }

        $slug = Str::slug($request->input('title'));

        $post = Post::create([
            'user_id' => $userId,
            'category_id' => $categoryId,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'image' => $imageUrl,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'slug' => $slug,
            'status' => 'scheduled',
        ]);

        return response()->json([
            'message' => 'Post created successfully.',
            'data' => $post
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found.',
            ], 404);
        }

        $post->increment('views');

        return response()->json([
            'message' => 'Post fetched successfully.',
            'data' => $post,
        ], 200);
    }

    public function getCategories(): JsonResponse
    {
        $categories = Category::where('status', 1)->get();
        return response()->json([
            'message' => 'Categories fetched successfully.',
            'data' => $categories
        ], 200);
    }

}
