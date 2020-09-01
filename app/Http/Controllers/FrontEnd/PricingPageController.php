<?php

namespace App\Http\Controllers\FrontEnd;
use Rinvex\Subscriptions\Models\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class PricingPageController extends Controller
{
    public function index()
    {
        //

        $planList = Plan::all();
       //  $productList = DB::table('products')
       // ->select('products.*,plans.*,discounts.*')
       // ->leftJoin('plans', 'plans.id', '=', 'products.planId')
       // ->leftJoin('discounts', 'discounts.productId', '=','products.Id')
       // ->get();
        return view('frontend.pricing', compact('planList'));
    }

    
}
