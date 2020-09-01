@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.module.title') }}
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    {{-- <tr>
                        <th>
                            {{ trans('cruds.module.fields.id') }}
                        </th>
                        <td>
                            {{ $module->id }}
                        </td>
                    </tr> --}}
                    <tr>
                        <th>
                            {{ trans('cruds.module.fields.name') }}
                        </th>
                        <td>
                            {{ $module->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.module.fields.code') }}
                        </th>
                        <td>
                            {{ $module->code }}
                        </td>
                    </tr>

                       <tr>
                        <th>
                            {{ trans('cruds.module.fields.slug') }}
                        </th>
                        <td>
                            {{ $module->slug }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.module.fields.desc') }}
                        </th>
                        <td>
                            {{ $module->description }}
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
