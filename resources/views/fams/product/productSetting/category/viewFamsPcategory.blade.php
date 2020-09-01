@extends('layouts/fams_layout')
@section('title', '| Category')
@section('content')

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addFamsPctg/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Catagory</a>
          </div>
            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">CATEGORY LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#famsProCtgView").dataTable().yadcf([
    
            ]);*/
            $("#famsProCtgView").dataTable({
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="famsProCtgView">
                    <thead>
                      <tr>
                        <th width="30">SL#</th>
                          <th>Name</th>
                          <th>Code</th>
                        <th>Group</th>
                        <th>Action</th>
                      </tr>
                      {{ csrf_field() }}
                    </thead>
                    <tbody>
                      <?php $no=0; ?>
                      @foreach($productCategories as $productCategory)
                      @php
                        
                        $isBelongToSubcategory = DB::table('fams_product_sub_category')->where('productCategoryId',$productCategory->id)->value('id');
                        $isBelongToProduct = (int) DB::table('fams_product')->where('categoryId',$productCategory->id)->value('id'); 
                        
                      @endphp
                        <tr class="item{{$productCategory->id}}">
                          <td class="text-center slNo">{{++$no}}</td>
                            <td style="text-align:left;padding-left: 15px;">{{$productCategory->name}}</td>
                            <td>{{$productCategory->categoryCode}}</td>
                          <td style="text-align: left; padding-left: 15px;">
                              <?php
                                $productGroupName = DB::table('fams_product_group')->select('name')->where('id',$productCategory->productGroupId)->first();
                              ?>
                              {{$productGroupName->name}}
                          </td>

                          <td  class="text-center" width="80">
                            <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$productCategory->id}}" data-name="{{$productCategory->name}}" data-productgroupid="{{$productCategory->productGroupId}}" data-slno="{{$no}}"  data-categorycode="{{$productCategory->categoryCode}}" notEditable="{{$isBelongToProduct}}">
                              <span class="glyphicon glyphicon-edit"></span>
                            </a>&nbsp
                            <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$productCategory->id}}" @php if($isBelongToSubcategory>0){echo "style=\"pointer-events: none;cursor: not-allowed;\"";} @endphp>
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
              {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text']) !!}
            </div>
          </div>
          <div class="form-group">
              {!! Form::label('productGroupId', 'Group:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
                  <?php 
                  $ProductGroup = array('' => 'Please Select Prduct Group') + DB::table('fams_product_group')->pluck('name','id')->all(); 
                  ?>      
                  {!! Form::select('productGroupId', ($ProductGroup), null, array('class'=>'form-control', 'id' => 'productGroupId')) !!}
                  <p id='productGroupIde' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group">
              {!! Form::label('name', 'Category:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
                  {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter product category name']) !!}
                  <p id='namee' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group">
               {!! Form::label('categoryCode', 'Category Code:', ['class' => 'col-sm-2 control-label']) !!}
               <div class="col-sm-10">
                   {!! Form::text('categoryCode', $value = null, ['class' => 'form-control', 'id' => 'categoryCode', 'type' => 'text', 'placeholder' => 'Enter product category code']) !!}
                        <p id='categoryCodee' style="max-height:3px;"></p>
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
          
          {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-danger', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
          
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
  if(hasAccess('editFamsPctgItem')){
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
    $('#name').val($(this).data('name'));
    $('#categoryCode').val($(this).data('categorycode'));
    $('#productGroupId').val($(this).data('productgroupid'));
    
    if ($(this).attr('notEditable')>0) {
      $('#productGroupId').find('option').not(':selected').hide();
    }
    else{
      $('#productGroupId').find('option').not(':selected').show();
      $('#productGroupId').find('option :selected').show();
    }
    

    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
  }
});
    // Edit Data (Modal and function edit data)
  $('.modal-footer').on('click', '.edit', function() {
  $.ajax({
      type: 'post',
      url: './editFamsPctgItem',
      data: $('form').serialize(),
      dataType: 'json',
      success: function(data){
        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }
        //alert(JSON.stringify(data));
        if(data.errors){
          if (data.errors['productGroupId']) {
                $('#productGroupIde').empty();
                $('#productGroupIde').append('<span class="errormsg" style="color:red;">'+data.errors.productGroupId+'</span>');
                return false;
            }
            if (data.errors['name']) {
                $('#namee').empty();
                $('#namee').show();
                $('#namee').append('<span class="errormsg" style="color:red;">'+data.errors.name+'</span>');
                return false;
            }
            if (data.errors['categoryCode']) {
                $('#categoryCodee').empty();
                 $('#categoryCodee').show();
                $('#categoryCodee').append('<span class="errormsg" style="color:red;">'+data.errors.categoryCode+'</span>');
                return false;
            }
        }else{
        $('#MSG').addClass("hidden"); 
        $('#MSGS').text('Data successfully updated !');
        $('#myModal').modal('hide');
        //alert(JSON.stringify(data));
        $('.item'+data["productCategory"].id).replaceWith(
                                      "<tr class='item"+data["productCategory"].id+"'><td class='text-center slNo'>"+ data.slno +
                                                                     "</td><td class='hidden'>" + data["productCategory"].id +                                                                   
                                                                    "</td><td style='text-align:left;padding-left:15px;'>" + data["productCategory"].name + 
                                                                    "</td><td>" + data["productCategory"].categoryCode + 
                                                                    "</td><td>" + data["productGroupName"].name + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["productCategory"].id + "' data-name='" + data["productCategory"].name + "' data-categorycode='" + data["productCategory"].categoryCode + "' data-productgroupid='" + data["productCategory"].productGroupId + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["productCategory"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
      }
            $("#name").val('');
            $("#productGroupId").val('');
      },
      error: function( data ){
            alert(_response.responseText);    
        }
  });
});

//delete function
$(document).on('click', '.delete-modal', function() {
  if(hasAccess('deleteFamsPctgItem')){
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
    url: './deleteFamsPctgItem',
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
             
    });
$('select').on('change', function (e) {
    var productGroupId = $("#productGroupId").val();
    if(productGroupId){$('#productGroupIde').hide();}else{$('#productGroupIde').show();}
    });

});//ready function end
</script>