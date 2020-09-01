@extends('layouts/gnr_layout')
@section('title', '| Project')
@section('content')
@include('successMsg')

@php
  $foreignProjectIds = DB::table('gnr_project_type')->distinct()->pluck('projectId')->toArray(); 
@endphp

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addProject/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Project</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PROJECT LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#gnrProjectView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="gnrProjectView">
            <thead>     
                  <tr>
                    <th width="32">SL#</th>
                    <th>Name</th>
                    <th>Project Code</th>
                    <th>Group Name</th>
                    <th>Company Name</th>
                    <th>Action</th>
                  </tr>
                    {{ csrf_field() }}
                </thead>
                <tbody> 
                  <?php $no=0; ?>
                  @foreach($projects as $project)
                    <tr class="item{{$project->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                      <td style='text-align: left; padding-left: 5px;'>{{$project->name}}</td>
                      <td>{{$project->projectCode}}</td>
                      <td>
                          <?php
                            $groupName = DB::table('gnr_group')->select('name')->where('id',$project->groupId)->first();
                          ?>
                          {{$groupName->name}}
                      </td>
                      <td>
                          <?php
                            $companyName = DB::table('gnr_company')->select('name')->where('id',$project->companyId)->first();
                          ?>
                          {{$companyName->name}}
                      </td>
                      <td class="text-center" width="80">
                        <a href="javascript:;" class="edit-modal" data-id="{{$project->id}}" data-name="{{$project->name}}" data-projectcode="{{$project->projectCode}}" data-groupid="{{$project->groupId}}" data-companyid="{{$project->companyId}}" data-slno="{{$no}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;

                        @php
                          if (in_array($project->id, $foreignProjectIds)) {
                            $canDelete = 0;
                          }
                          else{
                            $canDelete = 1;
                          }   
                        @endphp

                        <a href="javascript:;" class="delete-modal" data-id="{{$project->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
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
<!--           <button type="button" class="close" data-dismiss="modal">&times;</button>
-->            <h4 class="modal-title" style="clear:both"></h4>
      </div>
      <div class="modal-body">
        {!! Form::open(array('url' => '', 'id' => 'form1', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
          <!-- <input type = "hidden" name = "_token" value = ""> -->
          <div class="form-group hidden">
              {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
              {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
              {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text', 'readonly']) !!}
            </div>
          </div>
          <div class="form-group">
                {!! Form::label('name', 'Project Name:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
               {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text']) !!}
               <p id='namee' style="max-height:3px;"></p>
            </div>
          </div>
          <div class="form-group">
              {!! Form::label('projectCode', 'Project Code:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
                  {!! Form::text('projectCode', $value = null, ['class' => 'form-control', 'id' => 'projectCode', 'type' => 'text', 'placeholder' => 'Enter project code']) !!}
                  <p id='projectCodee' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group">
              {!! Form::label('groupId', 'Group Name:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
                  <?php 
                      $groupId = array('' => 'Please Select Group Name') + DB::table('gnr_group')->pluck('name','id')->all(); 
                  ?>      
                  {!! Form::select('groupId', ($groupId), null, array('class'=>'form-control', 'id' => 'groupId')) !!}
                  <p id='groupIde' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group">
              {!! Form::label('companyId', 'Company Name:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
                  <?php 
                      $companyId = array('' => 'Please Select Company Name') + DB::table('gnr_company')->pluck('name','id')->all(); 
                  ?>   
                  {!! Form::select('companyId', $companyId, null, array('class'=>'form-control', 'id' => 'companyId')) !!}
                  <p id='companyIde' style="max-height:3px;"></p>
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
          {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn',  'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}

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
    $('.modal-dialog').css('width','50%');
    $('.deleteContent').hide();
    $('.form-horizontal').show();
    $('#id').val($(this).data('id'));
    $('#slno').val($(this).data('slno'));
    $('#name').val($(this).data('name'));
    $('#projectCode').val($(this).data('projectcode'));
    $('#groupId').val($(this).data('groupid'));
    $('#companyId').val($(this).data('companyid'));
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
});
    // Edit Data (Modal and function edit data)
  $('.modal-footer').on('click', '.edit', function() {
  $.ajax({
      type: 'post',
      url: './editProjectItem',
      data: {
          '_token': $('input[name=_token]').val(),
          'id': $("#id").val(),
          'name': $('#name').val(),
          'projectCode': $('#projectCode').val(),
          'groupId': $('#groupId').val(),
          'companyId': $('#companyId').val(),
          'slno': $('#slno').val()
      },
      dataType: 'json',
      success: function( data ){
        //alert(JSON.stringify(data));
        if(data.errors){
           if (data.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red">'+data.errors.name+'</span>');
                return false;
            }
            if (data.errors['projectCode']) {
                $('#projectCodee').empty();
                $('#projectCodee').show();
                $('#projectCodee').append('<span class="errormsg" style="color:red">'+data.errors.projectCode+'</span>');
                return false;
            }
            if (data.errors['groupId']) {
                $('#groupIde').empty();
                $('#groupIde').append('<span class="errormsg" style="color:red">'+data.errors.groupId+'</span>');
                return false;
            }
            if (data.errors['companyId']) {
                $('#companyIde').empty();
                $('#companyIde').append('<span class="errormsg" style="color:red">'+data.errors.companyId+'</span>');
                return false;
            }
          }else{
        $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');
        //alert(JSON.stringify(data));
        $('.item' + data["project"].id).replaceWith(
                                    "<tr class='item" + data["project"].id + "'><td  class='text-center slNo'>" + data.slno +
                                                                    "</td><td class='hidden'>" + data["project"].id + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["project"].name + 
                                                                    "</td><td>" + data["project"].projectCode + 
                                                                    "</td><td>" + data["gnrGroupName"].name + 
                                                                    "</td><td>" + data["gnrCompanyName"].name + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["project"].id + "' data-name='" + data["project"].name + "' data-projectcode='" + data["project"].projectCode + "' data-groupid='" + data["project"].groupId + "'  data-companyid='" + data["project"].companyId + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["project"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
      }
            $("#groupId").val('');
            $("#name").val('');
            $("#companyId").val('');
      },
      error: function( data ){
            // Handle error  
            alert(_response.responseText);    
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
  $('.deleteContent').show();
  $('.form-horizontal').hide();
  $('#footer_action_button2').show();
  $('#footer_action_button').hide();
  $('#myModal').modal('show');
});

$('.modal-footer').on('click', '.delete', function() {
  $.ajax({
    type: 'post',
    url: './deleteProjectItem',
    data: {
      '_token': $('input[name=_token]').val(),
      'id': $('.id').text()
    },
    success: function(data) {
      $('.item' + $('.id').text()).remove();
    },
    error: function( data ){
            // Handle error  
            alert(_response.responseText);    
        }
  });
});

$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
            var projectCode = $("#projectCode").val();
            if(projectCode){$('#projectCodee').hide();}else{$('#projectCodee').show();}
});
$('select').on('change', function (e) {
    var groupId = $("#groupId").val();
    if(groupId){$('#groupIde').hide();}else{$('#groupIde').show();}
     var companyId = $("#companyId").val();
    if(companyId){$('#companyIde').hide();}else{$('#companyIde').show();}
});

});//ready function end
</script>