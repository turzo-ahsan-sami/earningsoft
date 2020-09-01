@extends('layouts/gnr_layout')
@section('title', '| Village')
@section('content')
@include('successMsg')
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addVillage/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Village</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">VILLAGE LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#gnrVillageView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="gnrVillageView">
            <thead>
                  <tr>
                    <th width="30">SL#</th>
                    <th>Village</th>
                    <th>Union</th>
                    <th>Upzilla</th>
                    <th>District</th>
                    <th>Division</th>
                    <th>Action</th>
                  </tr>
                    {{ csrf_field() }}
                </thead>
                <tbody> 
                  <?php $no=0; ?>
                  @foreach($villages as $village)
                    <tr class="item{{$village->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                      <td style='text-align: left; padding-left: 5px;'>{{$village->name}}</td>
                      <td style='text-align: left; padding-left: 5px;'>
                          <?php
                            $unionName = DB::table('gnr_union')->select('name')->where('id',$village->unionId)->first();
                          ?>
                          {{$unionName->name}}
                      </td>
                      <td style='text-align: left; padding-left: 5px;'>
                          <?php
                            $upzillaName = DB::table('upzilla')->select('upzilla_name')->where('id',$village->upzillaId)->first();
                          ?>
                          {{$upzillaName->upzilla_name}}
                      </td>
                      <td style='text-align: left; padding-left: 5px;'>
                          <?php
                            $districtName = DB::table('district')->select('district_name')->where('id',$village->districtId)->first();
                          ?>
                          {{$districtName->district_name}}
                      </td>
                      <td style='text-align: left; padding-left: 5px;'>
                          <?php
                            $divisionName = DB::table('division')->select('division_name')->where('id',$village->divisionId)->first();
                          ?>
                          {{$divisionName->division_name}}
                      </td>
                      <td class="text-center" width="80">
                      <a href="javascript:;" class="edit-modal" data-id="{{$village->id}}" data-name="{{$village->name}}" data-divisionid="{{$village->divisionId}}" data-districtid="{{$village->districtId}}" data-upzillaid="{{$village->upzillaId}}" data-unionid="{{$village->unionId}}" data-slno="{{$no}}">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>&nbsp
                      <a href="javascript:;" class="delete-modal" data-id="{{$village->id}}">
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
                      <div class="form-group hidden">
                              {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                              {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text']) !!}
                              {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text']) !!}
                          </div>
                      </div>
                      <div class="form-group">
                          {!! Form::label('name', 'Village/Locality Name:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                              {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter village']) !!}
                              <p id='namee' style="max-height:3px;"></p>
                          </div>
                      </div>
                      <div class="form-group">
                          {!! Form::label('divisionId', 'Division:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                              <?php 
                                  $divisionId = array('' => 'Please Select Division') + DB::table('gnr_division')->pluck('name','id')->all(); 
                              ?>      
                              {!! Form::select('divisionId', ($divisionId), null, array('class'=>'form-control', 'id' => 'divisionId')) !!}
                              <p id='divisionIde' style="max-height:3px;"></p>
                          </div>
                      </div>
                      <div class="form-group">
                          {!! Form::label('districtId', 'District:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                              <?php 
                                  $districtId = array('' => 'Please Select District') + DB::table('gnr_district')->pluck('name','id')->all(); 
                              ?> 
                              {!! Form::select('districtId', ($districtId), null, array('class'=>'form-control', 'id' => 'districtId')) !!}
                              <p id='districtIde' style="max-height:3px;"></p>
                          </div>
                      </div>
                      <div class="form-group">
                          {!! Form::label('upzillaId', 'Upazila:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                              <?php 
                                  $upzillaId = array('' => 'Please Select Upazila') + DB::table('gnr_upzilla')->pluck('name','id')->all(); 
                              ?> 
                              {!! Form::select('upzillaId', ($upzillaId), null, array('class'=>'form-control', 'id' => 'upzillaId')) !!}
                              <p id='upzillaIde' style="max-height:3px;"></p>
                          </div>
                      </div>
                      <div class="form-group">
                          {!! Form::label('unionId', 'Union/Zone:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                              <?php 
                                  $unionId = array('' => 'Please Select Union') + DB::table('gnr_union')->pluck('name','id')->all(); 
                              ?> 
                              {!! Form::select('unionId', ($unionId), null, array('class'=>'form-control', 'id' => 'unionId')) !!}
                             <p id='unionIde' style="max-height:3px;"></p>
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
    $('#divisionId').val($(this).data('divisionid'));
    $('#districtId').val($(this).data('districtid'));
    $('#upzillaId').val($(this).data('upzillaid'));
    $('#unionId').val($(this).data('unionid'));
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
});
    // Edit Data (Modal and function edit data)
  $('.modal-footer').on('click', '.edit', function() {
  $.ajax({
      type: 'post',
      url: './editVillageItem',
      data: $('form').serialize(),
      dataType: 'json',
      success: function( data ){
        //alert(JSON.stringify(data));
        if(data.errors){
          if (data.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red">'+data.errors.name+'</span>');
                return false;
            }
            if (data.errors['divisionId']) {
                $('#divisionIde').empty();
                $('#divisionIde').append('<span class="errormsg" style="color:red">'+data.errors.divisionId+'</span>');
                return false;
            }
            if (data.errors['districtId']) {
                $('#districtIde').empty();
                $('#districtIde').append('<span class="errormsg" style="color:red">'+data.errors.districtId+'</span>');
                return false;
            }
            if (data.errors['upzillaId']) {
                $('#upzillaIde').empty();
                $('#upzillaIde').append('<span class="errormsg" style="color:red">'+data.errors.upzillaId+'</span>');
                return false;
            }
            if (data.errors['unionId']) {
                $('#unionIde').empty();
                $('#unionIde').append('<span class="errormsg" style="color:red">'+data.errors.unionId+'</span>');
                return false;
            }
        }
        else{
        $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');

        
        $('.item' + data["village"].id).replaceWith(
                    "<tr class='item" + data["village"].id + "'><td class='text-center slNo'>" + data.slno + 
                        "</td><td class='hidden'>" + data["village"].id + 
                        "</td><td style='text-align: left; padding-left: 5px;'>" + data["village"].name +
                        "</td><td style='text-align: left; padding-left: 5px;'>" + data["unionName"].name +
                        "</td><td style='text-align: left; padding-left: 5px;'>" + data["upzillaName"].upzilla_name + 
                        "</td><td style='text-align: left; padding-left: 5px;'>" + data["districtName"].district_name + 
                        "</td><td style='text-align: left; padding-left: 5px;'>" + data["divisionName"].division_name +
                        "</td><td class='text-center' width='80'><a href='javascript:;' class='edit-modal' data-id='" + data["village"].id + "' data-name='" + data["village"].name + "' data-unionid='" + data["village"].unionId + "' data-upzillaid='" + data["village"].upzillaId + "'  data-districtid='" + data["village"].districtId + "' data-divisionid='" + data["village"].divisionId + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["village"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
                    $('.succsMsg').removeClass('hidden');
                    $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000); 
     }
            $("#name").val('');
            $("#upzillaId").val('');
            $("#districtId").val('');
            $("#divisionId").val('');
            $("#unionId").val('');
      },
      error: function( data ){
            // Handle error 
            alert('hi');  
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
    url: './deleteVillageItem',
    data: {
      '_token': $('input[name=_token]').val(),
      'id': $('.id').text()
    },
    success: function(data) {
      $('.item' + $('.id').text()).remove();
    }
  });
});

});//ready function end

$(document).ready(function(){
$("#divisionId").change(function(){
    $("#districtId").empty();
    $("#districtId").prepend('<option selected="selected" value="">Please Select District</option>');
    $.ajax({
        type: 'post',
        url: './divisionIdSend',
      data: $('form').serialize(),
      dataType: 'json', 
        success: function( _response ){
          //alert(JSON.stringify(_response));
            $.each(_response, function( index, value ){
                $('#districtId').append("<option value='"+index+"'>"+value+"</option>");
          //alert(value);
        });

          }
      });

    });
});

$(document).ready(function(){
$("#districtId").change(function(){
    $("#upzillaId").empty();
    $("#upzillaId").prepend('<option selected="selected" value="">Please Select Upazila</option>');
    $.ajax({
        type: 'post',
        url: './districtSendId',
      data: $('form').serialize(),
      dataType: 'json', 
        success: function( _response ){
          //alert(JSON.stringify(_response));
      $.each(_response, function( index, value ){
          $('#upzillaId').append("<option value='"+index+"'>"+value+"</option>");
          //alert(value);
        });

          }
      });

    });
});

</script>

    