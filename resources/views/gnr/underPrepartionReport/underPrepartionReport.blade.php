@extends('layouts/microfin_layout')
@section('title', '| POMIS-2 Report')
@section('content')
<style type="text/css">
.form-group, .form-control{
    font-size: 11px !important;
    color: black !important;
}
.form-control{
    padding: 5px !important;
}
#reportSubmit{
    font-size: 12px;
    margin-top: 20px;
}
</style>
<script type="text/javascript">
    $("#loadingModal").show();
    $(document).keydown(function(e) {
        if (e.keyCode == 27) return false;
    });
</script>

<div class="row">
    <div class="col-md-12"><h1 style="color:red" align="center">This Report is Under Preparation </h1> 
        <!--<div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                            <button id="printIcon" class="btn btn-info pull-right print-icon"  target="_blank" style="">
                                <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                            </button>
                        </div>
                        <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">POMIS-2 Report</h3>
                    </div>

                </div>
            </div>
        </div>-->
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        $("form").submit(function( event ) {
            event.preventDefault();

            var serializeValue=$(this).serialize();
        //alert(serializeValue);
        
        $('#loadingModal').show();
        $("#reportingDiv").load('{{URL::to("mfn/pksfPomisReport/getpomis2Report")}}'+'?'+serializeValue);
    });



    });


</script>


<script type="text/javascript">
    $(document).ready(function() {        

        $("#printIcon").click(function(event) {

        // $("#hiddenTitle").show();
        // $("#hiddenInfo").show();
        // $("#loanNSavingTable1").removeClass('table table-striped table-bordered');

        var mainContents = document.getElementById("printDiv").innerHTML;
        var headerContents = '';
        // var printContents = '<div id="order-details-wrapper">' + headerContents + mainContents +'</div>';


        var printStyle = '<style>#pomisOne1, #pomisOne2, #pomisOne3{float:left;height:auto;padding:0px;width:100% !important;font-size:11px;border:1pt ash;page-break-inside:auto;font-family:Arial;}  thead tr th{text-align:center;vertical-align: top;padding:3px;font-size:11px} #pomisOne1 thead tr th:nth-child(1){ width: 50px;} thead tr th:nth-child(10){ width: 40px;} tbody tr td:nth-child(10){ width: 40px;} tbody tr td.amount {border: 1px solid ash;text-align:right;vertical-align: middle;padding:3px;font-size:11px} tbody tr td {border: 1px solid ash;text-align:center;vertical-align: middle;padding:3px;font-size:11px} td:nth-child(1){text-align:left;} tr{ page-break-inside:avoid; page-break-after:auto } tr:last-child { font-weight: bold;} .name{text-align:left;vertical-align:left;}</style><style media="print">@page{margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{#pomisOne1 tr:nth-of-type(10n){page-break-after: always;}} tbody tr:nth-child(1) td:nth-child(2){text-align:left;}</style>';


        /*tr:nth-of-type(10n){page-break-after: always;}*/
       // @media print {.element-that-contains-table { overflow: visible !important; }}

       var printContents = '<div id="order-details-wrapper">' + headerContents +printStyle+ mainContents +'</div>';


        /*document.body.innerHTML = printStyle + printContents;
        window.print();*/

        var win = window.open('','printwindow');
        win.document.write(printContents);
        win.print();
        // $("#loanNSavingTable1").addClass('table table-striped table-bordered');
        win.close();
    });

        $("#loadingModal").hide();

        /*on change filReportLevel hide/show contents*/
        $("#filReportLevel").change(function(event) {
            if ($(this).val()=="Branch") {
                $("#branchDiv").show();  
                $("#areaDiv").hide();  
                $("#zoneDiv").hide();  
                $("#regionDiv").hide(); 
                
                $("#filArea").prop('required', false);
                $("#filZone").prop('required', false);
                $("#filRegion").prop('required', false); 
            }
            else if($(this).val()=="Area"){
                $("#branchDiv").hide(); 
                $("#areaDiv").show(); 
                $("#zoneDiv").hide(); 
                $("#regionDiv").hide(); 

            //$("#filBranch").prop('required', false);
            $("#filArea").prop('required', true);
            $("#filZone").prop('required', false);
            $("#filRegion").prop('required', false);
        }
        else if($(this).val()=="Zone"){
            $("#branchDiv").hide(); 
            $("#areaDiv").hide(); 
            $("#zoneDiv").show(); 
            $("#regionDiv").hide(); 

            //$("#filBranch").prop('required', false);
            $("#filArea").prop('required', false);
            $("#filZone").prop('required', true);
            $("#filRegion").prop('required', false);
        }
        else if($(this).val()=="Region"){
            $("#branchDiv").hide(); 
            $("#areaDiv").hide(); 
            $("#zoneDiv").hide(); 
            $("#regionDiv").show(); 

            //$("#filBranch").prop('required', false);
            $("#filArea").prop('required', false);
            $("#filZone").prop('required', false);
            $("#filRegion").prop('required', true);
        }        
    });
        /*end on change filReportLevel hide/show contents*/
        $("#filReportLevel").trigger('change');

    }); /* Ready*/
</script>



@endsection
