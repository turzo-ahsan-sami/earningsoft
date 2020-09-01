@extends('layouts/fams_layout')
@section('title', '| Report')
@section('content')
@include('successMsg')


<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">

                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                           {{--  <a href="" class="btn btn-info pull-right addViewBtn" id="print"><i class="fa fa-print" aria-hidden="true"></i> Print</a> --}}
                           <button id="print" class="btn btn-info pull-left print-icon"  target="_blank" style="">
                            <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                        </button>

                        <button id="btnExportExcel" class="btn btn-info pull-center print-icon"  target="_blank" style="">
                            <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Excel</i>
                        </button>

                        <button  id="btnExportPdf" class="btn btn-info pull-right print-icon"  target="_blank" style="">
                            <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Pdf</i>
                        </button>

                    </div>

                    @php
                    function dotPrint($value){
                        $value1 = (float) $value;
                        if ($value1<=0) {
                            return "-";
                        }
                        else{
                            return number_format($value1,2);
                        }
                    }
                    @endphp


                    <div class="row" id="filtering-group">

                        <div class="form-horizontal form-groups" style="padding-right: 0px;">

                            {!! Form::open(['url' => 'famsFixedAssetsScheduleReport','method' => 'get']) !!}
                            @php
                            $userBranchId = Auth::user()->branchId;
                            @endphp

                            @if($userBranchId==1)
                            <div class="col-md-2">
                                <div class="form-group" style="font-size: 13px; color:black;">
                                 <div style="text-align: center;" class="col-sm-12">
                                    {!! Form::label('', 'Project:', ['class' => 'control-label pull-left']) !!}
                                </div> 

                                <div class="col-sm-12">
                                    <select name="searchProject" class="form-control input-sm" id="searchProject">
                                        <option value="">All</option>                                                    
                                        @foreach($projects as $project)
                                        <option value="{{$project->id}}" @if($project->id==$projectSelected){{"selected=selected"}}@endif>{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT).'-'.$project->name}}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        @endif

                        @if($userBranchId==1)
                        <div class="col-md-2">
                            <div class="form-group" style="font-size: 13px; color:black;">
                                <div style="text-align: center;" class="col-sm-12">
                                    {!! Form::label('', 'Pro. Type:', ['class' => 'control-label pull-left']) !!}
                                </div>

                                <div class="col-sm-12">
                                    <select name="searchProjectType" class="form-control input-sm" id="searchProjectType">
                                        <option value="">All</option>
                                        @foreach($projectTypes as $projectType)
                                        <option value="{{$projectType->id}}" @if($projectType->id==$projectTypeSelected){{"selected=selected"}}@endif>{{str_pad($projectType->projectTypeCode,3,"0",STR_PAD_LEFT).'-'.$projectType->name}}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        @endif

                        @if($userBranchId==1)
                        <div class="col-md-2">
                            <div class="form-group" style="font-size: 13px; color:black;">
                                <div style="text-align: center;" class="col-sm-12">
                                    {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left']) !!}
                                </div>

                                <div class="col-sm-12">
                                    <select name="searchBranch" class="form-control input-sm" id="searchBranch">
                                        <option value="">All</option>
                                        <option value="0" @if($branchSelected===0){{"selected=selected"}}@endif>All Branches</option>
                                        @foreach($branches as $branch)
                                        <option value="{{$branch->id}}" @if($branch->id==$branchSelected){{"selected=selected"}}@endif>{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT).'-'.$branch->name}}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        @endif


                        <div class="col-md-0" style="display: none;">
                            <div class="form-group" style="font-size: 13px; color:black;">
                             <div style="text-align: center;" class="col-sm-12">
                                {!! Form::label('', 'Category:', ['class' => 'control-label pull-left']) !!}
                            </div> 

                            <div class="col-sm-12">
                                <select name="searchCategory" class="form-control input-sm" id="searchCategory">
                                    <option value="">All</option>
                                    @foreach($categories as $category)
                                    <option value="{{$category->id}}" @if($category->id==$categorySelected){{"selected=selected"}}@endif>{{str_pad($category->categoryCode,3,'0',STR_PAD_LEFT).'-'.$category->name}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-0" style="display: none;">
                        <div class="form-group" style="font-size: 13px; color:black;">
                         <div style="text-align: center;" class="col-sm-12">
                            {!! Form::label('', 'Pro. Type:', ['class' => 'control-label pull-left']) !!}
                        </div> 

                        <div class="col-sm-12">
                            <select name="searchProductType" class="form-control input-sm" id="searchProductType">
                                <option value="">All</option>
                                @foreach($productTypes as $productType)
                                <option value="{{$productType->id}}" @if($productType->id==$productTypeSelected){{"selected=selected"}}@endif>{{str_pad($productType->productTypeCode,3,'0',STR_PAD_LEFT).'-'.$productType->name}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group" style="font-size: 13px; color:black;">
                        <div style="text-align: center;" class="col-sm-12">
                            {!! Form::label('', 'Search By:', ['class' => 'control-label pull-left']) !!}
                        </div>

                        <div class="col-sm-12">
                            {!! Form::select('searchMethod',[''=>'Please Select','1'=>'Fiscal Year','2'=>'Current Year'],$searchMethodSelected,['id'=>'searchMethod','class'=>'form-control input-sm']) !!}

                        </div>
                    </div>
                </div>


                <div class="col-md-2" style="display: none;" id="fiscalYearDiv">
                    <div class="form-group" style="font-size: 13px; color:black">
                        {!! Form::label('', ' ', ['class' => 'control-label col-sm-12']) !!}
                        <div class="col-sm-12" style="padding-top: 18px;">

                            {!! Form::select('fiscalYear', $fiscalYears, $fiscalYearSelected, array('class'=>'form-control input-sm', 'id' => 'fiscalYear')) !!}

                        </div>
                    </div>
                </div>
                <div class="col-md-2" style="display: none;" id="dateRangeDiv">
                    <div class="form-group" style="font-size: 13px; color:black">
                        <div style="text-align: center;" class="col-sm-12">
                            {!! Form::label('', ' ', ['class' => 'control-label']) !!}
                        </div>

                        <div class="col-sm-12" style="padding-top: 7px;">
                            <div class="form-group">
                                <div class="col-sm-0" style="display: none;">
                                    {!! Form::text('dateFrom',$dateFromSelected,['id'=>'dateFrom','placeholder'=>'From','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                    <p id="dateFrome" style="color: red;display: none;">*Required</p>
                                </div>
                                <div class="col-sm-12">
                                    {!! Form::text('dateTo',$dateToSelected,['id'=>'dateTo','placeholder'=>'To','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                    <p id="dateToe" style="color: red;display: none;">*Required</p>
                                </div>
                            </div>

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

        </div>{{-- End Filtering Group --}}


        <h2 align="center" style="font-family: Antiqua;letter-spacing: 2px; color: white;margin-top: 0px;">Fixed Assets Schedule Report</h2>
    </div>
    <div class="panel-body panelBodyView">
        <div>

        </div>
        @php
        if($branchSelected===0){
            $selectedBranchName = "All Branches";
        }
        else{
            $selectedBranchName = DB::table('gnr_branch')->where('id',$branchSelected)->value('name');
        }
        $selectedProjectName = DB::table('gnr_project')->where('id',$projectSelected)->value('name');
        $selectedProjectTypeName = DB::table('gnr_project_type')->where('id',$projectTypeSelected)->value('name');
        $selectedCategoryName = DB::table('fams_product_category')->where('id',$categorySelected)->value('name');

        @endphp
        @if(!$firstRequest)
        <div id="printDiv">
            <div id="printingContent">

                <div style="display: none;text-align: center;" id="hiddenTitle">
                   <h3 style="text-align: center;padding: 0px;margin: 0px;">Ambala Foundation</h3>
                   <p style="text-align: center;padding: 0px;margin: 0px;font-size: 10px;">House # 62, Block # Ka, Piciculture Housing Society, Shyamoli, Dhaka-1207</p>
                   <h4 style="text-align: center;padding: 0px;margin: 0px;">Fixed Assets Schedule</h4>
                   <h5 style="text-align: center;font-size: 14;padding: 0px;margin: 0px;">{{date('F d, Y',strtotime($endDate))}}</h5>
               </div> 

               <div id="hiddenInfo" style="display: none;font-weight: bold;">  

                 <p style="padding: 0px;margin: 0px;font-size: 11px;">Branch Name : @php
                 if($selectedBranchName==null){
                    echo "All";
                }
                else{
                    echo $selectedBranchName;
                }
                @endphp                               
                <span style='float: right;'>
                 Project Name : @php
                 if($selectedProjectName==null){
                    echo "All";
                }
                else{
                    echo $selectedProjectName;
                }
                @endphp                   
            </span>      
        </p>  

        <p style="padding: 0px;margin: 0px;font-size: 11px;">Reporting Year(FY): {{date('Y',strtotime($startDate))." - ".date('Y',strtotime('+1 year',strtotime($startDate)))}}
         <span style='float: right;'>
             Project Type : @php
             if($selectedProjectTypeName==null){
                echo "All";
            }
            else{
                echo $selectedProjectTypeName;
            }
            @endphp
        </span>      
    </p> 




</div>
<br>


<table id="famsAssetScheduleReportTable" class="table table-striped table-bordered" style="color:black;font-size:11px;margin-bottom:50px;border-collapse: collapse;" border= "1px solid black;" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th rowspan="3" width="200">Particulars</th>
            <th colspan="8" width="1000" style="border-bottom: 0pt;">Cost Value (Tk)</th>
            <th colspan="8" style="border-bottom: 0pt;">Accmulated Depreciation (Tk)</th>
            <th rowspan="3" width="100">Written Down Value</th>                                          
        </tr>
        <tr>
            <th rowspan="2" width="80">Opening Balance</th>
            <th colspan="3" style="border-bottom: 0pt;">Current Period Add</th>
            <th colspan="3" style="border-bottom: 0pt;">Current Period Less</th>
            <th rowspan="2" width="80">Closing Balance</th>
            <th rowspan="2">Dep. Rate(%)</th>
            <th rowspan="2">Opening Balance</th>
            <th colspan="5" style="border-bottom: 0pt;">Current Period Add/Less</th>
            <th rowspan="2" style="border-right: 1pt solid ash;">Closing Balance</th>
        </tr>
        <tr>
            <th>Purchase</th>
            <th>Adjustment</th>
            <th>Transfer</th>
            <th>Sales</th>
            <th>Adjustment</th>
            <th style="border-right: 1pt solid ash;">Transfer</th>
            <th>Depreciation</th>
            <th>Disposal</th>                                             
            <th>Transfer In</th>
            <th>Transfer Out</th>
            <th>Adjustment</th>
        </tr>
    </thead>
    <tbody>
        @php

        $totalOpeningBalnace = 0;
        $totalPurchase = 0;
        $totalAdjustmentIn = 0;
        $totalTransferIn = 0;
        $totalSale = 0;
        $totalAdjustmentOut = 0;
        $totalTransferOut = 0;
        $totalClosing = 0;

        $totalDepOpeningBalance = 0;
        $totalCurrentDep = 0;
        $totalDisposalAmount = 0;
        $totalDepTransferIn = 0;
        $totalDepTransferOut = 0;
        $totalDepAdjustment = 0;
        $totalDepClosingBalance = 0;
        $totalWrittenDown = 0;

        @endphp



        @foreach($allGroups as $group)
        @php
        $gOpeningBalance = 0;
        $gPurchase = 0;
        $gAdjustmentIn = 0;
        $gTransferIn = 0;
        $gSale = 0;
        $gAdjustmentOut = 0;
        $gTransferOut = 0;
        $gClosingBalance = 0;
        $gDepOpeningBalance = 0;
        $gCurrentDep = 0;
        $gDepDisposalAmount = 0;
        $gDepTransferIn = 0;
        $gDepTransferOut = 0;
        $gDepAdjustment = 0;
        $gDepClosingBalance = 0;
        $gWrittenDownValue = 0;

        @endphp

        <tr>
           <td colspan="18" style="text-align: left;padding-left: 5px;font-size: 13px;font-weight: bold;background-color: #C1BBBA">{{$group->name}}</td>
       </tr>

       @php
       $tCategories = DB::table('fams_product_category')->where('productGroupId',$group->id)->get();
       @endphp

       @foreach($tCategories as $tCategory)   
       @php
       $cOpeningBalance = 0;
       $cPurchase = 0;
       $cAdjustmentIn = 0;
       $cTransferIn = 0;
       $cSale = 0;
       $cAdjustmentOut = 0;
       $cTransferOut = 0;
       $cClosingBalance = 0;
       $cDepOpeningBalance = 0;
       $cCurrentDep = 0;
       $cDepDisposalAmount = 0;
       $cDepTransferIn = 0;
       $cDepTransferOut = 0;
       $cDepAdjustment = 0;
       $cDepClosingBalance = 0;
       $cWrittenDownValue = 0;

       $cDepPercentage = 0; 



       @endphp  

       @foreach ($branchIds as $branchId)


       @php

       $transferedAllProduct = DB::table('fams_tra_transfer as t1')
       ->join('fams_product as t2','t1.productId','=','t2.id')
       ->where('t1.branchIdFrom',$branchId)
       ->where('purchaseDate','>=',$startDate)
       ->where('purchaseDate','<=',$endDate)
       ->where('t2.categoryId',$tCategory->id)
       ->whereIn('t2.projectId',$projectId)
       ->whereIn('t2.projectTypeId',$projectTypeId)
       ->pluck('productId');

       $productsOfthisCategory = DB::table('fams_product')->where('categoryId',$tCategory->id)->where('branchId',$branchId)->where('purchaseDate','<=',$endDate)->whereIn('projectId',$projectId)->whereIn('projectTypeId',$projectTypeId)->orWhereIn('id',$transferedAllProduct)->orderBy('purchaseDate','asc')->get();
       @endphp                 

       @foreach($productsOfthisCategory as $product)


       @php

       $transferedOutProduct = DB::table('fams_tra_transfer')->where('productId',$product->id)->where('branchIdFrom',$branchId)->where('transferDate','>',$startDate)->get();

       $transferedInProduct = DB::table('fams_tra_transfer')->where('productId',$product->id)->where('branchIdTo',$branchId)->where('transferDate','>',$endDate)->get();

       $depDisposalamount = 0;
       $saleAmount = 0;
       $adjustmentIn = 0;
       $adjustmentOut = 0;
       $transferInAmount = 0;
       $transferOutAmount = 0;
       $openingDisposalamount = 0;
       $openingDepTransferInAmount = 0;
       $openingDepTransferOutAmount = 0;
       $openingDepAdjustment = 0;
       @endphp
       @if($branchId==$product->branchId || $transferedOutProduct->count()>0)
       @if($transferedInProduct->count()==0)
       @php

       $branchName = DB::table('gnr_branch')->where('id',$branchId)->value('name');
       $purchaseDate = date('Y-m-d', strtotime($product->purchaseDate));

                                            //Get the Additional Charge After Start Date
       $postAddCharge = (float) DB::table('fams_additional_charge')->where('branchId',$branchId)->where('productId',$product->id)->where('purchaseDate','>=',$startDate)->where('purchaseDate','<=',$endDate)->sum('amount');


       if($purchaseDate<$startDate){

          $additionalCharge = (float) DB::table('fams_additional_charge')->where('branchId',$branchId)->where('productId',$product->id)->where('purchaseDate','<',$startDate)->sum('amount');

          $soldOut = DB::table('fams_sale')->where('productId',$product->id)->where('branchId',$branchId)->where('createdDate','<',$startDate)->first();



          if ($soldOut) {
              $saleAmount = $soldOut->amount;
              $adjustmentIn = $soldOut->profitAmount;
              $adjustmentOut = round((float) $soldOut->lossAmount + (float) DB::table('fams_depreciation_details')->where('depTo','<',$startDate)->where('productId',$product->id)->sum('amount') + (float)DB::table('fams_product')->where('id',$product->id)->value('depreciationOpeningBalance'),2);
          }                                              

                                              //If product is write offed (for opening balance)
          $writeOffed = DB::table('fams_write_off')->where('productId',$product->id)->where('branchId',$branchId)->where('createdDate','<',$startDate)->first();

          if ($writeOffed) {
            $adjustmentOut = round((float) $additionalCharge + DB::table('fams_product')->where('id',$product->id)->value('totalCost'),2);
            $openingDisposalamount = round((float) $writeOffed->amount,2);
            $openingDepAdjustment = (float) $product->totalCost + (float) DB::table('fams_additional_charge')->where('productId',$product->id)->sum('amount');
        }                                              
                                              //If product Transfered In Then get Tranfer Amount (for opening balance)
        $isTransferdInProduct = DB::table('fams_tra_transfer')->where('productId',$product->id)->where('branchIdTo',$branchId)->where('transferDate','<',$startDate)->orderBy('id','desc')->first();
        if ($isTransferdInProduct) {
          $transferInAmount = (float) $product->totalCost + (float) DB::table('fams_additional_charge')->where('productId',$product->id)->where('purchaseDate','<',$isTransferdInProduct->transferDate)->sum('amount');
          $openingDepTransferInAmount = $isTransferdInProduct->pastDep;
      }
      $isTransferdOutProduct = DB::table('fams_tra_transfer')->where('productId',$product->id)->where('branchIdFrom',$branchId)->where('transferDate','<',$startDate)->orderBy('id','desc')->first();

      if($isTransferdOutProduct){
          $transferOutAmount = (float) $product->totalCost + (float) DB::table('fams_additional_charge')->where('productId',$product->id)->where('purchaseDate','<',$isTransferdOutProduct->transferDate)->sum('amount');
          $openingDepTransferOutAmount = round((float) DB::table('fams_depreciation_details')->where('productId',$product->id)->where('branchId',$branchId)->where('depTo','<',$startDate)->sum('amount'),2);

      }

      $openingBalance = (float) $product->totalCost + $additionalCharge + $adjustmentIn - $saleAmount - $adjustmentOut ;
      $purchase = $postAddCharge;
                                            } // end for opening balance

                                            else{
                                              $openingBalance = 0;

                                              $additionalCharge = (float) DB::table('fams_additional_charge')->where('productId',$product->id)->where('branchId',$branchId)->where('purchaseDate','>=',$startDate)->where('purchaseDate','<=',$endDate)->sum('amount');
                                              $purchase = (float) $product->totalCost + $additionalCharge;
                                          }

                                            //If product is sold out
                                          $soldOut = DB::table('fams_sale')->where('productId',$product->id)->where('branchId',$branchId)->where('createdDate','>=',$startDate)->where('createdDate','<=',$endDate)->first();
                                          if ($soldOut) {
                                              $saleAmount = $soldOut->amount;
                                              $adjustmentIn = $soldOut->profitAmount;
                                              //$adjustmentOut = Dep. Generated + Loss amount
                                              $adjustmentOut = round((float) $soldOut->lossAmount + (float) DB::table('fams_depreciation_details')->where('productId',$product->id)->where('depTo','<',$endDate)->sum('amount') + (float)DB::table('fams_product')->where('id',$product->id)->value('depreciationOpeningBalance'),2);

                                              //$depAdjustment = round(DB::table('fams_depreciation_details')->where('productId',$product->id)->where('depTo','>=',$startDate)->where('depTo','<=',$endDate)->sum('amount'),2);
                                              $depDisposalamount = 0;
                                          }
                                          else{

                                              $saleAmount = 0;
                                              $adjustmentIn = 0;
                                              $adjustmentOut = 0;
                                              //If product is write offed
                                              $writeOffed = DB::table('fams_write_off')->where('productId',$product->id)->where('branchId',$branchId)->where('createdDate','>=',$startDate)->where('createdDate','<=',$endDate)->first();
                                              if ($writeOffed) {
                                                //$adjustmentOut = $writeOffed->lossAmount;
                                                $adjustmentOut = round((float) $additionalCharge + DB::table('fams_product')->where('id',$product->id)->value('totalCost'),2);

                                                /*$depDisposalamount = round((float) DB::table('fams_write_off')->where('productId',$product->id)->where('createdDate','>=',$startDate)->where('createdDate','<=',$endDate)->value('amount'),2);*/
                                                $depDisposalamount = round((float) $writeOffed->amount,2);


                                                //$depAdjustment = round((float) $additionalCharge + DB::table('fams_product')->where('id',$product->id)->value('totalCost'),2);
                                            }
                                            else{
                                                $adjustmentOut = 0;
                                                $depDisposalamount = 0;
                                                //$depAdjustment = 20;
                                                
                                            }

                                        }


                                            //If Transfered Out Then get old product Code and Tranfer Amount
                                        $isTransferdOutProduct = DB::table('fams_tra_transfer')->where('productId',$product->id)->where('branchIdFrom',$branchId)->where('transferDate','>=',$startDate)->where('transferDate','<=',$endDate)->orderBy('id','desc')->first();

                                        if($isTransferdOutProduct){
                                          $productCode = $isTransferdOutProduct->oldProductCode;
                                          $transferOutAmount = (float) $product->totalCost + (float) DB::table('fams_additional_charge')->where('productId',$product->id)->where('purchaseDate','<',$isTransferdOutProduct->transferDate)->sum('amount');
                                      }
                                      else{
                                          $productCode = $product->productCode;
                                          $transferOutAmount = 0;

                                      }

                                            //If product Transfered In Then get Tranfer Amount
                                      $isTransferdInProduct = DB::table('fams_tra_transfer')->where('productId',$product->id)->where('branchIdTo',$branchId)->where('transferDate','>=',$startDate)->where('transferDate','<=',$endDate)->orderBy('id','desc')->first();
                                      if($isTransferdInProduct){                                              
                                          $transferInAmount = (float) $product->totalCost + (float) DB::table('fams_additional_charge')->where('productId',$product->id)->where('purchaseDate','<',$isTransferdInProduct->transferDate)->sum('amount');                                               

                                          $openingBalance = 0;
                                          $purchase = 0;

                                      }
                                      else{                                              
                                          $transferInAmount = 0;

                                      }


                                      $closingBalance = round($openingBalance + $purchase + $adjustmentIn + $transferInAmount - $saleAmount - $adjustmentOut - $transferOutAmount,2);

                                            ///////    Dep   ///////

                                      $openingAccDep = round($product->depreciationOpeningBalance + (float) DB::table('fams_depreciation_details')->where('productId',$product->id)->where('branchId',$branchId)->where('depTo','<',$startDate)->sum('amount'),2);  
                                      $openingAccDep += $openingDisposalamount + $openingDepTransferInAmount - $openingDepAdjustment;                                         

                                      $currentAccDep = round((float) DB::table('fams_depreciation_details')->where('productId',$product->id)->where('branchId',$branchId)->where('depTo','>=',$startDate)->where('depTo','<=',$endDate)->sum('amount'),2);

                                            //If product is sold out
                                      $soldOut = DB::table('fams_sale')->where('productId',$product->id)->where('branchId',$branchId)->where('createdDate','>=',$startDate)->where('createdDate','<=',$endDate)->first();
                                      if ($soldOut) {
                                        $depAdjustment = $openingAccDep + $currentAccDep;
                                    }
                                    else{
                                                 //If product is write offed
                                      $writeOffed = DB::table('fams_write_off')->where('productId',$product->id)->where('branchId',$branchId)->where('createdDate','>=',$startDate)->where('createdDate','<=',$endDate)->first();
                                      if ($writeOffed) {
                                        $depAdjustment = (float) $product->totalCost + (float) DB::table('fams_additional_charge')->where('productId',$product->id)->sum('amount');  
                                    }
                                    else{
                                        $depAdjustment = 0;
                                    }
                                }

                                            //If product is Transfered In
                                if($isTransferdInProduct){
                                    $openingAccDep=0;
                                    $depTransferInAmount = (float) DB::table('fams_tra_transfer')->where('branchIdTo',$branchId)->where('productId',$product->id)->where('transferDate','>=',$startDate)->where('transferDate','<=',$endDate)->value('pastDep');
                                }
                                else{
                                    $depTransferInAmount = 0;
                                }

                                            //If product Is transfered Out
                                if($isTransferdOutProduct){
                                    $depTransferOutAmount = $openingAccDep + $currentAccDep;
                                }
                                else{
                                    $depTransferOutAmount = 0;
                                }

                                $depTransferAmount = $depTransferOutAmount + $depTransferInAmount;

                                $depClosingBalance = round($openingAccDep + $currentAccDep + $depDisposalamount + $depTransferInAmount - $depAdjustment - $depTransferOutAmount,2);

                                @endphp


                                @php
                                $cOpeningBalance = $cOpeningBalance + $openingBalance;
                                $cPurchase = $cPurchase + $purchase;
                                $cAdjustmentIn = $cAdjustmentIn + $adjustmentIn;
                                $cTransferIn = $cTransferIn + $transferInAmount;
                                $cSale = $cSale + $saleAmount;
                                $cAdjustmentOut = $cAdjustmentOut + $adjustmentOut;
                                $cTransferOut = $cTransferOut + $transferOutAmount;
                                $cClosingBalance = $cClosingBalance + $closingBalance;
                                $cDepOpeningBalance = $cDepOpeningBalance + $openingAccDep;
                                $cCurrentDep = $cCurrentDep + $currentAccDep;
                                $cDepDisposalAmount = $cDepDisposalAmount + $depDisposalamount;
                                $cDepTransferIn = $cDepTransferIn + $depTransferInAmount;
                                $cDepTransferOut = $cDepTransferOut + $depTransferOutAmount;
                                $cDepAdjustment = $cDepAdjustment + $depAdjustment;
                                $cDepClosingBalance = $cDepClosingBalance + $depClosingBalance;
                                $cWrittenDownValue = $cWrittenDownValue + $closingBalance - $depClosingBalance;
                                $cDepPercentage = $product->depreciationPercentage; 
                                @endphp



                                @endif
                                @endif
                                @endforeach   {{-- End Product Foreach --}}

                                @endforeach  {{-- For Branch --}}
                                <tr>
                                    <td style="text-align: left;padding-left: 5px;">{{$tCategory->name}}</td>
                                    <td>{{dotPrint($cOpeningBalance)}}</td>
                                    <td>{{dotPrint($cPurchase)}}</td>
                                    <td>{{dotPrint($cAdjustmentIn)}}</td>
                                    <td>{{dotPrint($cTransferIn)}}</td>
                                    <td>{{dotPrint($cSale)}}</td>
                                    <td>{{dotPrint($cAdjustmentOut)}}</td>
                                    <td>{{dotPrint($cTransferOut)}}</td>
                                    <td>{{dotPrint($cClosingBalance)}}</td>                                            
                                    <td style="text-align: center;">@if($cDepPercentage>0){{$cDepPercentage}}@else{{"-"}}@endif</td>
                                    <td>{{dotPrint($cDepOpeningBalance)}}</td>
                                    <td>{{dotPrint($cCurrentDep)}}</td>
                                    <td>{{dotPrint($cDepDisposalAmount)}}</td>
                                    <td>{{dotPrint($cDepTransferIn)}}</td>
                                    <td>{{dotPrint($cDepTransferOut)}}</td>
                                    <td>{{dotPrint($cDepAdjustment)}}</td>
                                    <td>{{dotPrint($cDepClosingBalance)}}</td>
                                    <td>{{dotPrint($cWrittenDownValue)}}</td>


                                </tr>
                                @php
                                $gOpeningBalance = $gOpeningBalance + $cOpeningBalance;
                                $gPurchase = $gPurchase + $cPurchase;
                                $gAdjustmentIn = $gAdjustmentIn + $cAdjustmentIn;
                                $gTransferIn = $gTransferIn + $cTransferIn;
                                $gSale = $gSale + $cSale;
                                $gAdjustmentOut = $gAdjustmentOut + $cAdjustmentOut;
                                $gTransferOut = $gTransferOut + $cTransferOut;
                                $gClosingBalance = $gClosingBalance + $cClosingBalance;
                                $gDepOpeningBalance = $gDepOpeningBalance + $cDepOpeningBalance;
                                $gCurrentDep = $gCurrentDep + $cCurrentDep;
                                $gDepDisposalAmount = $gDepDisposalAmount + $cDepDisposalAmount;
                                $gDepTransferIn = $gDepTransferIn + $cDepTransferIn;
                                $gDepTransferOut = $gDepTransferOut + $cDepTransferOut;
                                $gDepAdjustment = $gDepAdjustment + $cDepAdjustment;
                                $gDepClosingBalance = $gDepClosingBalance + $cDepClosingBalance;
                                $gWrittenDownValue = $gWrittenDownValue + $cWrittenDownValue;
                                @endphp

                                @endforeach   {{-- End Category Foreach --}} 

                                <tr style="font-weight: bold;background-color: #C0C0C0 !important;">
                                    <td style="text-align: center;padding-left: 5px;">{{"Sub Total"}}</td>
                                    <td>{{dotPrint($gOpeningBalance)}}</td>
                                    <td>{{dotPrint($gPurchase)}}</td>
                                    <td>{{dotPrint($gAdjustmentIn)}}</td>
                                    <td>{{dotPrint($gTransferIn)}}</td>
                                    <td>{{dotPrint($gSale)}}</td>
                                    <td>{{dotPrint($gAdjustmentOut)}}</td>
                                    <td>{{dotPrint($gTransferOut)}}</td>
                                    <td>{{dotPrint($gClosingBalance)}}</td>
                                    <td></td>
                                    <td>{{dotPrint($gDepOpeningBalance)}}</td>
                                    <td>{{dotPrint($gCurrentDep)}}</td>
                                    <td>{{dotPrint($gDepDisposalAmount)}}</td>
                                    <td>{{dotPrint($gDepTransferIn)}}</td>
                                    <td>{{dotPrint($gDepTransferOut)}}</td>
                                    <td>{{dotPrint($gDepAdjustment)}}</td>
                                    <td>{{dotPrint($gDepClosingBalance)}}</td>
                                    <td>{{dotPrint($gWrittenDownValue)}}</td> 
                                </tr>

                                @php
                                $totalOpeningBalnace = $totalOpeningBalnace + $gOpeningBalance;
                                $totalPurchase = $totalPurchase + $gPurchase;
                                $totalAdjustmentIn = $totalAdjustmentIn + $gAdjustmentIn;
                                $totalTransferIn = $totalTransferIn + $gTransferIn;
                                $totalSale = $totalSale + $gSale;
                                $totalAdjustmentOut = $totalAdjustmentOut + $gAdjustmentOut;
                                $totalTransferOut = $totalTransferOut + $gTransferOut;
                                $totalClosing = $totalClosing + $gClosingBalance;
                                $totalDepOpeningBalance = $totalDepOpeningBalance + $gDepOpeningBalance;
                                $totalCurrentDep = $totalCurrentDep + $gCurrentDep;
                                $totalDisposalAmount = $totalDisposalAmount + $gDepDisposalAmount;
                                $totalDepTransferIn = $totalDepTransferIn + $gDepTransferIn;
                                $totalDepTransferOut = $totalDepTransferOut + $gDepTransferOut;
                                $totalDepAdjustment = $totalDepAdjustment + $gDepAdjustment;
                                $totalDepClosingBalance = $totalDepClosingBalance + $gDepClosingBalance;
                                $totalWrittenDown = $totalWrittenDown + $gWrittenDownValue;
                                @endphp


                                @endforeach {{-- End Group Foreach --}}


                                <tr style="background-color: #696969 !important;font-weight: bold;">
                                    <td style="text-align: center;padding-left: 5px;">{{"Grand Total"}}</td>
                                    <td>{{dotPrint($totalOpeningBalnace)}}</td>
                                    <td>{{dotPrint($totalPurchase)}}</td>
                                    <td>{{dotPrint($totalAdjustmentIn)}}</td>
                                    <td>{{dotPrint($totalTransferIn)}}</td>
                                    <td>{{dotPrint($totalSale)}}</td>
                                    <td>{{dotPrint($totalAdjustmentOut)}}</td>
                                    <td>{{dotPrint($totalTransferOut)}}</td>
                                    <td>{{dotPrint($totalClosing)}}</td>
                                    <td></td>
                                    <td>{{dotPrint($totalDepOpeningBalance)}}</td>
                                    <td>{{dotPrint($totalCurrentDep)}}</td>
                                    <td>{{dotPrint($totalDisposalAmount)}}</td>
                                    <td>{{dotPrint($totalDepTransferIn)}}</td>
                                    <td>{{dotPrint($totalDepTransferOut)}}</td>
                                    <td>{{dotPrint($totalDepAdjustment)}}</td>
                                    <td>{{dotPrint($totalDepClosingBalance)}}</td>
                                    <td>{{dotPrint($totalWrittenDown)}}</td> 
                                </tr>

                            </tbody>
                        </table>

                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>


<script type="text/javascript">
  $(document).ready(function() {

   $(document).ready(function() {
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
          download: "Fixed Assets Schedule Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
    });

 });

   function toDate(dateStr) {
    var parts = dateStr.split("-");
    return new Date(parts[2], parts[1] - 1, parts[0]);
}

/* Date Range From */
$("#dateFrom").datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange : "1998:c",
    maxDate: "dateToday",
    dateFormat: 'dd-mm-yy',
    onSelect: function () {
        $('#dateFrome').hide();          
        $("#dateTo").datepicker("option","minDate",new Date(toDate($(this).val())));
        $( "#dateTo" ).datepicker( "option", "disabled", false );      
    }
});
/* Date Range From */



/* Date Range To */
$("#dateTo").datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange : "1998:c",
    maxDate: "dateToday",
    dateFormat: 'dd-mm-yy',
    onSelect: function () {
        $('#dateToe').hide();           
    }
});
//$( "#dateTo" ).datepicker( "option", "disabled", true );
/* End Date Range To */

var dateFromData = $("#dateFrom").val();

if (dateFromData!="") {
    $("#dateTo").datepicker("option","minDate",new Date(toDate(dateFromData)));
        //$("#dateTo").datepicker( "option", "disabled", false );  
    }


    /*Active Deactive Search Option Base on Radio Button*/
    $("input:radio[name='searchRadio']").click(function() {
       var value = $(this).val();
       if (value==1) {
        //$('#fiscalYear').prop('disabled', false);
        //$( "#dateFrom" ).datepicker( "option", "disabled", true );
        //$( "#dateFrom" ).datepicker().attr('readonly','readonly');
    }
    if (value==2) {
        //$('#fiscalYear').prop('disabled', 'disabled');
        //$( "#dateFrom" ).datepicker( "option", "disabled", false );
    }
});
      //$("input:radio[name='searchRadio']").trigger('click');      

      /*End Active Deactive Search Option Base on Radio Button*/



  });/*End Doc Ready*/
</script>

{{-- Filtering Mehod --}}
<script type="text/javascript">
  $(document).ready(function() {

      $("#searchMethod").change(function(event) {

          var searchMethod = $(this).val();
          if (searchMethod=="") {
            $("#fiscalYearDiv").hide();
            $("#dateRangeDiv").hide();
        }
              //Fiscal Year
              else if(searchMethod==1){
                $("#fiscalYearDiv").show();
                $("#dateRangeDiv").hide();
            }

              //Current Year
              else if(searchMethod==2){
                $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").show();
                var d = new Date();
                var year = d.getFullYear();
                var month = d.getMonth();
                if (month<=5) {
                    year--;
                    month = 6;
                }
                else{
                    month = 6;
                }
                d.setFullYear(year, month, 1); 

                $("#dateFrom").datepicker("option","minDate",new Date(d));
                $("#dateFrom").datepicker("option","maxDate",new Date(d));
                $("#dateFrom").datepicker("setDate", new Date(d));           
                $("#dateTo").datepicker("option","minDate",new Date(d));
            }

              //Date Range
              else if(searchMethod==3){
                $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").show();

                $("#dateFrom").datepicker("option","minDate",new Date(Date.parse("1998-01-01")));
            }
        });
      $("#searchMethod").trigger('change');
  });
</script>
{{-- End Filtering Mehod --}}

<script type="text/javascript">
    $(document).ready(function() {
        //$('body').width( "3000" );
    });
</script>

{{-- Print Page --}}
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#print").click(function(event) {

            $("#hiddenTitle").show();
            $("#hiddenInfo").show();
            $("#famsAssetScheduleReportTable").removeClass('table table-striped table-bordered');

            var mainContents = document.getElementById("printingContent").innerHTML;
            var headerContents = '';

            var footerContents = "<div class='row' style='font-size:12px;'>Prepared By <span style='display:inline-block; width: 40%;'></span> Checked By <span style='display:inline-block; width: 40%;'></span> Approved By</div>"; 




            var printStyle = '<style>#famsAssetScheduleReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt ash;page-break-inside:auto;}  thead tr th{text-align:center;vertical-align: top;padding:3px;font-size:11px} #famsAssetScheduleReportTable thead tr th:nth-child(1){ width: 50px;} thead tr th:nth-child(10){ width: 40px;} tbody tr td:nth-child(10){ width: 40px;} tbody tr td {border: 1px solid ash;text-align:right;vertical-align: middle;padding:3px;font-size:11px} td:nth-child(1){text-align:left;} tr{ page-break-inside:avoid; page-break-after:auto } tr:last-child { font-weight: bold;} .name{text-align:left;vertical-align:left;}</style><style media="print">@page{size:landscape;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{#famsAssetScheduleReportTable tr:nth-of-type(10n){page-break-after: always;}}</style>';

            printContents = '<div id="order-details-wrapper">' + printStyle + mainContents + footerContents+'</div>';

            /*tr:nth-of-type(10n){page-break-after: always;}*/
       // @media print {.element-that-contains-table { overflow: visible !important; }}


  /*document.body.innerHTML = printStyle + printContents;
  window.print();*/

  var win = window.open('','printwindow');
  win.document.write(printContents);
  win.print();
  win.close();
});
    });
</script>
{{-- EndPrint Page --}}

{{-- Filtering --}}
<script type="text/javascript">
    jQuery(document).ready(function($) {
     function pad (str, max) {
      str = str.toString();
      return str.length < max ? pad("0" + str, max) : str;
  }
  /* Change Project*/
  $("#searchProject").change(function(){
    var projectId = $(this).val();

    var csrf = "<?php echo csrf_token(); ?>";

    $.ajax({
        type: 'post',
        url: './famsAddProductOnChangeProject',
        data: {projectId:projectId,_token: csrf},
        dataType: 'json',
        success: function( data ){                    

            $("#searchProjectType").empty();
            $("#searchProjectType").prepend('<option selected="selected" value="">All</option>');

            $("#searchBranch").empty();
            $("#searchBranch").prepend('<option value="0">All Branches</option>');
            $("#searchBranch").prepend('<option selected="selected" value="">All</option>');


            $.each(data['projectTypeList'], function (key, projectObj) {

                $('#searchProjectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");
            });

            $.each(data['branchList'], function (key, branchObj) {

                $('#searchBranch').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
            });

        },
        error: function(_response){
            alert("error");
        }

    });/*End Ajax*/

});/*End Change Project*/


  /* Change Project Type*/
  $("#searchProjectType").change(function(){
    var projectId = $("#searchProject").val();
    var projectTypeId = $(this).val();


    var csrf = "<?php echo csrf_token(); ?>";

    $.ajax({
        type: 'post',
        url: './famsAddProductOnChangeProjectType',
        data: {projectId:projectId,projectTypeId: projectTypeId,_token: csrf},
        dataType: 'json',
        success: function( data ){ 

         $("#searchBranch").empty();
         $("#searchBranch").prepend('<option value="0">All Branches</option>');
         $("#searchBranch").prepend('<option selected="selected" value="">All</option>');


         $.each(data['branchList'], function (key, branchObj) {

            $('#searchBranch').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
        });

     },
     error: function(_response){
        alert("error");
    }

});/*End Ajax*/

});/*End Change Project Type*/
});
</script>
{{-- End Filtering --}}

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#search").click(function(event) {


            if ($("#searchMethod").val()==2 || $("#searchMethod").val()==3) {

                if ($("#dateFrom").val()=="") {
                    event.preventDefault();
                    $("#dateFrome").show();
                }
                if ($("#dateTo").val()=="") {
                    event.preventDefault();
                    $("#dateToe").show();
                }

            }
            
            
        });
    });
</script>




@include('dataTableScript')


<style type="text/css">

#filtering-group input{
    height: auto;

    border-radius: 0px;
}

#filtering-group select{height:auto; border-radius: 0px;}

.row-name{text-align: left;padding-left: 15px;}
.row-amount{text-align: right;padding-right: 15px;}

#famsAssetScheduleReportTable tbody tr td{text-align: right;padding-right: 5px;}
</style>

<style type="text/css">
.table thead tr th {
  border: 1px solid white;
  border-bottom: 1px solid red;
  border-collapse: separate;
  _background-color: transparent!important;
  border-bottom: 0 !important;
  position: static !important;
}
</style>




@endsection