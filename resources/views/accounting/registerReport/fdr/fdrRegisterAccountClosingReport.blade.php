@extends('layouts/acc_layout')
@section('title', '| FDR Report')
@section('content')


@php
    $projectSelected = isset($_GET['searchProject']) ? $_GET['searchProject'] : null;
    $projectTypeSelected = isset($_GET['searchProjectType']) ? $_GET['searchProjectType'] : null;
    $branchSelected = isset($_GET['searchBranch']) ? $_GET['searchBranch'] : null;

    $fdrTypeSelected = isset($_GET['searchFdrType']) ? $_GET['searchFdrType'] : null;
    $bankNameSelected = isset($_GET['searchBankName']) ? $_GET['searchBankName'] : null;
    $bankBranchLocationSelected = isset($_GET['searchBankBranchLocation']) ? $_GET['searchBankBranchLocation'] : null;

    
    $dateFromSelected = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : null;
    $dateToSelected = isset($_GET['dateTo']) ? $_GET['dateTo'] : null;
    $firstRequest = isset($_GET['firstRequest']) ? '1' : null;

    $userBranchId = Auth::user()->branchId;

   
@endphp


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options"> 
          <div class="panel-options">
                <a href="" class="btn btn-info pull-right addViewBtn" id="print"><i class="fa fa-print" aria-hidden="true"></i> Print</a>

            </div>
              
          </div>
          <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">FDR Account Closing Report</h3>
        </div>
        {{-- <h5>kxghixd</h5> --}}
        
        <div class="panel-body panelBodyView"> 

      
            <!-- Filtering Start-->
           <div class="row" id="filtering-group">
                            
                                <div class="form-horizontal form-groups" style="padding-right: 0px;">

                                    {!! Form::open(['url' => 'viewFdrAccountClosingReport','method' => 'get']) !!}
                                   

                                    {!! Form::hidden('firstRequest',0) !!}
                                
                                    
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                             <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Project:', ['class' => 'control-label pull-left']) !!}
                                            </div> 

                                            <div class="col-sm-12">
                                                <select name="searchProject" class="form-control input-sm" id="searchProject">
                                                   @if($userBranchId==1) <option value="">All</option>   @endif                                      
                                                    @foreach($projects as $project)
                                                    <option value="{{$project->id}}" @if($project->id==$projectSelected){{"selected=selected"}}@endif>{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT).'-'.$project->name}}</option>
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
                                                <select name="searchProjectType" class="form-control input-sm" id="searchProjectType">
                                                    @if($userBranchId==1) <option value="">All</option>   @endif                                  
                                                    @foreach($projectTypes as $projectType)
                                                    <option value="{{$projectType->id}}" @if($projectType->id==$projectTypeSelected){{"selected=selected"}}@endif>{{str_pad($projectType->projectTypeCode,3,"0",STR_PAD_LEFT).'-'.$projectType->name}}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('searchBranch', 'Branch:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                <select name="searchBranch" class="form-control input-sm" id="searchBranch">
                                                     @if($userBranchId==1) 
                                                     <option value="">All</option> 
                                                     <option value="0" @if($branchSelected=='0'){{"selected=selected"}}@endif>All Branches</option> 
                                                     @endif
                                                    @foreach($branches as $branch)
                                                    <option value="{{$branch->id}}" @if($branch->id==$branchSelected){{"selected=selected"}}@endif>{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT).'-'.$branch->name}}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('searchFdrType', 'FDR Type:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                            @php
                                                $fdrTypes = array(''=>'All') + $fdrTypes;
                                            @endphp
                                               {!! Form::select('searchFdrType',$fdrTypes,$fdrTypeSelected,['id'=>'searchFdrType','class'=>'form-control input-sm']) !!}

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('searchBankName', 'Bank Name:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            @php
                                                $bankNames = array(''=>'All') + $bankNames;
                                            @endphp

                                            <div class="col-sm-12">
                                               {!! Form::select('searchBankName',$bankNames,$bankNameSelected,['id'=>'searchBankName','class'=>'form-control input-sm']) !!}

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('searchBankBranchLocation', 'Location:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                             @php
                                                $bankBranchLocations = array(''=>'All') + $bankBranchLocations;
                                            @endphp

                                            <div class="col-sm-12">
                                               {!! Form::select('searchBankBranchLocation',$bankBranchLocations,$bankBranchLocationSelected,['id'=>'searchBankBranchLocation','class'=>'form-control input-sm']) !!}

                                            </div>
                                        </div>
                                    </div>
                                    

                                   
                                  
                                    <div class="col-md-2" id="dateRangeDiv">
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
            <!-- filtering end-->



            @if($firstRequest!=null)

            @php
               if($branchSelected=='0'){
                    $selectedBranchName = "All Branches";
                   }
               else{
                $selectedBranchName = DB::table('gnr_branch')->where('id',$branchSelected)->value('name');
               }
                $selectedProjectName = DB::table('gnr_project')->where('id',$projectSelected)->value('name');
                
                

            @endphp

            <div id="printingContent">

            <div style="display: none;text-align: center;" id="hiddenTitle">
                           <h3 style="text-align: center;padding: 0px;margin: 0px;">Ambala Foundation</h3>
                           <p style="text-align: center;padding: 0px;margin: 0px;font-size: 10px;">House # 62, Block # Ka, Piciculture Housing Society, Shyamoli, Dhaka-1207</p>
                           <h4 style="text-align: center;padding: 0px;margin: 0px;">OTS Account Opening Report</h4>                          
                            {{-- <h5 style="text-align: center;">{{$selectedBranchName}}</h5>  --}}
                           <h5 style="text-align: center;font-size: 14;padding: 0px;margin: 0px;">{{date('F d, Y',strtotime($dateToSelected))}}</h5>
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
                             Reporting Peroid : {{date('d-m-Y',strtotime($dateFromSelected))." to ".date('d-m-Y',strtotime($dateToSelected))}}
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
                                    Print Date : {{date('F d,Y')}}
                                </span>                               
                                                              
                                    </p>                       
                            
                        </div>


<br>

          <table id="interestPaymentReportTable" class="table table-striped table-bordered" style="color: black;font-size:11px;border-collapse: collapse;margin-bottom:50px;" border= "1px solid black;" cellpadding="0" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th width="60">SL#</th>
                <th>Opening Date</th>
                <th>Account No</th>                        
                <th>Account Name</th>
                <th>Bank Name</th>
                <th>Bank Branch Location</th>
                <th>Interest Rate (%)</th>
                <th>Principal Amount (Tk)</th>
                
              </tr>
              
            </thead>
              
            
            </thead>
            <tbody>
            @php
                $totalAmount = 0;
            @endphp

            <tbody>
                     @foreach($fdrAccounts as $index => $fdrAccount)
                    
                     <tr>
                        <td>{{$index+1}}</td>
                        <td>{{date('d-m-Y',strtotime($fdrAccount->openingDate))}}</td>
                        <td>{{$fdrAccount->accNo}}</td>
                        <td class="name">{{$fdrAccount->accName}}</td>
                        <td class="name">{{$fdrAccount->bankName}}</td>
                        <td class="name">{{$fdrAccount->bankBranchLocation}}</td>
                        
                        <td>{{number_format($fdrAccount->interestRate,2)}}</td>
                        <td class="amount">{{number_format($fdrAccount->principalAmount,2,'.',',')}}</td>
                        
                     </tr>

                     @php
                         $totalAmount = $totalAmount + $fdrAccount->principalAmount;
                     @endphp
                     
                     @endforeach

            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="name">Total</td>
                    <td class="amount">{{number_format($totalAmount,2,'.',',')}}</td>
                </tr>
            </tfoot>
          </table>
          </div>
          @endif
        </div>
      </div>
  </div>
  </div>
</div>
</div>


<style type="text/css">
    #interestPaymentReportTable tbody tr td.amount{
        text-align: right;
        padding-right: 5px;
    }
     #interestPaymentReportTable tbody tr td.name{
        text-align: left;
        padding-left: 5px;
    }

    #interestPaymentReportTable tfoot tr td.name{
        text-align: center;
        font-weight: bold;
    }
    #interestPaymentReportTable tfoot tr td.amount{
        text-align: right;
        padding-right: 5px;
        font-weight: bold;
    }
    #interestPaymentReportTable tfoot tr td{
        background-color: #8b8d91;
        line-height: 10px;
    }
</style>



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
            yearRange : "2000:c",
            /*minDate: new Date('2016-07-01'),*/
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#dateFrome').hide();          
                $("#dateTo").datepicker("option","minDate",new Date(toDate($(this).val())));
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

    /* End Date Range To */

    var firstRequest = "{{$firstRequest}}";
    if (firstRequest=='') {
        $("#dateFrom").datepicker().datepicker("setDate", new Date());
        $("#dateTo").datepicker().datepicker("setDate", new Date());
    }


    /*Validation*/
    $("#search").click(function(event) {
    var searchInterestPayment = $("#searchInterestPayment").val();
    if ($("#dateFrom").val()=="" && searchInterestPayment=="") {
            event.preventDefault();
            $("#dateFrome").show();
        }
    if ($("#dateTo").val()=="" && searchInterestPayment=="") {
        event.preventDefault();
        $("#dateToe").show();
    }
});
    /*End Validation*/


     /*Filter Branch Location On selecting Bank*/
         $("#searchBankName").on('change', function() {
             var indexValue = $("#searchBankName option:selected").val();
             var bankName = $("#searchBankName option:selected").text();
             var csrf = "{{csrf_token()}}";

             $.ajax({
                 url: './accFdrGetBranchLocationBaseOnBankName',
                 type: 'POST',
                 dataType: 'json',
                 data: {indexValue: indexValue, bankName: bankName, _token: csrf},
             })
             .done(function(data) {
                
                $("#searchBankBranchLocation").empty();
                $("#searchBankBranchLocation").append("<option value=''>All</option>");
                $.each(data, function(index, val) {
                     $("#searchBankBranchLocation").append("<option value='"+index+"'>"+val+"</option>");
                });
               

                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });
             
         });
         /*End Filter Branch Location On selecting Bank*/





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
            var branchId = $("#searchBranch option:selected").val();

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
                    $("#searchBranch").prepend('<option selected="selected" value="">All</option>');
                    $("#searchBranch").append('<option value="0">All Branches</option>');
                   

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
                    $("#searchBranch").append('<option selected="selected" value="">All</option>');
                    $("#searchBranch").append('<option value="0">All Branches</option>');
                    

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
    $(document).ready(function() {


        /*Disable Date When Payment Id is Selected*/
        $("#searchInterestPayment").on('change', function() {
            if (this.value!='') {
                $("#dateFrom").datepicker( "option", "disabled", true );
                $("#dateTo").datepicker( "option", "disabled", true );

                $("#dateFrom").val('');
                $("#dateTo").val('');
            }
            else{
                $("#dateFrom").datepicker( "option", "disabled", false );
                $("#dateTo").datepicker( "option", "disabled", false );
            }
        });
        /*End Hide Date When Payment Id is Selected*/
        



{{-- Print Page --}}

        $("#print").click(function(event) {

            $("#hiddenTitle").show();
            $("#hiddenInfo").show();
            $("#interestPaymentReportTable").removeClass('table table-striped table-bordered');

        var mainContents = document.getElementById("printingContent").innerHTML;

        var printStyle = '<style>#interestPaymentReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;page-break-inside:auto;}  thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px} thead tr th:nth-child(1){width:30px} tr{ page-break-inside:avoid; page-break-after:auto } #interestPaymentReportTable tfooy tr td:nth-child(1){text-align:center;} </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style><style>#interestPaymentReportTable tbody tr td{text-align: center;}#interestPaymentReportTable tbody tr td.amount{text-align: right;padding-right: 5px;}#interestPaymentReportTable tbody tr td.name{text-align: left;padding-left: 5px;} tfoot tr td:nth-child(1){text-align:center} tfoot tr td:nth-child(2){text-align: right;padding-right: 5px;} tfoot tr td{ font-weight:bold;}</style>';
  

  var footerContents = "<div class='row' style='font-size:12px;'>Prepared By <span style='display:inline-block; width: 36%;'></span> Checked By <span style='display:inline-block; width: 36%;'></span> Approved By</div>";
  printContents = '<div id="order-details-wrapper">' + printStyle + mainContents + footerContents +'</div>';


        

        /*tr:nth-of-type(10n){page-break-after: always;}*/
       // @media print {.element-that-contains-table { overflow: visible !important; }}


  /*document.body.innerHTML = printStyle + printContents;
  window.print();*/

  var win = window.open('','printwindow');
win.document.write(printContents);
win.print();
win.close();
});

{{-- End Print Page --}} 

});

</script>




@endsection




