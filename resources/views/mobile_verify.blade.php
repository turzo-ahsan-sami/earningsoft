@extends('layouts.frontend')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-group">
            <div class="card p-4">
                <div class="card-header">{{ __('Mobile number verification') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('nexmo') }}">
                        {{ csrf_field() }}
                        <h1>
                            <div class="login-logo">
                                <a href="#">
                                    {{ config('app.name') }}
                                </a>
                            </div>
                        </h1>
                        <p class="text-muted"></p>
                        <div>
                            {{ csrf_field() }}
                            <div class="form-group has-feedback">
                                <input type="number" name="code" class="form-control" required="autofocus" placeholder="code">
                                @if($errors->has('code'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('code') }}
                                    </em>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn btn-primary btn-block btn-flat">
                                    Verify
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
