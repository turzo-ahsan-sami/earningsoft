@extends('layouts.frontend')

@section('content')
    <div class="container">
        <div class="col-md-12">
            <div class="form-area-trial">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="70"
                                aria-valuemin="0" aria-valuemax="100" style="width:100%;background-color:#2CA01C">
                                <span>100% Complete</span>
                            </div>
                        </div>
                    </div>
                </div>
                <form role="form" action="{{ url('customer/all-set') }}" method="post"><br style="clear:both">
                    @csrf
                    <h1 style="margin-bottom: 10px; text-align: left;">Tell us about your business.</h1>
                    <p class="freeTrailP">
                        Everyone needs something a little different from EarningSoft. Let’s get to know what you need so we can tailor things to fit you. You can change your info anytime in Settings.
                    </p>
                    <div class="form-group">
                        <label for="business_name">What is the full, legal name of your business?</label>
                        <input type="text" class="form-control materialFormInput" id="business_name" name="business_name" placeholder="Business name/ Company name" required>
                    </div>
                    <div class="form-group">
                        <label for="name">What is your name/ the name of business holder?</label>
                        <input type="text" class="form-control materialFormInput" id="name" name="business_holder_name" placeholder="Business holder's name" required>
                    </div>
                    <div class="form-group">
                        <label for="company_address">What is the address of your company?</label>
                        <input type="text" class="form-control materialFormInput" id="name" name="business_address" placeholder="Location" required>
                    </div>
                    <div class="form-group">
                        <label for="business">How would you describe what your business does?</label>
                        <select class="form-control" name="business_type" required>
                            <option value="">Select business type</option>
                            @foreach($businessTypes as $record)
                                <option value="{{ $record->slug }}">{{ $record->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="business">How would you calculate your financial year?</label>
                        <select class="form-control" name="fy_type" required>
                            <option value="">Select financial year type</option>
                            @foreach (config('constants.fy_type') as $slug => $name)
                                <option value="{{ $slug }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stock">Would you like to calculate stock in your billing system?</label>
                        <select class="form-control" id="stock" name="stock_type" required>
                            <option value="">Select from option</option>
                            <option value="1">"Yes"</option>
                            <option value="0">"No"</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ca_level">What is the maximum chart of account level of your company?</label>
                        <input type="text" class="form-control materialFormInput" id="ca_level" name="ca_level" value="5" required>
                    </div>

                    <div>
                        <input class="btn btn-success" type="submit" value="All Set">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent

@endsection
