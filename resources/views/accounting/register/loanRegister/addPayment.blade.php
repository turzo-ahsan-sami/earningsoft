@extends('layouts/acc_layout')
@section('title', '| Loan Register')
@section('content')

<div class="row add-data-form">
<div class="col-md-1"></div>
<div class="col-md-10 fullbody">
<div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="{{url('viewLoanRegisterPayment/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>Payment List</a>
</div>

<div class="panel panel-default panel-border">
	<div class="panel-heading">
	    <div class="panel-title">Donor/Bank Repayment</div>
	</div>




    {{-- ////////////////////////////// --}}

    <div class="panel-body">
    {!! Form::open(['url'=>'','class'=>'form-horizontal form-groups']) !!}
            <div class="row">                
                <div class="col-md-12"> 
                    <div class="col-md-6">


                    <div class="form-group">
                        {!! Form::label('paymentId', 'Payment ID:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">          
                            {!! Form::text('paymentId',$paymentId, ['class'=>'form-control', 'id' => 'paymentId','readonly']) !!}
                        </div>
                    </div>

                     <div class="form-group">
                        {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        @php
                        $activeProjectTypes = DB::table('acc_loan_register_account')->pluck('projectTypeId_fk')->toArray();
                          $projectTypes = DB::table('gnr_project_type')->whereIn('id',$activeProjectTypes)->select('id','name','projectTypeCode')->get();
                        @endphp
                         <select name="projectType" class="form-control input-sm" id="projectType">
                            <option value="">Select Project Type</option>                                         
                            @foreach($projectTypes as $projectType)
                            <option value="{{$projectType->id}}">{{str_pad($projectType->projectTypeCode,3,"0",STR_PAD_LEFT).'-'.$projectType->name}}</option>
                            @endforeach
                        </select>                               
                            <p id='projectTypee' class="error"></p>
                        </div>
                    </div>
                    

                    <div class="form-group">
                        {!! Form::label('donor', 'Donor:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                        $activeDonors = DB::table('acc_loan_register_account')->pluck('bankId_fk')->toArray();
                          $donorList = array(''=>'Select Donor') + DB::table('gnr_bank')->whereIn('id',$activeDonors)->pluck('name','id')->toArray();
                        @endphp                                  
                            {!! Form::select('donor',$donorList ,null, ['class'=>'form-control', 'id' => 'donor']) !!}
                            <p id='donore' class="error"></p>
                        </div>
                    </div>

                    {!! Form::hidden('donorType',null,['id'=>'donorType']) !!}

                    <div class="form-group">
                        {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                          $activeBranches = DB::table('acc_loan_register_account')->pluck('bankBranchId_fk')->toArray();
                          $donorBranchList = DB::table('gnr_bank_branch')->whereIn('id',$activeBranches)->select('name','id','bankId_fk')->get();
                          $bankShortNameList = array();
                          foreach ($donorBranchList as $key => $donorBranch) {
                            $bankShortName = DB::table('gnr_bank')->where('id',$donorBranch->bankId_fk)->value('shortName');
                              array_push($bankShortNameList, $bankShortName);
                          }
                        @endphp

                        <select id="branch" name="branch" class="form-control">
                            <option value="">Select Branch</option>
                            @foreach($donorBranchList as $index => $branch)
                            <option value="{{$branch->id}}">{{$branch->name.'-'.$bankShortNameList[$index]}}</option>
                            @endforeach
                        </select>
                           
                            <p id='branche' class="error"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('loanProduct', 'Loan Product:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                        $activeLoanProduts = DB::table('acc_loan_register_account')->where('status',1)->groupBy('loanProductId_fk')->pluck('loanProductId_fk')->toArray();
                          $loanProductList = array(''=>'Select Loan Product') + DB::table('gnr_loan_product')->whereIn('id',$activeLoanProduts)->pluck('name','id')->toArray();
                        @endphp                                  
                            {!! Form::select('loanProduct',$loanProductList ,null, ['class'=>'form-control', 'id' => 'loanProduct']) !!}
                            <p id='loanProducte' class="error"></p>
                        </div>
                    </div>

                    

                   {{--  <div class="form-group">
                        {!! Form::label('project', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        @php
                          $projects = DB::table('gnr_project')->select('id','name','projectCode')->get();
                        @endphp
                         <select name="project" class="form-control input-sm" id="project">
                            <option value="">Select Project</option>                                         
                            @foreach($projects as $project)
                            <option value="{{$project->id}}">{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT).'-'.$project->name}}</option>
                            @endforeach
                        </select>                               
                            <p id='projecte' class="error"></p>
                        </div>
                    </div> --}}

                   
                
                    
                    
                     <div class="form-group">
                            {!! Form::label('accNo', 'Account No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::select('accNo', [''=>'Select Account'],null, ['class'=>'form-control', 'id' => 'accNo','disabled']) !!}
                                <p id='accNoe' class="error"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('isRebate', 'Is Rebate?:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">

                            @php
                                $rebateArray = array('0'=>'No','1'=>'Yes');
                            @endphp
                                
                           {!! Form::select('isRebate',$rebateArray ,null, ['class'=>'form-control', 'id' => 'isRebate']) !!}
                                <p id='isRebatee' class="error"></p>
                            </div>
                    </div>

                    

                    {{-- <div class="form-group">
                            {!! Form::label('agreementDate', 'Agreement/Sanction Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('agreementDate', null, ['class'=>'form-control', 'id' => 'agreementDate','readonly','style'=>'cursor:pointer']) !!}
                                <p id='agreementDatee' class="error"></p>
                            </div>
                    </div> --}}

                   {{--  <div class="form-group">
                            {!! Form::label('loanSanctionNumber', 'Loan Sanction Number:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('loanSanctionNumber', null, ['class'=>'form-control', 'id' => 'loanSanctionNumber']) !!}
                                <p id='loanSanctionNumbere' class="error"></p>
                            </div>
                    </div> --}}

                    {{-- <div class="form-group">
                            {!! Form::label('loanDate', 'Loan Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('loanDate', null, ['class'=>'form-control', 'id' => 'loanDate','readonly','style'=>'cursor:pointer']) !!}
                                <p id='loanDatee' class="error"></p>
                            </div>
                    </div> --}}
                    

                   
                </div> {{-- End of 1st coloum --}}

                <div class="col-md-6">

                <div class="form-group">
                            {!! Form::label('phase', 'Phase:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::select('phase', [''=>'Select Phase'],null, ['class'=>'form-control', 'id' => 'phase','disabled']) !!}
                                <p id='phasee' class="error"></p>
                            </div>
                    </div>


                 <div class="form-group">
                            {!! Form::label('cycle', 'Cycle:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::select('cycle', [''=>'Select Cycle'],null, ['class'=>'form-control', 'id' => 'cycle','disabled']) !!}
                                <p id='cyclee' class="error"></p>
                            </div>
                    </div>

                    

                     <div class="form-group">
                            {!! Form::label('numOfInstallment', 'No Of Installment:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('numOfInstallment', null, ['class'=>'form-control', 'id' => 'numOfInstallment','readonly']) !!}
                                <p id='numOfInstallmente' class="error"></p>                                
                            </div>
                    </div>

                     <div class="form-group">
                            {!! Form::label('loanAmount', 'Loan Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('loanAmount', null, ['class'=>'form-control', 'id' => 'loanAmount','readonly']) !!}
                                <p id='loanAmounte' class="error"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('principalAmount', 'Principal Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('principalAmount', null, ['amount'=>'0','class'=>'form-control', 'id' => 'principalAmount']) !!}
                                <p id='principalAmounte' class="error"></p>
                                <p id='principalAmountValuee' hasError='0' style="display: none;"></p>
                            </div>
                    </div>



               <div class="form-group">
                            {!! Form::label('interestAmount', 'Interest Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('interestAmount', null, ['amount'=>'0','class'=>'form-control', 'id' => 'interestAmount']) !!}
                                <p id='interestAmounte' class="error"></p>
                                <p id='interestAmountValuee' hasError='0' style="display: none;"></p>
                            </div>
                    </div>

                    <div id="rebateAmountDiv" style="display: none;">

                    <div class="form-group">
                            {!! Form::label('rebateAmount', 'Rebate Amount (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('rebateAmount', null, ['class'=>'form-control', 'id' => 'rebateAmount']) !!}
                                <p id='rebateAmounte' class="error"></p>
                            </div>
                    </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('paymentDate', 'Payment Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('paymentDate', null, ['class'=>'form-control', 'id' => 'paymentDate','readonly','style'=>'cursor:pointer;']) !!}
                                <p id='paymentDatee' class="error"></p>
                            </div>
                    </div>

                   

                    {{-- <div class="form-group">
                            {!! Form::label('repaymentFrequency', 'Repayment Frequency:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8"> 
                            @php
                               $monthArray = array(''=>'Select Duration','1'=>'1 month','2'=>'2 months','3'=>'3 months','4'=>'4 months','5'=>'5 months','6'=>'6 months','7'=>'7 months','8'=>'8 months','9'=>'9 months','10'=>'10 months','11'=>'11 months','12'=>'12 months');
                           @endphp    

                                {!! Form::select('repaymentFrequency', $monthArray,null, ['class'=>'form-control', 'id' => 'repaymentFrequency']) !!}
                                <p id='repaymentFrequencye' class="error"></p>
                            </div>
                    </div> --}}

                   {{--  <div class="form-group">
                            {!! Form::label('loanDuration', 'Loan Duration (Month):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('loanDuration', null, ['class'=>'form-control', 'id' => 'loanDuration']) !!}
                                <p id='loanDuratione' class="error"></p>                                
                            </div>
                    </div>  --}}

                   {{--  <div class="form-group">
                            {!! Form::label('gracePeriod', 'Grace Period (Month):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('gracePeriod', null, ['class'=>'form-control', 'id' => 'gracePeriod']) !!}
                                <p id='gracePeriode' class="error"></p>                                
                            </div>
                    </div>  --}} 

                   

                   {{--  <div class="form-group">
                            {!! Form::label('status', 'Status:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            @php
                                $statusArray = array(''=>'Select Status','1'=>'Activate','0'=>'Deactivate');
                             @endphp                                 
                                {!! Form::select('status', $statusArray,1, ['class'=>'form-control', 'id' => 'status']) !!}
                                <p id='statuse' class="error"></p>                                
                            </div>
                    </div>   --}}              

                </div> {{-- End of 2nd coloum --}}

               <p id="inappropriatee" class="error" style="font-size: 18px;"></p>

            
                
            </div>

        </div>
        
                        
                
                <br>  
                <br>  
                    
                    
                        
                        <div style="padding-left: 10px;padding-right: 10px;">                            
                            

                            <table id="scheduleTable" class="table table-striped table-bordered" style="color: black !important;">

                                <thead>
                               <tr>
                                   <th rowspan="2">Inst. No</th>
                                   <th colspan="4">Schedule</th>
                                   <th colspan="4">Payment</th>
                                   <th colspan="3">Due Amount</th>
                                   <th rowspan="2">Status</th>
                               </tr>
                               <tr>
                                   <th>Inst. Date</th>
                                   <th>Principal</th>
                                   <th>Interest</th>
                                   <th>Total</th>

                                   <th>Date</th>
                                   <th>Principal</th>
                                   <th>Interest</th>
                                   <th>Total</th>

                                   <th>Principal</th>
                                   <th>Interest</th>
                                   <th>Total</th>


                                  
                               </tr>
                           </thead>
                           <tbody>
                               
                           </tbody>

                           <tfoot>
                               
                           </tfoot>
                                
                                
                            </table>
                                
                            </div>
                        
                        
                      
                 
                    <br><br>
                    <!-- Tabs Pager -->
                    
                    <div class="form-group">
                        <div class="col-sm-12 text-right" style="padding-right: 25px;">
                            {!! Form::button('Submit', ['id' => 'submitButton', 'class' => 'btn btn-info','type'=>'button']) !!}
                            <a href="{{url('viewLoanRegisterPayment/')}}" class="btn btn-danger closeBtn">Close</a>
                        </div>
                </div>
                    
                
                
            {!! Form::close() !!}
    {{-- ////////////////////////////// --}}



</div>


</div>
<div class="footerTitle" style="border-top:1px solid white"></div>
</div>
<div class="col-md-1"></div>
</div>


<style type="text/css">
    .error{
        color: red;
    }
</style>


<script type="text/javascript">
    $(document).ready(function() {


        function num(argument){
            return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function formateDate(argument){
            if (argument==null) {
                return "-";
            }
            else{
                var date = $.datepicker.parseDate('yy-mm-dd',argument);
            return $.datepicker.formatDate("dd-mm-yy", date);
            }
            
        }

         function pad (str, max) {
      str = str.toString();
      return str.length < max ? pad("0" + str, max) : str;
    }

    function emptyForm() {
        $("#numOfInstallment").val('');
        $("#loanAmount").val('');
        $("#principalAmount").val('');
        $("#interestAmount").val('');
        $("#scheduleTable tbody").empty();
    }

       /* function monthDiff(d1, d2) {
            var months;
            months = (d2.getFullYear() - d1.getFullYear()) * 12;
            months -= d1.getMonth() + 1;
            months += d2.getMonth();
            return months <= 0 ? 0 : parseInt(months+1);
        }*/




        $("#principalAmount,#interestAmount,#rebateAmount").on('input', function() {            
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });

        $("#principalAmount").on('input', function() { 
            var initialValue = parseFloat($(this).attr('amount'));
            var inputValue = 0;
            if (this.value!='') {
                inputValue = parseFloat(this.value);
            }

            if (inputValue>initialValue) {
                var errorMessage = "* It should be less than or equal to "+initialValue;
                $("#principalAmountValuee").empty();
                $("#principalAmountValuee").show();
                $("#principalAmountValuee").append(errorMessage);
                $("#principalAmountValuee").attr('hasError','1');
            }
            else{
                $("#principalAmountValuee").empty();
                $("#principalAmountValuee").hide();
                $("#principalAmountValuee").attr('hasError','0');
            }
             
        });
         $("#interestAmount").on('input', function() { 
            var initialValue = parseFloat($(this).attr('amount'));
            var inputValue = 0;
            if (this.value!='') {
                inputValue = parseFloat(this.value);
            }

            if (inputValue>initialValue) {
                var errorMessage = "* It should be less than or equal to "+initialValue;
                $("#interestAmountValuee").empty();
                $("#interestAmountValuee").show();
                $("#interestAmountValuee").append(errorMessage);
                $("#interestAmountValuee").attr('hasError','1');                
                
            }
            else{
                $("#interestAmountValuee").empty();
                $("#interestAmountValuee").hide();
                $("#interestAmountValuee").attr('hasError','0');
            }
             
        });
       




         /*On input/change Hide the Errors*/
         $(document).on('input', 'input', function() {
             $(this).closest('div').find('.error').empty();
         });
          $(document).on('change', 'select', function() {
             $(this).closest('div').find('.error').empty();
         });
         /*End On input/change Hide the Errors*/





         /*On change Project Type*/
     $("#projectType").change(function() {
         var projectTypeId = $(this).val();
         var csrf = "{{csrf_token()}}";

         emptyForm();

         $.ajax({
             url: './loanRegisterFilterOnChangeProjectType',
             type: 'POST',
             dataType: 'json',
             data: {projectTypeId: projectTypeId, _token: csrf},
         })
         .done(function(data) {


            $("#donor").empty();
            $("#donor").append("<option value=''>Select Donor</option>");
            $.each(data['donors'], function(index, donor) {
                 $("#donor").append("<option value='"+donor.id+"'>"+donor.name+"</option>");
            });

            $("#branch").empty();
            $("#branch").append("<option value=''>Select Branch</option>");
            $.each(data['branches'], function(index, branch) {
                 $("#branch").append("<option value='"+branch.id+"'>"+branch.name+'-'+branch.shortName+"</option>");
            });

            $("#loanProduct").empty();
            $("#loanProduct").append("<option value=''>Select Loan Product</option>");
            $.each(data['loanProducts'], function(index, loanProduct) {
                 $("#loanProduct").append("<option value='"+loanProduct.id+"'>"+loanProduct.name+"</option>");
            });

            $("#loanProduct").val('').trigger('change');

             console.log("success");
         })
         .fail(function() {
             console.log("error");
         })
         .always(function() {
             console.log("complete");
         });
         
     });
     /*End On change Project Type*/
         



         /*Filter Branch Location and loan product On selecting Bank*/
         $("#donor").on('change', function() {
             var donor = $("#donor option:selected").val();
             var csrf = "{{csrf_token()}}";

             emptyForm();

             $("#loanProduct").val('').trigger('change');

             $.ajax({
                 url: './accFdrGetBranchLocationBaseOnBank',
                 type: 'POST',
                 dataType: 'json',
                 data: {bank: donor, _token: csrf},
             })
             .done(function(branch) {

                var activeBranches = "{{json_encode($activeBranches)}}";
                
                
                $("#branch").empty();
                $("#branch").append("<option value=''>Select Branch</option>");
                $.each(branch, function(index, branch) {
                    
                    if (activeBranches.includes(branch.id)) { 
                        $("#branch").append("<option value='"+branch.id+"'>"+branch.name+'-'+branch.bankName+"</option>");
                    }
                     
                });
               

                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });



             $.ajax({
                 url: './getLoanProductBaseOnDonor',
                 type: 'POST',
                 dataType: 'json',
                 data: {donor: donor, _token: csrf},
             })
             .done(function(loanProduct) {
                $("#loanProduct").empty();
                $("#loanProduct").append("<option value=''>Select Loan Product</option>");

                var activeLoanProduts = "{{json_encode($activeLoanProduts)}}";
                
                
                $.each(loanProduct, function(index, product) {
                    
                    if (activeLoanProduts.includes(product.id)) {
                       $("#loanProduct").append("<option value='"+product.id+"'>"+product.name+"</option>"); 
                    }
                     
                });

                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });


             /*Get Donar Type*/
             $.ajax({
                 url: './gnrGetBankInfo',
                 type: 'POST',
                 dataType: 'json',
                 data: {bankId: donor, _token: csrf},
             })
             .done(function(bank) {
                $("#donorType").val(bank.isDonor);
                if (bank.isDonor==0) {
                    
                    $("#phase").val('');
                    $("#cycle").val('');

                   /* $("#accNo").prop('disabled',false);
                    $("#phase").prop('disabled',true);
                    $("#cycle").prop('disabled',true);*/
                }
                else{
                    /*$("#accNo").val('');
                    $("#accNo").prop('disabled',true);
                    $("#phase").prop('disabled',false);
                    $("#cycle").prop('disabled',false);*/                 
                }

                
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });
             
             /*End Get Donar Type*/
             
             
         });
         /*End Filter Branch Location and loan product On selecting Bank*/


         /*On change Branch get the loan prosucts*/
         $("#branch").change(function() {
             var branchId = $(this).val();
             var csrf = "{{csrf_token()}}";

             emptyForm();

             $("#loanProduct").val('').trigger('change');

             if (branchId=='' || branchId==null) {
                $("#donor").trigger('change');
             }
             else{

                $.ajax({
                 url: './getLoanProductsBaseOnBranch',
                 type: 'POST',
                 dataType: 'json',
                 data: {branchId: branchId, _token: csrf},
             })
             .done(function(loanProducts) {

                $("#loanProduct").empty();
                $("#loanProduct").append("<option value=''>Select Loan Product</option>");
                $.each(loanProducts, function(index, loanProduct) {
                     $("#loanProduct").append("<option value='"+loanProduct.id+"'>"+loanProduct.name+"</option>");
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

             
         });
         /*End On change Branch get the loan prosucts*/




     /*On change Loan Product*/
     $("#loanProduct").on('change',function(){
        var loanProductId = $(this).val();
        var csrf = "{{csrf_token()}}";

        $("#scheduleTable tbody").empty();

        if (loanProductId=='' || loanProductId==null) {
             $("#accNo").val('');
            $("#phase").val('');
            $("#cycle").val('');

             $("#accNo").prop('disabled',true);
            $("#phase").prop('disabled',true);
            $("#cycle").prop('disabled',true);
        }
        else{

             $.ajax({
        url: './loanRegisterOnChangeLoanProduct',
        type: 'POST',
        dataType: 'json',
        data: {loanProductId: loanProductId, _token: csrf},
        })
        .done(function(data) {

            $("#donorType").val(data['isDonor']);

            if (data['isDonor']==0) {

                 $("#phase").val('');
                $("#cycle").val('');

                $("#accNo").prop('disabled',false);
                $("#phase").prop('disabled',true);
                $("#cycle").prop('disabled',true);


                $("#accNo").empty();
                $("#accNo").append("<option value=''>Select Account</option>");

                $.each(data['accounts'], function(index, account) {
                     $("#accNo").append("<option value='"+account.id+"'>"+account.accNo+"</option>");
                });
            }

            else{
                $("#accNo").val('');
                $("#accNo").prop('disabled',true);
                $("#phase").prop('disabled',false);
                

                $("#phase").empty();
                $("#phase").append("<option value=''>Select Phase</option>");

                $.each(data['phases'], function(index, phase) {
                     $("#phase").append("<option value='"+phase.phase+"'>"+phase.phase+"</option>");
                });

            }

           

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
     /*End On change Loan Product*/



     /*On change phase*/
     $("#phase").change(function() {
         var loanProductId = $("#loanProduct option:selected").val();
         var phase = $(this).val();
         var csrf = "{{csrf_token()}}";

         if (phase=='' || phase==null) {
            $("#cycle").val('');
            $("#cycle").prop('disabled',true);
            emptyForm();
         }
         else{
            $.ajax({
             url: './getCyclesBaseOnPhaseNLoanProduct',
             type: 'POST',
             dataType: 'json',
             data: {loanProductId: loanProductId, phase: phase, _token: csrf},
         })
         .done(function(cycles) {
            $("#cycle").empty();
            $("#cycle").append("<option value=''>Select Cycle</option>");

            $.each(cycles, function(index, cycle) {
                 $("#cycle").append("<option value='"+cycle.cycle+"'>"+cycle.cycle+"</option>");
            });


            $("#cycle").prop('disabled',false);

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
     /*End On change phase*/


     /*On change Account*/
     $("#accNo").change(function() {
         var key = "accNo";
         var loanAccId = $("#accNo option:selected").val();
         var csrf = "{{csrf_token()}}";
         

         if (loanAccId=='') {
            emptyForm();
         }

         else{


             $.ajax({
             url: './getLoanAccountNpaymentInfo',
             type: 'POST',
             dataType: 'json',
             data: {key: key, loanAccId: loanAccId, _token: csrf},
         })
         .done(function(data) {
            $("#numOfInstallment").val(data['lastUnpaidInstallmentId']);
            $("#loanAmount").val(num(data['account'].loanAmount));
            $("#principalAmount").val(data['principalAmount']);
            $("#principalAmount").attr('amount',data['principalAmount']);
            $("#interestAmount").val(data['interestAmount']);
            $("#interestAmount").attr('amount',data['interestAmount']);


            $("#paymentDate").datepicker("option","minDate",new Date(data['lastPaymentDate']));
            
             /*Make Schedule Table*/
            $("#scheduleTable tbody").empty();

            var tTotalSchedulePricipal = 0;
            var tTotalScheduleInterest = 0;
            var tTotalSchedule = 0;
            var tTotalPaymentPricipal = 0;
            var tTotalPaymentInterest = 0;
            var tTotalPayment = 0;
            var tTotalDuePrincipal = 0;
            var tTotalDueInterest = 0;
            var tTotalDue = 0;


            $.each(data['schedules'], function(index, schedule) {

                 markup = "<tr><td>"+schedule.scheduleNumber+"</td><td>"+formateDate(schedule.paymentDate)+"</td><td>"+num(schedule.principalAmount)+"</td><td>"+num(schedule.interestAmount)+"</td><td>"+num(schedule.totalAmount)+"</td><td>"+formateDate(data['paymentDate'][index])+"</td><td>"+num(data['principalPaymentAmount'][index])+"</td><td>"+num(data['interestPaymentAmount'][index])+"</td><td>"+num(data['totalPaidAmount'][index])+"</td><td>"+num(data['principalDueAmount'][index])+"</td><td>"+num(data['interestDueAmount'][index])+"</td><td>"+num(data['totalDueAmount'][index])+"</td><td>"+schedule.paymentStatus+"</td></tr>";
                 $("#scheduleTable tbody").append(markup);

                tTotalSchedulePricipal = tTotalSchedulePricipal + schedule.principalAmount;
                tTotalScheduleInterest = tTotalScheduleInterest + schedule.interestAmount;
                tTotalSchedule = tTotalSchedule + schedule.totalAmount;
                tTotalPaymentPricipal = tTotalPaymentPricipal + data['principalPaymentAmount'][index];
                tTotalPaymentInterest = tTotalPaymentInterest + data['interestPaymentAmount'][index];
                tTotalPayment = tTotalPayment + data['totalPaidAmount'][index];
                tTotalDuePrincipal = tTotalDuePrincipal + data['principalDueAmount'][index];
                tTotalDueInterest = tTotalDueInterest + data['interestDueAmount'][index];
                tTotalDue = tTotalDue + data['totalDueAmount'][index];
            });

            totalRowMarkup = "<tr><td colspan='2'>Total</td><td>"+num(tTotalSchedulePricipal)+"</td><td>"+num(tTotalScheduleInterest)+"</td><td>"+num(tTotalSchedule)+"</td><td></td><td>"+num(tTotalPaymentPricipal)+"</td><td>"+num(tTotalPaymentInterest)+"</td><td>"+num(tTotalPayment)+"</td><td>"+num(tTotalDuePrincipal)+"</td><td>"+num(tTotalDueInterest)+"</td><td>"+num(tTotalDue)+"</td><td></td></tr>";
            $("#scheduleTable tfoot").append(totalRowMarkup);
            /*End Make Schedule Table*/

            if ($("#isRebate").val()==1) {
                    $("#isRebate").trigger('change');            
                 }




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


     /*On change Cycle*/
     $("#cycle").change(function() {
        var key = "cycle";
         var loanProductId = $("#loanProduct").val();
         var phase = $("#phase").val();
         var cycle = $(this).val();
         var csrf = "{{csrf_token()}}";

         if (cycle=='') {
            emptyForm();
         }

         else {
             $.ajax({
             url: './getLoanAccountNpaymentInfo',
             type: 'POST',
             dataType: 'json',
             data: {key: key, loanProductId: loanProductId, phase: phase, cycle: cycle, _token: csrf},
             })
             .done(function(data) {

            $("#numOfInstallment").val(data['lastUnpaidInstallmentId']);
            $("#loanAmount").val(num(data['account'].loanAmount));
            $("#principalAmount").val(data['principalAmount']);
            $("#principalAmount").attr('amount',data['principalAmount']);
            $("#interestAmount").val(data['interestAmount']);
            $("#interestAmount").attr('amount',data['interestAmount']);

            $("#paymentDate").datepicker("option","minDate",new Date(data['lastPaymentDate']));
            
             /*Make Schedule Table*/
            $("#scheduleTable tbody").empty();
            $("#scheduleTable tfoot").empty();

            var tTotalSchedulePricipal = 0;
            var tTotalScheduleInterest = 0;
            var tTotalSchedule = 0;
            var tTotalPaymentPricipal = 0;
            var tTotalPaymentInterest = 0;
            var tTotalPayment = 0;
            var tTotalDuePrincipal = 0;
            var tTotalDueInterest = 0;
            var tTotalDue = 0;


            $.each(data['schedules'], function(index, schedule) {
               

                markup = "<tr><td>"+schedule.scheduleNumber+"</td><td>"+formateDate(schedule.paymentDate)+"</td><td>"+num(schedule.principalAmount)+"</td><td>"+num(schedule.interestAmount)+"</td><td>"+num(schedule.totalAmount)+"</td><td>"+formateDate(data['paymentDate'][index])+"</td><td>"+num(data['principalPaymentAmount'][index])+"</td><td>"+num(data['interestPaymentAmount'][index])+"</td><td>"+num(data['totalPaidAmount'][index])+"</td><td>"+num(data['principalDueAmount'][index])+"</td><td>"+num(data['interestDueAmount'][index])+"</td><td>"+num(data['totalDueAmount'][index])+"</td><td>"+schedule.paymentStatus+"</td></tr>";
                 $("#scheduleTable tbody").append(markup);


                 tTotalSchedulePricipal = tTotalSchedulePricipal + schedule.principalAmount;
                tTotalScheduleInterest = tTotalScheduleInterest + schedule.interestAmount;
                tTotalSchedule = tTotalSchedule + schedule.totalAmount;
                tTotalPaymentPricipal = tTotalPaymentPricipal + data['principalPaymentAmount'][index];
                tTotalPaymentInterest = tTotalPaymentInterest + data['interestPaymentAmount'][index];
                tTotalPayment = tTotalPayment + data['totalPaidAmount'][index];
                tTotalDuePrincipal = tTotalDuePrincipal + data['principalDueAmount'][index];
                tTotalDueInterest = tTotalDueInterest + data['interestDueAmount'][index];
                tTotalDue = tTotalDue + data['totalDueAmount'][index];


            });

            totalRowMarkup = "<tr><td colspan='2'>Total</td><td>"+num(tTotalSchedulePricipal)+"</td><td>"+num(tTotalScheduleInterest)+"</td><td>"+num(tTotalSchedule)+"</td><td></td><td>"+num(tTotalPaymentPricipal)+"</td><td>"+num(tTotalPaymentInterest)+"</td><td>"+num(tTotalPayment)+"</td><td>"+num(tTotalDuePrincipal)+"</td><td>"+num(tTotalDueInterest)+"</td><td>"+num(tTotalDue)+"</td><td></td></tr>";
            $("#scheduleTable tfoot").append(totalRowMarkup);
            /*End Make Schedule Table*/

            if ($("#isRebate").val()==1) {
                    $("#isRebate").trigger('change');            
                 }
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
     /*End On change Cycle*/



     /*Rebate*/
     $("#isRebate").change(function() {
         var isRebate = $(this).val();

         $("#principalAmount").prop('readonly',false);
         $("#interestAmount").prop('readonly',false);


         if (isRebate==0) {
            $("#rebateAmountDiv").hide();
            if ($("#accNo").val()!='' && $("#accNo").val()!=null) {
                
                $("#accNo").trigger('change');
            }
            else if($("#cycle").val()!='' && $("#cycle").val()!=null){
                
                $("#cycle").trigger('change');
            }
         }


         else{

            var isDonor = $("#donorType").val();
            var loanProductId = $("#loanProduct").val();
            var accNo = $("#accNo").val();
            var phase = $("#phase").val();
            var cycle = $("#cycle").val();
            var csrf = "{{csrf_token()}}";

            

            if (isDonor!='' && isDonor!=null && (accNo!='' || cycle!='')) {

                $.ajax({
                url: './loanRegisterGetRebateData',
                type: 'POST',
                dataType: 'json',
                data: {isDonor: isDonor, loanProductId: loanProductId, accNo: accNo, phase: phase, cycle: cycle, _token: csrf},
                })
                .done(function(data) {
                    $("#principalAmount").val(data['unpaidPrincipalAmount']);
                    $("#principalAmount").attr('amount',data['unpaidPrincipalAmount']);
                    $("#interestAmount").val(data['unpaidInterestAmount']);
                    $("#interestAmount").attr('amount',data['unpaidInterestAmount']);

                    $("#numOfInstallment").val(data['installments']);

                    $("#principalAmount").prop('readonly',true);
                    $("#interestAmount").prop('readonly',true);

                    console.log("success");
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });

            }

            
           
            $("#rebateAmountDiv").show();
         }
     });
     /*End Rebate*/


     /*Enable/Disable submit buton*/

     /*End Enable/Disable submit buton*/


     /*submit the data*/
     $("#submitButton").click(function() {



        /*Empty all errors*/
        $(".error").empty();
        /*End Empty all errors*/



        var isRebate = $("#isRebate").val();
        var isDonor = $("#donorType").val();        

        var loanProductId = $("#loanProduct").val();
        var loanAccId = $("#accNo").val();
        var phase = $("#phase").val();
        var cycle = $("#cycle").val();
        var installmentNo = $("#numOfInstallment").val();
        var principalAmount = $("#principalAmount").val();
        var duePrincipalAmount = $("#principalAmount").attr('amount') - principalAmount;
        var interestAmount = $("#interestAmount").val();
        var dueInterestAmount = $("#interestAmount").attr('amount') - interestAmount;
        var rebateAmount = $("#rebateAmount").val();
        var paymentDate = $("#paymentDate").val();

        var principalAmountValueError = parseInt($("#principalAmountValuee").attr('hasError'));
        var interestAmountValueError = parseInt($("#interestAmountValuee").attr('hasError'));

        var csrf = "{{csrf_token()}}";

      

        if (principalAmountValueError!=1 && interestAmountValueError!=1) {

            
            
            $.ajax({
                url: './storeLoanRegisterPayment',
                type: 'POST',
                dataType: 'json',
                data: {isRebate: isRebate, isDonor: isDonor, loanProductId: loanProductId, loanAccId: loanAccId, phase: phase, cycle: cycle, installmentNo: installmentNo, principalAmount: principalAmount,duePrincipalAmount: duePrincipalAmount, interestAmount: interestAmount,dueInterestAmount: dueInterestAmount, rebateAmount: rebateAmount,paymentDate: paymentDate,_token: csrf},
            })
            .done(function(data) {
                if (data.accessDenied) {
                    showAccessDeniedMessage();
                    return false;
                }
                if (data.errors) {
                    if (data.errors['loanProductId']) {
                        $("#loanProducte").empty();
                        $("#loanProducte").append('* '+data.errors['loanProductId']);
                    }
                    if (data.errors['principalAmount']) {
                        $("#principalAmounte").empty();
                        $("#principalAmounte").append('* '+data.errors['principalAmount']);
                    }
                    if (data.errors['interestAmount']) {
                        $("#interestAmounte").empty();
                        $("#interestAmounte").append('* '+data.errors['interestAmount']);
                    }
                    if (data.errors['paymentDate']) {
                        $("#paymentDatee").empty();
                        $("#paymentDatee").append('* '+data.errors['paymentDate']);
                    }
                    if (data.errors['loanAccId']) {
                        $("#accNoe").empty();
                        $("#accNoe").append('* '+data.errors['loanAccId']);
                    }
                    if (data.errors['phase']) {
                        $("#phasee").empty();
                        $("#phasee").append('* '+data.errors['phase']);
                    }
                    if (data.errors['cycle']) {
                        $("#cyclee").empty();
                        $("#cyclee").append('* '+data.errors['cycle']);
                    }
                    if (data.errors['rebateAmount']) {
                        $("#rebateAmounte").empty();
                        $("#rebateAmounte").append('* '+data.errors['rebateAmount']);
                    }
                }

                else{
                    location.href = "viewLoanRegisterPayment";
                }
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
     /*End submit the data*/


     /*Payment Date*/
         $("#paymentDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                $("#paymentDatee").empty();
            }
        });
         /*End Payment Date*/
     


        

    });/*End Ready*/
</script>


<style type="text/css">
    .tInterestAmount,.tPrincipalAmount{
        text-align: center;
    }
    #scheduleTable thead tr th{
        padding: 3px; 
    }
    #interestAmountValuee,#principalAmountValuee{
        color: red;
    }
    #scheduleTable tfoot tr td{
        font-weight: bold;
        padding: 4px;
        text-align: right;
        padding-right: 5px;
    }
    #scheduleTable tfoot tr td{
        font-weight: bold;
        padding: 4px;
    }
    #scheduleTable tfoot tr td:nth-child(1){
        text-align: center;
        padding-right: 0px;
    }
    #scheduleTable tbody tr td{
        text-align: right;
        padding-right: 5px;
    }
    #scheduleTable tbody tr td:nth-child(1),
    #scheduleTable tbody tr td:nth-child(2),
    #scheduleTable tbody tr td:nth-child(6),
    #scheduleTable tbody tr td:nth-child(13)
    {
        text-align: center;
        padding-right: 0px;
    }
</style>


@endsection
