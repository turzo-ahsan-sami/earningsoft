<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Module;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Admin\StoreModulesRequest;
use App\Http\Requests\Admin\UpdateModulesRequest;

class ModulesController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('modules_manage')) {
            return abort(401);
        }

        $modules = Module::all();

        return view('admin.modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('modules_manage')) {
            return abort(401);
        }

        return view('admin.modules.create');
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  App\Http\Requests\Admin\StoreProductsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreModulesRequest $request)
    {
        if (! Gate::allows('modules_manage')) {
            return abort(401);
        }

        Module::create($request->all());

        return redirect()->route('admin.modules.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Admin\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Module $module)
    {
        if (! Gate::allows('modules_manage')) {
            return abort(401);
        }

        return view('admin.modules.show', compact('module'));
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \App\Admin\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Module $module)
    {
        if (! Gate::allows('modules_manage')) {
            return abort(401);
        }

        return view('admin.modules.edit', compact('module'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \App\Http\Requests\UpdateProductsRequest  $request
     * @param  \App\Admin\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateModulesRequest $request, Module $module)
    {
        if (! Gate::allows('modules_manage')) {
            return abort(401);
        }

        $module->update($request->all());

        return redirect()->route('admin.modules.index');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Admin\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Module $module)
    {
        if (! Gate::allows('modules_manage')) {
            return abort(401);
        }

        $module->delete();

        return redirect()->route('admin.modules.index');
    }

    /**
     * Delete all selected products at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('modules_manage')) {
            return abort(401);
        }
        Module::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
