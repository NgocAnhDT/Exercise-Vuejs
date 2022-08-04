<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function comment(Request $requets)
    {
        $result = Comment::create([
            'comment' => $requets->comment,
            'user_id' => Auth::id(),
            'post_id' => $requets->post_id,
        ]);

        return response()->json($result);
    }
}
