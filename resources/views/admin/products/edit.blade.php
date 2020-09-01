@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.product.title_singular') }}
        </div>

        <div class="card-body">
            <form action="{{ route("admin.products.update", [$product->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                {{-- name --}}
                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">{{ trans('cruds.product.fields.name') }}*</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($product) ? $product->name : '') }}" required>
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.product.fields.name_helper') }}
                    </p>
                </div>
                {{-- code --}}
                <div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
                    <label for="code">{{ trans('cruds.product.fields.code') }}*</label>
                    <input type="text" id="code" name="code" class="form-control" value="{{ old('code', isset($product) ? $product->code : '') }}" required>
                    @if($errors->has('code'))
                        <em class="invalid-feedback">
                            {{ $errors->first('code') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.product.fields.code_helper') }}
                    </p>
                </div>

                {{-- price --}}
                <div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
                    <label for="price">{{ trans('cruds.product.fields.price') }}*</label>
                    <input type="text" id="price" name="price" class="form-control" value="{{ old('price', isset($product) ? $product->price : '') }}" required>
                    @if($errors->has('price'))
                        <em class="invalid-feedback">
                            {{ $errors->first('price') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.product.fields.price_helper') }}
                    </p>
                </div>

                {{-- modules --}}
                <div class="form-group {{ $errors->has('modules') ? 'has-error' : '' }}">
                    <label for="modules">
                        {{ trans('cruds.product.fields.modules') }}*
                        <span class="btn btn-info btn-xs select-all">{{ trans('global.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all">{{ trans('global.deselect_all') }}</span>
                    </label>
                    <select name="modules[]" id="modules" class="form-control select2" multiple="multiple" required>
                        @foreach($modules as $id => $modules)
                            <option value="{{ $id }}" {{ (in_array($id, old('modules', [])) || isset($product) && $product->modules->pluck('name', 'id')->contains($modules)) ? 'selected' : '' }}>{{ $modules }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('modules'))
                        <em class="invalid-feedback">
                            {{ $errors->first('modules') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.product.fields.modules_helper') }}
                    </p>
                </div>

                {{-- plans --}}
                <div class="form-group {{ $errors->has('planId') ? 'has-error' : '' }}">
                    <label for="planId">{{ trans('cruds.product.fields.planId') }}*</label>

                    <select name="planId" id="planId" class="form-control" required>
                        @foreach($plans as $id => $plans)
                            <option value="{{ $id }}" {{ isset($product) && $product->plans->id == $id ? 'selected' : '' }}>{{ $plans }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('planId'))
                        <em class="invalid-feedback">
                            {{ $errors->first('planId') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.product.fields.planId_helper') }}
                    </p>
                </div>

                {{-- number of users --}}
                <div class="form-group {{ $errors->has('numberOfUser') ? 'has-error' : '' }}">
                    <label for="numberOfUser">{{ trans('cruds.product.fields.numberOfUser') }}*</label>
                    <input type="text" id="numberOfUser" name="numberOfUser" class="form-control" value="{{ old('price', isset($product) ? $product->numberOfUser : '') }}" required>
                    @if($errors->has('numberOfUser'))
                        <em class="invalid-feedback">
                            {{ $errors->first('numberOfUser') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.product.fields.numberOfUser_helper') }}
                    </p>
                </div>

                {{-- renewal charge type --}}
                <div class="form-group {{ $errors->has('renewal_charge_type') ? 'has-error' : '' }}">
                    <label for="renewal_charge_type">{{ trans('cruds.product.fields.renewal_charge_type') }}*</label>
                    <select name="renewal_charge_type" id="renewal_charge_type" class="form-control" required>
                        <option value="">--Select a Type--</option>

                        <option value="percentage" {{ $product->renewal_charge_type === 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="amount" {{ $product->renewal_charge_type === 'amount' ? 'selected' : '' }}>Amount</option>

                    </select>
                    @if($errors->has('renewal_charge_type'))
                        <em class="invalid-feedback">
                            {{ $errors->first('renewal_charge_type') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.product.fields.renewal_charge_type_helper') }}
                    </p>
                </div>

                {{-- renewal_charge --}}
                <div class="form-group {{ $errors->has('renewal_charge') ? 'has-error' : '' }}">
                    <label for="renewal_charge">{{ trans('cruds.product.fields.renewal_charge') }}*</label>
                    <input type="text" id="renewal_charge" name="renewal_charge" class="form-control" value="{{ old('renewal_charge', isset($product) ? $product->renewal_charge : '') }}" required>
                    @if($errors->has('renewal_charge'))
                        <em class="invalid-feedback">
                            {{ $errors->first('renewal_charge') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.product.fields.renewal_charge_helper') }}
                    </p>
                </div>

                {{-- features --}}
                <div class="form-group  {{ $errors->has('features') ? 'has-error' : '' }}">
                    {{-- label --}}
                    <label for="features">{{ trans('cruds.product.fields.features') }}</label>
                    {{-- wrapper for feature input --}}
                    <div class="field_wrapper">
                        {{-- current features --}}
                        @foreach ($features as $key => $features)
                            <div class="row pb-2">
                                {{-- input field --}}
                                <div class="col-sm-11">
                                    <input type="text" name="features[]" class="form-control" value="{{ old('features', isset($features) ? $features : '') }}">
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
                        {{ trans('cruds.product.fields.features_helper') }}
                    </p>
                </div>

                {{-- description --}}
                <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                    <label for="description">{{ trans('cruds.product.fields.desc') }}*</label>
                    <input type="text" id="description" name="description" class="form-control" value="{{ old('description', isset($product) ? $product->description : '') }}">
                    @if($errors->has('description'))
                        <em class="invalid-feedback">
                            {{ $errors->first('description') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.product.fields.desc_helper') }}
                    </p>
                </div>

                {{-- product image --}}
                <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                    <label for="image">{{ trans('cruds.product.fields.image') }}</label>
                    @if ($product->image)
                        <div class="pb-2">
                            <img src="{{ asset('images/product/'. $product->image) }}" width="100">
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
                        {{ trans('cruds.product.fields.image_helper') }}
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
