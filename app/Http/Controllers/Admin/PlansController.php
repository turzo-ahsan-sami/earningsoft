<?php

namespace App\Http\Controllers\Admin;

use Rinvex\Subscriptions\Models\Plan;
use App\Admin\Module;
use App\Admin\PlanType;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

use App\Http\Requests\Admin\StorePlansRequest;
use App\Http\Requests\Admin\UpdatePlansRequest;

class PlansController extends Controller
{
    /**
    * Display a listing of the plans.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        $plans = Plan::all();

        return view('admin.plans.index', compact('plans'));
    }

    /**
    * Show the form for creating a new plan.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        $modules = Module::get()->pluck('name', 'id');
        $trialPlanTypes = PlanType::whereIn('slug', ['day', 'month'])->get()->pluck('name', 'slug');
        $invoicePlanTypes = PlanType::where('slug', '!=', 'day')->get()->pluck('name', 'slug');

        return view('admin.plans.create', compact('modules', 'trialPlanTypes', 'invoicePlanTypes'));
    }

    /**
    * Store a newly created plan in storage.
    *
    * @param  App\Http\Requests\Admin\StorePlansRequest  $request
    * @return \Illuminate\Http\Response
    */
    public function store(StorePlansRequest $request)
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        // dd($request->all());

        $create = $request->except('modules');
        //dd($create);

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/plan/';
            $file->move($destinationPath, $filename);
            $create['image'] = $filename;
        }

        $plan = Plan::create($create);
        $modules = $request->input('modules') ? $request->input('modules') : [];
        $plan->modules()->attach($modules);

        // previous code for json format input
        // $create = $request->all();
        // $modules =json_encode($request->input('modules'), true);
        // $create['modules'] = $modules;
        // Plan::create($create);

        return redirect()->route('admin.plans.index');
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Admin\Plan  $plan
    * @return \Illuminate\Http\Response
    */
    public function show(Plan $plan)
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        return view('admin.plans.show', compact('plan'));
    }

    /**
    * Show the form for editing the specified plan.
    *
    * @param  \App\Admin\Plan  $plan
    * @return \Illuminate\Http\Response
    */
    public function edit(Plan $plan)
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        $modules = Module::get()->pluck('name', 'id');
        $trialPlanTypes = PlanType::whereIn('slug', ['day', 'month'])->get()->pluck('name', 'slug');
        $invoicePlanTypes = PlanType::where('slug', '!=', 'day')->get()->pluck('name', 'slug');
        // dd($features);

        return view('admin.plans.edit', compact('plan','modules', 'trialPlanTypes', 'invoicePlanTypes'));
    }

    /**
    * Update the specified plan in storage.
    *
    * @param  \App\Http\Requests\UpdatePlansRequest  $request
    * @param  \App\Admin\Plan  $plan
    * @return \Illuminate\Http\Response
    */
    public function update(UpdatePlansRequest $request, Plan $plan)
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }
        // dd($request->all());
        $update = $request->except('modules');

        if ($request->file('image')) {

            if($plan->image != ''  && $plan->image != null){
                $oldFile = base_path() . '/public/images/plan/' . $plan->image;
                unlink($oldFile);
            }

            $file = $request->file('image');
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/plan/';
            $file->move($destinationPath, $filename);
            $update['image'] = $filename;
        }
        // dd($update);

        $plan->modules()->detach($plan->modules);
        $plan->update($update);
        $modulesToUpdate = $request->input('modules') ? $request->input('modules') : [];
        $plan->modules()->attach($modulesToUpdate);

        // previous code for json format update
        // $request['modules'] = json_encode($request->input('modules'), true);
        // $plan->update($request->all());

        return redirect()->route('admin.plans.index');
    }

    /**
    * Remove the specified plan from storage.
    *
    * @param  \App\Admin\Plan  $plan
    * @return \Illuminate\Http\Response
    */
    public function destroy(Plan $plan)
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        $plan->delete();
        $plan->modules()->detach($plan->modules);

        return redirect()->route('admin.plans.index');
    }

    /**
    * Delete all selected plans at once.
    *
    * @param Request $request
    */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('products_manage')) {
            return abort(401);
        }

        $plans = Plan::whereIn('id', request('ids'))->get();

        foreach ($plans as $key => $plan) {
            $plan->modules()->detach($plan->modules);
        }

        Plan::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
