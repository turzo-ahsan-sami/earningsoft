<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Training;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Admin\StoreTrainingsRequest;
use App\Http\Requests\Admin\UpdateTrainingsRequest;

class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('trainings_manage')) {
            return abort(401);
        }

        $trainings = Training::all();

        return view('admin.trainings.index', compact('trainings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('trainings_manage')) {
            return abort(401);
        }

        return view('admin.trainings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Admin\StoreTrainingsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTrainingsRequest $request)
    {
        if (! Gate::allows('trainings_manage')) {
            return abort(401);
        }

        Training::create($request->all());

        return redirect()->route('admin.trainings.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Admin\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function show(Training $training)
    {
        if (! Gate::allows('trainings_manage')) {
            return abort(401);
        }

        return view('admin.trainings.show', compact('training'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Admin\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function edit(Training $training)
    {
        if (! Gate::allows('trainings_manage')) {
            return abort(401);
        }

        return view('admin.trainings.edit', compact('training'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTrainingsRequest  $request
     * @param  \App\Admin\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTrainingsRequest $request, Training $training)
    {
        if (! Gate::allows('trainings_manage')) {
            return abort(401);
        }

        $training->update($request->all());

        return redirect()->route('admin.trainings.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Admin\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function destroy(Training $training)
    {
        if (! Gate::allows('trainings_manage')) {
            return abort(401);
        }

        $training->delete();

        return redirect()->route('admin.trainings.index');
    }

    /**
     * Delete all selected trainings at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('plans_manage')) {
            return abort(401);
        }

        Training::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
