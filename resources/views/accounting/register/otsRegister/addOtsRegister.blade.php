@extends('layouts/acc_layout')
@section('title', '| OTS Register')
@section('content')

<div class="row add-data-form">
<div class="col-md-1"></div>
<div class="col-md-10 fullbody">
<div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="{{url('otsRegisterList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>OTS Register List</a>
</div>

<div class="panel panel-default panel-border">
	<div class="panel-heading">
	    <div class="panel-title">OTS Register</div>
	</div>


<div class="panel-body">
            <div class="row">                
                <div class="col-md-12">
                {!! Form::open(array('url' => 'storeOts','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                    <div class="col-md-6">
                
                    <div class="form-group">
                            {!! Form::label('memberName', 'Member Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                                    
                                {!! Form::text('memberName', null, ['class'=>'form-control', 'id' => 'memberName']) !!}
                                <p id='memberNamee' style="max-height:3px;"></p>
                            </div>
                        </div>
                    <div class="form-group">
                            {!! Form::label('spouseOrFatherName', 'Spouse/Father:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                
                           {!! Form::text('spouseOrFatherName', null, ['class'=>'form-control', 'id' => 'spouseOrFatherName']) !!}
                                <p id='spouseOrFatherNamee' style="max-height:3px;"></p>
                            </div>
                    </div>
                     <div class="form-group">
                            {!! Form::label('nidNo', 'NID No:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                 
                            {!! Form::text('nidNo', null, ['class'=>'form-control', 'id' => 'nidNo']) !!}
                                <p id='nidNoe' style="max-height:3px;"></p>
                            </div>
                    </div>
                    <div class="form-group">
                            {!! Form::label('mobileNo', 'Mobile Number:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                 
                            {!! Form::text('mobileNo', null, ['class'=>'form-control', 'id' => 'mobileNo']) !!}
                                <p id='mobileNoe' style="max-height:3px;"></p>
                            </div>
                    </div>
                    <div class="form-group">
                            {!! Form::label('branchLocation', 'Branch:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            @php
                                $branches = DB::table('gnr_branch')->select('id','name','branchCode')->get();
                            @endphp
                            <select id="branchLocation" name="branchLocation" class="form-control">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                <option value="{{$branch->id}}">{{str_pad($branch->branchCode,3,'0',STR_PAD_LEFT).'-'.$branch->name}}</option>
                                @endforeach
                            </select>                              
                            
                                <p id='branchLocatione' style="max-height:3px;"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('employeeReference', 'Employee:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">

                            @php
                                $employees = DB::table('hr_emp_general_info')->select('id','emp_id','emp_name_english')->get();
                            @endphp
                            <select id="employeeReference" name="employeeReference" class="form-control">
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                <option value="{{$employee->id}}">{{$employee->emp_id.'-'.$employee->emp_name_english}}</option>
                                @endforeach
                            </select>                              
                            
                                <p id='employeeReferencee' style="max-height:3px;"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('certificateNo', 'Certificate No:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                                    
                                {!! Form::text('certificateNo', null, ['class'=>'form-control', 'id' => 'certificateNo']) !!}
                                <p id='certificateNoe' style="max-height:3px;"></p>
                            </div>
                        </div>
                    
                    {{-- <div class="form-group" sty>
                        {!! Form::label('address', 'Address:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                            {!! Form::textArea('address', $value = null, ['class' => 'form-control', 'id' => 'address', 'type' => 'text', 'placeholder' => 'Enter Adddress','rows'=>2]) !!}
                            <p id='namee' style="max-height:3px;"></p>
                        </div>
                    </div>   --}}                      
                    
                
                </div>



                <div class="col-md-6">
                
                    <div class="form-group">
                            {!! Form::label('accNo', 'Account No:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                                    
                                {!! Form::text('accNo', null, ['class'=>'form-control', 'id' => 'accNo']) !!}
                                <p id='accNoe' style="max-height:3px;"></p>
                            </div>
                        </div>
                        
                    <div class="form-group">
                            {!! Form::label('period', 'Period:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            @php
                                $periods = DB::table('acc_ots_period')->select('name','id','interestRate','months')->get();
                            @endphp
                            <select id="period" name="period" class="form-control">
                                <option value="" interestRate="0" months="0">Select Period</option>
                                @foreach($periods as $period)
                                <option value="{{$period->id}}" interestRate="{{number_format($period->interestRate,2)}}" months="{{$period->months}}">{{$period->name}}</option>
                                @endforeach
                            </select>                            
                                <p id='periode' style="max-height:3px;"></p>
                            </div>
                    </div>
                    <div class="form-group">
                            {!! Form::label('interestRate', 'Interest Rate:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            {!! Form::text('interestRate' ,null, ['class'=>'form-control', 'id' => 'interestRate','readonly']) !!}
                                <p id='interestRatee' style="max-height:3px;"></p>
                            </div>
                    </div>
                    <div class="form-group">
                            {!! Form::label('amount', 'Amount (TK):', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                                    
                                {!! Form::text('amount', null, ['class'=>'form-control', 'id' => 'amount']) !!}
                                <p id='amounte' style="max-height:3px;"></p>
                            </div>
                    </div>
                    <div class="form-group">
                            {!! Form::label('openingBalance', 'O/B-Interest:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                                    
                                {!! Form::text('openingBalance', null, ['class'=>'form-control', 'id' => 'openingBalance']) !!}
                                <p id='openingBalancee' style="max-height:3px;"></p>
                            </div>
                    </div>
                    <div class="form-group">
                            {!! Form::label('dateFrom', 'Date:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                                    
                                {!! Form::text('dateFrom', null, ['class'=>'form-control', 'id' => 'dateFrom','readonly','style'=>'cursor:pointer']) !!}
                                <p id='dateFrome' style="max-height:3px;"></p>
                            </div>
                    </div>
                    <div class="form-group">
                            {!! Form::label('matureDate', 'Mature Date:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                                    
                                {!! Form::text('matureDate', null, ['class'=>'form-control', 'id' => 'matureDate','readonly']) !!}
                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('effectiveDate', 'Effective Date:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                                    
                                {!! Form::text('effectiveDate', null, ['class'=>'form-control', 'id' => 'effectiveDate','readonly','style'=>'cursor:pointer']) !!}
                                
                            </div>
                    </div>
                    
                
                </div>
                 <div class="form-group">
                        <div class="col-sm-12 text-right">
                            {!! Form::submit('Submit', ['id' => 'save', 'class' => 'btn btn-info','type'=>'button']) !!}
                            <a href="{{url('otsRegisterList/')}}" class="btn btn-danger closeBtn">Close</a>
                        </div>
                </div>

            {!! Form::close() !!}
                
            </div>

        </div>

    </div>

</div>
<div class="footerTitle" style="border-top:1px solid white"></div>
</div>
<div class="col-md-1"></div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        $("#period").change(function() {
            $("#interestRate").val($("#period option:selected").attr('interestrate'));
        });

        /*Submit the form*/
        $("form").submit(function(event) {

            event.preventDefault();
            
            $.ajax({
                 type: 'post',
                 url: './storeOts',
                 data: $('form').serialize(),
                 dataType: 'json',
                success: function( _response ){

                 if (_response.errors) {
                    if (_response.errors['memberName']) {
                        $('#memberNamee').empty();
                        $('#memberNamee').show();
                        $('#memberNamee').append('<span class="errormsg" style="color:red;">'+_response.errors.memberName+'</span>');
                        //return false;
                    }
                    if (_response.errors['spouseOrFatherName']) {
                        $('#spouseOrFatherNamee').empty();
                        $('#spouseOrFatherNamee').show();
                        $('#spouseOrFatherNamee').append('<span class="errormsg" style="color:red;">'+_response.errors.spouseOrFatherName+'</span>');
                        //return false;
                    }
                    if (_response.errors['nidNo']) {
                        $('#nidNoe').empty();
                        $('#nidNoe').show();
                        $('#nidNoe').append('<span class="errormsg" style="color:red;">'+_response.errors.nidNo+'</span>');
                        //return false;
                    }
                    if (_response.errors['mobileNo']) {
                        $('#mobileNoe').empty();
                        $('#mobileNoe').show();
                        $('#mobileNoe').append('<span class="errormsg" style="color:red;">'+_response.errors.mobileNo+'</span>');
                        //return false;
                    }
                    if (_response.errors['branchLocation']) {
                        $('#branchLocatione').empty();
                        $('#branchLocatione').show();
                        $('#branchLocatione').append('<span class="errormsg" style="color:red;">'+_response.errors.branchLocation+'</span>');
                        //return false;
                    }
                    if (_response.errors['employeeReference']) {
                        $('#employeeReferencee').empty();
                        $('#employeeReferencee').show();
                        $('#employeeReferencee').append('<span class="errormsg" style="color:red;">'+_response.errors.employeeReference+'</span>');
                        //return false;
                    }
                    if (_response.errors['accNo']) {
                        $('#accNoe').empty();
                        $('#accNoe').show();
                        $('#accNoe').append('<span class="errormsg" style="color:red;">'+_response.errors.accNo+'</span>');
                        //return false;
                    }
                    if (_response.errors['certificateNo']) {
                        $('#certificateNoe').empty();
                        $('#certificateNoe').show();
                        $('#certificateNoe').append('<span class="errormsg" style="color:red;">'+_response.errors.certificateNo+'</span>');
                        //return false;
                    }
                    if (_response.errors['period']) {
                        $('#periode').empty();
                        $('#periode').show();
                        $('#periode').append('<span class="errormsg" style="color:red;">'+_response.errors.period+'</span>');
                        //return false;
                    }
                    if (_response.errors['amount']) {
                        $('#amounte').empty();
                        $('#amounte').show();
                        $('#amounte').append('<span class="errormsg" style="color:red;">'+_response.errors.amount+'</span>');
                        //return false;
                    }
                    if (_response.errors['dateFrom']) {
                        $('#dateFrome').empty();
                        $('#dateFrome').show();
                        $('#dateFrome').append('<span class="errormsg" style="color:red;">'+_response.errors.dateFrom+'</span>');
                        //return false;
                    }
                    
            } else {
                   
                    window.location.href = '{{url('otsRegisterList/')}}';
                    }
                },
                error: function( data ){
                    // Handle error
                    //alert(_response.errors);
                    alert('error');
                    
                }
            });
        });
        /*End Submit the form*/




        /*Hide the error if filed is filed*/
        $("input").keyup(function(){
        var memberName = $("#memberName").val();
        if(memberName){$('#memberNamee').hide();}else{$('#memberNamee').show();}

        var spouseOrFatherName = $("#spouseOrFatherName").val();
        if(spouseOrFatherName){$('#spouseOrFatherNamee').hide();}else{$('#spouseOrFatherNamee').show();}

        var nidNo = $("#nidNo").val();
        if(nidNo){$('#nidNoe').hide();}else{$('#nidNoe').show();}

        var mobileNo = $("#mobileNo").val();
        if(mobileNo){$('#mobileNoe').hide();}else{$('#mobileNoe').show();}

        var accNo = $("#accNo").val();
        if(accNo){$('#accNoe').hide();}else{$('#accNoe').show();}

        var certificateNo = $("#certificateNo").val();
        if(certificateNo.length<=11){$('#certificateNoe').hide();}else{$('#certificateNoe').show();}

        var amount = $("#amount").val();
        if(amount){$('#amounte').hide();}else{$('#amounte').show();}

        var dateFrom = $("#dateFrom").val();
        if(dateFrom){$('#dateFrome').hide();}else{$('#dateFrome').show();}
                 
        });



        $('select').on('change', function () {
        var branchLocation = $("#branchLocation").val();
        if(branchLocation){$('#branchLocatione').hide();}else{$('#branchLocatione').show();}

        var employeeReference = $("#employeeReference").val();
        if(employeeReference){$('#employeeReferencee').hide();}else{$('#employeeReferencee').show();}

        var period = $("#period").val();
        if(period){$('#periode').hide();}else{$('#periode').show();}
       
        });
        /*End Hide the error if filed is filed*/




        /*Validate Number Filed*/
        $("#amount,#nidNo,#mobileNo,#openingBalance,#certificateNo").on('input', function() {            
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        /*End Validate Number Filed*/


        function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

        function GetFormattedDate(CurrentDate) {
            var date = new Date(CurrentDate);
            return( pad(date.getDate(),2) + '-'+ pad((date.getMonth() + 1),2) +'-' +  date.getFullYear());
        }


       /*Calculate Mature Date*/       

       $("#period").on('change',  function() {
           var months = parseInt($("#period option:selected").attr('months'));
           var dateFrom = $("#dateFrom").val();
           
           if (dateFrom!="") {
            parts = dateFrom.split("-");
            var CurrentDate = new Date(parts[2],parts[1]-1,parts[0]);
            CurrentDate.setMonth(CurrentDate.getMonth() + months);
            $("#matureDate").val(GetFormattedDate(CurrentDate));
        }
           
       });
       
        /*End Calculate Mature Date*/

         /*Date From*/
         $("#dateFrom").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1995:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                 $("#dateFrome").hide(); 

                  var months = parseInt($("#period option:selected").attr('months'));
                   var dateFrom = $("#dateFrom").val();

                   
                    parts = dateFrom.split("-");
                    var CurrentDate = new Date(parts[2],parts[1]-1,parts[0]);
                    var CurrentDate2 = new Date(parts[2],parts[1]-1,parts[0]);
                    $("#effectiveDate").datepicker('option','minDate',CurrentDate2);
                    CurrentDate.setMonth(CurrentDate.getMonth() + months);
                    $("#matureDate").val(GetFormattedDate(CurrentDate));

                }                 
                    
        });
        /*End Date From*/ 


        /*Effective Date*/
         $("#effectiveDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1995:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy'
        });
        /*End Effective Date*/



        /*Filter Employee Base on Branch*/
        $("#branchLocation").change(function() {
            var branchId = $("#branchLocation option:selected").val();
            var csrf = "{{csrf_token()}}";
            $.ajax({
                url: './getEmployeeBaseOnBranch',
                type: 'POST',
                dataType: 'json',
                data: {branchId: branchId, _token:csrf},
                success: function(employee) {
                    //alert(JSON.stringify(employee));
                    $("#employeeReference").empty();
                    $('#employeeReference').append("<option value=''>Select Employee</option>");
                    $.each(employee, function(index, emp) {
                         $('#employeeReference').append("<option value='"+ emp.id+"'>"+emp.emp_id+"-"+emp.emp_name_english+"</option>");
                    });
                    
                },
                error: function(argument) {
                    alert('response error')
                }
            });
           
            
        });
        /*End Filter Employee Base on Branch*/



    });/*End Ready*/


</script>


@endsection
