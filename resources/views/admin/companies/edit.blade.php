@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.company.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.companies.update", [$company->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
             {{-- name --}}
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.company.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($company) ? $company->name : '') }}" required>
                @if($errors->has('name'))
                    <em class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.company.fields.name_helper') }}
                </p>
            </div>
            {{-- email --}}
            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                <label for="email">{{ trans('cruds.company.fields.email') }}*</label>
                <input type="text" id="email" name="email" class="form-control" value="{{ old('email', isset($company) ? $company->email : '') }}" required>
                @if($errors->has('email'))
                    <em class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.company.fields.email_helper') }}
                </p>
            </div>

              {{-- mobile --}}
            <div class="form-group {{ $errors->has('mobile') ? 'has-error' : '' }}">
                <label for="mobile">{{ trans('cruds.company.fields.mobile') }}*</label>
                <input type="text" id="mobile" name="mobile" class="form-control" value="{{ old('mobile', isset($company) ? $company->mobile : '') }}" required>
                @if($errors->has('mobile'))
                    <em class="invalid-feedback">
                        {{ $errors->first('mobile') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.company.fields.mobile_helper') }}
                </p>
            </div>

        
            {{-- address --}}
            <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                <label for="address">{{ trans('cruds.company.fields.address') }}</label>
                <input type="textarea" id="address" row="3" name="address" class="form-control" value="{{ old('address', isset($company) ? $company->address : '') }}">
                @if($errors->has('address'))
                    <em class="invalid-feedback">
                        {{ $errors->first('address') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.company.fields.address_helper') }}
                </p>
            </div>

                 {{-- website --}}
            <div class="form-group {{ $errors->has('website') ? 'has-error' : '' }}">
                <label for="website">{{ trans('cruds.company.fields.website') }}*</label>
                <input type="text" id="website" name="website" class="form-control" value="{{ old('website', isset($company) ? $company->website : '') }}" required>
                @if($errors->has('website'))
                    <em class="invalid-feedback">
                        {{ $errors->first('website') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.company.fields.website_helper') }}
                </p>
            </div>

                 {{-- logo --}}
            <div class="form-group {{ $errors->has('logo') ? 'has-error' : '' }}">
                <label for="logo">{{ trans('cruds.company.fields.logo') }}*</label>
                   @if ("/public/images/company/{{ $company->logo }}")
        <img src="{{ asset("images/company/$company->logo") }}" width="100">
         @else
            <p>No image found</p>
    @endif
                <input type="file" id="logo" name="logo" class="form-control" value="{{ old('logo', isset($company) ? $company->logo : '') }}">
                @if($errors->has('logo'))
                    <em class="invalid-feedback">
                        {{ $errors->first('logo') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.company.fields.logo_helper') }}
                </p>
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection


