@extends('layouts/gnr_layout')
@section('title', '| Project')
@section('content')
@include('successMsg')



<div class="row">
  <div class="col-md-12">
    <div class="" style="">
      <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
          <div class="panel-heading" style="padding-bottom:0px">
            <div class="panel-options">
              <a href="{{url('addNotice/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Notice</a>
            </div>
            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">NOTICE LIST</font></h1>
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
                  <th>Notice Title</th>
                  <th>Branch</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                {{ csrf_field() }}
              </thead>
              <tbody> 
                <?php $no=0; ?>
                @foreach($notices as $notice)
                <tr class="item{{$notice->id}}">
                  <td class="text-center slNo">{{++$no}}</td>
                  <td style='text-align: left; padding-left: 5px;'>{{$notice->name}}</td>
                  <td style="text-align:left;">
                    <?php 
                    $i = 0;
                    $branchIdStr = ''; 
                    ?>
                    @foreach($notice->branchId as $branchId)
                    <?php
                    $branchList = DB::table('gnr_branch')->select('name')->where('id', $branchId)->first();
                    $branchIdStr .= $branchId;

                    if($i!=count($notice->branchId)-1)
                      $branchIdStr .= ',';
                    ?>
                    {{ $branchList->name }}
                    @if($i!=count($notice->branchId)-1)
                    {{ ', ' }} 
                    @endif
                    <?php $i++; ?>
                    @endforeach
                  </td>
                  <td> 
                    <?php
                    $status = $notice->status;
                    if($status==0){
                      echo "<span class='btn btn-xs btn-danger'>Disabled</span>";
                    }else{echo "<span class='btn btn-xs btn-success'>Enabled</span>";
                  }
                  ?>
                </td>

                <td class="text-center" width="80">
                  <a href="javascript:;" class="edit-modal" data-id="{{$notice->id}}" data-name="{{$notice->name}}" data-status="{{$notice->status}}"ata-slno="{{$no}}" data-branchid="{{ $branchIdStr }}">
                    <span class="glyphicon glyphicon-edit"></span>
                  </a>&nbsp;

                  <a href="javascript:;" class="delete-modal" data-id="{{$notice->id}}">
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
    <div class="col-sm-6">
      {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
      {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text', 'readonly']) !!}
    </div>
  </div>
  <div class="form-group">
    {!! Form::label('name', 'Notice Title:', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
     {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text']) !!}
     <p id='namee' style="max-height:3px;"></p>
   </div>
 </div>

 <div class="form-group">
  {!! Form::label('status', 'Status:', ['class' => 'col-sm-2 control-label']) !!}
  <div class="col-sm-6">     
    {!! Form::select('status', array('1'=>'Active', '0'=>'Inactive'), null, array('class'=>'form-control', 'id' => 'status')) !!}
    <p id='statuse' style="max-height:3px;"></p>
  </div>
</div>


<div class="form-group">
  {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-2 control-label']) !!}
  <div class="col-sm-10" style="padding-left:0!important;">
    <?php 
                              //  GET ALL THE BRANCHES.
    $branchList = DB::table('gnr_branch')->select('name', 'branchCode', 'id')->get(); 
    ?>
    @foreach($branchList as $branch)
    {{-- BRANCH LISTING EXCEPT HEAD OFFICE --}}
    @if($branch->id==1)
    @continue
    @endif
    <div class="col-sm-3" style="padding-right:0px!important;">
      {!! Form::checkbox('branchId[]', ($branch->id), false, array('class' => 'branchId cbr', 'onchange' => 'checkDuplicate($(this).val())')) !!}
      <span style="font-size:11px;">  
        {!! Form::label(Illuminate\Support\Str::lower($branch->name), (str_pad($branch->branchCode, 4, '0', STR_PAD_LEFT) . ' - ' . $branch->name)) !!}
      </span>
    </div>
    @endforeach
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
        url: './editNoticeItem',
        data: {
          '_token': $('input[name=_token]').val(),
          'id': $("#id").val(),
          'name': $('#name').val(),
          'status': $('#status').val(),

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

      }else{
        $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');
        //alert(JSON.stringify(data));
        $('.item' + data["notice"].id).replaceWith(
          "<tr class='item" + data["notice"].id + "'><td  class='text-center slNo'>" + data.slno +
          "</td><td class='hidden'>" + data["notice"].id + 
          "</td><td style='text-align: left; padding-left: 5px;'>" + data["notice"].name + 
          "</td><td>" + data["notice"].status + 

          "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["notice"].id + "' data-name='" + data["notice"].name + "' data-status='" + data["notice"].status + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["notice"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
      }

      $("#name").val('');

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
    url: './deleteNoticeItem',
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