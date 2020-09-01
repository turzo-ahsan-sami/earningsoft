@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.training.title') }}
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    {{-- <tr>
                        <th>
                            {{ trans('cruds.training.fields.id') }}
                        </th>
                        <td>
                            {{ $training->id }}
                        </td>
                    </tr> --}}
                    <tr>
                        <th>
                            {{ trans('cruds.training.fields.title') }}
                        </th>
                        <td>
                            {{ $training->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.training.fields.numberOfTrainee') }}
                        </th>
                        <td>
                            {{ $training->numberOfTrainee }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.training.fields.price') }}
                        </th>
                        <td>
                            {{ $training->price }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ trans('cruds.training.fields.desc') }}
                        </th>
                        <td>
                            {{ $training->description }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>

        <nav class="mb-3">
            <div class="nav nav-tabs">

            </div>
        </nav>
        <div class="tab-content">

        </div>
    </div>
</div>
@endsection
