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
                            {{--     <a href="" class="btn btn-info pull-right addViewBtn" id="print"><i class="fa fa-print" aria-hidden="true"></i> Print</a> --}}
                            <button id="print" class="btn btn-info pull-left print-icon"  target="_blank" style="">
                                <i class="fa fa-print fa-lg" aria-hidden="true">Print</i>
                            </button>

                            <button id="btnExportExcel" class="btn btn-info pull-center print-icon"  target="_blank" style="">
                                <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Excel</i>
                            </button>

                            <button  id="btnExportPdf" class="btn btn-info pull-right print-icon"  target="_blank" style="">
                                <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Pdf</i>
                            </button>

                        </div>


                        
                        <div class="row" id="filtering-group">

                            <div class="form-horizontal form-groups" style="padding-right: 0px;">

                                {!! Form::open(['url' => 'famsFixedAssetsIdPrintReport','method' => 'get']) !!}
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
                            {!! Form::label('', 'Pro. Name:', ['class' => 'control-label pull-left']) !!}
                        </div> 
                        <div class="col-sm-12">
                            <select name="searchProductName" class="form-control input-sm" id="searchProductName">
                                <option value="">All</option>
                                @foreach($productNames as $productName)
                                <option value="{{$productName->id}}" @if($productName->id==$productNameSelected){{"selected=selected"}}@endif>{{$productName->name}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>

                <div class="col-md-1">
                    <div class="form-group" style="font-size: 13px; color:black;">
                       <div style="text-align: center;" class="col-sm-12">
                        {!! Form::label('', 'Product:', ['class' => 'control-label pull-left']) !!}
                    </div> 
                    <div class="col-sm-12">
                        <select name="searchProduct" class="form-control input-sm" id="searchProduct">
                            <option value="">All</option>
                            @foreach($products as $pObject)
                            <option value="{{$pObject->id}}" @if($pObject->id==$productSelected){{"selected=selected"}}@endif>{{$pObject->productCode}}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
            </div>

            <div id="searchMethodGroup">
                <div class="col-md-1">
                    <div class="form-group" style="font-size: 13px; color:black;">
                        <div style="text-align: center;" class="col-sm-12">
                            {!! Form::label('', 'Search By:', ['class' => 'control-label pull-left']) !!}
                        </div>

                        <div class="col-sm-12">
                            {!! Form::select('searchMethod',[''=>'Please Select','1'=>'Fiscal Year','2'=>'Current Year','3'=>'Date Range','4'=>'Transfer Date'],$searchMethodSelected,['id'=>'searchMethod','class'=>'form-control input-sm']) !!}

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


    <h2 align="center" style="font-family: Antiqua;letter-spacing: 2px; color: white; margin-top: 0px;">Fixed Asset's ID Print Report</h2>
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

                                  {{--  <div style="display: none;text-align: center;" id="hiddenTitle">
                           <h3 style="text-align: center;padding: 0px;margin: 0px;">Ambala Foundation</h3>
                           <p style="text-align: center;padding: 0px;margin: 0px;font-size: 10px;">House # 62, Block # Ka, Piciculture Housing Society, Shyamoli, Dhaka-1207</p>
                           <h4 style="text-align: center;padding: 0px;margin: 0px;">Fixed Assets Register</h4>                          
                            
                           <h5 style="text-align: center;font-size: 14;padding: 0px;margin: 0px;">{{date('F d, Y',strtotime($endDate))}}</h5>
                       </div>  --}}

                         {{--                 <div id="hiddenInfo" style="display: none;">                       

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

                        --}}    <div id="tableDiv">
                            <table id="famsAssetIdPrintReportTable" class="table table-striped table-bordered" style="color:black;font-size:11px;margin-bottom:50px;border-collapse: collapse;" border= "1px solid ash;" cellpadding="0" cellspacing="0"> 
                                @php
                                $lastIndex = sizeof($products);
                                @endphp                                   

                                <tbody>

                                    @foreach($products as $key => $product)

                                    @if(((int)$key)%2==0)
                                    <tr>
                                    <td width="50%">{{$product->productCode}}</td>
                                    @if($key==$lastIndex-1)
                                    <td width="50%"></td>
                                    @endif

                                    @endif

                                    @if(((int)$key)%2!=0)

                                    <td width="50%">{{$product->productCode}}</td>
                                    </tr>

                                    @endif
                                    @endforeach


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
                                        else if(searchMethod==3 || searchMethod==4){
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
                                              download: "Fixed Asset's ID Print Report_"+ today + ".xls"}).appendTo("body").get(0).click();
                                          e.preventDefault();
                                      });

                                     });
                                       $("#print").click(function(event) {

                                        $("#hiddenTitle").show();
                                        $("#hiddenInfo").show();
                                        $("#famsAssetIdPrintReportTable").removeClass('table table-striped table-bordered');

                                        var mainContents = document.getElementById("printingContent").innerHTML;
                                        var headerContents = '';

                                        var footerContents = "<div class='row' style='font-size:12px;'>Prepared By <span style='display:inline-block; width: 40%;'></span> Checked By <span style='display:inline-block; width: 40%;'></span> Approved By</div>";



                                        /*var printStyle = '<style>#famsAssetRegisterReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt ash;page-break-inside:auto;} th:nth-child(4){ width: 200px;} td:nth-child(4){ width: 200px;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px} tbody tr td {text-align:left;vertical-align: middle;padding:3px;font-size:10px} tr{ page-break-inside:avoid; page-break-after:auto } tr:last-child { font-weight: bold;} .name{text-align:left;vertical-align:left;}.amount{text-align:right;}</style><style media="print">@page{size:landscape;margin:20mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style>';*/

                                        var printStyle = '<style>#famsAssetIdPrintReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt ash;page-break-inside:auto;}  tbody tr td {border: 1px solid ash;text-align:center;vertical-align: middle;padding:3px;font-size:20px;height:50;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:landscape;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{#famsAssetIdPrintReportTable tr:nth-of-type(10n){page-break-after: always;}}</style>';

                                        printContents = '<div id="order-details-wrapper">' + printStyle + mainContents +'</div>';

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


  /* Change Product Type*/
  $("#searchProductType").change(function(){
    var productTypeId = $(this).val();

    var csrf = "<?php echo csrf_token(); ?>";


    $.ajax({
        type: 'post',
        url: './famsAddProductOnChangeProductType',
        data: {productTypeId:productTypeId,_token: csrf},
        dataType: 'json',
        success: function( _response ){

            $("#searchProductName").empty();
            $("#searchProductName").prepend('<option selected="selected" value="">All</option>');                   

            $.each(_response, function (key, value) {
                {

                    if (key == "productNameList") {
                        $.each(value, function (key1,obj) {
                            $('#searchProductName').append("<option value='"+ obj.id+"'>"+obj.name+"</option>");
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


  /* Change Product Name*/
  $("#searchProductName").change(function(){
    var productNameId = $(this).val();

    var csrf = "<?php echo csrf_token(); ?>";


    $.ajax({
        type: 'post',
        url: './famsAddProductOnChangeProductName',
        data: {productNameId:productNameId,_token: csrf},
        dataType: 'json',
        success: function( _response ){

            $("#searchProduct").empty();
            $("#searchProduct").prepend('<option selected="selected" value="">All</option>');                   

            $.each(_response, function (key, value) {
                {

                    if (key == "productList") {
                        $.each(value, function (key1,value1) {
                            $('#searchProduct').append("<option value='"+ value1+"'>"+key1+"</option>");
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

<script type="text/javascript">
jQuery(document).ready(function($) {
    $("#search").click(function(event) {


        if ($("#searchMethod").val()==2 || $("#searchMethod").val()==3 || $("#searchMethod").val()==4) {

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

<script type="text/javascript">
jQuery(document).ready(function($) {
    $("#searchProduct").change(function() {

        if (this.value=="") {

            $("#searchMethodGroup").show();                
        }
        else{

            $("#searchMethodGroup").hide();                
        }
    });

    $("#searchProduct").trigger('change');
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


@endsection