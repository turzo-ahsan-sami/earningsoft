@extends('layouts/acc_layout')
@section('title', '| FDR Register')
@section('content')

<div class="row add-data-form">
<div class="col-md-2"></div>
<div class="col-md-8 fullbody">
<div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="{{url('viewAccFdrInterest/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>FDR Interest List</a>
</div>

<div class="panel panel-default panel-border">
    <div class="panel-heading">
        <div class="panel-title">FDR Interest</div>
    </div>


<div class="panel-body">
            <div class="row">                
                <div class="col-md-12">
                {!! Form::open(array('url' => 'storeFdrInterest','role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                
                {{-- <div class="form-horizontal form-groups"> --}}
                
                    <div class="col-md-6">

                    <div class="form-group">
                        {!! Form::label('interestId', 'Interest ID:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8"> 
                                                         
                            {!! Form::text('interestId',$interestId, ['class'=>'form-control','readonly']) !!}

                            
                        </div>
                    </div>

                     <div class="form-group">
                        {!! Form::label('fdrType', 'FDR Type:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                          $fdrTypeList = array(''=>'Select FDR Type') + DB::table('acc_fdr_type')->pluck('name','id')->toArray();
                        @endphp                                  
                            {!! Form::select('fdrType',$fdrTypeList ,null, ['class'=>'form-control', 'id' => 'fdrType']) !!}
                            
                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('bank', 'Bank Name:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                          $bankList = array(''=>'Select Bank') + DB::table('gnr_bank')->pluck('name','id')->toArray();
                        @endphp                                  
                            {!! Form::select('bank',$bankList ,null, ['class'=>'form-control', 'id' => 'bank']) !!}
                            <p id='banke' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                    </div>


                     <div class="form-group">
                        {!! Form::label('bankBranch', 'Bank Branch Location:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        @php
                            $bankBranchList = DB::table('gnr_bank_branch')->select('name','id','bankId_fk')->get();
                        @endphp

                        <select id="bankBranch" name="bankBranch" class="form-control">
                             <option value="">Select Location</option>
                             @foreach($bankBranchList as $bankBranch)
                                @php
                                $bankShortName = DB::table('gnr_bank')->where('id',$bankBranch->bankId_fk)->value('shortName');
                                @endphp
                             <option value="{{$bankBranch->id}}">{{$bankBranch->name.'-'.$bankShortName}}</option>>
                             @endforeach
                         </select>                            
                            
                            <p id='bankBranche' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                    </div>


                     <div class="form-group">
                            {!! Form::label('accId', 'Account No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            @php
                                $accId = array(''=>'Select Account') + DB::table('acc_fdr_account')->where('status',1)->pluck('accNo','id')->toArray();
                            @endphp
                                
                           {!! Form::select('accId', $accId,null ,['class'=>'form-control', 'id' => 'accId']) !!}
                                <p id='accIde' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div> 


                    <div class="form-group">
                            {!! Form::label('principalAmount', 'Principal Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                
                            {!! Form::text('principalAmount', null ,['class'=>'form-control', 'id' => 'principalAmount','readonly']) !!}
                            </div>
                    </div> 
                    </div>
                    <div class="col-md-6">
                    <div class="form-group">
                            {!! Form::label('interestAmount', 'Interest Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                
                            {!! Form::text('interestAmount', null ,['class'=>'form-control', 'id' => 'interestAmount']) !!}
                            <p id='interestAmounte' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div> 
                     <div class="form-group">
                            {!! Form::label('bankCharge', 'Bank Charge:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                
                            {!! Form::text('bankCharge', null ,['class'=>'form-control', 'id' => 'bankCharge']) !!}
                            <p id='bankChargee' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div> 
                    <div class="form-group">
                            {!! Form::label('tax', 'Tax:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                
                            {!! Form::text('tax', null ,['class'=>'form-control', 'id' => 'tax']) !!}
                            <p id='taxe' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div>


                    {!! Form::hidden('receivableIds',null,['id'=>'receivableIds']) !!}

                    <div class="form-group">
                            {!! Form::label('receivableAmount', 'Receivable Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                
                            {!! Form::text('receivableAmount', null ,['class'=>'form-control', 'id' => 'receivableAmount','amount'=>'0','readonly']) !!}
                            
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('netInterestAmount', 'Net Interest Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                
                            {!! Form::text('netInterestAmount', null ,['class'=>'form-control', 'id' => 'netInterestAmount','readonly']) !!}
                            </div>
                    </div>
                    <div class="form-group">
                            {!! Form::label('date', 'Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                
                            {!! Form::text('receiveDate', null ,['class'=>'form-control', 'id' => 'receiveDate','readonly','style'=>'cursor:pointer']) !!}
                            <p id='receiveDatee' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div>
                
                </div>

            
                
            {{-- </div> --}}

        </div>


         <div class="form-group">
                        <div class="col-sm-12 text-right" style="padding-right: 30px;">
                            {!! Form::submit('Submit', ['id'=>'submitButton','class' => 'btn btn-info submitButton']) !!}
                            <a href="{{url('viewAccFdrInterest/')}}" class="btn btn-danger closeBtn">Close</a>
                        </div>
                </div>

    </div>
    </div>
    {!! Form::close() !!}

</div>
<div class="footerTitle" style="border-top:1px solid white"></div>
</div>
<div class="col-md-2"></div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        function num(argument) {
            if (argument!=null) {
                return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            else{
                return '0.00'.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            
        }

        function getFilteredAccounts() {
            var fdrType = $("#fdrType").val();
            var bank = $("#bank option:selected").val();
            var bankBranch = $("#bankBranch option:selected").val();
            var csrf = "{{csrf_token()}}";


            $.ajax({
                url: './getAccFdrFilteredAccount',
                type: 'POST',
                dataType: 'json',
                data: {fdrType: fdrType,bank: bank,bankBranch: bankBranch,_token: csrf},
            })
            .done(function(accounts) {
                if (accounts.accessDenied) {
                    showAccessDeniedMessage();
                    return false;
                }
                $("#accId").empty();
                $("#accId").append("<option value=''>Select Account</option>");


                $.each(accounts, function(index, account) {
                     $("#accId").append("<option value='"+account.id+"'>"+account.accNo+"</option>");
                });

                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
        }


        $("#fdrType,#bankBranch").on('change', function() {            
            getFilteredAccounts();
        });



        /*Filter Branch Location On selecting Bank*/
         $("#bank").on('change', function() {
             var bank = $("#bank option:selected").val();
             var csrf = "{{csrf_token()}}";

             $.ajax({
                 url: './accFdrGetBranchLocationBaseOnBank',
                 type: 'POST',
                 dataType: 'json',
                 data: {bank: bank, _token: csrf},
             })
             .done(function(branch) {
                if (branch.accessDenied) {
                    showAccessDeniedMessage();
                    return false;
                }
                
                $("#bankBranch").empty();
                $("#bankBranch").append("<option value=''>Select Location</option>");
                $.each(branch, function(index, branch) {
                     $("#bankBranch").append("<option value='"+branch.id+"'>"+branch.name+'-'+branch.bankName+"</option>");
                });
               
                getFilteredAccounts();
                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });
             
         });
         /*End Filter Branch Location On selecting Bank*/

        

         /*On change Account*/
         $("#accId").on('change', function() {
            var accId = $("#accId option:selected").val();
             if (accId!='') {                
                var csrf = "{{csrf_token()}}";

                $.ajax({
                    url: './fdrGetAccountInfo',
                    type: 'POST',
                    dataType: 'json',
                    data: {accId: accId, _token: csrf},
                })
                .done(function(data) {
                    if (data.accessDenied) {
                        showAccessDeniedMessage();
                        return false;
                    }
                    $("#principalAmount").val(num(data['account'].principalAmount));
                    $("#receivableIds").val('{'+data['receivableIds']+'}');

                    $("#receivableAmount").attr('amount',data['receiveableAmount']);
                    $("#receivableAmount").val(num(data['receiveableAmount']));

                    $("#receiveDate").datepicker("option","minDate",new Date(data['account'].openingDate)); 

                    calculateNetInterest();                  
                    console.log("success");
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
                
             }
         });
         /*End On change Account*/

         /*Calculate NetInterest*/
         function calculateNetInterest() {
             var interestAmount = 0;
             var bankCharge = 0;
             var tax = 0;
             var receivableAmount = parseFloat($("#receivableAmount").attr('amount'));

             if ($("#interestAmount").val()!='') { interestAmount = parseFloat($("#interestAmount").val()); }
             if ($("#bankCharge").val()!='') { bankCharge = parseFloat($("#bankCharge").val()); }
             if ($("#tax").val()!='') { tax = parseFloat($("#tax").val()); }

             var netInterestAmount = interestAmount - bankCharge - tax - receivableAmount;

             $("#netInterestAmount").val(netInterestAmount);
         }

         $("#interestAmount,#bankCharge,#tax").on('input', function() {            
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            calculateNetInterest();
         });
         /*End Calculate NetInterest*/


         /*Receive Date*/
         $("#receiveDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                $("#receiveDatee").empty();
            }
        });
         /*End Receive Date*/


         /*Submit the data*/

         $("form").submit(function(event) {
             event.preventDefault();
             
             $.ajax({
                 url: './storeFdrInterest',
                 type: 'POST',
                 dataType: 'json',
                 data: $('form').serialize(),
             })
             .done(function(data) {
                if (data.accessDenied) {
                    showAccessDeniedMessage();
                    return false;
                }
               if (data.errors) {
                    if (data.errors['accId']) {
                        $("#accIde").empty();
                        $("#accIde").append('* '+data.errors['accId']);
                    }
                    if (data.errors['interestAmount']) {
                        $("#interestAmounte").empty();
                        $("#interestAmounte").append('* '+data.errors['interestAmount']);
                    }
                    if (data.errors['bankCharge']) {
                        $("#bankChargee").empty();
                        $("#bankChargee").append('* '+data.errors['bankCharge']);
                    }
                    if (data.errors['tax']) {
                        $("#taxe").empty();
                        $("#taxe").append('* '+data.errors['tax']);
                    }
                    if (data.errors['receiveDate']) {
                        $("#receiveDatee").empty();
                        $("#receiveDatee").append('* '+data.errors['receiveDate']);
                    }
                   

                 } /*end has Errors*/
                 else{
                    location.href = "viewAccFdrInterest";
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
         
         /*End Submit the data*/


          /*On input/change Hide the Errors*/
         $(document).on('input', 'input', function() {
             $(this).closest('div').find('.error').empty();
         });
          $(document).on('change', 'select', function() {
             $(this).closest('div').find('.error').empty();
         });
         /*End On input/change Hide the Errors*/



    });/*End Ready*/
</script>
@endsection
