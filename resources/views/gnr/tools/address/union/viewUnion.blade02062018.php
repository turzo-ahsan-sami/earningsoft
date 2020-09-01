@extends('layouts/gnr_layout')
@section('title', '| Union')
@section('content')
@include('successMsg')


@php
  $foreignPreUnioIds = DB::table('hr_emp_general_info')->distinct()->pluck('pre_uni_id')->toArray(); 
  $foreignPerUnioIds = DB::table('hr_emp_general_info')->distinct()->pluck('per_uni_id')->toArray(); 
  $foreignUnioIds = array_merge($foreignPreUnioIds, $foreignPerUnioIds);
  $foreignUnioIds = array_unique($foreignUnioIds);  
  
@endphp


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addUnion/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Union</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">UNION LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#gnrAddressView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="gnrAddressView">
            <thead>
                  <tr>
                    <th width="30">SL#</th>
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
                        @foreach($unions as $union)
                          <tr class="item{{$union->id}}">
                            <td class="text-center slNo">{{++$no}}</td>
                            <td style='text-align: left; padding-left: 5px;'>{{$union->name}}</td>
                            <td style='text-align: left; padding-left: 5px;'>
                                <?php
                                  $upzillaName = DB::table('upzilla')->select('upzilla_name')->where('id',$union->upzillaId)->first();
                                ?>
                                {{$upzillaName->upzilla_name}}
                            </td>
                            <td style='text-align: left; padding-left: 5px;'>
                                <?php
                                  $districtName = DB::table('district')->select('district_name')->where('id',$union->districtId)->first();
                                ?>
                                {{$districtName->district_name}}
                            </td>
                            <td style='text-align: left; padding-left: 5px;'>
                                <?php
                                  $divisionName = DB::table('division')->select('division_name')->where('id',$union->divisionId)->first();
                                ?>
                                {{$divisionName->division_name}}
                            </td>
                            <td class="text-center" width="80">
                              <a href="javascript:;" class="edit-modal" data-id="{{$union->id}}" data-name="{{$union->name}}" data-divisionid="{{$union->divisionId}}" data-districtid="{{$union->districtId}}" data-upzillaid="{{$union->upzillaId}}" data-slno="{{$no}}">
                                <span class="glyphicon glyphicon-edit"></span>
                              </a>&nbsp

                              @php
                                if (in_array($union->id, $foreignUnioIds)) {
                                  $canDelete = 0;
                                }
                                else{
                                  $canDelete = 1;
                                }   
                              @endphp
                              <a href="javascript:;" class="delete-modal" data-id="{{$union->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
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
                          {!! Form::label('name', 'Union/Zone Name:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                              {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Union/Zone']) !!}
                              <p id='namee' style="max-height:3px;"></p>
                          </div>
                      </div>
                      <div class="form-group">
                          {!! Form::label('divisionId', 'Division:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                              <?php 
                                  $divisionId = array('' => 'Please Select Division') + DB::table('division')->pluck('division_name','id')->all(); 
                              ?>      
                              {!! Form::select('divisionId', ($divisionId), null, array('class'=>'form-control', 'id' => 'divisionId')) !!}
                              <p id='divisionIde' style="max-height:3px;"></p>
                          </div>
                      </div>
                      <div class="form-group">
                          {!! Form::label('districtId', 'District:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                              <?php 
                                  $districtId = array('' => 'Please Select District') + DB::table('district')->pluck('district_name','id')->all(); 
                              ?> 
                              {!! Form::select('districtId', ($districtId), null, array('class'=>'form-control', 'id' => 'districtId')) !!}
                              <p id='districtIde' style="max-height:3px;"></p>
                          </div>
                      </div>
                      <div class="form-group">
                          {!! Form::label('upzillaId', 'Upazila:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                              <?php 
                                  $upzillaId = array('' => 'Please Select Upazila') + DB::table('upzilla')->pluck('upzilla_name','id')->all(); 
                              ?> 
                              {!! Form::select('upzillaId', ($upzillaId), null, array('class'=>'form-control', 'id' => 'upzillaId')) !!}
                              <p id='upzillaIde' style="max-height:3px;"></p>
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
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
});
    // Edit Data (Modal and function edit data)
  $('.modal-footer').on('click', '.edit', function() {
  $.ajax({
      type: 'post',
      url: './editUnionItem',
      data: $('form').serialize(),
      dataType: 'json',
      success: function( data ){
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
        }
        else{
        $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');
        //alert(JSON.stringify(data));
        $('.item' + data["union"].id).replaceWith(
                                    "<tr class='item" + data["union"].id + "'><td class='text-center slNo'>" + data.slno + 
                                                                    "</td><td class='hidden'>" + data["union"].id + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["union"].name + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["upzillaName"].upzilla_name + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["districtName"].district_name + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["divisionName"].division_name +
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["union"].id + "' data-name='" + data["union"].name + "' data-upzillaid='" + data["union"].upzillaId + "'  data-districtid='" + data["union"].districtId + "' data-divisionid='" + data["union"].divisionId + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["union"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000); 
     }
            $("#name").val('');
            $("#upzillaId").val('');
            $("#districtId").val('');
            $("#divisionId").val('');
      },
      error: function( data ){
            // Handle error   
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
    url: './deleteUnionItem',
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

$(document).ready(function(){
$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
});
$('select').on('change', function (e) {
    var divisionId = $("#divisionId").val();
    if(divisionId){$('#divisionIde').hide();}else{$('#divisionIde').show();}
     var districtId = $("#districtId").val();
    if(districtId){$('#districtIde').hide();}else{$('#districtIde').show();}
     var upzillaId = $("#upzillaId").val();
    if(upzillaId){$('#upzillaIde').hide();}else{$('#upzillaIde').show();}
});

});
</script>

    