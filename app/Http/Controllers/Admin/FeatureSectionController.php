<?php

namespace App\Http\Controllers\Admin;

use App\Admin\FeatureSection;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Admin\StoreFeatureSectionRequest;
use App\Http\Requests\Admin\UpdateFeatureSectionRequest;
use Illuminate\Support\Str;

class FeatureSectionController extends Controller
{
    /**
    * Display a listing of the products.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        if (! Gate::allows('featureSection_manage')) {
            return abort(401);
        }

        $featureSections = FeatureSection::all();

        return view('admin.featureSection.index', compact('featureSections'));
    }

    /**
    * Show the form for creating a new product.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        if (! Gate::allows('featureSection_manage')) {
            return abort(401);
        }

        return view('admin.featureSection.create');
    }

    /**
    * Store a newly created product in storage.
    *
    * @param  App\Http\Requests\Admin\StoreProductsRequest  $request
    * @return \Illuminate\Http\Response
    */
    public function store(StoreFeatureSectionRequest $request)
    {
        if (! Gate::allows('featureSection_manage')) {
            return abort(401);
        }
        $create = $request->all();
        if ($request->file('image')) {
            $file = $request->file('image');
            //$filename = $file->getClientOriginalName();
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/featureSection/';
            $file->move($destinationPath,$filename);
        }


        if($request->file('image')){
            $create['image'] = $filename;
        }

        FeatureSection::create($create);

        return redirect()->route('admin.featureSection.index');
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function show(FeatureSection $featureSection)
    {
        if (! Gate::allows('featureSection_manage')) {
            return abort(401);
        }

        return view('admin.featureSection.show', compact('featureSection'));
    }

    /**
    * Show the form for editing the specified product.
    *
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function edit(FeatureSection $featureSection)
    {
        if (! Gate::allows('featureSection_manage')) {
            return abort(401);
        }

        return view('admin.featureSection.edit', compact('featureSection'));
    }

    /**
    * Update the specified product in storage.
    *
    * @param  \App\Http\Requests\UpdateProductsRequest  $request
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function update(UpdateFeatureSectionRequest $request, FeatureSection $featureSection)
    {
        if (! Gate::allows('featureSection_manage')) {
            return abort(401);
        }

        $create = $request->all();
        if ($request->file('image')) {
            if($featureSection->file != ''  && $featureSection->file != null){
                $file_old = $path.$featureSection->file;
                unlink($file_old);
            }
            $file = $request->file('image');

            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/featureSection/';
            $file->move($destinationPath,$filename);
        }


        if($request->file('image')){
            $create['image'] = $filename;
        }

        $featureSection->update($create);

        return redirect()->route('admin.featureSection.index');
    }

    /**
    * Remove the specified product from storage.
    *
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function destroy(FeatureSection $featureSection)
    {
        if (! Gate::allows('featureSection_manage')) {
            return abort(401);
        }

        $featureSection->delete();

        return redirect()->route('admin.featureSection.index');
    }

    /**
    * Delete all selected products at once.
    *
    * @param Request $request
    */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('featureSection_manage')) {
            return abort(401);
        }
        FeatureSection::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
