@extends('layouts/acc_layout')
@section('title', '| Loan Register')
@section('content')

<div class="row add-data-form">
<div class="col-md-1"></div>
<div class="col-md-10 fullbody">
<div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="{{url('viewLoanRegisterAccount/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>Loan Account List</a>
</div>

<div class="panel panel-default panel-border">
	<div class="panel-heading">
	    <div class="panel-title">Loan Register</div>
	</div>




    {{-- ////////////////////////////// --}}

    <div class="panel-body">
    {!! Form::open(['url'=>'','class'=>'form-horizontal form-groups']) !!}
            <div class="row">                
                <div class="col-md-12">
    
                     
               
                
                    <div class="col-md-6">
                    

                    <div class="form-group">
                        {!! Form::label('donor', 'Donor:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                          $donorList = array(''=>'Select Donor') + DB::table('gnr_bank')->pluck('name','id')->toArray();
                        @endphp                                  
                            {!! Form::select('donor',$donorList ,null, ['class'=>'form-control', 'id' => 'donor']) !!}
                            <p id='donore' class="error"></p>
                        </div>
                    </div>

                    {!! Form::hidden('donorType',null,['id'=>'donorType']) !!}

                    <div class="form-group">
                        {!! Form::label('loanProduct', 'Loan Product:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                          $loanProductList = array(''=>'Select Loan Product') + DB::table('gnr_loan_product')->pluck('name','id')->toArray();
                        @endphp                                  
                            {!! Form::select('loanProduct',$loanProductList ,null, ['class'=>'form-control', 'id' => 'loanProduct']) !!}
                            <p id='loanProducte' class="error"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                          /*$donorBranchList = array(''=>'Select Branch') + DB::table('gnr_bank_branch')->pluck('name','id')->toArray();*/
                          $donorBranchList = DB::table('gnr_bank_branch')->select('name','id','bankId_fk')->get();
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
                    </div>

                    <div class="form-group">
                        {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        @php
                          $projectTypes = DB::table('gnr_project_type')->select('id','name','projectTypeCode')->get();
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
                            {!! Form::label('accNo', 'Account No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('accNo', null, ['class'=>'form-control', 'id' => 'accNo']) !!}
                                <p id='accNoe' class="error"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('agreementDate', 'Agreement/Sanction Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('agreementDate', null, ['class'=>'form-control', 'id' => 'agreementDate','readonly','style'=>'cursor:pointer']) !!}
                                <p id='agreementDatee' class="error"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('loanSanctionNumber', 'Loan Sanction Number:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('loanSanctionNumber', null, ['class'=>'form-control', 'id' => 'loanSanctionNumber']) !!}
                                <p id='loanSanctionNumbere' class="error"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('loanDate', 'Loan Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('loanDate', null, ['class'=>'form-control', 'id' => 'loanDate','readonly','style'=>'cursor:pointer']) !!}
                                <p id='loanDatee' class="error"></p>
                            </div>
                    </div>
                    

                   
                </div> {{-- End of 1st coloum --}}

                <div class="col-md-6">


               <div class="form-group">
                            {!! Form::label('interestRate', 'Interest Rate:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('interestRate', null, ['class'=>'form-control', 'id' => 'interestRate']) !!}
                                <p id='interestRatee' class="error"></p>
                            </div>
                    </div>




                    <div class="form-group">
                            {!! Form::label('loanAmount', 'Loan Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('loanAmount', null, ['class'=>'form-control', 'id' => 'loanAmount']) !!}
                                <p id='loanAmounte' class="error"></p>
                            </div>
                    </div>

                     <div class="form-group">
                            {!! Form::label('phase', 'Phase:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('phase', null, ['class'=>'form-control', 'id' => 'phase']) !!}
                                <p id='phasee' class="error"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('cycle', 'Cycle:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('cycle', null, ['class'=>'form-control', 'id' => 'cycle']) !!}
                                <p id='cyclee' class="error"></p>
                            </div>
                    </div>

                   
                   

                    <div class="form-group">
                            {!! Form::label('repaymentFrequency', 'Repayment Frequency:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8"> 
                            @php
                               $monthArray = array(''=>'Select Duration','1'=>'1 month','2'=>'2 months','3'=>'3 months','4'=>'4 months','5'=>'5 months','6'=>'6 months','7'=>'7 months','8'=>'8 months','9'=>'9 months','10'=>'10 months','11'=>'11 months','12'=>'12 months');
                           @endphp    

                                {!! Form::select('repaymentFrequency', $monthArray,null, ['class'=>'form-control', 'id' => 'repaymentFrequency']) !!}
                                <p id='repaymentFrequencye' class="error"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('loanDuration', 'Loan Duration (Month):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('loanDuration', null, ['class'=>'form-control', 'id' => 'loanDuration']) !!}
                                <p id='loanDuratione' class="error"></p>                                
                            </div>
                    </div> 

                    <div class="form-group">
                            {!! Form::label('gracePeriod', 'Grace Period (Month):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('gracePeriod', null, ['class'=>'form-control', 'id' => 'gracePeriod']) !!}
                                <p id='gracePeriode' class="error"></p>                                
                            </div>
                    </div>  

                    <div class="form-group">
                            {!! Form::label('numOfInstallment', 'Number Of Installment:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('numOfInstallment', null, ['class'=>'form-control', 'id' => 'numOfInstallment']) !!}
                                <p id='numOfInstallmente' class="error"></p>                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('status', 'Status:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            @php
                                $statusArray = array(''=>'Select Status','1'=>'Activate','0'=>'Deactivate');
                             @endphp                                 
                                {!! Form::select('status', $statusArray,1, ['class'=>'form-control', 'id' => 'status']) !!}
                                <p id='statuse' class="error"></p>                                
                            </div>
                    </div>                

                </div> {{-- End of 2nd coloum --}}

               <p id="inappropriatee" class="error" style="font-size: 18px;"></p>

            
                
            </div>

        </div>
        
                        
                 <br>

                 <div class="form-group">
                        <div class="col-sm-12 text-right" style="padding-right: 30px;">
                            {!! Form::button('Create Schedule', ['id' => 'createSchedule', 'class' => 'btn btn-info','type'=>'button']) !!}
                        </div>
                </div> 

                <br>  
                <br>  
                    
                    
                        
                        <div style="padding-left: 10px;padding-right: 10px;">                            
                            

                            <table id="scheduleTable" class="table table-striped table-bordered" style="color: black !important;">

                                <thead>
                                    <tr>
                                        <th>Installment No</th>
                                        <th>Installment Date</th>
                                        <th>Principal Amount (Tk)</th>
                                        <th>Interest Amount (Tk)</th>
                                        <th>Total Amount (Tk)</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    
                                </tbody>
                                
                                
                            </table>
                                
                            </div>
                        
                        
                      
                 
                    <br><br>
                    <!-- Tabs Pager -->
                    
                    <div class="form-group">
                        <div class="col-sm-12 text-right" style="padding-right: 25px;">
                            {!! Form::button('Submit', ['id' => 'submitButton', 'class' => 'btn btn-info','type'=>'button','disabled']) !!}
                            <a href="{{url('viewLoanRegisterAccount/')}}" class="btn btn-danger closeBtn">Close</a>
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

       /* function monthDiff(d1, d2) {
            var months;
            months = (d2.getFullYear() - d1.getFullYear()) * 12;
            months -= d1.getMonth() + 1;
            months += d2.getMonth();
            return months <= 0 ? 0 : parseInt(months+1);
        }*/




        $("#loanAmount,#interestRate").on('input', function() {            
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        $("#cycle,#phase,#loanDuration,#gracePeriod,#numOfInstallment").on('input', function() {            
            this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
        });



         $("#loanDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                
                $("#loanDatee").empty();
            }
        });

         $("#agreementDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                $("#agreementDatee").empty();
            }
        });

         

         /*Create Schedule Button*/
         $('#createSchedule').on('click',function(){
            

             $.ajax({
                 url: './gnrLoanRegisterAccountValidateFirstStep',
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

                    $("#submitButton").prop('disabled',true);

                    $("#scheduleTable tbody").empty();

                    if (data.errors['donor']) {
                        $("#donore").empty();
                        $("#donore").append('* '+data.errors['donor']);
                    }
                    if (data.errors['loanProduct']) {
                        $("#loanProducte").empty();
                        $("#loanProducte").append('* '+data.errors['loanProduct']);
                    }
                    if (data.errors['branch']) {
                        $("#branche").empty();
                        $("#branche").append('* '+data.errors['branch']);
                    }
                    if (data.errors['project']) {
                        $("#projecte").empty();
                        $("#projecte").append('* '+data.errors['project']);
                    }
                    if (data.errors['projectType']) {
                        $("#projectTypee").empty();
                        $("#projectTypee").append('* '+data.errors['projectType']);
                    }
                    if (data.errors['accNo']) {
                        $("#accNoe").empty();
                        $("#accNoe").append('* '+data.errors['accNo']);
                    }
                    if (data.errors['agreementDate']) {
                        $("#agreementDatee").empty();
                        $("#agreementDatee").append('* '+data.errors['agreementDate']);
                    }
                    if (data.errors['loanSanctionNumber']) {
                        $("#loanSanctionNumbere").empty();
                        $("#loanSanctionNumbere").append('* '+data.errors['loanSanctionNumber']);
                    }
                    if (data.errors['loanDate']) {
                        $("#loanDatee").empty();
                        $("#loanDatee").append('* '+data.errors['loanDate']);
                    }
                    if (data.errors['interestRate']) {
                        $("#interestRatee").empty();
                        $("#interestRatee").append('* '+data.errors['interestRate']);
                    }
                    if (data.errors['loanAmount']) {
                        $("#loanAmounte").empty();
                        $("#loanAmounte").append('* '+data.errors['loanAmount']);
                    }

                    if (data.errors['cycle']) {
                        $("#cyclee").empty();
                        $("#cyclee").append('* '+data.errors['cycle']);
                    }

                    if (data.errors['phase']) {
                        $("#phasee").empty();
                        $("#phasee").append('* '+data.errors['phase']);
                    }
                    
                   
                    if (data.errors['repaymentFrequency']) {
                        $("#repaymentFrequencye").empty();
                        $("#repaymentFrequencye").append('* '+data.errors['repaymentFrequency']);
                    }
                    if (data.errors['loanDuration']) {
                        $("#loanDuratione").empty();
                        $("#loanDuratione").append('* '+data.errors['loanDuration']);
                    }
                    if (data.errors['gracePeriod']) {
                        $("#gracePeriode").empty();
                        $("#gracePeriode").append('* '+data.errors['gracePeriod']);
                    }
                    if (data.errors['numOfInstallment']) {
                        $("#numOfInstallmente").empty();
                        $("#numOfInstallmente").append('* '+data.errors['numOfInstallment']);
                    }
                    if (data.errors['status']) {
                        $("#statuse").empty();
                        $("#statuse").append('* '+data.errors['status']);
                    }

                    if (data.errors['inappropriate']) {
                        $("#inappropriatee").empty();
                        $("#inappropriatee").append('* '+data.errors['inappropriate']);
                    }

                 } /*end has Errors*/
                 else{

                 

                    $("#submitButton").prop('disabled',false);


                  /*Make the Table*/
                  $("#scheduleTable tbody").empty();
                  

                  var loanDate = $.datepicker.parseDate('dd-mm-yy',$("#loanDate").val());
                  var paymentDate = $.datepicker.parseDate('dd-mm-yy',$("#loanDate").val());
                  var previousPaymentDate = $.datepicker.parseDate('dd-mm-yy',$("#loanDate").val());
                  var gracePeriod = parseInt($("#gracePeriod").val());
                  var repaymentFrequency = parseInt($("#repaymentFrequency").val());
                  var numOfInstallment = parseInt($("#numOfInstallment").val());
                 
                  var loanAmount = 0;
                  var principalAmount = 0;
                  if ($("#loanAmount").val()!='') {
                    loanAmount = parseFloat($("#loanAmount").val());
                  }

                  
                  principalAmount = loanAmount/numOfInstallment;

                  var interestRate = 0;
                  if ($("#interestRate").val()!='') {
                    interestRate = parseFloat($("#interestRate").val());
                  }
                  
                  var interestAmount = 0;

                  var donorType = $("#donorType").val();

                   
                  var i = 1;
                  for(i=1;i<=numOfInstallment;i++){

                     if (i==1) {
                        paymentDate.setMonth(paymentDate.getMonth() + gracePeriod);
                        var dateText = $.datepicker.formatDate("dd-mm-yy", paymentDate);                        
                        interestAmount = loanAmount * interestRate * gracePeriod / (100*12);                        
                        }
                        else{
                            previousPaymentDate = new Date(paymentDate);//$.datepicker.parseDate(paymentDate);
                            paymentDate.setMonth(paymentDate.getMonth() + repaymentFrequency);
                            var dateText = $.datepicker.formatDate("dd-mm-yy", paymentDate);
                            interestAmount = loanAmount * interestRate * repaymentFrequency / (100*12);

                        }

                    loanAmount = loanAmount - principalAmount;

                    if (donorType==1) {                        
                    
                    markup = "<tr><td><input name='tInstallmentNumber[] value='"+i+"' style='display:none;'>"+i+"</td><td><input name='tPaymentDate[]' value='"+dateText+"' style='display:none;'>"+dateText+"</td><td><input name='tPrincipalAmount[]' value='"+(principalAmount)+"' style='display:none;'>"+num(principalAmount)+"</td><td><input name=tInterestAmount[] value='"+interestAmount+"' style='display:none;'>"+num(interestAmount)+"</td><td>"+num(principalAmount+interestAmount)+"</td></tr>";

                    }

                    else{
                        markup = "<tr><td><input name='tInstallmentNumber[] value='"+i+"' style='display:none;'>"+i+"</td><td><input name='tPaymentDate[]' class='tPaymentDate' value='"+dateText+"' style='cursor:pointer;text-align:center;' readonly></td><td><input name='tPrincipalAmount[]' class='tPrincipalAmount' value=''></td><td><input name=tInterestAmount[] class='tInterestAmount' value='' ></td><td class='tTotalAmount'></td></tr>";
                    }
                   
                    $("#scheduleTable tbody").append(markup); 

                    $(".tPrincipalAmount").on('input', function() {            
                        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

                        var tPrincipalAmount = 0;
                        var tInterestAmount = 0;
                        if ($(this).val()!='') {
                            tPrincipalAmount = parseFloat($(this).val());
                        }
                        if ($(this).closest('tr').find('.tInterestAmount').val()!='') {
                            tInterestAmount = parseFloat($(this).closest('tr').find('.tInterestAmount').val());
                        }

                        var tTotalAmount = tPrincipalAmount + tInterestAmount;

                        $(this).closest('tr').find('.tTotalAmount').html(num(tTotalAmount));
                    });


                    $(".tInterestAmount").on('input', function() {            
                        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

                        var tPrincipalAmount = 0;
                        var tInterestAmount = 0;
                        if ($(this).val()!='') {
                            tInterestAmount = parseFloat($(this).val());
                        }
                        if ($(this).closest('tr').find('.tPrincipalAmount').val()!='') {
                            tPrincipalAmount = parseFloat($(this).closest('tr').find('.tPrincipalAmount').val());
                        }

                        var tTotalAmount = tPrincipalAmount + tInterestAmount;

                        $(this).closest('tr').find('.tTotalAmount').html(num(tTotalAmount));
                    });


                                           
         
                     $(".tPaymentDate").datepicker({
                        changeMonth: true,
                        changeYear: true,
                        yearRange : "-20:+20",
                        /*maxDate: "dateToday",*/
                        dateFormat: 'dd-mm-yy',
                        onSelect: function() {
                            
                        }
                    });

                         



                    $("#submitButton").prop('disabled',false);


                    


                  }
                  /*End Make the Table*/                  
                    
                 }

                 
                   

             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });

             
                 
             
         });
         /*End Create Schedule Button*/

         


         /*On input/change Hide the Errors*/
         $(document).on('input', 'input', function() {
             $(this).closest('div').find('.error').empty();
         });
          $(document).on('change', 'select', function() {
             $(this).closest('div').find('.error').empty();
         });
         /*End On input/change Hide the Errors*/


         /*Filter Branch Location and loan product On selecting Bank*/
         $("#donor").on('change', function() {
             var donor = $("#donor option:selected").val();
             var csrf = "{{csrf_token()}}";

             $.ajax({
                 url: './accFdrGetBranchLocationBaseOnBank',
                 type: 'POST',
                 dataType: 'json',
                 data: {bank: donor, _token: csrf},
             })
             .done(function(branch) {

                if (branch.accessDenied) {
                    showAccessDeniedMessage();
                    return false;
                }
                
                $("#branch").empty();
                $("#branch").append("<option value=''>Select Branch</option>");
                $.each(branch, function(index, branch) {
                     $("#branch").append("<option value='"+branch.id+"'>"+branch.name+'-'+branch.bankName+"</option>");
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

                $.each(loanProduct, function(index, product) {
                     $("#loanProduct").append("<option value='"+product.id+"'>"+product.name+"</option>");
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
                if (bank.isDonor==1) {
                    $("#accNo").val('');
                    $("#phase").val('');
                    $("#cycle").val('');
                    $("#accNo").prop('readonly',true);
                    $("#phase").prop('readonly',false);
                    $("#cycle").prop('readonly',false);
                }
                else{
                    $("#accNo").val('');
                    $("#phase").val('');
                    $("#cycle").val('');
                    $("#accNo").prop('readonly',false);
                    $("#phase").prop('readonly',true);
                    $("#cycle").prop('readonly',true);
                 
                }
                console.log("success");
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


 function pad (str, max) {
      str = str.toString();
      return str.length < max ? pad("0" + str, max) : str;
    }

          /* Change Project*/
         $("#project").change(function(){
            
            var projectId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                success: function( data ){

                    $("#projectType").empty();
                    $("#projectType").prepend('<option selected="selected" value="">Select Project Type</option>');
                   

                    $.each(data['projectTypeList'], function (key, projectObj) {

                                
                            $('#projectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");                       
                    });

                   

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/
        });
         /*End Change Project*/




         /*Sunmit Button*/
         $("#submitButton").on('click',function(){
             $("#submitButton").prop('disabled',true);
          
            var flag = 1;

             $(".tPrincipalAmount").each(function() {
                if ($(this).val()=='') {
                    flag = 0;
                }
            });

            $(".tInterestAmount").each(function() {
                if ($(this).val()=='') {
                    flag = 0;
                }
            });

            if (flag==0) {
                alert("Please fill all the input fileds.");
            }
            else{

                

                $.ajax({
                url: './storeLoanRegisterAccount',
                type: 'POST',
                dataType: 'json',
                data: $('form').serialize(),
            })
            .done(function(data) {

                if (data.accessDenied) {
                    showAccessDeniedMessage();
                    return false;
                }
                
                location.href = "viewLoanRegisterAccount";
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
         /*End Sunmit Button*/



     $("#repaymentFrequency").change(function() {
         $("#inappropriatee").empty();
     });
     $("#loanDuration,#gracePeriod,#numOfInstallment").on('input',function(){
        $("#inappropriatee").empty();
     });


     

     


         


        

    });/*End Ready*/
</script>


<style type="text/css">
    .tInterestAmount,.tPrincipalAmount{
        text-align: center;
    }
</style>


@endsection
