<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Product;
use App\Admin\Module;
use App\Admin\Plan;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

use App\Http\Requests\Admin\StoreProductsRequest;
use App\Http\Requests\Admin\UpdateProductsRequest;

class ProductsController extends Controller
{
    /**
    * Display a listing of the products.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        $products = Product::all();

        return view('admin.products.index', compact('products'));
    }

    /**
    * Show the form for creating a new product.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        $modules = Module::get()->pluck('name', 'id');
        $plans = Plan::get()->pluck('name', 'id');

        return view('admin.products.create', compact('modules', 'plans'));
    }

    /**
    * Store a newly created product in storage.
    *
    * @param  App\Http\Requests\Admin\StoreProductsRequest  $request
    * @return \Illuminate\Http\Response
    */
    public function store(StoreProductsRequest $request)
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        $create = $request->except('modules');

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/product/';
            $file->move($destinationPath, $filename);
            $create['image'] = $filename;
        }

        $product = Product::create($create);
        $modules = $request->input('modules') ? $request->input('modules') : [];
        $product->modules()->attach($modules);

        // previous code for json format input
        // $create = $request->all();
        // $modules =json_encode($request->input('modules'), true);
        // $create['modules'] = $modules;
        // Product::create($create);

        return redirect()->route('admin.products.index');
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function show(Product $product)
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        return view('admin.products.show', compact('product'));
    }

    /**
    * Show the form for editing the specified product.
    *
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function edit(Product $product)
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        $modules = Module::get()->pluck('name', 'id');
        $plans = Plan::get()->pluck('name', 'id');
        $features = $product->features;
        // dd($features);

        return view('admin.products.edit', compact('product','modules','plans', 'features'));
    }

    /**
    * Update the specified product in storage.
    *
    * @param  \App\Http\Requests\UpdateProductsRequest  $request
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function update(UpdateProductsRequest $request, Product $product)
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }
        // dd($request->all());
        $update = $request->except('modules');

        if ($request->file('image')) {

            if($product->image != ''  && $product->image != null){
                $oldFile = base_path() . '/public/images/product/' . $product->image;
                unlink($oldFile);
            }

            $file = $request->file('image');
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/product/';
            $file->move($destinationPath, $filename);
            $update['image'] = $filename;
        }
        // dd($update);

        $product->modules()->detach($product->modules);
        $product->update($update);
        $modulesToUpdate = $request->input('modules') ? $request->input('modules') : [];
        $product->modules()->attach($modulesToUpdate);

        // previous code for json format update
        // $request['modules'] = json_encode($request->input('modules'), true);
        // $product->update($request->all());

        return redirect()->route('admin.products.index');
    }

    /**
    * Remove the specified product from storage.
    *
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function destroy(Product $product)
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        $product->delete();
        $product->modules()->detach($product->modules);

        return redirect()->route('admin.products.index');
    }

    /**
    * Delete all selected products at once.
    *
    * @param Request $request
    */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        $products = Product::whereIn('id', request('ids'))->get();

        foreach ($products as $key => $product) {
            $product->modules()->detach($product->modules);
        }

        Product::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
