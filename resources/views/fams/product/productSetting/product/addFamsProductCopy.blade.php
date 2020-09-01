@extends('welcome')
@section('title', '| Product')
@section('content')
    <div class="row add-data-form" style="height:100%">
        <div class="col-md-12">
            <div class="col-md-1"></div>
            <div class="col-md-10 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('viewFamsProduct/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Product List</a>
                </div>

                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <div class="col-md-12">
                            <div class="panel-title col-md-4">Product</div>
                            <div class="col-md-8">
                                <button type="button" id="headerStep1Button"  style=" width: 60px; height: 60px;text-align: center;padding: 6px 0;font-size: 12px; line-height: 1.428571429;border-radius: 30px;background-color: #4CAF50;color: white;">Step 1</button>
                                <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                <button type="button" id="headerStep2Button"  style=" width: 60px; height: 60px;text-align: center;padding: 6px 0;font-size: 12px; line-height: 1.428571429;border-radius: 30px;background-color: gray;color: white;" disabled>Step 2</button>
                                <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                <button type="button" id="headerStep3Button"  style=" width: 60px; height: 60px;text-align: center;padding: 6px 0;font-size: 12px; line-height: 1.428571429;border-radius: 30px;background-color: gray;color: white;" disabled>Step 3</button>
                            </div>
                        </div>
                        

                    </div>

                    <div class="panel-body">

                        {!! Form::open(array('url' => 'addFamsProduct' ,'id'=>'form', 'enctype' => 'multipart/form-data',  'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                        <div id="step1">
                            <div class="row">

                                <div class="col-md-12">

                                    <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                        <div class="form-group">
                                            {!! Form::label('supplierId', 'Supplier Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $supplierName = array('' => 'Please Select Supplier Name') + DB::table('gnr_supplier')->pluck('supplierCompanyName','id')->all();
                                                ?>
                                                {!! Form::select('supplierId', ($supplierName), null, array('class'=>'form-control', 'id' => 'supplierId')) !!}
                                                <p id='supplierIde' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('groupId', 'Group', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $GroupName = array('' => 'Please Select Product Group') + DB::table('fams_product_group')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('productGroupId', ($GroupName), null, array('class'=>'form-control', 'id' => 'productGroupId')) !!}
                                                <p id='groupIde' style="max-height:3px;"></p>

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('categoryId', 'Category:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $productCategoryId = array('' => 'Please Select Product Category') + DB::table('fams_product_category')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('productCategoryId', ($productCategoryId), null, array('class'=>'form-control', 'id' => 'productCategoryId')) !!}
                                                <p id='productCategoryIde' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('subCategoryId', 'Sub Catagory:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $productSubCategoryId = array('' => 'Please Select Product Sub Category') + DB::table('fams_product_sub_category')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('productSubCategoryId', ($productSubCategoryId), null, array('class'=>'form-control', 'id' => 'productSubCategoryId')) !!}
                                                <p id='productSubCategoryIde' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('productType', 'Product Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $productTypeId = array('' => 'Please Select Product Type') + DB::table('fams_product_type')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('productTypeId', ($productTypeId), null, array('class'=>'form-control', 'id' => 'productTypeId')) !!}
                                                <p id='productTypeIde' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('productName', 'Product Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $productName = array('' => 'Please Select Product Name') + DB::table('fams_product_name')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('productName', ($productName), null, array('class'=>'form-control', 'id' => 'productName')) !!}
                                                
                                                <p id='productNamee' style="max-height:3px;"></p>
                                            </div>                                           
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('brandId', 'Brand:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $productBrandId = array('' => 'Please Select Product Brand') + DB::table('fams_product_brand')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('brandId', ($productBrandId), null, array('class'=>'form-control', 'id' => 'productBrandId')) !!}
                                                <p id='productBrandIde' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('modelId', 'Model:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $productModelId = array('' => 'Please Select Product Model') + DB::table('fams_product_model')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('productModelId', ($productModelId), null, array('class'=>'form-control', 'id' => 'productModelId')) !!}
                                                <p id='productModelIde' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('sizeId', 'Size:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $productSizeId = array('' => 'Please Select Product Size') + DB::table('fams_product_size')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('sizeId', ($productSizeId), null, array('class'=>'form-control', 'id' => 'sizeId')) !!}
                                                <p id='productSizeIde' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('colorId', 'Color:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $productColorId = array('' => 'Please Select Prduct Color') + DB::table('fams_product_color')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('colorId', ($productColorId), null, array('class'=>'form-control', 'id' => 'colorId')) !!}
                                                <p id='colorIde' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('uomId', 'UOM:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $productUomId = array('' => 'Please Select UOM') + DB::table('fams_product_uom')->pluck('name','id')->all();
                                                ?>
                                                {!! Form::select('uomId', ($productUomId), null, array('class'=>'form-control', 'id' => 'uomId')) !!}
                                                <p id='uomIde' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                    </div>{{--End 1st col-sm-6--}}

                                    <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}

                                        <div class="form-group">
                                            {!! Form::label('project', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $projects =  DB::table('gnr_project')->select('name','id','projectCode')->get();
                                                ?>
                                                <select name="projectId" id="projectId" class="form-control">
                                                    <option value="">Please Select Project</option>
                                                    @foreach($projects as $project)
                                                    <option value="{{$project->id}}">{{str_pad($project->projectCode,3,'0',STR_PAD_LEFT).'-'.$project->name}}</option>
                                                    @endforeach                                                  
                                                </select>


                                                <p id='projectIde' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <?php
                                                $projectTypes = DB::table('gnr_project_type')->select('name','id','projectTypeCode')->get();
                                                ?>
                                                <select name="projectType" id="projectType" class="form-control">
                                                    <option value="">Please Select Project Type</option>
                                                    @foreach($projectTypes as $projectType)
                                                    <option value="{{$projectType->id}}">{{str_pad($projectType->projectTypeCode,3,'0',STR_PAD_LEFT).'-'.$projectType->name}}</option>
                                                    @endforeach                                                  
                                                </select>
                                               
                                                <p id='projectTypee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                
                                                <select  id="branchId" class="form-control input-sm" name="branchId">
                                                    <option value="">Please Select Branch</option>
                                                    @foreach($branches as $branch)
                                                        <option value={{$branch->id}}>{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT).'-'.$branch->name}}</option>
                                                    @endforeach

                                                </select>

                                                <p id='branchIde' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        

                                        <div class="form-group">
                                            {!! Form::label('purchaseDate', 'Purchase Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('purchaseDate', $value = null, ['class' => 'form-control', 'id' => 'purchaseDate', 'type' => 'text','placeholder' => 'Enter Purchase Date','autocomplete'=>'off']) !!}
                                                <p id='purchaseDatee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('warrantyYear', 'Warranty:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                <div class="col-sm-3" style="padding-left:0px;">
                                                    {!! Form::text('warrantyYear', $value = null, ['class' => 'form-control col-sm-12', 'id' => 'warrantyYear', 'type' => 'text', 'placeholder' => 'YY','autocomplete'=>'off']) !!}
                                                    </div>

                                                    <div class="col-sm-3" style="padding-left:0px;">
                                                    {!! Form::text('warrantyMonth', $value = null, ['class' => 'form-control col-sm-12', 'id' => 'warrantyMonth','placeholder' => 'MM','autocomplete'=>'off']) !!}
                                                    </div>

                                                <div class="col-sm-6" style="padding-right:0px;">
                                                {!! Form::text('warrantyExpireDate2', $value = null, ['class' => 'form-control col-sm-12', 'id' => 'warrantyExpireDate2', 'type' => 'text', 'placeholder' => 'Expire Date','autocomplete'=>'off','readonly']) !!}
                                                    </div>


                                                <p id='warrantye' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('serviceWarrantyYear', 'Ser. Warranty:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <div class="col-sm-3" style="padding-left:0px;">
                                                    {!! Form::text('serviceWarrantyYear', $value = null, ['class' => 'form-control col-sm-12', 'id' => 'serviceWarrantyYear',  'placeholder' => 'YY','autocomplete'=>'off']) !!}
                                                    </div>

                                                <div class="col-sm-3" style="padding-left:0px;">
                                                    {!! Form::text('serviceWarrantyMonth', $value = null, ['class' => 'form-control col-sm-12', 'id' => 'serviceWarrantyMonth',  'placeholder' => 'MM','autocomplete'=>'off']) !!}
                                                    </div>

                                                <div class="col-sm-6" style="padding-right:0px;">
                                                    {!! Form::text('serviceWarrantyExpireDate2', $value = null, ['class' => 'form-control col-sm-12', 'id' => 'serviceWarrantyExpireDate2', 'placeholder' => 'Expire Date','autocomplete'=>'off','readonly']) !!}
                                                </div>

                                                <p id='serviceWarrantye' style="max-height:3px;"></p>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            {!! Form::label('','', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::textarea('productHelp', $value = null, ['class' => 'form-control', 'rows'=>3,'autocomplete'=>'off','placeholder'=>'Ambala-AssetNo-Project-AssetNo-Branch-AssetNo-ProductType-AssetNo','readonly']) !!}
                                                <p class="error text-center alert alert-danger hidden"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('product', 'Product Code:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::textarea('productCode', $value = null, ['class' => 'form-control', 'id' => 'productCode', 'type' => 'text','rows'=>2,'autocomplete'=>'off','readonly']) !!}
                                                <p class="error text-center alert alert-danger hidden"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('description', 'Description:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::textarea('description', $value = null, ['class' => 'form-control', 'id' => 'description', 'type' => 'text','rows'=>3, 'placeholder' => 'Enter Description','autocomplete'=>'off']) !!}
                                                <p id='serviceWarrantye' style="max-height:3px;"></p>
                                            </div>
                                        </div>


                                    </div>{{--End 2nd col-sm-6--}}

                                </div>{{--End Col-md-12--}}
                                <div class="col-sm-12 text-right" style="padding-right: 2%">
                                    <button id="step1NextButton" type="button" class="btn btn-info">Next</button>
                                </div>
                            </div>
                        </div>{{--End Step 1--}}


                        <div id="step2">
                            <div class="row">

                                <div class="col-md-12">

                                    <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}

                                        <div class="form-group">
                                            {!! Form::label('costPrice', 'Cost Price:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('costPrice', $value = null, ['class' => 'form-control', 'id' => 'costPrice', 'type' => 'text', 'placeholder' => 'Enter Cost price','autocomplete'=>'off']) !!}
                                                <p id='costPricee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('additionalCost1', 'Additional Cost:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('additionalCost1', $value = null, ['class' => 'form-control', 'id' => 'additionalCost1', 'type' => 'text', 'placeholder' => 'Enter Additional Cost','autocomplete'=>'off']) !!}
                                                <p id='additionalCost1e' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('additionalCost2', 'VAT & Tax.:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('additionalCost2', $value = null, ['class' => 'form-control', 'id' => 'additionalCost2', 'type' => 'text', 'placeholder' => 'Enter VAT & Tax','autocomplete'=>'off']) !!}
                                                <p id='additionalCost2e' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('total', 'Total:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('total', $value = null, ['class' => 'form-control', 'id' => 'total', 'type' => 'text','autocomplete'=>'off','readonly']) !!}

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('usefulLife', 'Useful Life:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <div class="col-sm-6" style="padding-left:0px">
                                                {!! Form::text('usefulLifeYear', $value = null, ['class' => 'form-control', 'id' => 'usefulLifeYear', 'type' => 'text', 'placeholder' => 'Enter Year','autocomplete'=>'off']) !!}
                                                    </div>
                                                    <div class="col-sm-6" style="padding-right:0px">
                                                        {!! Form::text('usefulLifeMonth', $value = null, ['class' => 'form-control', 'id' => 'usefulLifeMonth', 'placeholder' => 'Enter Month','autocomplete'=>'off']) !!}
                                                        </div>
                                                <p id='usefulLifee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('productQuantity', 'Product Quantity:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('productQuantity', $value = null, ['class' => 'form-control', 'id' => 'productQuantity', 'placeholder' => 'Enter Product Quantity','autocomplete'=>'off']) !!}
                                                <p id='productQuantitye' style="max-height:3px;"></p>
                                                </div>

                                            </div>

                                    </div> {{--End 1st col-md-6--}}

                                    <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}

                                        <div class="form-group">
                                            {!! Form::label('resellValue', 'Resale Value:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('resellValue', $value = null, ['class' => 'form-control', 'id' => 'resellValue', 'type' => 'text', 'placeholder' => 'Enter Resale Value','autocomplete'=>'off']) !!}
                                                <p id='resellValuee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('depOpeningBalance', 'Dep. Opening Balance:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('depOpeningBalance', $value = null, ['class' => 'form-control', 'id' => 'depOpeningBalance', 'type' => 'text', 'placeholder' => 'Enter Dep. Opening Balance','autocomplete'=>'off']) !!}
                                                <p id='depOpeningBalancee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('depYear', 'Dep. Cost Per Year:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('depYear', $value = null, ['class' => 'form-control', 'id' => 'depYear', 'type' => 'text','autocomplete'=>'off','readonly']) !!}

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('depMonth', 'Dep. Cost Per Month:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('depMonth', $value = null, ['class' => 'form-control', 'id' => 'depMonth', 'type' => 'text','autocomplete'=>'off','readonly']) !!}

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('depDay', 'Dep. Cost Per Day:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('depDay', $value = null, ['class' => 'form-control', 'id' => 'depDay', 'type' => 'text','autocomplete'=>'off','readonly']) !!}

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('depPercentage', 'Dep. Percentage:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('depPercentage', $value = null, ['class' => 'form-control', 'id' => 'depPercentage','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                    </div> {{--End 2nd col-md-6--}}

                                    <div class="col-sm-12 text-right" style="padding-right: 1%">
                                        <button id="step2BackButton" class="btn btn-info" type="button">Back</button>
                                        <button id="step2NextButton" class="btn btn-info" type="button">Next</button>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div id="step3">
                            <div class="row">

                                <div class="col-md-12">

                                    <div class="col-md-6">{{--1st col-md-6--}}
                                        <div class="form-group">
                                            {!! Form::label('productImage', 'Product Image:', ['class' => 'col-sm-5 control-label']) !!}
                                            <div class="col-sm-7">
                                                {!! Form::file('productImage', $value = null, ['class' => 'form-control', 'id' => 'productImage', 'type' => 'file']) !!}
                                                <p id='productImagee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">

                                            {!! Form::label('warrantyImage', 'Warranty Card Image:', ['class' => 'col-sm-5 control-label']) !!}
                                            <div class="col-sm-7">
                                                {!! Form::file('warrantyImage', $value = null, ['class' => 'form-control', 'id' => 'warrantyImage', 'type' => 'file']) !!}
                                                <p id='warrantyImagee' style="max-height:3px;"></p>
                                            </div>

                                        </div>



                                    </div>{{-- End 1st col-md-6--}}

                                    <div class="col-md-6">{{--2nd col-md-6--}}
                                        <div class="form-group">
                                            {!! Form::label('billImage', 'Bill Image:', ['class' => 'col-sm-5 control-label']) !!}
                                            <div class="col-sm-7">
                                                {!! Form::file('billImage', $value = null, ['class' => 'form-control', 'id' => 'billImage', 'type' => 'file']) !!}
                                                <p id='billImagee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('additionalVoucherImage', 'Additional Voucher Image:', ['class' => 'col-sm-5 control-label']) !!}
                                            <div class="col-sm-7">
                                                {!! Form::file('additionalVoucherImage', $value = null, ['class' => 'form-control', 'id' => 'additionalVoucherImage', 'type' => 'file']) !!}
                                                <p id='additionalVoucherImagee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                    </div>{{--End 2nd col-md-6--}}
                                </div>
                            </div>

                            <div class="row" >
                                <div class="col-sm-12 " style="padding-right: 0px">
                                    <div class="col-sm-11 text-right" style="padding-right:5%;">
                                        <button id="step3BackButton" class="btn btn-info" type="button">Back</button>
                                    </div>
                                    <div class="col-sm-1"></div>

                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-md-12 text-center">
                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-2 control-label']) !!}
                                    <div class="col-sm-12">
                                        {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']) !!}
                                        <a href="{{url('viewFamsProduct/')}}" class="btn btn-danger closeBtn">Close</a>
                                    </div>
                                </div>
                                {{--<div class="col-md-4">
                                    <span id="success" style="color:green; font-size:20px;"></span>
                                </div>--}}
                            </div>

                            {!! Form::close() !!}
                        </div>{{--End Step 3--}}

                    </div>
                </div>
                <div class="footerTitle" style="border-top:1px solid white"></div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            $('#productImage').bind('change', function() {
                $('#productImagee').empty();


                //this.files[0].size gets the size of your file.
                if (this.files[0].size>512000){
                    this.value = null;
                    $('#productImagee').empty();
                    $('#productImagee').append('<span class="errormsg" style="color:red;">*Please select image size less than 512 KB</span>');

                }
                else{
                    var fileExtension = ['jpeg','jpg','png'];
                    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                        this.value = null;
                        $('#productImagee').empty();
                        $('#productImagee').append('<span class="errormsg" style="color:red;">*Only formats are allowed : '+fileExtension.join(", ")+'</span>');
                        //alert("Only formats are allowed : "+fileExtension.join(', '));
                    }

                }


            });

            $('#warrantyImage').bind('change', function() {
                $('#warrantyImagee').empty();

                //this.files[0].size gets the size of your file.
                if (this.files[0].size>512000){
                    this.value = null;
                    $('#warrantyImagee').empty();
                    $('#warrantyImagee').append('<span class="errormsg" style="color:red;">*Please select image size less than 512 KB</span>');

                }
                else{
                    var fileExtension = ['jpeg','jpg', 'png'];
                    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                        this.value = null;
                        $('#warrantyImagee').empty();
                        $('#warrantyImagee').append('<span class="errormsg" style="color:red;">*Only formats are allowed : '+fileExtension.join(", ")+'</span>');
                        //alert("Only formats are allowed : "+fileExtension.join(', '));
                    }

                }

            });

            $('#billImage').bind('change', function() {
                $('#billImagee').empty();

                //this.files[0].size gets the size of your file.
                if (this.files[0].size>512000){
                    this.value = null;
                    $('#billImagee').empty();
                    $('#billImagee').append('<span class="errormsg" style="color:red;">*Please select image size less than 512 KB</span>');
                }

                else{
                    var fileExtension = ['jpeg','jpg','png'];
                    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                        this.value = null;
                        $('#billImagee').empty();
                        $('#billImagee').append('<span class="errormsg" style="color:red;">*Only formats are allowed : '+fileExtension.join(", ")+'</span>');
                        //alert("Only formats are allowed : "+fileExtension.join(', '));
                    }
                }


            });

            $('#additionalVoucherImage').bind('change', function() {
                $('#additionalVoucherImagee').empty();

                //this.files[0].size gets the size of your file.
                if (this.files[0].size>512000){
                    this.value = null;
                    $('#additionalVoucherImagee').empty();
                    $('#additionalVoucherImagee').append('<span class="errormsg" style="color:red;">*Please select image size less than 512 KB</span>');

                }

                else{
                    var fileExtension = ['jpeg', 'jpg','png'];
                    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                        this.value = null;
                        $('#additionalVoucherImagee').empty();
                        $('#additionalVoucherImagee').append('<span class="errormsg" style="color:red;">*Only formats are allowed : '+fileExtension.join(", ")+'</span>');
                        //alert("Only formats are allowed : "+fileExtension.join(', '));
                    }
                }

            });

        });
    </script>


@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>

<script type="text/javascript">

    $(document).ready(function(){

        $("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}

            var costPrice = $("#costPrice").val();
            if(costPrice){$('#costPricee').hide();}else{$('#costPricee').show();}

            var usefulLifeYear = $("#usefulLifeYear").val();
            //if(usefulLifeYear){$('#usefulLifee').hide();}else{$('#usefulLifee').show();}

            var usefulLifeMonth = $("#usefulLifeMonth").val();
            if(usefulLifeYear || usefulLifeMonth){$('#usefulLifee').hide();}else{$('#usefulLifee').show();}

            var resellValue = $("#resellValue").val();
            if(resellValue){$('#resellValuee').hide();}else{$('#resellValuee').show();}

            var depOpeningBalance = $("#depOpeningBalance").val();
            if(depOpeningBalance){$('#depOpeningBalancee').hide();}else{$('#depOpeningBalancee').show();}

            var salesPrice = $("#salesPrice").val();
            if(salesPrice){$('#salesPricee').hide();}else{$('#salesPricee').show();}


            var purchaseDate = $("#purchaseDate").val();
            if(purchaseDate){$('#purchaseDatee').hide();}else{$('#purchaseDatee').show();}

            var warrantyYear = $("#warrantyYear").val();
            var warrantyMonth = $("#warrantyMonth").val();
            if(warrantyYear || warrantyMonth){$('#warrantye').hide();}else{$('#warrantye').show();}

            var serviceWarrantyYear = $("#serviceWarrantyYear").val();
            var serviceWarrantyMonth = $("#serviceWarrantyMonth").val();
            if(serviceWarrantyYear || serviceWarrantyMonth){$('#serviceWarrantye').hide();}else{$('#serviceWarrantye').show();}

            var productName = $("#productName").val();
            if(productName){$('#productNamee').hide();}else{$('#productNamee').show();}

            var productQuantity = $("#productQuantity").val();
            if(productQuantity){$('#productQuantitye').hide();}else{$('#productQuantitye').show();}
        });
        $('select').on('change', function (e) {
            var groupId = $("#productGroupId").val();
            if(groupId){$('#groupIde').hide();}else{$('#groupIde').show();}
            var supplierId = $("#supplierId").val();
            if(supplierId){$('#supplierIde').hide();}else{$('#supplierIde').show();}
            var categoryId = $("#productCategoryId").val();
            if(categoryId){$('#productCategoryIde').hide();}else{$('#productCategoryIde').show();}
            var subCategoryId = $("#productSubCategoryId").val();
            if(subCategoryId){$('#productSubCategoryIde').hide();}else{$('#productSubCategoryIde').show();}
            var productTypeId = $("#productTypeId").val();
            if(productTypeId){$('#productTypeIde').hide();}else{$('#productTypeIde').show();}
            var brandId = $("#productBrandId").val();
            if(brandId){$('#productBrandIde').hide();}else{$('#productBrandIde').show();}
            var modelId = $("#productModelId").val();
            if(modelId){$('#productModelIde').hide();}else{$('#productModelIde').show();}
            var sizeId = $("#sizeId").val();
            if(sizeId){$('#productSizeIde').hide();}else{$('#productSizeIde').show();}
            var colorId = $("#colorId").val();
            if(colorId){$('#colorIde').hide();}else{$('#colorIde').show();}
            var uomId = $("#uomId").val();
            if(uomId){$('#uomIde').hide();}else{$('#uomIde').show();}

            var productName = $("#productName").val();
            if(productName){$('#productNamee').hide();}else{$('#productNamee').show();}

            var projectId = $("#projectId").val();
            if(projectId){$('#projectIde').hide();}else{$('#projectIde').show();}
            var projectType = $("#projectType").val();
            if(projectType){$('#projectTypee').hide();}else{$('#projectTypee').show();}

            var branchId = $("#branchId").val();
            if(branchId){$('#branchIde').hide();}else{$('#branchIde').show();}


        });

    });
</script>

<script>
    $(document).ready(function () {
        $("#step2").hide();
        $("#step3").hide();
        $("#step1NextButton").on('click',function () {


            var supplierId = $("#supplierId").val();
            var productGroupId = $("#productGroupId").val();
            var productCategoryId = $("#productCategoryId").val();
            var productSubCategoryId = $("#productSubCategoryId").val();
            var productTypeId = $("#productTypeId").val();
            var productBrandId = $("#productBrandId").val();
            var productModelId = $("#productModelId").val();
            var sizeId = $("#sizeId").val();
            var colorId = $("#colorId").val();
            var uomId = $("#uomId").val();
            var projectId = $("#projectId").val();
            var projectType = $("#projectType").val();
            var branchId = $("#branchId").val();
            var productName = $("#productName").val();
            var purchaseDate = $("#purchaseDate").val();
            var warrantyYear = $("#warrantyYear").val();
            var warrantyMonth = $("#warrantyMonth").val();
            var serviceWarrantyYear = $("#serviceWarrantyYear").val();
            var serviceWarrantyMonth = $("#serviceWarrantyMonth").val();


            if(supplierId==""){
                $('#supplierIde').empty();
                $('#supplierIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(productGroupId==""){
                $('#groupIde').empty();
                $('#groupIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(productCategoryId==""){
                $('#productCategoryIde').empty();
                $('#productCategoryIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(productSubCategoryId==""){
                $('#productSubCategoryIde').empty();
                $('#productSubCategoryIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(productTypeId==""){
                $('#productTypeIde').empty();
                $('#productTypeIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(supplierId==""){
                $('#supplierIde').empty();
                $('#supplierIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(productBrandId==""){
                $('#productBrandIde').empty();
                $('#productBrandIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(productModelId==""){
                $('#productModelIde').empty();
                $('#productModelIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }if(sizeId==""){
                $('#productSizeIde').empty();
                $('#productSizeIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(colorId==""){
                $('#colorIde').empty();
                $('#colorIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }if(uomId==""){
                $('#uomIde').empty();
                $('#uomIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(projectId==""){
                $('#projectIde').empty();
                $('#projectIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(projectType==""){
                $('#projectTypee').empty();
                $('#projectTypee').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(branchId==""){
                $('#branchIde').empty();
                $('#branchIde').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(productName==""){
                $('#productNamee').empty();
                $('#productNamee').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(purchaseDate==""){
                $('#purchaseDatee').empty();
                $('#purchaseDatee').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(warrantyYear=="" && warrantyMonth==""){
                $('#warrantye').empty();
                $('#warrantye').append('<span class="errormsg" style="color:red;">*Required</span>');
            }
            if(serviceWarrantyYear=="" && serviceWarrantyMonth==""){
                $('#serviceWarrantye').empty();
                $('#serviceWarrantye').append('<span class="errormsg" style="color:red;">*Required</span>');
            }


            if(supplierId!="" && productGroupId!="" && productCategoryId!="" && productSubCategoryId!=""  && productBrandId!="" && productTypeId!="" && productModelId!="" && sizeId!="" && colorId!="" && uomId!="" && projectId!="" && projectType!="" && branchId!="" && productName !="" && purchaseDate!="" && warrantyYear!="" && serviceWarrantyYear!=""){
                $("#step1").hide();
                $("#step2").show();
                $("#step3").hide();

                $("#headerStep2Button").css("background-color","#4CAF50");
                $("#headerStep2Button").removeAttr('disabled');
            }


            /*$.ajax({

             type: 'post',
             url: './validateStep1',
             data: {supplierId: supplierId,productGroupId: productGroupId,productCategoryId: productCategoryId,productSubCategoryId: productSubCategoryId,productBrandId: productBrandId,productModelId: productModelId,sizeId: sizeId,colorId: colorId,uomId: uomId,projectId: projectId,branchId: branchId,item: item,purchaseDate: purchaseDate,warranty: warranty,serviceWarranty: serviceWarrantyYear, _token: csrf},
             dataType: 'json',
             success: function (data) {
             if(data=="Success")
             $("#step1").hide();
             $("#step2").show();
             }

             });*/


        });


        $("#step2BackButton").on('click',function () {
            $("#step1").show();
            $("#step2").hide();
            $("#step3").hide();
        });

        $("#step2NextButton").on('click',function () {

            var costPrice = $("#costPrice").val();
            var usefulLifeYear = $("#usefulLifeYear").val();
            var usefulLifeMonth = $("#usefulLifeMonth").val();
            var productQuantity = $("#productQuantity").val();
            var resellValue = $("#resellValue").val();
            var depOpeningBalance = $("#depOpeningBalance").val();
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './validateStep2',
                data: {costPrice: costPrice,usefulLifeYear: usefulLifeYear,usefulLifeMonth: usefulLifeMonth,productQuantity: productQuantity,resellValue: resellValue,depOpeningBalance: depOpeningBalance, _token: csrf},
                dataType: 'json',
                success: function (_response) {

                    if (_response.errors) {
                        if (_response.errors['costPrice']) {
                            $("#costPricee").empty();
                            $('#costPricee').append('<span class="errormsg" style="color:red;">*Required</span>');

                        }
                        if (_response.errors['usefulLifeYear']) {
                            $("#usefulLifee").empty();
                            $('#usefulLifee').append('<span class="errormsg" style="color:red;">*Required</span>');
                        }
                        if (_response.errors['usefulLifeMonth']) {
                            $("#usefulLifee").empty();
                            $('#usefulLifee').append('<span class="errormsg" style="color:red;">*Required</span>');
                        }
                        if (_response.errors['resellValue']) {
                            $("#resellValuee").empty();
                            $('#resellValuee').append('<span class="errormsg" style="color:red;">*Required</span>');

                        }
                        if (_response.errors['productQuantity']) {
                            $("#productQuantitye").empty();
                            $('#productQuantitye').append('<span class="errormsg" style="color:red;">*Required</span>');
                        }
                    }
                    else{
                        $("#step3").show();
                        $("#step2").hide();
                        $("#step1").hide();

                        $("#headerStep3Button").css("background-color","#4CAF50");
                        $("#headerStep3Button").removeAttr('disabled');
                    }
                }
            });

        });
        $("#step3BackButton").on('click',function () {
            $("#step2").show();
            $("#step3").hide();
            $("#step1").hide();
        });
    });
</script>

<script>
    $(document).ready(function () {


        function setWarrantyExpireDate(years,months) {
                var d = $.datepicker.parseDate('dd-mm-yy', $("#purchaseDate").val());

                //var years = parseInt(this.value);
            if(years!=0 || years!=""){
                d.setFullYear(d.getFullYear() + parseInt(years));
            }

                if(months!=0 || months!=""){
                    d.setMonth(d.getMonth() + parseInt(months));
                }

                var warrantyExpireDate2 = $('#warrantyExpireDate2');
                warrantyExpireDate2.datepicker({
                    dateFormat: 'dd-mm-yy'
                });
                warrantyExpireDate2.datepicker('setDate', d);

        }


        $("#warrantyYear").on('keyup',function () {
            if($("#purchaseDate").val()!=""){
                var months = $("#warrantyMonth").val();
                setWarrantyExpireDate(this.value,months);
            }

        });

        $("#warrantyMonth").on('keyup',function () {
            if($("#purchaseDate").val()!=""){
                var years = $("#warrantyYear").val();
                setWarrantyExpireDate(years,this.value);
            }

        });


        function setServiceWarrantyExpireDate(years,months) {

            var d = $.datepicker.parseDate('dd-mm-yy', $("#purchaseDate").val());

            //var years = parseInt(this.value);
            if(years!=0 || years!=""){
                d.setFullYear(d.getFullYear() + parseInt(years));
            }

            if(months!=0 || months!=""){
                d.setMonth(d.getMonth() + parseInt(months));
            }


            var serviceWarrantyExpireDate2 = $('#serviceWarrantyExpireDate2');
            serviceWarrantyExpireDate2.datepicker({
                dateFormat: 'dd-mm-yy'
            });
            serviceWarrantyExpireDate2.datepicker('setDate', d);

        }

        $("#serviceWarrantyYear").on('keyup',function () {
            if($("#purchaseDate").val()!=""){
                var months = $("#serviceWarrantyMonth").val();
                setServiceWarrantyExpireDate(this.value,months);
            }

        });
        $("#serviceWarrantyMonth").on('keyup',function () {
            if($("#purchaseDate").val()!=""){
                var years = $("#serviceWarrantyYear").val();
                setServiceWarrantyExpireDate(years,this.value);
            }

        });



        $("#purchaseDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1995:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#purchaseDatee').hide();
                $('#warrantyExpireDate2').val("");
                $('#serviceWarrantyExpireDate2').val("");
                $('#warrantyYear').val("");
                $('#warrantyMonth').val("");
                $('#serviceWarrantyYear').val("");
                $('#serviceWarrantyMonth').val("");
            }
        });

    });
</script>



{{-- Generate the Product Field Value --}}
<script>
    $(document).ready(function () {

        String.prototype.splice = function(idx, rem, str) {
            return this.slice(0, idx) + str + this.slice(idx + Math.abs(rem));
        };


        //Ambala-AssetNo-Project-AssetNo-Branch-AssetNo-ProductType-AssetNo
        var prefix = "{{$prefix}}";
        var text = "Ambala-00000-00-0000-000-0000-000-0000";
        key = "AmbalaAsset";
        var csrf = "<?php echo csrf_token(); ?>";
        $.ajax({
            type: 'post',
            url: './famsGetInfo',
            data: {key: key, _token: csrf},
            dataType: 'json',
            success: function (data) {

                var newText = text.splice(7,5,data);
                text = newText;
                $("#productCode").val(text);

            },
            error: function(_response){
                //alert("Error");
            }
        });
        $("#productCode").val(text);


        $("#projectId").change(function () {
            var key = "project";
            var projectId = this.value;
            var csrf = "<?php echo csrf_token(); ?>";


            $.ajax({
                type: 'post',
                url: './famsGetInfo',
                data: {key: key, projectId: projectId, _token: csrf},
                dataType: 'json',
                success: function (data) {

                    var newText = text.splice(13,2,data['project']);
                    text = newText;
                    var newText = text.splice(16,4,data['assetNo']);
                    text = newText;
                    $("#productCode").val(text);

                },
                error: function(_response){
                    alert("Error");
                }
            });

        });


        $("#branchId").change(function () {
            var key = "branch";
            var branchId = $("#branchId").val();
            var csrf = "<?php echo csrf_token(); ?>";


            $.ajax({
                type: 'post',
                url: './famsGetInfo',
                data: {key: key, branchId: branchId, _token: csrf},
                dataType: 'json',
                success: function (data) {

                    var newText = text.splice(21,3,data['branch']);
                    text = newText;
                    var newText = text.splice(25,4,data['assetNo']);
                    text = newText;
                    $("#productCode").val(text);

                },
                error: function(_response){
                    //alert("Error");
                }
            });
        });


        $("#productTypeId").change(function () {
            var key = "productType";
            var productTypeId = this.value;
            var csrf = "<?php echo csrf_token(); ?>";


            $.ajax({
                type: 'post',
                url: './famsGetInfo',
                data: {key: key, productTypeId: productTypeId, _token: csrf},
                dataType: 'json',
                success: function (data) {

                    var newText = text.splice(30,3,data['productTypeCode']);
                    text = newText;

                    var newText = text.splice(34,4,data['assetNo']);
                    text = newText;

                    $("#productCode").val(text);

                },
                error: function(_response){
                    alert("Error");
                }
            });
        });

        /*End generating Product Code */


        /* Calculate Total Cost */

        /*calculating Dep. per Year, Month and Day*/
        function calculateDep() {
            if(($("#usefulLifeYear").val()!="" || $("#usefulLifeMonth").val()!="") && $("#total").val()!=""){
                var usefulLifeYear = 0;
                var usefulLifeMonth = 0;
                if($("#usefulLifeYear").val()!=""){
                    usefulLifeYear = parseFloat($("#usefulLifeYear").val());
                }
                if($("#usefulLifeMonth").val()!=""){
                    usefulLifeMonth = parseFloat($("#usefulLifeMonth").val());
                }

                var months = usefulLifeYear * 12 + usefulLifeMonth;
                var years = parseFloat(months/12);
                var days = usefulLifeYear * 365 + usefulLifeMonth*30;

                var total = parseFloat($("#total").val());                

                var depYear = parseFloat(total/years);
                var depMonth = parseFloat(total/months);
                var depDay = parseFloat(total/days);
                //debugger;
                var depPercentage = (100/years).toFixed(2);
                $("#depYear").val(depYear);
                $("#depMonth").val(depMonth);
                $("#depDay").val(depDay);
                $("#depPercentage").val(depPercentage);
            }

        }


        $("#costPrice").on('input',function () {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            if($("#additionalCost1").val()!="" && $("#additionalCost2").val()!=""){
                var additionalCost1 = parseInt($("#additionalCost1").val());
                var additionalCost2 = parseInt($("#additionalCost2").val());
                var total = parseInt(this.value)+additionalCost1+additionalCost2;
                $("#total").val(total);
            }
            else if($("#additionalCost1").val()!="" && $("#additionalCost2").val()==""){
                var additionalCost1 = parseInt($("#additionalCost1").val());
                var total = parseInt(this.value)+additionalCost1;
                $("#total").val(total);
            }
            else if($("#additionalCost1").val()=="" && $("#additionalCost2").val()!=""){
                var additionalCost2 = parseInt($("#additionalCost2").val());
                var total = parseInt(this.value)+additionalCost2;
                $("#total").val(total);
            }
            else{
                $("#total").val(parseInt(this.value));
            }

            if(this.value == ""){
                if($("#additionalCost1").val()!="" && $("#additionalCost2").val()!=""){
                    var additionalCost1 = parseInt($("#additionalCost1").val());
                    var additionalCost2 = parseInt($("#additionalCost2").val());
                    var total = additionalCost1+additionalCost2;
                    $("#total").val(total);
                }
                else if($("#additionalCost1").val()!="" && $("#additionalCost2").val()==""){
                    var additionalCost1 = parseInt($("#additionalCost1").val());
                    var total = additionalCost1;
                    $("#total").val(total);
                }
                else if($("#additionalCost1").val()=="" && $("#additionalCost2").val()!=""){
                    var additionalCost2 = parseInt($("#additionalCost2").val());
                    var total = parseInt(this.value)+additionalCost2;
                    $("#total").val(total);
                }
                else{
                    $("#total").val("");
                }
            }

            calculateDep();

        });

        $("#additionalCost1").on('input',function () {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            if($("#costPrice").val()!="" && $("#additionalCost2").val()!=""){
                var costPrice = parseInt($("#costPrice").val());
                var additionalCost2 = parseInt($("#additionalCost2").val());
                var total = parseInt(this.value)+costPrice+additionalCost2;
                $("#total").val(total);
            }
            else if($("#costPrice").val()!="" && $("#additionalCost2").val()==""){
                var costPrice = parseInt($("#costPrice").val());
                var total = parseInt(this.value)+costPrice;
                $("#total").val(total);
            }
            else if($("#costPrice").val()=="" && $("#additionalCost2").val()!=""){
                var additionalCost2 = parseInt($("#additionalCost2").val());
                var total = parseInt(this.value)+additionalCost2;
                $("#total").val(total);
            }
            else{
                $("#total").val(parseInt(this.value));
            }

            if(this.value == ""){
                if($("#costPrice").val()!="" && $("#additionalCost2").val()!=""){
                    var costPrice = parseInt($("#costPrice").val());
                    var additionalCost2 = parseInt($("#additionalCost2").val());
                    var total = costPrice+additionalCost2;
                    $("#total").val(total);
                }
                else if($("#costPrice").val()!="" && $("#additionalCost2").val()==""){
                    var costPrice = parseInt($("#costPrice").val());
                    var total = costPrice;
                    $("#total").val(total);
                }
                else if($("#costPrice").val()=="" && $("#additionalCost2").val()!=""){
                    var additionalCost2 = parseInt($("#additionalCost2").val());
                    var total = parseInt(this.value)+additionalCost2;
                    $("#total").val(total);
                }
                else{
                    $("#total").val("");
                }
            }

            calculateDep();
        });

        $("#additionalCost2").on('input',function () {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            if($("#costPrice").val()!="" && $("#additionalCost1").val()!=""){
                var costPrice = parseInt($("#costPrice").val());
                var additionalCost1 = parseInt($("#additionalCost1").val());
                var total = parseInt(this.value)+costPrice+additionalCost1;
                $("#total").val(total);
            }
            else if($("#costPrice").val()!="" && $("#additionalCost1").val()==""){
                var costPrice = parseInt($("#costPrice").val());
                var total = parseInt(this.value)+costPrice;
                $("#total").val(total);
            }
            else if($("#costPrice").val()=="" && $("#additionalCost1").val()!=""){
                var additionalCost1 = parseInt($("#additionalCost1").val());
                var total = parseInt(this.value)+additionalCost1;
                $("#total").val(total);
            }
            else{
                $("#total").val(parseInt(this.value));
            }

            if(this.value==""){

                if($("#costPrice").val()!="" && $("#additionalCost1").val()!=""){
                    var costPrice = parseInt($("#costPrice").val());
                    var additionalCost1 = parseInt($("#additionalCost1").val());
                    var total = costPrice+additionalCost1;
                    $("#total").val(total);
                }
                else if($("#costPrice").val()!="" && $("#additionalCost1").val()==""){
                    var costPrice = parseInt($("#costPrice").val());
                    var total = costPrice;
                    $("#total").val(total);
                }
                else if($("#costPrice").val()=="" && $("#additionalCost1").val()!=""){
                    var additionalCost1 = parseInt($("#additionalCost1").val());
                    var total = additionalCost1;
                    $("#total").val(total);
                }
                else{
                    $("#total").val("");
                }
            }

            calculateDep();

        }); /* End Calculating Total*/

        $("#usefulLifeYear").on('input',function () {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            calculateDep();
        });
        $("#usefulLifeMonth").on('input',function () {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            calculateDep();
        });
        $("#warrantyYear,#warrantyMonth,#serviceWarrantyYear,#serviceWarrantyMonth").on('input',function () {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });

        $("#resellValue").on('input',function () {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            
        });
        $("#depOpeningBalance").on('input',function () {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        $("#productQuantity").val("1");
        $("#productQuantity").on('input',function () {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });



        $("#headerStep1Button").click(function () {
            $("#step1").show();
            $("#step2").hide();
            $("#step3").hide();
        });

        $("#headerStep2Button").click(function () {
            $("#step1").hide();
            $("#step2").show();
            $("#step3").hide();
        });

        $("#headerStep3Button").click(function () {
            $("#step1").hide();
            $("#step2").hide();
            $("#step3").show();
        });


    });
</script>


{{-- Filtering --}}
<script type="text/javascript">
    $(document).ready(function() {
        function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }
        /* Change Product Group*/
         $("#productGroupId").change(function(){
            var productGroupId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeGroup',
                data: {productGroupId:productGroupId,_token: csrf},
                dataType: 'json',
                success: function( _response ){

                     $("#productCategoryId").empty();
                    $("#productCategoryId").prepend('<option selected="selected" value="">Please Select Product Category</option>');


                    $("#productSubCategoryId").empty();
                    $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select Product Sub Category</option>');
                   

                    $.each(_response, function (key, value) {
                        {
                             if (key == "productCategoryList") {
                                $.each(value, function (key1,value1) {
                                    $('#productCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }

                            if (key == "productSubCategoryList") {
                                $.each(value, function (key1,value1) {
                                    $('#productSubCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
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
         $("#productCategoryId").change(function(){
            var productCategoryId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeCategory',
                data: {productCategoryId:productCategoryId,_token: csrf},
                dataType: 'json',
                success: function( _response ){
                    
                    $("#productSubCategoryId").empty();
                    $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select Product Sub Category</option>');                   

                    $.each(_response, function (key, value) {
                        {
                            
                            if (key == "productSubCategoryList") {
                                $.each(value, function (key1,value1) {
                                    $('#productSubCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
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
         $("#productSubCategoryId").change(function(){
            var productSubCategoryId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            //alert(productSubCategoryId);

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeSubCategory',
                data: {productSubCategoryId:productSubCategoryId,_token: csrf},
                dataType: 'json',
                success: function( _response ){
                    
                    $("#productTypeId").empty();
                    $("#productTypeId").prepend('<option selected="selected" value="">Please Select Product Type</option>');                   

                    $.each(_response, function (key, value) {
                        {
                            
                            if (key == "productTypeList") {
                                $.each(value, function (key1,value1) {
                                    $('#productTypeId').append("<option value='"+ value1+"'>"+key1+"</option>");
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
         $("#productTypeId").change(function(){
            var productTypeId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            //alert(productSubCategoryId);

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProductType',
                data: {productTypeId:productTypeId,_token: csrf},
                dataType: 'json',
                success: function( _response ){
                    
                    $("#productName").empty();
                    $("#productName").prepend('<option selected="selected" value="">Please Select Product Name</option>');                   

                    $.each(_response, function (key, value) {
                        {
                            
                            if (key == "productNameList") {
                                $.each(value, function (index,obj) {
                                    $('#productName').append("<option value='"+ obj.id+"'>"+obj.name+"</option>");
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

          

    });
</script>
{{-- End Filtering --}}

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
                $("#dateTo").datepicker("option","minDate",new Date(d));

                $("#dateFrom").datepicker("setDate", new Date(d));
                $("#dateFrom").hide();
                $("#dateFrome").hide();

                $("#dateToDiv").attr("class", "col-sm-12");
              }

              //Date Range
              else if(searchMethod==3){
               $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").show();

                 $("#dateToDiv").attr("class", "col-sm-6");
                $("#dateFrom").show();
                $("#dateFrom").val("");

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

{{-- Filtering --}}
<script type="text/javascript">
    jQuery(document).ready(function($) {

        function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

         /* Change Project*/
         $("#projectId").change(function(){
            
            var projectId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                success: function( data ){


                    $("#projectType").empty();
                    $("#projectType").prepend('<option selected="selected" value="">Please Select Project Type</option>');

                    $("#branchId").empty();
                    $("#branchId").prepend('<option selected="selected" value="">Please Select Branch</option>');
                   

                    $.each(data['projectTypeList'], function (key, projectObj) {
                                
                            $('#projectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");                       
                    });

                    $.each(data['branchList'], function (key, branchObj) {
                                
                            $('#branchId').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");                       
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Project*/

         /* Change Project Type*/
         $("#projectType").change(function(){
            var projectId = $("#projectId").val();
            var projectTypeId = $(this).val();


            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProjectType',
                data: {projectId:projectId,projectTypeId: projectTypeId,_token: csrf},
                dataType: 'json',
                success: function( data ){ 

                     $("#branchId").empty();
                    $("#branchId").prepend('<option selected="selected" value="">Please Select Branch</option>');
                    

                     $.each(data['branchList'], function (key, branchObj) {
                                
                            $('#branchId').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
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


