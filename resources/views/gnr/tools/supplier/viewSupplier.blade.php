@extends('layouts/gnr_layout')
@section('title', '| Supplier')
@section('content')
@include('successMsg')

@php
  $foreignSupplierIds = DB::table('inv_product')->distinct()->pluck('supplierId')->toArray(); 
@endphp


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addSupplier/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Supplier</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">SUPPLIER LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#gnrSupplierView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="gnrSupplierView">
            <thead>
                  <tr>
                    <th width="30">SL#</th>          
                    <th>Supplier Name</th>
                    <th>Company Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Website</th>
                    <th>Description</th>
                    <th>Action</th>
                  </tr>
                  {{ csrf_field() }}
                </thead>
                <tbody>
                  <?php $no=0; ?>
                  @foreach($suppliers as $supplier)
                    <tr class="item{{$supplier->id}}">
                      <td  class="text-center slNo">{{++$no}}</td>
                      <td style='text-align: left; padding-left: 5px;'>{{$supplier->name}}</td>
                      <td style='text-align: left; padding-left: 5px;'>{{$supplier->supplierCompanyName}}</td>
                      <td style='text-align: left; padding-left: 5px;'>{{$supplier->email}}</td>
                      <td>{{$supplier->phone}}</td>
                      <td style='text-align: left; padding-left: 5px;'>{{$supplier->address}}</td>
                      <td style='text-align: left; padding-left: 5px;'>{{$supplier->website}}</td>
                      <td style='text-align: left; padding-left: 5px;'>{{$supplier->description}}</td>
                      <td class="text-center">
                      <a href="javascript:;" class="edit-modal" data-id="{{$supplier->id}}" data-name="{{$supplier->name}}" data-suppliercompanyname="{{$supplier->supplierCompanyName}}" data-email="{{$supplier->email}}" data-mailfornotify="{{$supplier->mailForNotify}}" data-phone="{{$supplier->phone}}" data-address="{{$supplier->address}}" data-website="{{$supplier->website}}" data-description="{{$supplier->description}}" data-refno="{{$supplier->refNo}}" data-attentionfirst="{{$supplier->attentionFirst}}"  data-attentionsecond="{{$supplier->attentionSecond}}" data-attentionthird="{{$supplier->attentionThird}}" data-slno="{{$no}}">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>&nbsp


                      @php
                        if (in_array($supplier->id, $foreignSupplierIds)) {
                          $canDelete = 0;
                        }
                        else{
                          $canDelete = 1;
                        }   
                      @endphp


                      <a href="javascript:;" class="delete-modal" data-id="{{$supplier->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
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

<div id="myModal" class="modal fade" style="margin-top:2%">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" style="clear:both"></h4>
      </div>
      <div class="modal-body">
        {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                  <!-- <input type = "hidden" name = "_token" value = ""> -->
                  {!! Form::text('id', $value = null, ['class' => 'form-control hidden', 'id' => 'id', 'type' => 'text']) !!}
                  {!! Form::text('slno', $value = null, ['class' => 'form-control hidden', 'id' => 'slno', 'type' => 'text']) !!}
                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('name', 'Supplier Name:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Supplier Name']) !!}
                                    <p id='namee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('supplierCompanyName', "Supplier's Company Name:", ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('supplierCompanyName', $value = null, ['class' => 'form-control', 'id' => 'supplierCompanyName', 'type' => 'text', 'placeholder' => " Enter supplier's company name"]) !!}
                                    <p id='supplierCompanyNamee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('email', 'Email:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'text', 'placeholder' => 'Enter email address']) !!}
                                   <p id='emaile' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('mailForNotify', 'Emails for Notify:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::textarea('mailForNotify', $value = null, ['class' => 'form-control', 'id' => 'mailForNotify', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Emails for Notify']) !!}
                                   <p id='mailForNotifye' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('phone', 'Mobile:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('phone', $value = null, ['class' => 'form-control', 'id' => 'phone', 'type' => 'text', 'placeholder' => 'Enter phone number']) !!}
                                    <p id='phonee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('address', 'Address:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::textarea('address', $value = null, ['class' => 'form-control', 'id' => 'address', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Enter address']) !!}
                                    <p id='addresse' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('website', 'Website:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('website', $value = null, ['class' => 'form-control', 'id' => 'website', 'type' => 'text', 'placeholder' => 'Enter web address']) !!}
                                <p id='websitee' style="max-height:3px;"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('description', 'Description:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::textarea('description', $value = null, ['class' => 'form-control', 'id' => 'description', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Enter description']) !!}
                                    <p id='descriptione' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('refNo', 'Reference No:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('refNo', $value = null, ['class' => 'form-control', 'id' => 'refNo', 'type' => 'text', 'placeholder' => 'Enter reference number']) !!}
                                    <p id='refNoe' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('attentionFirst', 'Attention 1:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::textarea('attentionFirst', $value = null, ['class' => 'form-control', 'id' => 'attentionFirst', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Attention 1']) !!}
                                    <p id='attentionFirste' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('attentionSecond', 'Attention 2:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::textarea('attentionSecond', $value = null, ['class' => 'form-control', 'id' => 'attentionSecond', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Attention 2']) !!}
                                    <p id='attentionSeconde' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('attentionThird', 'Attention 3:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::textarea('attentionThird', $value = null, ['class' => 'form-control', 'id' => 'attentionThird', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Attention 3']) !!}
                                    <p id='attentionThirde' style="max-height:3px;"></p>
                                </div>
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
    $('.modal-dialog').css('width','60%');
    $('.deleteContent').hide();
    $('.form-horizontal').show();
    $('#id').val($(this).data('id'));
    $('#slno').val($(this).data('slno'));
    $('#name').val($(this).data('name'));
    $('#supplierCompanyName').val($(this).data('suppliercompanyname'));
    $('#email').val($(this).data('email'));
    $('#mailForNotify').val($(this).data('mailfornotify'));
    $('#phone').val($(this).data('phone'));
    $('#address').val($(this).data('address'));
    $('#website').val($(this).data('website'));
    $('#description').val($(this).data('description'));
    $('#refNo').val($(this).data('refno'));
    $('#attentionFirst').val($(this).data('attentionfirst'));
    $('#attentionSecond').val($(this).data('attentionsecond'));
    $('#attentionThird').val($(this).data('attentionthird'));
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
});
    // Edit Data (Modal and function edit data)
  $('.modal-footer').on('click', '.edit', function() {
  $.ajax({
      type: 'post',
      url: './editSupplierItem',
      data: $('form').serialize(),
      dataType: 'json',
      success: function( data ){
        //alert(JSON.stringify(data));
        if(data.errors){
          if (data.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+data.errors.name+'</span>');
                return false;
            }    
            if (data.errors['supplierCompanyName']) {
                $('#supplierCompanyNamee').empty();
                $('#supplierCompanyNamee').append('<span class="errormsg" style="color:red;">'+data.errors.supplierCompanyName+'</span>');
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
            if (data.errors['refNo']) {
                $('#refNoe').empty();
                $('#refNoe').append('<span class="errormsg" style="color:red;">'+data.errors.refNo+'</span>');
                return false;
            }
            if (data.errors['attentionFirst']) {
                $('#attentionFirste').empty();
                $('#attentionFirste').append('<span class="errormsg" style="color:red;">'+data.errors.attentionFirst+'</span>');
                return false;
            }
        }
        else{
        $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');
        //alert(JSON.stringify(data));
        $('.item' + data["supplier"].id).replaceWith(
                                    "<tr class='item" + data["supplier"].id + "'><td  class='text-center slNo'>" + data.slno +
                                                                    "</td><td class='hidden'>" + data["supplier"].id + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["supplier"].name + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["supplier"].supplierCompanyName + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["supplier"].email + 
                                                                    "</td><td>" + data["supplier"].phone + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["supplier"].address + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["supplier"].website + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["supplier"].description + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["supplier"].id + "' data-name='" + data["supplier"].name + "' data-supplierCompanyName='" + data["supplier"].supplierCompanyName + "' data-email='" + data["supplier"].email + "' data-mailForNotify='" + data["supplier"].mailForNotify + "' data-phone='" + data["supplier"].phone + "' data-address='" + data["supplier"].address + "' data-website='" + data["supplier"].website + "' data-description='" + data["supplier"].description + "' data-refNo='" + data["supplier"].refNo + "' data-attentionFirst='" + data["supplier"].attentionFirst + "' data-attentionSecond='" + data["supplier"].attentionSecond + "' data-attentionThird='" + data["supplier"].attentionThird + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["supplier"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000); 
        $('.table > tbody').append(string);
     }
            $("#name").val('');
            $("#supplierCompanyName").val('');
            $("#mailForNotify").val('');
            $("#description").val('');
            $("#refNo").val('');
            $("#email").val('');
            $("#phone").val('');
            $("#address").val('');
            $("#website").val('');
            $("#attentionFirst").val('');
            $("#attentionSecond").val('');
            $("#attentionThird").val('');
      },
      error: function( data ){
            // Handle error  
            //alert('hi');    
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
    url: './deleteSupplierItem',
    data: {
      '_token': $('input[name=_token]').val(),
      'id': $('.id').text()
    },
    success: function(data) {
      $('.item' + $('.id').text()).remove();
    },
    error: function( data ){
            // Handle error  
            alert(JSON.stringify(data)); 
        }
  });
});

$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
             var supplierCompanyName = $("#supplierCompanyName").val();
            if(supplierCompanyName){$('#supplierCompanyNamee').hide();}else{$('#supplierCompanyNamee').show();}
             var email = $("#email").val();
            if(email){$('#emaile').hide();}else{$('#emaile').show();}
             var phone = $("#phone").val();
            if(phone){$('#phonee').hide();}else{$('#phonee').show();}
             var website = $("#website").val();
            if(website){$('#websitee').hide();}else{$('#websitee').show();}
            var refNo = $("#refNo").val();
            if(refNo){$('#refNoe').hide();}else{$('#refNoe').show();}
});
$("textarea").keyup(function(){
    var address = $("#address").val();
    if(address){$('#addresse').hide();}else{$('#addresse').show();}
    var mailForNotify = $("#mailForNotify").val();
    if(mailForNotify){$('#mailForNotifye').hide();}else{$('#mailForNotifye').show();}
    var description = $("#description").val();
    if(description){$('#descriptione').hide();}else{$('#descriptione').show();}
    var attentionFirst = $("#attentionFirst").val();
    if(attentionFirst){$('#attentionFirste').hide();}else{$('#attentionFirste').show();}
    var attentionSecond = $("#attentionSecond").val();
    if(attentionSecond){$('#attentionSeconde').hide();}else{$('#attentionSeconde').show();}
    var attentionThird = $("#attentionThird").val();
    if(attentionThird){$('#attentionThirde').hide();}else{$('#attentionThirde').show();}
});

});//ready function end
</script>