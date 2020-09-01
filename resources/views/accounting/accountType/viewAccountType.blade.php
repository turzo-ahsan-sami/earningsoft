@extends('layouts/acc_layout')
@section('title', '| Account Type')
@section('content')
@include('successMsg')


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addAccountType/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Account Type</a>
          </div>
          <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">ACCOUNT TYPE</h3>
          {{-- <div class="col-sm-6"><h3 align="right" style="font-family: Antiqua; letter-spacing: 2px; color: white;">ACCOUNT TYPE</h3></div>
          <div class="panel-options col-sm-6">
              <a href="{{url('addAccountType/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Account Type</a>
          </div> --}}
          
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#accAccountTypeView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="accAccountTypeView">
            <thead>
              <tr>
                <th width="32">SL#</th>
                <th>Name</th>
                <th>Parent</th>
                <th>Description</th>
                <th class="" width="80">Actions</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody>
              <?php $no=0; ?>
                    @foreach($accountTypes as $accountType) 
                    <tr class="item{{$accountType->id}}">
                        <td class="text-center slNo">{{++$no}}</td>
                        <td>{{$accountType->name}}</td>
                        <td>
                          <?php

                          if($accountType->parentId==0) { 
                            echo "Grand Parent";
                          } else {
                                  $accountTypename = DB::table('acc_account_type')->select('name')->where('id',$accountType->parentId)->first();
                          ?>
                                {{$accountTypename->name}}
                          <?php } ?>

                        </td>
                        <td>{{$accountType->description}}</td>

                        <td class="text-center" width="80">
                            <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$accountType->id}}" data-name="{{$accountType->name}}" data-parentid="{{$accountType->parentId}}" data-description="{{$accountType->description}}" data-isparent="{{$accountType->isParent}}"  data-slno="{{$no}}">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>&nbsp;
                          <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$accountType->id}}">
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
                  {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Account Name']) !!}
                  <p id='namee' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group">
              {!! Form::label('parentId', 'Parent:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">

                  <select class ="form-control" id = "parentId" name="parentId">
                      <option value="">Select one</option>
                      <option value="0">Grand Parent</option>
                      @foreach($accountTypes as $accountType)
                          <option value="{{$accountType->id}}">{{$accountType->name}}</option>
                      @endforeach
                  </select>
                  <p id='parentIde' style="max-height:3px;"></p>
              </div>
          </div>                   

          <div class="form-group">
              {!! Form::label('description', 'Description:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                    
                  {!! Form::textarea('description', $value = null, ['class' => 'form-control textarea', 'id' => 'description', 'rows' => 3, 'placeholder' => 'Enter description', 'type' => 'textarea']) !!}  
                  <p id='descriptione' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group" style="padding-bottom:20px;">
              {!! Form::label('isParent', 'Is Parent:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">
                  {{Form::checkbox('isParent', 1, false, ['id' => 'isParent'])}}
                  <p id='isParente' style="max-height:3px;"></p>
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
    
    if(hasAccess('editAccountTypeItem')){
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

    }
});
    // Edit Data (Modal and function edit data)
  $('.modal-footer').on('click', '.edit', function() {
    
  $.ajax({
      type: 'post',
      url: './editAccountTypeItem',
      data: $('form').serialize(),
      dataType: 'json',
      success: function(data){
        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }
        //alert(JSON.stringify(data));
        if (data.errors) {
            if (data.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+data.errors.name+'</span>');
                return false;
            }
            if (data.errors['parentId']) {
                $('#parentIde').empty();
                $('#parentIde').show();
                $('#parentIde').append('<span class="errormsg" style="color:red;">'+data.errors.parentId+'</span>');
                return false;
            }

        }else{
        $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully updated !');
        //alert(JSON.stringify(data));
        $('#myModal').modal('hide');
        $('.item' + data["accountType"].id).replaceWith(
                                    "<tr class='item" + data["accountType"].id + "'><td  class='text-center slNo'>" + data.slno +
                                                                    "</td><td class='hidden'>" + data["accountType"].id + 
                                                                    "</td><td>" + data["accountType"].name + 
                                                                    "</td><td>" + data["accountType"].parentId +
                                                                    "</td><td>" + data["accountType"].description +
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["accountType"].id + "' data-name='" + data["accountType"].name + "' data-parentid='" + data["accountType"].parentId + "'  data-description='" + data["accountType"].description + "'  data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["accountType"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);                                                  
     }
            $("#name").val('');
            $("#parentId").val('');
            $("#description").val('');
            $("#isParent").val('');
      },
      error: function( data ){
            // Handle error  
            //alert(_response.responseText);    
        }
  });

});

//delete function
$(document).on('click', '.delete-modal', function() {

  if(hasAccess('deleteAccountTypeItem')){

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
    }
    
});

$('.modal-footer').on('click', '.delete', function() {
  $.ajax({
    type: 'post',
    url: './deleteAccountTypeItem',
    data: {
      '_token': $('input[name=_token]').val(),
      'id': $('.id').text()
    },
    success: function(data) {
      if (data.accessDenied) {
    showAccessDeniedMessage();
    return false;
}
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




