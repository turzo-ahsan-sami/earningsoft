@extends('layouts/gnr_layout')
@section('title', '| New Group')
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="col-md-0"></div>
      <div class="col-md-12 fullbody">
          <div class="viewTitle">
            <a href="{{url('addGroup/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Group</a>
            <span class="viewPageTitle">GROUP</span>
          </div>
            <div>
              <table class="table table-bordered responsive table-hover" id="table1" width="100%">
              	  	<thead>
	                  <tr>
	                      <th width="50">SL#</th>
	                      <th>Name</th>
	                      <th>Email</th>
	                      <th>Phone</th>
	                      <th>Address</th>
	                      <th>Website</th>
	                      <th>Action</th>
	                  </tr>
                  		{{ csrf_field() }}
                  	</thead>
                  	<tbody> 
                  	<?php $no=1; ?>
                  	@foreach($groups as $group) 
		                <tr class="item{{$group->id}}">
		                    <td class="text-center slNo">{{$no++}}</td>
		                    <td>{{$group->name}}</td>
		                    <td>{{$group->email}}</td>
		                    <td>{{$group->phone}}</td>
		                    <td>{{$group->address}}</td>
		                    <td>{{$group->website}}</td>
		                    <td class="text-center" width="80">
		                      <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$group->id}}" data-name="{{$group->name}}" data-email="{{$group->email}}" data-phone="{{$group->phone}}" data-address="{{$group->address}}" data-website="{{$group->website}}">
		                        <span class="glyphicon glyphicon-edit"></span>
		                      </a>&nbsp
		                      <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$group->id}}">
		                        <span class="glyphicon glyphicon-trash"></span>
		                      </a>
		                    </td>
		                </tr>
                  @endforeach
                </tbody>  
              </table>
            </div>
          <div class="footerTitle"></div>
      </div>
    <div class="col-md-0"></div>
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
          <input type = "hidden" name = "_token" value = "<?php echo csrf_token(); ?>">
          <div class="form-group hidd hidden">
              {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
              {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
            </div>
          </div>
          <div class="form-group">
                {!! Form::label('name', 'Group Name:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
               {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'required' => 'required']) !!}
            </div>
          </div>
          <div class="form-group">
              {!! Form::label('email', 'Email:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
              {!! Form::email('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'email']) !!}
            </div>
          </div>
          <div class="form-group">
             {!! Form::label('phone', 'Phone No:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
              {!! Form::text('phone', $value = null, ['class' => 'form-control', 'id' => 'phone', 'type' => 'text']) !!}
            </div>
          </div>
          <div class="form-group">
              {!! Form::label('address', 'Address:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
              {!! Form::textarea('address', $value = null, ['class' => 'form-control', 'id' => 'address', 'rows' => 3]) !!} 
            </div>
          </div>
          <div class="form-group"  style="padding-bottom:20px;">
              {!! Form::label('website', 'Website:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
               {!! Form::text('website', $value = null, ['class' => 'form-control', 'id' => 'website', 'type' => 'text']) !!}
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
          {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'id' => 'footer_action_button',  'data-dismiss' => 'modal'] ) !!}
          {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn',  'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}
          
          {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
          
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
$( document ).ready(function() {

$(document).on('click', '.edit-modal', function() {
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
        if(data.errors){
          if(data.errors['email']){ $('#MSG').text("Please enter a valid email address");}else{
          $('#MSG').text("Please fill all the field correctly");}
          return false;
        }else{
        $('#MSG').addClass("hidden"); 
        $('#MSGS').text('Data successfully updated !');
        //alert(JSON.stringify(data));
        $('.table > tbody').empty();
        $.each(data, function( index, value ){
         var string="<tr class='item"+value.id+"'><td class='hidden'>"+ value.id +"</td><td class='text-center slNo'>"+ ++index + 
                                                                    "</td><td>" + value.name + 
                                                                    "</td><td>" + value.email + 
                                                                    "</td><td>" + value.phone + 
                                                                    "</td><td>" + value.address + 
                                                                    "</td><td>" + value.website + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + value.id + "' data-name='" + value.name + "' data-email='" + value.email + "'  data-phone='" + value.phone + "' data-address='" + value.address + "' data-website='" + value.website + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + value.id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>"; 

        $('.table > tbody').append(string);
        });
      }
            $("#name").val('');
            $("#email").val('');
            $("#phone").val('');
            $("#address").val('');
            $("#website").val('');
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

});//ready function end
</script>