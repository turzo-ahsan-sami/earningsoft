@extends('layouts/gnr_layout')
@section('title', '| Group')
@section('content')
@include('successMsg')

@php
  $foreignGroupIds = DB::table('gnr_company')->distinct()->pluck('groupId')->toArray(); 
@endphp


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addGroup/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Group</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">GROUP LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#gnrGrounView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="gnrGrounView">
            <thead>
              <tr>
                <th width="32">SL#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Website</th>
                <th class="">Action</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody>
              <?php $no=0; ?>
                    @foreach($groups as $group) 
                    <tr class="item{{$group->id}}">
                        <td class="text-center slNo">{{++$no}}</td>
                        <td style="text-align: left; padding-left: 5px;">{{$group->name}}</td>
                        <td style="text-align: left; padding-left: 5px;">{{$group->email}}</td>
                        <td>{{$group->phone}}</td>
                        <td style="text-align: left; padding-left: 5px;">{{$group->address}}</td>
                        <td>{{$group->website}}</td>
                        <td class="text-center" width="80">
                          <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$group->id}}" data-name="{{$group->name}}" data-email="{{$group->email}}" data-phone="{{$group->phone}}" data-address="{{$group->address}}" data-website="{{$group->website}}" data-slno="{{$no}}">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>&nbsp


                          @php
                          if (in_array($group->id, $foreignGroupIds)) {
                            $canDelete = 0;
                          }
                          else{
                            $canDelete = 1;
                          }   
                        @endphp


                          <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$group->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
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
        {!! Form::open(array('url' => '', 'id' => 'form1', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
          <!-- <input type = "hidden" name = "_token" value = ""> -->
          <div class="form-group hidd hidden">
              {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
              {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
              {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text', 'readonly']) !!}
            </div>
          </div>
          <div class="form-group">
                {!! Form::label('name', 'Group Name:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
               {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'required' => 'required']) !!}
               <p id='namee' style="max-height:3px;"></p>
            </div>
          </div>
          <div class="form-group">
              {!! Form::label('email', 'Email:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
              {!! Form::email('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'email']) !!}
              <p id="emaile" style="max-height:3px;"></p>
            </div>
          </div>
          <div class="form-group">
             {!! Form::label('phone', 'Phone No:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
              {!! Form::text('phone', $value = null, ['class' => 'form-control', 'id' => 'phone', 'type' => 'text']) !!}
              <p id='phonee' style="max-height:3px;"></p>
            </div>
          </div>
          <div class="form-group">
              {!! Form::label('address', 'Address:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
              {!! Form::textarea('address', $value = null, ['class' => 'form-control', 'id' => 'address', 'rows' => 3]) !!} 
              <p id='addresse' style="max-height:3px;"></p>
            </div>
          </div>
          <div class="form-group"  style="padding-bottom:20px;">
              {!! Form::label('website', 'Website:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
               {!! Form::text('website', $value = null, ['class' => 'form-control', 'id' => 'website', 'type' => 'text']) !!}
               <p id='websitee' style="max-height:3px;"></p>
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
    $('#email').val($(this).data('email'));
    $('#phone').val($(this).data('phone'));
    $('#address').val($(this).data('address'));
    $('#website').val($(this).data('website'));
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
});
    // Edit Data (Modal and function edit data)
  $('.modal-footer').on('click', '.edit', function() {
  $.ajax({
      type: 'post',
      url: './editItem',
      data: $('form').serialize(),
      dataType: 'json',
      success: function(data){
      	//alert(JSON.stringify(data));
        if (data.errors) {
            if (data.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+data.errors.name+'</span>');
                return false;
            }
            if (data.errors['email']) {
                $('#emaile').empty();
                $('#emaile').show();
                $('#emaile').append('<span class="errormsg" style="color:red;">'+data.errors.email+'</span>');
                return false;
            }
            if (data.errors['phone']) {
                $('#phonee').empty();
                $('#phonee').show();
                $('#phonee').append('<span class="errormsg" style="color:red;">'+data.errors.phone+'</span>');
                return false;
            }
            if (data.errors['address']) {
                $('#addresse').empty();
                $('#addresse').append('<span class="errormsg" style="color:red;">'+data.errors.address+'</span>');
                return false;
            }
            if (data.errors['website']) {
                $('#websitee').empty();
                $('#websitee').append('<span class="errormsg" style="color:red;">'+data.errors.website+'</span>');
                return false;
            }
        }else{
        $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully updated !');
        //alert(JSON.stringify(data));
        $('#myModal').modal('hide');
        $('.item' + data["group"].id).replaceWith(
                                    "<tr class='item" + data["group"].id + "'><td  class='text-center slNo'>" + data.slno +
                                                                    "</td><td class='hidden'>" + data["group"].id + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["group"].name + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["group"].email + 
                                                                    "</td><td>" + data["group"].phone +
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["group"].address +
                                                                    "</td><td>" + data["group"].website +
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["group"].id + "' data-name='" + data["group"].name + "' data-email='" + data["group"].email + "'  data-phone='" + data["group"].phone + "' data-address='" + data["group"].address + "' data-website='" + data["group"].website + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["group"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);                                                  
     }
            $("#name").val('');
            $("#email").val('');
            $("#phone").val('');
            $("#address").val('');
            $("#website").val('');
      },
      error: function( data ){
            // Handle error  
            //alert(_response.responseText);    
        }
  });
});

//delete function
$(document).on('click', '.delete-modal', function() {
  $('#MSGE').empty();
  $('#MSGS').empty();
  $('.actionBtn').removeClass('edit');
  $('#footer_action_button2').text(" Yes");
  $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
  //$('#footer_action_button').addClass('glyphicon-trash');
  $('#footer_action_button_dismis').text(" No");
  $('#footer_action_button_dismis').removeClass('glyphicon glyphicon-remove');
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
  $('.title').html($(this).data('uname'));
  $('#myModal').modal('show');
});

$('.modal-footer').on('click', '.delete', function() {
  $.ajax({
    type: 'post',
    url: './deleteItem',
    data: {
      '_token': $('input[name=_token]').val(),
      'id': $('.id').text()
    },
    success: function(data) {
      $('.item' + $('.id').text()).remove();
    }
  });
});

$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
             var email = $("#email").val();
            if(email){$('#emaile').hide();}else{$('#emaile').show();}
             var phone = $("#phone").val();
            if(phone){$('#phonee').hide();}else{$('#phonee').show();}
             var website = $("#website").val();
            if(website){$('#websitee').hide();}else{$('#websitee').show();}
});
$("textarea").keyup(function(){
    var address = $("#address").val();
    if(address){$('#addresse').hide();}else{$('#addresse').show();}
});

});//ready function end
</script>






