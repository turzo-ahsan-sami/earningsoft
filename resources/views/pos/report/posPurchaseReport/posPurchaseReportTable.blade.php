<script type="text/javascript">
  $('#loadingModal').hide();
</script>



<div id="printDiv">
<div class="container-fluid">
   
  <div class="row">
    <p class="companyNameData fontCustomize" style="text-align: center;padding: 0px;margin: 0px; color: #333;"><b>{{$companyName->name}}</b></p>
    <p class="fontCustomize" style="text-align: center;padding: 0px;margin: 0px;color:#333;"><b>{{$companyName->address}}</b></p>
    <p class="fontCustomize" style="text-align: center;padding: 0px;margin: 0px;color:#333;"><b>Purchase Report</b></p> 
    <p class="fontCustomize" align="center" style="color:#333;"> <b>From</b> {{date('d-m-Y',strtotime($startDate)) }} <b>To</b> {{date('d-m-Y',strtotime($endDate))}} </p>
    <p class="text-right fontCustomize"  style="float: right;padding-right: 17px; color:#333; font-size: 12px; text-align: right;">Print Date: {{date("d-m-Y")}}</p> 
  </div>

 <table class="table table-bordered table-striped" id="posSalesView" width="100%" style="color:black;font-size:14px;margin-bottom:50px;border-collapse: collapse;" border= "1px solid black;" cellpadding="0" cellspacing="0">
    <thead>
      <tr>
        <th width="80">SL#</th>
        <th>Purchase Date</th>
        <th>Purchase No</th>             
        <th>Product Name</th>
        <th>Code</th>
        <th>Received Quantity</th>
        <th>Supplier Name</th>
        <th>Unit Price</th>
        <th>Total Amount</th>
      </tr>
      {{ csrf_field() }}
    </thead>

    <tbody>
      <?php 
        $no = 0; 
        $totalRececiveQty = 0; 
        $totalUnitPrice = 0;
        $totalAmount = 0;
      ?>

      @foreach($posPurchaseReports as $purchase)
        
        <tr class="item{{$purchase->id}}">

          <td>{{++$no}}</td>
          
          @if($purchase->purchaseDate) <td style="text-align: center" rowspan="{{$purchase->daterow}}">{{ date('d-m-Y', strtotime($purchase->purchaseDate)) }}</td> @endif
          @if($purchase->billNo) <td style="text-align: center" rowspan="{{$purchase->billrow}}">{{ $purchase->billNo }}</td> @endif

          <td style="text-align: left; padding-left: 5px">{{$purchase->name}}</td> 
          <td style="text-align: center">{{$purchase->code}}</td> 
          <td style="text-align: center">{{$purchase->quantity}}</td>

          @if($purchase->supplierId)
            <td style="text-align: left; padding-left: 5px" rowspan="{{$purchase->supplierrow}}"> 
              <?php $supplierName = DB::table('pos_supplier')->select('name')->where('id',$purchase->supplierId)->first(); ?>
              {{$supplierName->name}}
            </td>
          @endif

          <td style="text-align: right; padding-right: 5px">{{ number_format($purchase->price, 2) }}</td>
          <td style="text-align: right; padding-right: 5px">{{ number_format($purchase->total, 2) }}</td>

          <?php
            $totalRececiveQty += $purchase->quantity;
            $totalUnitPrice += $purchase->price;
            $totalAmount += $purchase->total;
          ?>

      @endforeach
        </tr>

        <tr>
          <td colspan="5"><b>Total</b></td>
          <td style="text-align: center"><b>{{$totalRececiveQty}}</b></td>
          <td></td>
          <td style="text-align: right; padding-right: 5px"><b>{{ number_format($totalUnitPrice, 2) }}<b></td>
          <td style="text-align: right; padding-right: 5px"><b>{{ number_format($totalAmount, 2) }}</b></td>
        </tr>

    </tbody>
  </table>
</div>
</div>

@include('dataTableScript')




<script>

  $(function(){


    $("#printIcon").click(function(event) {

        // $("#reportingTable").removeClass('table table-striped table-bordered');

        // var printStyle = '<style>#reportingTable{float:left;height:auto;padding:0px;width:100% !important;font-size:11px;page-break-inside:auto; font-family: arial!important;}  thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px} tr{ page-break-inside:avoid; page-break-after:auto } table tbody tr td.amount{text-align:right !important;}</style><style media="print">@page{size: A4 portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

        var printStyle = '<style>#reportingTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

        var mainContents = document.getElementById("printDiv").innerHTML;
        var headerContents = '';

        // var paddingDiv  = "<div class='row' style='padding-top:20px !important;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>";


        // var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-right:5px; padding-top:40px ;'>Prepared By <span style='display:inline-block; width: 36%; padding-top:40px ;'></span> Checked By <span style='display:inline-block; width: 35%; padding-top:40px ;'></span> Approved By</div>";

        // var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 35%;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:15px;'></span> Approved By</div>";

        var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 35%; padding-top:40px;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:15px; padding-top:40px;'></span>Approved By</div>";

        // var footerContents = "<div class='row' style='font-size:12px;'> Prepared By <span style='display:inline-block; width: 34%;'></span> Checked By <span style='display:inline-block; width: 34%;'></span> Approved By</div>";

        var printContents = '<div id="order-details-wrapper" style="padding: 10px;">' + headerContents + printStyle + mainContents /*+ paddingDiv*/ + footerContents +'</div>';


        var win = window.open('','printwindow');
        win.document.write(printContents);
        win.print();
        // $("#reportingTable").addClass('table table-striped table-bordered');
        win.close();


      });
  })

</script>

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
          download: "Purchase Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
      });

 });

</script>
