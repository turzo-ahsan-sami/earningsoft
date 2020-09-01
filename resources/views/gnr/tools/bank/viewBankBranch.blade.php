@extends('layouts/gnr_layout')
@section('title', '| Bank/Donor')
@section('content')

@php
  $foreignBankBranchIds = DB::table('acc_loan_register_account')->distinct()->pluck('bankBranchId_fk')->toArray();
@endphp


<div class="row">
<div class="col-md-1"></div>
<div class="col-md-10">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('gnrAddBankBranch/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Bank/Donor Branch</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">BANK/DONOR BRANCH LIST</font></h1>
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
                        <th>Bank/Donor</th>                        
                        <th>Contact Person</th>                        
                        <th>Mobile Number</th>                        
                        <th>E-mail</th>                        
                        <th>Action</th>
                      </tr>
                      
                    </thead>
                    <tbody>
                    @foreach($branches as $index => $branch)
                    @php
                      $bankName = DB::table('gnr_bank')->where('id',$branch->bankId_fk)->value('name');
                      
                    @endphp
                    <tr>
                      <td>{{$index+1}}</td>
                      <td class="name">{{$branch->name}}</td>
                      <td class="name">{{$bankName}}</td>
                      <td class="name">{{$branch->contactPerson}}</td>
                      <td>{{$branch->contactPersonMobileNumber}}</td>
                      <td>{{$branch->contactPersonEmail}}</td>

                       <td width="80">

                        <a href="javascript:;" class="view-modal" branchId="{{$branch->id}}" >
                            <i class="fa fa-eye" aria-hidden="true"></i>
                          </a>&nbsp; 
                          <a href="javascript:;" class="edit-modal" branchId="{{$branch->id}}">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;

                         @php
                          if (in_array($branch->id, $foreignBankBranchIds)) {
                            $canDelete = 0;
                          }
                          else{
                            $canDelete = 1;
                          }   
                        @endphp

                        <a href="javascript:;" class="delete-modal" branchId="{{$branch->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
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
<div class="col-md-1"></div>



{{-- View Modal --}}
        <div id="viewModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Bank/Donor Branch Info</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">   

                            <div class="form-horizontal form-groups">
                            
                                <div class="col-md-6">

                                <div class="form-group">
                                        {!! Form::label('branchName', 'Branch Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                                                
                                            {!! Form::text('branchName', null, array('class'=>'form-control', 'id' => 'VMbranchName','readonly')) !!}
                                            
                                        </div>
                                </div>
                                
                                <div class="form-group">
                                        {!! Form::label('bank', 'Bank/Donor:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                                
                                            {!! Form::text('bank', null, array('class'=>'form-control', 'id' => 'VMbank','readonly')) !!}
                                            
                                        </div>
                                </div>


                                 <div class="form-group">
                                    {!! Form::label('telephoneNumber', 'Tel. Number:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">                                                
                                        {!! Form::text('telephoneNumber', null, array('class'=>'form-control', 'id' => 'VMtelephoneNumber','readonly')) !!}
                                        
                                    </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('branchEmail', 'E-mail:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                                                
                                            {!! Form::text('branchEmail', null, array('class'=>'form-control', 'id' => 'VMbranchEmail','readonly')) !!}
                                            
                                        </div>
                                </div> 


                                <div class="form-group">
                                        {!! Form::label('address', 'Address:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                                                
                                            {!! Form::textArea('address', null, array('class'=>'form-control', 'id' => 'VMaddress','rows'=>'2','readonly')) !!}
                                            
                                        </div>
                                </div>  
                                


                                 
                            </div>


                            <div class="col-md-6">

                            <div class="form-group">
                                        {!! Form::label('contactPerson', 'Contact Person:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                                                
                                            {!! Form::text('contactPerson', null, array('class'=>'form-control', 'id' => 'VMcontactPerson','readonly')) !!}
                                            
                                        </div>
                                </div>


                                 <div class="form-group">
                                        {!! Form::label('designation', 'Designation:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                                                
                                            {!! Form::text('designation', null, array('class'=>'form-control', 'id' => 'VMdesignation','readonly')) !!}
                                            
                                        </div>
                                </div>

                            <div class="form-group">
                                    {!! Form::label('contactPersonTelephoneNumber', 'Tel. Number:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">                                                
                                        {!! Form::text('contactPersonTelephoneNumber', null, array('class'=>'form-control', 'id' => 'VMcontactPersonTelephoneNumber','readonly')) !!}
                                        
                                    </div>
                            </div>

                            <div class="form-group">
                                    {!! Form::label('contactPersonMobileNumber', 'Mobile Number:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">                                                
                                        {!! Form::text('contactPersonMobileNumber', null, array('class'=>'form-control', 'id' => 'VMcontactPersonMobileNumber','readonly')) !!}
                                        
                                    </div>
                            </div>

                                 <div class="form-group">
                                        {!! Form::label('contactPersonEmail', 'E-mail:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                                                
                                            {!! Form::text('contactPersonEmail', null, array('class'=>'form-control', 'id' => 'VMcontactPersonEmail','readonly')) !!}
                                            
                                        </div>
                                </div> 



                            </div>
                            </div>
                            </div>

                        </div>{{--row--}}

                        {{-- View ModalFooter--}}
                        <div class="modal-footer">
                        
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal"  type="button"><span> Close</span></button>
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
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update Branch Info</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">
                            

                            {!! Form::open(['url'=>'','class'=>'form-horizontal form-groups']) !!}

                            {!! Form::hidden('branchId',null,['id'=>'EMbranchId']) !!}
                              
                            
                                <div class="col-md-6">

                                <div class="form-group">
                                        {!! Form::label('branchName', 'Branch Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                                                
                                            {!! Form::text('branchName', null, array('class'=>'form-control', 'id' => 'EMbranchName')) !!}
                                            <p id='branchNamee' class="error" style="color:red;"></p>
                                        </div>
                                </div>
                                
                                <div class="form-group">
                                        {!! Form::label('bank', 'Bank/Donor:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">

                                        @php
                                            $banks = array(''=>'Select Bank/Donor') + DB::table('gnr_bank')->pluck('name','id')->toArray();
                                        @endphp
                                                
                                            {!! Form::select('bank', $banks,null, array('class'=>'form-control', 'id' => 'EMbank')) !!}
                                            <p id='banke' class="error" style="color:red;"></p>
                                        </div>
                                </div>


                                 <div class="form-group">
                                    {!! Form::label('telephoneNumber', 'Tel. Number:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">                                                
                                        {!! Form::text('telephoneNumber', null, array('class'=>'form-control', 'id' => 'EMtelephoneNumber')) !!}
                                        <p id='telephoneNumbere' class="error" style="color:red;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('branchEmail', 'E-mail:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                                                
                                            {!! Form::text('branchEmail', null, array('class'=>'form-control', 'id' => 'EMbranchEmail')) !!}
                                            <p id='branchEmaile' class="error" style="color:red;"></p>
                                        </div>
                                </div> 


                                <div class="form-group">
                                        {!! Form::label('address', 'Address:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                                                
                                            {!! Form::textArea('address', null, array('class'=>'form-control', 'id' => 'EMaddress','rows'=>'2')) !!}
                                            <p id='addresse' class="error" style="color:red;"></p>
                                        </div>
                                </div>  
                                

                               {{--  <div class="form-group">
                                        {!! Form::label('division', 'Division:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                        @php
                                            $divisions = array(''=>'Select Division') + DB::table('division')->pluck('division_name','id')->toArray();
                                        @endphp                                                
                                            {!! Form::select('division', $divisions,null, array('class'=>'form-control', 'id' => 'EMdivision')) !!}
                                            <p id='divisione' class="error" style="color:red;"></p>
                                        </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('district', 'District:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                        @php
                                            $districts = array(''=>'Select District') + DB::table('district')->pluck('district_name','id')->toArray();
                                        @endphp                                                
                                            {!! Form::select('district', $districts,null, array('class'=>'form-control', 'id' => 'EMdistrict')) !!}
                                            <p id='districte' class="error" style="color:red;"></p>
                                        </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('upazilla', 'Upazilla:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                        @php
                                            $upazillas = array(''=>'Select Upazilla') + DB::table('upzilla')->pluck('upzilla_name','id')->toArray();
                                        @endphp                                                
                                            {!! Form::select('upazilla', $upazillas,null, array('class'=>'form-control', 'id' => 'EMupazilla')) !!}
                                            <p id='upazillae' class="error" style="color:red;"></p>
                                        </div>
                                </div> --}}
                            

                                

                                 
                            </div>


                            <div class="col-md-6">

                            <div class="form-group">
                                        {!! Form::label('contactPerson', 'Contact Person:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                                                
                                            {!! Form::text('contactPerson', null, array('class'=>'form-control', 'id' => 'EMcontactPerson')) !!}
                                            <p id='contactPersone' class="error" style="color:red;"></p>
                                        </div>
                                </div>


                                 <div class="form-group">
                                        {!! Form::label('designation', 'Designation:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                                                
                                            {!! Form::text('designation', null, array('class'=>'form-control', 'id' => 'EMdesignation')) !!}
                                            <p id='designatione' class="error" style="color:red;"></p>
                                        </div>
                                </div>

                            <div class="form-group">
                                    {!! Form::label('contactPersonTelephoneNumber', 'Tel. Number:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">                                                
                                        {!! Form::text('contactPersonTelephoneNumber', null, array('class'=>'form-control', 'id' => 'EMcontactPersonTelephoneNumber')) !!}
                                        <p id='contactPersonTelephoneNumbere' class="error" style="color:red;"></p>
                                    </div>
                            </div>

                            <div class="form-group">
                                    {!! Form::label('contactPersonMobileNumber', 'Mobile Number:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">                                                
                                        {!! Form::text('contactPersonMobileNumber', null, array('class'=>'form-control', 'id' => 'EMcontactPersonMobileNumber')) !!}
                                        <p id='contactPersonMobileNumbere' class="error" style="color:red;"></p>
                                    </div>
                            </div>

                                 <div class="form-group">
                                        {!! Form::label('contactPersonEmail', 'E-mail:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                                                
                                            {!! Form::text('contactPersonEmail', null, array('class'=>'form-control', 'id' => 'EMcontactPersonEmail')) !!}
                                            <p id='contactPersonEmaile' class="error" style="color:red;"></p>
                                        </div>
                                </div> 



                            </div>
                            {!! Form::close() !!}
                            </div>

                        </div>{{--row--}}

                        {{-- Edit ModalFooter--}}
                        <div class="modal-footer">
                        <button id="updateButton" class="btn actionBtn glyphicon glyphicon-check btn-success edit" paymentId="0" type="button"> Update</button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal"  type="button"><span> Close</span></button>
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
            <input id="DMbranchId" type="hidden" name="DMbranchId" value="">
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


       /*View Modal*/
    $(document).on('click', '.view-modal', function() {
      
      var branchId = $(this).attr('branchId');
      var csrf = "{{csrf_token()}}";
      
      

      $.ajax({
        url: './gnrGetBankBranchInfo',
        type: 'POST',
        dataType: 'json',
        data: {branchId: branchId, _token: csrf},
      })
      .done(function(data) {

        
        $("#VMbranchName").val(data['branch'].name);
        $("#VMbank").val(data['bank'].name);
        $("#VMtelephoneNumber").val(data['branch'].telephoneNumber);
        $("#VMbranchEmail").val(data['branch'].branchEmail);
        $("#VMaddress").val(data['branch'].address);
        /*if (data['division']!=null) {
          $("#EMdivision").val(data['division'].id);  
        }

        if (data['district']!=null) {
          $("#EMdistrict").val(data['district'].id);
        }

        if (data['upazilla']!=null) {
          $("#EMupazilla").val(data['upazilla'].id);
        }*/
        

        $("#VMcontactPerson").val(data['branch'].contactPerson);
        $("#VMdesignation").val(data['branch'].contactPersonDesiganation);
        $("#VMcontactPersonTelephoneNumber").val(data['branch'].contactPersonTelephoneNumber);
        $("#VMcontactPersonMobileNumber").val(data['branch'].contactPersonMobileNumber);
        $("#VMcontactPersonEmail").val(data['branch'].contactPersonEmail);

        $("#viewModal").modal('show');

        console.log("success");
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      
    });/*End View Modal*/


    /*Edit Modal*/
    $(document).on('click', '.edit-modal', function() {

      
      $('.error').empty();
      
      var branchId = $(this).attr('branchId');
      var csrf = "{{csrf_token()}}";
      
      $("#EMbranchId").val(branchId);

      $.ajax({
        url: './gnrGetBankBranchInfo',
        type: 'POST',
        dataType: 'json',
        data: {branchId: branchId, _token: csrf},
      })
      .done(function(data) {
        
        $("#EMbranchName").val(data['branch'].name);
        $("#EMbank").val(data['bank'].id);
        $("#EMtelephoneNumber").val(data['branch'].telephoneNumber);
        $("#EMbranchEmail").val(data['branch'].branchEmail);
        $("#EMaddress").val(data['branch'].address);
        /*if (data['division']!=null) {
          $("#EMdivision").val(data['division'].id);  
        }

        if (data['district']!=null) {
          $("#EMdistrict").val(data['district'].id);
        }

        if (data['upazilla']!=null) {
          $("#EMupazilla").val(data['upazilla'].id);
        }*/

        

        $("#EMcontactPerson").val(data['branch'].contactPerson);
        $("#EMdesignation").val(data['branch'].contactPersonDesiganation);
        $("#EMcontactPersonTelephoneNumber").val(data['branch'].contactPersonTelephoneNumber);
        $("#EMcontactPersonMobileNumber").val(data['branch'].contactPersonMobileNumber);
        $("#EMcontactPersonEmail").val(data['branch'].contactPersonEmail);
        
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
      
      $.ajax({
        url: './gnrEditBankBranch',
        type: 'POST',
        dataType: 'json',
        data: $('form').serialize(),
      })
      .done(function(data) {

        //alert(JSON.stringify(data));

         if (data.errors) {
            if (data.errors['bank']) {
                        $("#banke").empty();
                        $("#banke").append('* '+data.errors['bank']);
                    }
                    if (data.errors['branchName']) {
                        $("#branchNamee").empty();
                        $("#branchNamee").append('* '+data.errors['branchName']);
                    }
                    if (data.errors['telephoneNumber']) {
                        $("#telephoneNumbere").empty();
                        $("#telephoneNumbere").append('* '+data.errors['telephoneNumber']);
                    }
                   
                    if (data.errors['branchEmail']) {
                        $("#branchEmaile").empty();
                        $("#branchEmaile").append('* '+data.errors['branchEmail']);
                    }
                    if (data.errors['contactPerson']) {
                        $("#contactPersone").empty();
                        $("#contactPersone").append('* '+data.errors['contactPerson']);
                    }
                    if (data.errors['designation']) {
                        $("#designatione").empty();
                        $("#designatione").append('* '+data.errors['designation']);
                    }
                    if (data.errors['contactPersonTelephoneNumber']) {
                        $("#contactPersonTelephoneNumbere").empty();
                        $("#contactPersonTelephoneNumbere").append('* '+data.errors['contactPersonTelephoneNumber']);
                    }
                    if (data.errors['contactPersonMobileNumber']) {
                        $("#contactPersonMobileNumbere").empty();
                        $("#contactPersonMobileNumbere").append('* '+data.errors['contactPersonMobileNumber']);
                    }
                    if (data.errors['contactPersonEmail']) {
                        $("#contactPersonEmaile").empty();
                        $("#contactPersonEmaile").append('* '+data.errors['contactPersonEmail']);
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
      var branchId = $(this).attr('branchId');

      $("#DMbranchId").val(branchId);
      $("#deleteModal").modal('show');
      
    });
    /*End Delete Modal*/


    /*Delete Data*/
    $("#DMconfirmButton").on('click', function() {
      var branchId = $("#DMbranchId").val();
      var csrf = "{{csrf_token()}}";

      $.ajax({
        url: './gnrDeleteBankBranch',
        type: 'POST',
        dataType: 'json',
        data: {branchId: branchId,  _token: csrf},
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


    /*Empty Error Message when filed*/
    $(document).on('input', 'input', function() {
        $(this).closest('div').find('.error').empty();
    });
    $(document).on('change', 'select', function() {
        $(this).closest('div').find('.error').empty();
    });
    /*End Empty Error Message when filed*/

    $("#EMtelephoneNumber,#EMcontactPersonMobileNumber,#EMcontactPersonTelephoneNumber").on('input',function() {            
            this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
        });


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

