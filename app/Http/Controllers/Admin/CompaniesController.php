<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Company;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Admin\StoreCompaniesRequest;
use App\Http\Requests\Admin\UpdateCompaniesRequest;
use Illuminate\Support\Str;

class CompaniesController extends Controller
{
    /**
    * Display a listing of the products.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        if (! Gate::allows('companies_manage')) {
            return abort(401);
        }

        $companies = Company::all();

        return view('admin.companies.index', compact('companies'));
    }

    /**
    * Show the form for creating a new product.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        if (! Gate::allows('companies_manage')) {
            return abort(401);
        }

        return view('admin.companies.create');
    }

    /**
    * Store a newly created product in storage.
    *
    * @param  App\Http\Requests\Admin\StoreProductsRequest  $request
    * @return \Illuminate\Http\Response
    */
    public function store(StoreCompaniesRequest $request)
    {
        if (! Gate::allows('companies_manage')) {
            return abort(401);
        }
        $create = $request->all();
        if ($request->file('logo')) {
            $file = $request->file('logo');
            //$filename = $file->getClientOriginalName();
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/company/';
            $file->move($destinationPath,$filename);
        }


        if($request->file('logo')){
            $create['logo'] = $filename;
        }

        Company::create($create);

        return redirect()->route('admin.companies.index');
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function show(Company $company)
    {
        if (! Gate::allows('companies_manage')) {
            return abort(401);
        }

        return view('admin.companies.show', compact('company'));
    }

    /**
    * Show the form for editing the specified product.
    *
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function edit(Company $company)
    {
        if (! Gate::allows('companies_manage')) {
            return abort(401);
        }

        return view('admin.companies.edit', compact('company'));
    }

    /**
    * Update the specified product in storage.
    *
    * @param  \App\Http\Requests\UpdateProductsRequest  $request
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function update(UpdateCompaniesRequest $request, Company $company)
    {
        if (! Gate::allows('companies_manage')) {
            return abort(401);
        }

        $create = $request->all();
        if ($request->file('logo')) {
            if($company->file != ''  && $company->file != null){
                $file_old = $path.$company->file;
                unlink($file_old);
            }
            $file = $request->file('logo');

            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/company/';
            $file->move($destinationPath,$filename);
        }


        if($request->file('logo')){
            $create['logo'] = $filename;
        }

        $company->update($create);

        return redirect()->route('admin.companies.index');
    }

    /**
    * Remove the specified product from storage.
    *
    * @param  \App\Admin\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function destroy(Company $company)
    {
        if (! Gate::allows('companies_manage')) {
            return abort(401);
        }

        $company->delete();

        return redirect()->route('admin.companies.index');
    }

    /**
    * Delete all selected products at once.
    *
    * @param Request $request
    */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('companies_manage')) {
            return abort(401);
        }
        Company::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
