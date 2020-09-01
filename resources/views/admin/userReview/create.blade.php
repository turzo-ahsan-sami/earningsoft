@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.userReview.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.userReview.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- name --}}
            <div class="form-group {{ $errors->has('user_id') ? 'has-error' : '' }}">
                <label for="user_id">{{ trans('cruds.userReview.fields.user_id') }}*</label>
                <select class="form-control" id="user_id" name="user_id">
                    <option>Select user name</option>
                    @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>
                @if($errors->has('user_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('user_id') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.userReview.fields.name_helper') }}
                </p>
            </div>
    

            {{--Description--}}
            <div class="form-group {{ $errors->hasDescription ? 'has-error' : '' }}">
                <label for="address">{{ trans('cruds.userReview.fields.comment') }}</label>
                <textarea id="comment" rows="3" name="comment" class="form-control" value="{{ old('comment', isset($userReview) ? $userReview->comment : '') }}"></textarea>
                @if($errors->has('comment'))
                    <em class="invalid-feedback">
                        {{ $errors->first('comment') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.userReview.fields.comment_helper') }}
                </p>
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection
