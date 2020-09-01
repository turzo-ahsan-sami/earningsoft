@extends('layouts/inventory_layout')
@section('title', '| Size')
@section('content')
@include('successMsg')

@php
  $foreignSizeIds = DB::table('inv_product')->distinct()->pluck('sizeId')->toArray(); 
@endphp

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addProductSize/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Size</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">SIZE LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#invProSizeView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="invProSizeView">
            <thead>
                      <tr>
                        <th width="32">SL#</th>
                        <th>Group</th>
                        <th>Catagory</th>
                        <th>Sub Catagory</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Size</th>
                        <th>Action</th>
                      </tr>
                      {{ csrf_field() }}
                </thead>
                <tbody> 
                  <?php $no=0; ?>
                  @foreach($productSizes as $productSize)
                    <tr class="item{{$productSize->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                      <td style="padding-left: 5px; text-align: left;">
                          <?php
                            $productGroupName = DB::table('inv_product_group')->select('name')->where('id',$productSize->productGroupId)->first();
                          ?>
                          {{$productGroupName->name}}
                      </td>
                      <td style="padding-left: 5px; text-align: left;">
                          <?php
                            $productCategoryName = DB::table('inv_product_category')->select('name')->where('id',$productSize->productCategoryId)->first();
                          ?>
                          {{$productCategoryName->name}}
                      </td>
                      <td style="padding-left: 5px; text-align: left;">
                          <?php
                            $productSubCategoryName = DB::table('inv_product_sub_category')->select('name')->where('id',$productSize->productSubCategoryId)->first();
                          ?>
                          {{$productSubCategoryName->name}}
                      </td>
                      <td style="padding-left: 5px; text-align: left;">
                          <?php
                            $productBrandName = DB::table('inv_product_brand')->select('name')->where('id',$productSize->productBrandId)->first();
                          ?>
                          {{$productBrandName->name}}
                      </td>
                      <td style="padding-left: 5px; text-align: left;">
                          <?php
                            $productModelName = DB::table('inv_product_model')->select('name')->where('id',$productSize->productModelId)->first();
                          ?>
                          {{$productModelName->name}}
                      </td>
                      <td style="padding-left: 5px; text-align: left;">{{$productSize->name}}</td>
                      <td class="text-center" width="80">
                      <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$productSize->id}}" data-name="{{$productSize->name}}" data-productgroupid="{{$productSize->productGroupId}}" data-productcategoryid="{{$productSize->productCategoryId}}" data-productsubcategoryid="{{$productSize->productSubCategoryId}}" data-productbrandid="{{$productSize->productBrandId}}" data-productmodelid="{{$productSize->productModelId}}" data-slno="{{$no}}">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>&nbsp

                      @php
                        if (in_array($productSize->id, $foreignSizeIds)) {
                          $canDelete = 0;
                        }
                        else{
                          $canDelete = 1;
                        }   
                      @endphp

                      <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$productSize->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
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
         <!--  <input type = "hidden" name = "_token" value = ""> -->
          <div class="form-group hidden">
              {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
              {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
              {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text']) !!}
            </div>
          </div>
          <div class="form-group">
              {!! Form::label('name', 'Size:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
                  {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter product Size']) !!}
                  <p id='namee' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group">
              {!! Form::label('productGroupId', 'Group:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
                  <?php 
                      $ProductGroup = array('' => 'Please Select Prduct Group') + DB::table('inv_product_group')->pluck('name','id')->all(); 
                  ?>      
                  {!! Form::select('productGroupId', ($ProductGroup), null, array('class'=>'form-control', 'id' => 'productGroupId')) !!}
                  <p id='productGroupIde' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group">
              {!! Form::label('productCategoryId', 'Category:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
              <?php 
                  $productCategoryId = array('' => 'Please Select Prduct Category') + DB::table('inv_product_category')->pluck('name','id')->all(); 
              ?>      
              {!! Form::select('productCategoryId', ($productCategoryId), null, array('class'=>'form-control', 'id' => 'productCategoryId')) !!}
                  <p id='productCategoryIde' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group">
              {!! Form::label('productSubCategoryId', 'Sub Catagory:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
              <?php 
                  $productSubCategoryId = array('' => 'Please Select Prduct SubCategory') + DB::table('inv_product_sub_category')->pluck('name','id')->all(); 
              ?>  
                  {!! Form::select('productSubCategoryId', ($productSubCategoryId), null, array('class'=>'form-control', 'id' => 'productSubCategoryId')) !!}
                  <p id='productSubCategoryIde' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group">
              {!! Form::label('productBrandId', 'Brand:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
              <?php 
                  $productBrandId = array('' => 'Please Select Prduct Brand') + DB::table('inv_product_brand')->pluck('name','id')->all(); 
              ?>
                  {!! Form::select('productBrandId', ($productBrandId), null, array('class'=>'form-control', 'id' => 'productBrandId')) !!}
                  <p id='productBrandIde' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group">
              {!! Form::label('productModelId', 'Model:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
              <?php 
                  $productModelId = array('' => 'Please Select Prduct Model') + DB::table('inv_product_model')->pluck('name','id')->all(); 
              ?>
                  {!! Form::select('productModelId', ($productModelId), null, array('class'=>'form-control', 'id' => 'productModelId')) !!}
                  <p id='productModelIde' style="max-height:3px;"></p>
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
  if(hasAccess('editProductSizeItem')){
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
    $('#productGroupId').val($(this).data('productgroupid'));
    $('#productCategoryId').val($(this).data('productcategoryid'));
    $('#productSubCategoryId').val($(this).data('productsubcategoryid'));
    $('#productBrandId').val($(this).data('productbrandid'));
    $('#productModelId').val($(this).data('productmodelid'));
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
      url: './editProductSizeItem',
      data: $('form').serialize(),
      dataType: 'json',
      success: function(data){
        if(data.errors){
          if (data.errors['name']) {
                $('#namee').empty();
                $('#namee').show();
                $('#namee').append('<span class="errormsg" style="color:red;">'+data.errors.name+'</span>');
                //return false;
            }
            if (data.errors['productGroupId']) {
                $('#productGroupIde').empty();
                $('#productGroupIde').append('<span class="errormsg" style="color:red;">'+data.errors.productGroupId+'</span>');
                //return false;
            }
            if (data.errors['productCategoryId']) {
                $('#productCategoryIde').empty();
                $('#productCategoryIde').append('<span class="errormsg" style="color:red;">'+data.errors.productCategoryId+'</span>');
                //return false;
            }
            if (data.errors['productSubCategoryId']) {
                $('#productSubCategoryIde').empty();
                $('#productSubCategoryIde').append('<span class="errormsg" style="color:red;">'+data.errors.productSubCategoryId+'</span>');
                //return false;
            }
            if (data.errors['productBrandId']) {
                $('#productBrandIde').empty();
                $('#productBrandIde').append('<span class="errormsg" style="color:red;">'+data.errors.productBrandId+'</span>');
                //return false;
            }
            if (data.errors['productModelId']) {
                $('#productModelIde').empty();
                $('#productModelIde').append('<span class="errormsg" style="color:red;">'+data.errors.productModelId+'</span>');
                //return false;
            }
        }else{
        $('#MSG').addClass("hidden"); 
        $('#MSGS').text('Data successfully updated !');
        $('#myModal').modal('hide');
        //alert(JSON.stringify(data));
        $('.item'+data["productSize"].id).replaceWith(
                                    "<tr class='item"+data["productSize"].id+"'><td class='text-center slNo'>"+ data.slno + 
                                                                    "</td><td class='hidden'>" + data["productSize"].id + 
                                                                    "</td><td style='padding-left: 25px; text-align: left;'>" + data["productGroupName"].name + 
                                                                    "</td><td style='padding-left: 25px; text-align: left;'>" + data["productCategoryName"].name + 
                                                                    "</td><td style='padding-left: 25px; text-align: left;'>" + data["productSubCategoryName"].name + 
                                                                    "</td><td style='padding-left: 25px; text-align: left;'>" + data["productBrandName"].name + 
                                                                    "</td><td style='padding-left: 25px; text-align: left;'>" + data["productModelName"].name + 
                                                                    "</td><td style='padding-left: 25px; text-align: left;'>" + data["productSize"].name + 
                                                                    "</td><td class='text-center' width='80'><a href='javascript:;' class='edit-modal' data-id='" + data["productSize"].id + "' data-productgroupid='" + data["productSize"].productGroupId + "' data-productcategoryid='" + data["productSize"].productCategoryId + "' data-productsubcategoryid='" + data["productSize"].productSubCategoryId + "' data-productbrandid='" + data["productSize"].productBrandId + "' data-productmodelid='" + data["productSize"].productModelId + "' data-name='" + data["productSize"].name + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["productSize"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
      }
            /*$("#name").val('');
            $("#productGroupId").val('');
            $("#productCategoryId").val('');
            $("#productSubCategoryId").val('');
            $("#productBrandId").val('');
            $("#productModelId").val('');*/
      },
      error: function( data ){
            alert(data.responseText);    
        }
  });
});

//delete function
$(document).on('click', '.delete-modal', function() {
  if(hasAccess('deleteProductSizeItem')){
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
  $('.title').html($(this).data('uname'));
  $('#myModal').modal('show');
}
});

$('.modal-footer').on('click', '.delete', function() {
  $.ajax({
    type: 'post',
    url: './deleteProductSizeItem',
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
             
    });
$('select').on('change', function (e) {
    var productGroupId = $("#productGroupId").val();
    if(productGroupId){$('#productGroupIde').hide();}else{$('#productGroupIde').show();}
    var productCategoryId = $("#productCategoryId").val();
    if(productCategoryId){$('#productCategoryIde').hide();}else{$('#productCategoryIde').show();}
    var productSubCategoryId = $("#productSubCategoryId").val();
    if(productSubCategoryId){$('#productSubCategoryIde').hide();}else{$('#productSubCategoryIde').show();}
    var productBrandId = $("#productBrandId").val();
    if(productBrandId){$('#productBrandIde').hide();}else{$('#productBrandIde').show();}
    var productModelId = $("#productModelId").val();
    if(productModelId){$('#productModelIde').hide();}else{$('#productModelIde').show();}
    });

});//ready function end
</script>

  



