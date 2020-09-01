@extends('layouts/acc_layout')
@section('title', '| FDR Register')
@section('content')


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addAccFdrInterest/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add FDR Interest</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">FDR INTEREST LIST</font></h1>
        </div>
        
        <div class="panel-body panelBodyView">       

        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#fdrInterestTable").dataTable().yadcf([

    
            ]);*/
            $("#fdrInterestTable").dataTable({ 
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });

              
       });
          
          </script>
        </div>
          <table  id="fdrInterestTable" style="color: black;" class="table table-striped table-bordered" style="color: black;font-size:11px;border-collapse: collapse;margin-bottom:50px;" border= "1px solid black;" cellpadding="0" cellspacing="0">
                    <thead>
                      <tr style="vertical-align: top;">
                        <th width="30">SL#</th>
                        <th>Date</th>                      
                        {{-- <th>Interest ID</th>    --}}                   
                        <th>Account No</th>                      
                        <th>Account Name</th>                      
                        <th>Bank Name</th>
                        {{-- <th>Bank Branch <br> Location</th> --}}
                        <th>Principal Amount (Tk)</th>
                        <th>Bank Charge (Tk)</th>
                        <th>Tax (Tk)</th>
                        
                        <th>Action</th>
                      </tr>                     
                      
                    </thead>
                    <tbody>
                     @foreach($interests as $index => $interest)
                     @php
                        $account = DB::table('acc_fdr_account')->where('id',$interest->fdrAccId_fk)->first();
                        $bankName = DB::table('gnr_bank')->where('id',$account->bankId_fk)->value('name');
                        $bankBranchName = DB::table('gnr_bank_branch')->where('id',$account->bankBranchId_fk)->value('name');
                     @endphp              
                     <tr>
                        <td>{{$index+1}}</td>
                        <td>{{$interest->receiveDate}}</td>
                        {{-- <td>{{$interest->interestId}}</td> --}}
                        <td>{{$account->accNo}}</td>
                        <td class="name">{{$account->accName}}</td>
                        <td class="name">{{$bankName}}</td>
                        {{-- <td class="name">{{$bankBranchName}}</td> --}} 
                        <td class="amount">{{number_format($account->principalAmount,2,'.',',')}}</td>
                        <td class="amount">{{number_format($interest->bankCharge,2,'.',',')}}</td>
                        <td class="amount">{{number_format($interest->taxAmount,2,'.',',')}}</td>
                        
                        <td width="80">

                        <a href="javascript:;" class="view-modal" interestId="{{$interest->id}}">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                          </a>&nbsp; 
                          <a href="javascript:;" class="edit-modal" interestId="{{$interest->id}}">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;

                        <a href="javascript:;" class="delete-modal" interestId="{{$interest->id}}">
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
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Interest Details</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    <div class="form-group">
                                            {!! Form::label('interestId', 'Interest Id:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('interestId', null,['id'=>'VMinterestId','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('accNo', 'Account Number:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('accNo', null,['id'=>'VMaccNo','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('accName', 'Account Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('accName', null,['id'=>'VMaccName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            {!! Form::label('fdrType', 'FDR Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">  
                                                                            
                                                {!! Form::text('fdrType',null, ['class'=>'form-control', 'id' => 'VMfdrType','readonly']) !!}
                                                
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('bankName', 'Bank Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('bankName', null,['id'=>'VMbankName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('bankBranchLocation', 'Bank Branch Location:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                                
                                                {!! Form::text('bankBranchLocation', null,['id'=>'VMbankBranchLocation','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                     

                                    </div>{{--form-horizontal form-groups--}}
                                </div>{{--End 1st col-md-6--}}

                                <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    <div class="form-group">
                                            {!! Form::label('receivedDate', 'Received Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('receivedDate', null,['id'=>'VMreceivedDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                    <div class="form-group">
                                            {!! Form::label('principalAmount', 'Principal Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('principalAmount', null,['id'=>'VMprincipalAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                       

                                       

                                        <div class="form-group">
                                            {!! Form::label('interest', 'Interest:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('interest', null,['id'=>'VMinterest','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        

                                        <div class="form-group">
                                            {!! Form::label('bankCharge', 'Bank Charge:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('bankCharge', null,['id'=>'VMbankCharge','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('taxAmount', 'Tax:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('taxAmount', null,['id'=>'VMtaxAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('receivableAmount', 'Receivable Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('receivableAmount', null,['id'=>'VMreceivableAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('netInterestAmount', 'Net Interest Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('netInterestAmount', null,['id'=>'VMnetInterestAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        

                                    </div>
                                </div>{{--End 2nd col-md-6--}}
                            </div>

                        </div>{{--row--}}

                        {{-- View ModalFooter--}}
                        <div class="modal-footer">

                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span> Close</span></button>
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
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update Interest</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    <div class="form-group">

                                    {!! Form::hidden('interestRowId',null,['id'=>'EMinterestRowId']) !!}
                                            {!! Form::label('interestId', 'Interest Id:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('interestId', null,['id'=>'EMinterestId','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('accNo', 'Account Number:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('accNo', null,['id'=>'EMaccNo','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('accName', 'Account Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('accName', null,['id'=>'EMaccName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            {!! Form::label('fdrType', 'FDR Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">  
                                                                            
                                                {!! Form::text('fdrType',null, ['class'=>'form-control', 'id' => 'EMfdrType','readonly']) !!}
                                                
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('bankName', 'Bank Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('bankName', null,['id'=>'EMbankName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('bankBranchLocation', 'Bank Branch Location:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                                
                                                {!! Form::text('bankBranchLocation', null,['id'=>'EMbankBranchLocation','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                     

                                    </div>{{--form-horizontal form-groups--}}
                                </div>{{--End 1st col-md-6--}}

                                <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    <div class="form-group">
                                            {!! Form::label('receivedDate', 'Received Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('receivedDate', null,['id'=>'EMreceivedDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                    <div class="form-group">
                                            {!! Form::label('principalAmount', 'Principal Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('principalAmount', null,['id'=>'EMprincipalAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                       

                                       

                                        <div class="form-group">
                                            {!! Form::label('interest', 'Interest:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('interest', null,['id'=>'EMinterest','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                            </div>
                                        </div>

                                        

                                        <div class="form-group">
                                            {!! Form::label('bankCharge', 'Bank Charge:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('bankCharge', null,['id'=>'EMbankCharge','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('taxAmount', 'Tax:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('taxAmount', null,['id'=>'EMtaxAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('receivableAmount', 'Receivable Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('receivableAmount', null,['id'=>'EMreceivableAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('netInterestAmount', 'Net Interest Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('netInterestAmount', null,['id'=>'EMnetInterestAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        

                                    </div>
                                </div>{{--End 2nd col-md-6--}}
                            </div>

                        </div>{{--row--}}

                        {{-- Edit ModalFooter--}}
                        <div class="modal-footer">
                        <button id="updateButton" class="btn actionBtn glyphicon glyphicon-check btn-success edit" paymentId="0" type="button"> Update</button>
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
            
            <input id="DMinterestId" type="hidden" name="DMinterestId" value="">
            <button id="deleteButton" type="button" class="btn btn-danger"> Confirm</button>
            <button class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span> Close</span></button>
            

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

 /*View Modal*/    
    
    $(document).on('click', '.view-modal', function() {
        if(hasAccess('getAccFdrInterestInfo')){

      var interestId = $(this).attr('interestId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './getAccFdrInterestInfo',
        type: 'POST',
        dataType: 'json',
        data: {interestId: interestId, _token: csrf},
        success: function(data) {
        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }
         $("#VMinterestId").val(data['interest'].interestId);
         $("#VMaccNo").val(data['account'].accNo);
         $("#VMaccName").val(data['account'].accName);
         $("#VMfdrType").val(data['frdTypeName']);
         $("#VMbankName").val(data['account'].bankName);
         $("#VMbankBranchLocation").val(data['account'].bankBranchLocation);
         

         $("#VMreceivedDate").val(data['receiveDate']);
         $("#VMprincipalAmount").val(num(data['account'].principalAmount));
         $("#VMinterest").val(num(data['interest'].interestAmount));
         $("#VMbankCharge").val( num(data['interest'].bankCharge));
         $("#VMtaxAmount").val( num(data['interest'].taxAmount));
         $("#VMnetInterestAmount").val(num(data['interest'].netInterestAmount));
         $("#VMreceivableAmount").val(num(data['receivableAmount']));


         $("#viewModal").modal('show');

        },
        error: function(argument) {
          alert('response error');
        }
      });

      }
    });
    /*End View Modal*/

     $("#EMreceivedDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                $("#EMreceivedDatee").empty();
                }
            
        });


 /*Edit Modal*/    
    
    $(document).on('click', '.edit-modal', function() {
        if(hasAccess('getAccFdrInterestInfoToUpdate')){

      var interestId = $(this).attr('interestId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './getAccFdrInterestInfoToUpdate',
        type: 'POST',
        dataType: 'json',
        data: {interestId: interestId, _token: csrf},
        success: function(data) {
        
        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }

         $("#EMinterestRowId").val(data['interest'].id);
         $("#EMinterestId").val(data['interest'].interestId);
         $("#EMaccNo").val(data['account'].accNo);
         $("#EMaccName").val(data['account'].accName);
         $("#EMfdrType").val(data['frdTypeName']);
         $("#EMbankName").val(data['account'].bankName);
         $("#EMbankBranchLocation").val(data['account'].bankBranchLocation);
         

         //$("#EMreceivedDate").val(data['receiveDate']);
         $("#EMprincipalAmount").val(num(data['account'].principalAmount));
         $("#EMinterest").val(data['interest'].interestAmount);
         $("#EMbankCharge").val(data['interest'].bankCharge);         
         $("#EMtaxAmount").val(data['interest'].taxAmount);
         $("#EMreceivableAmount").val(data['receivableAmount']);

         $("#EMnetInterestAmount").val(data['interest'].netInterestAmount);

         $('#EMreceivedDate').datepicker("setDate", new Date(data['interest'].receiveDate) );
         $('#EMreceivedDate').datepicker("option","minDate", new Date(data['account'].openingDate) );

         $("#editModal").modal('show');

        },
        error: function(argument) {
          alert('response error');
        }
      });

      }
    });
    /*End Edit Modal*/


    /*Calculate Net Interest Amount In Edit Modal*/
    $("#EMinterest,#EMbankCharge,#EMtaxAmount").on('input', function() {      
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

      var interest = 0;
      var bankCharge = 0;
      var taxAmount = 0;
      var receivableAmount = 0;

      if ($("#EMinterest").val()!='') {interest = parseFloat($("#EMinterest").val());}
      if ($("#EMbankCharge").val()!='') {bankCharge = parseFloat($("#EMbankCharge").val());}
      if ($("#EMtaxAmount").val()!='') {taxAmount = parseFloat($("#EMtaxAmount").val());}
      if ($("#EMreceivableAmount").val()!='') {receivableAmount = parseFloat($("#EMreceivableAmount").val());}

      var netInterestAmount = interest - bankCharge - taxAmount - receivableAmount;

      $("#EMnetInterestAmount").val(netInterestAmount);

    });
    /*End Calculate Net Interest Amount In Edit Modal*/


    /*Update Interest*/
    $("#updateButton").on('click', function() {
      var interestId = $("#EMinterestRowId").val();
      var interestAmount = $("#EMinterest").val();
      var bankChargeAmount = $("#EMbankCharge").val();
      var taxAmount = $("#EMtaxAmount").val();
      var netInterestAmount = $("#EMnetInterestAmount").val();
      var receivedDate = $("#EMreceivedDate").val();
      var csrf = "{{csrf_token()}}";

      $.ajax({
        url: './editFdrInterest',
        type: 'POST',
        dataType: 'json',
        data: {interestId: interestId, interestAmount: interestAmount, bankChargeAmount: bankChargeAmount, taxAmount: taxAmount, netInterestAmount: netInterestAmount, receivedDate: receivedDate, _token: csrf },
      })
      .done(function(data) {
        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }
        location.reload();
        console.log("success");
      })
      .fail(function() {
        alert("error");
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      
    });
    /*End Update Interest*/


     /*Delete Modal*/    
    
      $(document).on('click', '.delete-modal', function() {
        if(hasAccess('deleteFdrInterest')){
      
      $("#DMinterestId").val($(this).attr('interestId'));
      $("#deleteModal").modal('show');
  }
      
    });
    /*End Delete Modal*/


    /*Delete The record*/
    $("#deleteButton").on('click',  function() {
        var interestId = $("#DMinterestId").val();
        var csrf = "{{csrf_token()}}";

        $.ajax({
            url: './deleteFdrInterest',
            type: 'POST',
            dataType: 'json',
            data: {interestId: interestId, _token:csrf},
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


    



  });/*End Ready*/
</script>



<style type="text/css">
    #fdrInterestTable tr td.name{
        text-align: left;
        padding-left: 5px;
    }
    #fdrInterestTable tr td.amount{
        text-align: right;
        padding-right: 5px;
    }
   
    .EMtable input { text-align: center; width: 80px; max-height: 20px;}
</style>

<style type="text/css">
    #fdrInterestTable thead tr th{padding: 2px;}
</style>


@include('dataTableScript')

<style type="text/css">
   #fdrInterestTable thead tr:nth-child(2) th{
      border-top-width: 1px !important;        
    }
     #fdrInterestTable thead tr:nth-child(2) th:nth-child(3){
      border-right-width: 1px !important;        
    }

    
    
</style>
@endsection
