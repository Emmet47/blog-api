<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\User;
use App\Notifications\CommentSubmittedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Notification;

class CommentController extends Controller
{
    public function index($postId)
    {
        $comments = Comments::where('post_id', $postId)->where('status', 'published')->get();

        if ($comments->isEmpty()) {
            return response()->json([
                'message' => 'No comments found for this post.',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Comments fetched successfully.',
            'data' => $comments
        ], 200);
    }

    public function store(Request $request, $postId)
    {
        $userId = Auth::id();

        $validatedData = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        if (is_null($userId)) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        $comment = Comments::create([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $validatedData['content'],
            'status' => 'scheduled',
        ]);

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new CommentSubmittedNotification($comment));

        return response()->json(['message' => 'Comment submitted and waiting for approval.'], 201);

    }
}
