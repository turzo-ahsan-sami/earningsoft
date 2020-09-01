@extends('layouts/gnr_layout')
@section('title', '| Department')
@section('content')
@include('successMsg')

@php
  $departmentIdsArray = DB::table('gnr_room')->pluck('departmentId')->toArray();

  $departmentForeignIds = array();

  foreach ($departmentIdsArray as $departmentIdArray) {
    $departmentIdArrayString = str_replace(['[',']','"'], '', $departmentIdArray);
    $departmentIds = explode(',', $departmentIdArrayString);
    foreach ($departmentIds as $departmentId) {
      array_push($departmentForeignIds,(int) $departmentId);
    }
  }

  $departmentForeignIds = array_unique($departmentForeignIds);

@endphp

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addDepartmentF/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Department</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">DEPARTMENT LIST</font></h1>
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
                    <th width="80">SL#</th>
                    <th>Department Name</th>
                    <th>Action</th>
                  </tr>
                    {{ csrf_field() }}
                </thead>
                <tbody> 
                  <?php $no=0; ?>
                  @foreach($departments as $department)
                    <tr class="item{{$department->id}}">
                      <td class="text-center">{{++$no}}</td>
                      <td style="text-align: left; padding-left: 5px;">{{$department->name}}</td>
                      <td>
                        <a href="javascript:;" class="edit-modal" data-id="{{$department->id}}" data-name="{{$department->name}}" data-slno="{{$no}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;

                        @php
                            // if (in_array($department->id, $departmentForeignIds)) {
                            //   $canDelete = 0;
                            // }
                            // else{
                            //   $canDelete = 1;
                            // }   
                            $employeeId = DB::table('gnr_employee')->where('company_id_fk',Auth::user()->company_id_fk)->where('department_id_fk',$department->id)->value('id');
                          @endphp
                        <a href="javascript:;" class="delete-modal" data-id="{{$department->id}}" data-employeeId="{{$employeeId}}">
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
                {!! Form::label('name', 'Department Name:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
               {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text']) !!}
               <p id='namee' style="max-height:3px;"></p>
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
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
});
    // Edit Data (Modal and function edit data)
  $('.modal-footer').on('click', '.edit', function() {
  $.ajax({
      type: 'post',
      url: './editDepartmentItem',
      data: {
          '_token': $('input[name=_token]').val(),
          'id': $("#id").val(),
          'name': $('#name').val(),
          'slno': $('#slno').val()
      },
      dataType: 'json',
      success: function( data ){
        //alert(JSON.stringify(data));
        if(data.errors){
           if (data.errors['name']) {
                $('#namee').empty();
                $('#namee').show();
                $('#namee').append('<span class="errormsg" style="color:red">'+data.errors.name+'</span>');
                return false;
            }
          }else{
        $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');
        $('.item' + data["department"].id).replaceWith(
                                    "<tr class='item" + data["department"].id + "'><td  class='text-center slNo'>" + data.slno +
                                                                    "</td><td class='hidden'>" + data["department"].id + 
                                                                    "</td><td>" + data["department"].name + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["department"].id + "' data-name='" + data["department"].name + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["department"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
        location.reload();
      }
        $("#name").val('');
      },
      error: function( data ){
            // Handle error  
            alert('error');    
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
  $('#delEmployeeId').text('employeeId : '+$(this).data('employeeId'));
  $('#footer_action_button').hide();
  $('#myModal').modal('show');
});

$('.modal-footer').on('click', '.delete', function() {
    $.ajax({
      type: 'post',
      url: './deleteDepartmentItem',
      data: {
        '_token': $('input[name=_token]').val(),
        'id': $('.id').text()
      },
      // success: function(data) {
      //   $('.item' + $('.id').text()).remove();
      // },
      // error: function( data ){
      //         // Handle error  
      //         alert(_response.responseText);    
      //     }

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
    
});
$('select').on('change', function (e) {
          var groupId = $("#groupId").val();
          if(groupId){$('#groupIde').hide();}else{$('#groupIde').show();}
});

});//ready function end
</script>