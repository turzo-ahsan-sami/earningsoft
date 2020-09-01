@extends('layouts/gnr_layout')
@section('title', '| Sub Functionality')
@section('content')
@include('successMsg')
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addSubFunction/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Sub Function</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">SUB FUNCTIONALITY LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#subFunctionalityView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="subFunctionalityView">
            <thead>
                  <tr>
                    <th width="30">SL#</th>
                    <th>Sub Function Name</th>
                    <th>description</th>
                    <th>Action</th>
                  </tr>
                  {{ csrf_field() }}
                </thead>
                <tbody>
                    <?php $no=0; ?>
                    @foreach($subfunctionalities as $subfunctionalitie)
                      <tr class="item{{$subfunctionalitie->id}}">
                        <td class="text-center slNo">{{++$no}}</td>
                        <td class="text-center">{{$subfunctionalitie->subFunctionName}}</td>
                        <td class="text-center">{{$subfunctionalitie->description}}</td>
                        <td class="text-center" width="80">
                          <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$subfunctionalitie->id}}" data-subfunctionname="{{$subfunctionalitie->subFunctionName}}" data-description="{{$subfunctionalitie->description}}" data-slno="{{$no}}">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>&nbsp
                          <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$subfunctionalitie->id}}">
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

          <div class="form-group hidden">
              {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
              {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
              {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text', 'readonly']) !!}
            </div>
          </div>
          <div class="form-group">
              {!! Form::label('subFunctionName', 'Sub Function Name:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">
              {!! Form::text('subFunctionName', $value = null, ['class' => 'form-control', 'id' => 'subFunctionName', 'type' => 'text', 'placeholder' => 'Enter Sub Function Name']) !!}
              <p id='subFunctionNamee' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group">
              {!! Form::label('description', 'Description:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">
              {!! Form::textarea('description', $value = null, ['class' => 'form-control', 'id' => 'description', 'rows' => 2, 'placeholder' => 'Enter description']) !!}  
                <p id='descriptione' style="max-height:3px;"></p>
              </div>
          </div>
        {!! Form::close()  !!}
          <div class="deleteContent" style="padding-bottom:20px;">
            <h4>You are about to delete this item this procedure is irreversible !</h4>
            <h4>Do you want to proceed ?</h4> 
            <span class="hidden id"></span>
          </div>
        <div class="modal-footer">
            <p id="MSG" class="pull-left" style="color:red"></p>
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
    $('#subFunctionName').val($(this).data('subfunctionname'));
    $('#description').val($(this).data('description'));
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
});
    // Edit Data (Modal and function edit data)
  $('.modal-footer').on('click', '.edit', function() {
  $.ajax({
      type: 'post',
      url: './editsubfunctionItem',
      data: $('form').serialize(),
      dataType: 'json',
      success: function(data){
        //alert(JSON.stringify(data));
        if(data.errors){
          if (data.errors['subFunctionName']) {
                $('#subFunctionNamee').empty();
                $('#subFunctionNamee').append('<span class="errormsg" style="color:red;">'+data.errors.subFunctionName+'</span>');
                return false;
            }
            if (data.errors['description']) {
                $('#descriptione').empty();
                $('#descriptione').append('<span class="errormsg" style="color:red;">'+data.errors.description+'</span>');
            }
        }else{
        $('#MSG').addClass("hidden"); 
        $('#MSGS').text('Data successfully updated !');
        $('#myModal').modal('hide');
        //alert(JSON.stringify(data));
        $('.item'+data["updateSubfunction"].id).replaceWith(
                                    "<tr class='item"+data["updateSubfunction"].id+"'><td class='text-center slNo'>"+ data.slno + 
                                                                    "</td><td class='hidden'>" + data["updateSubfunction"].id + 
                                                                    "</td><td class='text-center'>" + data["updateSubfunction"].subFunctionName + 
                                                                    "</td><td class='text-center'>" + data["updateSubfunction"].description + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["updateSubfunction"].id + "' data-subfunctionname='" + data["updateSubfunction"].subFunctionName + "' data-description='" + data["updateSubfunction"].description + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["updateSubfunction"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
      }
            $("#name").val('');
      },
      error: function( data ){
            alert(data);   
        }
  });
});

//delete function
$(document).on('click', '.delete-modal', function() {
  $('#MSGE').empty();
  $('#MSGS').empty();
  $('#footer_action_button2').text(" Yes");
  $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
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
    url: './deleteSubfunctionItem',
    data: {
      '_token': $('input[name=_token]').val(),
      'id': $('.id').text()
    },
    success: function(data) {
      $('.item' + $('.id').text()).remove();
      aliert();
    }
  });
});

});//ready function end



</script>