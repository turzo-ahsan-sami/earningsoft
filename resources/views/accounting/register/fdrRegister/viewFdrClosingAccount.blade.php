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
              <a href="{{url('addFdrAccountClose/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Encashment</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">FDR ENCASHMENT LIST</font></h1>
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
          <table  id="otsTable" style="color: black;" class="table table-striped table-bordered" style="color: black;font-size:11px;border-collapse: collapse;margin-bottom:50px;" border= "1px solid black;" cellpadding="0" cellspacing="0">
                    <thead>
                      <tr style="vertical-align: top;">
                        <th width="30">SL#</th>
                        <th>Date</th>                      
                        <th>Account No</th>
                        <th>Account Name</th>
                        <th>Bank</th>
                        <th>Bank Branch Location</th>                   
                        <th>Principal Amount (TK)</th>                   
                        <th>Net Interest Amount (TK)</th>
                        <th>Total Amount (TK)</th>
                        <th>Action</th>           
                      </tr>
                    </thead>

                    <tbody>
                     @foreach($accountClose as $index => $accClose)
                     @php
                      $account = DB::table('acc_fdr_account')->where('id',$accClose->accId_fk)->first();
                      $netInterest = DB::table('acc_fdr_interest')->where('fdrAccId_fk',$account->id)->sum('netInterestAmount');

                      $bankName = DB::table('gnr_bank')->where('id',$account->bankId_fk)->value('name');
                      $bankBranchName = DB::table('gnr_bank_branch')->where('id',$account->bankBranchId_fk)->value('name');
                    @endphp                   
                     <tr>
                        <td>{{$index+1}}</td>
                        <td>{{date('d-m-Y',strtotime($accClose->closingDate))}}</td>
                        <td>{{$account->accNo}}</td>
                        <td class="name">{{$account->accName}}</td>
                        <td class="name">{{$bankName}}</td>
                        <td class="name">{{$bankBranchName}}</td>
                        
                        
                        <td class="amount">{{number_format($account->principalAmount,2,'.',',')}}</td>
                        
                        <td class="amount">{{number_format($netInterest,2,'.',',')}}</td>

                        <td class="amount">{{number_format($account->principalAmount + $netInterest,2,'.',',')}}</td>
                        
                        <td width="80">

                        <a href="javascript:;" class="view-modal" accCloseId="{{$accClose->id}}" accountId="{{$account->id}}">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                          </a>&nbsp; 
                          <a href="javascript:;" class="edit-modal" accCloseId="{{$accClose->id}}" accountId="{{$account->id}}">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;

                        <a href="javascript:;" class="delete-modal" accCloseId="{{$accClose->id}}" accountId="{{$account->id}}">
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
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Account Details</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

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
                                                {!! Form::text('fdrType', null,['id'=>'VMfdrType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            {!! Form::label('project', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('project', null,['id'=>'VMproject','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('projectType', null,['id'=>'VMprojectType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        

                                         <div class="form-group">
                                            {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">  
                                                                            
                                                {!! Form::text('branch',null, ['class'=>'form-control', 'id' => 'VMbranch','readonly']) !!}
                                                
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
                                            {!! Form::label('principalAmount', 'Principal Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('principalAmount', null,['id'=>'VMprincipalAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('netInterestAmount', 'Net Interest Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('netInterestAmount', null,['id'=>'VMnetInterestAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('totalAmount', 'Total Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('totalAmount', null,['id'=>'VMtotalAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>                                       

                                       

                                        <div class="form-group">
                                            {!! Form::label('interestRate', 'Interest Rate:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('interestRate', null,['id'=>'VMinterestRate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        

                                        <div class="form-group">
                                            {!! Form::label('duration', 'Duration:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('duration', null,['id'=>'VMduration','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('openingDate', 'Opening Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('openingDate', null,['id'=>'VMopeningDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('matureDate', 'Maturity Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('matureDate', null,['id'=>'VMmatureDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('closingDate', 'Closing Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('closingDate', null,['id'=>'VMclosingDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
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
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Account Details</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    {!! Form::hidden('accCloseId',null,['id'=>'EMaccCloseId']) !!}

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
                                                {!! Form::text('fdrType', null,['id'=>'EMfdrType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            {!! Form::label('project', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('project', null,['id'=>'EMproject','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('projectType', null,['id'=>'EMprojectType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        

                                         <div class="form-group">
                                            {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">  
                                                                            
                                                {!! Form::text('branch',null, ['class'=>'form-control', 'id' => 'EMbranch','readonly']) !!}
                                                
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
                                            {!! Form::label('principalAmount', 'Principal Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('principalAmount', null,['id'=>'EMprincipalAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('netInterestAmount', 'Net Interest Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('netInterestAmount', null,['id'=>'EMnetInterestAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                       
                                         <div class="form-group">
                                            {!! Form::label('totalAmount', 'Total Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('totalAmount', null,['id'=>'EMtotalAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                       

                                        <div class="form-group">
                                            {!! Form::label('interestRate', 'Interest Rate:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('interestRate', null,['id'=>'EMinterestRate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        

                                        <div class="form-group">
                                            {!! Form::label('duration', 'Duration:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('duration', null,['id'=>'EMduration','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('openingDate', 'Opening Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('openingDate', null,['id'=>'EMopeningDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('matureDate', 'Maturity Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('matureDate', null,['id'=>'EMmatureDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('closingDate', 'Closing Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('closingDate', null,['id'=>'EMclosingDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly','style'=>'cursor:pointer;']) !!}
                                            </div>
                                        </div>

                                        

                                    </div>
                                </div>{{--End 2nd col-md-6--}}
                            </div>

                        </div>{{--row--}}

                        {{-- Edit ModalFooter--}}
                        <div class="modal-footer">
                        <button id="updateButton" class="btn actionBtn glyphicon glyphicon-check btn-success edit" paymentId="0" type="button"> Update</button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span> Close</span></button>
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
            <input id="DMaccCloseId" type="hidden" name="accCloseId" value="">
            <button id="DMconfirmButton" type="button" class="btn btn-danger"> Confirm</button>
            <button  class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
            
          </div>

        </div>
      </div>
    </div>
  </div>
  {{-- End Delete Modal --}}





<script type="text/javascript">
  $(document).ready(function() {


    function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

      function newDateFormate(argument) {
        var formattedDate = new Date(argument);
        var d = formattedDate.getDate();
        var m =  formattedDate.getMonth();
        m += 1;  // JavaScript months are 0-11
        var y = formattedDate.getFullYear();

        return (pad(d,2) + "-" + pad(m,2) + "-" + y);
      }

      function num(argument) {
          return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }




    /*View Modal*/    
    
    $(document).on('click', '.view-modal', function() {
        if(hasAccess('fdrClosingGetAccountInfo')){

      var accId = $(this).attr('accountId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './fdrClosingGetAccountInfo',
        type: 'POST',
        dataType: 'json',
        data: {accId: accId, _token: csrf},
        success: function(data) {

        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }
          
         $("#VMaccNo").val(data['account'].accNo);
         $("#VMaccName").val(data['account'].accName);
         $("#VMfdrType").val(data['fdrTypeName']);
         $("#VMbankName").val(data['account'].bankName);
         $("#VMbankBranchLocation").val(data['account'].bankBranchLocation);
         
         $("#VMproject").val(data['projectName']);
         $("#VMprojectType").val(data['projectTypeName']);
         $("#VMbranch").val(data['branchName']);

         $("#VMprincipalAmount").val(num(data['account'].principalAmount));
         $("#VMnetInterestAmount").val(num(data['netInterestAmount']));
         $("#VMtotalAmount").val(num(data['totalAmount']));

         $("#VMinterestRate").val(num(data['account'].interestRate));
         $("#VMduration").val(data['duration']);         
         $("#VMopeningDate").val(data['openingDate']);
         $("#VMmatureDate").val(data['matureDate']);
         $("#VMclosingDate").val(newDateFormate(data['closingDate']));


         $("#viewModal").modal('show');

        },
        error: function(argument) {
          alert('response error');
        }
      });

      }
    });
    /*End View Modal*/


 /*Edit Modal*/    
    
    $(document).on('click', '.edit-modal', function() {
        if(hasAccess('fdrClosingGetAccountInfoToUpdate')){

      var accId = $(this).attr('accountId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './fdrClosingGetAccountInfoToUpdate',
        type: 'POST',
        dataType: 'json',
        data: {accId: accId, _token: csrf},
        success: function(data) {

        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }
          
        $("#EMaccCloseId").val(data['accCloseId']);
         $("#EMaccNo").val(data['account'].accNo);
         $("#EMaccName").val(data['account'].accName);
         $("#EMfdrType").val(data['fdrTypeName']);
         $("#EMbankName").val(data['account'].bankName);
         $("#EMbankBranchLocation").val(data['account'].bankBranchLocation);

         $("#EMproject").val(data['projectName']);
         $("#EMprojectType").val(data['projectTypeName']);
         $("#EMbranch").val(data['branchName']);
         

         $("#EMprincipalAmount").val(data['account'].principalAmount);
         $("#EMnetInterestAmount").val(data['netInterestAmount']);
         $("#EMtotalAmount").val(num(data['totalAmount']));
         $("#EMinterestRate").val(data['account'].interestRate);
         $("#EMduration").val(data['account'].duration)     
         $("#EMopeningDate").val(data['openingDate']);
         $("#EMmatureDate").val(data['matureDate']);
         $("#EMclosingDate").val(newDateFormate(data['closingDate']));

         if (data['lastInterestReceivedDate']!=null) {
          $("#EMclosingDate").datepicker('option','minDate',new Date(data['lastInterestReceivedDate']));
         }
         else{
          $("#EMclosingDate").datepicker('option','minDate',new Date(data['account'].matureDate));
         }
         
         $("#editModal").find('.modal-dialog').css('width', '80%');
         $("#editModal").modal('show');

        },
        error: function(argument) {
          alert('response error');
        }
      });

      }
    });
    /*End Edit Modal*/


    /*Update Data*/
    $("#updateButton").on('click', function() {
      
      var accCloseId = $("#EMaccCloseId").val();
      var closingDate = $("#EMclosingDate").val();
      var csrf = "{{csrf_token()}}";

      

      $.ajax({
        url: './editFdrAccountClose',
        type: 'POST',
        dataType: 'json',
        data: {accCloseId: accCloseId, closingDate: closingDate, _token: csrf},
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
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      
    });
    /*End Update Data*/







    /*Delete Modal*/    
    
      $(document).on('click', '.delete-modal', function() {
        if(hasAccess('deleteFdrAccountClose')){
      
      $("#DMaccCloseId").val($(this).attr('accCloseId'));
      $("#deleteModal").modal('show');
  }

      
    });
    /*End Delete Modal*/

    /*Delete Data*/
    $("#DMconfirmButton").on('click', function() {
      
      var accCloseId = $("#DMaccCloseId").val();
      var csrf = "{{csrf_token()}}";

      $.ajax({
        url: './deleteFdrAccountClose',
        type: 'POST',
        dataType: 'json',
        data: {accCloseId: accCloseId, _token: csrf},
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
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      
    });
    /*End Delete Data*/


     $("#EMclosingDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy'
            
        });




  });/*End Ready*/
</script>

<style type="text/css">
    #otsTable tr td.name{
        text-align: left;
        padding-left: 5px;
    }
    #otsTable tr td.amount{
        text-align: right;
        padding-right: 5px;
    }
   
    .EMtable input { text-align: center; width: 80px; max-height: 20px;}
</style>

<style type="text/css">
    #otsTable thead tr th{padding: 2px;}
</style>


@include('dataTableScript')

<style type="text/css">
   #otsTable thead tr:nth-child(2) th{
      border-top-width: 1px !important;        
    }
     #otsTable thead tr:nth-child(2) th:nth-child(3){
      border-right-width: 1px !important;        
    }

    
</style>
@endsection
