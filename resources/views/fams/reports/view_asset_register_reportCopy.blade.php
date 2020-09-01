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
                            <a href="" class="btn btn-info pull-right addViewBtn" id="print"><i class="fa fa-print" aria-hidden="true"></i> Print</a>
                        </div>
                        
                        <div class="row" id="filtering-group">
                            
                                <div class="form-horizontal form-groups" style="padding-right: 0px;">

                                    {!! Form::open(['url' => 'famsAssetRegisterReport','method' => 'get']) !!}
                                    @php
                                        $userBranchId = Auth::user()->branchId;
                                    @endphp

                                    @if($userBranchId==1)
                                    <div class="col-md-1">
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
                                    <div class="col-md-1">
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
                                    <div class="col-md-1">
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


                                    <div class="col-md-1">
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

                                    <div class="col-md-1">
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

                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Search By:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                {!! Form::select('searchMethod',[''=>'Please Select','1'=>'Fiscal Year','2'=>'Current Year','3'=>'Date Range'],$searchMethodSelected,['id'=>'searchMethod','class'=>'form-control input-sm']) !!}

                                            </div>
                                        </div>
                                    </div>
                                    

                                    <div class="col-md-1" style="display: none;" id="fiscalYearDiv">
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
                                                    <div class="col-sm-6">
                                                        {!! Form::text('dateFrom',$dateFromSelected,['id'=>'dateFrom','placeholder'=>'From','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                                        <p id="dateFrome" style="color: red;display: none;">*Required</p>
                                                    </div>
                                                    <div class="col-sm-6" id="dateToDiv">
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


                        <h2 align="center" style="font-family: Antiqua;letter-spacing: 2px; color: white; margin-top: 0px;">Fixed Assets Register Report</h2>
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
                                <div id="printingContent">

                                   <div style="display: none;text-align: center;" id="hiddenTitle">
                           <h3 style="text-align: center;padding: 0px;margin: 0px;">Ambala Foundation</h3>
                           <p style="text-align: center;padding: 0px;margin: 0px;font-size: 10px;">House # 62, Block # Ka, Piciculture Housing Society, Shyamoli, Dhaka-1207</p>
                           <h4 style="text-align: center;padding: 0px;margin: 0px;">Fixed Assets Register</h4>                          
                            {{-- <h5 style="text-align: center;">{{$selectedBranchName}}</h5>  --}}
                           <h5 style="text-align: center;font-size: 14;padding: 0px;margin: 0px;">{{date('F d, Y',strtotime($endDate))}}</h5>
                        </div> 

                                         <div id="hiddenInfo" style="display: none;">                       

                       <p style="padding: 0px;margin: 0px;font-size: 11px;">Branch Name : @php
                            if($selectedBranchName==null){
                                echo "All";
                            }
                            else{
                                echo $selectedBranchName;
                            }
                        @endphp                               
                         <span style='float: right;'>
                             Reporting Peroid : {{date('d-m-Y',strtotime($startDate))." to ".date('d-m-Y',strtotime($endDate))}}
                         </span>      
                        </p>                           
                       
                                                           
                                    <p style="padding: 0px;margin: 0px;font-size: 11px;">Project Name : @php
                                        if($selectedProjectName==null){
                                            echo "All";
                                        }
                                        else{
                                            echo $selectedProjectName;
                                        }
                                    @endphp                               
                                <span style='float: right;'>
                                    Assets Category :  @php
                                        if($selectedCategoryName==null){
                                            echo "All";
                                        }
                                        else{
                                            echo $selectedCategoryName;
                                        }
                                    @endphp
                                </span>                                
                                    </p>                                
                            

                                                          
                                    <p style="padding: 0px;margin: 0px;font-size: 11px;">Project Type :  @php
                                        if($selectedProjectTypeName==null){
                                            echo "All";
                                        }
                                        else{
                                            echo $selectedProjectTypeName;
                                        }
                                    @endphp                               
                                <span style='float: right;'>
                                    Print Date : {{date('F d,Y')}}
                                </span>  
                                
                                    </p>                                
                            
                        </div>
                        <br>

                                    <div id="tableDiv">
                                    <table id="famsAssetRegisterReportTable" class="table table-striped table-bordered" style="color:black;font-size:11px;margin-bottom:50px;border-collapse: collapse;" border= "1px solid ash;" cellpadding="0" cellspacing="0">
                                    
                                        <thead>
                                        <tr>
                                            <th rowspan="3" width="50">SL#</th>
                                            <th rowspan="3" width="78">Purchase Date</th>
                                            <th rowspan="3" width="100">Product Name</th>
                                            <th rowspan="3" width="200">Product ID Number</th>
                                            <th rowspan="3" width="70">Branch</th>
                                            <th colspan="8" width="1000" style="border-bottom: 0pt solid white;">Cost Value (Tk)</th>
                                            <th colspan="8" style="border-bottom: 0pt;">Accmulated Depreciation (Tk)</th>
                                            <th rowspan="3" width="100">Written Down Value</th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2" width="80">Opening Balance</th>
                                            <th colspan="3" style="border-bottom: 0pt;">Curr. Period Add</th>
                                            <th colspan="3" style="border-bottom: 0pt;">Curr. Period Less</th>
                                            <th rowspan="2">Closing Balance</th>
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
                                            $index = 0;

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
                                            //echo var_dump($branches);
                                        @endphp
                                        @foreach($branchIds as $branchObj)

                                        {{-- Get the Produts belongs to this branch --}}
                                        @php
                                            $transferedAllProduct = DB::table('fams_tra_transfer as t1')
                                                                    ->join('fams_product as t2','t1.productId','=','t2.id')
                                                                    ->where('t1.branchIdFrom',$branchObj)
                                                                    ->where('t2.purchaseDate','<=',$endDate)
                                                                    ->whereIn('t2.categoryId',$categoryId)
                                                                    ->whereIn('t2.productTypeId',$productTypeId)
                                                                    ->whereIn('t2.projectId',$projectId)
                                                                    ->whereIn('t2.projectTypeId',$projectTypeId)
                                                                    ->pluck('productId');                                                                    
                                                                    
                                            $productsOfthisBranch = DB::table('fams_product')->where('branchId',$branchObj)->where('purchaseDate','<=',$endDate)->whereIn('projectId',$projectId)->whereIn('projectTypeId',$projectTypeId)->whereIn('categoryId',$categoryId)->whereIn('productTypeId',$productTypeId)->orWhereIn('id',$transferedAllProduct)->orderBy('purchaseDate','asc')->get();


                                        @endphp

                                        @foreach($productsOfthisBranch as $product)

                                        @php
                                        
                                            $transferedOutProduct = DB::table('fams_tra_transfer')->where('productId',$product->id)->where('branchIdFrom',$branchObj)->where('transferDate','>=',$startDate)->get();

                                            $transferedInProduct = DB::table('fams_tra_transfer')->where('productId',$product->id)->where('branchIdTo',$branchObj)->where('transferDate','>',$endDate)->get();
                                        @endphp
                                        @if($branchObj==$product->branchId || sizeof($transferedOutProduct)>0)
                                        @if(sizeof($transferedInProduct)==0)
                                            @php
                                            $index++;
                                            $branchName = DB::table('gnr_branch')->where('id',$branchObj)->value('name');
                                            $purchaseDate = date('Y-m-d', strtotime($product->purchaseDate));

                                            //Get the Additional Charge After Start Date
                                            $postAddCharge = (float) DB::table('fams_additional_charge')->where('branchId',$branchObj)->where('productId',$product->id)->where('purchaseDate','>=',$startDate)->where('purchaseDate','<=',$endDate)->sum('amount');


                                            if($purchaseDate<$startDate){

                                              $additionalCharge = (float) DB::table('fams_additional_charge')->where('branchId',$branchObj)->where('productId',$product->id)->where('purchaseDate','<',$startDate)->sum('amount');

                                              $openingBalance = (float) $product->totalCost + $additionalCharge;
                                              $purchase = $postAddCharge;
                                            }
                                            else{
                                              $openingBalance = 0;

                                              $additionalCharge = (float) DB::table('fams_additional_charge')->where('productId',$product->id)->where('branchId',$branchObj)->where('purchaseDate','>=',$startDate)->where('purchaseDate','<=',$endDate)->sum('amount');
                                              $purchase = (float) $product->totalCost + $additionalCharge;
                                            }

                                            //If product is sold out
                                            $soldOut = DB::table('fams_sale')->where('productId',$product->id)->where('branchId',$branchObj)->where('createdDate','>=',$startDate)->where('createdDate','<=',$endDate)->first();
                                            if (sizeOf($soldOut)>0) {
                                              $saleAmount = $soldOut->amount;
                                              $adjustmentIn = $soldOut->profitAmount;
                                              //$adjustmentOut = Dep. Generated + Loss amount
                                              $adjustmentOut = round((float) $soldOut->lossAmount + (float) DB::table('fams_depreciation_details')->where('productId',$product->id)->sum('amount') + (float)DB::table('fams_product')->where('id',$product->id)->value('depreciationOpeningBalance'),2);

                                              //$depAdjustment = round(DB::table('fams_depreciation_details')->where('productId',$product->id)->where('depTo','>=',$startDate)->where('depTo','<=',$endDate)->sum('amount'),2);
                                              $depDisposalamount = 0;
                                            }
                                            else{
                                                
                                              $saleAmount = 0;
                                              $adjustmentIn = 0;
                                              $adjustmentOut = 0;
                                              //If product is write offed
                                              $writeOffed = DB::table('fams_write_off')->where('productId',$product->id)->where('branchId',$branchObj)->where('createdDate','>=',$startDate)->where('createdDate','<=',$endDate)->first();
                                              if (sizeOf($writeOffed)>0) {

                                                
                                                //$adjustmentOut = $writeOffed->lossAmount;
                                                $adjustmentOut = round((float) $additionalCharge + DB::table('fams_product')->where('id',$product->id)->value('totalCost'),2);

                                                $depDisposalamount = round((float) DB::table('fams_write_off')->where('productId',$product->id)->where('createdDate','>=',$startDate)->where('createdDate','<=',$endDate)->value('amount'),2);

                                                //$depAdjustment = round((float) $additionalCharge + DB::table('fams_product')->where('id',$product->id)->value('totalCost'),2);
                                              }
                                              else{


                                                /*Is writeoff before start date */
                                                $writeOffedBeforeStartDate = DB::table('fams_write_off')->where('productId',$product->id)->where('branchId',$branchObj)->where('createdDate','<',$startDate)->first();
                                                 if (sizeOf($writeOffedBeforeStartDate)>0) {
                                                    continue;
                                                 }
                                                /*End Is writeoff before start date */


                                                $adjustmentOut = 0;
                                                $depDisposalamount = 0;
                                                //$depAdjustment = 20;
                                                
                                              }
                                              
                                            }

                                            //If Transfered Out Then get old product Code and Tranfer Amount
                                            $isTransferdOutProduct = DB::table('fams_tra_transfer')->where('productId',$product->id)->where('branchIdFrom',$branchObj)->where('transferDate','<=',$endDate)->orderBy('id','desc')->first();
                                            if(sizeof($isTransferdOutProduct)>0){
                                              $productCode = $isTransferdOutProduct->oldProductCode;
                                              $transferOutAmount = (float) $product->totalCost + (float) DB::table('fams_additional_charge')->where('productId',$product->id)->where('purchaseDate','<',$isTransferdOutProduct->transferDate)->sum('amount');;

                                              
                                            }
                                            else{
                                              $productCode = $product->productCode;
                                              $transferOutAmount = 0;
                                              
                                            }

                                            //If product Transfered In Then get Tranfer Amount
                                            $isTransferdInProduct = DB::table('fams_tra_transfer')->where('productId',$product->id)->where('branchIdTo',$branchObj)->orderBy('id','desc')->first();
                                            if(sizeof($isTransferdInProduct)>0){                                              
                                              $transferInAmount = (float) $product->totalCost + (float) DB::table('fams_additional_charge')->where('productId',$product->id)->where('purchaseDate','<',$isTransferdInProduct->transferDate)->sum('amount');                                               

                                              $openingBalance = 0;
                                              $purchase = 0;
                                              
                                            }
                                            else{                                              
                                              $transferInAmount = 0;
                                              
                                            }                                            


                                            $closingBalance = round($openingBalance + $purchase + $adjustmentIn + $transferInAmount - $saleAmount - $adjustmentOut - $transferOutAmount,2);



                                            ///////    Dep   ///////

                                            $accDep = round($product->depreciationOpeningBalance + (float) DB::table('fams_depreciation_details')->where('productId',$product->id)->where('branchId',$branchObj)->where('depTo','<',$startDate)->sum('amount'),2);                                           

                                            $currentAccDep = round((float) DB::table('fams_depreciation_details')->where('productId',$product->id)->where('branchId',$branchObj)->where('depTo','>=',$startDate)->where('depTo','<=',$endDate)->sum('amount'),2);

                                            //If product is sold out
                                            $soldOut = DB::table('fams_sale')->where('productId',$product->id)->where('branchId',$branchObj)->where('createdDate','>=',$startDate)->where('createdDate','<=',$endDate)->first();
                                            if (sizeOf($soldOut)>0) {
                                                $depAdjustment = $accDep + $currentAccDep;
                                            }
                                            else{


                                                /*If sold out before start date*/
                                                $soldOutBeforeStartDate = DB::table('fams_sale')->where('productId',$product->id)->where('branchId',$branchObj)->where('createdDate','<',$startDate)->first();
                                                if (sizeOf($soldOutBeforeStartDate)>0) {
                                                    continue;
                                                }
                                                /*End If sold out before start date*/



                                                 //If product is write offed
                                              $writeOffed = DB::table('fams_write_off')->where('productId',$product->id)->where('branchId',$branchObj)->where('createdDate','>=',$startDate)->where('createdDate','<=',$endDate)->first();
                                              if (sizeOf($writeOffed)>0) {
                                                $depAdjustment = (float) $product->totalCost + (float) DB::table('fams_additional_charge')->where('productId',$product->id)->sum('amount');  
                                              }
                                              else{
                                                $depAdjustment = 0;
                                              }
                                            }
                                            
                                            //If product is Transfered In
                                             if(sizeof($isTransferdInProduct)>0){
                                                $accDep=0;
                                                $depTransferInAmount = (float) DB::table('fams_tra_transfer')->where('branchIdTo',$branchObj)->where('productId',$product->id)->where('transferDate','>=',$startDate)->where('transferDate','<=',$endDate)->value('pastDep');
                                            }
                                            else{
                                                $depTransferInAmount = 0;
                                            }

                                            //If product Is transfered Out
                                            if(sizeof($isTransferdOutProduct)>0){
                                                $depTransferOutAmount = $accDep + $currentAccDep;
                                            }
                                            else{
                                                $depTransferOutAmount = 0;
                                            }

                                            $depTransferAmount = $depTransferOutAmount + $depTransferInAmount;

                                            $depClosingBalance = round($accDep + $currentAccDep + $depDisposalamount + $depTransferInAmount - $depAdjustment - $depTransferOutAmount,2);

                                            @endphp

                                            <tr>
                                                <td>{{$index}}</td>
                                                <td>{{date('d-m-Y', strtotime($product->purchaseDate))}}</td>
                                                <td style="text-align: left;padding-left: 5px;">{{$product->name}}</td>
                                                <td >{{$prefix.$productCode}}</td>
                                                <td style="text-align: left;padding-left: 5px;">{{$branchName}}</td>

                                                <td style="text-align: right;padding-right: 5px;">
                                                  @if($openingBalance==0) {{""}} @else {{$openingBalance}} @endif
                                                  @php
                                                  $totalOpeningBalnace = $totalOpeningBalnace + $openingBalance;
                                                  @endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                @if($purchase==0) {{""}} @else {{$purchase}} @endif
                                                @php
                                                $totalPurchase = $totalPurchase + $purchase;
                                                @endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                @if($adjustmentIn==0) {{""}} @else {{$adjustmentIn}} @endif
                                                @php$totalAdjustmentIn = $totalAdjustmentIn + $adjustmentIn;@endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                @if($transferInAmount==0) {{""}} @else {{$transferInAmount}} @endif
                                                @php$totalTransferIn = $totalTransferIn + $transferInAmount;@endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                @if($saleAmount==0) {{""}} @else {{$saleAmount}} @endif
                                                @php $totalSale = $totalSale + $saleAmount; @endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                @if($adjustmentOut==0) {{""}} @else {{$adjustmentOut}} @endif
                                                @php $totalAdjustmentOut = $totalAdjustmentOut + $adjustmentOut; @endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                @if($transferOutAmount==0) {{""}} @else {{$transferOutAmount}} @endif
                                                @php $totalTransferOut = $totalTransferOut + $transferOutAmount; @endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                {{$closingBalance}}
                                                @php $totalClosing = $totalClosing + $closingBalance; @endphp
                                                </td>
                                                <td style="text-align: center;">{{$product->depreciationPercentage}}</td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                @if($accDep==0) {{""}} @else {{number_format($accDep,2)}} @endif
                                                @php $totalDepOpeningBalance = $totalDepOpeningBalance + $accDep; @endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                @if($currentAccDep==0) {{""}} @else {{number_format($currentAccDep,2)}} @endif
                                                @php $totalCurrentDep = $totalCurrentDep + $currentAccDep; @endphp
                                                </td>
                                                
                                                <td style="text-align: right;padding-right: 5px;">
                                                @if($depDisposalamount==0) {{""}} @else {{number_format($depDisposalamount,2)}} @endif
                                                @php $totalDisposalAmount = $totalDisposalAmount + $depDisposalamount; @endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                @if($depTransferInAmount==0) {{""}} @else {{number_format($depTransferInAmount,2)}} @endif
                                                @php $totalDepTransferIn = $totalDepTransferIn + $depTransferInAmount; @endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                @if($depTransferOutAmount==0) {{""}} @else {{number_format($depTransferOutAmount,2)}} @endif
                                                @php $totalDepTransferOut = $totalDepTransferOut + $depTransferOutAmount; @endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                @if($depAdjustment==0) {{""}} @else {{number_format($depAdjustment,2)}} @endif
                                                @php $totalDepAdjustment = $totalDepAdjustment + $depAdjustment; @endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                
                                                {{number_format($depClosingBalance,2)}}
                                                @php $totalDepClosingBalance = $totalDepClosingBalance + $depClosingBalance; @endphp
                                                </td>
                                                <td style="text-align: right;padding-right: 5px;">
                                                
                                                {{number_format($closingBalance - $depClosingBalance,2)}}
                                                @php $totalWrittenDown = $totalWrittenDown + $closingBalance - $depClosingBalance; @endphp
                                                </td>
                                            </tr>                                           

                                            
                                            
                                            @endif
                                            @endif
                                        @endforeach                                        
                                        @endforeach
                                        <tr class="totalRow">
                                                <td colspan="5" style="text-align: center;">Total</td>                                                
                                                <td style="text-align: right;padding-right: 5px;">{{$totalOpeningBalnace}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{$totalPurchase}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{$totalAdjustmentIn}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{$totalTransferIn}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{$totalSale}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{$totalAdjustmentOut}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{$totalTransferOut}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{$totalClosing}}</td>
                                                <td style="text-align: right;padding-right: 5px;"></td>
                                                <td style="text-align: right;padding-right: 5px;">{{number_format($totalDepOpeningBalance,2)}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{number_format($totalCurrentDep,2)}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{number_format($totalDisposalAmount,2)}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{number_format($totalDepTransferIn,2)}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{number_format($totalDepTransferOut,2)}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{number_format($totalDepAdjustment,2)}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{number_format($totalDepClosingBalance,2)}}</td>
                                                <td style="text-align: right;padding-right: 5px;">{{number_format($totalWrittenDown,2)}}</td>
                                            </tr>
                                        </tbody>

                                    </table>
                                    
                                    </div>
                                    </div>
                                @endif
                           
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>


<script type="text/javascript">
  $(document).ready(function() {

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
                $("#dateFrom").datepicker("setDate", new Date(d));
                $("#dateFrom").hide();
                $("#dateFrome").hide();

                $("#dateToDiv").attr("class", "col-sm-12");
                $("#dateTo").datepicker("option","minDate",new Date(d));
              }

              //Date Range
              else if(searchMethod==3){
                $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").show();

                $("#dateToDiv").attr("class", "col-sm-6");
                $("#dateFrom").show();
                //$("#dateFrom").val("");
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
            $("#famsAssetRegisterReportTable").removeClass('table table-striped table-bordered');

        var mainContents = document.getElementById("printingContent").innerHTML;
  var headerContents = '';

  var footerContents = "<div class='row' style='font-size:12px;'>Prepared By <span style='display:inline-block; width: 40%;'></span> Checked By <span style='display:inline-block; width: 40%;'></span> Approved By</div>";
  


        /*var printStyle = '<style>#famsAssetRegisterReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt ash;page-break-inside:auto;} th:nth-child(4){ width: 200px;} td:nth-child(4){ width: 200px;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px} tbody tr td {text-align:left;vertical-align: middle;padding:3px;font-size:10px} tr{ page-break-inside:avoid; page-break-after:auto } tr:last-child { font-weight: bold;} .name{text-align:left;vertical-align:left;}.amount{text-align:right;}</style><style media="print">@page{size:landscape;margin:20mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style>';*/

        var printStyle = '<style>#famsAssetRegisterReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt ash;page-break-inside:auto;} #famsAssetRegisterReportTable thead tr th:nth-child(4){ width: 200px;}  thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px}  tbody tr td {border: 1px solid ash;text-align:left;vertical-align: middle;padding:3px;font-size:10px} tr{ page-break-inside:avoid; page-break-after:auto } tr:last-child { font-weight: bold;} .name{text-align:left;vertical-align:left;}.amount{text-align:right;}</style><style media="print">@page{size:landscape;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{#famsAssetRegisterReportTable tr:nth-of-type(10n){page-break-after: always;}}</style>';

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



          /* Change Category*/
         $("#searchCategory").change(function(){

            
            var categoryId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsFixedAssetsDepReportOnChngeCategory',
                data: {categoryId:categoryId,_token: csrf},
                dataType: 'json',
                success: function( data ){


                    $("#searchProductType").empty();
                    $("#searchProductType").prepend('<option selected="selected" value="">All</option>');
                   

                    $.each(data['productTypeList'], function (key, productObj) {                       
                             
                                
                            $('#searchProductType').append("<option value='"+ productObj.id+"'>"+pad(productObj.productTypeCode,3)+"-"+productObj.name+"</option>");
                        
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Category*/



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


    </style>

   <style type="text/css">
       #tableDiv
        {
            width:100%;
            height:60vh;            
            overflow-y: scroll;
            
        }
   </style>

   <style type="text/css">
       #famsAssetRegisterReportTable thead tr th {
          border: 1px solid white;
          border-bottom: 1px solid red;
          border-collapse: separate;
          _background-color: transparent!important;
          border-bottom: 0 !important;
          position: static !important;
       }
   </style>


@endsection