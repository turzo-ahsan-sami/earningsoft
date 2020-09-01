@extends('layouts/gnr_layout')
@section('title', '| Room')
@section('content')
@include('successMsg')

@php
  $foreignRoomIds = DB::table('inv_tra_use')->distinct()->pluck('roomId')->toArray(); 
@endphp

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addRoomF/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Department</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">ROOM LIST</font></h1>
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
                    <th>Room Name</th>
                    <th>Department Name</th>
                    <th>Action</th>
                  </tr>
                    {{ csrf_field() }}
                </thead>
                <tbody> 
                  <?php $no=0; ?>
                  @foreach($rooms as $room)
                    <tr class="item{{$room->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                      <td>{{$room->name}}</td>
                      <td>
                          <?php $arraySize = count($room->departmentId); $j=0; ?>
                          @foreach($room->departmentId as $departmentId)
                            {{App\gnr\GnrDepartment::where('id',$departmentId)->value('name')}}
                            <?php $j++; 
                              if($j<$arraySize){
                                echo '&nbsp/&nbsp';
                              }
                            ?>
                          @endforeach
                      </td>
                      
                      <td class="text-center" width="80">
                        <a href="javascript:;" class="edit-modal" data-id="{{$room->id}}" data-name="{{$room->name}}" data-slno="{{$no}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp

                        @php
                        if (in_array($room->id, $foreignRoomIds)) {
                          $canDelete = 0;
                        }
                        else{
                          $canDelete = 1;
                        }   
                      @endphp

                        <a href="javascript:;" class="delete-modal" data-id="{{$room->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
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
                {!! Form::label('name', 'Room Number:', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-9">
               {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text']) !!}
               <p id='namee' style="max-height:3px;"></p>
            </div>
          </div>
          
          <div class="form-group">
              {!! Form::label('departmentId', 'Department Name:', ['class' => 'col-sm-3 control-label']) !!}
                 <div class="col-sm-9">
                          <div class="col-sm-12" id="editRoomCheckDiv">
                                  @foreach(App\gnr\GnrDepartment::all() as $departmentId)
                                      <div class="col-sm-4">
                                          {!! Form::checkbox("departmentId[]", $departmentId->id, null, array('class' => 'empty')) !!} &nbsp;
                                          {!! Form::label('departmentId', $departmentId->name, ['class' => 'control-label']) !!}
                                      </div>
                                  @endforeach
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

    $.ajax({
      type: 'post',
      url: './bringDepartments',
      data: {
              '_token': $('input[name=_token]').val(),
              'id': $("#id").val()
           },
      dataType: 'json',

      success: function( data ){
        //alert(JSON.stringify(data));
        $(".empty").prop('checked', false);
            $.each(data, function (key, data) {
              $.each(data, function (index, data1) {
                $.each(data1, function (index2, data2) {
                  $("#editRoomCheckDiv :checkbox[value="+data2+"]").prop('checked', true);
                })
            })
        })
      },
      error: function( data ){
            // Handle error  
            alert('responseText');    
        }
    })


});
    // Edit Data (Modal and function edit data)
  $('.modal-footer').on('click', '.edit', function() {
  $.ajax({
      type: 'post',
      url: './editRoomItem',
       data: $('form').serialize(),
      dataType: 'json',
      success: function( data ){
        alert(JSON.stringify(data));
        if(data.errors){
           if (data.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red">'+data.errors.name+'</span>');
            }
            if (data.errors['departmentId']) {
                $('#departmentIde').empty();
                $('#departmentIde').append('<span class="errormsg" style="color:red">'+data.errors.departmentId+'</span>');
                return false;
            }
          }else{
        $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');
       
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
      }
         window.location.href = '{{url('viewRoomList/')}}';
      },
      error: function( data ){
            // Handle error  
            alert('responseText');    
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
    url: './deleteRoomItem',
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
});

$('select').on('change', function (e) {
        var departmentId = $("#departmentId").val();
        if(departmentId){$('#departmentIde').hide();}else{$('#departmentIde').show();}
    });

});//ready function end
</script>