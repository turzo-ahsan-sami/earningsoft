@extends('layouts/inventory_layout')
@section('title', '| Issue Report')
@section('content')
@include('successMsg')

<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">            

                <div class="panel panel-default" style="background-color:#708090;">

                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                         {{--    <a href="" class="btn btn-info pull-right addViewBtn" id="print"><i class="fa fa-print" aria-hidden="true"></i> Print</a> --}}

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

                            {!! Form::open(['url' => 'viewIssueReport','method' => 'get']) !!}
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
                            {!! Form::label('', 'Bill No:', ['class' => 'control-label pull-left']) !!}
                        </div> 

                        <div class="col-sm-12">
                            <select name="searchBillNo" class="form-control input-sm" id="searchBillNo">
                                <option value="">All</option>
                                @foreach($billNos as $billNo)
                                <option value="{{$billNo->id}}" @if($billNo->id==$billSelected){{"selected=selected"}}@endif>{{$billNo->issueBillNo}}</option>
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

        </div> {{-- End Filtering Group --}}


        <h2 align="center" style="font-family: Antiqua;letter-spacing: 2px; color: white;margin-top: 0px;">Issue Report </h2>

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


        @endphp

        @if(!$firstRequest )
        <div id="printDiv">
            <div id="printingContent">
                <div style="display: none;text-align: center;" id="hiddenTitle">
                   <h3 style="text-align: center;padding: 0px;margin: 0px;">Ambala Foundation</h3>
                   <p style="text-align: center;padding: 0px;margin: 0px;font-size: 10px;">House # 62, Block # Ka, Piciculture Housing Society, Shyamoli, Dhaka-1207</p>
                   <h4 style="text-align: center;padding: 0px;margin: 0px;">Issue Report</h4>                          


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
              Reporting Peroid : {{date('d-m-Y',strtotime($startDate))." to ".date('d-m-Y',strtotime($endDate))}}
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
        Print Date: {{date("d-m-Y h:m a")}}
    </span>  

</p>                                

</div>
<br>

<table id="purchaseReportTable" class="table table-striped table-bordered" style="color:black;font-size:11px;margin-bottom:50px;border-collapse: collapse;" width="100%" border= "1px solid white;" cellpadding="0" cellspacing="0">
    <thead>
       <tr style="height:30px;">
        <th>SL#</th>
        <th>Issue Date</th>
        <th>Issue Bill No</th>
        <th>Branch Name</th>
        <th>Total Quantity</th>
        <th>Total Amount (Tk)</th>

    </tr>
</thead>
<tbody>
    @php                                          

    $totalIssueAmount = 0;
    $totalIssueQuantity = 0;

    @endphp

    @foreach($issue as $key => $issue)

    @php
    $branchName = DB::table('gnr_branch')->where('id',$issue->branchId)->value('name'); 
    @endphp
    <tr>
        <td style="text-align: center;">{{$key+1}}</td>
        <td style="text-align: center;">{{date('d-m-Y',strtotime($issue->issueDate))}}</td>
        <td style="text-align: center;">{{$issue->issueBillNo}}</td>
        <td style="text-align:center;">{{$branchName}}</td>
        <td style="text-align:center;">{{$issue->totalQuantity}}</td>
        <td style="text-align: right;padding-right: 5px;">{{number_format($issue->totalAmount,2)}}</td>
    </tr>

    @php
    $totalIssueAmount = $totalIssueAmount + $issue->totalAmount; 

    $totalIssueQuantity = $totalIssueQuantity + $issue->totalQuantity;
    @endphp

    @endforeach

    <tr class="totalRow">
        <td colspan="4" style="text-align: center;font-weight: bold;">Total</td> 

        <td style="text-align:center;font-weight: bold;">{{$totalIssueQuantity}}</td>                                                
        <td style="text-align: right;padding-right: 5px;font-weight: bold;">{{number_format($totalIssueAmount,2)}}</td>

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
          download: "Issue Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
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



<!--  Change Category -->
<script type="text/javascript">
  $(document).ready(function(){
    $("#searchCategory").change(function(){
        var productCategoryId = $(this).val();
        var csrf = "<?php echo csrf_token(); ?>";

        $.ajax({
            type: 'post',
            url: './invCategoryChange',
            data: {productCategoryId:productCategoryId,_token: csrf},
            async: false,
            dataType: 'json',
            success: function( data ){
                $("#searchSubCategory").empty();
                $("#searchSubCategory").prepend('<option selected="selected" value="">All</option>');

                $.each(data['subCategories'], function (key, productObj) {                       
                    $('#searchSubCategory').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");

                });

                $("#searchProduct").empty();
                $("#searchProduct").prepend('<option selected="selected" value="">All</option>');

                $.each(data['product'], function (key, productObj) {                       
                    $('#searchProduct').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");

                });


            },
            error: function(_response){
                alert("error");
            }

        });/*End Ajax*/

    });/*End Change Category*/

});
</script> 

<!--  Change Sub Category -->
<script type="text/javascript">
  $(document).ready(function(){
    $("#searchSubCategory").change(function(){
        var productSubCategoryId = $(this).val();
        var csrf = "<?php echo csrf_token(); ?>";

        $.ajax({
            type: 'post',
            url: './invSubCategoryChange',
            data: {productSubCategoryId:productSubCategoryId,_token: csrf},
            async: false,
            dataType: 'json',
            success: function( data ){


              $("#searchProduct").empty();
              $("#searchProduct").prepend('<option selected="selected" value="">All</option>');

              $.each(data['product'], function (key, productObj) {                       
                $('#searchProduct').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");

            });


          },
          error: function(_response){
            alert("error");
        }

    });/*End Ajax*/

    });/*End Change Category*/

});
</script> 



{{-- Print Page --}}
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#print").click(function(event) {

            $("#hiddenTitle").show();
            $("#hiddenInfo").show();
            $("#purchaseReportTable").removeClass('table table-striped table-bordered');

            var mainContents = document.getElementById("printingContent").innerHTML;
            var headerContents = '';

            var footerContents = "<div class='row' style='font-size:12px;'>Prepared By <span style='display:inline-block; width: 36%;'></span> Checked By <span style='display:inline-block; width: 36%;'></span> Approved By</div>";
            printContents = '<div id="order-details-wrapper">' + headerContents + mainContents + footerContents +'</div>';


            var printStyle = '<style>#purchaseReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt ash;page-break-inside:auto;} tr:last-child { font-weight: bold;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px} tbody tr td {text-align:center;vertical-align: middle;padding:3px;font-size:10px} tr:last-child { font-weight: bold;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

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


    {{-- <style type="text/css">

        #filtering-group input{
            height: auto;

            border-radius: 5px;
        }

        #filtering-group select{height:auto; border-radius: 5px;}

        .row-name{text-align: left;padding-left: 15px;}
        .row-amount{text-align: right;padding-right: 15px;}

        #famsAssetRegisterReportTable tr.totalRow td{text-align: right;padding-right: 15px;font-weight: bold;}
    </style> --}}
    <style type="text/css">
    @media print {
       thead {display: table-header-group;}
   }
</style>


<style type="text/css">

#filtering-group input{
  height:25px;
  border-radius: 0px;
}

#filtering-group select{height:25px; border-radius: 0px;}

.dataTables_filter, .dataTables_info { display: none; } 
/*.stockViewTable_length, .dataTables_paginate { display: none; }  */
</style>

<style type="text/css" media="print">
@media print {
   thead {display: table-header-group;}
}
</style>


<style type="text/css">
#purchaseReportTable thead tr th {
  background: #696969 !important;
  border-bottom: 0px !important;

}    

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