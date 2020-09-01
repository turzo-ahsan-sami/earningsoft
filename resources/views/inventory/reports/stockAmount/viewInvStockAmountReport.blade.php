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

$totalopeningAmount 	=0;
$totalPurchaseAmount 	=0;
$totalPurchaseRetAmount =0;
$totalIssueAmount 		=0;  
$totalIssueReturnAmount =0;
$totalUseAmount 		=0;
$totaluseReturnAmount  	=0;
$totalStock             =0; 
$totalStockAmount       =0; 
$totalAveragePrice     =0; 



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


if (str_contains(Request::fullUrl(), 'filterStockAmountReport')){
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
                 <button id="printList" class="btn btn-info pull-left print-icon"  target="_blank" style="">
                    <i class="fa fa-print fa-lg" aria-hidden="true">Print</i>
                </button>


                <button id="btnExportExcel" class="btn btn-info pull-center print-icon"  target="_blank" style="">
                    <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Excel</i>
                </button>

                <button  id="btnExportPdf" class="btn btn-info pull-right print-icon"  target="_blank" style="">
                    <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Pdf</i>
                </button>
                <!-- <a href="{{url('addInvBrnRequiF/')}}" class="btn btn-info pull-left addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Requisition</a> -->
            </div>
            <h3 style="text-align: center; color: white">Stock Amount Report</h3>

        </div>
        <div class="panel-body panelBodyView"> 

            <!-- Filtering Start-->  
            <div class="row">
                <div class="col-md-11">

                  {!! Form::open(array('url' => 'filterStockAmountReport/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filteringForm', 'method'=>'get')) !!}

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
                          $supplierNames  = DB::table('gnr_supplier')->select('id','name','supplierCompanyName')->get();
                          $supplierNames = $supplierNames->whereIn('id', $supplierIds);
                          $supplierNames->all();
                          ?> 
                          <select name="supplierId" id="supplierId" class="form-control input-sm">
                            <option value="">Select</option>
                            @foreach($supplierNames as $supplierName) 
                            <option value="{{$supplierName->id}}">{{$supplierName->supplierCompanyName}}</option>
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
 {{--    <div class="form-group">
      {!! Form::label('printList', '', ['class' => 'control-label col-sm-12 hidden']) !!}
      <div class="col-sm-12" style="padding-top: 25%; color: black">
          <button id="printList" style="background-color:transparent;border:none;float:left;">
            <i class="fa fa-print fa-lg" aria-hidden="true"></i>
        </button>
    </div>
</div> --}}
</div>     
</div>             
<!-- filtering end-->     
        {{-- <div>
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

    @php

    if (isset($_GET['branchId'])) {
        if($_GET['branchId']!=''){
            $searchBranchName = DB::table('gnr_branch')->where('id',$_GET['branchId'])->value('name');
        }
        else{
            if($gnrBranchId==1){
                $searchBranchName = "All Branches";
            }
            else{
                $searchBranchName = DB::table('gnr_branch')->where('id',$gnrBranchId)->value('name');
            }
        }
    }
    else{
        $searchBranchName = "All Branches";
    }

    if ($endDate=="" || $endDate==null) {
        $reportingPeriod = "Till ".date("j F, Y");
    }
    else{
        $reportingPeriod = date("d-m-Y",strtotime($startDate)) ." - ".date("d-m-Y",strtotime($endDate));
    }            

    @endphp
    <div id="printDiv">
        <div id="printView">
            <div class="text-center">
                <span style="color: black; font-weight: bold">Ambala Foundation</span><br/>
                <span style="color: black; font-weight: bold">Branch Name: <?php echo $branchName; ?> </span><br/>
                <span style="color: black; font-weight: bold">Reporting Period: {{$reportingPeriod}}</span><br/>
                <span style="color: black; font-weight: bold">Stock Amount Report</span><br>
                <span id="printDate" style="display: none;color: black; font-weight: bold">Print Date: {{date("d-m-Y h:m a")}}</span>
            </div>
            <table class="table table-striped table-bordered" id="stockViewTable"  border="1pt solid ash" style="color:black; border-collapse: collapse;">
                <thead>
                  <tr>
                    <th width="32" class="reportPgTh">SL#</th>
                    <th class="reportPgTh">Product Name</th>
                    <th class="reportPgTh">Supplier</th>
                    <th class="reportPgTh">Group</th>
                    <th class="reportPgTh">Catagory</th>
                    <th class="reportPgTh">Sub Catagory</th>
                    <th class="reportPgTh">Brand</th>
                    <th class="reportPgTh">Model</th>

                    <th class="reportPgTh">O.Stock</th>
                    <th class="reportPgTh">Purchase</th>
                    <th class="reportPgTh">Purchase Return</th>
                    <th class="reportPgTh">Issue</th>
                    <th class="reportPgTh">Issue Return</th>
                    <th class="reportPgTh">Use</th>
                    <th class="reportPgTh">Use Return</th>
                    <th class="reportPgTh">Stock Qty</th>
                    <th class="reportPgTh">T. St. AM.</th>

                </tr>
                {{ csrf_field() }}
            </thead>
            <tbody>
              <?php $no=0; ?>
              @foreach($InvProducts as $InvProduct)  
              <tr>
                  <td class="text-center slNo">{{++$no}}</td>
                  <td style="text-align: left; padding-left: 5px;font-weight: bold;">
                      {{$InvProduct->name}}
                  </td>
                  <td style="text-align: left; padding-left: 5px">
                    <?php
                    $supplierName = DB::table('gnr_supplier')->where('id',$InvProduct->supplierId)->value('supplierCompanyName');
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

                <td style="text-align: right; padding-right: 5px; ">
                    <?php 
                    if($gnrBranchId==1 || $branchName=='Head Office'){
                        $openingStock = $InvProduct->openingStock;
                        $openingAmount = (float)$InvProduct->openingStockAmount;
                    }
                    $totalopeningAmount += $openingAmount;

                    ?>
                    {{ number_format($openingAmount,2) }}
                </td>
                <td style="text-align: right; padding-right: 5px; ">
                    <?php
                    $purchaseBrnachIds = DB::table('inv_purchase')->where('branchId', $gnrBranchId);

                    if ($startDate)
                        $purchaseBrnachIds->where(DB::raw('DATE(createdDate)'), '>=', $startDate);
                    if ($endDate)
                     $purchaseBrnachIds->where(DB::raw('DATE(createdDate)'), '<=', $endDate);
                 $purchaseBrnachIds = $purchaseBrnachIds->pluck('id')->all();

                 $purchaseDetailsProducts = DB::table('inv_purchase_details')->select('purchaseId','productId','quantity', 'totalPrice')->get();
                 $purchaseQty = $purchaseDetailsProducts->whereIn('purchaseId', $purchaseBrnachIds)
                 ->whereIn('productId', $InvProduct->id)
                 ->sum('quantity');
                 $purchaseAmount = $purchaseDetailsProducts->whereIn('purchaseId', $purchaseBrnachIds)
                 ->whereIn('productId', $InvProduct->id)
                 ->sum('totalPrice');
                 $totalPurchaseAmount+= $purchaseAmount;
                 ?>
                 {{ number_format($purchaseAmount,2) }}
             </td>
             <td style="text-align: right; padding-right: 5px; ">
                <?php
                $purchRetBrnachIds = DB::table('inv_purchase_return')->where('branchId', $gnrBranchId);
                if ($startDate)
                    $purchRetBrnachIds->where(DB::raw('DATE(createdDate)'), '>=', $startDate);
                if ($endDate)
                    $purchRetBrnachIds->where(DB::raw('DATE(createdDate)'), '<=', $endDate);
                $purchRetBrnachIds = $purchRetBrnachIds->pluck('id')->all();

                $purchRetDetailsProducts = DB::table('inv_purchase_return_details')->select('purchaseReturnId','productId','quantity', 'totalPrice')->get();
                $purchaseReturnQty = $purchRetDetailsProducts->whereIn('purchaseReturnId', $purchRetBrnachIds)
                ->whereIn('productId', $InvProduct->id)->sum('quantity');
                $purchaseReturnAmount = $purchRetDetailsProducts->whereIn('purchaseReturnId', $purchRetBrnachIds)
                ->whereIn('productId', $InvProduct->id)->sum('totalPrice');
                $totalPurchaseRetAmount += $purchaseReturnAmount;
                ?>
                {{ number_format($purchaseReturnAmount,2) }}
            </td>
            <td style="text-align: right; padding-right: 5px; ">
             <?php
             $issueQty = DB::table('inv_tra_issue_details')->where('issueProductId', $InvProduct->id);
             if ($startDate)
                $issueQty->where(DB::raw('DATE(createdDate)'), '>=', $startDate);
            if ($endDate)
                $issueQty->where(DB::raw('DATE(createdDate)'), '<=', $endDate);
            $issueQty = (int)$issueQty->sum('issueQuantity');

            $issueAmount = DB::table('inv_tra_issue_details')->where('issueProductId', $InvProduct->id);
            if ($startDate)
                $issueAmount->where(DB::raw('DATE(createdDate)'), '>=', $startDate);
            if ($endDate)
                $issueAmount->where(DB::raw('DATE(createdDate)'), '<=', $endDate);
            $issueAmount = (int)$issueAmount->sum('totalPrice');

            $totalIssueAmount += $issueAmount;
            ?>
            {{ number_format($issueAmount,2) }}
        </td>
        <td style="text-align: right; padding-right: 5px; ">
            <?php

            $issueReturnQty = DB::table('inv_tra_issue_return_details')->where('productId', $InvProduct->id);
            if ($startDate)
                $issueReturnQty->where(DB::raw('DATE(createdDate)'), '>=', $startDate);
            if ($endDate)
                $issueReturnQty->where(DB::raw('DATE(createdDate)'), '<=', $endDate);
            $issueReturnQty = (int)$issueReturnQty->sum('quantity');

            $issueReturnAmount = DB::table('inv_tra_issue_return_details')->where('productId', $InvProduct->id);
            if ($startDate)
                $issueReturnAmount->where(DB::raw('DATE(createdDate)'), '>=', $startDate);
            if ($endDate)
                $issueReturnAmount->where(DB::raw('DATE(createdDate)'), '<=', $endDate);
            $issueReturnAmount = (int)$issueReturnAmount->sum('totalAmount');

            $totalIssueReturnAmount += $issueReturnAmount;
            ?>
            {{ number_format($issueReturnAmount,2) }}
        </td>

        <td style="text-align: right; padding-right: 5px; ">
            <?php
            $useBrnachIds = DB::table('inv_tra_use')->where('branchId', $gnrBranchId);
            if ($startDate)
                $useBrnachIds->where(DB::raw('DATE(useDate)'), '>=', $startDate);
            if ($endDate)
                $useBrnachIds->where(DB::raw('DATE(useDate)'), '<=', $endDate);

            $useBrnachIds = $useBrnachIds->pluck('id')->all();

            $useDetailsProducts = DB::table('inv_tra_use_details')->select('useId','productId','productQuantity', 'totalCostPrice')->get();
            $useQty = $useDetailsProducts->whereIn('useId', $useBrnachIds)
            ->whereIn('productId', $InvProduct->id)->sum('productQuantity');
            $useAmount = $useDetailsProducts->whereIn('useId', $useBrnachIds)
            ->whereIn('productId', $InvProduct->id)->sum('totalCostPrice');
            $totalUseAmount +=$useAmount;                             
            ?>
            {{ number_format($useAmount,2) }}
        </td>
        <td style="text-align: right; padding-right: 5px; ">
            <?php
            $useRetBrnachIds = DB::table('inv_tra_use_return')->where('branchId', $gnrBranchId);
            if ($startDate)
                $useRetBrnachIds->where(DB::raw('DATE(createdDate)'), '>=', $startDate);
            if ($endDate)
                $useRetBrnachIds->where(DB::raw('DATE(createdDate)'), '<=', $endDate);
            $useRetBrnachIds = $useRetBrnachIds->pluck('id')->all();

            $useRetDetailsProducts = DB::table('inv_tra_use_return_details')->select('useReturnId','productId','productQuantity', 'totalPrice')->get();
            $useReturnQty = $useRetDetailsProducts->whereIn('useReturnId', $useRetBrnachIds)
            ->whereIn('productId', $InvProduct->id)->sum('productQuantity');
            $useReturnAmount = $useRetDetailsProducts->whereIn('useReturnId', $useRetBrnachIds)
            ->whereIn('productId', $InvProduct->id)->sum('totalPrice');
            $totaluseReturnAmount +=$useReturnAmount; 
            ?>
            {{ number_format($useReturnAmount,2) }}
        </td>
        <td style="text-align: center; ">  
            <?php
                          //$openingStock = (int)$InvProduct->openingStock;
            $stock = (int)($openingStock+$purchaseQty+$issueReturnQty+$useReturnQty)-(int)($purchaseReturnQty+$issueQty+$useQty);
            $totalStock += $stock;

            ?>  
            {{ number_format($stock) }} 
        </td>
        <td style="text-align: right; padding-right: 5px; ">  
            <?php
                          //$openingStock = (int)$InvProduct->openingStock;
            $stockAmount = (float)($openingAmount+$purchaseAmount+$issueReturnAmount+$useReturnAmount)-(float)($purchaseReturnAmount+$issueAmount+$useAmount);
            $totalStockAmount += $stockAmount;
            ?>  
            {{ number_format($stockAmount,2) }} 
        </td>

    </tr>
    @endforeach 
</tbody> 

<tr>
    <td class="slNo"></td>
    <td  class="" colspan="7" style="padding: 5px !important; background-color: white; font-weight: bold;"><span style="font-size: 13px;">Total</span></td>

    <td class="fotBgColor" style="background-color: white; "><span style="font-size: 12px; font-weight: bold;">{{ number_format($totalopeningAmount,2) }}</span></td>

    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; font-weight: bold;">{{ number_format($totalPurchaseAmount,2) }}</span></td>

    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; font-weight: bold;">{{ number_format($totalPurchaseRetAmount,2) }}</span></td>

    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; font-weight: bold;">{{ number_format($totalIssueAmount,2) }}</span></td>

    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; font-weight: bold;">{{ number_format($totalIssueReturnAmount,2) }}</span></td>

    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; font-weight: bold;">{{ number_format($totalUseAmount,2) }}</span></td>

    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; font-weight: bold;">{{ number_format($totaluseReturnAmount,2) }}</span></td>

    <td class="fotBgColor" style="background-color: white;text-align: center;"><span style="font-size: 12px; font-weight: bold;">{{ number_format($totalStock) }}</span></td>
    <td class="fotBgColor" style="background-color: white"><span style="font-size: 12px; font-weight: bold;">{{ number_format($totalStockAmount,2) }}</span></td>

</tr>

</table>
</div>
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

       $("#btnExportExcel").click(function(e) {
        //alert('sdsds');
        var today = new Date();
        var dd = today.getDate();

        var mm = today.getMonth()+1; 
        var yyyy = today.getFullYear();
        if(dd<10) 
        {
            dd='0'+dd;
        } 

        if(mm<10) 
        {
            mm='0'+mm;
        } 
        today = dd+'-'+mm+'-'+yyyy;
        //alert(today);
        let file = new Blob([$('#printDiv').html()], {type:"application/vnd.ms-excel"});
        let url = URL.createObjectURL(file);
        let a = $("<a />", {
          href: url,
          download: "Stock Amount Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
    });

       $("#printList").click(function(){

           $(".dataTables_filter, .dataTables_info").css("display", "none");
           $(".stockViewTable_length, .dataTables_paginate").css("display", "none");
           $("#stockViewTable_length").hide();

         // $(".table thead tr th").css({ 
         //     "color":"black"});

         // $(".table tbody tr td").css({"border": "1px solid black"});

         // $(".table thead tr th").css({"border": "1px solid black"});
         
         $("#printDate").show();
         var printContents = document.getElementById("printView").innerHTML;
         var originalContents = document.body.innerHTML;
         document.body.innerHTML = printContents;
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

#stockViewTable tbody tr td.fotBgColor{
    text-align: right;
    padding-right: 5px;
}

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


