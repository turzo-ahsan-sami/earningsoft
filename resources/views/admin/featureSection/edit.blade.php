@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.featureSection.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.featureSection.update", [$featureSection->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
             {{-- name --}}
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.featureSection.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($featureSection) ? $featureSection->name : '') }}" required>
                @if($errors->has('name'))
                    <em class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.featureSection.fields.name_helper') }}
                </p>
            </div>
    

        
            {{-- description --}}
            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                <label for="description">{{ trans('cruds.featureSection.fields.description') }}</label>
                <textarea id="description" rows="3" name="description" class="form-control" value="{{ old('description', isset($featureSection) ? $featureSection->description : '') }}">{{ old('description', isset($featureSection) ? $featureSection->description : '') }}</textarea>
                @if($errors->has('description'))
                    <em class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.featureSection.fields.description_helper') }}
                </p>
            </div>

                 {{-- image --}}
            <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                <label for="image">{{ trans('cruds.featureSection.fields.image') }}*</label>
                   @if ("/public/images/featureSection/{{ $featureSection->image }}")
        <img src="{{ asset("images/featureSection/$featureSection->image") }}" width="100">
         @else
            <p>No image found</p>
    @endif
                <input type="file" id="image" name="image" class="form-control" value="{{ old('image', isset($featureSection) ? $featureSection->image : '') }}">
                @if($errors->has('image'))
                    <em class="invalid-feedback">
                        {{ $errors->first('image') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.featureSection.fields.image_helper') }}
                </p>
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection


