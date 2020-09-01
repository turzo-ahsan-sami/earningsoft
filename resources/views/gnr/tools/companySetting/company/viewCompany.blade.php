@extends('layouts/gnr_layout')
@section('title', '| Company')
@section('content')
@include('successMsg')


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading"  style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addCompany/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Company</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">COMPANY LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#gnrCompanyView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="gnrCompanyView">
            <thead>
              <tr>
                <th width="80">SL#</th>
                {{-- <th>Group Name</th> --}}
                <th>Company Name</th>
                <th>Company Business</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Website</th>
                <th>Image</th>
                <th>Action</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody> 
                  <?php $no=0; ?>
                  @foreach($companys as $company)
                    <tr class="item{{$company->id}}">
                      <td class="text-center">{{++$no}}</td>
                      <td style='text-align: left; padding-left: 5px;'>{{$company->name}}</td>
                      <td style='text-align: left; padding-left: 5px;'>{{config('constants.business_type')[$company->business_type]}}</td>
                      <td style='text-align: left; padding-left: 5px;'>{{$company->email}}</td>
                      <td>{{$company->phone}}</td>
                      <td style='text-align: left; padding-left: 5px;'>{{$company->address}}</td>
                      <td>{{$company->website}}</td>
                      <td class="text-center"><img src="{{ asset("images/company/$company->image") }}" width="60"></td>
                      <td class="text-center" width="80">
                        <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$company->id}}" data-name="{{$company->name}}" data-email="{{$company->email}}" data-phone="{{$company->phone}}" data-address="{{$company->address}}" data-website="{{$company->website}}" data-image="{{$company->image}}" data-slno="{{$no}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp
                        @php
                          $customerId = DB::table('customers')->where('id',$company->customer_id)->value('id');
                          //dd($customerId);
                        @endphp
                        <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$company->id}}" data-customerId="{{$customerId}}">
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
<!-- <script type="text/javascript">
    $(document).ready(function() {
        $('label:contains("entries")').each(function() {
            $(this).html($(this).html().split("entries").join(""));
        });
    });
</script> -->
<div id="myModal" class="modal fade" style="margin-top:3%">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
<!--           <button type="button" class="close" data-dismiss="modal">&times;</button>
-->            <h4 class="modal-title" style="clear:both"></h4>
      </div>
      <div class="modal-body">
        {!! Form::open(array('url' => 'editCompanyItem', 'enctype' => 'multipart/form-data', 'role' => 'form',  'class'=>'form-horizontal form-groups')) !!}
             <!-- <input type = "hidden" name = "_token" value = ""> -->
            <div class="form-group hidden">
                {!! Form::label('id', 'ID:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">
                  {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
                  {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text', 'readonly']) !!}
                </div>
            </div> 
            <div class="form-group">
                {!! Form::label('name', 'Company Name:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">
                    {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Company Name']) !!}
                    <p id='namee' style="max-height:3px;"></p>
                </div>
            </div>
           
            <div class="form-group">
                <label class="col-sm-3 control-label" for="business_type">Business type:</label>
                <div class="col-sm-9">
                   {{--  {!! Form::select('business_type', ['' => 'Select business type'] + config('constants.business_type'), $value = $company->business_type, ['class' => 'form-control', 'id' => 'business_type']) !!} --}}

                   <select class="form-control" name="business_type" id="business_type">
                    <option value="">Select business type</option>
                      @foreach(config('constants.business_type') as $key => $value)
                        <option value="{{ $key }}"  @if($key  == $company->business_type){{'selected'}}@endif>{{ $value}}</option>
                      @endforeach
                    
                   </select>
                    <p id='business_typee' style="max-height:3px;"></p>
                </div>
            </div>
             <div class="form-group">
                <label class="col-sm-3 control-label" for="voucher_type_step">Voucher appproval step:</label>
                <div class="col-sm-9">
                    <select class="form-control" name="voucher_type_step" id="voucher_type_step">
                         <option value="">Select step</option>
                        @if($company->voucher_type_step == 0)
                          <option value="0" selected>No step</option>
                          <option value="1">Single step</option>
                          <option value="2">Two steps</option>
                          <option value="3">Three steps</option>
                         
                         @elseif($company->voucher_type_step == 1)
                          <option value="0">No step</option>
                          <option value="1" selected>Single step</option>
                          <option value="2">Two steps</option>
                          <option value="3">Three steps</option>
                         
                         @elseif($company->voucher_type_step == 2)
                          <option value="0">No step</option>
                          <option value="1">Single step</option>
                          <option value="2" selected>Two steps</option>
                          <option value="3">Three steps</option>
                         

                         @elseif($company->voucher_type_step == 3)
                            <option value="0">No step</option>
                            <option value="1">Single step</option>
                            <option value="2">Two steps</option>
                            <option value="3" selected>Three steps</option>
                         
                        @endif 
                     
                     {{--    <p id='voucher_type_stepe' style="max-height:3px;"></p> --}}
                     </select>
                     <p id='voucher_type_stepe' style="max-height:3px;"></p>
                </div>
            </div>
            {{-- <div class="form-group">
                <label class="col-sm-2 control-label" for="fy_type">Fiscal Year type:</label>
                <div class="col-sm-10">
                    {!! Form::select('fy_type', ['' => 'Select fiscal year type'] + config('constants.fy_type'), $value = $company->fy_type, ['class' => 'form-control', 'id' => 'fy_type']) !!}
                    <p id='fy_typee' style="max-height:3px;"></p>
                </div>
            </div> --}}
            <div class="form-group">
                {!! Form::label('email', 'Email:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">
                    {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'text', 'placeholder' => 'Enter Company Email']) !!}
                    <p id='emaile' style="max-height:3px;"></p>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('phone', 'Mobile:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">
                    {!! Form::text('phone', $value = null, ['class' => 'form-control', 'id' => 'phone', 'type' => 'text', 'placeholder' => 'Enter Company Phone']) !!}
                    <p id='phonee' style="max-height:3px;"></p>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('address', 'Address:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">                    
                    {!! Form::textarea('address', $value = null, ['class' => 'form-control', 'id' => 'address', 'rows' => 3, 'placeholder' => 'Enter Address ']) !!}  
                   <p id='addresse' style="max-height:3px;"></p>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('website', 'Website:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">
                    {!! Form::text('website', $value = null, ['class' => 'form-control', 'id' => 'website', 'type' => 'text', 'placeholder' => 'Enter Website']) !!}
                    <p id='websitee' style="max-height:3px;"></p>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('image', 'Upload Image:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">
                    {!! Form::file('image', $value = null, ['class' => 'form-control', 'id' => 'image', 'type' => 'file']) !!}
                    <p id='imageShow' style="padding-top:10px;"></p>
                    <p id='imageShow2' style="padding-top:10px;"></p>
                </div>
            </div>
          <div class="Updatesuccessmsg" style="clear:both" id="forSubmit">
              <p id="MSGE" class="pull-left" style="color:red"></p>
              <p id="MSGS" class="pull-left" style="color:green"></p>
              <p class="pull-right">
            {!! Form::submit('Update', ['class' => 'btn actionBtn'] ) !!}
            {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
              </p>
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
//delete image from folder when update
$(document).on('click', '.edit', function() {
  var image1 = $('#image1').attr('src');
  var image2 = $('#image2').attr('src');
  var replacedImage1 = image1.substring(image1.indexOf("/images") + 1);
if(image1 && image2){
$.ajax({
    type: 'post',
    url: './imageDelete',
    data: {
      '_token': $('input[name=_token]').val(),
      'replacedImage1': replacedImage1
    },
    success: function(data) {
          if (data.responseTitle=='Success!') {
              toastr.success(data.responseText, data.responseTitle, opts);
              $('.item' + $('.id').text()).remove();                        
          }
          else if(data.responseTitle=='Warning!'){
              toastr.warning(data.responseText, data.responseTitle, opts);                        
          }
          // $('#myModal').modal('hide');

    }
  });
}
});

  //update image show
$('#image').on('change',function(){
  $("#imageShow").hide();
  $("#imageShow2").empty();
  var files = !!this.files ? this.files:[];
  if(!files.length || !window.FileReader) return;
  if(/^image/.test(files[0].type)){
    var reader = new FileReader();
    reader.readAsDataURL(files[0]);
    reader.onloadend = function(){
      $('#imageShow2').append('<img src="'+this.result+'" width="100" height="80" id="image2"/>');
    }
  }
});

$(document).on('click', '.edit-modal', function() {
    $('.errormsg').empty();
    $('#imageShow').empty();
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
    $('.modal-footer').hide();
    $('.form-horizontal').show();
    $('#id').val($(this).data('id'));
     $('#delCustomerId').text('employeeId : '+$(this).data('employeeId'));
    $('#slno').val($(this).data('slno'));
    // $('#groupId').val($(this).data('groupid'));
    $('#name').val($(this).data('name'));
    //$('#business_type').val($(this).data('business_type'));
    

    // $('#fy_type').val($(this).data('fy_type'));
    $('#email').val($(this).data('email'));
    $('#phone').val($(this).data('phone'));
    $('#address').val($(this).data('address'));
    $('#website').val($(this).data('website'));
    var imageName = ($(this).data('image'));
    //alert(imageName);
    $('#imageShow').append('<img src="{{ asset("images/company/") }}'+ '/' + imageName +'" width="100" height="80" id="image1"/>');
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
});
    // Edit Data (Modal and function edit data)
  $('form').submit(function( event ) {
    event.preventDefault();
    var formData = new FormData($(this)[0]);
    $.ajax({
         type: 'post',
         url: './editCompanyItem',
         data: formData,
         async: false,
         cache: false,
         contentType: false,
         processData: false,
         dataType: 'json',
      success: function( data ){
        if(data.errors){
          if (data.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+data.errors.name+'</span>');
                return false;
            }
          if (data.errors['business_type']) {
                $('#business_typee').empty();
                $('#business_typee').append('<span class="errormsg" style="color:red;">'+data.errors.business_type+'</span>');
                return false;
            }
          // if (data.errors['fy_type']) {
          //       $('#fy_typee').empty();
          //       $('#fy_typee').append('<span class="errormsg" style="color:red;">'+data.errors.fy_type+'</span>');
          //       return false;
          //   }
            if (_response.errors['voucher_type_step']) {
                $('#voucher_type_stepe').empty();
                $('#voucher_type_stepe').append('<span class="errormsg" style="color:red;">'+_response.errors.voucher_type_step+'</span>');
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
        }else{
          location.reload();
        $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully updated!');
        $('#myModal').modal('hide');
        //alert(JSON.stringify(data));
        $('.item'+data["create"].id).replaceWith(
                                    "<tr class='item"+data["create"].id+"'><td class='text-center slNo'>"+ data.slno + 
                                                                "</td><td class='hidden'>" + data["create"].id + 
                                                                // "</td><td style='text-align: left; padding-left: 5px;'>" + data["groupName"].name + 
                                                                "</td><td style='text-align: left; padding-left: 5px;'>" + data["create"].name + 
                                                                "</td><td style='text-align: left; padding-left: 5px;'>" + data["create"].business_type + 
                                                                "</td><td style='text-align: left; padding-left: 5px;'>" + data["create"].email + 
                                                                "</td><td>" + data["create"].phone + 
                                                                "</td><td style='text-align: left; padding-left: 5px;'>" + data["create"].address + 
                                                                "</td><td>" + data["create"].website + 
                                                                "</td><td class='text-center'>" + '<img src="{{ asset("images/company/") }}'+ '/' + data["create"].image +'" width="60">' + 
                                                                "</td><td><a href='javascript:;' class='edit-modal' data-id='" + data["create"].id + "' data-name='" + data["create"].name + "' data-groupid='" + "' data-email='" + data["create"].email + "'  data-phone='" + data["create"].phone + "' data-address='" + data["create"].address + "' data-website='" + data["create"].website + "' data-image='" + data["create"].image + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["create"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
    $('.succsMsg').removeClass('hidden');
    $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
      }
            // $("#groupId").val('');
            $("#name").val('');
            $("#business_type").val('');
            // $("#fy_type").val('');
            $("#voucher_type_step").val('');
            $("#email").val('');
            $("#phone").val('');
            $("#address").val('');
            $("#website").val('');
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
  $('#delCustomerId').text('customerId : '+$(this).data('customerId'));
  $('.deleteContent').show();
  $('.modal-footer').show();
  $('.form-horizontal').hide();
  $('#footer_action_button2').show();
  $('#footer_action_button').hide();
  $('#myModal').modal('show');
});

$('.modal-footer').on('click', '.delete', function() {
  $.ajax({
    type: 'post',
    url: './deleteCompanyItem',
    data: {
      '_token': $('input[name=_token]').val(),
      'id': $('.id').text()
    },
    success:function(data){
        if (data.responseTitle=='Success!') {
            toastr.success(data.responseText, data.responseTitle, opts);
            $('.item' + $('.id').text()).remove();                        
        }
        else if(data.responseTitle=='Warning!'){
            toastr.warning(data.responseText, data.responseTitle, opts);                        
        }

        if(data.image){
          var image = data.image;
          var replacedImage1 = 'images/company/'+image;
           $.ajax({
              type: 'post',
              url: './imageDelete',
              data: {
                  '_token': $('input[name=_token]').val(),
                  'replacedImage1': replacedImage1
                },
                 success: function(data) {
                  $('.item' + $('.id').text()).remove();
                  }
            });
        }
    }
    // success: function(data) {
    //   var image = data.image;
    //   var replacedImage1 = 'images/company/'+image;
    //         $.ajax({
    //           type: 'post',
    //           url: './imageDelete',
    //           data: {
    //               '_token': $('input[name=_token]').val(),
    //               'replacedImage1': replacedImage1
    //             },
    //              // success: function(data) {
    //              //  $('.item' + $('.id').text()).remove();
    //              //  }
    //              success: function(data) {
    //               if (data.responseTitle=='Success!') {
    //                     toastr.success(data.responseText, data.responseTitle, opts);
    //                     $('.item' + $('.id').text()).remove();                        
    //                 }
    //                 else if(data.responseTitle=='Warning!'){
    //                     toastr.warning(data.responseText, data.responseTitle, opts);                        
    //                 }
                    
    //               }
    //         });
    //   $('.item' + $('.id').text()).remove();
    // }
  });
});

$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
             var email = $("#email").val();
            if(email){$('#emaile').hide();}else{$('#emaile').show();}
             var phone = $("#phone").val();
            if(phone){$('#phonee').hide();}else{$('#phonee').show();}
            var voucher_type_step = $("#voucher_type_step").val();
            if(voucher_type_step){$('#voucher_type_stepe').hide();}else{$('#voucher_type_stepe').show();}
});
$("textarea").keyup(function(){
    var address = $("#address").val();
    if(address){$('#addresse').hide();}else{$('#addresse').show();}
});

// $('select').on('change', function (e) {
//     var groupId = $("#groupId").val();
//     if(groupId){$('#groupIde').hide();}else{$('#groupIde').show();}
// });

});//ready function end
</script>