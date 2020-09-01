@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.planType.title') }}
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    {{-- <tr>
                        <th>
                            {{ trans('cruds.planType.fields.id') }}
                        </th>
                        <td>
                            {{ $planType->id }}
                        </td>
                    </tr> --}}
                    <tr>
                        <th>
                            {{ trans('cruds.planType.fields.name') }}
                        </th>
                        <td>
                            {{ $planType->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.planType.fields.slug') }}
                        </th>
                        <td>
                            {{ $planType->slug }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ trans('cruds.planType.fields.desc') }}
                        </th>
                        <td>
                            {{ $planType->description }}
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
