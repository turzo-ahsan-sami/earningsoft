@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.banner.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.banners.update", [$banner->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            {{-- banner_text1 --}}
            <div class="form-group {{ $errors->has('banner_text1') ? 'has-error' : '' }}">
                <label for="banner_text1 ">{{ trans('cruds.banner.fields.banner_text1') }}*</label>
                <input type="text" id="banner_text1" name="banner_text1" class="form-control" value="{{ old('banner_text1', isset($banner) ? $banner->banner_text1 : '') }}" required>
                @if($errors->has('banner_text1'))
                    <em class="invalid-feedback">
                        {{ $errors->first('banner_text1') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.banner.fields.banner_text1_helper') }}
                </p>
            </div>



                {{-- banner_text2 --}}
            <div class="form-group {{ $errors->has('banner_text2') ? 'has-error' : '' }}">
                <label for="banner_text2 ">{{ trans('cruds.banner.fields.banner_text2') }}*</label>
                <input type="text" id="banner_text2" name="banner_text2" class="form-control" value="{{ old('banner_text2', isset($banner) ? $banner->banner_text2 : '') }}" required>
                @if($errors->has('banner_text2'))
                    <em class="invalid-feedback">
                        {{ $errors->first('banner_text2') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.banner.fields.banner_text2_helper') }}
                </p>
            </div>



                {{-- banner_text3 --}}
            <div class="form-group {{ $errors->has('banner_text3') ? 'has-error' : '' }}">
                <label for="banner_text3 ">{{ trans('cruds.banner.fields.banner_text3') }}*</label>
                <input type="text" id="banner_text3" name="banner_text3" class="form-control" value="{{ old('banner_text3', isset($banner) ? $banner->banner_text3 : '') }}" required>
                @if($errors->has('banner_text3'))
                    <em class="invalid-feedback">
                        {{ $errors->first('banner_text3') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.banner.fields.banner_text3_helper') }}
                </p>
            </div>


                   {{-- button_text1 --}}
            <div class="form-group {{ $errors->has('button_text1') ? 'has-error' : '' }}">
                <label for="button_text1 ">{{ trans('cruds.banner.fields.button_text1') }}*</label>
                <input type="text" id="button_text1" name="button_text1" class="form-control" value="{{ old('button_text1', isset($banner) ? $banner->button_text1 : '') }}" required>
                @if($errors->has('button_text1'))
                    <em class="invalid-feedback">
                        {{ $errors->first('button_text1') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.banner.fields.banner_text3_helper') }}
                </p>
            </div>




                   {{-- button_text2 --}}
            <div class="form-group {{ $errors->has('button_text2') ? 'has-error' : '' }}">
                <label for="button_text2">{{ trans('cruds.banner.fields.button_text2') }}*</label>
                <input type="text" id="button_text2" name="button_text2" class="form-control" value="{{ old('button_text2', isset($banner) ? $banner->button_text2 : '') }}" required>
                @if($errors->has('button_text2'))
                    <em class="invalid-feedback">
                        {{ $errors->first('button_text2') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.banner.fields.button_text2_helper') }}
                </p>
            </div>

        
         
        
            {{-- banner image --}}
             <div class="form-group {{ $errors->has('banner_image') ? 'has-error' : '' }}">
                <label for="banner_image">{{ trans('cruds.banner.fields.banner_image') }}*</label>
                   @if ("/public/images/banner/{{ $banner->banner_image }}")
        <img src="{{ asset("images/banner/$banner->banner_image") }}" width="100">
         @else
            <p>No image found</p>
    @endif
                <input type="file" id="banner_image" name="banner_image" class="form-control" value="{{ old('logo', isset($banner) ? $banner->banner_image : '') }}">
                @if($errors->has('banner_image'))
                    <em class="invalid-feedback">
                        {{ $errors->first('banner_image') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.banner.fields.banner_image_helper') }}
                </p>
            </div>


               {{-- mini image --}}
            <div class="form-group {{ $errors->has('mini_image') ? 'has-error' : '' }}">
                <label for="mini_image">{{ trans('cruds.banner.fields.mini_image') }}*</label>
                   @if ("/public/images/banner/{{ $banner->mini_image }}")
        <img src="{{ asset("images/banner/$banner->mini_image") }}" width="100">
         @else
            <p>No image found</p>
    @endif
                <input type="file" id="mini_image" name="mini_image" class="form-control" value="{{ old('logo', isset($banner) ? $banner->mini_image : '') }}">
                @if($errors->has('mini_image'))
                    <em class="invalid-feedback">
                        {{ $errors->first('mini_image') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.banner.fields.mini_image_helper') }}
                </p>
            </div>



            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection


