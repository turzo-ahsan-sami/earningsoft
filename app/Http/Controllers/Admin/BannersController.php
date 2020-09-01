<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Banner;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Admin\StoreBannersRequest;
use App\Http\Requests\Admin\UpdateBannersRequest;
use Illuminate\Support\Str;

class BannersController extends Controller
{
    /**
    * Display a listing of the banners.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        if (! Gate::allows('banners_manage')) {
            return abort(401);
        }

        $banners = Banner::all();

        return view('admin.banners.index', compact('banners'));
    }

    /**
    * Show the form for creating a new banner.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        if (! Gate::allows('banners_manage')) {
            return abort(401);
        }

        return view('admin.banners.create');
    }

    /**
    * Store a newly created banner in storage.
    *
    * @param  App\Http\Requests\Admin\StoreBannersRequest  $request
    * @return \Illuminate\Http\Response
    */
    public function store(StoreBannersRequest $request)
    {
        if (! Gate::allows('banners_manage')) {
            return abort(401);
        }
        $create = $request->all();
        if ($request->file('banner_image')) {
            $file = $request->file('banner_image');
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/banner/';
            $file->move($destinationPath,$filename);
        }


        if($request->file('banner_image')){
            $create['banner_image'] = $filename;
        }

        if ($request->file('mini_image')) {
            $file = $request->file('mini_image');
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/banner/';
            $file->move($destinationPath,$filename);
        }


        if($request->file('mini_image')){
            $create['mini_image'] = $filename;
        }

        Banner::create($create);

        return redirect()->route('admin.banners.index');
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Admin\Banner  $banner
    * @return \Illuminate\Http\Response
    */
    public function show(Banner $banner)
    {
        if (! Gate::allows('banners_manage')) {
            return abort(401);
        }

        return view('admin.banners.show', compact('banner'));
    }

    /**
    * Show the form for editing the specified banner.
    *
    * @param  \App\Admin\Banner  $banner
    * @return \Illuminate\Http\Response
    */
    public function edit(Banner $banner)
    {
        if (! Gate::allows('banners_manage')) {
            return abort(401);
        }

        return view('admin.banners.edit', compact('banner'));
    }

    /**
    * Update the specified banner in storage.
    *
    * @param  \App\Http\Requests\UpdateBannersRequest  $request
    * @param  \App\Admin\Banner  $banner
    * @return \Illuminate\Http\Response
    */
    public function update(UpdateBannersRequest $request, Banner $banner)
    {
        if (! Gate::allows('banners_manage')) {
            return abort(401);
        }

        $create = $request->all();
        if ($request->file('banner_image')) {
            if($banner->file != ''  && $banner->file != null){
                $file_old = $path.$banner->file;
                unlink($file_old);
            }
            $file = $request->file('banner_image');

            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/banner/';
            $file->move($destinationPath,$filename);
        }


        if($request->file('banner_image')){
            $create['banner_image'] = $filename;
        }


        if ($request->file('mini_image')) {
            if($banner->file != ''  && $banner->file != null){
                $file_old = $path.$banner->file;
                unlink($file_old);
            }
            $file = $request->file('mini_image');

            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/banner/';
            $file->move($destinationPath,$filename);
        }


        if($request->file('mini_image')){
            $create['mini_image'] = $filename;
        }


        $banner->update($create);

        return redirect()->route('admin.banners.index');
    }

    /**
    * Remove the specified banner from storage.
    *
    * @param  \App\Admin\Banner  $banner
    * @return \Illuminate\Http\Response
    */
    public function destroy(Banner $banner)
    {
        if (! Gate::allows('banners_manage')) {
            return abort(401);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index');
    }

    /**
    * Delete all selected banners at once.
    *
    * @param Request $request
    */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('banners_manage')) {
            return abort(401);
        }
        Banner::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
