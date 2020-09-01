<?php

namespace App\Http\Controllers\Admin;

use App\Admin\PlanType;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Admin\StorePlanTypesRequest;
use App\Http\Requests\Admin\UpdatePlanTypesRequest;

class PlanTypesController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('planTypes_manage')) {
            return abort(401);
        }

        $planTypes = PlanType::all();

        return view('admin.planTypes.index', compact('planTypes'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('planTypes_manage')) {
            return abort(401);
        }

        return view('admin.planTypes.create');
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  App\Http\Requests\Admin\StoreProductsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePlanTypesRequest $request)
    {
        if (! Gate::allows('planTypes_manage')) {
            return abort(401);
        }

        PlanType::create($request->all());

        return redirect()->route('admin.planTypes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Admin\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(PlanType $planType)
    {
        if (! Gate::allows('planTypes_manage')) {
            return abort(401);
        }

        return view('admin.planTypes.show', compact('planType'));
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \App\Admin\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(PlanType $planType)
    {
        if (! Gate::allows('planTypes_manage')) {
            return abort(401);
        }

        return view('admin.planTypes.edit', compact('planType'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \App\Http\Requests\UpdateProductsRequest  $request
     * @param  \App\Admin\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePlanTypesRequest $request, PlanType $planType)
    {
        if (! Gate::allows('planTypes_manage')) {
            return abort(401);
        }

        $planType->update($request->all());

        return redirect()->route('admin.planTypes.index');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Admin\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlanType $planType)
    {
        if (! Gate::allows('planTypes_manage')) {
            return abort(401);
        }

        $planType->delete();

        return redirect()->route('admin.planTypes.index');
    }

    /**
     * Delete all selected products at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('planTypes_manage')) {
            return abort(401);
        }
        PlanType::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
