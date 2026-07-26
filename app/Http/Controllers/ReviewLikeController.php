<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewLikeController extends Controller
{
    public function toggle(Request $request, Review $review)
    {
        $user = $request->user();

        if ($review->user_id === $user->id) {
            return back()->with('error', '自分のレビューには「いいね」できません。');
        }

        $user->likedReviews()->toggle($review->id);

        return back();
    }
}
