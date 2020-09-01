@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.discount.title_singular') }}
        </div>

        <div class="card-body">
            <form action="{{ route("admin.discounts.update", [$discount->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                {{-- title --}}
                <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                    <label for="title">{{ trans('cruds.discount.fields.title') }}*</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($discount) ? $discount->title : '') }}" required>
                    @if($errors->has('title'))
                        <em class="invalid-feedback">
                            {{ $errors->first('title') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.discount.fields.title_helper') }}
                    </p>
                </div>

                {{-- products --}}
                <div class="form-group {{ $errors->has('planId') ? 'has-error' : '' }}">
                    <label for="planId">{{ trans('cruds.discount.fields.planId') }}*</label>

                    <select name="planId" id="planId" class="form-control" required>
                        @foreach($plans as $id => $plans)
                            <option value="{{ $id }}" {{ isset($discount) && $discount->planId == $id ? 'selected' : '' }}>{{ $plans }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('planId'))
                        <em class="invalid-feedback">
                            {{ $errors->first('planId') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.discount.fields.planId_helper') }}
                    </p>
                </div>
                {{-- discount type --}}
                <div class="form-group {{ $errors->has('discount_type') ? 'has-error' : '' }}">
                    <label for="discount_type">{{ trans('cruds.discount.fields.discount_type') }}*</label>



                    <select name="discount_type" id="discount_type" class="form-control" required>
                        <option value="percentage" <?= $discount->discount_type === 'percentage' ? 'selected' : '' ?>>Percentage</option>
                        <option value="amount" <?= $discount->discount_type === 'amount' ? 'selected' : '' ?>>Amount</option>

                    </select>
                    @if($errors->has('discount_type'))
                        <em class="invalid-feedback">
                            {{ $errors->first('discount_type') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.discount.fields.discount_type_helper') }}
                    </p>
                </div>
                {{-- value --}}
                <div class="form-group {{ $errors->has('value') ? 'has-error' : '' }}">
                    <label for="value">{{ trans('cruds.discount.fields.value') }}*</label>
                    <input type="text" id="value" name="value" class="form-control" value="{{ old('value', isset($discount) ? $discount->value : '') }}" required>
                    @if($errors->has('value'))
                        <em class="invalid-feedback">
                            {{ $errors->first('value') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.discount.fields.value_helper') }}
                    </p>
                </div>

                {{-- Effective Date --}}
                <div class="form-group {{ $errors->has('effective_date') ? 'has-error' : '' }}">
                    <label for="effective_date">{{ trans('cruds.discount.fields.effective_date') }}*</label>
                    <input type="text" id="effective_date" name="effective_date" class="form-control input-append date" value="{{ old('effective_date', isset($discount) ? $discount->effective_date : '') }}">
                    @if($errors->has('effective_date'))
                        <em class="invalid-feedback">
                            {{ $errors->first('effective_date') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.discount.fields.effective_date_helper') }}
                    </p>
                </div>


                {{-- End Date --}}
                <div class="form-group {{ $errors->has('end_date') ? 'has-error' : '' }}">
                    <label for="effective_date">{{ trans('cruds.discount.fields.end_date') }}*</label>
                    <input type="text" id="end_date" name="end_date" class="form-control input-append date" value="{{ old('end_date', isset($discount) ? $discount->end_date : '') }}">
                    @if($errors->has('end_date'))
                        <em class="invalid-feedback">
                            {{ $errors->first('end_date') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.discount.fields.end_date_helper') }}
                    </p>
                </div>


                {{-- description --}}
                <div class="form-group {{ $errors->has('desc') ? 'has-error' : '' }}">
                    <label for="desc">{{ trans('cruds.discount.fields.desc') }}</label>
                    <input type="text" id="desc" name="desc" class="form-control" value="{{ old('desc', isset($discount) ? $discount->desc : '') }}">
                    @if($errors->has('desc'))
                        <em class="invalid-feedback">
                            {{ $errors->first('desc') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.discount.fields.desc_helper') }}
                    </p>
                </div>

                <div>
                    <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>


        </div>
    </div>

    <script type="text/javascript">
    $(function(){
        $( "#effective_date" ).datetimepicker({
            changeMonth: true,
            changeYear: true,

            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $("#effective_date").datetimepicker("option", "minDate", $(this).datetimepicker('getDate'));
            }
        });

        $( "#end_date" ).datetimepicker({
            changeMonth: true,
            changeYear: true,

            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $("#end_date").datetimepicker("option", "minDate", $(this).datetimepicker('getDate'));
            }
        });
    }
    </script>
    @endsection
