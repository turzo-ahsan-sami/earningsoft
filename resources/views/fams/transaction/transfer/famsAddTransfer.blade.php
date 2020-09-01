@extends('layouts/fams_layout')
@section('title', '| Transfer')
@section('content')

<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-1"></div>
                <div class="col-md-10 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('famsViewTransfer/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Transfer List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Transfer</div>
                                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12" style="padding-right: 0px; padding-left: 0px;">
                            <div class="form-horizontal form-groups">  
                            {!! Form::open(['url' => 'famsStoreTransfer','id'=>'submitForm']) !!}                           
                            
                                <div class="col-md-6">
                            
                                <div class="form-group">
                                        {!! Form::label('transferNo', 'Transfer ID:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                 
                                            {!! Form::text('transferNo', "", ['id' => 'transferNo','class'=>'form-control','readonly']) !!}
                                        </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('projectFrom', 'Project From:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                 
                                            {!! Form::hidden('projectFromId', "", ['id' => 'projectFromId','class'=>'form-control','readonly']) !!}
                                            {!! Form::text('projectFrom', "", ['id' => 'projectFrom','class'=>'form-control','readonly']) !!}
                                        </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('projectTypeFrom', 'Project Type From:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                 
                                            {!! Form::hidden('projectTypeFromId', "", ['id' => 'projectTypeFromId','class'=>'form-control','readonly']) !!}
                                            {!! Form::text('projectTypeFrom', "", ['id' => 'projectTypeFrom','class'=>'form-control','readonly']) !!}
                                        </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('branchFrom', 'Branch From:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                 
                                            {!! Form::hidden('branchFromId', "", ['id' => 'branchFromId','class'=>'form-control','readonly']) !!}
                                            {!! Form::text('branchFrom', "", ['id' => 'branchFrom','class'=>'form-control','readonly']) !!}
                                        </div>
                                </div>                                                                                         
                                
                                </div>{{-- End Col 6 --}}

                                <div class="col-md-6">
                            
                                <div class="form-group">
                                        {!! Form::label('costPrice', 'Cost Price:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                 
                                            {!! Form::text('costPrice', "", ['class'=>'form-control','id'=>'costPrice','readonly']) !!}
                                        </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('depGenerated', 'Accumulated Dep.:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                 
                                            {!! Form::text('depGenerated', "", ['id' => 'depGenerated','class'=>'form-control','readonly']) !!}
                                        </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('remainingDep', 'Remaining Dep.:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                 
                                            {!! Form::text('remainingDep', "", ['id' => 'remainingDep','class'=>'form-control','readonly']) !!}
                                        </div>
                                </div>
                                <div class="form-group">
                                        {!! Form::label('purchaseDate', 'Purchase Date:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                 
                                            {!! Form::text('purchaseDate', "", ['id' => 'purchaseDate','class'=>'form-control','style'=>'cursor: pointer','readonly']) !!}
                                            
                                        </div>
                                </div>
                                {{-- <div class="form-group">
                                        {!! Form::label('transferDate', 'Transfer Date:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                 
                                            {!! Form::text('transferDate', "", ['id' => 'transferDate','class'=>'form-control','style'=>'cursor: pointer','readonly']) !!}
                                            <p><span id="transferDatee" style="display: none;color: red">*Required</span></p>
                                        </div>
                                </div> --}}

                                
                                </div>{{-- End Col 6 --}}

                               
                            
                            </div>
                            
                        </div>{{-- End Col 12 --}}
                        
                    </div>                   
                </div>  
            </div>{{-- End Panel Border --}}


            <div class="panel panel-default panel-border" style="padding-left: 0px;padding-right: 0px;">
                <div class="panel-body">
                    {{-- Filtering Inputs --}}
                    <div class="col-md-12" style="padding-right: 0px;">

                        <div class="form-group col-sm-4" style="padding-right: 2%;">

                            {!! Form::label('branchId', 'Branch:', ['class' => 'control-label']) !!}

                            <select  id="branchId" class="form-control input-sm"  @if($userBranchId!=1) {{"disabled=disabaled"}} @endif >
                                <option value="" >Please Select</option>
                                @foreach($branches as $branch)
                                    <option value={{$branch->id}} @if($userBranchId==$branch->id) {{"selected=selected"}} @endif>{{str_pad($branch->branchCode,3,'0',STR_PAD_LEFT).'-'.$branch->name}} </option>
                                @endforeach

                            </select>
                        </div>                        

                        <div class="form-group col-sm-4" style="padding-right: 2%;">
                            {!! Form::label('productTypeId', 'Product Type:', ['class' => 'control-label']) !!}

                            <select  id="productTypeId" class="form-control input-sm">
                                <option value=""  selected="selected">Please Select</option>
                                @foreach($productTypes as $productType)
                                    <option value={{$productType->id}}>{{str_pad($productType->productTypeCode,3,'0',STR_PAD_LEFT).'-'.$productType->name}}</option>
                                @endforeach

                            </select>
                        </div>

                        <div class="form-group col-sm-4" style="padding-right: 2%;">

                            {!! Form::label('productNameId', 'Product Name:', ['class' => 'control-label',]) !!}

                            <select  id="productNameId" class="form-control input-sm" style="padding-right: 0px;">
                                <option value="" selected="selected">Please Select</option>
                                @foreach($productNames as $productName)
                                    <option value={{$productName->id}}>{{str_pad($productName->productNameCode,3,'0',STR_PAD_LEFT).'-'.$productName->name}}</option>
                                @endforeach
                            </select>

                        </div>

                    </div>
                    {{-- End Filtering Input --}}
                    <div class="col-md-12" style="padding-right: 0px;">
                        <div class="col-md-4" style="padding-right: 2%;">
                            {!! Form::label('product', 'Product:', ['class' => 'control-label']) !!}
                            <select id="filProduct" class="form-control">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{$product->id}}" >{{$prefix.$product->productCode}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4" style="text-align:center">
                            {!! Form::label('addProduct', 'Add to Selected List', ['class' => 'control-label']) !!}
                            <div >
                                {!! Form::button('<i class="fa fa-chevron-right" aria-hidden="true"></i><i class="fa fa-chevron-right" aria-hidden="true"></i>',['class'=>'btn btn-primary btn-xs','id'=>'addToList']) !!}
                            </div>

                        </div>
                        {!! Form::hidden('productSelectedId',null,['id'=>'productSelectedId']) !!}
                        <div class="col-md-4" style="padding-right: 2%;">
                            {!! Form::label('addProduct', 'Selected Product', ['class' => 'control-label']) !!}
                            {!! Form::text('productSelected', $value = null, ['class' => 'form-control', 'id' => 'productSelected', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            <p><span id="productSelectede" style="display: none;color: red">*Required</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default panel-border" style="padding-left: 0px;padding-right: 0px;">
                <div class="panel-body">
                
                <div class="col-md-12" style="padding-right: ; padding-left: 15px;">
                
                <div class="form-horizontal form-groups">

                <div class="col-sm-4" >
                <div class="form-group">
                    {!! Form::label('projectTo', 'Project To:', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                      <?php 
                        $projectTo = DB::table('gnr_project')->select('name','id','projectCode')->orderBy('projectCode','asc')->get(); 
                      ?>
                      <select name="projectTo" id="projectTo" class="form-control">
                          <option value="">Please Select Project</option>
                          @foreach ($projectTo as $projTo)
                              <option value="{{$projTo->id}}">{{str_pad($projTo->projectCode,3,"0",STR_PAD_LEFT).'-'.$projTo->name}}</option>
                          @endforeach
                      </select>    
                    
                    <p><span id="projectToe" style="display: none;color: red">*Required</span></p>
                           
                    </div>
                </div>                  
                </div>

                <div class="col-sm-4" >
                <div class="form-group">
                    {!! Form::label('projectTypeTo', 'Project Type To:', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                      <?php 
                        $projectTypeTo =  DB::table('gnr_project_type')->select('name','id','projectTypeCode')->get(); 
                      ?>
                      <select name="projectTypeTo" id="projectTypeTo" class="form-control">
                          <option value="">Please Select Project Type</option>
                          @foreach ($projectTypeTo as $proTypeTo)
                              <option value="{{$proTypeTo->id}}">{{str_pad($proTypeTo->projectTypeCode,3,"0",STR_PAD_LEFT).'-'.$proTypeTo->name}}</option>
                          @endforeach
                      </select>   
                        <p><span id="projectTypeToe" style="display: none;color: red">*Required</span></p>   
                    </div>
                </div>                  
                </div>

                <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('branchTo', 'Branch To:', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                      <?php 
                        $branchesTo = DB::table('gnr_branch')->select('name','id','branchCode')->get(); 
                      ?>
                      <select name="branchTo" id="branchTo" class="form-control">
                          <option value="">Please Select Branch</option>
                          @foreach ($branchesTo as $branchTo)
                              <option value="{{$branchTo->id}}">{{str_pad($branchTo->branchCode,3,"0",STR_PAD_LEFT).'-'.$branchTo->name}}</option>
                          @endforeach
                      </select>   
                    
                    <p><span id="branchToe" style="display: none;color: red">*Required</span></p>
                    <p><span id="Samebranche" style="display: none;color: red">*It is already in this Branch.</span></p>
                           
                    </div>
                </div>                  
                </div>
                 
                                
                </div>
                </div>


                <div class="row">
                    
                
                <div class="col-md-12" style="padding-right: auto; padding-left: auto;">
                
                <div class="form-horizontal form-groups">
                <div class="col-sm-6">
                <div class="form-group" style="padding-left: 15px;text-align: center;">
                    {!! Form::label('newProductCode', 'New Product ID:', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                         
                    {!! Form::text('newProductCode',"",['class'=>'form-control','id'=>'newProductCode','readonly']) !!}
                           
                    </div>
                </div>                  
                </div>
                
                <div class="col-sm-6" style="padding-right: 25px;">
                    <div class="form-group">
                            {!! Form::label('transferDate', 'Transfer Date:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                                                 
                                {!! Form::text('transferDate', "", ['id' => 'transferDate','class'=>'form-control','style'=>'cursor: pointer','readonly']) !!}
                                <p><span id="transferDatee" style="display: none;color: red">*Required</span></p>
                            </div>
                    </div>
                </div>

                </div>


                </div>
                </div>
                 <div class="row">
                    <div class="form-group col-md-12 text-right" style="padding-right: 3%;">
                {!! Form::submit('Submit',['class'=>'btn btn-info','id'=>'submit']) !!}
                <a href="{{url('famsViewTransfer/')}}" class="btn btn-danger closeBtn">Close</a>
                </div>
                </div>

                {!! Form::close() !!}
                
                </div>
            </div>


             
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
            <div class="col-md-1"></div>
    </div>
</div>


{{-- Filtering --}}
<script type="text/javascript">
    $(document).ready(function () {

        var prefix = "{{$prefix}}";

        function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

        /* Change Branch*/

        $("#branchId").change(function(){

            var branchId = $(this).val();            
            var productTypeId = $("#productTypeId").val();
            var productNameId =  $("#productNameId").val();

            // alert(branchId);
            // alert(productTypeId);
            // alert(productNameId);
            

            var csrf =  "{{csrf_token()}}";

            $.ajax({
                type: 'post',
                url: './famsOnChangeBranch2',
                data: {branchId:branchId,productNameId: productNameId,productTypeId: productTypeId,_token: csrf},
                dataType: 'json',
                success: function( _response ){


                    $("#filProduct").empty();
                    $("#filProduct").prepend('<option selected="selected" value="">Select Product</option>');


                    $.each(_response, function (key, value) {
                        {

                            if (key == "productList") {
                                $.each(value, function (key1,value1) {

                                    $('#filProduct').append("<option value='"+ value1+"'>"+prefix+key1+"</option>");

                                });
                            }
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

        }); /*End Change Branch*/


        
        
         /* Change Product Type*/
         $("#productTypeId").change(function(){
            var branchId = $("#branchId option:selected").val();
            var productTypeId = $(this).val();


            var csrf = "<?php echo csrf_token(); ?>";
            
            $.ajax({
                type: 'post',
                url: './famsOnChangeProductType',
                data: {branchId: branchId, productTypeId:productTypeId,_token: csrf},
                dataType: 'json',
                success: function( _response ){
                    
                    $("#productNameId").empty();
                    $("#productNameId").prepend('<option selected="selected" value="">Please Select</option>');

                    $("#filProduct").empty();
                    $("#filProduct").prepend('<option selected="selected" value="">Select Product</option>');                   

                    $.each(_response, function (key, value) {
                        {
                            
                            if (key == "productNameList") {
                                $.each(value, function (key1, obj) {

                                    $('#productNameId').append("<option value='"+ obj.id+"'>"+pad(obj.productNameCode,3)+'-'+obj.name+"</option>");
                                });
                            }

                            if (key == "productList") {
                                $.each(value, function (key1,value1) {
                                    $('#filProduct').append("<option value='"+ value1+"'>"+prefix+key1+"</option>");
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


        /*Change Product Name*/
        $("#productNameId").change(function(){

            var branchId = $("#branchId").val();
            var productNameId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsOnChangeName',
                data: {branchId: branchId,productNameId: productNameId,_token: csrf},
                dataType: 'json',
                success: function( _response ){                    


                    $("#filProduct").empty();
                    $("#filProduct").prepend('<option selected="selected" value="">Select Product</option>');

                    $.each(_response, function (key, value) {
                        {


                            if (key == "productList") {
                                $.each(value, function (key1,value1) {

                                    $('#filProduct').append("<option value='"+ value1+"'>"+prefix+key1+"</option>");

                                });
                            }
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

        }); /*End Change Product Name*/

    });

</script>
    {{--End Filtering--}}


<script type="text/javascript">
  $(document).ready(function() {

    //alert($("#branchId").val());
    $("#branchId").trigger( "change" );

String.prototype.splice = function(idx, rem, str) {
 return this.slice(0, idx) + str + this.slice(idx + Math.abs(rem));
 };
 function pad (str, max) {
      str = str.toString();
      return str.length < max ? pad("0" + str, max) : str;
    }

    function toDateString(dateStr) {
       var parts = dateStr.split("-");
       return parts[2]+'-'+ parts[1]+'-'+ parts[0];
    }


    $("#addToList").on('click',function () {
                $("#productSelectede").hide();
                var productId = $("#filProduct option:selected").val();
                var productCode = $("#filProduct option:selected").html();

                if(productId!=""){
                    $("#productSelectedId").val(productId);
                    $("#productSelected").val(productCode);                 
                    $("#newProductCode").val(productCode);                 
                    var csrf =  "{{csrf_token()}}";                   

                    $.ajax({
                        type: 'post',
                        url: './famsTransferGetProductInfo',
                        data: {productId:productId, _token: csrf},
                        dataType: 'json',
                        success: function( data ){
                            $("#projectFromId").val(data['projectFromId']);
                            $("#projectFrom").val(data['projectFrom']);
                            $("#projectTypeFromId").val(data['projectTypeFromId']);
                            $("#projectTypeFrom").val(data['projectTypeFrom']);
                            $("#branchFromId").val(data['branchFromId']);
                            $("#branchFrom").val(data['branchFrom']);
                            $("#costPrice").val(data['costPrice']);
                            $("#depGenerated").val(data['depGenerated']);
                            $("#remainingDep").val(data['remainingDep']);
                            $("#purchaseDate").val(toDateString(data['purchaseDate']));
                            
                            $("#transferNo").val(data['transferId']);
                            $("#transferDate").datepicker("option","minDate",new Date(data['purchaseDate']));
                        }
                    });                    
                }

                $("#projectTo").val("");
                $("#projectTypeTo").val("");
                $("#branchTo").val("");
            });


    $("#projectTo").change(function() {
        $("#projectToe").hide();
        var projectId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                success: function( _response ){


                    $("#projectTypeTo").empty();
                    $("#projectTypeTo").prepend('<option selected="selected" value="">Please Select Project Type</option>');

                    $("#branchTo").empty();
                    $("#branchTo").prepend('<option selected="selected" value="">Please Select Branch</option>');
                   

                    $.each(_response, function (key, value) {
                        {
                             if (key == "projectTypeList") {
                                $.each(value, function (key1,proTypeObj) {
                                    $('#projectTypeTo').append("<option value='"+ proTypeObj.id+"'>"+pad(proTypeObj.projectTypeCode,3)+"-"+proTypeObj.name+"</option>");
                                });
                            }

                            if (key == "branchList") {
                                $.each(value, function (key2,branchObj) {
                                    $('#branchTo').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                                });
                            }

                            
                           
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/


            //Change Product Code When ProjectTo Change

            

            var newProductCode = $("#newProductCode").val();
            if(newProductCode!=""){
                var text = newProductCode;
                var key = "project";
                var prefix = "{{$prefix}}";

                if (projectId=="" || $("#projectFromId").val()==projectId) {
                    var initialProductCode = $("#productSelected").val();
                    var oldPieces =  initialProductCode.split("-");
                    var newPieces =  text.split("-");

                    if (prefix=="") {
                        text = newPieces[0]+'-'+oldPieces[1]+'-'+oldPieces[2]+'-'+newPieces[3]+'-'+newPieces[4]+'-'+newPieces[5]+'-'+newPieces[6];
                    }
                    else{
                        text = newPieces[0]+'-'+newPieces[1]+'-'+oldPieces[2]+'-'+oldPieces[3]+'-'+newPieces[4]+'-'+newPieces[5]+'-'+newPieces[6]+'-'+newPieces[7];    
                    }

                    

                   
                    $("#newProductCode").val(text);
                }



                else{

                    $.ajax({
                        type: 'post',
                        url: './famsGetInfo',
                        data: {key: key, projectId: projectId, _token: csrf},
                        dataType: 'json',
                        success: function (data) {

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
                            

                            $("#newProductCode").val(text);

                        },
                        error: function(_response){
                            alert("Error");
                        }
                    });

                }

                
            }
    });/*End Change ProjectTo*/


    $("#projectTypeTo").change(function() {
        $("#projectTypeToe").hide();
    });


    $("#branchTo").change(function () {
            $("#branchToe").hide();
            $("#Samebranche").hide();
            var key = "branch";
            var branchId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";
            var text = $("#newProductCode").val();
            var prefix = "{{$prefix}}";

            if (text!="") {

                if (branchId=="" || branchId==$("#branchFromId").val()) {
                    var initialProductCode = $("#productSelected").val();
                    var oldPieces =  initialProductCode.split("-");
                    var newPieces =  text.split("-");

                    if (prefix=="") {
                    text = newPieces[0]+'-'+newPieces[1]+'-'+newPieces[2]+'-'+oldPieces[3]+'-'+oldPieces[4]+'-'+newPieces[5]+'-'+newPieces[6];    
                    }
                    else{
                    text = newPieces[0]+'-'+newPieces[1]+'-'+newPieces[2]+'-'+newPieces[3]+'-'+oldPieces[4]+'-'+oldPieces[5]+'-'+newPieces[6]+'-'+newPieces[7];    
                    }
                    
                    
                    $("#newProductCode").val(text);
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
                            
                            $("#newProductCode").val(text);

                        },
                        error: function(_response){
                            //alert("Error");
                        }
                    });

                }  

            }

            
        });

  });
</script>

{{-- Submit the data --}}

<script type="text/javascript">
    $(document).ready(function() {
        $("#submit").on('click', function(event) {            

            var productSelected = $("#productSelected").val();
            var projectTo = $("#projectTo").val();
            var projectTypeTo = $("#projectTypeTo").val();
            var branchFromId = $("#branchFromId").val();
            var branchFrom = $("#branchFrom").val();
            var branchTo = $("#branchTo option:selected").val();
            var transferDate = $("#transferDate").val();


            if (branchFromId==branchTo) {
                event.preventDefault();
                $("#Samebranche").show();
            }
            
            if(productSelected==""){
                event.preventDefault();
                $("#productSelectede").show();
                
            }

            /*if(projectTo==""){
                event.preventDefault();
                $("#projectToe").show();
            }
            if(projectTypeTo==""){
                event.preventDefault();
                $("#projectTypeToe").show();
            }*/
            if(branchTo==""){
                event.preventDefault();
                $("#branchToe").show();
            }

            if(transferDate==""){
                event.preventDefault();
                $("#transferDatee").show();
            }            

            
        });
    });
</script>

{{-- Transfer Date --}}
 <script type="text/javascript">
    $(document).ready(function() {
        
        $("#transferDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1998:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#transferDatee').hide();               
            }
        });
    });
</script> 
{{-- End Transfer Date --}}

 


@endsection





