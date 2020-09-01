@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.training.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.trainings.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- title --}}
            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                <label for="title">{{ trans('cruds.training.fields.title') }}*</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($training) ? $training->title : '') }}" required>
                @if($errors->has('title'))
                    <em class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.training.fields.title_helper') }}
                </p>
            </div>

            {{-- Number Of Trainee --}}
            <div class="form-group {{ $errors->has('numberOfTrainee') ? 'has-error' : '' }}">
                <label for="numberOfTrainee">{{ trans('cruds.training.fields.numberOfTrainee') }}*</label>
                <input type="text" id="numberOfTrainee" name="numberOfTrainee" class="form-control" value="{{ old('numberOfTrainee', isset($training) ? $training->numberOfTrainee : '') }}" required>
                @if($errors->has('numberOfTrainee'))
                    <em class="invalid-feedback">
                        {{ $errors->first('numberOfTrainee') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.training.fields.numberOfTrainee_helper') }}
                </p>
            </div>

            {{-- price --}}
            <div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
                <label for="price">{{ trans('cruds.training.fields.price') }}*</label>
                <input type="text" id="price" name="price" class="form-control" value="{{ old('price', isset($training) ? $training->price : '') }}" required>
                @if($errors->has('price'))
                    <em class="invalid-feedback">
                        {{ $errors->first('price') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.training.fields.price_helper') }}
                </p>
            </div>

            {{-- description --}}
            <div class="form-group {{ $errors->has('desc') ? 'has-error' : '' }}">
                <label for="desc">{{ trans('cruds.training.fields.desc') }}</label>
                <input type="text" id="desc" name="desc" class="form-control" value="{{ old('desc', isset($training) ? $training->desc : '') }}">
                @if($errors->has('desc'))
                    <em class="invalid-feedback">
                        {{ $errors->first('desc') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.training.fields.desc_helper') }}
                </p>
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection
