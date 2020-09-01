@extends('layouts/fams_layout')
@section('title', '| Product Type')
@section('content')

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addFamsPtype/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Product Type</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PRODUCT TYPE LIST</font></h1>
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
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Action</th>
                      </tr>
                      
                    </thead>
                    <tbody>
                      <?php $no=0; ?>
                      @foreach($productTypes as $productType)

                       @php
                    
                    $isBelongToProductName = DB::table('fams_product_name')->where('productTypeId',$productType->id)->value('id');
                   
                  @endphp
                        <tr class="item{{$productType->id}}">
                          <td class="text-center slNo">{{++$no}}</td>
                          <td style="text-align: left; padding-left: 15px;">
                              {{$productType->name}}
                          </td>
                          <td>
                            {{$productType->productTypeCode}}
                          </td>
                          <td style="text-align: left; padding-left: 15px;">
                          @php
                            $groupName = DB::table('fams_product_group')->where('id',$productType->productGroupId)->value('name');
                          @endphp
                              {{$groupName}}
                          </td>
                          <td style="text-align: left; padding-left: 15px;">
                          @php
                            $categoryName = DB::table('fams_product_category')->where('id',$productType->productCategoryId)->value('name');
                          @endphp
                              {{$categoryName}}
                          </td>
                          <td style="text-align: left; padding-left: 15px;">
                          @php
                            $subCategoryName = DB::table('fams_product_sub_category')->where('id',$productType->productSubCategoryId)->value('name');
                          @endphp
                              {{$subCategoryName}}
                          </td>
                          <td  class="text-center" width="80">
                            <a href="" id={{$productType->id}} data-toggle="modal" data-target="#edit-modal-{{$productType->id}}" >
                              <span class="glyphicon glyphicon-edit"></span>
                            </a>&nbsp
                            <a href="" data-toggle="modal" data-target="#delete-modal-{{$productType->id}}" @php if($isBelongToProductName>0){echo "style=\"pointer-events: none;cursor: not-allowed;\"";} @endphp>
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

@foreach($productTypes as $prodType)


  {{-- Edit Modal --}}

  <div id="edit-modal-{{$prodType->id}}" class="modal fade edit-modal" style="margin-top:3%;">

    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Item</h4>
        </div>

        <div class="modal-body" >
          {!! Form::open(['url' => '','id'=>'form-'.$prodType->id,'class'=>'form-horizontal form-groups']) !!}
          {!! Form::hidden('productTypeId',$prodType->id,['id'=>'productTypeId-'.$prodType->id]) !!}
          <div class="form-group">
                {!! Form::label('productGroupId', 'Group:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">
                <?php 
                 $ProductGroup = array('' => 'Please Select Product Group') + DB::table('fams_product_group')->pluck('name','id')->all(); 
                  ?>      
                 {!! Form::select('productGroupId', ($ProductGroup), $prodType->productGroupId, array('class'=>'form-control', 'id' => 'productGroupId-'.$prodType->id)) !!}
                     <p id='productGroupIde-{{$prodType->id}}' style="max-height:3px;"></p>
                       </div>
         </div>
            <div class="form-group">
                {!! Form::label('productCategoryId', 'Category:', ['class' => 'col-sm-3 control-label']) !!}
                   <div class="col-sm-9">
                   <?php 
                      $productCategoryId = array('' => 'Please Select Product Category') + DB::table('fams_product_category')->pluck('name','id')->all(); 
                    ?>      
                         {!! Form::select('productCategoryId', ($productCategoryId), $prodType->productCategoryId, array('class'=>'form-control', 'id' => 'productCategoryId-'.$prodType->id)) !!}
                          <p id='productCategoryIde-{{$prodType->id}}' style="max-height:3px;"></p>
                         </div>
                                </div>
                                <div class="form-group">
                                        {!! Form::label('productSubCategoryId', 'Sub Category:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        <?php 
                                            $productSubCategoryId = array('' => 'Please Select Product Sub Category') + DB::table('fams_product_sub_category')->pluck('name','id')->all(); 
                                        ?>      
                                        {!! Form::select('productSubCategoryId', ($productSubCategoryId), $prodType->productSubCategoryId, array('class'=>'form-control', 'id' => 'productSubCategoryId-'.$prodType->id)) !!}
                                            <p id='productSubCategoryIde-{{$prodType->id}}' style="max-height:3px;"></p>
                                        </div>
                                </div>
                                
                                <div class="form-group">
                                    {!! Form::label('productTypeName', 'Product Type Name:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('name', $value = $prodType->name, ['class' => 'form-control', 'id' => 'name-'.$prodType->id, 'type' => 'text', 'placeholder' => 'Enter Product Type Name']) !!}
                                        <p id='namee-{{$prodType->id}}' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                    <div class="form-group">
                                        {!! Form::label('productTypeCode', 'Product Type Code:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('productTypeCode', $value = $prodType->productTypeCode, ['class' => 'form-control', 'id' => 'productTypeCode-'.$prodType->id, 'type' => 'text', 'placeholder' => 'Enter Product Type Code']) !!}
                                            <p id='productTypeCodee-{{$prodType->id}}' style="max-height:3px;"></p>
                                        </div>
                                    </div>
          <div class="modal-footer">
          <div class="form-group">
            <div class="col-sm-12">
            
               <button  type="button" class="btn actionBtn glyphicon glyphicon-check btn-success confirmButton" id="summitButton-{{$prodType->id}}" modalId={{$prodType->id}}><span> Update</span></button> 
              <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span> Close</span></button>
            </div>
          </div>
            </div>

          {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
    {{-- End Edit Modal --}}


  {{-- Delete Modal --}}
  <div id="delete-modal-{{$prodType->id}}" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
        </div>
        <div class="modal-body">
          <h2>Are You Confirm to Delete This Item?</h2>

          <div class="modal-footer">
            {!! Form::open(['url' => 'deleteFamsItem/']) !!}
            <input type="hidden" name="itemId" value={{$prodType->id}}>
            <button  type="submit" class="btn btn-danger"><span id=""> Confirm</span></button>
            <button class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span> Close</span></button>
            {!! Form::close() !!}

          </div>

        </div>
      </div>
    </div>
  </div>
  {{-- End Delete Modal --}}


<script>
  $(document).ready(function () {
    var modalId = "{{$prodType->id}}";
    
   $('#summitButton-'+modalId).on('click',function() {

    var csrf = "<?php echo csrf_token(); ?>";
    var productGroupId = $("#productGroupId-"+modalId).val();
    var productCategoryId = $("#productCategoryId-"+modalId).val();
    var productSubCategoryId = $("#productSubCategoryId-"+modalId).val();
    var name = $("#name-"+modalId).val();
    var productTypeCode = $("#productTypeCode-"+modalId).val();
    var productTypeId = $("#productTypeId-"+modalId).val();


// alert(name);
// alert(productTypeCode);
// alert(productTypeId);
// alert(productGroupId);
// alert(productCategoryId);
// alert(productSubCategoryId);
    
    //event.preventDefault();
    
    $.ajax({
         type: 'post',
         url: './famsEditProductType',
         data: {productTypeId: productTypeId, name: name, productTypeCode: productTypeCode,productGroupId: productGroupId, productCategoryId: productCategoryId,productSubCategoryId: productSubCategoryId, _token: csrf },
         dataType: 'json',
        success: function( _response ){
            //alert(JSON.stringify(_response));
    if (_response.errors) {
        if (_response.errors['name']) {
            $('#namee-'+modalId).empty();
            $('#namee-'+modalId).append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
            //return false;
        }
            if (_response.errors['productTypeCode']) {
                $('#productTypeCodee-'+modalId).empty();
                $('#productTypeCodee-'+modalId).show();
                $('#productTypeCodee-'+modalId).append('<span class="errormsg" style="color:red;">'+_response.errors.productTypeCode+'</span>');
                //return false;
            }
            if (_response.errors['productGroupId']) {
                $('#productGroupIde-'+modalId).empty();
                $('#productGroupIde-'+modalId).show();
                $('#productGroupIde-'+modalId).append('<span class="errormsg" style="color:red;">'+_response.errors.productGroupId+'</span>');
                //return false;
            }
            if (_response.errors['productCategoryId']) {
                $('#productCategoryIde-'+modalId).empty();
                $('#productCategoryIde-'+modalId).show();
                $('#productCategoryIde-'+modalId).append('<span class="errormsg" style="color:red;">'+_response.errors.productCategoryId+'</span>');
                //return false;
            }
            if (_response.errors['productSubCategoryId']) {
                $('#productSubCategoryIde-'+modalId).empty();
                $('#productSubCategoryIde-'+modalId).show();
                $('#productSubCategoryIde-'+modalId).append('<span class="errormsg" style="color:red;">'+_response.errors.productSubCategoryId+'</span>');
                //return false;
            }

    }
        else{
        window.location.href = '{{url('viewFamsPtype/')}}';
    }
        },
        error: function( _response ){
            // Handle error
            alert("error");
            
        }
    });
});
  });

</script>





  @endforeach


<script>
$(".itemCode").on('input',function () {
this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
});
</script>

@include('dataTableScript')
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
