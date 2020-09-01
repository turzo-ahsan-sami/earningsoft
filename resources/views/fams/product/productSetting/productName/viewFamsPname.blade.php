@extends('layouts/fams_layout')
@section('title', '| Product Name')
@section('content')
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addFamsPname/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Product Name</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PRODUCT NAME LIST</font></h1>
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
                        <th>Product Type</th>
                        <th>Action</th>
                      </tr>
                      
                    </thead>
                    <tbody>
                      <?php $no=0; ?>
                      @foreach($productNames as $productName)
                       @php
                    
                    $isBelongToProduct = DB::table('fams_product')->where('productNameId',$productName->id)->value('id');
                   
                  @endphp

                        <tr class="item{{$productName->id}}">
                          <td class="text-center slNo">{{++$no}}</td>
                          <td style="text-align: left; padding-left: 15px;">
                              {{$productName->name}}
                          </td>
                          <td>
                            {{$productName->productNameCode}}
                          </td>
                          
                          <td style="text-align: left; padding-left: 15px;">
                          @php
                            $groupName = DB::table('fams_product_group')->where('id',$productName->productGroupId)->value('name');
                          @endphp
                              {{$groupName}}
                          </td>
                          <td style="text-align: left; padding-left: 15px;">
                          @php
                            $categoryName = DB::table('fams_product_category')->where('id',$productName->productCategoryId)->value('name');
                          @endphp
                              {{$categoryName}}
                          </td>
                          <td style="text-align: left; padding-left: 15px;">
                          @php
                            $subCategoryName = DB::table('fams_product_sub_category')->where('id',$productName->productSubCategoryId)->value('name');
                          @endphp
                              {{$subCategoryName}}
                          </td>
                          <td style="text-align: left; padding-left: 15px;">
                          @php
                            $productTypeName = DB::table('fams_product_type')->where('id',$productName->productTypeId)->value('name');
                          @endphp
                              {{$productTypeName}}
                          </td>
                          <td  class="text-center" width="80">
                            <a href="" id={{$productName->id}} data-toggle="modal" data-target="#edit-modal-{{$productName->id}}" >
                              <span class="glyphicon glyphicon-edit"></span>
                            </a>&nbsp
                            <a href="" data-toggle="modal" data-target="#delete-modal-{{$productName->id}}" @php if($isBelongToProduct>0){echo "style=\"pointer-events: none;cursor: not-allowed;\"";} @endphp>
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

@foreach($productNames as $prodName)


  {{-- Edit Modal --}}

  <div id="edit-modal-{{$prodName->id}}" class="modal fade edit-modal" style="margin-top:3%;" data-keyboard="false" data-backdrop="static">

    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Item</h4>
        </div>

        <div class="modal-body" >
          {!! Form::open(['url' => '','id'=>'form-'.$prodName->id,'class'=>'form-horizontal form-groups']) !!}
          {!! Form::hidden('productNameId',$prodName->id,['id'=>'productNameId-'.$prodName->id]) !!}
          <div class="form-group">
                {!! Form::label('productGroupId', 'Group:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">
                <?php 
                 $ProductGroup = array('' => 'Please Select Product Group') + DB::table('fams_product_group')->pluck('name','id')->all(); 
                  ?>      
                 {!! Form::select('productGroupId', ($ProductGroup), $prodName->productGroupId, array('class'=>'form-control', 'id' => 'productGroupId-'.$prodName->id)) !!}
                     <p id='productGroupIde-{{$prodName->id}}' style="max-height:3px;"></p>
                       </div>
         </div>
            <div class="form-group">
                {!! Form::label('productCategoryId', 'Category:', ['class' => 'col-sm-3 control-label']) !!}
                   <div class="col-sm-9">
                   <?php 
                      $productCategoryId = array('' => 'Please Select Product Category') + DB::table('fams_product_category')->pluck('name','id')->all(); 
                    ?>      
                         {!! Form::select('productCategoryId', ($productCategoryId), $prodName->productCategoryId, array('class'=>'form-control', 'id' => 'productCategoryId-'.$prodName->id)) !!}
                          <p id='productCategoryIde-{{$prodName->id}}' style="max-height:3px;"></p>
                         </div>
                                </div>
                                <div class="form-group">
                                        {!! Form::label('productSubCategoryId', 'Sub Category:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        <?php 
                                            $productSubCategoryId = array('' => 'Please Select Product Sub Category') + DB::table('fams_product_sub_category')->pluck('name','id')->all(); 
                                        ?>      
                                        {!! Form::select('productSubCategoryId', ($productSubCategoryId), $prodName->productSubCategoryId, array('class'=>'form-control', 'id' => 'productSubCategoryId-'.$prodName->id)) !!}
                                            <p id='productSubCategoryIde-{{$prodName->id}}' style="max-height:3px;"></p>
                                        </div>
                                </div>
                                <div class="form-group">
                                        {!! Form::label('productTypeId', 'Product Type:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        <?php 
                                            $productTypeId = array('' => 'Please Select Product Type') + DB::table('fams_product_type')->pluck('name','id')->all(); 
                                        ?>      
                                        {!! Form::select('productTypeId', ($productTypeId), $prodName->productTypeId, array('class'=>'form-control', 'id' => 'productTypeId-'.$prodName->id)) !!}
                                            <p id='productTypeIde-{{$prodName->id}}' style="max-height:3px;"></p>
                                        </div>
                                </div>
                                
                                <div class="form-group">
                                    {!! Form::label('productName', 'Product Name:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('name', $value = $prodName->name, ['class' => 'form-control', 'id' => 'name-'.$prodName->id, 'type' => 'text', 'placeholder' => 'Enter Product Type Name']) !!}
                                        <p id='namee-{{$prodName->id}}' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                    <div class="form-group">
                                        {!! Form::label('productNameCode', 'Product Type Code:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('productNameCode', $value = $prodName->productNameCode, ['class' => 'form-control', 'id' => 'productNameCode-'.$prodName->id, 'type' => 'text', 'placeholder' => 'Enter Product Type Code']) !!}
                                            <p id='productNameCodee-{{$prodName->id}}' style="max-height:3px;"></p>
                                        </div>
                                    </div>
          <div class="modal-footer">
          <div class="form-group">
            <div class="col-sm-12">
            
               <button  type="button" class="btn actionBtn glyphicon glyphicon-check btn-success confirmButton" id="summitButton-{{$prodName->id}}" modalId={{$prodName->id}}><span> Update</span></button> 
              <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span> Close</span></button>
            </div>
          </div>
            </div>

          {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>


  {{-- Delete Modal --}}
  <div id="delete-modal-{{$prodName->id}}" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
        </div>
        <div class="modal-body">
          <h2>Are You Confirm to Delete This Item?</h2>

          <div class="modal-footer">
            {!! Form::open(['url' => 'deleteFamsItem/']) !!}
            <input type="hidden" name="itemId" value={{$prodName->id}}>
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
    var modalId = "{{$prodName->id}}";
    
   $('#summitButton-'+modalId).on('click',function() {

    

    var csrf = "<?php echo csrf_token(); ?>";
    var productGroupId = $("#productGroupId-"+modalId).val();
    var productCategoryId = $("#productCategoryId-"+modalId).val();
    var productSubCategoryId = $("#productSubCategoryId-"+modalId).val();
    var productTypeId = $("#productTypeId-"+modalId).val();
    var name = $("#name-"+modalId).val();
    var productNameCode = $("#productNameCode-"+modalId).val();
    var productNameId = $("#productNameId-"+modalId).val();


// alert(name);
// alert(productNameCode);
// alert(productNameId);
// alert(productGroupId);
// alert(productCategoryId);
// alert(productSubCategoryId);
    
    //event.preventDefault();
    
    $.ajax({
         type: 'post',
         url: './editFamsPname',
         data: {productNameId: productNameId, name: name, productNameCode: productNameCode,productGroupId: productGroupId, productCategoryId: productCategoryId,productSubCategoryId: productSubCategoryId,productTypeId: productTypeId, _token: csrf },
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
            if (_response.errors['productTypeId']) {
                $('#productTypeIde-'+modalId).empty();
                $('#productTypeIde-'+modalId).show();
                $('#productTypeIde-'+modalId).append('<span class="errormsg" style="color:red;">'+_response.errors.productTypeId+'</span>');
                //return false;
            }

    }
        else{
        window.location.href = '{{url('viewFamsPname/')}}';
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



@include('dataTableScript')
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
