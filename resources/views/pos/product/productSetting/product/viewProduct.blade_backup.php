@extends('layouts/pos_layout')
@section('title', '| Product')
@section('content')
@include('successMsg')
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('pos/posAddProduct/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Product</a>
          </div>
      
             <div class="row" id="filtering-group">
                            
                                <div class="form-horizontal form-groups" style="padding-right: 0px;">

                                    {!! Form::open(['url' => 'viewProduct','method' => 'get']) !!}
                                          {{ csrf_field() }}
                                   
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                             <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Group:', ['class' => 'control-label pull-left']) !!}
                                            </div> 

                                            <div class="col-sm-12">
                                                <select name="searchGroup" class="form-control input-sm" id="searchGroup">
                                                       <option value="">All</option>
                                                     @foreach($groups as $group)
                                                       <option value="{{$group->id}}" @if($group->id==$groupSelected){{"selected=selected"}}@endif>{{$group->name}}</option>
                                                    @endforeach                 
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                             <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Category:', ['class' => 'control-label pull-left']) !!}
                                            </div> 

                                            <div class="col-sm-12">
                                                <select name="searchCategory" class="form-control input-sm" id="searchCategory">
                                                    <option value="">All</option>
                                                      @foreach($categories as $category)
                                                    <option value="{{$category->id}}" @if($category->id==$categorySelected){{"selected=selected"}}@endif>{{$category->name}}</option>
                                                    @endforeach
                                                    
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                             <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Sub Category:', ['class' => 'control-label pull-left']) !!}
                                            </div> 

                                            <div class="col-sm-12">
                                                <select name="searchSubCategory" class="form-control input-sm" id="searchSubCategory">
                                                    <option value="">All</option>

                                                     @foreach($subCategories as $subCategory)
                                                    <option value="{{$subCategory->id}}" @if($subCategory->id==$subCategorySelected){{"selected=selected"}}@endif>{{$subCategory->name}}</option>
                                                    @endforeach
                                                    
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                             <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Brand:',['class' => 'control-label pull-left']) !!}
                                            </div> 

                                            <div class="col-sm-12">
                                                <select name="searchBrand" class="form-control input-sm" id="searchBrand">
                                                       <option value="">All</option>

                                                        @foreach($brands as $brand)
                                                       <option value="{{$brand->id}}" @if($brand->id==$brandSelected){{"selected=selected"}}@endif>{{$brand->name}}</option>
                                                    @endforeach
                                                   
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                     <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Model:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                <select name="searchModel" class="form-control input-sm" id="searchModel">
                                                      <option value="">All</option>

                                                       @foreach($models as $model)
                                                         <option value="{{$model->id}}" @if($model->id==$modelSelected){{"selected=selected"}}@endif>{{$model->name}}</option>
                                                    @endforeach
                                                   
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Size:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                <select name="searchSize" class="form-control input-sm" id="searchSize">
                                                    <option value="">All</option>

                                                      @foreach($sizes as $size)
                                                         <option value="{{$size->id}}" @if($size->id==$sizeSelected){{"selected=selected"}}@endif>{{$size->name}}</option>
                                                    @endforeach
                                                   
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                      <div class="form-group" style="font-size: 13px; color:black;">
                                           <div style="text-align: center;" class="col-sm-12">
                                              {!! Form::label('', 'Color:', ['class' => 'control-label pull-left']) !!}
                                          </div> 
                                  
                                          <div class="col-sm-12">
                                              <select name="searchColor" class="form-control input-sm" id="searchColor">
                                                  <option value="">All</option>

                                                   @foreach($colors as $color)
                                                         <option value="{{$color->id}}" @if($color->id==$colorSelected){{"selected=selected"}}@endif>{{$color->name}}</option>
                                                    @endforeach                                                 
                                                 
                                               </select>
                                  
                                          </div>
                                      </div>
                                  </div>  
                                
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black">

                                            <div class="col-sm-12" style="padding-top: 25px;">

                                                {!! Form::submit('Search',['id'=>'search','class'=>'btn btn-primary btn-xs','style'=>'font-size:15px;']) !!}
                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>                                    

                                </div>                            
                            
                        </div>
         


          
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PRODUCT LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#productView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="productView" style="color:black;">
            <thead>
                    <tr>
                      <th width="30">SL#</th>
                      <th>Product Name</th>
                      <th>Group</th>
                      <th>Catagory</th>
                      <th>Sub Catagory</th>
                      <th>Brand</th>
                      <th>Model</th>
                      <th>Size</th>
                      <th>Color</th>                      
                      <th>Cost Price</th>
                      <th>Seles Price</th>
                      <th>Action</th>
                    </tr>
                    {{ csrf_field() }}
                </thead>
                <tbody> 
                    <?php $no=0; ?>
                    @foreach($products as $product)
                      <tr class="item{{$product->id}}">
                        <td class="text-center slNo">{{++$no}}</td>
                        <td style="text-align: left; padding-left: 5px;font-weight: bold;">{{$product->name}}</td>
                        <td style="text-align: left; padding-left: 5px;">
                            <?php
                              $groupName = DB::table('pos_product_group')->select('name')->where('id',$product->groupId)->first();
                            ?>
                            {{$groupName->name}}
                        </td>
                        <td style="text-align: left; padding-left: 5px;">
                            <?php
                              $categoryName = DB::table('pos_product_category')->select('name')->where('id',$product->categoryId)->first();
                            ?>
                            {{$categoryName->name}}
                        </td>
                        <td style="text-align: left; padding-left: 5px;">
                            <?php
                              $subCategoryName = DB::table('pos_product_sub_category')->select('name')->where('id',$product->subCategoryId)->first();
                            ?>
                            {{$subCategoryName->name}}
                        </td>
                        <td style="text-align: left; padding-left: 5px;">
                            <?php
                              $productBrandName = DB::table('pos_product_brand')->select('name')->where('id',$product->brandId)->first();
                            ?>
                            {{$productBrandName->name}}
                        </td>
                        <td style="text-align: left; padding-left: 5px;">
                            <?php
                              $productModelName = DB::table('pos_product_model')->select('name')->where('id',$product->modelId)->first();
                            ?>
                            {{$productModelName->name}}
                        </td>
                        <td>
                            <?php
                              $ProductSizeName = DB::table('pos_product_size')->select('name')->where('id',$product->sizeId)->first();
                            ?>
                            {{$ProductSizeName->name}}
                        </td>
                        <td style="text-align: left; padding-left: 5px;">
                            <?php
                              $productColorName = DB::table('pos_product_color')->select('name')->where('id',$product->colorId)->first();
                            ?>
                            {{$productColorName->name}}
                        </td>
                        
                        <td style="text-align: right; padding-right: 5px;">{{$product->costPrice}}</td>
                        <td style="text-align: right; padding-right: 5px;">{{$product->salesPrice}}</td>
                        
                        <td class="text-center" width="100">
                        <a href="javascript:;" class="form5" data-token="" data-id="{{$product->id}}">
                            <span class="fa fa-eye"></span>
                        </a>
                        &nbsp
                        <a href="javascript:;" class="edit-modal" data-id="{{$product->id}}" data-name="{{$product->name}}" data-description="{{$product->description}}"  data-groupid="{{$product->groupId}}" data-categoryid="{{$product->categoryId}}" data-subcategoryid="{{$product->subCategoryId}}" data-brandid="{{$product->brandId}}" data-modelid="{{$product->modelId}}" data-sizeid="{{$product->sizeId}}" data-colorid="{{$product->colorId}}" data-costprice="{{$product->costPrice}}" data-salesprice="{{$product->salesPrice}}"  data-image="{{$product->image}}" data-slno="{{$no}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp
                        <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$product->id}}">
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
      {!! Form::open(array('url' => '', 'enctype' => 'multipart/form-data', 'role' => 'form',  'class'=>'form-horizontal form-groups','id'=>'editProducts')) !!}

                {!! Form::text('id', $value = null, ['class' => 'form-control hidden', 'id' => 'id', 'type' => 'text',]) !!}
                {!! Form::text('slno', $value = null, ['class' => 'form-control hidden', 'id' => 'slno', 'type' => 'text']) !!}
                <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('name', 'Product:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter product name']) !!}
                                <p id='namee' style="max-height:3px;"></p>
                        </div>
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('groupId', 'Group', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php 
                                    $GroupName = array('' => 'Please Select Product Group') + DB::table('pos_product_group')->pluck('name','id')->all(); 
                                ?>
                                {!! Form::select('groupId', ($GroupName), null, array('class'=>'form-control', 'id' => 'groupId')) !!}
                                <p id='groupIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('categoryId', 'Category:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            <?php 
                                $productCategoryId = array('' => 'Please Select Prduct Category') + DB::table('pos_product_category')->pluck('name','id')->all(); 
                            ?>      
                            {!! Form::select('categoryId', ($productCategoryId), null, array('class'=>'form-control', 'id' => 'categoryId')) !!}
                                <p id='productCategoryIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('subCategoryId', 'Sub Catagory:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            <?php 
                                $productSubCategoryId = array('' => 'Please Select Prduct SubCategory') + DB::table('pos_product_sub_category')->pluck('name','id')->all(); 
                            ?>  
                                {!! Form::select('subCategoryId', ($productSubCategoryId), null, array('class'=>'form-control', 'id' => 'subCategoryId')) !!}
                                <p id='productSubCategoryIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('brandId', 'Brand:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            <?php 
                                $productBrandId = array('' => 'Please Select Prduct Brand') + DB::table('pos_product_brand')->pluck('name','id')->all(); 
                            ?>
                                {!! Form::select('brandId', ($productBrandId), null, array('class'=>'form-control', 'id' => 'brandId')) !!}
                                <p id='productBrandIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('modelId', 'Model:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            <?php 
                                $productModelId = array('' => 'Please Select Prduct Model') + DB::table('pos_product_model')->pluck('name','id')->all(); 
                            ?>
                                {!! Form::select('modelId', ($productModelId), null, array('class'=>'form-control', 'id' => 'modelId')) !!}
                                <p id='productModelIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('sizeId', 'Size:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            <?php 
                                $productSizeId = array('' => 'Please Select Prduct Size') + DB::table('pos_product_size')->pluck('name','id')->all(); 
                            ?>
                                {!! Form::select('sizeId', ($productSizeId), null, array('class'=>'form-control', 'id' => 'sizeId')) !!}
                                <p id='productSizeIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('colorId', 'Color:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php 
                                    $productColorId = array('' => 'Please Select Prduct Color') + DB::table('pos_product_color')->pluck('name','id')->all(); 
                                ?>
                                {!! Form::select('colorId', ($productColorId), null, array('class'=>'form-control', 'id' => 'colorId')) !!}
                                <p id='colorIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        {{--<div class="form-group">
                            {!! Form::label('productPackge', 'Product Package:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                @php
                               $posProducts = DB::table('pos_product')->select('name','id')->get();
                               @endphp
                              
                              <select id="productPackge" name="productPackge[]" class="form-control" multiple="multiple">
                                <div id="packageHidden">
                                   @foreach ($posProducts as $posProduct)
                                       <option class="packageHidden" value="{{$posProduct->id}}" >{{$posProduct->name}}</option>
                                   @endforeach
                                 </div>  
                              </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('image', 'Upload Image:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::file('image', $value = null, ['class' => 'form-control', 'id' => 'image', 'type' => 'file']) !!}
                                 <p id='imageShow' style="padding-top:5px;"></p>
                                 <p id='imageShow2' style="padding-top:5px;"></p>
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div> --}}                                   
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('costPrice', 'Cost Price:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('costPrice', $value = null, ['class' => 'form-control', 'id' => 'costPrice', 'type' => 'text', 'placeholder' => 'Enter cost price']) !!}
                                <p id='costPricee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('salesPrice', 'Seles Price:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('salesPrice', $value = null, ['class' => 'form-control', 'id' =>'salesPrice', 'type' => 'text', 'placeholder' => 'Enter seles price']) !!}
                                <p id='selesPricee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('productPackge', 'Product Package:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                @php
                               $posProducts = DB::table('pos_product')->select('name','id')->get();
                               @endphp
                              
                              <select id="productPackge" name="productPackge[]" class="form-control" multiple="multiple" width="300">
                                <div id="packageHidden"> 
                                     @foreach ($posProducts as $posProduct)
                                         <option class="packageHidden" value="{{$posProduct->id}}" >{{$posProduct->name}}</option>
                                     @endforeach
                                </div>  
                              </select>
                            </div>
                        </div>

                         {{-- <div class="form-group">
                            {!! Form::label('openingStock', 'Opening Stock:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('openingStock', $value = null, ['class' => 'form-control', 'id' => 'openingStock', 'type' => 'text', 'placeholder' => 'Enter opening stock']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('openingStockAmount', 'O. Stock Amount:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('openingStockAmount', $value = null, ['class' => 'form-control', 'id' => 'openingStockAmount', 'type' => 'text', 'placeholder' => 'Enter opening stock amount']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('minimumStock', 'Minimum Stock:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('minimumStock', $value = null, ['class' => 'form-control', 'id' => 'minimumStock', 'type' => 'text', 'placeholder' => 'Enter minimum stock']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('vat', 'VAT:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('vat', $value = null, ['class' => 'form-control', 'id' => 'vat', 'type' => 'text', 'placeholder' => 'Enter VAT']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                         <div class="form-group">
                            {!! Form::label('barcode', 'Barcode:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('barcode', $value = null, ['class' => 'form-control', 'id' => 'barcode', 'type' => 'text', 'placeholder' => 'Enter Barcode']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('systemBarcode', 'System Barcode:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('systemBarcode', $value = null, ['class' => 'form-control', 'id' => 'systemBarcode', 'type' => 'text', 'placeholder' => 'Enter system barcode']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div> 
                        <div class="form-group">
                           {!! Form::label('warranty', 'Warranty:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('warranty', $value = null, ['class' => 'form-control', 'id' => 'warranty', 'type' => 'text', 'placeholder' => 'Enter warranty']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                           {!! Form::label('serviceWarranty', 'Service Warranty:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('serviceWarranty', $value = null, ['class' => 'form-control', 'id' => 'serviceWarranty', 'type' => 'text', 'placeholder' => 'Enter service warranty']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('compresserWarranty', 'Compresser Warranty:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('compresserWarranty', $value = null, ['class' => 'form-control', 'id' => 'compresserWarranty', 'type' => 'text', 'placeholder' => 'Enter compresser warranty']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div> --}}
                        <div class="form-group">
                            {!! Form::label('description', 'Decscription:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                    
                                {!! Form::textarea('description', $value = null, ['class' => 'form-control', 'id' => 'description', 'rows' => 2, 'placeholder' => 'Enter description']) !!}  
                                <p id='descriptione' style="max-height:3px;"></p>
                            </div>
                        </div>

                    </div>         
                         
                </div>
                </div>
                            
      <div class="Updatesuccessmsg" style="clear:both; padding-bottom:10px;" id="forSubmit">
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
        {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn',  'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}

        {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
      </div>
    </div>
  </div>
</div>
</div>
@include('pos/product/productSetting/product/productDetails')
@include('dataTableScript')


<!-- <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script> -->
<script type="text/javascript">
   $('#productPackge').select2();
   $(".select2-selection__rendered").empty(); 
$( document ).ready(function() {
  //$(".select2-selection__rendered").empty();

//delete image from folder when update
$(document).on('click', '.edit', function() {
  var image1 = $('#image1').attr('src');
  var image2 = $('#image2').attr('src');
  var replacedImage1 = image1.substring(image1.indexOf("/images") + 1);
if(image1 && image2){
$.ajax({
    type: 'post',
    url: './posProimageDelete',
    data: {
      '_token': $('input[name=_token]').val(),
      'replacedImage1': replacedImage1
    },
    success: function(data) {
      //alert(data);
    },
    error: function( data ){
            // Handle error  
            alert('hi');    
        }
  });
}
});

//update image show
$('#image').on('change',function(){
  $("#imageShow").hide();
  $('#imageShow2').empty();
  var files = !!this.files ? this.files:[];
  if(!files.length || !window.FileReader) return;
  if(/^image/.test(files[0].type)){
    var reader = new FileReader();
    reader.readAsDataURL(files[0]);
    reader.onloadend = function(){
      $('#imageShow2').append('<img src="'+this.result+'" width="100" height="50" id="image2"/>');
    }
  }
});

$(document).on('click', '.edit-modal', function() {

     
 
    $('#imageShow').empty();
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
    $('.modal-dialog').css('width','80%');
    $('.deleteContent').hide();
    $('.modal-footer').hide();
    $('.form-horizontal').show();
    $('#id').val($(this).data('id'));
    $('#slno').val($(this).data('slno'));
    $('#name').val($(this).data('name'));
    $('#groupId').val($(this).data('groupid'));
    $('#categoryId').val($(this).data('categoryid'));
    $('#subCategoryId').val($(this).data('subcategoryid'));
    $('#brandId').val($(this).data('brandid'));
    $('#modelId').val($(this).data('modelid'));
    $('#sizeId').val($(this).data('sizeid'));
    $('#colorId').val($(this).data('colorid'));
    $('#description').val($(this).data('description'));
    $('#costPrice').val($(this).data('costprice'));
    $('#salesPrice').val($(this).data('salesprice'));

    //alert(JSON.stringify( $('#costPrice').val($(this).data('costprice'))));
    /*$('#openingStock').val($(this).data('openingstock'));
    $('#openingStockAmount').val($(this).data('openingstockamount'));
    $('#minimumStock').val($(this).data('minimumstock'));
    $('#vat').val($(this).data('vat'));
    $('#barcode').val($(this).data('barcode'));
    $('#systemBarcode').val($(this).data('systembarcode'));
    $('#warranty').val($(this).data('warranty'));
    $('#serviceWarranty').val($(this).data('servicewarranty'));
    $('#compresserWarranty').val($(this).data('compresserwarranty'));*/
    var imageName = ($(this).data('image'));
    //alert(imageName);
    $('#imageShow').append('<img src="{{ asset("images/product/") }}'+ '/' + imageName +'" width="100" height="50" id="image1"/>');
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
   

 
    
});

    // Edit Data (Modal and function edit data)
  $('#editProducts').submit(function( event ) {
    event.preventDefault();
    var formData = new FormData($(this)[0]);
    $.ajax({
         type: 'post',
         url: './posEditProductItem',
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
                $('#namee').show();
                $('#namee').append('<span class="errormsg" style="color:red;">'+data.errors.name+'</span>');
                //return false;
            }
           
            if (data.errors['groupId']) {
                $('#groupIde').empty();
                $('#groupIde').append('<span class="errormsg" style="color:red;">'+data.errors.groupId+'</span>');
                //return false;
            }
            if (data.errors['categoryId']) {
                $('#productCategoryIde').empty();
                $('#productCategoryIde').append('<span class="errormsg" style="color:red;">'+data.errors.categoryId+'</span>');
                //return false;
            }
            if (data.errors['subCategoryId']) {
                $('#productSubCategoryIde').empty();
                $('#productSubCategoryIde').append('<span class="errormsg" style="color:red;">'+data.errors.subCategoryId+'</span>');
                //return false;
            }
            if (data.errors['brandId']) {
                $('#productBrandIde').empty();
                $('#productBrandIde').append('<span class="errormsg" style="color:red;">'+data.errors.brandId+'</span>');
                //return false;
            }
            if (data.errors['modelId']) {
                $('#productModelIde').empty();
                $('#productModelIde').append('<span class="errormsg" style="color:red;">'+data.errors.modelId+'</span>');
                //return false;
            }
            if (data.errors['sizeId']) {
                $('#productSizeIde').empty();
                $('#productSizeIde').append('<span class="errormsg" style="color:red;">'+data.errors.sizeId+'</span>');
                //return false;
            }
            if (data.errors['colorId']) {
                $('#colorIde').empty();
                $('#colorIde').append('<span class="errormsg" style="color:red;">'+data.errors.colorId+'</span>');
                //return false;
            }
            if (data.errors['uomId']) {
                $('#uomIde').empty();
                $('#uomIde').append('<span class="errormsg" style="color:red;">'+data.errors.uomId+'</span>');
                //return false;
            }
            
        }else{
            location.reload();
          }
        
      },
      error: function( data ){
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
  $('.modal-footer').show();
  $('.form-horizontal').hide();
  $('#footer_action_button2').show();
  $('#footer_action_button').hide();
  $('#myModal').modal('show');

});

$('.modal-footer').on('click', '.delete', function() {
  $.ajax({
    type: 'post',
    url: './posDeleteProductItem',
    data: {
      '_token': $('input[name=_token]').val(),
      'id': $('.id').text()
    },
    success: function(data) {
        //alert(data);
        //var image = data.image;
        //var replacedImage1 = 'images/product/'+image;
           /* $.ajax({
              type: 'post',
              url: './posProimageDelete',
              data: {
                  '_token': $('input[name=_token]').val(),
                  //'replacedImage1': replacedImage1
                },
              success: function(data) {
                  $('.item' + $('.id').text()).remove();
                  }
            });*/
      $('.item' + $('.id').text()).remove();
    }
  });
});

//product details model
$(document).on('click', '.form5', function() {
  
    $('#imageShow3').empty();
    var id = ($(this).data('id'));
    var crsf = ($(this).data('token'));
    //alert(id);alert(crsf);
    $.ajax({
         type: 'post',
         url: './posProductDetail',
         data: {
            '_token': $('input[name=_token]').val(),
            'id': id
         },
         dataType: 'json',
        success: function( data ){
            //alert(JSON.stringify(data));
    $.each(data, function( index, value ){
        //alert(data["supplierName"].supplierCompanyName);
        $('#ProductName').text(data.productName);
        $('#Id').text(data.productIdNo);
        $('#Description').text(data.description);
        $('#Group').text(data["groupName"].name);
        $('#Catagory').text(data["categoryName"].name);
        $('#SubCatagory').text(data["subCategoryName"].name);
        $('#Brand').text(data["productBrandName"].name);
        $('#Model').text(data["productModelName"].name);
        $('#Size').text(data["ProductSizeName"].name);
        $('#Color').text(data["productColorName"].name);
        $('#CostPrice').text(data.costPrice);
        $('#SalesPrice').text(data.salesPrice);
        /*$('#OpeningSt').text(data.openingStock);
        $('#OpeningStAmount').text(data.openingStockAmount);
        $('#MinimumSt').text(data.minimumStock);
        $('#Vat').text(data.vat);
        $('#Barcode').text(data.barcode);
        $('#BarcodeSystem').text(data.systemBarcode);
        $('#Warranty').text(data.warranty);
        $('#ServiceWarranty').text(data.serviceWarranty);
        $('#CompresserWarranty').text(data.compresserWarranty);*/
        
        });
        /*var imageName = data.imageName;
        $('#imageShow3').append('<img src="{{ asset("images/product/") }}'+ '/' + imageName +'" width="300" height="300" class="amni"/>');*/
            $('.modal-title').text('Product Details');
            $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
            $('.modal-dialog').css('width','90%');
            $('.deleteContent').hide();
            $('.modal-footer').hide();
            $('.form-horizontal').show();
            $('#myModal2').modal('show');  
        },
        error: function( data ){
        alert();
        }
    });

    
});

$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
             var costPrice = $("#costPrice").val();
            if(costPrice){$('#costPricee').hide();}else{$('#costPricee').show();}
             var salesPrice = $("#salesPrice").val();
            if(salesPrice){$('#selesPrice').hide();}else{$('#selesPricee').show();}
});
$('select').on('change', function (e) {
    var groupId = $("#groupId").val();
    if(groupId){$('#groupIde').hide();}else{$('#groupIde').show();}
    /*var supplierId = $("#supplierId").val();
    if(supplierId){$('#supplierIde').hide();}else{$('#supplierIde').show();}*/
    var categoryId = $("#categoryId").val();
    if(categoryId){$('#productCategoryIde').hide();}else{$('#productCategoryIde').show();}
    var subCategoryId = $("#subCategoryId").val();
    if(subCategoryId){$('#productSubCategoryIde').hide();}else{$('#productSubCategoryIde').show();}
    var brandId = $("#brandId").val();
    if(brandId){$('#productBrandIde').hide();}else{$('#productBrandIde').show();}
    var modelId = $("#modelId").val();
    if(modelId){$('#productModelIde').hide();}else{$('#productModelIde').show();}
    var sizeId = $("#sizeId").val();
    if(sizeId){$('#productSizeIde').hide();}else{$('#productSizeIde').show();}
    var colorId = $("#colorId").val();
    if(colorId){$('#colorIde').hide();}else{$('#colorIde').show();}
    /*var uomId = $("#uomId").val();
    if(uomId){$('#uomIde').hide();}else{$('#uomIde').show();}*/
});

});//ready function end
</script>

<script type="text/javascript">
    
   
     jQuery(document).ready(function($) {

       $("#searchGroup").change(function(){
            var productGroupId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './invOnGroupChange',
                data: {productGroupId:productGroupId,_token: csrf},
                async: false,
                dataType: 'json',
                success: function( data ){

                   $("#searchCategory").empty();
                    $("#searchCategory").prepend('<option selected="selected" value="">All</option>');

                 $.each(data['categories'], function (key, productObj) {                       
                    $('#searchCategory').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");                        
                    });

                    $("#searchSubCategory").empty();
                    $("#searchSubCategory").prepend('<option selected="selected" value="">All</option>');

                 $.each(data['subCategories'], function (key, productObj) {                       
                    $('#searchSubCategory').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");                        
                    });
                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Category*/
   
    });

    </script>
    
    
      <script type="text/javascript">
      $(document).ready(function(){
        $("#searchCategory").change(function(){
            var productCategoryId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './posCategoryChange',
                data: {productCategoryId:productCategoryId,_token: csrf},
                async: false,
                dataType: 'json',
                success: function( data ){
                    $("#searchSubCategory").empty();
                    $("#searchSubCategory").prepend('<option selected="selected" value="">All</option>');

                 $.each(data['subCategories'], function (key, productObj) {                       
                    $('#searchSubCategory').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");                        
                    });                  
                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Category*/

        });
     </script> 
    
   <script type="text/javascript"> 
    /* Change brand*/
      $(document).ready(function(){
    
        $("#searchBrand").change(function(){
            var productBrandId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './invBrandChange',
                data: {productBrandId:productBrandId,_token: csrf},
                async: false,
                dataType: 'json',
                success: function( data ){
                    $("#searchModel").empty();
                    $("#searchModel").prepend('<option selected="selected" value="">All</option>');

                 $.each(data['brands'], function (key, brandObj) {                       
                    $('#searchModel').append("<option value='"+ brandObj.id+"'>"+(brandObj.name)+"</option>");
                        
                    });
                 },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change brand*/

     $('#searchGroup').find('select').trigger('change');
      $('#searchCategory').find('select').trigger('change');
      $('#searchBrand').find('select').trigger('change');
     });

</script>
@endsection