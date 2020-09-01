<?php

namespace App\Http\Controllers\FrontEnd;
use App\Admin\Banner;
use App\Admin\FeatureSection;
use Rinvex\Subscriptions\Models\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class HomepageController extends Controller
{
    public function index()
    {
       
        $bannerInfo = Banner::first();
        $featureSections = FeatureSection::all();
        $planList = Plan::all();
        $userReviews = DB::table('user_review')
       ->select('user_review.*','users.*')
       ->Join('users','users.id', '=','user_review.user_id')
       ->get();

       $userReviewsLatest = DB::table('user_review')
       ->select('user_review.*','users.*')
       ->Join('users','users.id', '=','user_review.user_id')
       ->latest('user_review.id')
       ->first();
        // return view('frontend.homepage', compact('bannerInfo','logo'));
        return view('frontend.homepage', compact('bannerInfo','featureSections','planList','userReviews','userReviewsLatest'));
    }
}
