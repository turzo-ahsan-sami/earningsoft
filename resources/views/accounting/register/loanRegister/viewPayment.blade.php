@extends('layouts/acc_layout')
@section('title', '| Loan Register')
@section('content')



<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addLoanRegisterPayment/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Payment</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PAYMENT LIST</font></h1>
        </div>
        
        <div class="panel-body panelBodyView">       

        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#otsTable").dataTable().yadcf([
    
            ]);*/
            $("#otsTable").dataTable({ 
                  
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });

              
       });
          
          </script>
        </div>
          <table class="table table-striped table-bordered" id="otsTable" style="color: black;">
                    <thead>
                      <tr>
                        <th width="30">SL#</th>
                        <th>Date</th>
                        <th>Loan Product</th>                        
                        <th>Principal Amount (Tk)</th>                        
                        <th>Interest Amount (Tk)</th>                        
                        <th>Installment No</th>                        
                        <th>Bank/Donor</th>                        
                        <th>Account No</th>                        
                        <th>Phase</th>                        
                        <th>Cycle</th>                        
                        <th>Action</th>                        
                       
                      </tr>
                      
                    </thead>
                    <tbody>
                     @foreach($payments as $index => $payment)
                     @php
                      $account = DB::table('acc_loan_register_account')->where('id',$payment->accId_fk)->first();
                       $loanProduct = DB::table('gnr_loan_product')->where('id',$account->loanProductId_fk)->value('name');
                       $bankName = DB::table('gnr_bank')->where('id',$account->bankId_fk)->value('name');
                     @endphp
                        <tr>
                            <td>{{$index+1}}</td>

                            <td>{{date('d-m-Y',strtotime($payment->paymentDate))}}</td>  
                            <td>{{$loanProduct}}</td>
                            <td class="amount">{{number_format($payment->principalAmount,2,'.',',')}}</td>
                            <td class="amount">{{number_format($payment->interestAmount,2,'.',',')}}</td>
                            <td>{{$payment->installmentNo}}</td>
                            <td class="name">{{$bankName}}</td>
                            <td>{{$account->accNo}}</td>
                            @if($account->phase>0 && $account->cycle>0)
                            <td>{{$account->phase}}</td>
                            <td>{{$account->cycle}}</td>
                            @else
                            <td></td>
                            <td></td>
                            @endif
                        
                            
                           
                            <td width="80">

                            <a href="javascript:;" class="view-modal" paymentId="{{$payment->id}}">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                              </a>&nbsp; 

                            @php
                              $lastPayment = DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->max('paymentNo');
                            @endphp


                              <a href="javascript:;" class="edit-modal @if($payment->paymentNo!=$lastPayment){{"disabled"}}@endif" paymentId="{{$payment->id}}">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>&nbsp;

                            
                            <a href="javascript:;" class="delete-modal @if($payment->paymentNo!=$lastPayment){{"disabled"}}@endif" paymentId="{{$payment->id}}"  >
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                            
                          </td>
                        </tr>
                     @endforeach
                </tbody>
          </table>
        </div>
      </div>
  </div>
  </div>
</div>
</div>


{{-- View Modal --}}
<div id="viewModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Payment Info</h4>
            </div>
            <div class="modal-body">

                <div class="row" style="padding-bottom: 20px;">

                <div class="form-horizontal form-groups">
                    <div class="col-md-12"> 
                    <div class="col-md-6">


                    <div class="form-group">
                        {!! Form::label('paymentId', 'Payment ID:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">          
                            {!! Form::text('paymentId',null, ['class'=>'form-control', 'id' => 'VMpaymentId','readonly']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('donor', 'Donor:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                                                        
                            {!! Form::text('donor',null, ['class'=>'form-control', 'id' => 'VMdonor','readonly']) !!}
                            
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                                                        
                            {!! Form::text('branch',null, ['class'=>'form-control', 'id' => 'VMbranch','readonly']) !!}
                            
                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('loanProduct', 'Loan Product:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                                                         
                            {!! Form::text('loanProduct' ,null, ['class'=>'form-control', 'id' => 'VMloanProduct','readonly']) !!}
                            
                        </div>
                    </div>

                    
                    
                     <div id="VMaccNoDiv" class="form-group">
                            {!! Form::label('accNo', 'Account No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('accNo',null, ['class'=>'form-control', 'id' => 'VMaccNo','readonly']) !!}
                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('isRebate', 'Is Rebate?:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                           {!! Form::text('isRebate',null, ['class'=>'form-control', 'id' => 'VMisRebate','readonly']) !!}
                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('paymentDate', 'Payment Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('paymentDate', null, ['class'=>'form-control', 'id' => 'VMpaymentDate','readonly']) !!}
                                
                            </div>
                    </div>


                   
                </div> {{-- End of 1st coloum --}}

                <div class="col-md-6">

                <div id="VMphaseCycleDiv">

                <div class="form-group">
                            {!! Form::label('phase', 'Phase:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('phase',null, ['class'=>'form-control', 'id' => 'VMphase','readonly']) !!}
                                
                            </div>
                    </div>


                 <div class="form-group">
                            {!! Form::label('cycle', 'Cycle:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('cycle',null, ['class'=>'form-control', 'id' => 'VMcycle','readonly']) !!}
                                
                            </div>
                    </div>
                    </div>

                    

                     <div class="form-group">
                            {!! Form::label('numOfInstallment', 'No Of Installment:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('numOfInstallment', null, ['class'=>'form-control', 'id' => 'VMnumOfInstallment','readonly']) !!}
                                                               
                            </div>
                    </div>

                     <div class="form-group">
                            {!! Form::label('loanAmount', 'Loan Amount (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('loanAmount', null, ['class'=>'form-control', 'id' => 'VMloanAmount','readonly']) !!}
                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('principalAmount', 'Principal Amount (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('principalAmount', null, ['amount'=>'0','class'=>'form-control', 'id' => 'VMprincipalAmount','readonly']) !!}
                                
                            </div>
                    </div>



               <div class="form-group">
                            {!! Form::label('interestAmount', 'Interest Amount (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('interestAmount', null, ['amount'=>'0','class'=>'form-control', 'id' => 'VMinterestAmount','readonly']) !!}
                               
                            </div>
                    </div>

                    

                    <div id="VMrebateAmountDiv" class="form-group">
                            {!! Form::label('rebateAmount', 'Rebate Amount (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('rebateAmount', null, ['class'=>'form-control', 'id' => 'VMrebateAmount','readonly']) !!}
                                
                            </div>
                    </div>
                    


                </div> {{-- End of 2nd coloum --}}

               
                
            </div> {{-- End col-12 --}}


                
            </div>
            

                </div>{{--row--}}

                

                {{-- View ModalFooter--}}
                <div class="modal-footer">
               
                    <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span> Close</span></button>
                </div>


            </div> {{-- End View Modal Body--}}

        </div>
    </div>
</div>
{{-- End View Modal --}}

{{-- Edit Modal --}}
<div id="editModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update Payment</h4>
            </div>
            <div class="modal-body">

                <div class="row" style="padding-bottom: 20px;">

                {!! Form::open(['url'=>'','class'=>'form-horizontal']) !!}
                    <div class="col-md-12"> 
                    <div class="col-md-6">


                    {!! Form::hidden('EMpaymentRowId',null,['id'=>'EMpaymentRowId']) !!}
                    {!! Form::hidden('EMisDonor',null,['id'=>'EMisDonor']) !!}



                    <div class="form-group">
                        {!! Form::label('paymentId', 'Payment ID:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">          
                            {!! Form::text('paymentId',null, ['class'=>'form-control', 'id' => 'EMpaymentId','readonly']) !!}
                        </div>
                    </div>

                    

                    <div class="form-group">
                        {!! Form::label('donor', 'Donor:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                                                          
                            {!! Form::text('donor',null, ['class'=>'form-control', 'id' => 'EMdonor','readonly']) !!}
                            
                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        {!! Form::text('donor',null, ['class'=>'form-control', 'id' => 'EMbranch','readonly']) !!}

                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('loanProduct', 'Loan Product:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                                                        
                            {!! Form::text('loanProduct' ,null, ['class'=>'form-control', 'id' => 'EMloanProduct','readonly']) !!}
                            
                        </div>
                    </div>

                    

                    
                    <div id="EMaccNoDiv">
                     <div class="form-group">
                            {!! Form::label('accNo', 'Account No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('accNo',null, ['class'=>'form-control', 'id' => 'EMaccNo','readonly']) !!}
                            </div>
                    </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('isRebate', 'Is Rebate?:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">

                                
                           {!! Form::text('isRebate',null, ['isRebate'=>'0','class'=>'form-control', 'id' => 'EMisRebate','readonly']) !!}
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('paymentDate', 'Payment Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('paymentDate', null, ['class'=>'form-control', 'id' => 'EMpaymentDate','readonly','style'=>'cursor:pointer;']) !!}
                                <p id='paymentDatee' class="error"></p>
                            </div>
                    </div>


                   
                </div> {{-- End of 1st coloum --}}

                <div class="col-md-6">

                <div id="EMphaseCycleDiv">

                <div class="form-group">
                            {!! Form::label('phase', 'Phase:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('phase',null, ['class'=>'form-control', 'id' => 'EMphase','readonly']) !!}
                            </div>
                    </div>


                 <div class="form-group">
                            {!! Form::label('cycle', 'Cycle:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('cycle',null, ['class'=>'form-control', 'id' => 'EMcycle','readonly']) !!}
                            </div>
                    </div>
                    </div>

                    

                     <div class="form-group">
                            {!! Form::label('numOfInstallment', 'No Of Installment:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('numOfInstallment', null, ['class'=>'form-control', 'id' => 'EMnumOfInstallment','readonly']) !!}                               
                            </div>
                    </div>

                     <div class="form-group">
                            {!! Form::label('loanAmount', 'Loan Amount (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('loanAmount', null, ['class'=>'form-control', 'id' => 'EMloanAmount','readonly']) !!}
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('principalAmount', 'Principal Amount (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('principalAmount', null, ['amount'=>'0','class'=>'form-control', 'id' => 'EMprincipalAmount']) !!}
                                <p id='principalAmounte' class="error"></p>
                                <p id='principalAmountValuee' hasError='0' style="display: none;"></p>
                            </div>
                    </div>



               <div class="form-group">
                            {!! Form::label('interestAmount', 'Interest Amount (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('interestAmount', null, ['amount'=>'0','class'=>'form-control', 'id' => 'EMinterestAmount']) !!}
                                <p id='interestAmounte' class="error"></p>
                                <p id='interestAmountValuee' hasError='0' style="display: none;"></p>
                            </div>
                    </div>

                    <div id="EMrebateAmountDiv" style="display: none;">

                    <div class="form-group">
                            {!! Form::label('rebateAmount', 'Rebate Amount (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('rebateAmount', null, ['class'=>'form-control', 'id' => 'EMrebateAmount']) !!}
                                <p id='rebateAmounte' class="error"></p>
                            </div>
                    </div>
                    </div>

                    

                   


                </div> {{-- End of 2nd coloum --}}

               

            
                
            </div>
            {!! Form::close() !!}

                </div>{{--row--}}

                {{-- Edit ModalFooter--}}
                <div class="modal-footer">
                <button id="updateButton" class="btn actionBtn glyphicon glyphicon-check btn-success" type="button"> Update</button>
                    <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span> Close</span></button>
                </div>


            </div> {{-- End Edit Modal Body--}}

        </div>
    </div>
</div>
{{-- End Edit Modal --}}




{{-- Delete Modal --}}
  <div id="deleteModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
        </div>
        <div class="modal-body">
          <h2>Are You Confirm to Delete This Record?</h2>

          <div class="modal-footer">
            {{-- {!! Form::open(['url' => 'deleteOts/']) !!} --}}
            <input id="DMpaymentId" type="hidden" name="paymentId" value="">
            <button id="DMconfirmButton" type="button" class="btn btn-danger"> Confirm</button>
            <button  class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
            {{-- {!! Form::close() !!} --}}

          </div>

        </div>
      </div>
    </div>
  </div>
  {{-- End Delete Modal --}}





<script type="text/javascript">
  $(document).ready(function() {

    function num(argument) {
        return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formateDate(argument){
            var date = $.datepicker.parseDate('yy-mm-dd',argument);
            return $.datepicker.formatDate("dd-mm-yy", date);
        }


    /*View Modal*/    
    
    $(document).on('click', '.view-modal', function() {
        if(hasAccess('getLoanRegisterPaymentInfo')){

      var paymentId = $(this).attr('paymentId');
      var csrf = "{{csrf_token()}}";


      $.ajax({
        url: './getLoanRegisterPaymentInfo',
        type: 'POST',
        dataType: 'json',
        data: {paymentId: paymentId, _token: csrf},
      })
      .done(function(data) {
        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }
        $("#VMpaymentId").val(data['payment'].paymentId);
        $("#VMdonor").val(data['donarName']);
        $("#VMbranch").val(data['branchName']);
        $("#VMloanProduct").val(data['loanProductName']);
        $("#VMpaymentDate").val(formateDate(data['payment'].paymentDate));
        $("#VMnumOfInstallment").val(data['payment'].installmentNo);
        $("#VMloanAmount").val(num(data['account'].loanAmount));
        $("#VMprincipalAmount").val(num(data['payment'].principalAmount));
        $("#VMinterestAmount").val(num(data['payment'].interestAmount));

        if(data['payment'].isRebate==1){
          $("#VMrebateAmountDiv").show();
          $("#VMisRebate").val("Yes");
          $("#VMrebateAmount").val(data['payment'].rebateAmount);
        }
        else{
          $("#VMrebateAmountDiv").hide();
          $("#VMisRebate").val("No");
        }
        

        if (data['isDonor']==1) {
          $("#VMaccNoDiv").hide();
          $("#VMphaseCycleDiv").show();

          $("#VMphase").val(data['account'].phase);
          $("#VMcycle").val(data['account'].cycle);
        }
        else{
          $("#VMaccNoDiv").show();
          $("#VMphaseCycleDiv").hide();
          $("#VMaccNo").val(data['account'].accNo);
        }



         $("#viewModal").find('.modal-dialog').css('width', '80%');
         $("#viewModal").modal('show');
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
    /*End View Modal*/


 /*Edit Modal*/    
    
    $(document).on('click', '.edit-modal', function() {
        if(hasAccess('getLoanRegisterPaymentInfoToUpdate')){

      var paymentId = $(this).attr('paymentId');
      var csrf = "{{csrf_token()}}";

      $("#EMpaymentRowId").val(paymentId);


      $("#principalAmountValuee").empty();
      $("#principalAmountValuee").hide();
      $("#principalAmountValuee").attr('hasError','0');

      $("#interestAmountValuee").empty();
      $("#interestAmountValuee").hide();
      $("#interestAmountValuee").attr('hasError','0');
      $("#updateButton").prop('disabled', false);


      $.ajax({
        url: './getLoanRegisterPaymentInfoToUpdate',
        type: 'POST',
        dataType: 'json',
        data: {paymentId: paymentId, _token: csrf},
      })
      .done(function(data) {

        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }

        $("#EMpaymentId").val(data['payment'].paymentId);
        $("#EMisDonor").val(data['isDonor']);
        $("#EMdonor").val(data['donarName']);
        $("#EMbranch").val(data['branchName']);
        $("#EMloanProduct").val(data['loanProductName']);
        $("#EMpaymentDate").val(formateDate(data['payment'].paymentDate));
        $("#EMpaymentDate").datepicker('option','minDate',new Date(data['previousPaymentDate']));
        $("#EMnumOfInstallment").val(data['payment'].installmentNo);
        $("#EMloanAmount").val(num(data['account'].loanAmount));
        $("#EMprincipalAmount").val(data['payment'].principalAmount);
        $("#EMinterestAmount").val(data['payment'].interestAmount);

        $("#EMprincipalAmount").attr('amount',data['schedulePrincipalAmount']);
        $("#EMinterestAmount").attr('amount',data['scheduleInterestAmount']);

        if(data['payment'].isRebate==1){
          $("#EMrebateAmountDiv").show();
          $("#EMisRebate").val("Yes");
          $("#EMisRebate").attr("isRebate",1);
          $("#EMrebateAmount").val(data['payment'].rebateAmount);

          $("#EMprincipalAmount").prop('readonly',true);
          $("#EMinterestAmount").prop('readonly',true);
        }
        else{
          $("#EMrebateAmountDiv").hide();
          $("#EMisRebate").val("No");
          $("#EMisRebate").attr("isRebate",0);

          $("#EMprincipalAmount").prop('readonly',false);
          $("#EMinterestAmount").prop('readonly',false);
        }
        

        if (data['isDonor']==1) {
          $("#EMaccNoDiv").hide();
          $("#EMphaseCycleDiv").show();

          $("#EMphase").val(data['account'].phase);
          $("#EMcycle").val(data['account'].cycle);
        }
        else{
          $("#EMaccNoDiv").show();
          $("#EMphaseCycleDiv").hide();
          $("#EMaccNo").val(data['account'].accNo);
        }

        $("#editModal").find('.modal-dialog').css('width', '80%');
         $("#editModal").modal('show');

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
    /*End Edit Modal*/






    /*Delete Modal*/ 


    
      $(document).on('click', '.delete-modal', function() {
        if(hasAccess('deleteLoanRegisterPayment')){
      
      $("#DMpaymentId").val($(this).attr('paymentId'));
      $("#deleteModal").modal('show');
  }

      
    });
    /*End Delete Modal*/

    /*Delete The record*/
    $("#DMconfirmButton").on('click',  function() {
        var paymentId = $("#DMpaymentId").val();
        var csrf = "{{csrf_token()}}";

        $.ajax({
            url: './deleteLoanRegisterPayment',
            type: 'POST',
            dataType: 'json',
            data: {paymentId: paymentId, _token:csrf},
        })
        .done(function(data) {
            if (data.accessDenied) {
                showAccessDeniedMessage();
                return false;
            }
            location.reload();
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
        
        
    });
    /*End Delete The record*/


 function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

        function GetFormattedDate(CurrentDate) {
            var date = new Date(CurrentDate);
            return( pad(date.getDate(),2) + '-'+ pad((date.getMonth() + 1),2) +'-' +  date.getFullYear());
        }


        

    /*Opening Date*/
         $("#EMpaymentDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1995:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy'
                    
        });
        /*End Opening Date*/

    

        /*Update the data*/
        $("#updateButton").on('click', function() {
            var paymentId = $("#EMpaymentRowId").val();
            var isDonor = $("#EMisDonor").val();
            var isRebate = $("#EMisRebate").attr('isRebate');
            var principalAmount = $("#EMprincipalAmount").val();
            var duePrincipalAmount = $("#EMprincipalAmount").attr('amount') - principalAmount;
            duePrincipalAmount = parseFloat(duePrincipalAmount.toFixed(2));
            var interestAmount = $("#EMinterestAmount").val();
            var dueInterestAmount = $("#EMinterestAmount").attr('amount') - interestAmount;
            dueInterestAmount = parseFloat(dueInterestAmount.toFixed(2));
            var rebateAmount = $("#EMrebateAmount").val();
            var paymentDate = $("#EMpaymentDate").val();

            alert(duePrincipalAmount);
            alert(dueInterestAmount);
           
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './editLoanRegisterPayment',
                type: 'POST',
                dataType: 'json',
                data: {paymentId: paymentId,isDonor: isDonor,isRebate: isRebate,principalAmount: principalAmount,duePrincipalAmount: duePrincipalAmount,interestAmount: interestAmount,dueInterestAmount: dueInterestAmount,rebateAmount: rebateAmount,paymentDate: paymentDate,  _token: csrf},
            })
            .done(function(data) {
                alert(data);
                
                if (data.errors) {
                    if (data.errors['principalAmount']) {
                        $("#principalAmounte").empty();
                        $("#principalAmounte").show();
                        $("#principalAmounte").append('* '+data.errors['principalAmount']);
                    }
                    if (data.errors['interestAmount']) {
                        $("#interestAmounte").empty();
                        $("#interestAmounte").show();
                        $("#interestAmounte").append('* '+data.errors['interestAmount']);
                    }
                     if (data.errors['rebateAmount']) {
                        $("#rebateAmounte").empty();
                        $("#rebateAmounte").show();
                        $("#rebateAmounte").append('* '+data.errors['rebateAmount']);
                    }
                    
                }
                else{
                    location.reload();
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
        /*End Update the data*/


        /*Validate Input Filed Maximum Value*/
        $("#EMprincipalAmount").on('input', function() { 

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
                
                $("#updateButton").prop('disabled', true);
            }
            else{
                $("#principalAmountValuee").empty();
                $("#principalAmountValuee").hide();
                $("#principalAmountValuee").attr('hasError','0');


                if ($("#interestAmountValuee").attr('hasError')!=1) {
                    $("#updateButton").prop('disabled', false);
                }
                
            }
             
        });

        $("#EMinterestAmount").on('input', function() { 
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
                $("#updateButton").prop('disabled', true);               
                
            }
            else{
                $("#interestAmountValuee").empty();
                $("#interestAmountValuee").hide();
                $("#interestAmountValuee").attr('hasError','0');
                
                if ($("#principalAmountValuee").attr('hasError')!=1) {
                    $("#updateButton").prop('disabled', false);
                }
            }
             
        });
        /*End Validate Input Filed Maximum Value*/


         /*On input/change Hide the Errors*/
         $(document).on('input', 'input', function() {
             $(this).closest('div').find('.error').empty();
             $(this).closest('div').find('.error').hide();
         });
         /* $(document).on('change', 'select', function() {
             $(this).closest('div').find('.error').empty();
         });*/
         /*End On input/change Hide the Errors*/


         $("#EMprincipalAmount,#EMinterestAmount,#EMrebateAmount").on('input', function() {            
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });




  });/*End Ready*/
</script>
@include('dataTableScript')

<style type="text/css">
    #otsTable tr td.name{
        text-align: left;
        padding-left: 5px;
    }
    #otsTable tr td.amount{
        text-align: right;
        padding-right: 5px;
    }
    #VMinstallmentTable thead tr th{
      padding: 3px;
    }
    #VMinstallmentTable thead tr th{
      border: 1px solid white;
    }
    .error{
      display: none;
      color: red;
    }
    .disabled {
   pointer-events: none;
   cursor: default;
}
#principalAmountValuee,#interestAmountValuee{
  color: red;
}
</style>



@endsection
