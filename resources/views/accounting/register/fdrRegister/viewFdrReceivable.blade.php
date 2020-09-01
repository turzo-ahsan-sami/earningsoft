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
              <a href="{{url('addAccFdrReceivable/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add FDR Receivable</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">FDR RECEIVABLE LIST</font></h1>
        </div>
        
        <div class="panel-body panelBodyView">       

        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#fdrreceivableTable").dataTable().yadcf([
    
            ]);*/
            $("#fdrreceivableTable").dataTable({ 
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });

              
       });
          
          </script>
        </div>
          <table  id="fdrreceivableTable" style="color: black;" class="table table-striped table-bordered" style="color: black;font-size:11px;border-collapse: collapse;margin-bottom:50px;" border= "1px solid black;" cellpadding="0" cellspacing="0">
                    <thead>
                      <tr style="vertical-align: top;">
                        <th width="30">SL#</th>
                        <th>Date</th>                      
                        {{-- <th>Receivable ID</th>  --}}                   
                        <th>Account No</th>                      
                        <th>Account Name</th>                      
                        <th>Bank Name</th>
                        {{-- <th>Bank Branch <br> Location</th> --}}
                        <th>Principal Amount (Tk)</th>
                        <th>Balance (Tk)</th>
                        <th>Receiable Amount (Tk)</th>
                        <th>Action</th>
                      </tr>                     
                      
                    </thead>
                    <tbody>
                     @foreach($receivables as $index => $receivable)
                     @php
                        $account = DB::table('acc_fdr_account')->where('id',$receivable->accId_fk)->first();
                        $bankName = DB::table('gnr_bank')->where('id',$account->bankId_fk)->value('name');
                        $bankBranchName = DB::table('gnr_bank_branch')->where('id',$account->bankBranchId_fk)->value('name');
                     @endphp              
                     <tr>
                        <td>{{$index+1}}</td>
                        <td>{{$receivable->receivableDate}}</td>
                        {{-- <td>{{$receivable->receivableId}}</td> --}}
                        <td>{{$account->accNo}}</td>
                        <td class="name">{{$account->accName}}</td>
                        <td class="name">{{$bankName}}</td>
                        {{-- <td class="name">{{$bankBranchName}}</td>  --}}
                        <td class="amount">{{number_format($account->principalAmount,2,'.',',')}}</td>
                        <td class="amount">{{number_format($receivable->netAmountBeforeReceivable,2,'.',',')}}</td>
                        <td class="amount">{{number_format($receivable->amount,2,'.',',')}}</td>
                        <td width="80">

                        <a href="javascript:;" class="view-modal" receivableId="{{$receivable->id}}">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                          </a>&nbsp; 
                          <a href="javascript:;" class="edit-modal" receivableId="{{$receivable->id}}">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;

                        <a href="javascript:;" class="delete-modal" receivableId="{{$receivable->id}}">
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
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">receivable Details</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    <div class="form-group">
                                            {!! Form::label('receivableId', 'Receivable Id:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('receivableId', null,['id'=>'VMreceivableId','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
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
                                            {!! Form::label('fdrType', 'FDR Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">  
                                                                            
                                                {!! Form::text('fdrType',null, ['class'=>'form-control', 'id' => 'VMfdrType','readonly']) !!}
                                                
                                            </div>
                                        </div>

                                    

                                    <div class="form-group">
                                            {!! Form::label('principalAmount', 'Principal Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('principalAmount', null,['id'=>'VMprincipalAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                       

                                       

                                        <div class="form-group">
                                            {!! Form::label('receivableAmount', 'Receivable Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('receivableAmount', null,['id'=>'VMreceivableAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>


                                    <div class="form-group">
                                        {!! Form::label('dateFrom', 'Date From:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('dateFrom', null,['id'=>'VMdateFrom','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('receivabledDate', 'Received Date:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('receivabledDate', null,['id'=>'VMreceivabledDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        {!! Form::label('days', 'Days:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('days', null,['id'=>'VMdays','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
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
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update receivable</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    <div class="form-group">

                                    {!! Form::hidden('receivableRowId',null,['id'=>'EMreceivableRowId']) !!}
                                            {!! Form::label('receivableId', 'receivable Id:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('receivableId', null,['id'=>'EMreceivableId','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
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
                                            {!! Form::label('fdrType', 'FDR Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">  
                                                                            
                                                {!! Form::text('fdrType',null, ['class'=>'form-control', 'id' => 'EMfdrType','readonly']) !!}
                                                
                                            </div>
                                        </div>

                                   

                                    <div class="form-group">
                                            {!! Form::label('principalAmount', 'Principal Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('principalAmount', null,['id'=>'EMprincipalAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                       

                                       

                                        <div class="form-group">
                                            {!! Form::label('receivableAmount', 'Receivable Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('receivableAmount', null,['id'=>'EMreceivableAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                                <p id="receivableAmounte" class="error"></p>
                                            </div>
                                        </div>


                                    <div class="form-group">
                                        {!! Form::label('dateFrom', 'Date From:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('dateFrom', null,['id'=>'EMdateFrom','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                        </div>
                                    </div>

                                        
                                         <div class="form-group">
                                            {!! Form::label('receivableDate', 'Receivable Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('receivableDate', null,['id'=>'EMreceivableDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                                <p id="EMreceivableDatee" class="error"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                        {!! Form::label('days', 'Days:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('days', null,['id'=>'EMdays','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
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
            
            <input id="DMreceivableId" type="hidden" name="DMreceivableId" value="">
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
        if(hasAccess('getAccFdrReceivableInfo')){

      var receivableId = $(this).attr('receivableId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './getAccFdrReceivableInfo',
        type: 'POST',
        dataType: 'json',
        data: {receivableId: receivableId, _token: csrf},
        success: function(data) {

        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }
          
         $("#VMreceivableId").val(data['receivable'].receivableId);
         $("#VMaccNo").val(data['account'].accNo);
         $("#VMaccName").val(data['account'].accName);
         $("#VMfdrType").val(data['frdTypeName']);
         $("#VMbankName").val(data['bankName']);
         $("#VMbankBranchLocation").val(data['bankBranchName']);
         

         $("#VMprincipalAmount").val(num(data['account'].principalAmount));
         $("#VMreceivableAmount").val(num(data['receivable'].amount));

         $("#VMdateFrom").val(data['dateFrom']);
         $("#VMreceivabledDate").val(data['receivableDate']);
         $("#VMdays").val(data['receivable'].days);


         $("#viewModal").modal('show');

        },
        error: function(argument) {
          alert('response error');
        }
      });

      }
    });
    /*End View Modal*/

    



     function calculateDays() {
             var dateFrom = $("#EMdateFrom").val();
             var receivableDate = $("#EMreceivableDate").val();

             if (dateFrom!='' && receivableDate!='') {
                var startDate = $.datepicker.parseDate('dd-mm-yy',dateFrom);
                var endDate = $.datepicker.parseDate('dd-mm-yy',receivableDate);
                var dayDiff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24))+1;

                $("#EMdays").val(dayDiff);
             }
         }

    $("#EMdateFrom").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                var date = $.datepicker.parseDate('dd-mm-yy',$(this).val());
                $("#EMreceivableDate").datepicker("option","minDate",date);
                calculateDays();
                }            
        });

     $("#EMreceivableDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                calculateDays();
            }          
        });


 /*Edit Modal*/    
    
    $(document).on('click', '.edit-modal', function() {
        if(hasAccess('getAccFdrReceivableInfoToUpdate')){

      var receivableId = $(this).attr('receivableId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './getAccFdrReceivableInfoToUpdate',
        type: 'POST',
        dataType: 'json',
        data: {receivableId: receivableId, _token: csrf},
        success: function(data) {
        
        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }

         $("#EMreceivableRowId").val(data['receivable'].id);
         $("#EMreceivableId").val(data['receivable'].receivableId);
         $("#EMaccNo").val(data['account'].accNo);
         $("#EMaccName").val(data['account'].accName);
         $("#EMfdrType").val(data['frdTypeName']);
         $("#EMbankName").val(data['bankName']);
         $("#EMbankBranchLocation").val(data['bankBranchName']);
         

         //$("#EMreceivedDate").val(data['receiveDate']);
         $("#EMprincipalAmount").val(num(data['account'].principalAmount));
         $("#EMreceivableAmount").val(data['receivable'].amount);
         
         $('#EMdateFrom').datepicker("setDate", new Date(data['receivable'].dateFrom) );
         $('#EMdateFrom').datepicker("option","minDate", new Date(data['account'].openingDate) );

         $('#EMreceivableDate').datepicker("setDate", new Date(data['receivable'].receivableDate) );
         $('#EMreceivableDate').datepicker("option","minDate", new Date(data['account'].openingDate) );

         $("#EMdays").val(data['receivable'].days);

         $("#editModal").modal('show');

        },
        error: function(argument) {
          alert('response error');
        }
      });

      }
    });
    /*End Edit Modal*/


    /*Calculate Net receivable Amount In Edit Modal*/
    $("#EMreceivableAmount").on('input', function() {      
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

    });
    /*End Calculate Net receivable Amount In Edit Modal*/


    /*Update receivable*/
    $("#updateButton").on('click', function() {
      var receivableId = $("#EMreceivableRowId").val();
      var receivableAmount = $("#EMreceivableAmount").val();
      var dateFrom = $("#EMdateFrom").val();
      var receivableDate = $("#EMreceivableDate").val();
      var days = $("#EMdays").val();
      var csrf = "{{csrf_token()}}";

      $.ajax({
        url: './editFdrReceivable',
        type: 'POST',
        dataType: 'json',
        data: {receivableId: receivableId,dateFrom: dateFrom, days: days ,receivableAmount: receivableAmount, receivableDate: receivableDate, _token: csrf },
      })
      .done(function(data) {
        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }
        if (data.errors) {
            if (data.errors['receivableAmount']) {
                $("#receivableAmounte").empty();
                $("#receivableAmounte").append('* '+data.errors['receivableAmount']);
            }
        }
        else{
            location.reload();    
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
      
    });
    /*End Update receivable*/


     /*Delete Modal*/    
    
      $(document).on('click', '.delete-modal', function() {
        if(hasAccess('deleteFdrReceivable')){
      
      $("#DMreceivableId").val($(this).attr('receivableId'));
      $("#deleteModal").modal('show');

      }
    });
    /*End Delete Modal*/


    /*Delete The record*/
    $("#deleteButton").on('click',  function() {
        var receivableId = $("#DMreceivableId").val();
        var csrf = "{{csrf_token()}}";

        $.ajax({
            url: './deleteFdrReceivable',
            type: 'POST',
            dataType: 'json',
            data: {receivableId: receivableId, _token:csrf},
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



<style type="text/css">
    #fdrreceivableTable tr td.name{
        text-align: left;
        padding-left: 5px;
    }
    #fdrreceivableTable tr td.amount{
        text-align: right;
        padding-right: 5px;
    }
   
    .EMtable input { text-align: center; width: 80px; max-height: 20px;}
</style>

<style type="text/css">
    #fdrreceivableTable thead tr th{padding: 2px;}
</style>


@include('dataTableScript')

<style type="text/css">
   #fdrreceivableTable thead tr:nth-child(2) th{
      border-top-width: 1px !important;        
    }
     #fdrreceivableTable thead tr:nth-child(2) th:nth-child(3){
      border-right-width: 1px !important;        
    }
    .error{
        color: red;
    }

    
    
</style>
@endsection
