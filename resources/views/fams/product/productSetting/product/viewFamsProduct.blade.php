@extends('layouts/fams_layout')
@section('title', '| Product')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="" style="">
                <div class="">
                    <div class="panel panel-default" style="background-color:#708090;">
                        <div class="panel-heading" style="padding-bottom:0px">
                            <div class="panel-options">
                                <a href="{{url('addFamsProduct/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Product</a>
                            </div>
                            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PRODUCT LIST</font></h1>

                        </div>


                        <div class="row">
                        <div class="col-md-11">
                        <div class="form-horizontal form-groups" id="filtering-group">

                            {!! Form::open(['url' => 'viewFamsProduct','method' => 'get']) !!}


                            <div class="row" id="filtering-group">
                            
                                <div class="form-horizontal form-groups" style="padding-right: 0px;">

                                    {!! Form::open(['url' => 'famsFixedAssetsPurchaseReport','method' => 'get']) !!}
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
                                        <div class="form-group" style="font-size: 13px; color:black">

                                            <div class="col-sm-12" style="padding-top: 25px;">

                                                {!! Form::submit('Search',['id'=>'search','class'=>'btn btn-primary btn-xs','style'=>'font-size:15px;']) !!}
                                            </div>
                                        </div>
                                        
                                    </div>                                    

                                </div>                            
                            
                        </div> {{-- End Filtering Group --}}

                        {{-- <div class="col-md-2">
                                <div class="form-group" style="font-size: 13px; color:black">
                                    {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                     @php
                                        $projects = DB::table('gnr_project')->select('name','id','projectCode')->get();
                                    @endphp
                                    
                                    <select id="filterProjectId" name="filterProjectId" class="form-control">
                                    <option value="">All</option>
                                    @foreach($projects as $project)
                                        <option value="{{$project->id}}" @if($project->id==$projectSelected) {{"selected=selected"}} @endif>{{str_pad($project->projectCode, 3, "0", STR_PAD_LEFT ).'-'.$project->name}}</option>
                                    @endforeach
                                    </select>
                                        
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-2">
                                <div class="form-group" style="font-size: 13px; color:black">
                                    {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                     @php
                                        $branches = DB::table('gnr_branch')->get();
                                    @endphp


                                    <select name="filterbranchId" id="filterbranchId" class="form-control">
                                        <option value="">All</option>
                                        <option value="0" @if($branchSelected===0) {{"selected=selected"}} @endif>All Branch</option>
                                        @foreach($branches as $branch)                                        
                                        
                                        <option value="{{$branch->id}}" @if($branch->id==$branchSelected) {{"selected=selected"}} @endif >{{str_pad($branch->branchCode, 3, "0", STR_PAD_LEFT ).'-'.$branch->name}}</option>
                                        
                                        @endforeach
                                    </select>
                                    
                                        
                                    </div>
                                </div>
                        </div> --}}
                        {{-- <div class="col-md-2">
                            <div class="form-group" style="font-size: 13px; color:black">
                                
                                 <div class="col-sm-12" style="padding-top: 25px;">

                                    {!! Form::submit('Search',['class'=>'btn btn-primary btn-xs','style'=>'font-size:15px;']) !!}
                                 </div>                        
                            </div>                        
                        </div> --}}

                        {!! Form::close() !!}
                            
                        </div>
                        </div>
                        </div>                         

                        <div class="panel-body panelBodyView">                          
                            


                            <table class="table table-striped table-bordered" id="famsProductTable" style="color:black;">
                                <thead>
                                <tr>
                                    <th width="55">SL#</th>
                                    <th width="100">Purchase Date</th>
                                    <th width="200">Product Name</th>
                                    <th width="200">Product ID Number</th>
                                    <th width="200">Branch Location</th>
                                    <th width="100">Project</th>
                                    <th width="150">Project Type</th>
                                    <th >Dep. Opening Balance</th>                                    
                                    <th >Dep. Rate (%)</th>
                                    <th >Cost Price</th>
                                    <th id="details" width="80" style="pointer-events:none;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($_GET['page'])) {
                                    $pagebumber = (int)$_GET['page'] ;
                                }
                                else{
                                    $pagebumber=1;
                                }
                                    
                                    $no= ($pagebumber-1)*20; 
                                ?>
                                @foreach($products as $product)
                                    <tr class="item{{$product->id}}">
                                        <td class="text-center slNo">{{++$no}}</td>
                                        <td width="100" style="padding-left: 2px;padding-right: 2px;">{{(date('d-m-Y', strtotime($product->purchaseDate)))}}</td>
                                        <td style="text-align: left; padding-left: 5px;">
                                            {{$product->name}}
                                        </td>
                                        <td>
                                            {{$prefix.$product->productCode}}
                                        </td>
                                        <td style="text-align: left; padding-left: 5px;">
                                         @php
                                            $BranchName = DB::table('gnr_branch')->where('id',$product->branchId)->value('name');
                                        @endphp
                                           {{$BranchName}}
                                        </td>
                                        <td style="text-align: left;padding-left: 5px;">
                                        @php
                                            $projectName = DB::table('gnr_project')->where('id',$product->projectId)->value('name');
                                        @endphp
                                            {{$projectName}}
                                        </td>
                                        <td style="text-align: left;padding-left: 5px;">
                                        @php
                                            $projectTypeName = DB::table('gnr_project_type')->where('id',$product->projectTypeId)->value('name');
                                        @endphp
                                            {{$projectTypeName}}
                                        </td>
                                        <td style="text-align: right; padding-right: 5px;">
                                            {{number_format($product->depreciationOpeningBalance,2)}}
                                        </td>

                                       
                                        <td width="75">
                                            {{$product->depreciationPercentage}}
                                        </td>
                                        
                                        @php
                                            $additionalCharge = DB::table('fams_additional_charge')->where('productId',$product->id)->sum('amount');
                                            if(!$additionalCharge){
                                                $additionalCharge = 0;
                                            }
                                            $grandTotalCost = $product->totalCost+$additionalCharge;
                                        @endphp
                                        <td style="text-align: right; padding-right: 15px;" width="60">
                                            {{$grandTotalCost}}
                                        </td>
                                        

                                        <td class="text-center" width="50">

                                        @php                                          
                                            

                                            $isdep = (int) DB::table('fams_depreciation_details')->where('productId',$product->id)->value('id');
                                            $isSold = (int) DB::table('fams_sale')->where('productId',$product->id)->value('id');
                                            $isWriteOffed = (int) DB::table('fams_write_off')->where('productId',$product->id)->value('id');
                                            $isTransfered = (int) DB::table('fams_tra_transfer')->where('productId',$product->id)->value('id');
                                            
                                            $isEditable = max($isdep,$isSold,$isWriteOffed,$isTransfered);

                                        @endphp
                                          

                                            <a href="javascript:;" class="view-modal" productId="{{$product->id}}" >
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                              </a>&nbsp; 
                                              <a href="javascript:;" class="edit-modal" productId="{{$product->id}}"  isEditable="{{$isEditable}}">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>&nbsp;

                                            <a href="javascript:;" class="delete-modal" productId="{{$product->id}}" isEditable="{{$isEditable}}" @php if($isEditable>0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                            

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                            <div style="text-align:right;">
                                {{ $products->appends(['searchProject'=>$projectSelected,'searchProjectType'=>$projectTypeSelected,'searchBranch'=>$branchSelected,'searchCategory'=>$categorySelected,'searchProductType'=>$productTypeSelected])->links() }}
                            </div>
                            

                        </div>{{--End panel-body panelBodyView--}}
                    </div>{{--End panel panel-default--}}
                </div>
            </div>
        </div>
    </div>  



 {{-- View Modal --}}
        <div id="viewModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Product Details</h4>
                    </div>
                    <div class="modal-body">

                        <h3> <p style="color: black;" id="viewModalProductName">Product Name: </p></h3><br>

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                        <div class="form-group">
                                            {!! Form::label('supplierId', 'Supplier:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('supplierName', null,['id'=>'viewModalSupplierName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('group', 'Group:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('group', null,['id'=>'viewModalGroupName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('category', 'Category:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('category', null,['id'=>'viewModalCategoryName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('subCategory', 'Sub Category:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                                
                                                {!! Form::text('subCategory', null,['id'=>'viewModalSubCategoryName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('productType', 'Product Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('productType', null,['id'=>'viewModalProductTypeName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('brand', 'Brand:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('brand', null,['id'=>'viewModalBrandName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('model', 'Model:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('model', null,['id'=>'viewModalModelName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('size', 'Size:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                               
                                                {!! Form::text('size', null,['id'=>'viewModalSizeName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('color', 'Color:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                
                                                {!! Form::text('color', null,['id'=>'viewModalColorName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('uom', 'UOM:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                
                                                {!! Form::text('uom', null,['id'=>'viewModalUOMName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            {!! Form::label('productCode', 'Product Code:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::textarea('productName', null,['class'=>'form-control','id'=>'viewModalProductCode','rows' => 2,'autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>



                                    </div>{{--form-horizontal form-groups--}}
                                </div>{{--End 1st col-md-6--}}

                                <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                        <div class="form-group">
                                            {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('branch',null,['id'=>'viewModalBranchName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('project', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                                
                                                {!! Form::text('project', null,['id'=>'viewModalProjectName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                                
                                                {!! Form::text('projectType', null,['id'=>'viewModalProjectTypeName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('purchaseDate', 'Purchase Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('purchaseDate', null,['id'=>'viewModalPurchaseDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('warranty', 'Warranty(Years):', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('warranty', null,['id'=>'viewModalWarranty','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('warrantyExpireDate', 'Warranty Expire Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('warrantyExpireDate', null,['id'=>'viewModalWarrantyExpireDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('serviceWarranty', 'Service Warranty(Years):', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('serviceWarranty', null,['id'=>'viewModalServiceWarranty','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('serviceWarrantyExpireDate', 'Service Warranty Expire Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('serviceWarrantyExpireDate', null,['id'=>'viewModalServiceWarrantyExpireDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('description', 'Description:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::textarea('description', null,['id'=>'viewModalDescription','class'=>'form-control','type' => 'text','autocomplete'=>'off','rows'=>3,'readonly']) !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>{{--End 2nd col-md-6--}}
                            </div>

                        </div>{{--row--}}
                        <div style="border-style: solid;border-width: 1px;"></div><br>
                        <div class="row" style="padding-bottom: 20px;">{{--2nd Row--}}
                            <div class="form-horizontal form-groups">
                                <div class="col-md-12">
                                    <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                        <div class="form-horizontal form-groups">

                                            <div class="form-group">
                                                {!! Form::label('costPrice', 'Cost Price:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                                    {!! Form::text('costPrice', null,['id'=>'viewModalCostPrice','class'=>'form-control','autocomplete'=>'off','readonly']) !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('additionalCost1', 'Additional Cost:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                                    {!! Form::text('additionalCost1', null,['id'=>'viewModalAddCost','class'=>'form-control','autocomplete'=>'off','readonly']) !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('additionalCost2', 'VAT & Tax:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                                    {!! Form::text('additionalCost2', null,['id'=>'viewModalTaxVat','class'=>'form-control','autocomplete'=>'off','readonly']) !!}
                                                </div>
                                            </div>                                            
                                           
                                                <div class="form-group">
                                                {!! Form::label('additionalCharge', 'Additional Charge:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                                    {!! Form::text('additionalCharge',null,['id'=>'viewModalAddCharge','class'=>'form-control','autocomplete'=>'off','readonly']) !!}
                                                </div>
                                            </div>                                            

                                            <div class="form-group">
                                                {!! Form::label('totalCost', 'Total Cost:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                                    {!! Form::text('totalCost', null,['id'=>'viewModalTotalCost','class'=>'form-control','autocomplete'=>'off','readonly']) !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('usefulLife', 'Useful Life:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                                    {!! Form::text('usefulLife', null,['id'=>'viewModalUsefulLife','class'=>'form-control','autocomplete'=>'off','readonly']) !!}
                                                </div>
                                            </div>


                                        </div>
                                    </div>{{--End 1st col-md-6--}}

                                    <div class="col-md-6" style="padding-left:2%;">
                                        <div class="form-group">
                                            {!! Form::label('resellValue', 'Resale Value:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('resellValue',null,['id'=>'viewModalResaleValue','class'=>'form-control','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('depreciationOpeningBalance', 'Dep. Opening Balance:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('depreciationOpeningBalance', null,['id'=>'viewModalDepOpeningBal','class'=>'form-control','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('depreciationAmountPerYear', 'Dep. Cost Per Year:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('depreciationAmountPerYear', null,['id'=>'viewModalDepPerYear','class'=>'form-control','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        

                                        <div class="form-group">
                                            {!! Form::label('depreciationPercentage', 'Dep. Percentage:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('depreciationPercentage', null,['id'=>'viewModalDepPercentage','class'=>'form-control','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                    </div>{{--End 2nd col-md--}}

                                </div>
                            </div>
                        </div>{{--End 2nd Row--}}

                        <div style="border-style: solid;border-width: 1px;"></div><br>

                        <div class="row" style="padding-bottom: 20px;">{{--3rd Row--}}

                            <div class="form-horizontal form-groups">
                                <div class="col-md-12">
                                    <div class="col-md-12" style="padding-right:2%;">{{--1st col-md-6--}}                                       

                                            <div class="col-sm-3">
                                                <h3>Product Image</h3>
                                                <img src="" id="viewModalProductImage" alt="Product Image" height="200" width="200">
                                            </div>                                       

                                            <div class="col-sm-3">
                                                <h3 id="viewModalWarrantyCardImageTitle">Warranty Card Image</h3>
                                                <img src="" id="viewModalWarrantyCardImage" alt="Warranty Card Image" height="200" width="200">
                                            </div>                                        

                                            <div class="col-sm-3">
                                                <h3>Bill Image</h3>
                                                <img src="" id="viewModalBillImage" alt="Bill Image" height="200" width="200">
                                            </div>

                                            <div class="col-sm-3">
                                                <h3>Additional Voucher Image</h3>
                                                <img src="" id="viewModalAddVoucherImage" alt="Additional Voucher Image" height="200" width="200">
                                            </div>
                                       

                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- View ModalFooter--}}
                        <div class="modal-footer">
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span id=""> Close</span></button>
                        </div>


                    </div> {{-- End View Modal Body--}}

                </div>
            </div>
        </div>
        {{-- End View Modal --}}




        {{-- Edit Modal --}}
        <div id="editModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Edit Product</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}

                                    {!! Form::open(['url' => 'editFamsProduct','enctype' => 'multipart/form-data','id'=>'editModalForm']) !!}
                                    {!! Form::hidden('productId',null,['id'=>'editModalProductId']) !!}

                                    {!! Form::hidden('projectSelected',$projectSelected) !!}
                                    {!! Form::hidden('projectTypeSelected',$projectTypeSelected) !!}
                                    {!! Form::hidden('branchSelected',$branchSelected) !!}
                                    {!! Form::hidden('categorySelected',$categorySelected) !!}
                                    {!! Form::hidden('productTypeSelected',$productTypeSelected) !!}
                                    {!! Form::hidden('page',$currentPage) !!}

                                    <div class="form-horizontal form-groups">

                                        <div class="form-group">
                                            {!! Form::label('editModalProductName', 'Product Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $productName = array('' => 'Please Select Product Name') + DB::table('fams_product_name')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('editModalProductName', ($productName), null, array('class'=>'form-control', 'id' => 'editModalProductName')) !!}                                                
                                                <p id="editModalProductNamee" style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">

                                            {!! Form::label('editModalSupplierId', 'Supplier:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_supplier = array('' => 'Please Select Supplier') + DB::table('gnr_supplier')->pluck('supplierCompanyName','id')->all();
                                                ?>
                                                {!! Form::select('editModalSupplierId', ($edit_modal_supplier), null, array('class'=>'form-control', 'id' => 'editModalSupplierId')) !!}
                                                    <p id="editModalSupplierIde" style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalGroup', 'Group:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_group = array('' => 'Please Select Group') + DB::table('fams_product_group')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('editModalGroup', ($edit_modal_group), null, array('class'=>'form-control', 'id' => 'editModalGroup')) !!}
                                                    <p id="editModalGroupe" style="max-height:3px;"></p>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalCategory', 'Category:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_category = array('' => 'Please Select Category') + DB::table('fams_product_category')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('editModalCategory', ($edit_modal_category), null, array('class'=>'form-control', 'id' => 'editModalCategory')) !!}
                                                    <p id="editModalCategorye" style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalSubCategory', 'Sub Category:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_subCategory = array('' => 'Please Select Sub Category') + DB::table('fams_product_sub_category')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('editModalSubCategory', ($edit_modal_subCategory), null, array('class'=>'form-control', 'id' => 'editModalSubCategory')) !!}
                                                    <p id="editModalSubCategorye" style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalProductType', 'Product Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_ProductType = array('' => 'Please Select Product Type') + DB::table('fams_product_type')->pluck('name','id')->all();
                                                ?>
                                                
                                                {!! Form::select('editModalProductType', ($edit_modal_ProductType), null, array('class'=>'form-control', 'id' => 'editModalProductType')) !!}
                                                    <p id="editModalProductTypee" style="max-height:3px;"></p>

                                                {!! Form::hidden('EMinitialProductType',null,['id'=>'EMinitialProductType']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalBrand', 'Brand:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_brand = array('' => 'Please Select Category') + DB::table('fams_product_brand')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('editModalBrand', ($edit_modal_brand), null, array('class'=>'form-control', 'id' => 'editModalBrand')) !!}
                                                    <p id="editModalBrande" style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalModel', 'Model:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_model = array('' => 'Please Select Model') + DB::table('fams_product_model')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('editModalModel', ($edit_modal_model), null, array('class'=>'form-control', 'id' => 'editModalModel')) !!}
                                                    <p id="editModalModele" style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalSize', 'Size:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_size = array('' => 'Please Select Size') + DB::table('fams_product_size')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('editModalSize', ($edit_modal_size), null, array('class'=>'form-control', 'id' => 'editModalSize')) !!}
                                                    <p id="editModalSizee" style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalColor', 'Color:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_color = array('' => 'Please Select Color') + DB::table('fams_product_color')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('editModalColor', ($edit_modal_color), null, array('class'=>'form-control', 'id' => 'editModalColor')) !!}
                                                    <p id="editModalColore" style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalUOM', 'UOM:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_uom = array('' => 'Please Select UOM') + DB::table('fams_product_uom')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('editModalUOM', ($edit_modal_uom), null, array('class'=>'form-control', 'id' => 'editModalUOM')) !!}
                                                    <p id="editModalUOMe" style="max-height:3px;"></p>
                                            </div>
                                        </div>



                                    </div>{{--form-horizontal form-groups--}}

                                </div>{{--End 1st col-md-6--}}

                                <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                        <div class="form-group">
                                            {!! Form::label('editModalBranch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_branch = array('' => 'Please Select Branch') + DB::table('gnr_branch')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('editModalBranch', ($edit_modal_branch), null, array('class'=>'form-control', 'id' => 'editModalBranch')) !!}
                                                    <p id="editModalBranche" style="max-height:3px;"></p>

                                                    {!! Form::hidden('EMinitialBranch',null,['id'=>'EMinitialBranch']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalProject', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_project = array('' => 'Please Select Project') + DB::table('gnr_project')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('editModalProject', ($edit_modal_project), null, array('class'=>'form-control', 'id' => 'editModalProject')) !!}
                                                    <p id="editModalProjecte" style="max-height:3px;"></p>

                                                {!! Form::hidden('EMinitialProject',null,['id'=>'EMinitialProject']) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('editModalProjectType', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $edit_modal_project_type = array('' => 'Please Select Project') + DB::table('gnr_project_type')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('editModalProjectType', ($edit_modal_project_type), null, array('class'=>'form-control', 'id' => 'editModalProjectType')) !!}
                                                    <p id="editModalProjectTypee" style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalPurchaseDate', 'Purchase Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('editModalPurchaseDate', null,['class'=>'form-control','id' => 'editModalPurchaseDate','autocomplete'=>'off']) !!}
                                                <p id="editModalPurchaseDatee" style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalWarrantyYear', 'Warranty:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                <div class="col-sm-3" style="padding-left:0px;">
                                                    {!! Form::text('editModalWarrantyYear', null,['class'=>'form-control editModalWarrantyYear','id' => 'editModalWarrantyYear','placeholder' => 'YY','autocomplete'=>'off']) !!}
                                                </div>
                                                <div class="col-sm-3" style="padding-left:0px;">
                                                    {!! Form::text('editModalWarrantyMonth', null,['class'=>'form-control editModalWarrantyYear','id' => 'editModalWarrantyMonth','placeholder' => 'MM','autocomplete'=>'off']) !!}
                                                </div>
                                                <div class="col-sm-6" style="padding-right:0px;">
                                                    {!! Form::text('editModalWarrantyExpireDate', null,['class'=>'form-control editModalWarrantyYear','id' => 'editModalWarrantyExpireDate','placeholder' => 'Expire Date','autocomplete'=>'off','readonly']) !!}
                                                    <p id="editModalWarrantye" style="max-height:3px;"></p>
                                                </div>


                                            </div>
                                        </div>

                                        <!-- <div class="form-group">
                                    {!! Form::label('warrantyExpireDate', 'Warranty Expire Date:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                                    {!! Form::text('warrantyExpireDate', null,['class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                                </div>
                                            </div> -->

                                        <div class="form-group">
                                            {!! Form::label('serviceWarranty', 'Service Warranty:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">


                                                <div class="col-sm-3" style="padding-left:0px;">
                                                    {!! Form::text('editModalServiceWarrantyYear', null,['class'=>'form-control editModalWarrantyYear','id' => 'editModalServiceWarrantyYear','placeholder' => 'YY','autocomplete'=>'off']) !!}
                                                </div>
                                                <div class="col-sm-3" style="padding-left:0px;">
                                                    {!! Form::text('editModalServiceWarrantyMonth', null,['class'=>'form-control editModalWarrantyYear','id' => 'editModalServiceWarrantyMonth','placeholder' => 'MM','autocomplete'=>'off']) !!}
                                                </div>
                                                <div class="col-sm-6" style="padding-right:0px;">
                                                    {!! Form::text('editModalSeviceWarrantyExpireDate', null,['class'=>'form-control editModalWarrantyYear','id' => 'editModalSeviceWarrantyExpireDate','placeholder' => 'Expire Date','autocomplete'=>'off','readonly']) !!}
                                                    <p id="editModalServiceWarrantye" style="max-height:3px;"></p>
                                                </div>

                                            </div>
                                        </div>


                                        <div class="form-group">
                                            {!! Form::label('productCode', 'Product Code:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                {!! Form::textarea('editModalProductCode', null,['class'=>'form-control','id'=>'editModalProductCode','rows' => 2,'autocomplete'=>'off','readonly']) !!}
                                                {!! Form::hidden('EMinitialProductCode',null,['id'=>'EMinitialProductCode']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalDescription', 'Description:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::textarea('editModalDescription', null,['class'=>'form-control','type' => 'text','autocomplete'=>'off','rows'=>3]) !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>{{--End 2nd col-md-6--}}
                            </div>

                        </div>{{--row--}}
                        <div style="border-style: solid;border-width: 1px;"></div><br>
                        <div class="row" style="padding-bottom: 20px;">{{--2nd Row--}}
                            <div class="form-horizontal form-groups">
                                <div class="col-md-12">
                                    <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                        <div class="form-horizontal form-groups">

                                            <div class="form-group">
                                                {!! Form::label('editModalCostPrice', 'Cost Price:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                               
                                                    {!! Form::text('editModalCostPrice', null,['class'=>'form-control','autocomplete'=>'off','id'=>'editModalCostPrice']) !!}                                                
                                                
                                                    
                                                    <p id="editModalCostPricee" style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('editModalAdditionalCost1', 'Additional Cost:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                                
                                                    {!! Form::text('editModalAdditionalCost1', null,['class'=>'form-control','autocomplete'=>'off','id'=>'editModalAdditionalCostA']) !!}
                                                    
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('editModalAdditionalCost2', 'VAT & Tax:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                                
                                                    {!! Form::text('editModalAdditionalCost2', null,['class'=>'form-control','autocomplete'=>'off','id'=>'editModalAdditionalCostB']) !!}
                                                    
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('editModalAdditionalCharge', 'Additional Charge:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                                
                                                    {!! Form::text('editModalAdditionalCharge', null,['class'=>'form-control','autocomplete'=>'off','id'=>'editModalAdditionalCharge','readonly']) !!}
                                                    
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('editModalTotalCost', 'Total Cost:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                                    {!! Form::text('editModalTotalCost', null,['class'=>'form-control','autocomplete'=>'off','id'=>'editModalTotalCost','readonly']) !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('editModalUsefulLifeYear', 'Useful Life:', ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-8">
                                                    <div class="col-sm-6" style="padding-left:0px">
                                                    
                                                        {!! Form::text('editModalUsefulLifeYear', null,['class'=>'form-control','id'=>'editModalUsefulLifeYear','placeholder'=>'YY','autocomplete'=>'off']) !!}
                                                        
                                                    </div>
                                                    <div class="col-sm-6" style="padding-right:0px">
                                                    
                                                        {!! Form::text('editModalUsefulLifeMonth', null,['class'=>'form-control','id'=>'editModalUsefulLifeMonth','placeholder'=>'MM','autocomplete'=>'off']) !!}
                                                        
                                                    </div>
                                                    <p id="editModalUsefulLifee" style="max-height:3px;"></p>
                                                </div>
                                            </div>


                                        </div>
                                    </div>{{--End 1st col-md-6--}}

                                    <div class="col-md-6" style="padding-left:2%;">
                                        <div class="form-group">
                                            {!! Form::label('resellValue', 'Resale Value:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                            
                                                
                                                
                                                {!! Form::text('editModalResellValue', null,['class'=>'form-control','id'=>'editModalResellValue','autocomplete'=>'off']) !!}
                                                
                                                <p id="editModalResellValuee" style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('editModalDepOpeningBalance', 'Dep. Opening Balance:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                            
                                                {!! Form::text('editModalDepOpeningBalance', null,['class'=>'form-control','id'=>'editModalDepOpeningBalance','autocomplete'=>'off']) !!}
                                                
                                                
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('editModalDepAmountPerYear', 'Dep. Cost Per Year:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('editModalDepAmountPerYear', null,['class'=>'form-control','autocomplete'=>'off','readonly','id'=>'editModalDepAmountPerYear']) !!}
                                            </div>
                                        </div>

                                         <div class="form-group" style="display: none;">
                                            {!! Form::label('editModalDepAmountPerMonth', 'Dep. Cost Per Month:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('editModalDepAmountPerMonth', null,['class'=>'form-control','autocomplete'=>'off','readonly','id'=>'editModalDepAmountPerMonth']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group" style="display: none;">
                                            {!! Form::label('depreciationAmountPerDay', 'Dep. Cost Per Day:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('editModalDepAmountPerDay', null,['class'=>'form-control','autocomplete'=>'off','readonly','id'=>'editModalDepAmountPerDay']) !!}
                                            </div>
                                        </div> 

                                        <div class="form-group">
                                            {!! Form::label('editModalDepPercentage', 'Dep. Percentage:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('editModalDepPercentage', null,['class'=>'form-control','autocomplete'=>'off','readonly','id'=>'editModalDepPercentage']) !!}
                                            </div>
                                        </div>

                                    </div>{{--End 2nd col-md--}}

                                </div>
                            </div>
                        </div>{{--End 2nd Row--}}

                        <div style="border-style: solid;border-width: 1px;"></div><br>

                        <div class="row" style="padding-bottom: 20px;">{{--3rd Row--}}

                            <div class="form-horizontal form-groups">
                                <div class="col-md-12">
                                    <div class="col-md-12" style="padding-right:2%;">{{--1st col-md-6--}}



                                        <div class="col-sm-3">
                                            <h3>Product Image</h3>
                                            
                                                <img src="" alt="Product Image" id="editModalProductImageShow" height="200" width="200">
                                            
                                            {!! Form::file('editModalProductImage',  ['class' => 'form-control', 'type' => 'file','id'=>'editModalProductImage']) !!}
                                            <p id="editModalProductImagee" style="max-height:3px;"></p>
                                        </div>


                                        <div class="col-sm-3">
                                            <h3>Warranty Card Image</h3>
                                            
                                                <img src="" alt="Warranty Card Image" id="editModalWarrantyImageShow" height="200" width="200">
                                            
                                            {!! Form::file('editModalWarrantyImage',  ['class' => 'form-control', 'type' => 'file','id'=>'editModalWarrantyImage']) !!}
                                            <p id="editModalWarrantyImagee" style="max-height:3px;"></p>
                                        </div>




                                        <div class="col-sm-3">
                                            <h3>Bill Image</h3>
                                            
                                                <img src="" alt="Bill Image" id="editModalBillImageShow" height="200" width="200">
                                            
                                            {!! Form::file('editModalBillImage',  ['class' => 'form-control', 'type' => 'file','id'=>'editModalBillImage']) !!}
                                            <p id="editModalBillImagee" style="max-height:3px;"></p>
                                        </div>





                                        <div class="col-sm-3">
                                            <h3>Additional Voucher Image </h3>
                                            
                                                <img src="" alt="Additional Voucher Image" id="editModalAdditionalVoucherImageShow" height="200" width="200">
                                            
                                            {!! Form::file('editModalAdditionalVoucherImage',  ['class' => 'form-control', 'type' => 'file','id'=>'editModalAdditionalVoucherImage']) !!}
                                            <p id="editModalAdditionalVoucherImagee-" style="max-height:3px;"></p>
                                        </div>



                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Edit ModalFooter--}}
                        <div class="modal-footer">
                            <button class="btn actionBtn glyphicon glyphicon-check btn-success" type="submit"><span> Update</span></button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span id=""> Close</span></button>
                        </div>

                        {!! Form::close() !!}


                    </div> {{-- End Edit Modal Body--}}

                </div>
            </div>
        </div>
        {{-- End Edit Modal --}}

        {{-- Delete Modal --}}
        <div id="deleteModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
                    </div>
                    <div class="modal-body">
                    
                        <h3 id="DMconfirmText">Are You Confirm to Delete This Product?</h3>
                        <div id="DMmessageText">
                                <h3>Sorry, You can't delete this product!!</h3> 
                                <p>Because Depreciation is generated for this Product.</p>                   
                       </div>                       
                                              
                        

                        <div class="modal-footer">
                            {!! Form::open(['url' => 'deleteFamsProductItem/']) !!}
                            <input type="hidden" name="productId" id="deleteModalProductId">
                            
                            <button id="DMconfirmButton" type="submit" class="btn actionBtn glyphicon glyphicon-check btn-success"><span > Confirm</button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                            {!! Form::close() !!}

                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- End Delete Modal --}}





<script type="text/javascript">
    $(document).ready(function() {
        
        $(document).on('click', '.view-modal', function() {
            if(hasAccess('famsGetProductInfo')){
            var thisModal = $(this);
            var productId = $(this).attr('productId');
            var csrf = "{{csrf_token()}}";
            $.ajax({
                type: 'post',
                url: './famsGetProductInfo',
                data: {productId:productId,_token: csrf},
                dataType: 'json',
                success: function( data ){
                    //alert(JSON.stringify(data));
                    if (data.accessDenied) {
                        showAccessDeniedMessage();
                        return false;
                    }
                    $("#viewModalProductName").empty();
                    $("#viewModalProductName").append('Product Name: '+data['productName']);
                    $("#viewModalProductCode").val(data['productCode']);
                    
                    $("#viewModalSupplierName").val(data['supplier']);
                    $("#viewModalGroupName").val(data['group']);
                    $("#viewModalCategoryName").val(data['category']);
                    $("#viewModalSubCategoryName").val(data['subCategory']);
                    $("#viewModalProductTypeName").val(data['productType']);
                    $("#viewModalBrandName").val(data['brand']);
                    $("#viewModalModelName").val(data['model']);
                    $("#viewModalSizeName").val(data['size']);
                    $("#viewModalColorName").val(data['color']);
                    $("#viewModalUOMName").val(data['uom']);
                    $("#viewModalBranchName").val(data['branchName']);
                    $("#viewModalProjectName").val(data['projectName']);
                    $("#viewModalProjectTypeName").val(data['projectTypeName']);
                    $("#viewModalPurchaseDate").val(data['purchaseDate']);
                    $("#viewModalWarranty").val(data['warranty']);
                    $("#viewModalWarrantyExpireDate").val(data['warrantyExpireDate']);
                    $("#viewModalServiceWarranty").val(data['serviceWarranty']);
                    $("#viewModalServiceWarrantyExpireDate").val(data['serviceWarrantyExpireDate']);
                    $("#viewModalDescription").val(data['description']);
                    $("#viewModalCostPrice").val(data['costPrice']);
                    $("#viewModalAddCost").val(data['additionalCost']);
                    $("#viewModalTaxVat").val(data['vatTax']);
                    $("#viewModalAddCharge").val(data['addCharge']);
                    $("#viewModalTotalCost").val(data['grandTotalCost']);
                    $("#viewModalUsefulLife").val(data['usefulLife']);
                    $("#viewModalResaleValue").val(data['resaleValue']);
                    $("#viewModalDepOpeningBal").val(data['depOpeningBalance']);
                    $("#viewModalDepPerYear").val(data['depPerYear']);
                    $("#viewModalDepPercentage").val(data['depPercentage']);                    
                    $("#viewModalProductImage").attr('src',"{{asset('/images/fams/product/')}}/"+data['productImage']);
                    $("#viewModalWarrantyCardImage").attr('src',"{{asset('/images/fams/product/')}}/"+data['warrantyCardImage']);
                    $("#viewModalBillImage").attr('src',"{{asset('/images/fams/product/')}}/"+data['billImage']);
                    $("#viewModalAddVoucherImage").attr('src',"{{asset('/images/fams/product/')}}/"+data['additionalVoucherImage']);
                    $("#viewModal").modal('show');
                },
                error: function(){
                    alert("Error in Loading Data.");
                }
            });/*End Ajax*/
        }
            
        });/*End View Modal On Click*/


        $(document).on('click', '.edit-modal', function() {
            if(hasAccess('famsGetProductInfoForEditModal')){

            var thisModal = $(this);
            var productId = $(this).attr('productId');
            var csrf = "{{csrf_token()}}";

            $.ajax({
                type: 'post',
                url: './famsGetProductInfoForEditModal',
                data: {productId:productId,_token: csrf},
                dataType: 'json',
                success: function( data ){
                   

                    $("#editModalProductId").val(productId);
                    
                    $("#editModalProductName").val(data['productNameId']);
                    $("#editModalProductCode").val(data['productCode']);
                    $("#EMinitialProductCode").val(data['productCode']);
                    $("#editModalSupplierId").val(data['supplierId']);
                    $("#editModalGroup").val(data['groupId']);
                    $("#editModalCategory").val(data['categoryId']);
                    $("#editModalSubCategory").val(data['subCategoryId']);
                    $("#editModalProductType").val(data['productTypeId']);
                    $("#EMinitialProductType").val(data['productTypeId']);
                    $("#editModalBrand").val(data['brandId']);                    
                    $("#editModalModel").val(data['modelId']);
                    $("#editModalSize").val(data['sizeId']);
                    $("#editModalColor").val(data['colorId']);
                    $("#editModalUOM").val(data['uomId']);

                    $("#editModalBranch").val(data['branchId']);
                    $("#EMinitialBranch").val(data['branchId']);
                    $("#editModalProject").val(data['projectId']);
                    $("#EMinitialProject").val(data['projectId']);
                    $("#editModalProjectType").val(data['projectTypeId']);

                    $("#editModalPurchaseDate").val(data['purchaseDate']);
                    $("#editModalWarrantyYear").val(data['warrantyYear']);
                    $("#editModalWarrantyMonth").val(data['warrantyMonth']);
                    $("#editModalWarrantyExpireDate").val(data['warrantyExpireDate']);
                    $("#editModalServiceWarrantyYear").val(data['serviceWarrantyYear']);
                    $("#editModalServiceWarrantyMonth").val(data['serviceWarrantyMonth']);
                    $("#editModalSeviceWarrantyExpireDate").val(data['serviceWarrantyExpireDate']);

                    $("#editModalDescription").val(data['description']);
                    $("#editModalUsefulLifeYear").val(data['usefulLifeYear']);
                    $("#editModalUsefulLifeMonth").val(data['usefulLifeMonth']);

                    $("#editModalCostPrice").val(data['costPrice']);
                    $("#editModalAdditionalCostA").val(data['additionalCost']);
                    $("#editModalAdditionalCostB").val(data['vatTax']);
                    $("#editModalAdditionalCharge").val(data['addCharge']);
                    $("#editModalTotalCost").val(data['grandTotalCost']);
                    $("#editModalResellValue").val(data['resaleValue']);
                    $("#editModalDepOpeningBalance").val(data['depOpeningBalance']);
                    $("#editModalDepAmountPerYear").val(parseFloat(data['depPerYear']).toFixed(2));
                    /*$("#editModalDepAmountPerMonth").val(data['depPerMonth']);
                    $("#editModalDepAmountPerDay").val(data['depPerDay']);*/
                    $("#editModalDepPercentage").val(data['depPercentage']); 

                    $("#editModalProductImageShow").attr('src',"{{asset('/images/fams/product/')}}/"+data['productImage']);
                    $("#editModalWarrantyImageShow").attr('src',"{{asset('/images/fams/product/')}}/"+data['warrantyCardImage']);
                    $("#editModalBillImageShow").attr('src',"{{asset('/images/fams/product/')}}/"+data['billImage']);
                    $("#editModalAdditionalVoucherImageShow").attr('src',"{{asset('/images/fams/product/')}}/"+data['additionalVoucherImage']);


                    /*Empty the Error Messages*/
                    $("#editModalProductNamee").empty();
                    $("#editModalSupplierIde").empty();
                    $("#editModalGroupe").empty();
                    $("#editModalCategorye").empty();
                    $("#editModalSubCategorye").empty();
                    $("#editModalProductTypee").empty();
                    $("#editModalBrande").empty();
                    $("#editModalModele").empty();
                    $("#editModalSizee").empty();
                    $("#editModalColore").empty();
                    $("#editModalUOMe").empty();
                    $("#editModalBranche").empty();
                    $("#editModalProjecte").empty();
                    $("#editModalProjectTypee").empty();

                    $("#editModalPurchaseDatee").empty();
                    $("#editModalWarrantye").empty();
                    $("#editModalServiceWarrantye").empty();
                    $("#editModalCostPricee").empty();
                    $("#editModalUsefulLifee").empty();
                    $("#editModalResellValuee").empty();

                    $("#editModalProductImagee").empty();
                    $("#editModalWarrantyImagee").empty();
                    $("#editModalBillImagee").empty();
                    $("#editModalAdditionalVoucherImagee").empty();
                    
                    /*End Empty the Error Messages*/


                    /*Disable Editing If this any Depreciation Generated*/
                    if (thisModal.attr('isEditable')>0) {
                        $("#editModalBranch").attr('readonly', 'readonly');
                        $("#editModalGroup").attr('readonly', 'readonly');
                        $("#editModalCategory").attr('readonly', 'readonly');
                        $("#editModalSubCategory").attr('readonly', 'readonly');
                        $("#editModalProductType").attr('readonly', 'readonly');
                        $("#editModalProject").attr('readonly', 'readonly');
                        $("#editModalProjectType").attr('readonly', 'readonly');
                        $("#editModalPurchaseDate").attr('readonly', 'readonly');

                        $("#editModalCostPrice").attr('readonly', 'readonly');
                        $("#editModalAdditionalCostA").attr('readonly', 'readonly');
                        $("#editModalAdditionalCostB").attr('readonly', 'readonly');
                        $("#editModalUsefulLifeYear").attr('readonly', 'readonly');
                        $("#editModalUsefulLifeMonth").attr('readonly', 'readonly');
                        $("#editModalResellValue").attr('readonly', 'readonly');
                        $("#editModalDepOpeningBalance").attr('readonly', 'readonly');
                    }
                    else{
                         $("#editModalBranch").removeAttr('readonly');
                        $("#editModalGroup").removeAttr('readonly');
                        $("#editModalCategory").removeAttr('readonly');
                        $("#editModalSubCategory").removeAttr('readonly');
                        $("#editModalProductType").removeAttr('readonly');
                        $("#editModalProject").removeAttr('readonly');
                        $("#editModalProjectType").removeAttr('readonly');
                        $("#editModalPurchaseDate").removeAttr('readonly');
                        
                        $("#editModalCostPrice").removeAttr('readonly');
                        $("#editModalAdditionalCostA").removeAttr('readonly');
                        $("#editModalAdditionalCostB").removeAttr('readonly');
                        $("#editModalUsefulLifeYear").removeAttr('readonly');
                        $("#editModalUsefulLifeMonth").removeAttr('readonly');
                        $("#editModalResellValue").removeAttr('readonly');
                        $("#editModalDepOpeningBalance").removeAttr('readonly');
                    }

                    /*End Disable Editing If this any Depreciation Generated*/
                    
                    $("#editModal").modal('show');  
                },
                error: function(){
                    alert("Error in Loading Data.");
                }
            });/*End Ajax*/

                   

       }
            
        });/*End View Modal On Click*/



            $(document).on('click', '.delete-modal', function(event) {

                
                if (hasAccess('deleteFamsProductItem')) {

                    $("#deleteModalProductId").val($(this).attr('productId'));

                    if ($(this).attr('isEditable')>0) {
                        $("#DMconfirmButton").hide();
                        $("#DMconfirmText").hide();
                        $("#DMmessageText").show();
                    }
                    else{
                        $("#DMconfirmButton").show();
                        $("#DMconfirmText").show();
                        $("#DMmessageText").hide();  
                    }
                    $("#deleteModal").modal('show');
                }
                       
                
                
               
               
                    
            });
    });
</script>


    <script type="text/javascript">

        $(document).ready(function(){
            $("#viewModal").find(".modal-dialog").css("width","80%");
            $("#editModal").find(".modal-dialog").css("width","80%");
            $("#famsProductTable_info").hide();
        });

    </script>

<style type="text/css">
 

#filtering-group input{
  height: auto;
  width: auto;
  border-radius: 0px;
}

#filtering-group select{height:auto; border-radius: 0px;}
</style>



{{-- Filtering --}}
<script type="text/javascript">
    $(document).ready(function() {
         
        /* Change Product Group*/
         $("#editModalGroup").change(function(){
            var productGroupId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeGroup',
                data: {productGroupId:productGroupId,_token: csrf},
                dataType: 'json',
                success: function( _response ){

                     $("#editModalCategory").empty();
                    $("#editModalCategory").prepend('<option selected="selected" value="">Please Select Product Category</option>');


                    $("#editModalSubCategory").empty();
                    $("#editModalSubCategory").prepend('<option selected="selected" value="">Please Select Product Sub Category</option>');
                   

                    $.each(_response, function (key, value) {
                        {
                             if (key == "productCategoryList") {
                                $.each(value, function (key1,value1) {
                                    $('#editModalCategory').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }

                            if (key == "productSubCategoryList") {
                                $.each(value, function (key1,value1) {
                                    $('#editModalSubCategory').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }
                           
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Product Group*/


         /* Change Product Category*/
         $("#editModalCategory").change(function(){
            var productCategoryId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeCategory',
                data: {productCategoryId:productCategoryId,_token: csrf},
                dataType: 'json',
                success: function( _response ){
                    
                    $("#editModalSubCategory").empty();
                    $("#editModalSubCategory").prepend('<option selected="selected" value="">Please Select Product Sub Category</option>');                   

                    $.each(_response, function (key, value) {
                        {
                            
                            if (key == "productSubCategoryList") {
                                $.each(value, function (key1,value1) {
                                    $('#editModalSubCategory').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }
                           
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Product Category*/

         /* Change Product Sub Category*/
         $("#editModalSubCategory").change(function(){
            var productSubCategoryId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            
            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeSubCategory',
                data: {productSubCategoryId:productSubCategoryId,_token: csrf},
                dataType: 'json',
                success: function( _response ){
                    
                    $("#editModalProductType").empty();
                    $("#editModalProductType").prepend('<option selected="selected" value="">Please Select Product Type</option>');                   

                    $.each(_response, function (key, value) {
                        {
                            
                            if (key == "productTypeList") {
                                $.each(value, function (key1,value1) {
                                    $('#editModalProductType').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }
                           
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Product Sub Category*/

         /* Change Product Type*/
         $("#editModalProductType").change(function(){
            var productTypeId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            
            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProductType',
                data: {productTypeId:productTypeId,_token: csrf},
                dataType: 'json',
                success: function( _response ){
                    
                    $("#editModalProductName").empty();
                    $("#editModalProductName").prepend('<option selected="selected" value="">Please Select Product Name</option>');                   

                    $.each(_response, function (key, value) {
                        {
                            
                            if (key == "productNameList") {
                                $.each(value, function (key1,value1) {
                                    $('#editModalProductName').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }
                           
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Product Type*/

         function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

         /* Change Project*/
         $("#editModalProject").change(function(){
            var projectId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                success: function( data ){


                    $("#editModalProjectType").empty();
                    $("#editModalProjectType").prepend('<option selected="selected" value="">Please Select Project Type</option>');

                    $.each(data['projectTypeList'], function (key, projectObj) {
                                
                            $('#editModalProjectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");                       
                    });                   

                   

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Project*/
    });
</script>
{{-- End Filtering --}}


{{-- Change Product Code --}}


  <script>
            $(document).ready(function () {
                


                /*Change Product Code*/
                String.prototype.splice = function(idx, rem, str) {
            return this.slice(0, idx) + str + this.slice(idx + Math.abs(rem));
        };

        
        $("#editModalProject").change(function () {
            
            var text = $("#editModalProductCode").val();
            var key = "project";
            var projectId = this.value;
            var csrf = "<?php echo csrf_token(); ?>";
            var prefix = "{{$prefix}}";



             if ($("#EMinitialProject").val()==$("#editModalProject option:selected").val()) {
                
                var newPieces = $("#editModalProductCode").val().split('-'); 
                var oldPieces = $("#EMinitialProductCode").val().split('-');

                if (prefix=="") {
                     text = newPieces[0]+'-'+oldPieces[1]+'-'+oldPieces[2]+'-'+newPieces[3]+'-'+newPieces[4]+'-'+newPieces[5]+'-'+newPieces[6];
                }
                else{
                    text = newPieces[0]+'-'+newPieces[1]+'-'+oldPieces[2]+'-'+oldPieces[3]+'-'+newPieces[4]+'-'+newPieces[5]+'-'+newPieces[6]+'-'+newPieces[7];
                }

                 
                    $("#editModalProductCode").val(text);

            }

            else{
                

                 $.ajax({
                type: 'post',
                url: './famsGetInfo',
                data: {key: key, projectId: projectId, _token: csrf},
                dataType: 'json',
                success: function (data) {

                    //alert(data);

                    var codeArray = text.split('-');

                    if (prefix=="") {
                        codeArray[1] = data['project'];                
                    codeArray[2] = data['assetNo']; 

                    text = codeArray[0]+'-'+codeArray[1]+'-'+codeArray[2]+'-'+codeArray[3]+'-'+codeArray[4]+'-'+codeArray[5]+'-'+codeArray[6];
                    }
                    else{
                        codeArray[2] = data['project'];                
                    codeArray[3] = data['assetNo']; 

                    text = codeArray[0]+'-'+codeArray[1]+'-'+codeArray[2]+'-'+codeArray[3]+'-'+codeArray[4]+'-'+codeArray[5]+'-'+codeArray[6]+'-'+codeArray[7];
                    }
                    

                    $("#editModalProductCode").val(text);

                },
                error: function(_response){
                    //alert("Error");
                }
            });

            }
           

        });



        $("#editModalBranch").change(function () {
            var text = $("#editModalProductCode").val();
            var key = "branch";
            var branchId = this.value;
            var csrf = "<?php echo csrf_token(); ?>";
            var prefix = "{{$prefix}}";

            if ($("#EMinitialBranch").val()==$("#editModalBranch option:selected").val()) {
                var newPieces = $("#editModalProductCode").val().split('-'); 
                var oldPieces = $("#EMinitialProductCode").val().split('-');

                if (prefix=="") {
                    text = newPieces[0]+'-'+newPieces[1]+'-'+newPieces[2]+'-'+oldPieces[3]+'-'+oldPieces[4]+'-'+newPieces[5]+'-'+newPieces[6];                    
                }
                else{
                 text = newPieces[0]+'-'+newPieces[1]+'-'+newPieces[2]+'-'+newPieces[3]+'-'+oldPieces[4]+'-'+oldPieces[5]+'-'+newPieces[6]+'-'+newPieces[7];   
                }
                 
                    $("#editModalProductCode").val(text);

            }
            else{

                $.ajax({
                type: 'post',
                url: './famsGetInfo',
                data: {key: key, branchId: branchId, _token: csrf},
                dataType: 'json',
                success: function (data) {

                    var codeArray = text.split('-');
                    if (prefix=="") {
                    codeArray[3] = data['branch'];                
                    codeArray[4] = data['assetNo'];   

                    text = codeArray[0]+'-'+codeArray[1]+'-'+codeArray[2]+'-'+codeArray[3]+'-'+codeArray[4]+'-'+codeArray[5]+'-'+codeArray[6];    
                    }
                    else{
                     codeArray[4] = data['branch'];                
                    codeArray[5] = data['assetNo'];   

                    text = codeArray[0]+'-'+codeArray[1]+'-'+codeArray[2]+'-'+codeArray[3]+'-'+codeArray[4]+'-'+codeArray[5]+'-'+codeArray[6]+'-'+codeArray[7];   
                    }
                    
                    $("#editModalProductCode").val(text);

                },
                error: function(_response){
                    //alert("Error");
                }
            });

            }

            
        });

        $("#editModalProductType").change(function () {
            var text = $("#editModalProductCode").val();
            var key = "productType";
            var productTypeId = this.value;
            var csrf = "<?php echo csrf_token(); ?>";
            var prefix = "{{$prefix}}";


            if ($("#EMinitialProductType").val()==$("#editModalProductType option:selected").val()) {
                var newPieces = $("#editModalProductCode").val().split('-'); 
                var oldPieces = $("#EMinitialProductCode").val().split('-');

                if(prefix==""){
                 text = newPieces[0]+'-'+newPieces[1]+'-'+newPieces[2]+'-'+newPieces[3]+'-'+newPieces[4]+'-'+oldPieces[5]+'-'+oldPieces[6];   
                }
                else{
                    text = newPieces[0]+'-'+newPieces[1]+'-'+newPieces[2]+'-'+newPieces[3]+'-'+newPieces[4]+'-'+newPieces[5]+'-'+oldPieces[6]+'-'+oldPieces[7];
                }
                 
                    $("#editModalProductCode").val(text);

            }

            else{

                 $.ajax({
                type: 'post',
                url: './famsGetInfo',
                data: {key: key, productTypeId: productTypeId, _token: csrf},
                dataType: 'json',
                success: function (data) {

                   var codeArray = text.split('-');
                   if (prefix=="") {
                    codeArray[5] = data['productTypeCode'];                
                    codeArray[6] = data['assetNo'];   

                    text = codeArray[0]+'-'+codeArray[1]+'-'+codeArray[2]+'-'+codeArray[3]+'-'+codeArray[4]+'-'+codeArray[5]+'-'+codeArray[6];
                   }
                   else{
                     codeArray[6] = data['productTypeCode'];                
                    codeArray[7] = data['assetNo'];   

                    text = codeArray[0]+'-'+codeArray[1]+'-'+codeArray[2]+'-'+codeArray[3]+'-'+codeArray[4]+'-'+codeArray[5]+'-'+codeArray[6]+'-'+codeArray[7];
                   }
                   

                    $("#editModalProductCode").val(text);

                },
                error: function(_response){
                    alert("Error");
                }
            });

            }


           
        });       

                /*End Change Product Code*/
            });
</script>


{{-- End Change Product Code --}}


{{-- Validate Data --}}

<script type="text/javascript">
    $(document).ready(function() {
         $("#editModalForm").submit(function( event ){
            
                    if ($("#editModalProductName").val()==""){
                        event.preventDefault();
                        $("#editModalProductNamee").empty();
                        $("#editModalProductNamee").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalSupplierId").val()==""){
                        event.preventDefault();
                        $("#editModalSupplierIde").empty();
                        $("#editModalSupplierIde").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalGroup").val()==""){
                        event.preventDefault();
                        $("#editModalGroupe").empty();
                        $("#editModalGroupe").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalCategory").val()==""){
                        event.preventDefault();
                        $("#editModalCategorye").empty();
                        $("#editModalCategorye").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalSubCategory").val()==""){
                        event.preventDefault();
                        $("#editModalSubCategorye").empty();
                        $("#editModalSubCategorye").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalProductType").val()==""){
                        event.preventDefault();
                        $("#editModalProductTypee").empty();
                        $("#editModalProductTypee").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalBrand").val()==""){
                        event.preventDefault();
                        $("#editModalBrande").empty();
                        $("#editModalBrande").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalModel").val()==""){
                        event.preventDefault();
                        $("#editModalModele").empty();
                        $("#editModalModele").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalSize").val()==""){
                        event.preventDefault();
                        $("#editModalSizee").empty();
                        $("#editModalSizee").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalColor").val()==""){
                        event.preventDefault();
                        $("#editModalColore").empty();
                        $("#editModalColore").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalUOM").val()==""){
                        event.preventDefault();
                        $("#editModalUOMe").empty();
                        $("#editModalUOMe").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalBranch").val()==""){
                        event.preventDefault();
                        $("#editModalBranche").empty();
                        $("#editModalBranche").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalProject").val()==""){
                        event.preventDefault();
                        $("#editModalProjecte").empty();
                        $("#editModalProjecte").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalProjectType").val()==""){
                        event.preventDefault();
                        $("#editModalProjectTypee").empty();
                        $("#editModalProjectTypee").append('<span class="errormsg" style="color:red;">*Required</span>');
                        $("#editModalProjectTypee").show();
                        //alert("Product Name Required");
                    }
                    if ($("#editModalPurchaseDate").val()==""){
                        event.preventDefault();
                        $("#editModalPurchaseDatee").empty();
                        $("#editModalPurchaseDatee").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalWarranty").val()==""){
                        event.preventDefault();
                        $("#editModalWarrantye").empty();
                        $("#editModalWarrantye").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalServiceWarranty").val()==""){
                        event.preventDefault();
                        $("#editModalServiceWarrantye").empty();
                        $("#editModalServiceWarrantye").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalCostPrice").val()==""){
                        event.preventDefault();
                        $("#editModalCostPricee").empty();
                        $("#editModalCostPricee").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalUsefulLife").val()==""){
                        event.preventDefault();
                        $("#editModalUsefulLifee").empty();
                        $("#editModalUsefulLifee").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }
                    if ($("#editModalResellValue").val()==""){
                        event.preventDefault();
                        $("#editModalResellValuee").empty();
                        $("#editModalResellValuee").append('<span class="errormsg" style="color:red;">*Required</span>');
                        //alert("Product Name Required");
                    }

                });

                $("input").keyup(function(){
                    

                    var editModalPurchaseDate = $("#editModalPurchaseDate").val();
                    if(editModalPurchaseDate){$('#editModalPurchaseDatee').hide();}else{$('#editModalPurchaseDatee').show();}

                    var editModalWarrantyYear = $("#editModalWarrantyYear").val();
                    var editModalWarrantyMonth = $("#editModalWarrantyMonth").val();
                    if(editModalWarrantyYear || editModalWarrantyMonth){$('#editModalWarrantye').hide();}else{$('#editModalWarrantye').show();}

                    var editModalServiceWarrantyYear = $("#editModalServiceWarrantyYear").val();
                    var editModalServiceWarrantyMonth = $("#editModalServiceWarrantyMonth").val();
                    if(editModalServiceWarrantyYear || editModalServiceWarrantyMonth){$('#editModalServiceWarrantye').hide();}else{$('#editModalServiceWarrantye').show();}

                    var editModalCostPrice = $("#editModalCostPrice").val();
                    if(editModalCostPrice){$('#editModalCostPricee').hide();}else{$('#editModalCostPricee').show();}

                    var editModalResellValue = $("#editModalResellValue").val();
                    if(editModalResellValue){$('#editModalResellValuee').hide();}else{$('#editModalResellValuee').show();}

                    var editModalUsefulLife = $("#editModalUsefulLife").val();
                    if(editModalUsefulLife){$('#editModalUsefulLifee').hide();}else{$('#editModalUsefulLifee').show();}
                });

                $('select').on('change', function () {
                    var editModalProductName = $("#editModalProductName").val();
                    if(editModalProductName){$('#editModalProductNamee').hide();}else{$('#editModalProductNamee').show();}

                    var editModalProductType = $("#editModalProductType").val();
                    if(editModalProductType){$('#editModalProductTypee').hide();}else{$('#editModalProductTypee').show();}

                    var editModalSupplierId = $("#editModalSupplierId").val();
                    if(editModalSupplierId){$('#editModalSupplierIde').hide();}else{$('#editModalSupplierIde').show();}

                    var editModalGroup = $("#editModalGroup").val();
                    if(editModalGroup){$('#editModalGroupe').hide();}else{$('#editModalGroupe').show();}

                    var editModalCategory = $("#editModalCategory").val();
                    if(editModalCategory){$('#editModalCategorye').hide();}else{$('#editModalCategorye').show();}

                    var editModalSubCategory = $("#editModalSubCategory").val();
                    if(editModalSubCategory){$('#editModalSubCategorye').hide();}else{$('#editModalSubCategorye').show();}

                    var editModalBrand = $("#editModalBrand").val();
                    if(editModalBrand){$('#editModalBrande').hide();}else{$('#editModalBrande').show();}

                    var editModalModel = $("#editModalModel").val();
                    if(editModalModel){$('#editModalModele').hide();}else{$('#editModalModele').show();}

                    var editModalSize = $("#editModalSize").val();
                    if(editModalSize){$('#editModalSizee').hide();}else{$('#editModalSizee').show();}

                    var editModalColor = $("#editModalColor").val();
                    if(editModalColor){$('#editModalColore').hide();}else{$('#editModalColore').show();}

                    var editModalUOM = $("#editModalUOM").val();
                    if(editModalUOM){$('#editModalUOMe').hide();}else{$('#editModalUOMe').show();}

                    var editModalBranch = $("#editModalBranch").val();
                    if(editModalBranch){$('#editModalBranche').hide();}else{$('#editModalBranche').show();}

                    var editModalProject = $("#editModalProject").val();
                    if(editModalProject){$('#editModalProjecte').hide();}else{$('#editModalProjecte').show();}

                    var editModalProjectType = $("#editModalProjectType").val();
                    if(editModalProjectType){$('#editModalProjectTypee').hide();}else{$('#editModalProjectTypee').show();}
                });


    });
</script>
{{-- End Validate Data --}}

{{-- Validate Image --}}
<script type="text/javascript">
    $(document).ready(function() {
        

                $('#editModalProductImage').bind('change', function() {

                    $('#editModalProductImagee').empty();


                    //this.files[0].size gets the size of your file.
                    if (this.files[0].size>512000){
                        this.value = null;
                        $('#editModalProductImagee').empty();
                        $('#editModalProductImagee').append('<span class="errormsg" style="color:red;">*Please select image size less than 512 KB</span>');

                    }
                    else{
                        var fileExtension = ['jpeg','jpg','png'];
                        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                            this.value = null;
                            $('#editModalProductImagee').empty();
                            $('#editModalProductImagee').append('<span class="errormsg" style="color:red;">*Only formats are allowed : '+fileExtension.join(", ")+'</span>');

                        }

                    }


                });

                    $('#editModalWarrantyImage').bind('change', function() {
                    $('#editModalWarrantyImagee').empty();

                    //this.files[0].size gets the size of your file.
                    if (this.files[0].size>512000){
                        this.value = null;
                        $('#editModalWarrantyImagee').empty();
                        $('#editModalWarrantyImagee').append('<span class="errormsg" style="color:red;">*Please select image size less than 512 KB</span>');

                    }
                    else{
                        var fileExtension = ['jpeg','jpg', 'png'];
                        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                            this.value = null;
                            $('#editModalWarrantyImagee').empty();
                            $('#editModalWarrantyImagee').append('<span class="errormsg" style="color:red;">*Only formats are allowed : '+fileExtension.join(", ")+'</span>');
                            //alert("Only formats are allowed : "+fileExtension.join(', '));
                        }

                    }

                });

                $('#editModalBillImage').bind('change', function() {
                    $('#editModalBillImagee').empty();

                    //this.files[0].size gets the size of your file.
                    if (this.files[0].size>512000){
                        this.value = null;
                        $('#editModalBillImagee').empty();
                        $('#editModalBillImagee').append('<span class="errormsg" style="color:red;">*Please select image size less than 512 KB</span>');
                    }

                    else{
                        var fileExtension = ['jpeg','jpg','png'];
                        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                            this.value = null;
                            $('#editModalBillImagee').empty();
                            $('#editModalBillImagee').append('<span class="errormsg" style="color:red;">*Only formats are allowed : '+fileExtension.join(", ")+'</span>');
                            //alert("Only formats are allowed : "+fileExtension.join(', '));
                        }
                    }


                });

                $('#editModalAdditionalVoucherImage').bind('change', function() {
                    $('#editModalAdditionalVoucherImagee').empty();

                    //this.files[0].size gets the size of your file.

                    if (this.files[0].size>512000){

                        this.value = null;
                        $('#editModalAdditionalVoucherImagee').empty();
                        $('#editModalAdditionalVoucherImagee').append('<span class="errormsg" style="color:red;">*Please select image size less than 512 KB</span>');

                    }

                    else{
                        var fileExtension = ['jpeg', 'jpg','png' ];
                        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                            this.value = null;
                            $('#editModalAdditionalVoucherImagee').empty();
                            $('#editModalAdditionalVoucherImagee').append('<span class="errormsg" style="color:red;">*Only formats are allowed : '+fileExtension.join(", ")+'</span>');
                            //alert("Only formats are allowed : "+fileExtension.join(', '));
                        }
                    }

                });

                
    });
</script>
{{-- End Validate Image --}}

{{-- Calculate Total Cost & Depreciation --}}
<script type="text/javascript">
    $(document).ready(function() {
        function calculateDepreciation(){

                    /*calculating Dep. per Year, Month and Day*/
                    if($("#editModalUsefulLifeYear").val()!="" && $("#editModalTotalCost").val()!="" && $("#editModalResellValue").val!=""){

                        var usefulLifeYear = 0;
                        var usefulLifeMonth = 0;

                        if($("#editModalUsefulLifeYear").val()!=""){
                            usefulLifeYear = parseFloat($("#editModalUsefulLifeYear").val());
                        }
                        if($("#editModalUsefulLifeMonth").val()!=""){
                            usefulLifeMonth = parseFloat($("#editModalUsefulLifeMonth").val());
                        }
                        var months = usefulLifeYear * 12 + usefulLifeMonth;
                        var years = months/12;
                        var days = usefulLifeYear * 365 + usefulLifeMonth*30;

                        var total = parseFloat($("#editModalTotalCost").val());
                        var resellValue = parseFloat($("#editModalResellValue").val());
                        var depYear = ((total)/years).toFixed(2);
                        var depMonth = ((total)/months)/*.toFixed(2)*/;
                        var depDay = ((total)/days)/*.toFixed(2)*/;
                        var depPercentage = (100/years).toFixed(2);

                        $("#editModalDepAmountPerYear").val(depYear);
                        $("#editModalDepAmountPerMonth").val(depMonth);
                        $("#editModalDepAmountPerDay").val(depDay);
                        $("#editModalDepPercentage").val(depPercentage);
                    }
                }

                 $("#editModalUsefulLifeYear").on('keyup',function () {
                    calculateDepreciation();
                });
                $("#editModalUsefulLifeMonth").on('keyup',function () {
                    calculateDepreciation();
                });
                $("#editModalTotalCost").on('keyup',function () {
                    calculateDepreciation();
                });
                $("#editModalResellValue").on('keyup',function () {
                    calculateDepreciation();
                });



                ///////Calculate Total Cost
                function calculateTotalCost() {
                    var costPrice = parseFloat(0);
                    var additionalCostA = parseFloat(0);
                    var additionalCostB = parseFloat(0);
                    var totalCost = parseFloat(0);
                    var addCharge = parseFloat(0);

                    
                    var editModalCostPrice = $("#editModalCostPrice").val();
                    var editModalAdditionalCostA = $("#editModalAdditionalCostA").val();
                    var editModalAdditionalCostB = $("#editModalAdditionalCostB").val();
                    var editModalAdditionalCharge = $("#editModalAdditionalCharge").val();

                    if (editModalCostPrice != ""){
                        costPrice = parseFloat(editModalCostPrice).toFixed(2);
                    }

                    if (editModalAdditionalCostA!=""){
                        additionalCostA = parseFloat(editModalAdditionalCostA).toFixed(2);
                    }

                    if (editModalAdditionalCostB!=""){
                        additionalCostB = parseFloat(editModalAdditionalCostB).toFixed(2);
                    }

                    if (editModalAdditionalCharge!=""){
                        addCharge = parseFloat(editModalAdditionalCharge).toFixed(2);
                    }

                    totalCost = parseFloat(costPrice) + parseFloat(additionalCostA) + parseFloat(additionalCostB);
                    var grandTotalCost =  totalCost + parseFloat(addCharge);                 

                    $("#editModalTotalCost").val(grandTotalCost);
                }


                 $("#editModalCostPrice").on('input',function () {
                    calculateTotalCost();
                    calculateDepreciation();


                });
                $("#editModalAdditionalCostA").on('input',function () {
                    calculateTotalCost();
                    calculateDepreciation();
                });
                $("#editModalAdditionalCostB").on('input',function () {
                    calculateTotalCost();
                    calculateDepreciation();
                });
    });
</script>
{{-- End Calculate Total Cost & Depreciation --}}

{{-- Number Filed Validation --}}
<script type="text/javascript">
    $(document).ready(function() {
        $("#editModalWarrantyYear,#editModalWarrantyMonth,#editModalServiceWarrantyYear,#editModalServiceWarrantyMonth,#editModalUsefulLifeYear,#editModalUsefulLifeMonth").on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
        });

        $("#editModalCostPrice,#editModalAdditionalCostA,#editModalAdditionalCostB,#editModalResellValue,#editModalDepOpeningBalance").on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
    });
</script>
{{-- End Number Filed Validation --}}

{{-- Edit Modal Purchase Date --}}
<script>
            $(document).ready(function(){               
                

                /*Edit Purchase Date*/
                $("#editModalPurchaseDate").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    yearRange : "1995:c",
                    maxDate: "dateToday",
                    dateFormat: 'dd-mm-yy',
                    onSelect: function () {
                        $('#editModalPurchaseDatee').hide();
                        $("#editModalWarrantyYear").trigger('keyup');
                        $("#editModalWarrantyMonth").trigger('keyup');
                        $("#editModalServiceWarrantyYear").trigger('keyup');
                        $("#editModalServiceWarrantyMonth").trigger('keyup');
                        
                    }
                });

            });

        </script>
{{-- End Edit Modal Purchase Date --}}


{{-- Set Edit Modal Warranty and Ser. Warranty Date --}}
 <script>
            /*Change Edit Modal Warranty and Service Warranty Expire Date*/
            $(document).ready(function () {
                

                function setWarrantyExpireDate(years,months) {

                    var d = $.datepicker.parseDate('dd-mm-yy', $("#editModalPurchaseDate").val());

                    if(years!=0 || years!=""){
                        d.setFullYear(d.getFullYear() + parseInt(years));
                    }

                    if(months!=0 || months!=""){
                        d.setMonth(d.getMonth() + parseInt(months));
                    }


                    var editModalWarrantyExpireDate = $('#editModalWarrantyExpireDate');
                    editModalWarrantyExpireDate.datepicker({
                        dateFormat: 'dd-mm-yy'
                    });
                    editModalWarrantyExpireDate.datepicker('setDate', d);

                }

                function setServiceWarrantyExpireDate(years,months) {

                    var d = $.datepicker.parseDate('dd-mm-yy', $("#editModalPurchaseDate").val());

                    //var years = parseInt(this.value);
                    if(years!=0 || years!=""){
                        d.setFullYear(d.getFullYear() + parseInt(years));
                    }

                    if(months!=0 || months!=""){
                        d.setMonth(d.getMonth() + parseInt(months));
                    }


                    var editModalSeviceWarrantyExpireDate = $('#editModalSeviceWarrantyExpireDate');
                    editModalSeviceWarrantyExpireDate.datepicker({
                        dateFormat: 'dd-mm-yy'
                    });
                    editModalSeviceWarrantyExpireDate.datepicker('setDate', d);

                }

                $("#editModalWarrantyYear").on('keyup',function(){
                    var months = $("#editModalWarrantyMonth").val();
                    setWarrantyExpireDate(this.value,months);
                });

                $("#editModalWarrantyMonth").on('keyup',function(){
                    var years = $("#editModalWarrantyYear").val();
                    setWarrantyExpireDate(years,this.value);
                });

                $("#editModalServiceWarrantyYear").on('keyup',function(){
                    var months = $("#editModalServiceWarrantyMonth").val();
                    setServiceWarrantyExpireDate(this.value,months);
                });

                $("#editModalServiceWarrantyMonth").on('keyup',function(){
                    var years = $("#editModalServiceWarrantyYear").val();
                    setServiceWarrantyExpireDate(years,this.value);
                });

            });

        </script>
{{-- End Set Edit Modal Warranty and Ser. Warranty Date --}}






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




@endsection
