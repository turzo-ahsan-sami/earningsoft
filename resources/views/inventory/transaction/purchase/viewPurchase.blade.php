@extends('layouts/inventory_layout')
@section('title', '| Purchase')
@section('content')
@include('successMsg')

@php
$purchaseBillNos = DB::table('inv_purchase_return')->distinct()->pluck('purchaseBillNo')->toArray(); 
$foreignPurchaseIds = DB::table('inv_purchase')->whereIn('billNo',$purchaseBillNos)->distinct()->pluck('id')->toArray(); 
@endphp


<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$gnrBranchId = Session::get('branchId');
$logedUserName = $user->name;
?>
<div class="row">
  <div class="col-md-12">
    <div class="" style="">
      <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
          <div class="panel-heading"  style="padding-bottom:0px">
            <div class="panel-options">
              <a href="{{url('addInvPurchaseRequiF/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Purchase</a>
            </div>
            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PURCHASE LIST</font></h1>
          </div>
          <div class="panel-body panelBodyView"> 
            <div>
              <script type="text/javascript">
                jQuery(document).ready(function($)
                {
                  $("#invPurchaseView").dataTable().yadcf([

                    ]);
                });
              </script>
            </div>
            <table class="table table-striped table-bordered" id="invPurchaseView">
              <thead>
                <tr>
                  <th width="32">SL#</th>
                  <th>Date</th>
                  <th>Bill No</th>
                  <th>Order No</th>
                  <th>Supplier Name</th>
                  <th>Purchase By</th>
                  
                  <th>Branch Name</th>
                  <th>Totlal Quantity</th>
                  <th>Gross Totlal</th>

                  <th>Action</th>
                </tr>
                {{ csrf_field() }}
              </thead>
              <tbody> 
                <?php $no=0; ?>
                @foreach($purchases as $purchase)
                <tr class="item{{$purchase->id}}">
                  <td class="text-center slNo">{{++$no}}</td>
                  <td>{{date('d-m-Y', strtotime($purchase->purchaseDate))}}</td>
                  <td style="text-align: left; padding-left: 5px;">{{$purchase->billNo}}</td>
                  <td style="text-align: left; padding-left: 5px;">{{$purchase->orderNo}}</td>
                  <td style="text-align: left; padding-left: 5px;">
                    <?php
                    $supplierName = DB::table('gnr_supplier')->select('supplierCompanyName')->where('id',$purchase->supplierId)->first();
                    ?>
                    {{$supplierName->supplierCompanyName}}
                  </td>

                  @php

                  $employeeNameId = DB::table('hr_emp_general_info')->select('emp_name_english')->where('id',$purchase->createdBy)->first();

                  @endphp
                  <td style="text-align: left; padding-left: 5px;">

                    {{--  {{$article->poster['name']}} --}}
                    {{@$employeeNameId->emp_name_english}}</td>

                    <td style="text-align: left; padding-left: 5px;">
                      <?php
                      $branchName = DB::table('gnr_branch')->select('name')->where('id',$purchase->branchId)->first();


                      ?>
                      {{$branchName->name}}
                    </td>

                    <td style="text-align: right; padding-right: 5px;">{{$purchase->totalQuantity}}</td>
                    <td style="text-align: right; padding-right: 5px;">{{$purchase->grossTotal}}</td>

                    <td class="text-center" width="80">
                      <a href="javascript:;" class="forPurchaseDetailsModel" data-token="{{csrf_token()}}" data-id="{{$purchase->id}}">
                        <span class="fa fa-eye"></span>
                      </a>&nbsp
                      <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$purchase->id}}" data-billno="{{$purchase->billNo}}"data-orderno="{{$purchase->orderNo}}" data-orderdate="{{$purchase->orderDate}}" data-chalanno="{{$purchase->chalanNo}}" data-chalandate="{{$purchase->chalanDate}}" data-projectid="{{$purchase->projectId}}" data-projecttypeid="{{$purchase->projectTypeId}}" data-supplierid="{{$purchase->supplierId}}" data-contactperson="{{$purchase->contactPerson}}" data-discount="{{$purchase->discount}}" data-purchasedate="{{$purchase->purchaseDate}}" data-totalquantity="{{$purchase->totalQuantity}}" data-totalamount="{{$purchase->totalAmount}}" data-amountafterdiscount="{{$purchase->amountAfterDiscount}}" data-vat="{{$purchase->vat}}" data-grosstotal="{{$purchase->grossTotal}}" data-payamount="{{$purchase->payAmount}}" data-due="{{$purchase->due}}" data-paymentstatus="{{$purchase->paymentStatus}}" data-remark="{{$purchase->remark}}" data-createddate="{{$purchase->createdDate}}" data-slno="{{$no}}">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>&nbsp

                      @php
                      if (in_array($purchase->id, $foreignPurchaseIds)) {
                        $canDelete = 0;
                      }
                      else{
                        $canDelete = 1;
                      }   
                      @endphp

                      <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$purchase->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
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
         {!! Form::open(array('url' => '', 'role' => 'form',  'class'=>'form-horizontal form-groups', 'id' => 'purchaseForm')) !!}

         <div class="row">
           <div class="col-md-6">   
            <div class="form-group hidden">
              {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
              <div class="col-sm-10">
                {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text']) !!}
                {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text']) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('billNo', 'Bill No:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('billNo', null, ['class' => 'form-control', 'id' => 'billNo', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                <p id='billNoe' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('orderNo', 'Order No:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('orderNo', null, ['class' => 'form-control', 'id' => 'orderNo', 'type' => 'text','autocomplete'=>'off']) !!}
                <p id='orderNoe' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('orderDate', 'Order Date:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('orderDate', null, ['class' => 'form-control', 'id' => 'orderDate','readonly']) !!}
                <p id='ordarDatee' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('chalanNo', 'Chalan No:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">

                {!! Form::text('chalanNo', null, array('class'=>'form-control', 'id' => 'chalanNo','type'=>'text')) !!}
                <p id='chalanNoe' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('chalanDate', 'Chalan Date:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('chalanDate', null, ['class' => 'form-control', 'id' => 'chalanDate','readonly']) !!}
                <p id='chalanDatee' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('projectId', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                <?php 

                $projectId  =  array('' => 'Please Select') +DB::table('gnr_project')->pluck('name','id')->all();

                ?> 
                {!! Form::select('projectId', ($projectId), null, array('class'=>'form-control', 'id' => 'projectId')) !!} 
                <p id='projectIde' style="max-height:3px;"></p>
              </div>

            </div>

            <div class="form-group">
              {!! Form::label('projectTypeId', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                <?php 

                $projecttypeId  =  array('' => 'Please Select') + DB::table('gnr_project_type')->pluck('name','id')->all();

                ?> 
                {!! Form::select('projectTypeId', ($projecttypeId), null, array('class'=>'form-control', 'id' => 'projectTypeId')) !!}  
                <p id='projectTypeIde' style="max-height:3px;"></p>
              </div>

            </div>


            <div class="form-group">
              {!! Form::label('supplierId', 'Supplier Name:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                <?php 
                $supplierId = array('' => 'Please Select') + DB::table('gnr_supplier')->pluck('supplierCompanyName','id')->all(); 
                ?>      
                {!! Form::select('supplierId', ($supplierId), null, array('class'=>'form-control', 'id' => 'supplierId')) !!}
                <p id='supplierIde' style="max-height:3px;"></p>
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('contactPerson', 'Contact Person', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('contactPerson', null, ['class' => 'form-control', 'id' => 'contactPerson', 'type' => 'text','autocomplete'=>'off', 'readonly']) !!}
                <p id='contactpersone' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('remark', 'Remark:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::textarea('remark', null, ['class' => 'form-control', 'rows'=>2, 'id' => 'remark']) !!}
                <p id='remarke' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('purchaseDate', 'Purchase Date:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('purchaseDate', null, ['class' => 'form-control', 'id' => 'purchaseDate','readonly']) !!}
                <p id='purchaseDatee' style="max-height:3px;"></p>
              </div> 
            </div>

          </div>    
          <div class="col-md-6">

            <div class="form-group">
              {!! Form::label('totalQuantity', 'Total Quantity:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('totalQuantity', null, ['class' => 'form-control numeric', 'id' => 'totalQuantity', 'readonly']) !!}
                <p id='totalQuantitye' style="max-height:3px; color:red;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('totalAmount', 'Total Amount:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('totalAmount', null, ['class' => 'form-control numeric', 'id' => 'totalAmount', 'readonly']) !!}
                <p id='totalAmounte' style="max-height:3px; color:red;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('discount', 'Discount:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                <div class="col-sm-5" style="padding-left: 0%">
                  {!! Form::text('discountPercent', null, ['class' => 'form-control numeric col-sm-5', 'id' => 'discountPercent', 'placeholder'=>'%']) !!}
                </div>

                <div class="col-sm-7" style="padding-right: 0%; padding-left: 0%">
                  {!! Form::text('discount', null, ['class' => 'form-control col-sm-7 numeric', 'id' => 'discount']) !!}
                </div>
                <p id='discounte' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('amountAfterDiscount', 'T/A After Discount:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('amountAfterDiscount', null, ['class' => 'form-control', 'id' => 'amountAfterDiscount', 'readonly']) !!}
                <p id='amountAfterDiscounte' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('vat', 'VAT:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                <div class="col-sm-5" style="padding-left: 0%">
                  {!! Form::text('vatPercent', null, ['class' => 'form-control numeric col-sm-5', 'id' => 'vatPercent', 'placeholder'=>'%']) !!}
                </div>
                <div class="col-sm-7" style="padding-right: 0%; padding-left: 0%">
                  {!! Form::text('vat', null, ['class' => 'form-control col-sm-7 numeric', 'id' => 'vat', 'readonly']) !!}
                </div>
                <p id='vate' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('grossTotal', 'Gross Total:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('grossTotal', null, ['class' => 'form-control', 'id' => 'grossTotal', 'readonly']) !!}
                <p id='grossTotale' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('payAmount', 'Pay Amount:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('payAmount', null, ['class' => 'form-control numeric', 'id' => 'payAmount']) !!}
                <p id='payAmounte' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('due', 'Purchase Due:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('due', null, ['class' => 'form-control numeric', 'id' => 'due']) !!}
                <p id='duee' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group">
              {!! Form::label('paymentStatus', 'Payment Status:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('paymentStatus', null, ['class' => 'form-control', 'id' => 'paymentStatus']) !!}
                <p id='paymentStatuse' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group hidden">
              {!! Form::label('createdDate', 'createdDate:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('createdDate', null, ['class' => 'form-control', 'id' => 'createdDate']) !!}
                <p id='createdDatee' style="max-height:3px;"></p>
              </div> 
            </div>

            <div class="form-group hidden">
              {!! Form::label('branchId', 'Branch Name:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                <?php 
                $branchNames = DB::table('gnr_branch')->where('id',$gnrBranchId)->get(); 
                ?>
                <select class ="form-control" id = "branchId" autocomplete="off" name="branchId" readonly>
                  @foreach($branchNames as $branchName)
                  <option value="{{$branchName->id}}">{{$branchName->name}}</option>
                  @endforeach
                </select>
                <p id='branchIde' style="max-height:3px;"></p>
              </div>
            </div>

            <div class="form-group hidden">
              {!! Form::label('createdBy', 'Created By:', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-sm-8">
                {!! Form::text('createdBy', $logedUserName, ['class' => 'form-control', 'id' => 'createdBy']) !!}
                <p id='branchIde' style="max-height:3px;"></p>

              </div>
            </div>
            <p id='numericError' style="max-height:3px; color:red;"></p>                     
          </div>                  
        </div>       
        <!-- filtering -->
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('productGroupId', 'Group:', ['class' => 'control-label col-sm-12']) !!}
              <div class="col-sm-12">
                <?php 
                $groupIds =  DB::table('inv_product')->select('groupId')->get();
                $groupArSize = count($groupIds);
                if($groupArSize>0){
                  foreach($groupIds as $groupId){
                    $groupName [] =  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->first();   
                  }
                  $groupNames = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
                }
                ?>   
                <select name="productGroupId" class="form-control input-sm" id="productGroupId">
                  <option value="">Please select</option>
                  <?php if($groupArSize>0): ?>
                  @foreach($groupNames as $groupName)
                  <option value="{{$groupName->id}}">{{$groupName->name}}</option>
                  @endforeach
                  <?php endif ; ?>
                </select>

              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('productCategoryId', 'Category:', ['class' => 'control-label col-sm-12']) !!}
              <div class="col-sm-12">
                <?php 
                $categoryIds =  DB::table('inv_product')->select('categoryId')->get();
                $catArSize = count($categoryIds);
                if($catArSize>0){
                  foreach($categoryIds as $categoryId){
                    $categoryName [] =  DB::table('inv_product_category')->select('name','id')->where('id',$categoryId->categoryId)->first();   
                  }
                  $categoryNames = array_map("unserialize", array_unique(array_map("serialize", $categoryName)));
                }
                ?>
                <select name="productCategoryId" class="form-control input-sm" id="productCategoryId">
                  <option value="">Please select</option>
                  <?php if($catArSize>0): ?>
                  @foreach($categoryNames as $categoryName)
                  <option value="{{$categoryName->id}}">{{$categoryName->name}}</option>
                  @endforeach
                  <?php endif ; ?>
                </select>

              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('productSubCategoryId', 'Subcatagory:', ['class' => 'control-label col-sm-12']) !!}
              <div class="col-sm-12">
                <?php 
                $subCategoryIds =  DB::table('inv_product')->select('subCategoryId')->get();
                $subCatArSize = count($subCategoryIds);
                if($subCatArSize>0){
                  foreach($subCategoryIds as $subCategoryId){
                    $subCategoryName [] =  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCategoryId->subCategoryId)->first();   
                  }
                  $subCategoryNames = array_map("unserialize", array_unique(array_map("serialize", $subCategoryName)));
                }
                ?>
                <select name="productSubCategoryId" class="form-control input-sm" id="productSubCategoryId">
                  <option value="">Please select</option>
                  <?php if($subCatArSize>0): ?>
                  @foreach($subCategoryNames as $subCategoryName)
                  <option value="{{$subCategoryName->id}}">{{$subCategoryName->name}}</option>
                  @endforeach
                  <?php endif ; ?>
                </select>

              </div>
            </div>
          </div>

          <div class="col-md-3 hidden">
            <div class="form-group">
              {!! Form::label('productBrandId', 'Brand:', ['class' => 'control-label col-sm-12']) !!}
              <div class="col-sm-12">
                <?php 
                $brandIds =  DB::table('inv_product')->select('brandId')->get();
                $bndArSize = count($brandIds);
                if($bndArSize){
                  foreach($brandIds as $brandId){
                    $brandName [] =  DB::table('inv_product_brand')->select('name','id')->where('id',$brandId->brandId)->first();   
                  }
                  $brandNames = array_map("unserialize", array_unique(array_map("serialize", $brandName)));
                }
                ?>
                <select name="productBrandId" class="form-control input-sm" id="productBrandId">
                  <option value="">Please select</option>
                  <?php if($bndArSize>0): ?>
                  @foreach($brandNames as $brandName)
                  <option value="{{$brandName->id}}">{{$brandName->name}}</option>
                  @endforeach
                  <?php endif ; ?>
                </select>

              </div>
            </div>
          </div>
        </div>    
        <!-- filtering end-->

        <div class="row">
          <div class="col-md-12">
            <table id="addProductTable" class="table table-bordered responsive addProductTable">
              <thead>
                <tr class="">
                  <th style="text-align:center;" class="col-sm-3">Item Name</th>
                  <th style="text-align:center;" class="col-sm-2">Qty</th>
                  <th style="text-align:center;" class="col-sm-2">Price</th>
                  <th style="text-align:center;" class="col-sm-2">Total</th>
                  <th style="text-align:center;" class="col-sm-1">Remove</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <?php 
                    $productId = array('' => 'Please Select product') /*+ DB::table('inv_product')->pluck('name','id')->all()*/; 
                    ?>
                    {!! Form::select('productId', ($productId), null, array('class'=>'form-control input-sm', 'id' => 'productId')) !!}
                  </td>
                  <td class="">
                    <input type='number' class="form-control input-sm text-center" id='productQntty' name='productQntty[]' value='' placeholder='Insert Item' min="1"/>
                  </td>
                  <td class="">
                    <input type='number' class="form-control input-sm text-center" id='productPriceAddPro' name='productPriceApn[]' value='' placeholder='Enter product price' min="1"/>
                  </td>
                  <td class="">
                    <input type='number' class="form-control input-sm text-center" id='totalAmountAddPro' name='totalAmountApn[]' value='' placeholder='' min="1" readonly="readonly" />
                  </td>
                  <td><input style='text-align:center;border-radius:0;width:80px;' id='addProduct' class='btn btn-primary btn-xs'
                    type='button' name='productAddButton' value='Add Product'/>
                  </td>
                </tr>
                <tr class="">
                  <td>
                    <p class="hidden" id="productIdError" style="color: red;">Product Field Is Requrired</p>
                    <p class="hidden" id="productIde" style="color: red;">Product Name and Quantity is required</p>
                  </td>
                  <td>
                    <p class="hidden" id="qnttyError" style="color: red;">Product Qty Is Required</p>
                    <p class="hidden" id="productQuantitye" style="color: red;"></p>
                  </td>
                  <td class=""></td>
                  <td class=""></td>
                  <td class=""></td>

                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td style="text-align:right;"><strong>Total Quantity</strong></td>
                  <td style="text-align:center;" id='totalQuantityFooter'></td>
                  <td style="text-align:center;" id='productPriceShow' class="hidden"><strong>Total Amount</strong></td>
                  <td style="text-align:right;"><strong>Total Amount</strong></td>
                  <td style="text-align:center;" id='proTotalAmountShowFooter' class=""></td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
          <div class="col-md-0"></div>
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
        <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
      </div>
    </div>

  </div>
  <div class="col-md-1"></div>
</div>



@include('inventory/transaction/purchase/purchaseDetails')
@include('dataTableScript')
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>

<script type="text/javascript">
  $( document ).ready(function() {


    $(".numeric").keypress(function (e) {
            //this.value = this.value.replace(/[^0-9\.]/g,'');
            $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
              $("#numericError").html("Digits Only").show().fadeOut("slow");
              event.preventDefault(); 
            }
          });

    $( "#purchaseDate" ).datepicker({
      dateFormat: "yy-mm-dd",  
      changeMonth: true,
      changeYear: true,
      maxDate: "0"
    });
//$("#purchaseDate").datepicker().datepicker("setDate", new Date());

$( "#orderDate" ).datepicker({
  dateFormat: "yy-mm-dd",  
  changeMonth: true,
  changeYear: true,
  maxDate: "0"
});
//$("#orderDate").datepicker().datepicker("setDate", new Date());

$( "#chalanDate" ).datepicker({
  dateFormat: "yy-mm-dd",  
  changeMonth: true,
  changeYear: true,
  maxDate: "0"
});
//$("#chalanDate").datepicker().datepicker("setDate", new Date());

function num(argument){
  return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}



var i=0;
$('#addProduct').click(function(){
  var testx = '';

  var productId       = $('#productId').val();
  var productName     = $('#productId option:selected').text();
    var productQntty    = parseFloat($('#productQntty').val()); //alert(productQntty);
    var csrf = "<?php echo csrf_token(); ?>";
    i++;
    if(productId == ''){$('#productIdError').removeClass('hidden'); return false;}
    else if(isNaN(productQntty) || productQntty==''){$('#qnttyError').removeClass('hidden'); return false;} 

    var getProductId                   = $("#productId").val();
    var getProductQty                  = $("#productQntty").val();
    var productPriceForTotalPrice      = $('#productPriceAddPro').val();
    var toShowTotalPriceInApnTable     = $("#totalAmountAddPro").val();

    $('#addProductTable tr.forhide').each(function() {
                var cellText = $(this).closest('tr').find('.productIdclass').val(); //alert(cellText);
                if(cellText==getProductId){
                  var getApnProductQty = $(this).closest('tr').find('.apnQnt').val();
                  var totalQtyforsamePro = parseFloat(getApnProductQty)+parseFloat(getProductQty); 
                  $(this).closest('tr').find('.apnQnt').val(totalQtyforsamePro);

                  var getApnProductAmount = $(this).closest('tr').find('.productPriceApnTable').val();
                  var totalAmountforsamePro = parseFloat(getApnProductAmount)+parseFloat(toShowTotalPriceInApnTable); 
                  $(this).closest('tr').find('.productPriceApnTable').val(totalAmountforsamePro);
                  testx = 'yes';
                    //alert(testx);
                  }
                });

    if(testx!=='yes'){
     $('#addProductTable').append('<tr id="row'+i+'" class="forhide"><td><input type="text" name="productName[]" class="form-control name_list input-sm" style="text-align:left; cursor:default" value="'+productName+'" readonly/></td><td><input type="number" name="productQntty5[]" class="form-control name_list input-sm apnQnt" id="apnQnt" style="text-align:center; cursor:default"  value="'+productQntty+'" readonly/></td><td><input type="number" name="productTotalPriceApnTable[]" class="form-control name_list input-sm productTotalPriceApnTable" id="productTotalPriceApnTable" style="text-align:center; cursor:default" value="'+productPriceForTotalPrice+'" readonly/></td><td><input type="number" name="productPriceApnTable[]" class="form-control name_list input-sm productPriceApnTable" id="productPriceApnTable" style="text-align:center; cursor:default"  value="'+toShowTotalPriceInApnTable+'" readonly/></td><td><input type="text" name="productId5[]" class="form-control input-sm name_list hidden productIdclass" style="text-align:center; cursor:default" value="'+productId+'" id="productId5"/><a href="javascript:;" name="remove" id="'+i+'" class="btn_remove" style="width:80px"><i class="glyphicon glyphicon-trash" style="color:red; font-size:16px"></i></a></td></tr>');
   }
   $('#productQntty').val('');
   $('#productId').val('');
   $('#productPriceAddPro').val('');
   $('#totalAmountAddPro').val('');
   $('#productGroupId').val('');
   $('#productCategoryId').val(''); 
   $('#productSubCategoryId').val('');

// onclick add button total amount summation  
var sumTotal = 0;
$(".productPriceApnTable").each(function() {
  var value = $(this).text();
                /*if(!isNaN(value) && value.length != 0) {
                sum += parseFloat(value);
              }*/
              sumTotal += Number($(this).val());
              $('#totalAmount').val(Math.ceil(sumTotal));
              $('#amountAfterDiscount').val(Math.ceil(sumTotal));
              $('#proTotalAmountShowFooter').text(Math.ceil(sumTotal));
            });
// onclick add button quantity summation          
var sum = 0;
$(".apnQnt").each(function() {
  var value = $(this).text();
                /*if(!isNaN(value) && value.length != 0) {
                sum += parseFloat(value);
              }*/
              sum += Number($(this).val());
              $('#totalQuantity').val(sum);
              $('#totalQuantityFooter').text(sum);
            });
//dinamicaly set value to get alert or something            
$("#totalAmount").trigger('input');
$("#amountAfterDiscount").trigger('input');

}); 

// calculation of total price per row
$("#productQntty, #productPriceAddPro").on("input",function (e) {
            var productQntyForTotalPrice  = $('#productQntty').val(); //$(this).val();
            var productPriceForTotalPrice = $('#productPriceAddPro').val(); 
            if(productPriceForTotalPrice==''){productPriceForTotalPrice=0;}
            var toShowTotalPriceInApnTable = parseFloat(productQntyForTotalPrice*productPriceForTotalPrice).toFixed(5).replace(/\.0+$/,''); 
            $("#totalAmountAddPro").val(toShowTotalPriceInApnTable);

          });


//to remove append row row 
$(document).on('click', '.btn_remove', function(){ 
           var removeQntty          = parseFloat($(this).closest('tr').find('.apnQnt').val()); //alert(removeQntty);
           var removeAmount         = parseFloat($(this).closest('tr').find('.productPriceApnTable').val());  
           var toralQnttyFromInput  = parseFloat($('#totalQuantity').val()); //alert(toralQnttyFromInput);
           var toralAmountFromInput = parseFloat($('#totalAmount').val()); alert(toralAmountFromInput);
           var qntAfterRemove       = parseFloat(toralQnttyFromInput-removeQntty); //alert(qntAfterRemove);
           var amountAfterRemove    = parseFloat(toralAmountFromInput-removeAmount); //alert(amountAfterRemove);

       var totalVat                 = parseFloat($('#vatPercent').val()); //alert(totalVat);
       var amountAfterDiscountForGross  = parseFloat($("#amountAfterDiscount").val()); //alert(amountAfterDiscountForGross);
       var grossTotalForRemove      = parseFloat($("#grossTotal").val()); 

       $('#totalQuantity').val(qntAfterRemove);
       $('#totalQuantityFooter').text(qntAfterRemove);
       if(amountAfterRemove<0){
        $('#totalAmount').val(0);
        $('#proTotalAmountShowFooter').text('');
      }else{$('#totalAmount').val(Math.ceil(amountAfterRemove));
      $('#proTotalAmountShowFooter').text(Math.ceil(amountAfterRemove));
    }


           // Discount Calcualtion after remove row
                var percent = parseFloat($('#discountPercent').val()); //alert(percent);
                var tAmount = parseFloat($("#totalAmount").val()); //alert(tAmount);
                var result = parseFloat(Math.ceil(tAmount*percent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(result);
                var totalAfterDiscount = parseFloat(Math.round(tAmount-result)).toFixed(5).replace(/\.0+$/,''); //alert(result);
                //var result = parseFloat(parseInt($("#totalAmount").val(), 10) * percent)/ 100;
                if(isNaN(result)){ $('#discount').val(''); $('#amountAfterDiscount').val(tAmount);}
                else{$('#discount').val(result||''); $('#amountAfterDiscount').val(totalAfterDiscount||'');}

            //VAT calculation after remove row
            var vatPercent            = parseFloat($('#vatPercent').val()); //alert(vatPercent);
            var amountForVat          = parseFloat($("#totalAmount").val()); //alert(amountForVat);
            var afterDiscountForVat   = parseFloat($("#amountAfterDiscount").val()); //alert(afterDiscountForVat);
            if(afterDiscountForVat==0){
                        var varBeforeDisc = parseFloat(Math.round(amountForVat*vatPercent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(varBeforeDisc);
                        if(isNaN(varBeforeDisc)){$('#vat').val('');}
                        else{$('#vat').val(varBeforeDisc||'');}   
                      }
                      else if(afterDiscountForVat==0 && amountForVat){
                        var varBeforeDisc = parseFloat(Math.round(afterDiscountForVat*vatPercent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(varBeforeDisc);
                        if(isNaN(varBeforeDisc)){$('#vat').val('');}
                        else{$('#vat').val(varBeforeDisc||'');} 
                      }

                      else if(afterDiscountForVat && amountForVat){
                        var varBeforeDisc = parseFloat(Math.round(afterDiscountForVat*vatPercent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(varBeforeDisc);
                        if(isNaN(varBeforeDisc)){$('#vat').val('');}
                        else{$('#vat').val(varBeforeDisc||'');} 
                      } 
                      else if(amountForVat==0){$('#vat').val('');} 

            //Gross total calculation after remove
            if(isNaN(totalVat) && amountAfterDiscountForGross==0){ alert('1');
            var grossTotalAfterRemove = parseFloat(toralAmountFromInput-removeAmount);
            $('#grossTotal').val(grossTotalAfterRemove||'');
              //$('#payAmount').val(grossTotalAfterRemove||'');
            }
            else if(amountAfterDiscountForGross>0 && percent && isNaN(totalVat)){  //alert('2');
              var grossTotalAfterRemove = parseFloat(Math.round(removeAmount*percent) / 100); //alert(toralQnttyFromInput);
              var totalRemoveAmount     = parseFloat(removeAmount-grossTotalAfterRemove); //alert(totalRemoveAmount);
              var grossTotalAfterRemoveShow = parseFloat(Math.round(grossTotalForRemove-totalRemoveAmount));

              var payAmountFieldValue = $('#payAmount').val();
              var dueAmount = parseFloat(Math.round(grossTotalAfterRemoveShow-payAmountFieldValue)); 
              if(grossTotalAfterRemoveShow==''){$('#grossTotal').val(0); $('#due').val(dueAmount);}
              else{$('#grossTotal').val(grossTotalAfterRemoveShow||''); $('#due').val(dueAmount||'');}
              /*$('#grossTotal').val(grossTotalAfterRemoveShow||'');*/
            }
            else if(isNaN(percent) && isNaN(totalVat)){ //alert('3');

              var payAmountFieldValue = $('#payAmount').val(); //alert(payAmountFieldValue);
              var removeFromGrosstoral = parseFloat(Math.round(grossTotalForRemove-removeAmount));  
              var dueAmount = parseFloat(Math.round(removeFromGrosstoral-payAmountFieldValue)); 
              if(grossTotalAfterRemoveShow <0){$('#grossTotal').val(0); $('#due').val(dueAmount);}
              else{$('#grossTotal').val(removeFromGrosstoral||'');  $('#due').val(dueAmount);}
              
            }
            else if(amountAfterDiscountForGross && totalVat){
              var grossTotalAfterRemove = parseFloat(Math.round(removeAmount*percent) / 100); 
              if(isNaN(grossTotalAfterRemove)){grossTotalAfterRemove=0}
              var totalRemoveAmount     = parseFloat(removeAmount-grossTotalAfterRemove); //alert(totalRemoveAmount);
              var vatAfterRemove        = parseFloat(Math.round(totalRemoveAmount*vatPercent) / 100); //alert(grossTotalAftervat);
              var amountToRemove      = parseFloat(Math.ceil(totalRemoveAmount+vatAfterRemove)); //alert(amountToRemove);
              var grossTotalAfterRemoveShow = parseFloat(Math.round(grossTotalForRemove-amountToRemove));

              var payAmountFieldValue = $('#payAmount').val();
              var dueAmount = parseFloat(Math.round(grossTotalAfterRemoveShow-payAmountFieldValue)); 
              if(grossTotalAfterRemoveShow=='' || grossTotalAfterRemoveShow<0){$('#grossTotal').val(0);/* $('#payAmount').val(0);*/ $('#due').val(dueAmount); }
              else{$('#grossTotal').val(grossTotalAfterRemoveShow||''); /*$('#payAmount').val(grossTotalAfterRemoveShow);*/ $('#due').val(dueAmount);}
            }

            //$('#due').val(0);

            var button_id = $(this).attr("id");   
            $('#row'+button_id+'').remove();  
      }); //end remove button click

// Discount Calcualtion
$("#discountPercent, #totalAmount").on("input",function(e) { // input on change
    var percent = parseFloat($('#discountPercent').val()); //alert(percent);
    var tAmount = parseFloat($("#totalAmount").val()); //alert(tAmount);
    
    var result = parseFloat(Math.ceil(tAmount*percent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(result);
    var totalAfterDiscount = parseFloat(Math.round(tAmount-result)).toFixed(5).replace(/\.0+$/,''); //alert(result);

    if(isNaN(result)){ $('#discount').val(''); $('#amountAfterDiscount').val(tAmount||'');}
    else{$('#discount').val(result||''); $('#amountAfterDiscount').val(totalAfterDiscount||''); }
    //$('#due').val(0); 
  })

//Discount percentage calculation
$("#discount").on("input",function(e) {
  var discountPer = parseFloat($("#discountPercent").val()); //alert(discountPer);
  var tAmount = parseFloat($("#totalAmount").val()); //alert(tAmount);
  var discountAmount = parseFloat($("#discount").val()); //alert(discountAmount);
  var discountRate = parseFloat(Math.round(100*discountAmount ) /tAmount);

    var totalAfterDiscount = parseFloat(Math.round(tAmount-discountAmount)).toFixed(5).replace(/\.0+$/,''); //alert(totalAfterDiscount);
    if(isNaN(totalAfterDiscount) || totalAfterDiscount==''){$('#amountAfterDiscount').val(tAmount||''); $('#discountPercent').val('');}
    else{ 
      $('#discountPercent').val(discountRate);
      $('#amountAfterDiscount').val(totalAfterDiscount); 
    } 
  })

// Calculation VAT Rate
$("#vatPercent, #totalAmount, #amountAfterDiscount, #discountPercent, #discount").on("input",function(e) { // input on change
    var vatPercent            = parseFloat($('#vatPercent').val()); //alert(vatPercent);
    var amountForVat          = parseFloat($("#totalAmount").val()); //alert(amountForVat);
    var afterDiscountForVat   = parseFloat($("#amountAfterDiscount").val()); //alert(afterDiscountForVat);
    if(isNaN(afterDiscountForVat) || afterDiscountForVat==0){
                var varBeforeDisc = parseFloat(Math.round(amountForVat*vatPercent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(varBeforeDisc);
                if(isNaN(varBeforeDisc)){$('#vat').val('');}
                else{$('#vat').val(varBeforeDisc||'');}   
              }
              else if(isNaN(afterDiscountForVat) && amountForVat){
                var varBeforeDisc = parseFloat(Math.round(afterDiscountForVat*vatPercent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(varBeforeDisc);
                if(isNaN(varBeforeDisc)){$('#vat').val('');}
                else{$('#vat').val(varBeforeDisc||'');} 
              }

              else if(afterDiscountForVat && amountForVat){
                var varBeforeDisc = parseFloat(Math.round(afterDiscountForVat*vatPercent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(varBeforeDisc);
                if(isNaN(varBeforeDisc)){$('#vat').val('');}
                else{$('#vat').val(varBeforeDisc||'');} 
              }
              $('#due').val(0); 
            })

//Gross total calculation
$("#vatPercent, #totalAmount, #amountAfterDiscount, #discountPercent, #discount").on("input",function(e) { // input on change
    var totalVat                    = parseFloat($('#vat').val()); //alert(totalVat);
    var amountForGrosstotal         = parseFloat($("#totalAmount").val()); //alert(amountForGrosstotal);
    var amountAfterDiscountForGross = parseFloat($("#amountAfterDiscount").val()); //alert(amountAfterDiscountForGross);

    var valueOfPayAmountField       = parseFloat($('#payAmount').val()); //alert(valueOfPayAmountField);
    var grossTotalForDue            = parseFloat($('#grossTotal').val()); //alert(grossTotalForDue);

      if(isNaN(totalVat) && isNaN(amountAfterDiscountForGross)){  //alert('1');
      $('#grossTotal').val(amountForGrosstotal||'');

      $('#due').val(dueAfterPayAmount||'');
    }

      else if(amountAfterDiscountForGross && isNaN(totalVat)){ //alert('3');
      var dueAfterPayAmount = parseFloat(amountAfterDiscountForGross-valueOfPayAmountField);
      $('#grossTotal').val(amountAfterDiscountForGross||'');
      $('#due').val(dueAfterPayAmount);  
    }
      else if(isNaN(amountAfterDiscountForGross) && totalVat){ //alert('4');
      var dueAfterPayAmount = parseFloat(grossTotalAmount-valueOfPayAmountField);
      $('#grossTotal').val(grossTotalAmount||''); 
      $('#due').val(dueAfterPayAmount);
    }
      else if(amountAfterDiscountForGross && totalVat){ //alert('5');
      var grossTotalAmount = parseFloat(Math.ceil(amountAfterDiscountForGross+totalVat));
      var dueAfterPayAmount = parseFloat(grossTotalAmount-valueOfPayAmountField);

        $('#grossTotal').val(grossTotalAmount); //alert(dueAfterPayAmount);
        
        $('#due').val(dueAfterPayAmount||'');
      } 
      else if(amountAfterDiscountForGross<1 && isNaN(totalVat)){
       $('#grossTotal').val(amountAfterDiscountForGross||'');

     }
     else if(amountAfterDiscountForGross<1 && totalVat){
       $('#grossTotal').val(amountAfterDiscountForGross||'');
       $('#payAmount').val(amountAfterDiscountForGross||'');
       $('#vat').val('');
     }
      //$('#due').val(0);    
    })

//Pay amount/due amount calculation
$("#payAmount, #due").on("input",function(e) { 
  var grossTotalForDue = parseFloat($("#grossTotal").val());
  var payAmount = parseFloat($("#payAmount").val());
  if(isNaN(payAmount)){$('#due').val(grossTotalForDue);}
  else if(isNaN(grossTotalForDue)){
    grossTotalForDue = 0;
  }else{
    var dueAmount = grossTotalForDue-payAmount;
    if(payAmount>grossTotalForDue){
      $("#payAmounte").html("Pay Amount Should not be more than gross total").show().fadeOut("slow");
      $("#payAmounte").css("color", "red");
      event.preventDefault();
    }
    else if(isNaN(dueAmount)){$('#due').val(0);}
    else{$('#due').val(dueAmount);}
  }
})    


$(document).on('click', '.edit-modal', function() {
  if(hasAccess('editInvPurchaseItem')){
    $('#addProductTable tbody .forEmpty').remove();
  //var string = '';
  var supplierIdFfetchRow = $(this).data('supplierid');
        //var supplierIdFfetchRow = $(this).data('projectid');
        $.ajax({
          type: 'POST',
          url: './selPurOptForEditProductRow',
          data: {'_token': '{{csrf_token()}}', 'supplierId':supplierIdFfetchRow},
          dataType: 'json',   
          success: function(data){
                   // alert(JSON.stringify(data));
                   $("#productId").empty();
                   $("#productId").prepend('<option selected="selected" value="">Please Select</option>');
                   $.each(data, function (key, value) {
                    $('#productId').append("<option value='"+ key+"'>"+value+"</option>"); 
                  });
                 },
                 error: function(data){
                  alert("error");
                }
              });/*End Ajax*/

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
        $('.modal-dialog').css('width','80%');
        $('.deleteContent').hide();
        $('.form-horizontal').show();
        $('#id').val($(this).data('id'));
        $('#slno').val($(this).data('slno'));
        $('#billNo').val($(this).data('billno'));
        $('#orderNo').val($(this).data('orderno'));
        $('#orderDate').val($(this).data('orderdate'));
        $('#chalanNo').val($(this).data('chalanno'));
        $('#chalanDate').val($(this).data('chalandate'));
        $('#projectId').val($(this).data('projectid'));
        $('#projectTypeId').val($(this).data('projecttypeid'));
        $('#contactPerson').val($(this).data('contactperson'));
        $('#supplierId').val($(this).data('supplierid'));
        $('#remark').val($(this).data('remark'));
        $('#purchaseDate').val($(this).data('purchasedate'));
        $('#totalQuantity').val($(this).data('totalquantity'));
        $('#totalAmount').val($(this).data('totalamount')); 
        $('#discount').val($(this).data('discount'));
        $('#amountAfterDiscount').val($(this).data('amountafterdiscount'));
        $('#vat').val($(this).data('vat'));
        $('#grossTotal').val($(this).data('grosstotal'));
        $('#payAmount').val($(this).data('payamount'));
        $('#due').val($(this).data('due'));
        $('#paymentStatus').val($(this).data('paymentstatus'));
        $('#totalQuantityFooter').text($(this).data('totalquantity'));
        $('#proTotalAmountShowFooter').text($(this).data('totalamount'));
        $('#createdDate').val($(this).data('createddate'));
        $('#footer_action_button2').hide();
        $('#footer_action_button').show();
        $('.actionBtn').removeClass('delete');
        $('#myModal').modal('show');


        if($(this).data('discount')){
          var discountAmount      = $(this).data('discount');
          var totalAmount         = $(this).data('totalamount');
          var vatAmount           = $(this).data('vat');
          var tAmountafterDisc    = $(this).data('amountafterdiscount');
          var calculationDisPer   = parseFloat(Math.round(100*discountAmount) / totalAmount);
          $('#discountPercent').val(calculationDisPer);
          if(vatAmount){
            var calculationVatPer   = parseFloat(Math.round(100*vatAmount) / tAmountafterDisc);
            $('#vatPercent').val(calculationVatPer);
          }
        }
        if($(this).data('discount') == '' && $(this).data('vat')){
          var totalAmount     = $(this).data('totalamount');
          var vatAmount       = $(this).data('vat');
          var calculationVatPer   = parseFloat(Math.round(100*vatAmount) / totalAmount);
      $('#vatPercent').val(calculationVatPer); //alert(calculationVatPer);
    }

    var purIdForCountRow = $(this).data('id');
    var csrf = "<?php echo csrf_token(); ?>"; 
    $.ajax({
     type: 'post',
     url: './purchaseEditAppendRows',
     data: {
      '_token': csrf,
      'id': purIdForCountRow
    },
    dataType: 'json',
    success: function( data ){
            //alert(JSON.stringify(data));
            $.each(data, function (key, value) {
              if (key == "purchaseDetailsTables") {
                var i = 0;
                $.each(value, function (key1, value1) {
          //alert(value1.totalQuantity);
          var string="<tr id='row"+i+"' class='forEmpty forhide'>";
          string+="<td class='' style='text-align: left;'><select name='productId5[]' class='apendSelectOption productIdclass form-control input-sm' id='productId5"+i+"'><option>select Product</option></select></td>";
          string+="<td><input type='number' name='productQntty5[]'' class='form-control name_list input-sm apnQnt' id='apnQnt' style='text-align:center;' value='"+value1.quantity+"' autocomplete='off'/></td>";
          string+="<td><input type='number' name='productTotalPriceApnTable[]'' class='form-control name_list input-sm productTotalPriceApnTable' id='productTotalPriceApnTable' style='text-align:center;' value='"+value1.price+"'/></td>";
          string+="<td><input type='number' name='productPriceApnTable[]' class='form-control name_list input-sm productPriceApnTable' id='productPriceApnTable' style='text-align:center;'  value='"+value1.totalPrice+"' readonly/></td>";
          string+="<td><input type='text' name='purchaseDetailsTablesId[]' class='form-control input-sm name_list hidden deleteIdSend' style='text-align:center; cursor:default' value='"+value1.id+"' /><a href='javascript:;' name='remove' id='"+i+"' class='btn_remove' style='width:80px'><i class='glyphicon glyphicon-trash' style='color:red; font-size:16px'></i></a></td></td>";
          string+="</tr>";
          $('#addProductTable').append(string);
            //$('#forOptionSelected'+increage).val(value1.productId);
            ++i;
          })
              }

              if (key == "productId") {
                $.each(value, function (key1, value1) {
          //alert(value1.id);
          $('.apendSelectOption').append("<option value='"+ value1.id+"'>"+value1.name+"</option>");

        })
              } 
            });

            var productDetailsRowLength = data.purchaseDetailsTables.length;
      //alert(thakur);
      for(var i=0; i<productDetailsRowLength; i++){
        $('#productId5'+i).val(data['purchaseDetailsTables'][i].productId);
      }
    },
    error: function( data ){
      alert('error');
    }
  }); 
  }
});

$("#addProductTable").hover(function(){
  $(".apnQnt,.productTotalPriceApnTable").on('input',function(){
    var getApnProductQty  = $(this).closest('tr').find('.apnQnt').val();
    var getApnProPerpri   = $(this).closest('tr').find('.productTotalPriceApnTable').val();
    var totalAmountForApnR  = parseFloat(Math.ceil(getApnProductQty*getApnProPerpri));
    $(this).closest('tr').find('.productPriceApnTable').val(totalAmountForApnR);

    var sum = 0;
    var total = 0;
    $('.apnQnt').each(function() {
      sum += Number($(this).val());
      $('#totalQuantity').val(sum);
      $('#totalQuantityFooter').text(sum);
    });

    $('.productPriceApnTable').each(function() {
      total += Number($(this).val());
      $('#totalAmount').val(total);
      $('#proTotalAmountShowFooter').text(total);
    });

    $("#totalAmount").trigger('input');
    $("#grossTotal").trigger('input');

  });
});


// Edit Data (Modal and function edit data)
$('.modal-footer').on('click', '.edit', function() {
  $('#supplierId').removeAttr('disabled');
  $.ajax({
   type: 'post',
   url: './editInvPurchaseItem',
   data: $('form').serialize(),
   dataType: 'json',
   success: function( data ){
        //alert(JSON.stringify(data));
        if(data=='false'){$('#productIde').show(); $('#productIde').removeClass('hidden'); return false;}
        if (data.errors) {
          if (data.errors['supplierId']) {
            $('#supplierIde').empty();
            $('#supplierIde').append('<span class="errormsg" style="color:red;">'+data.errors.supplierId+'</span>');
          }
        }
        else{ 
          $('#MSGE').addClass("hidden"); 
          $('#MSGS').text('Data successfully inserted!');
          $('#myModal').modal('hide');

          $('.item' + data["updateDatas"][0].id).replaceWith(
            "<tr class='item" + data["updateDatas"][0].id + "'><td  class='text-center slNo'>" + data.slno +
            "</td><td class='hidden'>" + data["updateDatas"][0].id + 
            "</td><td>" + data.dateFromarte + 
            "</td><td style='text-align: left; padding-left: 5px;'>" + data["updateDatas"][0].billNo + 
            "</td><td style='text-align: left; padding-left: 5px;'>" + data["updateDatas"][0].orderNo + 
            "</td><td style='text-align: left; padding-left: 5px;'>" + data["updateDatas"][0].orderDate +
            "</td><td style='text-align: left; padding-left: 5px;'>" + data["updateDatas"][0].chalanNo +data.chalanDate+
            "</td><td style='text-align: left; padding-left: 5px;'>" + data["updateDatas"][0].chalanDate +
            "</td><td style='text-align: left; padding-left: 5px;'>" + data["projectName"].projectId +
            "</td><td style='text-align: left; padding-left: 5px;'>" + data["projectTypeName"].projectTypeId +
            "</td><td style='text-align: left; padding-left: 5px;'>" + data["supplierName"].supplierCompanyName +
            "</td><td style='text-align: left; padding-left: 5px;'>" + data["branchName"].name +
            "</td><td style='text-align: left; padding-left: 5px;'>" + data["updateDatas"][0].createdBy + 
            "</td><td style='text-align: right; padding-right: 5px;'>" + data["updateDatas"][0].totalQuantity +
            "</td><td style='text-align: right; padding-right: 5px;'>" + data["updateDatas"][0].grossTotal + 
            "</td><td class='text-center'><a href='javascript:;' class='forPurchaseDetailsModel' data-id='" + data["updateDatas"][0].id + "'><span class='fa fa-eye'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='edit-modal' data-id='" + data["updateDatas"][0].id + "' data-billNo='"+ data["updateDatas"][0].billNo + "' data-orderno='"+ data["updateDatas"][0].orderNo + "' data-supplierid='" + data["updateDatas"][0].supplierId + "' data-contactperson='" + data["updateDatas"][0].contactPerson + "' data-discount='" + data["updateDatas"][0].discount + "' data-purchasedate='" + data["updateDatas"][0].purchaseDate + "' data-totalquantity='" + data["updateDatas"][0].totalQuantity + "' data-totalamount='" + data["updateDatas"][0].totalAmount+ "' data-amountafterdiscount='" + data["updateDatas"][0].amountAfterDiscount + "' data-vat='" + data["updateDatas"][0].vat + "' data-grosstotal='" + data["updateDatas"][0].grossTotal + "' data-payamount='" + data["updateDatas"][0].payAmount + "' data-due='" + data["updateDatas"][0].due + "' data-paymentstatus='" + data["updateDatas"][0].paymentStatus + "' data-remark='" + data["updateDatas"][0].remark + "' data-createddate='" + data["updateDatas"][0].createdDate + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["updateDatas"][0].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
          $('.succsMsg').removeClass('hidden');
          $('.succsMsg').show();
          setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
        }
      },
      error: function( data ){
            // Handle error
            //alert('data.errors');
            
          }
        });
});

//delete function
$(document).on('click', '.delete-modal', function() {
  if(hasAccess('deletInvPurchaseItem')){
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
}
});

$('.modal-footer').on('click', '.delete', function() {

  $.ajax({
    type: 'post',
    url: './deletInvPurchaseItem',
    data: {
      '_token': $('input[name=_token]').val(),
      'id': $('.id').text()
    },
    success: function(data) {
    //alert(JSON.stringify(data));
    $('.item' + $('.id').text()).remove();
  },
  error: function( data ){
    alert('hi');
  }
});
});

//purchase detaisl
$(document).on('click', '.forPurchaseDetailsModel', function() {
  if(hasAccess('invPurchaseDetails')){
   $('.modal-header').css({"background-color":"white"});
   $('#swhoAppendRows tbody').empty();
   $('#purchDetailsModel').modal('show');
   $('.modal-dialog').css('width','50%');
   var id = ($(this).data('id'));
   var crsf = ($(this).data('token'));
    //alert(id);alert(crsf);
    $.ajax({
     type: 'post',
     url: './invPurchaseDetails',
     data: {
      '_token': $('input[name=_token]').val(),
      'id': id
    },
    dataType: 'json',
    success: function( data ){

      $('#purDateHead').text(data.createdDate);    
      $('#billNoHead').text(data['prchDetails'][0].billNo);
      $('#billDateHead').text(data.createdDate);
      $('#orderNoHead').text(data['prchDetails'][0].orderNo);
      $('#orderDateHead').text(data.orderDate);
      $('#chalanNoHead').text(data['prchDetails'][0].chalanNo);
      $('#chalanDateHead').text(data.chalanDate);
      $('#MobileHead').text(data['supplierMobile'].phone);
      $('#projectHead').text(data['projectName'].name);
      $('#projectTypeHead').text(data['projectTypeName']);
      $('#purBranchHead').text(data['branchName'].name);
      $('#supplierHead').text(data['supplierName'].supplierCompanyName);
      $('#showPurbillNo').text(data['prchDetails'][0].billNo);
      $('#SupplierAddressHead').text(data['supplierAddress'].address);

      if(data['projectTypeName']==null){
        $('#project').hide();
        $('#projectTypeHead').hide();
        $('#projectView').hide();
      }else{
        $('#project').show();
        $('#projectTypeHead').show();
        $('#projectView').show();

      }
      $(document).on('click', '#print', function(){ 

       $("#purchasePrint").show();
     });
      /* Start Print */
      $('#showbilldate').text(data.createdDate);
      $('#showbillNo').text(data['prchDetails'][0].billNo);
      $('#showOrderNo').text(data['prchDetails'][0].orderNo);
      $('#showOrderDate').text(data.orderDate);
      $('#showProject').text(data['projectName'].name);
      $('#showLocation').text(data['branchName'].name);
      $('#showSupplier').text(data['supplierName'].supplierCompanyName);
      $('#showSupplierAddress').text(data['supplierAddress'].address);
      $('#showMobile').text(data['supplierMobile'].phone);
      $('#showChalanName').text(data['prchDetails'][0].chalanNo);
      $('#showChalanDate').text(data.chalanDate);
      $('#createPerson').text(data['employeeName'].emp_name_english);
      $('#createPersonId').text(data['employeeName'].emp_id);
      $('#createPersonDeg').text(data['employeeDege']);


      /* Start Print */

      $.each(data, function (key, value) {
        if (key == "purDetailsTables") {

          //alert(JSON.stringify(data));
          var i = 1;
          $.each(value, function (key1, value1) {

            var string="<tr>";
            string+="<td style='text-align:center' class='slNoUse'>"+i+"</td>";
            string+="<td class='productName' style='text-align:left;' id='productRowName"+i+"'>"+data.productName[key1]+"</td>";
            string+="<td class='productPricePerPc'>"+value1.quantity+"</td>";
            string+="<td class='productPricePerPc'>"+value1.price+"</td>";
            string+="<td class='productPricePerPc'>"+value1.totalPrice+"</td>";
            string+="</tr>";
            $('#swhoAppendRows').append(string);

            i++;

          })
        }



        if (key == "purDetailsTables") {
          var j = 1;
          $.each(value, function (key2, value2) {
          //alert(value2.quantity);

          var strin="<tr>";
          strin+="<td style='text-align:center' class='slNoUse'>"+j+"</td>";
          strin+="<td class='productName' style='text-align:left; font-size:14px;'  id='product"+j+"'>"+data.productName[key2]+"</td>";
          strin+="<td class='productPricePerPc'style='font-size:14px;'>"+value2.quantity+"</td>";
          strin+="<td class='productPricePerPc'style='font-size:14px; text-align:right; padding-right:5px;'>"+num(value2.price)+"</td>";
          strin+="<td class='productPricePerPc'style='font-size:14px; text-align:right; padding-right:5px;'>"+num(value2.totalPrice)+"</td>";
          strin+="</tr>";
          $('#purchasePrint').append(strin);

          j++;

        })
        } 
      });

      $('#totalQtyDetails').val(data['prchDetails'][0].totalQuantity);
      $('#totalAmountDetails').val(data['prchDetails'][0].totalAmount);
      $('#totalQtyPrint').html(data['prchDetails'][0].totalQuantity);
      $("#totalAmountPrint").html(num(data['prchDetails'][0].totalAmount));

    },
    error: function( data ){

    }
  });
}
});


$("#purchaseForm").mouseover(function(){
  var rows= $('#addProductTable tbody tr.forhide').length;
  if(rows>0){

    $("#supplierId").prop('disabled', true);
  }else{$("#supplierId").prop('disabled', false);}
}); 
});
</script>



{{-- Filtering --}}
<script type="text/javascript">
  $(document).ready(function(){

    $("#supplierId").change(function(){ 
             var supplierId = $('#supplierId').val(); //alert(supplierId);
             var csrf = "<?php echo csrf_token(); ?>";

             $.ajax({
              type: 'post',
              url: './invPurOnCngSupl',
              data: {supplierId:supplierId,_token: csrf},
              dataType: 'json',   
              success: function( _response ){
                    //alert(JSON.stringify(_response));
                    
                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Please Select</option>');

                    $.each(_response, function (key, value) {

                      if (key == "productName") {
                        $.each(value, function (key1, value1) {
                          $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                        })
                      }  

                      if (key == "groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })
                        })         
                      }

                      if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })    
                        })
                      }

                      if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })
                        })    
                      }

                      if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productBrandId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })
                        })    
                      }

                      if (key == "contactPerson") {
                        $.each(value, function (key1, value1) {
                                //alert(value1);
                                $('#contactPerson').val(value1);
                              })    
                      }

                    });

                  },
                  error: function(_response){
                    alert("error");
                  }

                });/*End Ajax*/

           }); 

    $("#productGroupId").change(function(){ 
     var productGroupId = $('#productGroupId').val();
     var supplierId     = $('#supplierId').val();
     var csrf = "<?php echo csrf_token(); ?>";

     $.ajax({
      type: 'post',
      url: './invPurchaseOnCngGrp',
      data: {
        productGroupId:productGroupId,
        _token: csrf,
        supplierId:supplierId
      },
      dataType: 'json',   
      success: function( _response ){
                    //alert(JSON.stringify(_response));
                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Please Select</option>');

                    $.each(_response, function (key, value) {

                      if (key == "productName") {
                        $.each(value, function (key1, value1) {
                          $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                        })
                      } 

                      if (key == "groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })
                        })         
                      } 

                      if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })    
                        })
                      }

                      if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })
                        })    
                      }

                      if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productBrandId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })
                        })    
                      }

                    });

                  },
                  error: function(_response){
                    alert("error");
                  }

              });//End Ajax

        }); //End Change Product Group

        //Change Category
        $("#productCategoryId").change(function(){ 
         var productGroupId     = $('#productGroupId').val();
             var productCategoryId  = $('#productCategoryId').val(); //alert(productCategoryId);
             var supplierId         = $('#supplierId').val();
             var csrf               = "<?php echo csrf_token(); ?>";

             $.ajax({
              type: 'post',
              url: './invPurchaseOnCngCtg',
              data: {
                productGroupId:productGroupId, 
                productCategoryId:productCategoryId,
                supplierId:supplierId,
                _token: csrf
              },
              dataType: 'json',   
              success: function( _response ){
                    //alert(JSON.stringify(_response));
                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Please Select</option>');

                    $.each(_response, function (key, value) {

                      if (key == "productName") {
                        $.each(value, function (key1, value1) {
                          $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                        })
                      } 

                      if (key == "groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })
                        })         
                      } 

                      if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })    
                        })
                      }

                      if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })
                        })    
                      }

                      if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productBrandId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })
                        })    
                      }

                    });

                  },
                  error: function(_response){
                    alert("error");
                  }

                });/*End Ajax*/

           }); /*End Change Category*/

        //Change sub Category
        $("#productSubCategoryId").change(function(){ 
         var productGroupId             = $('#productGroupId').val();
         var productCategoryId          = $('#productCategoryId').val(); 
         var supplierId                 = $('#supplierId').val();
         var productSubCategoryId       = $('#productSubCategoryId').val();
         var csrf                       = "<?php echo csrf_token(); ?>";

         $.ajax({
          type: 'post',
          url: './invPurchaseOnCngSubCtg',
          data: {
            productGroupId: productGroupId, 
            productCategoryId: productCategoryId,
            supplierId: supplierId,
            productSubCategoryId: productSubCategoryId,
            _token: csrf
          },
          dataType: 'json',   
          success: function( _response ){
                    //alert(JSON.stringify(_response));
                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Please Select</option>');

                    $.each(_response, function (key, value) {

                      if (key =="productName") {
                        $.each(value, function (key1, value1) {
                          $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                        })
                      } 

                      if (key =="groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })
                        })         
                      } 

                      if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })    
                        })
                      }

                      if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })
                        })    
                      }

                      if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                        $.each(value, function (key1, value1) {
                          $.each(value1, function (key2, value2) {
                            $('#productBrandId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                          })
                        })    
                      }

                    });

                  },
                  error: function(_response){
                    alert("error");
                  }

                });/*End Ajax*/

       }); /*End Change Category*/

      });
    </script>


    <script type="text/javascript">
      $(document).ready(function(){ 


        function pad (str, max) {
          str = str.toString();
          return str.length < max ? pad("0" + str, max) : str;
        }

        $("#projectId").change(function(){

         var project = $(this).val();
         var csrf = "<?php echo csrf_token(); ?>";

         $.ajax({
          type: 'post',
          url: './famsAddProductOnChangeProject',
          data: {projectId:project,_token: csrf},
          dataType: 'json',
          success: function( data ) {

            $("#projectTypeId").empty();
            $("#projectTypeId").prepend('<option selected="selected" value="">Select Project Type</option>');

            $.each(data['projectTypeList'], function (key, projectObj) {

             $('#projectTypeId').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");                      
           });
          },
          error: function(_response) {
            alert("error");
          }

        });/*End Ajax*/

       });/*End Change Project*/
      });
    </script>


    {{-- Print Page --}}
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        $("#print").click(function(event) {

          $("#hiddenTitle").show();
          $("#hiddenInfo").show();
          $("#purchasePrint").removeClass('table table-striped table-bordered');

          var mainContents = document.getElementById("printingContent").innerHTML;
          var headerContents = '';

          var printStyle = '<style>#purchasePrint{float:none !important;height:auto;padding:0px; margin-top:10px; margin-bottom:10px;width:100%;font-size:11px;border:1pt ash;page-break-inside:auto;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px} tbody tr td {text-align:center;vertical-align: middle;padding:3px;font-size:10px}  tbody tr{ page-break-inside:avoid; margin-bottom:10px; page-break-after:auto} </style><style media="print">@page{size:portrait;}</style><style>@media print { #tableDiv { -moz-overflow: visible !important;}}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

          printContents = '<div id="order-details-wrapper" style="-moz-overflow: visible !important;"> ' + printStyle + mainContents+'</div>';


          var win = window.open('','printwindow');
          win.document.write(printContents);
          win.print();
          location.reload();
          win.close();

        });
      });

    </script>
    {{-- EndPrint Page --}}


    <style>
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    input[type=number] {
      -moz-appearance:textfield;
    }
  </style>