@extends('layouts/acc_layout')
@section('title', '| OTS Register')
@section('content')

<div class="row add-data-form">
<div class="col-md-1"></div>
<div class="col-md-10 fullbody">
<div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="{{url('viewOtsPrincipalPayment/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>OTS Account Closing List</a>
</div>

<div class="panel panel-default panel-border">
    <div class="panel-heading">
        <div class="panel-title">OTS Account Closing</div>
    </div>


<div class="panel-body">
            <div class="row">                
                <div class="col-md-12">
               {{--  {!! Form::open(array('url' => 'storeOtsPayment','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!} --}}
                
                <div class="form-horizontal form-groups">
                
                    <div class="col-md-6">

                    <div class="form-group">
                            {!! Form::label('paymentId', 'Payment ID:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                            
                                
                           {!! Form::text('paymentId', $paymentId, ['class' => 'form-control', 'id' => 'paymentId','style'=>'text-align:left;', 'type' => 'text','autocomplete'=>'off','readonly']) !!}
                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('branchLocation', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            @php
                                $branches = DB::table('gnr_branch')->select('id','name','branchCode')->get();
                            @endphp
                            <select id="branchLocation" name="branchLocation" class="form-control">
                                <option value="">Select Branch</option>
                                <option value="0">All</option>
                                @foreach($branches as $branch)
                                <option value="{{$branch->id}}">{{str_pad($branch->branchCode,3,'0',STR_PAD_LEFT).'-'.$branch->name}}</option>
                                @endforeach
                            </select>                              
                            </div>
                    </div>
                   

                    <div class="form-group">
                            {!! Form::label('accNature', 'Account Nature:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            @php
                                $accNature = array(''=>'Select Account Nature') + DB::table('acc_ots_period')->pluck('name','id')->toArray();
                            @endphp
                                
                           {!! Form::select('accNature', $accNature,null ,['class'=>'form-control', 'id' => 'accNature']) !!}
                                
                            </div>
                    </div>

                     <div class="form-group">
                            {!! Form::label('accId', 'Account No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            @php
                                $accId = array(''=>'Select Account') + DB::table('acc_ots_account')->where('status',1)->pluck('accNo','id')->toArray();
                            @endphp
                                
                           {!! Form::select('accId', $accId,null ,['class'=>'form-control', 'id' => 'accId']) !!}
                                
                            </div>
                    </div> 

                     <div class="form-group">
                        {!! Form::label('closingDate', 'Closing Date:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::text('closingDate', $value = null, ['class' => 'form-control', 'id' => 'closingDate', 'type' => 'text','placeholder' => 'Enter Closing Date','autocomplete'=>'off','readonly','style'=>'cursor:pointer']) !!}
                        </div>
                    </div>
                
                </div>


                {{-- 2nd col-6 --}}
                <div class="col-md-6">

                    <div class="form-group">
                            {!! Form::label('paymentMode', 'Payment Mode:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8"> 

                            {!! Form::radio('paymentMode', '1', true,['id'=>'cashChecKbox']) !!}   
                            {!! Form::label('cash', 'Cash', ['class' => 'control-label']) !!}  &nbsp &nbsp

                             {!! Form::radio('paymentMode', '2', false,['id'=>'bankChecKbox']) !!}   
                            {!! Form::label('bank', 'Bank', ['class' => 'control-label']) !!}                      
                                
                          
                                
                            </div>
                    </div>

                    {{-- <div class="form-group">
                            {!! Form::label('bankName', 'Bank Name:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            @php
                                $branches = DB::table('gnr_branch')->select('id','name','branchCode')->get();
                            @endphp
                            <select id="bankName" name="bankName" class="form-control">
                                <option value="">Select Bank</option>                                
                                @foreach($branches as $branch)
                                <option value="{{$branch->id}}">{{str_pad($branch->branchCode,3,'0',STR_PAD_LEFT).'-'.$branch->name}}</option>
                                @endforeach
                            </select>                              
                            </div>
                    </div> --}}
                   

                    <div class="form-group">
                            {!! Form::label('bankAccNumber', 'Account Number:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            @php
                                $bankAccNumber = array(''=>'Select Account Number') + DB::table('acc_account_ledger')->where('accountTypeId',5)->where('id','!=',350)->pluck('name','id')->toArray();
                            @endphp
                                
                           {!! Form::select('bankAccNumber', $bankAccNumber,null ,['class'=>'form-control', 'id' => 'bankAccNumber','disabled']) !!}
                                <p id="bankAccNumbere"></p>
                            </div>
                    </div>

                     <div class="form-group">
                            {!! Form::label('chequeNumber', 'Cheque Number:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                           {!! Form::text('chequeNumber', $value = null, ['class' => 'form-control', 'id' => 'chequeNumber', 'type' => 'text','placeholder' => 'Enter Cheque Number','autocomplete'=>'off','readonly']) !!}
                           <p id="chequeNumbere"></p>
                                
                            </div>
                    </div> 
                    
                
                </div> {{-- End 2nd col-6 --}}



                
                 <div class="form-group">
                        <div class="col-sm-12 text-right" style="padding-right:29px;">
                            {!! Form::button('Select', ['id'=>'selectButton','class' => 'btn btn-info submitButton','type'=>'button']) !!}
                            
                        </div>
                </div>

            
            {{-- </div> --}}
                
            </div>

        </div>

<br>

    

        <table id="accountTable" class="table table-bordered responsive" style="color: black;">
            
            <thead>
                <tr>
                    <th>Opening Date</th>
                    <th>Account Name</th>
                    <th>Account No</th>
                    <th>Certificate Number</th>
                    <th>Amount (Tk)</th>
                    
                </tr>
                
            </thead>
            <tbody id="accountTableTbody">
                
            </tbody>
            
        </table>
        <br>
         <div class="form-group">
                        <div class="col-sm-12 text-right" style="padding-right: 0px;">
                            {!! Form::button('Submit', ['id'=>'submitButton','class' => 'btn btn-info submitButton']) !!}
                            <a href="{{url('viewOtsPrincipalPayment/')}}" class="btn btn-danger closeBtn">Close</a>
                        </div>
                </div>

    </div>
    </div>
    {{-- {!! Form::close() !!} --}}

</div>
<div class="footerTitle" style="border-top:1px solid white"></div>
</div>
<div class="col-md-1"></div>
</div>



<script type="text/javascript">
    $(document).ready(function() {

         function num(argument) {
            return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        /*On Change Branch*/
        $("#branchLocation").on('change',  function() {
            var branchId = $("#branchLocation option:selected").val();
            var periodId = $("#accNature option:selected").val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './filterOtsAccByBranch',
                type: 'POST',
                dataType: 'json',
                data: {branchId: branchId, periodId: periodId, _token:csrf},
            })
            .done(function(account) {
                $("#accId").empty();
                    $('#accId').append("<option value=''>Select Account</option>");
                    $.each(account, function(index, account) {
                         $('#accId').append("<option value='"+ account.id+"'>"+account.accNo+"</option>");
                    });
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
        });
        /*End On Change Branch*/

        /*On Change Acc Nature*/
        $("#accNature").on('change', function() {            
            $("#branchLocation").trigger('change');
        });
        /*End On Change Acc Nature*/

        /*Select Button*/
        $("#selectButton").on('click', function() {
            var accId = $("#accId option:selected").val();
            var csrf = "{{csrf_token()}}";
            if (accId!='') {

                $.ajax({
                    url: './getOtsAccDetails',
                    type: 'POST',
                    dataType: 'json',
                    data: {accId: accId, _token: csrf},
                })
                .done(function(data) {
                    markup = "<tr style='line-height: 30px;'><td>"+data['openingDate']+"<td  class='name'><input id='tAccId' style='display:none;' value='"+data['accId']+"'><input id='tAccNo' style='display:none;' value='"+data['accNo']+"'>"+data['memberName']+"</td><td>"+data['accNo']+"</td><td>"+data['certificateNo']+"</td><td class='amount'>"+num(data['principalAmount'])+"</td></tr><tr><td colspan='4'>Interest Payment Due</td><td class='amount'><input id='tDueAmount' style='display:none;' value='"+data['dueAmount']+"'>"+num(data['dueAmount'])+"</td></tr></tr><tr><td colspan='4'>Account Closing Fee</td><td class='amount'>"+num(data['serviceCharge'])+"</td></tr><tr><td colspan='4' style='font-weight:bold;'>Net Payable Amount</td><td class='amount' style='font-weight:bold;'><input id='tPayableAmount' style='display:none;' value='"+data['payableAmount']+"'>"+num(data['payableAmount'])+"</td></tr>";
                     $("#accountTableTbody").empty();
                    $("#accountTableTbody").append(markup);
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
                
            }
            
        });
        /*End Select Button*/


        /*Submit Button*/
        $("#submitButton").on('click', function(event) {
            event.preventDefault();
            var accId = $("#tAccId").val();
            var accNo = $("#tAccNo").val();
            var amount = $("#tPayableAmount").val();
            var dueAmount = $("#tDueAmount").val();
            var closingDate = $("#closingDate").val();
            var paymentMode = $('input[name=paymentMode]:checked').val();
            var bankAccNumber = $("#bankAccNumber").val();
            var chequeNumber = $("#chequeNumber").val();
            var csrf = "{{csrf_token()}}";
           
           

            if (accId!=undefined) {
                $.ajax({
                    url: './storeOtsPrincipalPayment',
                    type: 'POST',
                    dataType: 'json',
                    data: {accId: accId,accNo: accNo, amount: amount,dueAmount: dueAmount,closingDate: closingDate,paymentMode: paymentMode,bankAccNumber: bankAccNumber,chequeNumber: chequeNumber, _token: csrf},
                })
                .done(function(_response) {
                    if (_response.errors) {
                        if (_response.errors['bankAccNumber']) {
                            $('#bankAccNumbere').empty();
                            $('#bankAccNumbere').append('<span class="errormsg" style="color:red;">'+_response.errors.bankAccNumber+'</span>');
                            
                        }
                        if (_response.errors['chequeNumber']) {
                            $('#chequeNumbere').empty();
                            $('#chequeNumbere').append('<span class="errormsg" style="color:red;">'+_response.errors.chequeNumber+'</span>');
                            
                        }
                    }
                    else{
                       window.location.href = "viewOtsPrincipalPayment"; 
                    }

                    
                    console.log("success");
                })
                .fail(function() {
                    alert("error");
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
                
            }
            
        });
        /*End Submit Button*/


        /*Hide the Erros*/
        $("select").on('change', function(event) {
            $("#bankAccNumbere").empty();            
        });

        $("input").on('input', function(event) {
            $("#chequeNumbere").empty();            
        });
        /*End Hide the Erros*/






        /*Closing Date*/
        $("#closingDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1995:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy'           
        });
        /*End Closing Date*/

        /*Input Number Field*/
        $("#chequeNumber").on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');            
        });
        /*End Input Number Field*/

        /*Maintain Check Box Selection*/
        $(document).on('click', '#cashChecKbox', function() {
            if ($(this).is(':checked')) {            
                $("#bankAccNumber").prop("disabled", true);
                $('#chequeNumber').attr('readonly', true);
            }
            else{
                $("#bankAccNumber").prop("disabled", false);
                $('#chequeNumber').attr('readonly', false);
            }
            
        });

        $(document).on('click', '#bankChecKbox', function() {
            if ($(this).is(':checked')) {            
                $("#bankAccNumber").prop("disabled", false);
                $('#chequeNumber').attr('readonly', false);
                
            }
            else{
                $("#bankAccNumber").prop("disabled", true);
                $('#chequeNumber').attr('readonly', true);
               
            }
            
        });
        /*End Maintain Check Box Selection*/



    });/*End Ready*/
</script>



<style type="text/css">
    #accountTable tbody tr td.amount,tfoot tr td.amount { text-align: right;padding-right: 5px !important; }
    #accountTable tbody tr td.name { text-align: left;padding-left: 5px !important; }
    #accountTable thead tr th { margin: 0px !important; padding: 0px !important;line-height: 40px !important;}
    #accountTable tbody tr td { margin: 0px !important; padding: 0px !important;line-height: 35px !important;}
</style>


@endsection
