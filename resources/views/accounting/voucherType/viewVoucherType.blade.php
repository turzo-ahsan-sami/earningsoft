@extends('layouts/acc_layout')
@section('title', '| Voucher Type')
@section('content')
@include('successMsg')


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addVoucherType/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Voucher Type</a>
          </div>
          <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">VOUCHER TYPE</h3>
          {{-- <div class="col-sm-6"><h3 align="right" style="font-family: Antiqua; letter-spacing: 2px; color: white;">ACCOUNT TYPE</h3></div>
          <div class="panel-options col-sm-6">
              <a href="{{url('addvoucherType/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Account Type</a>
          </div> --}}
          
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#accvoucherTypeView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="accvoucherTypeView">
            <thead>
              <tr>
                <th width="32">SL#</th>
                <th>Name</th>
                <th>Title Name</th>
                <th>Short Name</th>
                <th class="" width="80">Actions</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody>
              <?php $no=0; ?>
                    @foreach($voucherTypes as $voucherType) 
                    <tr class="item{{$voucherType->id}}">
                        <td class="text-center slNo">{{++$no}}</td>
                        <td> {{$voucherType->name}} </td>
                        <td> {{$voucherType->titleName}} </td>
                        <td> {{$voucherType->shortName}} </td>

                        <td class="text-center" width="80">
                            <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$voucherType->id}}" data-name="{{$voucherType->name}}" data-titlename="{{$voucherType->titleName}}" data-shortname="{{$voucherType->shortName}}"  data-slno="{{$no}}">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a> &nbsp;
                          <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$voucherType->id}}">
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
          <div class="form-group hidd hidden">
              {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
              {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
              {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text', 'readonly']) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('name', 'Name:', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-9">
                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Voucher Name','autocomplete' => 'off']) !!}
                <p id='namee' style="max-height:3px;"></p>
            </div>
          </div>

          <div class="form-group">
            {!! Form::label('titleName', 'Title Name:', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-9">
                {!! Form::text('titleName', $value = null, ['class' => 'form-control', 'id' => 'titleName', 'type' => 'text', 'placeholder' => 'Enter Voucher Title Name','autocomplete' => 'off']) !!}
                <p id='titleNamee' style="max-height:3px;"></p>
            </div>
          </div>

          <div class="form-group">
            {!! Form::label('shortName', 'Short Name:', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-9">
                {!! Form::text('shortName', $value = null, ['class' => 'form-control', 'id' => 'shortName', 'type' => 'text', 'placeholder' => 'Enter Voucher Short Name','autocomplete' => 'off']) !!}
                <p id='shortNamee' style="max-height:3px;"></p>
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
//    alert(($(this).data('parentid')));
    $('#parentId').val($(this).data('parentid'));
    $('#description').val($(this).data('description'));
//    alert(($(this).data('isparent')));
    // $('#isParent').val($(this).data('isparent'));
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');

    var isParentValue  = $(this).data('isparent');
    // alert(isParentValue);

    if(isParentValue==1){
        $('#isParent ').prop('checked', true);
    }else if(isParentValue==0){
        $('#isParent ').prop('checked', false);
    }

    

});
    // Edit Data (Modal and function edit data)
  $('.modal-footer').on('click', '.edit', function() {
  $.ajax({
      type: 'post',
      url: './editvoucherTypeItem',
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
            if (_response.errors['titleName']) {
                $('#titleNamee').empty();
                $('#titleNamee').append('<span style="color:red;">'+_response.errors.titleName+'</span>');
                return false;
            }
            if (_response.errors['shortName']) {
                $('#shortNamee').empty();
                $('#shortNamee').append('<span style="color:red;">'+_response.errors.shortName+'</span>');
                return false;
            }

        }else{
        $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully updated !');
        //alert(JSON.stringify(data));
        $('#myModal').modal('hide');
        $('.item' + data["voucherType"].id).replaceWith(
                                    "<tr class='item" + data["voucherType"].id + "'><td  class='text-center slNo'>" + data.slno +
                                                                    "</td><td class='hidden'>" + data["voucherType"].id + 
                                                                    "</td><td>" + data["voucherType"].name + 
                                                                    "</td><td>" + data["voucherType"].titleName +
                                                                    "</td><td>" + data["voucherType"].shortName +
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["voucherType"].id + "' data-name='" + data["voucherType"].name + "' data-titlename='" + data["voucherType"].titleName + "'  data-shortname='" + data["voucherType"].shortName + "'  data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["voucherType"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);                                                  
     }
        $("#name").val('');
        $("#titleName").val('');
        $("#shortName").val('');
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
    $('.modal-dialog').css('width','30%');
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
    url: './deletevoucherTypeItem',
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

     var titleName = $("#titleName").val();
    if(titleName){$('#titleNamee').hide();}else{$('#titleNamee').show();}

     var shortName = $("#shortName").val();
    if(shortName){$('#shortNamee').hide();}else{$('#shortNamee').show();}
});


});//ready function end
</script>




