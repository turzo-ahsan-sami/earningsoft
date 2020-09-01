<?php

namespace App\Http\Controllers\Admin;

use App\Admin\UserReview;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Admin\StoreUserReviewRequest;
use App\Http\Requests\Admin\UpdateUserReviewRequest;
use Illuminate\Support\Str;
use App\User;
use DB;

class UserReviewController extends Controller
{
    /**
    * Display a listing of the products.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        if (! Gate::allows('user_review_manage')) {
            return abort(401);
        }
        $userReviews = DB::table('user_review')
        ->select('users.id','users.name','user_review.*')
        ->join('users','user_review.user_id','=','users.id')
        ->get();

        return view('admin.userReview.index', compact('userReviews'));
    }

    /**
    * Show the form for creating a new product.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        if (! Gate::allows('user_review_manage')) {
            return abort(401);
        }

        $users = User::all();

        return view('admin.userReview.create', compact('users'));
    }

    /**
    * Store a newly created product in storage.
    *
    * @param  App\Http\Requests\Admin\StoreProductsRequest  $request
    * @return \Illuminate\Http\Response
    */
    public function store(StoreUserReviewRequest $request)
    {
        if (! Gate::allows('user_review_manage')) {
            return abort(401);
        }
        $create = $request->all();
      
        UserReview::create($create);

        return redirect()->route('admin.userReview.index');
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function show(UserReview $userReview)
    {
        if (! Gate::allows('user_review_manage')) {
            return abort(401);
        }

        $data['name'] = User::where('id',$userReview->user_id)->value('name');
        $data['userReview'] =  $userReview;
        return view('admin.userReview.show', $data);
    }

    /**
    * Show the form for editing the specified product.
    *
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function edit(UserReview $userReview)
    {
        if (! Gate::allows('user_review_manage')) {
            return abort(401);
        }
        $data['users'] = User::all();
        $data['userReview'] = $userReview;
        return view('admin.userReview.edit', $data);
    }

    /**
    * Update the specified product in storage.
    *
    * @param  \App\Http\Requests\UpdateProductsRequest  $request
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function update(UpdateUserReviewRequest $request, UserReview $userReview)
    {
        if (! Gate::allows('user_review_manage')) {
            return abort(401);
        }


        $create = $request->all();

        $userReview->update($create);

        return redirect()->route('admin.userReview.index');
    }

    /**
    * Remove the specified product from storage.
    *
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function destroy(UserReview $userReview)
    {
        if (! Gate::allows('user_review_manage')) {
            return abort(401);
        }

        $userReview->delete();

        return redirect()->route('admin.userReview.index');
    }

    /**
    * Delete all selected products at once.
    *
    * @param Request $request
    */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('user_review_manage')) {
            return abort(401);
        }
        UserReview::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
