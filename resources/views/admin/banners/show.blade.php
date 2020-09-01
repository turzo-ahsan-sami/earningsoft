@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.banner.title') }}
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    {{-- <tr>
                        <th>
                            {{ trans('cruds.banner.fields.id') }}
                        </th>
                        <td>
                            {{ $banner->id }}
                        </td>
                    </tr> --}}
                    <tr>
                        <th>
                            {{ trans('cruds.banner.fields.banner_text1') }}
                        </th>
                        <td>
                            {{ $banner->banner_text1 }}
                        </td>
                    </tr>

                      <tr>
                        <th>
                            {{ trans('cruds.banner.fields.banner_text2') }}
                        </th>
                        <td>
                            {{ $banner->banner_text2 }}
                        </td>
                    </tr>
                      <tr>
                        <th>
                            {{ trans('cruds.banner.fields.banner_text3') }}
                        </th>
                        <td>
                            {{ $banner->banner_text3 }}
                        </td>
                    </tr>

                      <tr>
                        <th>
                            {{ trans('cruds.banner.fields.button_text1') }}
                        </th>
                        <td>
                            {{ $banner->button_text1 }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ trans('cruds.banner.fields.button_text2') }}
                        </th>
                        <td>
                            {{ $banner->button_text2 }}
                        </td>
                    </tr>


                

                     <tr>
                        <th>
                            {{ trans('cruds.banner.fields.banner_image') }}
                        </th>
                        <td>
                           <img src="{{ asset("images/banner/$banner->banner_image") }}" width="60">
                        </td>
                    </tr>

                     <tr>
                        <th>
                            {{ trans('cruds.banner.fields.mini_image') }}
                        </th>
                        <td>
                           <img src="{{ asset("images/banner/$banner->mini_image") }}" width="60">
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
