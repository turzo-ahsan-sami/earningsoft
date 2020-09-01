@extends('layouts/gnr_layout')
@section('title', '| Bank Branch')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-1"></div>
                <div class="col-md-10 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('gnrViewBankBranch/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Bank/Donor Branch List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Bank/Donor Branch</div>
                                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                
                            
                            {{-- <div class="form-horizontal form-groups"> --}}

                            {!! Form::open(['url'=>'','class'=>'form-horizontal form-groups']) !!}

                                
                            <div class="col-md-6">
                                <div class="form-group">
                                        {!! Form::label('bank', 'Bank/Donor Name:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">

                                        @php
                                            $banks = array(''=>'Select Bank/Donor') + DB::table('gnr_bank')->pluck('name','id')->toArray();
                                        @endphp
                                                
                                            {!! Form::select('bank', $banks,null, array('class'=>'form-control', 'id' => 'bank')) !!}
                                            <p id='banke' class="error" style="color:red;"></p>
                                        </div>
                                </div>
                                <div class="form-group">
                                        {!! Form::label('branchName', 'Branch Name:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                
                                            {!! Form::text('branchName', null, array('class'=>'form-control', 'id' => 'branchName')) !!}
                                            <p id='branchNamee' class="error" style="color:red;"></p>
                                        </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('telephoneNumber', 'Tel. Number:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">                                                
                                        {!! Form::text('telephoneNumber', null, array('class'=>'form-control', 'id' => 'telephoneNumber')) !!}
                                        <p id='telephoneNumbere' class="error" style="color:red;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('branchEmail', 'E-mail:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                
                                            {!! Form::text('branchEmail', null, array('class'=>'form-control', 'id' => 'branchEmail')) !!}
                                            <p id='branchEmaile' class="error" style="color:red;"></p>
                                        </div>
                                </div> 

                                 <div class="form-group">
                                        {!! Form::label('address', 'Address:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                
                                            {!! Form::textArea('address', null, array('class'=>'form-control', 'id' => 'address','rows'=>'1')) !!}
                                            <p id='addresse' class="error" style="color:red;"></p>
                                        </div>
                                </div>  

                               {{--  <div class="form-group">
                                        {!! Form::label('division', 'Division:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        @php
                                            $divisions = array(''=>'Select Division') + DB::table('division')->pluck('division_name','id')->toArray();
                                        @endphp                                                
                                            {!! Form::select('division', $divisions,null, array('class'=>'form-control', 'id' => 'division')) !!}
                                            <p id='divisione' class="error" style="color:red;"></p>
                                        </div>
                                </div> --}}

                                {{-- <div class="form-group">
                                        {!! Form::label('district', 'District:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        @php
                                            $districts = array(''=>'Select District') + DB::table('district')->pluck('district_name','id')->toArray();
                                        @endphp                                                
                                            {!! Form::select('district', $districts,null, array('class'=>'form-control', 'id' => 'district')) !!}
                                            <p id='districte' class="error" style="color:red;"></p>
                                        </div>
                                </div> --}}

                               {{--  <div class="form-group">
                                        {!! Form::label('upazilla', 'Upazilla:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        @php
                                            $upazillas = array(''=>'Select Upazilla') + DB::table('upzilla')->pluck('upzilla_name','id')->toArray();
                                        @endphp                                                
                                            {!! Form::select('upazilla', $upazillas,null, array('class'=>'form-control', 'id' => 'upazilla')) !!}
                                            <p id='upazillae' class="error" style="color:red;"></p>
                                        </div>
                                </div> --}}

                                

                                 
                            </div>

                            <div class="col-md-6">

                            <div class="form-group">
                                        {!! Form::label('contactPerson', 'Contact Person:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                
                                            {!! Form::text('contactPerson', null, array('class'=>'form-control', 'id' => 'contactPerson')) !!}
                                            <p id='contactPersone' class="error" style="color:red;"></p>
                                        </div>
                                </div>


                                <div class="form-group">
                                        {!! Form::label('designation', 'Designation:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                
                                            {!! Form::text('designation', null, array('class'=>'form-control', 'id' => 'designation')) !!}
                                            <p id='designatione' class="error" style="color:red;"></p>
                                        </div>
                                </div>

                            <div class="form-group">
                                    {!! Form::label('contactPersonTelephoneNumber', 'Tel. Number:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">                                                
                                        {!! Form::text('contactPersonTelephoneNumber', null, array('class'=>'form-control', 'id' => 'contactPersonTelephoneNumber')) !!}
                                        <p id='contactPersonTelephoneNumbere' class="error" style="color:red;"></p>
                                    </div>
                            </div>

                            <div class="form-group">
                                    {!! Form::label('contactPersonMobileNumber', 'Mobile Number:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">                                                
                                        {!! Form::text('contactPersonMobileNumber', null, array('class'=>'form-control', 'id' => 'contactPersonMobileNumber')) !!}
                                        <p id='contactPersonMobileNumbere' class="error" style="color:red;"></p>
                                    </div>
                            </div>

                                 <div class="form-group">
                                        {!! Form::label('contactPersonEmail', 'E-mail:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                
                                            {!! Form::text('contactPersonEmail', null, array('class'=>'form-control', 'id' => 'contactPersonEmail')) !!}
                                            <p id='contactPersonEmaile' class="error" style="color:red;"></p>
                                        </div>
                                </div> 

                                                   
                                  
                               

                            </div>


                            <div class="form-group">
                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-12 text-right" style="padding-right: 30px;">
                                        {!! Form::button('Submit', ['id' => 'submitButton', 'class' => 'btn btn-info']) !!}
                                        <a href="{{url('gnrViewBankBranch/')}}" class="btn btn-danger closeBtn">Close</a>
                                        <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                    </div>
                                </div>

                            {!! Form::close() !!}
                            {{-- </div> --}}

                             
                           
                        </div>
                    </div>
                </div>
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
            <div class="col-md-1"></div>
    </div>
</div>



<script type="text/javascript">
    $(document).ready(function() {

        /*Submit Data*/
        $("#submitButton").on('click',function(event) {

            $.ajax({
                url: './gnrStoreBankBranch',
                type: 'POST',
                dataType: 'json',
                data: $('form').serialize(),
            })
            .done(function(data) {
                

                if (data.errors) {
                    if (data.errors['bank']) {
                        $("#banke").empty();
                        $("#banke").append('* '+data.errors['bank']);
                    }
                    if (data.errors['branchName']) {
                        $("#branchNamee").empty();
                        $("#branchNamee").append('* '+data.errors['branchName']);
                    }
                    if (data.errors['telePhoneNumber']) {
                        $("#telePhoneNumbere").empty();
                        $("#telePhoneNumbere").append('* '+data.errors['telePhoneNumber']);
                    }
                   
                    if (data.errors['branchEmail']) {
                        $("#branchEmaile").empty();
                        $("#branchEmaile").append('* '+data.errors['branchEmail']);
                    }
                    if (data.errors['contactPerson']) {
                        $("#contactPersone").empty();
                        $("#contactPersone").append('* '+data.errors['contactPerson']);
                    }
                    if (data.errors['designation']) {
                        $("#designatione").empty();
                        $("#designatione").append('* '+data.errors['designation']);
                    }
                    if (data.errors['contactPersonTelephoneNumber']) {
                        $("#contactPersonTelephoneNumbere").empty();
                        $("#contactPersonTelephoneNumbere").append('* '+data.errors['contactPersonTelephoneNumber']);
                    }
                    if (data.errors['contactPersonMobileNumber']) {
                        $("#contactPersonMobileNumbere").empty();
                        $("#contactPersonMobileNumbere").append('* '+data.errors['contactPersonMobileNumber']);
                    }
                    if (data.errors['contactPersonEmail']) {
                        $("#contactPersonEmaile").empty();
                        $("#contactPersonEmaile").append('* '+data.errors['contactPersonEmail']);
                    }
                    
                }
                else{
                    location.href = "gnrViewBankBranch";
                }
                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
            
        });
        /*End Sbbmit Data*/


        /*Filter District And Upazilla*/
        $("#division").on('change', function() {
            
            var divisionId = $("#division option:selected").val();
            
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './gnrGetfilteredDistrictNUpazilla',
                type: 'POST',
                dataType: 'json',
                data: {divisionId: divisionId,_token: csrf},
            })
            .done(function(data) {

                $("#district").empty();
                $("#district").append("<option value=''>Select District</option>");

                $("#upazilla").empty();
                $("#upazilla").append("<option value=''>Select Upazilla</option>");

                $.each(data['district'], function(index, obj) {
                     $("#district").append("<option value='"+obj.id+"'>"+obj.district_name  +"</option>");
                });

                $.each(data['upazilla'], function(index, obj) {
                     $("#upazilla").append("<option value='"+obj.id+"'>"+obj.upzilla_name  +"</option>");
                });
                
                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
        });

        $("#district").on('change', function() {
            
            var divisionId = $("#division option:selected").val();
            var districtId = $("#district option:selected").val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './gnrGetfilteredDistrictNUpazilla',
                type: 'POST',
                dataType: 'json',
                data: {divisionId: divisionId, districtId: districtId, _token: csrf},
            })
            .done(function(data) {

                $("#upazilla").empty();
                $("#upazilla").append("<option value=''>Select Upazilla</option>");
                

                $.each(data['upazilla'], function(index, obj) {
                     $("#upazilla").append("<option value='"+obj.id+"'>"+obj.upzilla_name  +"</option>");
                });
                
                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
        });
        /*End Filter District And Upazilla*/


        /*Empty Error Message when filed*/
        $(document).on('input', 'input', function() {
            $(this).closest('div').find('.error').empty();
        });
        $(document).on('change', 'select', function() {
            $(this).closest('div').find('.error').empty();
        });
        /*End Empty Error Message when filed*/

        $("#telephoneNumber,#contactPersonMobileNumber,#contactPersonTelephoneNumber").on('input',function() {            
            this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
        });



    });/*Ready*/
</script>






@endsection

