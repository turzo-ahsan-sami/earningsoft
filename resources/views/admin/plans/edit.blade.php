@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.plan.title_singular') }}
        </div>

        <div class="card-body">
            <form action="{{ route("admin.plans.update", [$plan->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                {{-- name --}}
                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">{{ trans('cruds.plan.fields.name') }}*</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($plan) ? $plan->name : '') }}" required>
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.name_helper') }}
                    </p>
                </div>
                {{-- code --}}
                <div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
                    <label for="code">{{ trans('cruds.plan.fields.code') }}*</label>
                    <input type="text" id="code" name="code" class="form-control" value="{{ old('code', isset($plan) ? $plan->code : '') }}" required>
                    @if($errors->has('code'))
                        <em class="invalid-feedback">
                            {{ $errors->first('code') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.code_helper') }}
                    </p>
                </div>

                {{-- modules --}}
                <div class="form-group {{ $errors->has('modules') ? 'has-error' : '' }}">
                    <label for="modules">
                        {{ trans('cruds.plan.fields.modules') }}*
                        <span class="btn btn-info btn-xs select-all">{{ trans('global.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all">{{ trans('global.deselect_all') }}</span>
                    </label>
                    <select name="modules[]" id="modules" class="form-control select2" multiple="multiple" required>
                        @foreach($modules as $id => $modules)
                            <option value="{{ $id }}" {{ (in_array($id, old('modules', [])) || isset($plan) && $plan->modules->pluck('name', 'id')->contains($modules)) ? 'selected' : '' }}>{{ $modules }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('modules'))
                        <em class="invalid-feedback">
                            {{ $errors->first('modules') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.modules_helper') }}
                    </p>
                </div>

                {{-- currency --}}
                <div class="form-group {{ $errors->has('currency') ? 'has-error' : '' }}">
                    <label for="currency">{{ trans('cruds.plan.fields.currency') }}*</label>
                    <input type="text" id="currency" name="currency" class="form-control" value="{{ old('currency', isset($plan) ? $plan->currency : '') }}" required>
                    @if($errors->has('currency'))
                        <em class="invalid-feedback">
                            {{ $errors->first('currency') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.currency_helper') }}
                    </p>
                </div>

                {{-- active user --}}
                <div class="form-group {{ $errors->has('active_users_limit') ? 'has-error' : '' }}">
                    <label for="active_users_limit">{{ trans('cruds.plan.fields.active_users_limit') }}</label>
                    <input type="text" id="active_users_limit" name="active_users_limit" class="form-control" value="{{ old('active_users_limit', isset($plan) ? $plan->active_users_limit : '') }}">
                    @if($errors->has('active_users_limit'))
                        <em class="invalid-feedback">
                            {{ $errors->first('active_users_limit') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.active_users_limit_helper') }}
                    </p>
                </div>

                 {{-- active company --}}
                <div class="form-group {{ $errors->has('active_company_limit') ? 'has-error' : '' }}">
                    <label for="active_company_limit">{{ trans('cruds.plan.fields.active_company_limit') }}</label>
                    <input type="text" id="active_company_limit" name="active_company_limit" class="form-control" value="{{ old('active_company_limit', isset($plan) ? $plan->active_company_limit : '') }}">
                    @if($errors->has('active_company_limit'))
                        <em class="invalid-feedback">
                            {{ $errors->first('active_company_limit') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.active_company_limit_helper') }}
                    </p>
                </div>



                   {{-- active branch --}}
                <div class="form-group {{ $errors->has('active_branch_limit') ? 'has-error' : '' }}">
                    <label for="active_users_limit">{{ trans('cruds.plan.fields.active_branch_limit') }}</label>
                    <input type="text" id="active_branch_limit" name="active_branch_limit" class="form-control" value="{{ old('active_branch_limit', isset($plan) ? $plan->active_branch_limit : '') }}">
                    @if($errors->has('active_branch_limit'))
                        <em class="invalid-feedback">
                            {{ $errors->first('active_branch_limit') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.active_branch_limit_helper') }}
                    </p>
                </div>

                {{-- price --}}
                <div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
                    <label for="price">{{ trans('cruds.plan.fields.price') }}*</label>
                    <input type="text" id="price" name="price" class="form-control" value="{{ old('price', isset($plan) ? $plan->price : number_format(0, 2)) }}" required>
                    @if($errors->has('price'))
                        <em class="invalid-feedback">
                            {{ $errors->first('price') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.price_helper') }}
                    </p>
                </div>

                {{-- signup fee --}}
                <div class="form-group {{ $errors->has('signup_fee') ? 'has-error' : '' }}">
                    <label for="signup_fee">{{ trans('cruds.plan.fields.signup_fee') }}</label>
                    <input type="text" id="signup_fee" name="signup_fee" class="form-control" value="{{ old('signup_fee', isset($plan) ? $plan->signup_fee : number_format(0, 2)) }}">
                    @if($errors->has('signup_fee'))
                        <em class="invalid-feedback">
                            {{ $errors->first('signup_fee') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.signup_fee_helper') }}
                    </p>
                </div>

                {{-- renewal_fee --}}
                <div class="form-group {{ $errors->has('renewal_fee') ? 'has-error' : '' }}">
                    <label for="renewal_fee">{{ trans('cruds.plan.fields.renewal_fee') }}</label>
                    <input type="text" id="renewal_fee" name="renewal_fee" class="form-control" value="{{ old('renewal_fee', isset($plan) ? $plan->renewal_fee : number_format(0, 2)) }}">
                    @if($errors->has('renewal_fee'))
                        <em class="invalid-feedback">
                            {{ $errors->first('renewal_fee') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.renewal_fee_helper') }}
                    </p>
                </div>
                {{-- trial_interval --}}
                <div class="form-group {{ $errors->has('trial_interval') ? 'has-error' : '' }}">
                    <label for="trial_interval">{{ trans('cruds.plan.fields.trial_interval') }}*</label>
                    <select name="trial_interval" id="trial_interval" class="form-control" required>
                        <option value="">--Select interval--</option>
                        @foreach($trialPlanTypes as $slug => $planType)
                            <option value="{{ $slug }}" {{ (old('trial_interval') == $slug || isset($plan) && $plan->trial_interval == $slug) ? 'selected' : '' }}>{{ $planType }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('trial_interval'))
                        <em class="invalid-feedback">
                            {{ $errors->first('trial_interval') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.trial_interval_helper') }}
                    </p>
                </div>
                {{-- trial_period --}}
                <div class="form-group {{ $errors->has('trial_period') ? 'has-error' : '' }}">
                    <label for="trial_period">{{ trans('cruds.plan.fields.trial_period') }}*</label>
                    <input type="text" id="trial_period" name="trial_period" class="form-control" value="{{ old('trial_period', isset($plan) ? $plan->trial_period : 14) }}" required>
                    @if($errors->has('trial_period'))
                        <em class="invalid-feedback">
                            {{ $errors->first('trial_period') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.trial_period_helper') }}
                    </p>
                </div>

                {{-- invoice_interval --}}
                <div class="form-group {{ $errors->has('invoice_interval') ? 'has-error' : '' }}">
                    <label for="invoice_interval">{{ trans('cruds.plan.fields.invoice_interval') }}*</label>
                    <select name="invoice_interval" id="invoice_interval" class="form-control" required>
                        <option value="">--Select interval--</option>
                        @foreach($invoicePlanTypes as $slug => $planType)
                            <option value="{{ $slug }}" {{ (old('invoice_interval') == $slug || isset($plan) && $plan->invoice_interval == $slug) ? 'selected' : '' }}>{{ $planType }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('invoice_interval'))
                        <em class="invalid-feedback">
                            {{ $errors->first('invoice_interval') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.invoice_interval_helper') }}
                    </p>
                </div>
                {{-- invoice_period --}}
                <div class="form-group {{ $errors->has('invoice_period') ? 'has-error' : '' }}">
                    <label for="invoice_period">{{ trans('cruds.plan.fields.invoice_period') }}*</label>
                    <input type="text" id="invoice_period" name="invoice_period" class="form-control" value="{{ old('invoice_period', isset($plan) ? $plan->invoice_period : 1) }}" required>
                    @if($errors->has('invoice_period'))
                        <em class="invalid-feedback">
                            {{ $errors->first('invoice_period') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.invoice_period_helper') }}
                    </p>
                </div>
                
                {{-- sort_order --}}
                <div class="form-group {{ $errors->has('sort_order') ? 'has-error' : '' }}">
                    <label for="sort_order">{{ trans('cruds.plan.fields.sort_order') }}</label>
                    <input type="text" id="sort_order" name="sort_order" class="form-control" value="{{ old('sort_order', isset($plan) ? $plan->sort_order : 0) }}">
                    @if($errors->has('sort_order'))
                        <em class="invalid-feedback">
                            {{ $errors->first('sort_order') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.sort_order_helper') }}
                    </p>
                </div>

                {{-- features --}}
                <div class="form-group  {{ $errors->has('features') ? 'has-error' : '' }}">
                    {{-- label --}}
                    <label for="features">{{ trans('cruds.plan.fields.features') }}</label>
                    {{-- wrapper for feature input --}}
                    <div class="field_wrapper">
                        {{-- current features --}}
                        @foreach ($plan->features as $key => $features)
                            <div class="row pb-2">
                                {{-- input field --}}
                                <div class="col-sm-11">
                                    <input type="text" name="features[]" class="form-control" value="{{ isset($features) ? $features : '' }}">
                                </div>
                                {{-- remove button for delete feature --}}
                                <div class="col-sm-1 text-right">
                                    <a href="javascript:void(0);" class="remove_button btn btn-danger">
                                        <i class="fas fa-minus"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach



                    </div>
                    {{-- new row --}}
                    <div class="row">
                        {{-- input field --}}
                        {{-- <div class="col-sm-11">
                            <input type="text" id="features" name="features[]" class="form-control" value="">
                        </div> --}}
                        {{-- add button for more feature --}}
                        <div class="col-sm-1">
                            <a href="javascript:void(0);" class="add_button btn btn-success">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>

                    @if($errors->has('features'))
                        <em class="invalid-feedback">
                            {{ $errors->first('features') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.features_helper') }}
                    </p>
                </div>

                {{-- description --}}
                <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                    <label for="description">{{ trans('cruds.plan.fields.description') }}</label>
                    <input type="text" id="description" name="description" class="form-control" value="{{ old('description', isset($plan) ? $plan->description : '') }}">
                    @if($errors->has('description'))
                        <em class="invalid-feedback">
                            {{ $errors->first('description') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.description_helper') }}
                    </p>
                </div>

                {{-- plan image --}}
                <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                    <label for="image">{{ trans('cruds.plan.fields.image') }}</label>
                    @if ($plan->image)
                        <div class="pb-2">
                            <img src="{{ asset('images/plan/'. $plan->image) }}" width="100">
                        </div>

                    @else
                        <p>No image found</p>
                    @endif
                    <input type="file" id="image" name="image" class="form-control">
                    @if($errors->has('image'))
                        <em class="invalid-feedback">
                            {{ $errors->first('image') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.plan.fields.image_helper') }}
                    </p>
                </div>

                <div>
                    <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>


        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
    $(document).ready(function(){
        var maxField = 10; //Input fields increment limitation
        var addButton = $('.add_button'); //Add button selector
        var wrapper = $('.field_wrapper'); //Input field wrapper
        var fieldHTML = '<div class="row pb-2"><div class="col-sm-11"><input type="text" name="features[]" value="" class="form-control"/></div><div class="col-sm-1 text-right"><a href="javascript:void(0);" class="remove_button btn btn-danger"><i class="fas fa-minus"></i></a></div></div>'; //New input field row
        var x = 1; //Initial field counter is 1

        //Once add button is clicked
        $(addButton).click(function(){
            //Check maximum number of input fields
            if(x < maxField){
                x++; //Increment field counter
                $(wrapper).append(fieldHTML); //Add field html
            }
        });

        //Once remove button is clicked
        $(wrapper).on('click', '.remove_button', function(e){
            e.preventDefault();
            $(this).parent('div').parent('div').remove(); //Remove field html
            x--; //Decrement field counter
        });
    });
</script>
@endsection
