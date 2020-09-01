@extends('layouts.frontend')

@section('content')
    <div class="container">
        <div class="row wow fadeIn">
            <div class="col-md-6 white-text text-center text-md-left" style="margin-bottom: 50px !important">
                <h4 class="headerPlan">Your EarningSoft Online Plan</h4>
                <p class="pDescriptionText">EarningSoft Online Simple Start</p>
                <ul class="ulBullet">
                    <li class="ulListItem">Automatic data back-ups</li>
                    <li class="ulListItem">Bank-level security and encryption</li>
                    <li class="ulListItem">Access data from all your devices</li>

                </ul>
                <p class="pDescriptionTextLast">
                    *If you have an existing EarningSoft Online account, you can add / sign up for a new company using your existing sign in details. This single sign in allows you to view and manage multiple companies, including your existing account.
                </p>
                <!--   <a href="http:/www.facebook.com" target="_blank"  class="btn btn-indigo btn-lg">Facebook</a> -->
            </div>
            <div class="col-md-6 col-xl-5 " style="margin-bottom: 50px !important">
                <h4 class="headerPlan">Customer Signup</h4>
                <div class="card">
                    <div class="card-body">
                        <form action="{{ url("customer/store") }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="planId" value="{{ $planId }}">
                            <input type="hidden" name="purchaseType" value="{{ $purchaseType }}">
                            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                <label for="name">{{ trans('cruds.user.fields.name') }}*</label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($user) ? $user->name : '') }}" required>
                                @if($errors->has('name'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('name') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('cruds.user.fields.name_helper') }}
                                </p>
                            </div>
                            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                <label for="email">{{ trans('cruds.user.fields.email') }}*</label>
                                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', isset($user) ? $user->email : '') }}" required>
                                @if($errors->has('email'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('email') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('cruds.user.fields.email_helper') }}
                                </p>
                            </div>
                            <div class="form-group {{ $errors->has('mobile') ? 'has-error' : '' }}">
                                <label for="mobile">{{ trans('cruds.user.fields.mobile') }}*</label>
                                <input type="text" id="mobile" name="mobile" class="form-control" value="{{ old('mobile', isset($user) ? $user->mobile : '') }}" required>
                                @if($errors->has('mobile'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('mobile') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('cruds.user.fields.mobile_helper') }}
                                </p>
                            </div>
                            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                                <label for="password">{{ trans('cruds.user.fields.password') }}</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                                @if($errors->has('password'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('password') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('cruds.user.fields.password_helper') }}
                                </p>
                            </div>
                            <div>
                                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent

@endsection
