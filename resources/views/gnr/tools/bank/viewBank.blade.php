@extends('layouts/gnr_layout')
@section('title', '| Bank')
@section('content')


@php
  $bankIdsFromLoanRegisterTable = DB::table('acc_loan_register_account')->distinct()->pluck('bankId_fk')->toArray();
  $bankIdsFromBankBranchTable = DB::table('gnr_bank_branch')->distinct()->pluck('bankId_fk')->toArray();
  
  $foreignBankIds = array_merge($bankIdsFromLoanRegisterTable, $bankIdsFromBankBranchTable);
  $foreignBankIds = array_unique($foreignBankIds);  
@endphp



<div class="row">
<div class="col-md-2"></div>
<div class="col-md-8">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addBank/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Bank/Donor</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">BANK/DONOR LIST</font></h1>
        </div>
        
        <div class="panel-body panelBodyView">       

        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#bankTable").dataTable().yadcf([
    
            ]);*/
            $("#bankTable").dataTable({              
                  
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });

              
       });
          
          </script>
        </div>
          <table class="table table-striped table-bordered" id="bankTable" style="color:black;">
                    <thead>
                      <tr>
                        <th width="30">SL#</th>
                        <th>Name</th>
                        <th>Short Name</th>
                        <th>Action</th>
                      </tr>
                      
                    </thead>
                    <tbody>
                    @foreach($banks as $index => $bank)
                    <tr>
                      <td>{{$index+1}}</td>
                      <td class="name">{{$bank->name}}</td>
                      <td class="name">{{$bank->shortName}}</td>


                       <td width="80">

                       {{--  <a href="javascript:;" class="view-modal" bankId="{{$bank->id}}" >
                            <i class="fa fa-eye" aria-hidden="true"></i>
                          </a>&nbsp;  --}}
                          <a href="javascript:;" class="edit-modal" bankId="{{$bank->id}}">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;

                        @php
                          if (in_array($bank->id, $foreignBankIds)) {
                            $canDelete = 0;
                          }
                          else{
                            $canDelete = 1;
                          }   
                        @endphp

                        <a href="javascript:;" class="delete-modal" bankId="{{$bank->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
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
<div class="col-md-2"></div>



{{-- Edit Modal --}}
        <div id="editModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update Bank</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">
                                <div class="col-md-12" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    {!! Form::hidden('accCloseId',null,['id'=>'EMbankId']) !!}

                                        <div class="form-group">
                                            {!! Form::label('bankName', 'Bank/Donor Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('bankName', null,['id'=>'EMbankName','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                                <p id='bankNamee' style="color:red;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('bankShortName', 'Bank/Donor Short Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('bankShortName', null,['id'=>'EMbankShortName','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                                <p id='bankShortNamee' style="color:red;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                        {!! Form::label('type', 'Type:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                        @php
                                            $typeList = array(''=>'Select Type','0'=>'Bank','1'=>'Donor');
                                        @endphp
                                                
                                            {!! Form::select('type', $typeList,null, array('class'=>'form-control', 'id' => 'EMtype')) !!}
                                            <p id='typee' style="color:red;"></p>
                                        </div>
                                </div> 

                                       </div>
                                       </div>
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
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
        </div>
        <div class="modal-body">
          <h2>Are You Confirm to Delete This Record?</h2>

          <div class="modal-footer">
            <input id="DMbakId" type="hidden" name="DMbakId" value="">
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


    /*Edit Modal*/
    $(document).on('click', '.edit-modal', function() {
      
      var bankId = $(this).attr('bankId');
      var csrf = "{{csrf_token()}}";
      
      $("#EMbankId").val(bankId);

      $.ajax({
        url: './gnrGetBankInfo',
        type: 'POST',
        dataType: 'json',
        data: {bankId: bankId, _token: csrf},
      })
      .done(function(bank) {
        
        $("#EMbankName").val(bank.name);
        $("#EMbankShortName").val(bank.shortName);
        $("#EMtype").val(bank.isDonor);

        $("#editModal").modal('show');

        console.log("success");
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      
    });/*End Edit Modal*/




    /*Update Data*/
    $("#updateButton").on('click',function() {
      var bankId = $("#EMbankId").val();
      var bankName = $("#EMbankName").val();
      var bankShortName = $("#EMbankShortName").val();
      var type = $("#EMtype").val();
      var csrf = "{{csrf_token()}}";

      $.ajax({
        url: './gnrEditBank',
        type: 'POST',
        dataType: 'json',
        data: {bankId: bankId, bankName: bankName, bankShortName: bankShortName,type: type, _token: csrf},
      })
      .done(function(data) {

         if (data.errors) {
            if (data.errors['bankName']) {
                $("#bankNamee").empty();
                $("#bankNamee").append('* '+data.errors['bankName']);
            }
            if (data.errors['bankShortName']) {
                $("#bankShortNamee").empty();
                $("#bankShortNamee").append('* '+data.errors['bankShortName']);
            }
            if (data.errors['type']) {
                $("#typee").empty();
                $("#typee").append('* '+data.errors['type']);
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
    /*End Update Data*/



    /*Delete Modal*/
    $(document).on('click', '.delete-modal', function() {
      var bankId = $(this).attr('bankId');

      $("#DMbakId").val(bankId);
      $("#deleteModal").modal('show');
      
    });
    /*End Delete Modal*/


    /*Delete Data*/
    $("#DMconfirmButton").on('click', function() {
      var bankId = $("#DMbakId").val();
      var csrf = "{{csrf_token()}}";

      $.ajax({
        url: './gnrDeleteBank',
        type: 'POST',
        dataType: 'json',
        data: {bankId: bankId,  _token: csrf},
      })
      .done(function(data) {
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




  });/*Ready*/
</script>


@include('dataTableScript')


<style type="text/css">
  #bankTable thead tr th{
    border-bottom: 1px solid white !important;
  }
  #bankTable tbody tr td.name{
    text-align: left;
    padding-left: 5px;
  }
</style>

@endsection

