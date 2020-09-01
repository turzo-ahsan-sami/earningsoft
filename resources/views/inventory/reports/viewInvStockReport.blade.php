@extends('layouts/inventory_layout')
@section('title', '| Stock Report')
@section('content')
@include('successMsg')
<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$gnrBranchId = Session::get('branchId');
$gnrBranchId;
$branchName = DB::table('gnr_branch')->where('id',$gnrBranchId)->value('name');

$totalopeningStock 		=0;
$totalPurchaseQty 		=0;
$totalStock 			=0; 
$totalPurchaseRetQty  	=0;
$totalIssueQty 			=0;  
$totalIssueReturnQty 	=0;
$totalUseQty 			=0;
$totaluseReturnQty  	=0;



$startDate                  = '';
$endDate                    = '';
$searchBranchId             = '';
$supplierId                 = '';
$productGroupId             = '';
$productCategoryId          = '';
$productSubCategoryId       = '';
$productBrandId             = '';
$productName                = '';
$searchTypeCtrView          = 0;
$fiscalYearCtrView          = 0;


if (str_contains(Request::fullUrl(), 'filterStockReport')){
    if($searchTypeCtr == 1){
         $startDate = App\gnr\FiscalYear::where('id', $fiscalYearCtr)->value('fyStartDate');
         $endDate   = App\gnr\FiscalYear::where('id', $fiscalYearCtr)->value('fyEndDate');
    }else{
        $startDate              = $_GET['startDate'];
        $endDate                = $_GET['endDate'];
    }
$searchBranchId         = $_GET['branchId'];
$supplierId             = $_GET['supplierId'];
$productGroupId         = $_GET['productGroupId'];
$productCategoryId      = $_GET['productCategoryId'];
$productSubCategoryId   = $_GET['productSubCategoryId'];
$productBrandId         = $_GET['productBrandId'];
$productName            = $_GET['productName'];
$searchTypeCtrView      = $searchTypeCtr; /*<!--from controler -->>*/
$fiscalYearCtrView      = $fiscalYearCtr; /*<!--from controler -->>*/

}

?>
<div class="row">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading"  style="padding-bottom:0px">
          <div class="panel-options">
              <!-- <a href="{{url('addInvBrnRequiF/')}}" class="btn btn-info pull-left addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Requisition</a> -->
          </div>
          <h3 style="text-align: center; color: white">Stock Report</h3>
                    
        </div>
        <div class="panel-body panelBodyView"> 

        <!-- Filtering Start-->  
                      <div class="row">
                        <div class="col-md-11">

                          {!! Form::open(array('url' => 'filterStockReport/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filteringForm', 'method'=>'get')) !!}

                          <div class="col-md-1 hidden">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
                                    {!! Form::label('branchId', 'branchId:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                    <?php
                                      if($gnrBranchId==1 || $branchName=='Head Office'):
                                        $branchId = array('' => 'Select') + DB::table('gnr_branch') ->whereNotIn('id', [1])->pluck('name','id')->all();
                                        else:
                                        $branchId = DB::table('gnr_branch') ->where('id', $gnrBranchId)->pluck('name','id')->all();    
                                      endif;  
                                    ?>   
                                      {!! Form::select('branchId', ($branchId), null, array('class'=>'form-control input-sm', 'id' => 'branchId')) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
                            {!! Form::label('supplierId', 'Supplier:', ['class' => 'col-sm-12 control-label']) !!}
                                <div class="col-md-12">
                                  <?php 
                                  /*$supplierId = array('' => 'Please Select') + DB::table('gnr_supplier')->pluck('supplierCompanyName','id')->all();*/
                                      $supplierIds = DB::table('inv_product')->pluck('supplierId')->all();
                                      $supplierNames  = DB::table('gnr_supplier')->select('id','name')->get();
                                      $supplierNames = $supplierNames->whereIn('id', $supplierIds);
                                      $supplierNames->all();
                                  ?> 
                                  <select name="supplierId" id="supplierId" class="form-control input-sm">
                                    <option value="">Select</option>
                                    @foreach($supplierNames as $supplierName) 
                                      <option value="{{$supplierName->id}}">{{$supplierName->name}}</option>
                                    @endforeach 
                                  </select>  
                                  {{-- {!! Form::select('supplierId', ($supplierId), null, array('class'=>'form-control', 'id' => 'supplierId')) !!} --}}
                                </div>
                              </div> 
                            </div>

                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
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
                                            <option value="">Select</option>
                                                <?php if($groupArSize>0): ?>
                                                    @foreach($groupNames as $groupName)
                                                           <option value="{{$groupName->id}}">{{$groupName->name}}</option>
                                                    @endforeach
                                                <?php endif ; ?>
                                        </select>
                                    
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
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
                                        <option value="">Select</option>
                                            <?php if($catArSize>0): ?>
                                                @foreach($categoryNames as $categoryName)
                                                       <option value="{{$categoryName->id}}">{{$categoryName->name}}</option>
                                                @endforeach
                                            <?php endif ; ?>
                                    </select>
                                    
                                    </div>
                                </div>
                            </div>
            
                          <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:#212F3C; ">
                                    {!! Form::label('productSubCategoryId', 'Sub. Cat:', ['class' => 'control-label col-sm-12']) !!}
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
                                        <option value="">Select</option>
                                            <?php if($subCatArSize>0): ?>
                                                @foreach($subCategoryNames as $subCategoryName)
                                                       <option value="{{$subCategoryName->id}}">{{$subCategoryName->name}}</option>
                                                @endforeach
                                            <?php endif ; ?>
                                    </select>
                                    
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
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
                                        <option value="">Select</option>
                                            <?php if($bndArSize>0): ?>
                                                @foreach($brandNames as $brandName)
                                                       <option value="{{$brandName->id}}">{{$brandName->name}}</option>
                                                @endforeach
                                            <?php endif ; ?>
                                        </select>
                                        
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
                                    {!! Form::label('productName', 'Pro. Name:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                        {!! Form::text('productName', null, ['class' => 'form-control', 'id' => 'productName', 'type' => 'text','autocomplete'=>'off', 'placeholder'=>'name']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
                                    {!! Form::label('', 'Search By:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                        <?php 
                                            $searchType = array('' => 'Select')+DB::table('searching_type')->pluck('name','id')->all();
                                        ?>
                                        {!! Form::select('searchType',($searchType), null, array('class'=>'form-control input-sm', 'id' => 'searchType')) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1 hidden" id="fiscalYearDiv">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
                                    {!! Form::label('', 'Fiscal Y.', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                        <?php 
                                            $fisCalYearFt = array('' => 'Select') + DB::table('gnr_fiscal_year')->pluck('name','id')->all();
                                        ?>
                                        {!! Form::select('fiscalYear',($fisCalYearFt), DB::table('gnr_fiscal_year')->orderBy('name', 'desc')->pluck('id')->first(), array('class'=>'form-control input-sm', 'id' => 'fiscalYear')) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="hidden" id="dateRangeDiv">
                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
                                    {!! Form::label('startDate', '', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                        {!! Form::text('startDate', null, ['class' => 'form-control', 'id' => 'startDate', 'type' => 'text','autocomplete'=>'off', 'placeholder'=>'From']) !!}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
                                    {!! Form::label('endDate', '', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                        {!! Form::text('endDate', null, ['class' => 'form-control', 'id' => 'endDate', 'type' => 'text','autocomplete'=>'off', 'placeholder'=>'To']) !!}
                                    </div>
                                </div>
                            </div>

                            </div>

                            <div class="col-md-1">
                                <div class="form-group" style="">
                                    {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12" style="padding-top: 13%;">
                                        {!! Form::submit('search', ['id' => 'add', 'class' => 'btn btn-primary btn-xs']); !!}
                                    </div>
                                </div>
                            </div>

                        {!! Form::close()  !!}
                      </div> 

                      <div class="col-md-1">
                        <div class="form-group">
                          {!! Form::label('printList', '', ['class' => 'control-label col-sm-12 hidden']) !!}
                            <div class="col-sm-12" style="padding-top: 25%; color: black">
                              <button id="printList" style="background-color:transparent;border:none;float:left;">
                                <i class="fa fa-print fa-lg" aria-hidden="true"></i>
                              </button>
                            </div>
                        </div>
                      </div>     
                </div>             
                <!-- filtering end-->     
       {{--  <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#stockViewTable").dataTable({
                "oLanguage": {
                        "sEmptyTable": "No Records Available",
                        "sLengthMenu": "Show _MENU_"
                        }
                });
            });

        </script>
        </div> --}}
          <div id="printView">
            <div class="text-center">
                <span style="color: black; font-weight: bold">Ambala ERP</span><br/>
                <span style="color: black; font-weight: bold">Branch Name: <?php echo $branchName; ?> </span><br/>
                <span style="color: black; font-weight: bold">Date: <?php echo date("j F, Y"); ?></span><br/>
                <span style="color: black; font-weight: bold">Stock Report</span>
            </div>
          <table class="table table-striped table-bordered" id="stockViewTable">
            <thead>
              <tr>
                <th width="32" class="reportPgTh">SL#</th>
                <th class="reportPgTh">Supplier</th>
                <th class="reportPgTh">Group</th>
                <th class="reportPgTh">Catagory</th>
                <th class="reportPgTh">Sub Catagory</th>
                <th class="reportPgTh">Brand</th>
                <th class="reportPgTh">Model</th>
                <th class="reportPgTh">Product Name</th>
                <th class="reportPgTh">O.Stock</th>
                <th class="reportPgTh">Purchase</th>
                <th class="reportPgTh">Purchase Return</th>
                <th class="reportPgTh">Issue</th>
                <th class="reportPgTh">Issue Return</th>
               
                <th>Use</th>
                <th>Use Return</th>
                <th>Stock</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody>
                  <?php $no=0; ?>
                @foreach($InvProducts as $InvProduct)  
                    <tr>
                      <td class="text-center slNo">{{++$no}}</td>
                      <td style="text-align: left; padding-left: 5px">
                        <?php
                          $supplierName = DB::table('gnr_supplier')->where('id',$InvProduct->supplierId)->value('name');
                        ?>  
                          {{$supplierName}}
                      </td>
                      <td style="text-align: left; padding-left: 5px">
                        <?php
                          $groupName = DB::table('inv_product_group')->where('id',$InvProduct->groupId)->value('name');
                        ?>  
                          {{$groupName}}
                      </td>
                      <td style="text-align: left; padding-left: 5px">
                        <?php
                          $categoryName = DB::table('inv_product_category')->where('id',$InvProduct->categoryId)->value('name');
                        ?>  
                          {{$categoryName}}
                      </td>
                      <td style="text-align: left; padding-left: 5px">
                        <?php
                          $subCategoryName = DB::table('inv_product_sub_category')->where('id',$InvProduct->subCategoryId)->value('name');
                        ?>  
                          {{$subCategoryName}}
                      </td>
                      <td style="text-align: left; padding-left: 5px">
                        <?php
                          $brandName = DB::table('inv_product_brand')->where('id',$InvProduct->brandId)->value('name');
                        ?>  
                          {{$brandName}}
                      </td>
                      <td style="text-align: left; padding-left: 5px">
                        <?php
                          $modelName = DB::table('inv_product_model')->where('id',$InvProduct->modelId)->value('name');
                        ?>  
                          {{$modelName}}
                      </td>
                      <td style="text-align: left; padding-left: 5px">
                          {{$InvProduct->name}}
                      </td>
                      <td style="text-align: center; ">
                        <?php 
                          
                            $openingStock = $InvProduct->openingStock;
                          
     // ================================== Opening stock calculation =============================  
                        // Purchase Opening Stock                                  
                        if ($startDate){
                          $purchaseBrnachIdStock = DB::table('inv_purchase')->where('branchId', $gnrBranchId);  
                          $purchaseBrnachIdStock->where(DB::raw('DATE(createdDate)'), '<', $startDate);
                          $purchaseBrnachIdStock = $purchaseBrnachIdStock->pluck('id')->all();
                          $purchaseDetailsOpeningStock = DB::table('inv_purchase_details')->select('purchaseId','productId','quantity')->get(); 
                          $purchaseDateWiseStock = $purchaseDetailsOpeningStock->whereIn('purchaseId', $purchaseBrnachIdStock)
                                                                               ->whereIn('productId', $InvProduct->id)->sum('quantity');
                          $openingStock += $purchaseDateWiseStock;

                          // Pruchase Return opening stock
                          $purchRetBrnachIdStock = DB::table('inv_purchase_return')->where('branchId', $gnrBranchId);
                          if ($startDate)
                            $purchRetBrnachIdStock->where(DB::raw('DATE(createdDate)'), '<', $startDate);
                            $purchRetBrnachIdStock = $purchRetBrnachIdStock->pluck('id')->all();
                          $purchRetDetailsOpeningStock = DB::table('inv_purchase_return_details')->select('purchaseReturnId','productId','quantity')->get();
                          $purchaseReturnOpeningStockDateWise = $purchRetDetailsOpeningStock->whereIn('purchaseReturnId', $purchRetBrnachIdStock)
                                                                    ->whereIn('productId', $InvProduct->id)->sum('quantity');
                          $openingStock -= $purchaseReturnOpeningStockDateWise;

                        // Issue Opening Stock                                  
                        if ($startDate){
                          $issueOpenintQty = DB::table('inv_tra_issue_details')->where('issueProductId', $InvProduct->id);
                          if ($startDate)
                            $issueOpenintQty->where(DB::raw('DATE(createdDate)'), '<', $startDate);
                            $issueOpenintQty = (int)$issueOpenintQty->sum('issueQuantity');
                            $openingStock -= $issueOpenintQty;
                         }

                         // Issue Return Opening Stock                                  
                        if ($startDate){
                          $issueReturnOpeningQty = DB::table('inv_tra_issue_return_details')->where('productId', $InvProduct->id);
                          if ($startDate)
                            $issueReturnOpeningQty->where(DB::raw('DATE(createdDate)'), '<', $startDate);
                            $issueReturnOpeningQty = (int)$issueReturnOpeningQty->sum('quantity');
                            $openingStock += $issueReturnOpeningQty;
                         }

                         // Use Opening Stock                                  
                        if ($startDate){
                          $useBrnachIdStock = DB::table('inv_tra_use')->where('branchId', $gnrBranchId);
                          if ($startDate)
                            $useBrnachIdStock->where(DB::raw('DATE(useDate)'), '<', $startDate);
                            $useBrnachIdStock = $useBrnachIdStock->pluck('id')->all();
                            $useDetailsOpeningStock = DB::table('inv_tra_use_details')->select('useId','productId','productQuantity')->get();
                            $useOpeiningDateWiseQty = $useDetailsOpeningStock->whereIn('useId', $useBrnachIdStock)
                                                                    ->whereIn('productId', $InvProduct->id)->sum('productQuantity');
                            $openingStock -= $useOpeiningDateWiseQty;
                         }

                         // Use return Opening Stock                                  
                        if ($startDate){
                          $useRetBrnachStockId = DB::table('inv_tra_use_return')->where('branchId', $gnrBranchId);
                          if ($startDate)
                            $useRetBrnachStockId->where(DB::raw('DATE(createdDate)'), '<', $startDate);
                          $useRetBrnachStockId = $useRetBrnachStockId->pluck('id')->all();
                          $useRetDetailsOpeiningStockProducts = DB::table('inv_tra_use_return_details')->select('useReturnId','productId','productQuantity')->get();
                          $useReturnOpeiningStockDateWiseQty = $useRetDetailsOpeiningStockProducts
                                                                ->whereIn('useReturnId', $useRetBrnachStockId)
                                                                ->whereIn('productId', $InvProduct->id)->sum('productQuantity');
                          $openingStock += $useReturnOpeiningStockDateWiseQty;
                         }

                        }// If statement end

                        $totalopeningStock += $openingStock;
                        ?>
                          {{$openingStock}}
                      </td>

                      <td style="text-align: center; ">
                        <?php
                          $purchaseBrnachIds = DB::table('inv_purchase')->where('branchId', $gnrBranchId);
                        
                        if ($startDate)
                            $purchaseBrnachIds->where(DB::raw('DATE(createdDate)'), '>=', $startDate);
                        if ($endDate)
                          	$purchaseBrnachIds->where(DB::raw('DATE(createdDate)'), '<=', $endDate);
                            $purchaseBrnachIds = $purchaseBrnachIds->pluck('id')->all();

                          $purchaseDetailsProducts = DB::table('inv_purchase_details')->select('purchaseId','productId','quantity')->get();
                          $purchaseQty = $purchaseDetailsProducts->whereIn('purchaseId', $purchaseBrnachIds)
                                                                 ->whereIn('productId', $InvProduct->id)->sum('quantity');
                        
                          $totalPurchaseQty+= $purchaseQty;
                        ?>
                          {{$purchaseQty}}
                      </td>
                      <td style="text-align: center; ">
                        <?php
                          $purchRetBrnachIds = DB::table('inv_purchase_return')->where('branchId', $gnrBranchId);
                          if ($startDate)
                            $purchRetBrnachIds->where(DB::raw('DATE(createdDate)'), '>=', $startDate);
                          if ($endDate)
                            $purchRetBrnachIds->where(DB::raw('DATE(createdDate)'), '<=', $endDate);
                            $purchRetBrnachIds = $purchRetBrnachIds->pluck('id')->all();

                          $purchRetDetailsProducts = DB::table('inv_purchase_return_details')->select('purchaseReturnId','productId','quantity')->get();
                          $purchaseReturnQty = $purchRetDetailsProducts->whereIn('purchaseReturnId', $purchRetBrnachIds)
                                                                    ->whereIn('productId', $InvProduct->id)->sum('quantity');
                          $totalPurchaseRetQty += $purchaseReturnQty;
                        ?>
                         {{$purchaseReturnQty}}
                      </td>
                      <td style="text-align: center; ">
                       <?php
                            $issueQty = DB::table('inv_tra_issue_details')->where('issueProductId', $InvProduct->id);
                          if ($startDate)
                            $issueQty->where(DB::raw('DATE(createdDate)'), '>=', $startDate);
                          if ($endDate)
                            $issueQty->where(DB::raw('DATE(createdDate)'), '<=', $endDate);

                            $issueQty = (int)$issueQty->sum('issueQuantity');

                          $totalIssueQty += $issueQty;
                        ?>
                          {{$issueQty}}
                      </td>
                      <td style="text-align: center; ">
                        <?php
                          
                            $issueReturnQty = DB::table('inv_tra_issue_return_details')->where('productId', $InvProduct->id);
                          if ($startDate)
                            $issueReturnQty->where(DB::raw('DATE(createdDate)'), '>=', $startDate);
                          if ($endDate)
                            $issueReturnQty->where(DB::raw('DATE(createdDate)'), '<=', $endDate);

                            $issueReturnQty = (int)$issueReturnQty->sum('quantity');

                          
                            $totalIssueReturnQty += $issueReturnQty;
                        ?>
                          {{$issueReturnQty}}
                      </td>
                    
                      <td style="text-align: center; ">
                        <?php
                          $useBrnachIds = DB::table('inv_tra_use')->where('branchId', $gnrBranchId);
                          if ($startDate)
                            $useBrnachIds->where(DB::raw('DATE(useDate)'), '>=', $startDate);
                          if ($endDate)
                            $useBrnachIds->where(DB::raw('DATE(useDate)'), '<=', $endDate);

                          $useBrnachIds = $useBrnachIds->pluck('id')->all();

                          $useDetailsProducts = DB::table('inv_tra_use_details')->select('useId','productId','productQuantity')->get();
                          $useQty = $useDetailsProducts->whereIn('useId', $useBrnachIds)
                                                       ->whereIn('productId', $InvProduct->id)->sum('productQuantity');
                          $totalUseQty +=$useQty;                             
                        ?>
                         {{$useQty}}
                      </td>
                      <td style="text-align: center; ">
                        <?php
                          $useRetBrnachIds = DB::table('inv_tra_use_return')->where('branchId', $gnrBranchId);
                          if ($startDate)
                            $useRetBrnachIds->where(DB::raw('DATE(createdDate)'), '>=', $startDate);
                          if ($endDate)
                            $useRetBrnachIds->where(DB::raw('DATE(createdDate)'), '<=', $endDate);
                          $useRetBrnachIds = $useRetBrnachIds->pluck('id')->all();

                          $useRetDetailsProducts = DB::table('inv_tra_use_return_details')->select('useReturnId','productId','productQuantity')->get();
                          $useReturnQty = $useRetDetailsProducts->whereIn('useReturnId', $useRetBrnachIds)
                                                                 ->whereIn('productId', $InvProduct->id)->sum('productQuantity');
                            $totaluseReturnQty +=$useReturnQty; 
                        ?>
                         {{$useReturnQty}}
                      </td>
                      <td style="text-align: center; ">  
                        <?php

                          
                          //$openingStock = (int)$InvProduct->openingStock;
                            $stock = (int)($openingStock+$purchaseQty+$issueReturnQty+$useReturnQty)-(int)($purchaseReturnQty+$issueQty+$useQty);
                          
                          $totalStock += $stock;

                        ?>  
                          {{$stock}} 
                      </td>
                  </tr>
                @endforeach 
            </tbody> 
            
                <tr>
                    <td class="slNo"></td>
                    <td  class="fotBgColor" colspan="7" style="padding: 5px !important; background-color: white; font-weight: bold;"><span style="font-size: 13px;">Total</span></td>

                    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; padding: 0px 10px; font-weight: bold;">{{$totalopeningStock}}</span></td>

                    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; padding: 0px 10px; font-weight: bold;">{{$totalPurchaseQty}}</span></td>

                    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; padding: 0px 10px; font-weight: bold;">{{$totalPurchaseRetQty}}</span></td>

                    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; padding: 0px 10px; font-weight: bold;">{{$totalIssueQty}}</span></td>

                    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; padding: 0px 10px; font-weight: bold;">{{$totalIssueReturnQty}}</span></td>

                    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; padding: 0px 10px; font-weight: bold;">{{$totalUseQty}}</span></td>
                    
                    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; padding: 0px 10px; font-weight: bold;">{{$totaluseReturnQty}}</span></td>

                    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; padding: 0px 10px; font-weight: bold;">{{$totalStock}}</span></td>
                </tr>
            
          </table>
          </div>
        </div>
      </div>
  </div>
  </div>
</div>

@include('dataTableScript')
@endsection
<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>

<script type="text/javascript">
$(function(){
    
    $("#printList").click(function(){

         $(".dataTables_filter, .dataTables_info").css("display", "none");
         $(".stockViewTable_length, .dataTables_paginate").css("display", "none");
         $("#stockViewTable_length").hide();

         // $(".table thead tr th").css({ 
         //     "color":"black"});

         // $(".table tbody tr td").css({"border": "1px solid black"});

         // $(".table thead tr th").css({"border": "1px solid black"});
         

        var printContents = document.getElementById("printView").innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML ="<p class='text-center'>Ambala Social Marketing Ltd.</p><p class='text-center'>Stoct Report</p>" + printContents;
        window.print();
        document.body.innerHTML = originalContents;
    });

    var startDate           = <?php echo json_encode($startDate) ;?>;
                                $("#startDate").val(startDate); 
    var endDate             = <?php echo json_encode($endDate) ;?>;
                                $("#endDate").val(endDate);
    var searchBranchId      = <?php echo json_encode($searchBranchId) ;?>;
                                if(searchBranchId){ $("#branchId").val(searchBranchId);}
    var supplierId          = <?php echo json_encode($supplierId) ;?>;
                                if(supplierId){ $("#supplierId").val(supplierId);}
    var productGroupId      = <?php echo json_encode($productGroupId) ;?>;
                                if(productGroupId){ $("#productGroupId").val(productGroupId);}
    var productCategoryId   = <?php echo json_encode($productCategoryId) ;?>;
                                if(productCategoryId){ $("#productCategoryId").val(productCategoryId);}
    var productSubCategoryId= <?php echo json_encode($productSubCategoryId) ;?>;
                                if(productSubCategoryId){ $("#productSubCategoryId").val(productSubCategoryId);}
    var productBrandId      = <?php echo json_encode($productBrandId) ;?>;
                                if(productBrandId){ $("#productBrandId").val(productBrandId);}
    var productName         = <?php echo json_encode($productName) ;?>;
                                if(productName){ $("#productName").val(productName);} 
    var searchTypeCtrView   = <?php echo json_encode($searchTypeCtrView) ;?>;
                                if(searchTypeCtrView){ $("#searchType").val(searchTypeCtrView);}    
    var fiscalYearCtrView   = <?php echo json_encode($fiscalYearCtrView) ;?>;
                                if(fiscalYearCtrView){ $("#fiscalYear").val(fiscalYearCtrView);} 
    if(fiscalYearCtrView){
        $("#fiscalYearDiv").removeClass('hidden');
    }                                                        
    else if(startDate || endDate){
        $("#dateRangeDiv").removeClass('hidden');
    }

});

    $(function() {

    // $("#fiscalYearDiv").hide();
    // $("#dateRangeDiv").hide();

    var maxSrcDate  = '';
    var minSrcDate  = '';

    var csrf = "<?php echo csrf_token(); ?>";  
            $.ajax({
                type: 'post',
                url: './currentYearFscYrFdLd',
                data: { _token: csrf },
                dataType: 'json',
                success: function(data) {
                    // alert(JSON.stringify(data));
                    maxSrcDate  = data.maxDate ;
                    minSrcDate  = data.minDate ;
                }
            });
    
    $("#searchType").on("change", function(){
        var searchType = $("#searchType").val();
        if(searchType==1){
            $("#fiscalYearDiv").removeClass('hidden');
            $("#dateRangeDiv").addClass('hidden');
            $("#startDate").val('');
            $("#endDate").val('');
        }
        else if(searchType==2){
            $("#dateRangeDiv").removeClass('hidden');
            $("#fiscalYearDiv").addClass('hidden');
            $("#startDate").val('');
            $("#endDate").val('');
            $("#fiscalYear").val('');

            $("#startDate").datepicker("option","maxDate", maxSrcDate);
            $("#startDate").datepicker("option","minDate", minSrcDate);

            $("#endDate").datepicker("option","maxDate", maxSrcDate);
            $("#endDate").datepicker("option","minDate", minSrcDate); 
        }

        else if(searchType == 3){
            $("#dateRangeDiv").removeClass('hidden');
            $("#fiscalYearDiv").addClass('hidden');
            $("#startDate").val('');
            $("#endDate").val('');
            $("#fiscalYear").val('');

            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();
            var maxDates = d.getFullYear() +10+ '-' +
                ((''+month).length<2 ? '0' : '') + month + '-' +
                ((''+day).length<2 ? '0' : '') + day;

            var minDates = d.getFullYear() -10+ '-' +
                ((''+month).length<2 ? '0' : '') + month + '-' +
                ((''+day).length<2 ? '0' : '') + day;    

            $("#startDate").datepicker("option","maxDate", maxDates );
            $("#startDate").datepicker("option","minDate", minDates );

            $("#endDate").datepicker("option","maxDate", maxDates );
            $("#endDate").datepicker("option","minDate", minDates );
        }else{
             $("#dateRangeDiv").addClass('hidden');
            $("#fiscalYearDiv").addClass('hidden');
            $("#fiscalYearDiv").addClass('hidden');
        }

    });

    $( "#startDate" ).datepicker({
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      changeYear: true
      
    });

    $( "#endDate" ).datepicker({
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      changeYear: true
      
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
                  data: { _token: csrf, supplierId:supplierId},
                  dataType: 'json',   
                  success: function( _response ){
                    //alert(JSON.stringify(_response));
                    
                        $("#productId").empty();
                        $("#productId").prepend('<option selected="selected" value="">Select</option>');
                        
                    $.each(_response, function (key, value) {

                            if (key == "productName") {
                                $.each(value, function (key1, value1) {
                                    $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                                })
                            }  

                        if (key == "groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                           })         
                        }

                        if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })    
                            })
                        }

                        if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }

                        if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productBrandId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }

                        if (key == "contactPerson") {
                            $('#contactPerson').val('');
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
                        $("#productId").prepend('<option selected="selected" value="">Select</option>');
                        
                    $.each(_response, function (key, value) {

                            if (key == "productName" && supplierId!=='') {
                                $.each(value, function (key1, value1) {
                                    $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                                })
                            } 

                            if (key == "groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                           })         
                        } 

                        if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })    
                            })
                        }

                        if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }

                        if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Select</option>');
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
                        $("#productId").prepend('<option selected="selected" value="">Select</option>');
                        
                    $.each(_response, function (key, value) {

                            if (key == "productName" && supplierId!=='') {
                                $.each(value, function (key1, value1) {
                                    $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                                })
                            } 

                            if (key == "groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                           })         
                        } 

                        if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })    
                            })
                        }

                        if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }

                        if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Select</option>');
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
                        $("#productId").prepend('<option selected="selected" value="">Select</option>');
                        
                    $.each(_response, function (key, value) {

                            if (key == "productName" && supplierId!=='') {
                                $.each(value, function (key1, value1) {
                                    $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                                })
                            } 

                            if (key == "groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                           })         
                        } 

                        if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })    
                            })
                        }

                        if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }

                        if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Select</option>');
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
              
        }); /*End Change SubCategory*/

        //Change Brand
        $("#productSubCategoryId").change(function(){ 
             var productGroupId             = $('#productGroupId').val();
             var productCategoryId          = $('#productCategoryId').val(); 
             var supplierId                 = $('#supplierId').val();
             var productSubCategoryId       = $('#productSubCategoryId').val();
             var productBrandId             = $('#productBrandId').val();
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
                        $("#productId").prepend('<option selected="selected" value="">Select</option>');
                        
                    $.each(_response, function (key, value) {

                            if (key == "productName" && supplierId!=='') {
                                $.each(value, function (key1, value1) {
                                    $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                                })
                            } 

                            if (key == "groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                           })         
                        } 

                        if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })    
                            })
                        }

                        if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }

                        if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Select</option>');
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
              
        }); /*End Change Brand*/
       
    });

</script>

<style type="text/css">
  #stockViewTable thead tr th{
    font-size: 11px !important;
    padding: 7px !important;
}
  #stockViewTable tbody tr td{
    font-size: 10px !important;
}

/*#stockViewTable tbody tr td.fotBgColor{
    background-color:  #cceeff !important;
}*/

#stockViewTable tbody tr th.reportPgTh{
   color:  black !important;
}

#filteringForm input{
  height:25px;
  border-radius: 1px;
}

#filteringForm select{height:25px; border-radius: 1px;}

.dataTables_filter, .dataTables_info { display: none; } 
/*.stockViewTable_length, .dataTables_paginate { display: none; }  */
</style>

<style type="text/css" media="print">
  @media print {
   thead {display: table-header-group;}
}
</style>


