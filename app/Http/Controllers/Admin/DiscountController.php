<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Discount;
use App\Admin\Product;
use App\Http\Controllers\Controller;
use Rinvex\Subscriptions\Models\Plan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Admin\StoreDiscountsRequest;
use App\Http\Requests\Admin\UpdateDiscountsRequest;

class DiscountController extends Controller
{
    /**
     * Display a listing of the discounts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('discounts_manage')) {
            return abort(401);
        }

        $discounts = Discount::all();

        return view('admin.discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new discounts.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('discounts_manage')) {
            return abort(401);
        }
        $plans = Plan::get()->pluck('name', 'id');

        return view('admin.discounts.create', compact('plans'));
    }

    /**
     * Store a newly created discount in storage.
     *
     * @param  App\Http\Requests\Admin\StoreDiscountsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDiscountsRequest $request)
    {
        if (! Gate::allows('discounts_manage')) {
            return abort(401);
        }

        Discount::create($request->all());

        return redirect()->route('admin.discounts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Admin\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function show(Discount $discount)
    {
        if (! Gate::allows('discounts_manage')) {
            return abort(401);
        }

        return view('admin.discounts.show', compact('discount'));
    }

    /**
     * Show the form for editing the specified discount.
     *
     * @param  \App\Admin\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function edit(Discount $discount)
    {
        if (! Gate::allows('discounts_manage')) {
            return abort(401);
        }
        $plans = Plan::get()->pluck('name', 'id');

        return view('admin.discounts.edit', compact('discount','plans'));
    }

    /**
     * Update the specified discount in storage.
     *
     * @param  \App\Http\Requests\UpdateDiscountsRequest  $request
     * @param  \App\Admin\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDiscountsRequest $request, Discount $discount)
    {
        if (! Gate::allows('discounts_manage')) {
            return abort(401);
        }

        $discount->update($request->all());

        return redirect()->route('admin.discounts.index');
    }

    /**
     * Remove the specified discount from storage.
     *
     * @param  \App\Admin\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function destroy(Discount $discount)
    {
        if (! Gate::allows('discounts_manage')) {
            return abort(401);
        }

        $discount->delete();

        return redirect()->route('admin.discounts.index');
    }

    /**
     * Delete all selected discounts at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('discounts_manage')) {
            return abort(401);
        }
        Discount::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
