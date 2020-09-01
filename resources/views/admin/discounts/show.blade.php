@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.discount.title') }}
        </div>

        <div class="card-body">
            <div class="mb-2">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.discount.fields.title') }}
                            </th>
                            <td>
                                {{ $discount->title }}
                            </td>
                        </tr>

                        <tr>
                            <th>
                                {{ trans('cruds.discount.fields.planId') }}
                            </th>
                            <td>
                                {{ $discount->plan->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.discount.fields.discount_type') }}
                            </th>
                            <td>
                                {{ $discount->discount_type }}
                            </td>
                        </tr>

                        <tr>
                            <th>
                                {{ trans('cruds.discount.fields.value') }}
                            </th>
                            <td>
                                {{ $discount->value }}
                            </td>
                        </tr>

                        <tr>
                            <th>
                                {{ trans('cruds.discount.fields.desc') }}
                            </th>
                            <td>
                                {{ $discount->desc }}
                            </td>
                        </tr>

                        <tr>
                            <th>
                                {{ trans('cruds.discount.fields.effective_date') }}
                            </th>
                            <td>
                                {{ \Carbon\Carbon::parse($discount->effective_date)->format('d/m/Y')}}
                            </td>
                        </tr>

                        <tr>
                            <th>
                                {{ trans('cruds.discount.fields.end_date') }}
                            </th>
                            <td>
                                {{ \Carbon\Carbon::parse($discount->end_date)->format('d/m/Y')}}
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
