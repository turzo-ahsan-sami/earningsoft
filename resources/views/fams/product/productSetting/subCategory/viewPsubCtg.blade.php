@extends('layouts/fams_layout')
@section('title', '| Sub Category')
@section('content')

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addFamsPsubCtg/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add SubCatagory</a>
          </div>
            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">SUB CATEGORY LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#famsProSubctgView").dataTable().yadcf([
    
            ]);*/
            $("#famsProSubctgView").dataTable({
               "oLanguage": {
              "sEmptyTable": "No Records Available",
              "sLengthMenu": "Show _MENU_ "
              }
            });
            
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="famsProSubctgView">
            <thead>
                      <tr>
                        <th width="30">SL#</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Catagory</th>
                        <th>Group</th>
                        <th>Action</th>
                      </tr>
                    {{ csrf_field() }}
                </thead>
                <tbody> 
                  <?php $no=0; ?>
                  @foreach($productSubCategories as $productSubCategorie)
                  @php
                    $isBelongToProductType = DB::table('fams_product_type')->where('productSubCategoryId',$productSubCategorie->id)->value('id');
                    $isBelongToProduct = (int) DB::table('fams_product')->where('subCategoryId',$productSubCategorie->id)->value('id');
                  @endphp
                    <tr class="item{{$productSubCategorie->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                        <td style="text-align: left;padding-left: 15px;">{{$productSubCategorie->name}}</td>
                        <td>{{$productSubCategorie->subCategoryCode}}</td>
                      <td style="text-align: left; padding-left: 15px;">
                          <?php
                            $productCategoryName = DB::table('fams_product_category')->select('name')->where('id',$productSubCategorie->productCategoryId)->first();
                          ?>
                          {{$productCategoryName->name}}
                      </td>

                        <td style="text-align: left; padding-left: 15px;">
                            <?php
                            $productGroupName = DB::table('fams_product_group')->select('name')->where('id',$productSubCategorie->productGroupId)->first();
                            ?>
                            {{$productGroupName->name}}
                        </td>

                      <td  class="text-center" width="80">
                        <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$productSubCategorie->id}}" data-name="{{$productSubCategorie->name}}" data-productgroupid="{{$productSubCategorie->productGroupId}}" data-productcategoryid="{{$productSubCategorie->productCategoryId}}" data-subcategorycode="{{$productSubCategorie->subCategoryCode}}" data-slno="{{$no}}" notEditable="{{$isBelongToProduct}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp
                        <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$productSubCategorie->id}}" @php if($isBelongToProductType>0){echo "style=\"pointer-events: none;cursor: not-allowed;\"";} @endphp>
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
              {!! Form::label('productCategoryId', 'Category:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
              <?php 
                  $productCategoryId = array('' => 'Please Select Prduct Category') + DB::table('fams_product_category')->pluck('name','id')->all(); 
              ?>      
              {!! Form::select('productCategoryId', ($productCategoryId), null, array('class'=>'form-control', 'id' => 'productCategoryId')) !!}
                  <p id='productCategoryIde' style="max-height:3px;"></p>
              </div>
          </div>
          <div class="form-group">
              {!! Form::label('name', 'Sub Catagory:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
                  {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter product sub category name']) !!}
                  <p id='namee' style="max-height:3px;"></p>
              </div>
              </div>
          <div class="form-group">
              {!! Form::label('subCategoryCode', 'Sub Catagory Code:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
                  {!! Form::text('subCategoryCode', $value = null, ['class' => 'form-control', 'id' => 'subCategoryCode', 'type' => 'text', 'placeholder' => 'Enter sub category code']) !!}
                  <p id='subCategoryCodee' style="max-height:3px;"></p>
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
  if(hasAccess('editFamsPsubCtgItem')){
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
    $('#subCategoryCode').val($(this).data('subcategorycode'));
    $('#productGroupId').val($(this).data('productgroupid'));

    if ($(this).attr('notEditable')>0) {
      $('#productGroupId').find('option').not(':selected').hide();
    }
    else{
      $('#productGroupId').find('option').not(':selected').show();
      $('#productGroupId').find('option :selected').show();
    }

    $('#productCategoryId').val($(this).data('productcategoryid'));

    if ($(this).attr('notEditable')>0) {
      $('#productCategoryId').find('option').not(':selected').hide();
    }
    else{
      $('#productCategoryId').find('option').not(':selected').show();
      $('#productCategoryId').find('option :selected').show();
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
      url: './editFamsPsubCtgItem',
      data: $('form').serialize(),
      dataType: 'json',
      success: function(data){
        //alert(JSON.stringify(data));
        if(data.errors){
            if (data.errors['productGroupId']) {
                $('#productGroupIde').empty();
                $('#productGroupIde').append('<span class="errormsg" style="color:red;">'+data.errors.productGroupId+'</span>');
                return false;
            }
            if (data.errors['productCategoryId']) {
                $('#productCategoryIde').empty();
                $('#productCategoryIde').append('<span class="errormsg" style="color:red;">'+data.errors.productCategoryId+'</span>');
                return false;
            }
            if (data.errors['name']) {
                $('#namee').empty();
                $('#namee').show();
                $('#namee').append('<span class="errormsg" style="color:red;">'+data.errors.name+'</span>');
                return false;
            }
            if (data.errors['subCategoryCode']) {
                $('#subCategoryCodee').empty();
                $('#subCategoryCodee').show();
                $('#subCategoryCodee').append('<span class="errormsg" style="color:red;">'+data.errors.subCategoryCode+'</span>');
                return false;
            }
        }else{
        $('#MSG').addClass("hidden"); 
        $('#MSGS').text('Data successfully updated !');
        $('#myModal').modal('hide');
        //alert(JSON.stringify(data));
        $('.item'+data["productSubCategory"].id).replaceWith(
                          "<tr class='item"+data["productSubCategory"].id+"'><td class='text-center slNo'>"+ data.slno +
                                                                    "</td><td class='hidden'>" + data["productSubCategory"].id + 
                                                                    "</td><td style='text-align:left'>\xa0\xa0\xa0\xa0" + data["productGroupName"].name +
                                                                    "</td><td>" + data.subCategoryCode + 
                                                                    "</td><td>" + data["productCategoryName"].name + 
                                                                    "</td><td>" + data["productSubCategory"].name + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["productSubCategory"].id + "' data-productgroupid='" + data["productSubCategory"].productGroupId + "' data-productcategoryid='" + data["productSubCategory"].productCategoryId + "' data-name='" + data["productSubCategory"].name + "' data-subcategorycode='" + data.subCategoryCode + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["productSubCategory"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);  
      }
            $("#name").val('');
            $("#productGroupId").val('');
            $("#productCategoryId").val('');
      },
      error: function( data ){
            alert(_response.responseText);    
        }
  });
});

//delete function
$(document).on('click', '.delete-modal', function() {
  if(hasAccess('deleteFamsPsubCtgItem')){
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
    url: './deleteFamsPsubCtgItem',
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
    });

});//ready function end
</script>

  



