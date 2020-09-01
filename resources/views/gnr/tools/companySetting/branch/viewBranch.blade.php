@extends('layouts/gnr_layout')
@section('title', '| Branch')
@section('content')
@include('successMsg')


@php
  $branchIdsFromEmployeeTable = DB::table('hr_emp_org_info')->distinct()->pluck('branch_id_fk')->toArray();

  $branchIdsFromLedgerTable = array();
  $ledgers = DB::table('acc_account_ledger')->select('projectBranchId')->get();

  foreach ($ledgers as $ledger) {
    $projectBranchIdString = str_replace(["[","]","\""],"",$ledger->projectBranchId);
    $firstResultArray = explode(',',$projectBranchIdString);

    foreach ($firstResultArray as $firstResult) {
      $secondResultArray = explode(':',$firstResult);      
      array_push($branchIdsFromLedgerTable, (int) $secondResultArray[1]);
    }
  }
  
  $branchIdsFromLedgerTable = array_unique($branchIdsFromLedgerTable);
  $branchIdsFromLedgerTable = array_diff($branchIdsFromLedgerTable,[0]);

  $foreignBranchIds = array_merge($branchIdsFromEmployeeTable, $branchIdsFromLedgerTable);
  $foreignBranchIds = array_unique($foreignBranchIds);
  
@endphp






<div class="row">



<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addBranch/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Branch</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">BRANCH LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#gnrBranchView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="gnrBranchView">
                    <thead>
                        <tr>
                          <th width="80">SL#</th>
                          <th style="text-align:center">B. Code</th>
                          <th style="text-align:center">Branch Name</th>
                          <th style="text-align:center">Contact Person</th>
                          <th style="text-align:center">Phone</th>
                          <th style="text-align:center">Address</th>
                          <th style="text-align:center">B. Start Date</th>
                          <th style="text-align:center">S. Start Date</th>
                          <th style="text-align:center">L. Day End Close</th>
                          <th style="text-align:center">Status</th>
                          <th width="45" style="text-align:center">Action</th>
                        </tr>
                          {{ csrf_field() }}
                    </thead>
                    <tbody> 
                    <?php $no=0; ?>
                          @foreach($branchs as $branch)
                            <tr class="item{{$branch->id}}">
                              <td class="text-center">{{++$no}}</td>
                              <td>{{$branch->branchCode}}</td>
                              <td style="text-align:left">&nbsp;{{$branch->name}}</td>
                              <td style="text-align:left">&nbsp;{{$branch->contactPerson}}</td>
                              <td>{{$branch->phone}}</td>
                              <td style="text-align:left">&nbsp;{{$branch->address}}</td>
                              <td>
                                <?php
                                  $branchOpeningDate = $branch->branchOpeningDate;
                                  echo $branchOpeningDate = date('Y-m-d', strtotime($branchOpeningDate));
                                  ?>
                              </td>
                              <td>
                                <?php
                                  $softwareStartDate = $branch->softwareStartDate;
                                  echo $softwareStartDate = date('Y-m-d', strtotime($softwareStartDate));
                                  ?>
                              </td>
                              <td>1</td>
                              <td> 
                                  <?php
                                  $status = $branch->status;
                                  if($status==0){
                                    echo "<i style='background-color:red; border-radius:50%; 
                                                  font-size:16px; padding:3px; padding-right:5px;  padding-left:5px; color:white;' class='fa fa-times'
                                                  aria-hidden='true'></i>";
                                  }else{echo "<i style='background-color:green; border-radius:50%;
                                                  font-size:16px; padding:3px; color:white;' class='fa fa-check'
                                                  aria-hidden='true'></i>";
                                        }
                                  ?>
                              </td>
                              <td class="text-center" width="80">
                                <a href="javascript:;" class="edit-modal" data-id="{{$branch->id}}" data-name="{{$branch->name}}" data-branchcode="{{$branch->branchCode}}" data-groupid="{{$branch->groupId}}" data-companyid="{{$branch->companyId}}" data-projectid="{{$branch->projectId}}" data-projecttypeid="{{$branch->projectTypeId}}" data-contactperson="{{$branch->contactPerson}}" data-email="{{$branch->email}}" data-phone="{{$branch->phone}}" data-address="{{$branch->address}}" data-branchopeningdate="{{$branchOpeningDate}}" data-softwarestartdate="{{$softwareStartDate}}" data-status="{{$branch->status}}" data-slno="{{$no}}">
                                  <span class="glyphicon glyphicon-edit"></span>
                                </a>&nbsp


                                @php
                                  //  if (in_array($branch->id, $foreignBranchIds)) {
                                  //   $canDelete = 0;
                                  //  }
                                  //  else{
                                  //    $canDelete = 1;
                                  //  }   
                                $voucherId = DB::table('acc_voucher')->where('companyId',Auth::user()->company_id_fk)->where('branchId',$branch->id)->value('id');
                                  $employeeId = DB::table('gnr_employee')->where('company_id_fk',Auth::user()->company_id_fk)->where('branchId',$branch->id)->value('id');
                                @endphp

                                <a href="javascript:;" class="delete-modal" data-id="{{$branch->id}}" data-voucherId="{{$voucherId}}">
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


<div id="myModal" class="modal fade" style="margin-top:3%">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" style="clear:both"></h4>
      </div>
      <div class="modal-body">
        {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                       <!-- <input type = "hidden" name = "_token" value = ""> -->
                        {!! Form::text('id', $value = null, ['class' => 'form-control hidden', 'id' => 'id', 'type' => 'text']) !!}
                         {!! Form::text('slno', $value = null, ['class' => 'form-control hidden', 'id' => 'slno', 'type' => 'text']) !!}
                       <div class="row">
                    <div class="col-md-6">    
                            <div class="form-group">
                                {!! Form::label('name', 'Branch Name:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter branch name']) !!}
                                    <p id='namee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('branchCode', 'Branch Code:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('branchCode', $value = null, ['class' => 'form-control', 'id' => 'branchCode', 'type' => 'text', 'placeholder' => 'Enter branc code']) !!}
                                   <p id='branchCodee' style="max-height:3px;"></p>
                                </div>
                            </div>
                           
                            <div class="form-group">
                                {!! Form::label('projectId', 'Project Name:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    <?php 
                                        $projectId = array('' => 'Please Select Project Name') + DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->pluck('name','id')->all(); 
                                    ?>
                                    {!! Form::select('projectId', $projectId, null, array('class'=>'form-control', 'id' => 'projectId')) !!}
                                     <p id='projectIde' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group" hidden>
                                {!! Form::label('projectTypeId', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                <?php 
                                    $projectTypeId = array('' => 'Please Select Project Type') + DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->pluck('name','id')->all(); 
                                ?>
                                    {!! Form::select('projectTypeId', $projectTypeId, null, array('class'=>'form-control', 'id' => 'projectTypeId')) !!}
                                    <p id='projectTypeIde' style="max-height:3px;"></p>
                                </div>
                            </div>  
                            <div class="form-group">
                                {!! Form::label('contactPerson', 'Contact Person:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('contactPerson', $value = null, ['class' => 'form-control', 'id' => 'contactPerson', 'type' => 'text', 'placeholder' => 'Enter contact person name']) !!}
                                    <p id='contactPersone' style="max-height:3px;"></p>
                                </div>
                            </div>   
                    </div>
                    <div class="col-md-6">    

                            <div class="form-group">
                                {!! Form::label('phone', 'Mobile:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('phone', $value = null, ['class' => 'form-control', 'id' => 'phone', 'type' => 'text', 'placeholder' => 'Enter phone number']) !!}
                                   <p id='phonee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('email', 'Email:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'text', 'placeholder' => 'Enter email address']) !!}
                                    <p id='emaile' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('address', 'Address:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::textarea('address', $value = null, ['class' => 'form-control', 'id' => 'address', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Enter address']) !!}
                                    <p id='addresse' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('branchOpeningDate', 'B. Opening Date:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('branchOpeningDate', $value = null, ['class' => 'form-control', 'id' => 'branchOpeningDate', 'type' => 'text', 'placeholder' => 'Enter Branch Opening Date:']) !!}
                                   <p id='phonee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('softwareStartDate', 'S. Opening Date:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('softwareStartDate', $value = null, ['class' => 'form-control', 'id' => 'softwareStartDate', 'type' => 'text', 'placeholder' => 'Enter Software Opening Date']) !!}
                                    <p id='softwareStartDatee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('status', 'Status:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">     
                                    {!! Form::select('status', array('1'=>'Active', '0'=>'Inactive'), null, array('class'=>'form-control', 'id' => 'status')) !!}
                                   <p id='statuse' style="max-height:3px;"></p>
                                </div>
                            </div>
                    </div>
                </div> 
        {!! Form::close()  !!}
          <div class="deleteContent" style="padding-bottom:20px;">
            <h4>You are about to delete this item this procedure is irreversible !</h4>
            <h4>Do you want to proceed ?</h4> 
            <span class="hidden id"></span>
          </div>
        <div class="modal-footer">
            <p id="MSGE" class="pull-left" style="color:red"></p>
            <p id="MSGS" class="pull-left" style="color:green"></p>
         {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'id' => 'footer_action_button'] ) !!}
          {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}

         {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
        </div>
      </div>
    </div>
  </div>
</div>
@include('dataTableScript')
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
$( document ).ready(function() {

$(function() {
    $( "#branchOpeningDate" ).datepicker({
      changeMonth: true,
      changeYear: true
    });
  });
 $(function() {
    $( "#softwareStartDate" ).datepicker({
      changeMonth: true,
      changeYear: true
    });
  });

$(document).on('click', '.edit-modal', function() {
    $('.errormsg').empty();
    $('#MSGE').empty();
    $('#MSGS').empty();
    $('#footer_action_button').text(" Update");
    $('#footer_action_button').addClass('glyphicon glyphicon-check');
    //$('#footer_action_button').removeClass('glyphicon-trash');
    $('#footer_action_button_dismis').text(" Close");
    $('#footer_action_button_dismis').addClass('glyphicon glyphicon-remove');
    $('.actionBtn').addClass('btn-success');
    $('.actionBtn').removeClass('btn-danger');
    $('.actionBtn').addClass('edit');
    $('.modal-title').text('Update Data');
    $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
    $('.modal-dialog').css('width','70%');
    $('.deleteContent').hide();
    $('.form-horizontal').show();
    $('#id').val($(this).data('id'));
    $('#slno').val($(this).data('slno'));
    $('#name').val($(this).data('name'));
    $('#branchCode').val($(this).data('branchcode'));
    $('#groupId').val($(this).data('groupid'));
    $('#companyId').val($(this).data('companyid'));
    $('#projectId').val($(this).data('projectid'));
    $('#projectTypeId').val($(this).data('projecttypeid'));
    $('#contactPerson').val($(this).data('contactperson'));
    $('#email').val($(this).data('email'));
    $('#phone').val($(this).data('phone'));
    $('#address').val($(this).data('address'));
    $('#branchOpeningDate').val($(this).data('branchopeningdate'));
    $('#softwareStartDate').val($(this).data('softwarestartdate'));
    $('#status').val($(this).data('status'));
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
});
    // Edit Data (Modal and function edit data)
  $('.modal-footer').on('click', '.edit', function() {
  $.ajax({
      type: 'post',
      url: './editBranchItem',
      data: $('form').serialize(),
      dataType: 'json',
      success: function( data ){
        //alert(JSON.stringify(data.errors.branchCode));
        location.reload();
        if(data.errors){
          if (data.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+data.errors.name+'</span>');
                return false;
            }
            if (data.errors['branchCode']) {
                $('#branchCodee').empty();
                 $('#branchCodee').show();
                $('#branchCodee').append('<span class="errormsg" style="color:red;">'+data.errors.branchCode+'</span>');  
                return false;
            } 
            // if (data.errors['groupId']) {
            //     $('#groupIde').empty();
            //     $('#groupIde').append('<span class="errormsg" style="color:red;">'+data.errors.groupId+'</span>');
            //      return false;
            // }
            // if (data.errors['companyId']) {
            //     $('#companyIde').empty();
            //     $('#companyIde').append('<span class="errormsg" style="color:red;">'+data.errors.companyId+'</span>');
            //     return false;
            // }
            if (data.errors['projectId']) {
                $('#projectIde').empty();
                $('#projectIde').append('<span class="errormsg" style="color:red;">'+data.errors.projectId+'</span>');
                return false;
            }
            if (data.errors['projectTypeId']) {
                $('#projectTypeIde').empty();
                $('#projectTypeIde').append('<span class="errormsg" style="color:red;">'+data.errors.projectTypeId+'</span>');
                return false;
            }
           
        }
        else{
        $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');
        //alert(JSON.stringify(data));
        $('.item' + data["branch"].id).replaceWith(
                                    "<tr class='item" + data["branch"].id + "'><td  class='text-center slNo'>" + data.slno + 
                                                                    "</td><td class='hidden'>" + data["branch"].id + 
                                                                    "</td><td>" + data["branch"].branchCode +
                                                                    "</td><td style='text-align:left'>\xa0" + data["branch"].name+  
                                                                    "</td><td style='text-align:left'>\xa0" + data["branch"].contactPerson + 
                                                                    "</td><td>" + data["branch"].phone + 
                                                                    "</td><td style='text-align:left'>\xa0" + data["branch"].address + 
                                                                    "</td><td>" + data["branch"].branchOpeningDate + 
                                                                    "</td><td>" + data["branch"].softwareStartDate + 
                                                                    "</td><td>"+'1'+
                                                                    "</td><td>" + data.status + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["branch"].id + "' data-name='" + data["branch"].name + "' data-branchcode='" + data["branch"].branchCode + "' data-groupid='" + data["branch"].groupId + "'  data-companyid='" + data["branch"].companyId + "' data-projectid='" + data["branch"].projectId + "' data-projecttypeid='" + data["branch"].projectTypeId + "' data-contactperson='" + data["branch"].contactPerson + "' data-email='" + data["branch"].email + "' data-phone='" + data["branch"].phone + "' data-address='" + data["branch"].address + "' data-branchopeningdate='" + data["branch"].branchOpeningDate + "' data-softwarestartdate='" + data["branch"].softwareStartDate + "' data-status='" + data["branch"].status + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["branch"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
     }
            $("#name").val('');
            $("#branchCode").val('');
            $("#groupId").val('');
            $("#companyId").val('');
            $("#projectId").val('');
            $("#projectTypeId").val('');
            $("#branchOpeningDate").val('');
            $("#softwareStartDate").val('');
      },
      error: function( data ){
            // Handle error  
            //alert('hi');    
        }
  });
});

//delete function
$(document).on('click', '.delete-modal', function() {
  $('#MSGE').empty();
  $('#MSGS').empty();
  $('#footer_action_button2').text(" Yes");
  $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
  //$('#footer_action_button').addClass('glyphicon-trash');
  $('#footer_action_button_dismis').text(" No");
  $('#footer_action_button_dismis').removeClass('glyphicon glyphicon-remove');
  $('.actionBtn').removeClass('edit');
  $('.actionBtn').removeClass('btn-success');
  $('.actionBtn').addClass('btn-danger');
  $('.actionBtn').addClass('delete');
  $('.modal-title').text('Delete');
  $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
  $('.modal-dialog').css('width','50%');
  $('.id').text($(this).data('id'));
  $('#delvoucherId').text('voucherId : '+$(this).data('voucherId'));
  $('.deleteContent').show();
  $('.form-horizontal').hide();
  $('#footer_action_button2').show();
  $('#footer_action_button').hide();
  $('#myModal').modal('show');
});

$('.modal-footer').on('click', '.delete', function() {
  $.ajax({
    type: 'post',
    url: './deleteBranchItem',
    data: {
      '_token': $('input[name=_token]').val(),
      'id': $('.id').text()
    },
    success: function(data) {
          if (data.responseTitle=='Success!') {
              toastr.success(data.responseText, data.responseTitle, opts);
              $('.item' + $('.id').text()).remove();                        
          }
          else if(data.responseTitle=='Warning!'){
              toastr.warning(data.responseText, data.responseTitle, opts);                        
          }
          // $('#myModal').modal('hide');

      }
  });
});

$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
            var branchCode = $("#branchCode").val();
            if(branchCode){$('#branchCodee').hide();}else{$('#branchCodee').show();}
             var contactPerson = $("#contactPerson").val();
            if(contactPerson){$('#contactPersone').hide();}else{$('#contactPersone').show();}
             var email = $("#email").val();
            if(email){$('#emaile').hide();}else{$('#emaile').show();}
             var phone = $("#phone").val();
            if(phone){$('#phonee').hide();}else{$('#phonee').show();}
            var email = $("#email").val();
            if(emaile){$('#emaile').hide();}else{$('#emaile').show();}
});
$("textarea").keyup(function(){
    var address = $("#address").val();
    if(address){$('#addresse').hide();}else{$('#addresse').show();}
});
$('select').on('change', function (e) {
    var groupId = $("#groupId").val();
    if(groupId){$('#groupIde').hide();}else{$('#groupIde').show();}
     var companyId = $("#companyId").val();
    if(companyId){$('#companyIde').hide();}else{$('#companyIde').show();}
     var projectId = $("#projectId").val();
    if(projectId){$('#projectIde').hide();}else{$('#projectIde').show();}
     var projectTypeId = $("#projectTypeId").val();
    if(projectTypeId){$('#projectTypeIde').hide();}else{$('#projectTypeIde').show();}
    
});

});//ready function end

$(document).ready(function(){

$("#projectId").change(function(){
        $("#projectTypeId").empty();
        //$("#projectTypeId").prepend('<option selected="selected" value="">Please Select</option>');
        var projectId = $('#projectId').val();
        
    $.ajax({
        type: 'post',
        url: './projectIdSendGetPTypeId',
        data: {
            '_token': $('input[name=_token]').val(),
            'projectId' : projectId
        },
        dataType: 'json',   
        success: function( _response ){
            //alert(JSON.stringify(_response));
        $.each(_response, function( index, value ){
                    $('#projectTypeId').append("<option value='"+index+"'>"+value+"</option>");
                    //alert(value);
                });
                }
            });
        });
});
</script>