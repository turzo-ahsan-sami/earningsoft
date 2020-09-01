@extends('layouts/gnr_layout')
@section('title', '| Role')
@section('content')
@include('successMsg')
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addRole/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Role</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">ROLE LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#gnrRoleView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="gnrRoleView">
            <thead>
                  <tr>
                    <th width="30">SL#</th>
                    <th>Role Name</th>
                    <th>Action</th>
                  </tr>
                    {{ csrf_field() }}
                </thead>
                <tbody> 
                  <?php $no=0; ?>
                  @foreach($roleList as $userRoleList)
                    <tr class="item{{$userRoleList->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                      <td> {{$userRoleList->name}}</td>
                      <td class="text-center" width="100" style="">
                      <form action="{{ url('roleCheckedValue/') }}" method="post"  style="max-height: 0px; align:center; padding-bottom:7px">
                      <input type = "hidden" name = "_token" value = "<?php echo csrf_token(); ?>">
                      <input type="text" class="hidden" name="id" value="{!! $userRoleList->id !!}">
                        <span class="form-group" style="display: inline !important;">
                          <button type="submit" class="btn btn-link" style="padding:0px;"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i></button>
                          <button type="button"  class="btn btn-link"  style="padding:0px;">
                          <a href="javascript:;" class="delete-modal" data-id="{{$userRoleList->id}}">
                            <span class="glyphicon glyphicon-trash"></span>
                          </a>
                          </button>
                        </span>
                      </form>
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
                       <input type = "hidden" name = "_token" value = "<?php echo csrf_token(); ?>">
                        {!! Form::text('id', $value = null, ['class' => 'form-control hidden', 'id' => 'id', 'type' => 'text']) !!}
                         {!! Form::text('slno', $value = null, ['class' => 'form-control hidden', 'id' => 'slno', 'type' => 'text']) !!}
                      
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
    url: './deleteGnrRoleItem',
    data: {
      '_token': $('input[name=_token]').val(),
      'id': $('.id').text()
    },
    success: function(data) {
      //alert(JSON.stringify(data));
      $('.item' + $('.id').text()).remove();
    }
  });
});


});
</script>





