@extends('layouts/pos_layout')
@section('title', '| Pos Purchase Report')
@section('content')
@include('successMsg')

<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$gnrBranchId = Session::get('branchId');
$logedUserName = $user->name;
if(isset($_GET['salesTypeId'])) {
  $salesTypeId =$_GET['salesTypeId'];   
} else {
 $salesTypeId =0; 
}

?>
<!-- Start of the Dropdown form -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default" style="background-color:#708090;">
      <div class="panel-heading" style="padding-bottom:0px;">
        <div class="panel-options">
          <button id="printIcon" class="btn btn-info pull-right print-icon"  target="_blank" style="">
            <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
          </button>

          <button id="btnExportExcel" class="btn btn-info pull-center print-icon"  target="_blank" style="">
            <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Excel</i>
          </button>

          <button  id="btnExportPdf" class="btn btn-info pull-right print-icon"  target="_blank" style="">
            <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Pdf</i>
          </button>
        </div>
        <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white; padding-left: 220px">
         Purchase Return Report
        </h3>
      {{--   <h4 style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">
          Summery Sheet
        </h4> --}}
        
      </div>

      <div class="panel-body panelBodyView">
        <!-- Filtering Start-->
      {!! Form::open(array('url' => './pos/posPurchaseReturnGetReport', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'salesForm','method' => 'get')) !!}

        <div class="row">
          {{-- Start of Branch Name --}}
          <div class="col-md-12">

             <div class="col-md-2">
          <div class="form-group">
            {!! Form::label('startDate', 'Start Date:', ['class' => 'col-md-12 control-label']) !!}
            <div class="col-md-12">
            {!! Form::text('startDate', $value = null, ['class' => 'form-control', 'id' => 'startDate','required'=>'required','type' => 'text','placeholder' => 'Enter Start Date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
    
            </div> 
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            {!! Form::label('endDate', 'End Date:', ['class' => 'col-md-12 control-label']) !!}
            <div class="col-sm-12">
          {!! Form::text('endDate', $value = null, ['class' => 'form-control', 'id' => 'endDate', 'required'=>'required','type' => 'text','placeholder' => 'Enter End Date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
            </div> 
          </div>
        </div>

         

      

         <div class="col-md-2">
          <div class="form-group">
            {!! Form::label('productId', 'Product:', ['class' => 'col-md-12 control-label']) !!}
            <div class="col-sm-12">
          <select name="billNo" style="width:200px"   id="billNo" class="form-control">
              <option value="" selected="selected">Select Purchase</option>
             <option value="" >All</option>
              @foreach($purchaseNo as $purchaseNos)
              <option value="{{$purchaseNos->id}}">{{$purchaseNos->billNo}}</option>
              @endforeach
            </select>
          </div> 
        </div>
      </div>

       

            {{-- Start of the Submit Button --}}
            <div class="col-md-2">
              <div class="form-group">
                {{-- {!! Form::label('', '', ['class' => 'control-label col-md-12', 'style' => 'color:#708090; padding-top: 25px;']) !!} --}}
                <div class="col-md-12">
                  <label for="year" class="control-label">Action</label><br>
                  <input class="btn btn-primary" type="submit" name="Show" value="Search">
                </div>
              </div>
            </div>
            {{-- End of the Submit button --}}
          </div>
          <!-- filtering end -->
        </div>
        {!! Form::close()  !!}
      </div>

      {{-- Start of the table --}}
      <div class="row" id="reportingDiv">
      </div>
      {{-- End of the table --}}
    </div>      {{-- panel-body panelBodyView DIV --}}
  </div>
</div>
<!-- End of Dropdown form -->


<script type="text/javascript">
$(document).ready(function() {

    $( "#startDate" ).datepicker({
      dateFormat: "dd-mm-yy",
      showOtherMonths: true,
      selectOtherMonths: true,
      changeMonth: true,
      changeYear: true,
      yearRange: "-20:+0"
    });

      $( "#endDate" ).datepicker({
      dateFormat: "dd-mm-yy",
      showOtherMonths: true,
      selectOtherMonths: true,
      changeMonth: true,
      changeYear: true,
      yearRange: "-20:+0"
    });
});

$('.select2').select2();

</script>

<script type="text/javascript">
  $("form").submit(function( event ) {
   event.preventDefault();

   var serializeValue=$(this).serialize();

   $("#reportingDiv").load('{{URL::to("./pos/posPurchaseReturnGetReport")}}'+'?'+serializeValue);
 });

  $(document).ready(function() {

    $("#printIcon").click(function(event) {

      var mainContents = document.getElementById("reportingDiv").innerHTML;
      var headerContents = '';

      var printStyle = '<style>thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px}  tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

      var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By: <span style='display:inline-block; width: 30%; padding-top:40px;'></span> Reviewed By: <span style='display:inline-block; width: 34%; padding-right:15px; padding-top:40px;'></span>Approved By:</div>";

      var printContents = '<div id="order-details-wrapper">' + headerContents +printStyle+ mainContents + footerContents + '</div>';

      var win = window.open('','printwindow');
      win.document.write(printContents);
      win.print();
      win.close();
    });

    $("#loadingModal").hide();

  }); /* Ready to print */
</script>

@endsection
