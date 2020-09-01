@extends('layouts/acc_layout')
@section('title', '| FDR Register')
@section('content')

<div class="row add-data-form">
<div class="col-md-2"></div>
<div class="col-md-8 fullbody">
<div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="{{url('viewAccFdrReceivable/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>FDR Receivable List</a>
</div>

<div class="panel panel-default panel-border">
    <div class="panel-heading">
        <div class="panel-title">FDR Receivable</div>
    </div>


<div class="panel-body">
            <div class="row">                
                <div class="col-md-12">
                {!! Form::open(array('url' => 'storeFdrReceivable','role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                
                {{-- <div class="form-horizontal form-groups"> --}}
                
                    <div class="col-md-6">

                    <div class="form-group">
                        {!! Form::label('receivableId', 'Receivable ID:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8"> 
                                                         
                            {!! Form::text('interestId',$receivableId, ['class'=>'form-control','readonly']) !!}

                            
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
                        {!! Form::label('totalAmount', 'Total Amount:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                                
                        {!! Form::text('totalAmount', null ,['class'=>'form-control', 'id' => 'totalAmount','readonly']) !!}
                        </div>
                    </div>

                    

                    <div class="form-group">
                            {!! Form::label('interestRate', 'Interest Rate:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                
                            {!! Form::text('interestRate', null ,['class'=>'form-control', 'id' => 'interestRate']) !!}
                            <p id="interestRatee" class="error" style="color: red;"></p>
                            </div>

                    </div>


                    <div class="form-group">
                            {!! Form::label('receivableAmount', 'Receivable Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                
                            {!! Form::text('receivableAmount', null ,['class'=>'form-control', 'id' => 'receivableAmount']) !!}
                            <p id='receivableAmounte' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div> 

                    <div class="form-group">
                            {!! Form::label('dateFrom', 'Date From:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                
                            {!! Form::text('dateFrom', null ,['class'=>'form-control', 'id' => 'dateFrom','readonly','style'=>'cursor:pointer']) !!}
                            <p id='dateFrome' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div>
                    
                   
                   
                    <div class="form-group">
                            {!! Form::label('receivableDate', 'Receivable Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                
                            {!! Form::text('receivableDate', null ,['class'=>'form-control', 'id' => 'receivableDate','readonly','style'=>'cursor:pointer']) !!}
                            <p id='receivableDatee' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div>



                    <div class="form-group">
                        {!! Form::label('days', 'Days:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                                
                        {!! Form::text('days', null ,['class'=>'form-control', 'id' => 'days','readonly']) !!}
                        </div>
                    </div>


                
                </div>

            
                
            {{-- </div> --}}

        </div>


         <div class="form-group">
                        <div class="col-sm-12 text-right" style="padding-right: 30px;">
                            {!! Form::submit('Submit', ['id'=>'submitButton','class' => 'btn btn-info submitButton']) !!}
                            <a href="{{url('viewAccFdrReceivable/')}}" class="btn btn-danger closeBtn">Close</a>
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
            return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
                    $("#totalAmount").val(num(data['totalAmount']));

                    $("#dateFrom").datepicker("option","minDate",new Date(data['account'].openingDate));                   
                    $("#receivableDate").datepicker("option","minDate",new Date(data['account'].openingDate));                   
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


         $("#receivableAmount,#interestRate").on('input', function() {            
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            
         });


         function calculateDays() {
             var dateFrom = $("#dateFrom").val();
             var receivableDate = $("#receivableDate").val();

             if (dateFrom!='' && receivableDate!='') {
                var startDate = $.datepicker.parseDate('dd-mm-yy',dateFrom);
                var endDate = $.datepicker.parseDate('dd-mm-yy',receivableDate);
                var dayDiff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24))+1;

                $("#days").val(dayDiff);
             }
         }
         


         /*Receive Date*/
         $("#receivableDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                $("#receivableDatee").empty();                
                calculateDays();
            }
        });
         /*End Receive Date*/

          /*Receive Date*/
         $("#dateFrom").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                $("#dateFrome").empty();
                var date = $.datepicker.parseDate('dd-mm-yy', $(this).val());
                $("#receivableDate").datepicker("option","minDate",new Date(date)); 
                calculateDays();
            }
        });
         /*End Receive Date*/


         /*Submit the data*/

         $("form").submit(function(event) {
             event.preventDefault();
             
             $.ajax({
                 url: './storeFdrReceivable',
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
                    if (data.errors['interestRate']) {
                        $("#interestRatee").empty();
                        $("#interestRatee").append('* '+data.errors['interestRate']);
                    }
                    if (data.errors['receivableAmount']) {
                        $("#receivableAmounte").empty();
                        $("#receivableAmounte").append('* '+data.errors['receivableAmount']);
                    }
                    
                    if (data.errors['receivableDate']) {
                        $("#receivableDatee").empty();
                        $("#receivableDatee").append('* '+data.errors['receivableDate']);
                    }
                    if (data.errors['dateFrom']) {
                        $("#dateFrome").empty();
                        $("#dateFrome").append('* '+data.errors['dateFrom']);
                    }
                   

                 } /*end has Errors*/
                 else{
                    location.href = "viewAccFdrReceivable";
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
