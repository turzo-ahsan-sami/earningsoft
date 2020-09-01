@extends('layouts/pos_layout')
@section('title', '| Purchase')
@section('content')'
<style type="text/css">
.positionLabel{
font-size: 15px;
font-weight: bold;
color:#666161;
}
.positionClass{
font-size: 15px;
font-weight: bold;
color:#666161;
padding-left: 15px;
}
.positionClass p{
font-size: 10px;
color: #ccc;
margin-top: 10px;
}
.modal-center{
top:10%;
transform: translateX(-10%);
}
.modal-image{
width: 100%;
height: 400px;
}
.position{
color: #666161;
font-size: 11px;
}
.img_modal{
cursor: pointer;
}
.img_modal:hover{
border: 1px solid #666161;
opacity: 0.6;
transition: width 2s;
}
.commentStyle{
font-size: 12px !important;
font-weight: 100 !important;
}
.status{
font-weight: bold;
}
</style>
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('pos/purchase')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Purchase List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div style="float: right; padding-right: 30px; font-size:18px; color: #64363F">
                                    <button id="printList" style="background-color:transparent; float:left; border: 3px solid #a1a1a1; border-radius: 25px; padding:0px 10px 0px 10px">
                                    <i class="fa fa-print fa-lg" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                           
                            <div id="printView">
                                <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
                                    <span style="font-size:14px;">{{ $data['companyInfo']['name'] }}</span><br/>
                                    <span style="font-size:12px;">{{ $data['companyInfo']['address'] }}</span><br/>
                                    <span style="text-decoration: underline; font-size:14px;">Purchase</span>
                                </div>
                                <div class="row" style="padding: 0px 15px;">       
                                    <table id="voucherInfoTable">
                                        <tbody>
                                            <tr>
                                                <td style="font-weight: bold; width: 7%;">Bill No</td>
                                                <td style="width: 1%;">:</td>
                                                <td style="width: 36%;">{{ $data['transactionInfo']['billNo'] }}</td>
                                                <td style="font-weight: bold; width: 7%;">Branch Name</td>
                                                <td style="width: 1%;">:</td>
                                                <td style="width: 8%;">{{ $data['transactionInfo']['branch'] }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;">Date</td>
                                                <td>:</td>
                                                <td>{{date('d-m-Y',strtotime($data['transactionInfo']['date']))}}</td>
                                                <td style="font-weight: bold;">Print Date</td>
                                                <td>:</td>
                                                <td>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;">Project Name</td>
                                                <td>:</td>
                                                <td>{{ $data['transactionInfo']['project'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row" style="padding: 20px 14px 5px 14px; ">
                                    <table id="voucherView" border="1pt solid ash" style="border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th style="">Product</th>
                                                <th style=" width: 7%;">Code</th>
                                                <th style=" width: 15%;">Quantity</th>
                                                <th style=" width: 16%;">Price</th>
                                                <th style=" width: 20%;">Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['transactionDetails'] as $record)
                                            <tr>
                                              <td style="text-align:left;">{{ $record['product'] }}</td>
                                              <td>{{ $record['code'] }}</td>
                                              <td>{{ $record['quantity'] }}</td>
                                              <td style="text-align:right;">{{ number_format($record['price'], 2, '.', ',') }}</td>
                                              <td style="text-align:right;">{{ number_format($record['total'], 2, '.', ',') }}</td>
                                            </tr>
                                            @endforeach
                                            
                                            @if($data['amountInfo']['vatAmount'] != 0)
                                            <tr>
                                                <td colspan="4">
                                                  <span style="color: black; font-weight: bold;" >
                                                      Vat:
                                                  </span>
                                                </td>
                                                <td style="text-align:right;">
                                                  <span style="color: black; font-weight: bold;" >
                                                      {{ number_format($data['amountInfo']['vatAmount'], 2, '.', ',') }}
                                                  </span>
                                                </td>
                                            </tr>
                                            @endif

                                            @if($data['amountInfo']['discountAmount'] != 0)
                                            <tr>
                                                <td colspan="4">
                                                  <span style="color: black; font-weight: bold;" >
                                                      Discount:
                                                  </span>
                                                </td>
                                                <td style="text-align:right;">
                                                  <span style="color: black; font-weight: bold;" >
                                                      {{ number_format($data['amountInfo']['discountAmount'], 2, '.', ',') }}
                                                  </span>
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td colspan="4">
                                                  <span style="color: black; font-weight: bold;" >
                                                      Total:
                                                  </span>
                                                </td>
                                                <td style="text-align:right;">
                                                  <span style="color: black; font-weight: bold;" >
                                                      {{ number_format($data['amountInfo']['grossTotal'], 2, '.', ',') }}
                                                  </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="4">
                                                  <span style="color: black; font-weight: bold;" >
                                                      Due:
                                                  </span>
                                                </td>
                                                <td style="text-align:right;">
                                                  <span style="color: black; font-weight: bold;" >
                                                      {{ number_format($data['amountInfo']['dueAmount'], 2, '.', ',') }}
                                                  </span>
                                                </td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>

<script type="text/javascript">

  $(function(){
      $("#printList").click(function(event) {
        var mainContents = document.getElementById("printView").innerHTML;
        var headerContents = '';
        var printStyle = '<style>#voucherView{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt ash; page-break-inside:auto;}  #voucherView thead tr th{text-align:center;vertical-align: middle; padding: 5px!important; font-size:11px;} #voucherView tbody tr td {text-align:center;vertical-align: middle;padding:3px ;font-size:11px;} tr{ page-break-inside:avoid; page-break-after:auto }#voucherView tr td.amount{ text-align: right; padding-right: 5px; }#voucherView tr td.name{text-align: left; padding-left: 5px;}#voucherInfoTable tbody tr td{ font-size:12px;}#globalNarrationTable{padding: 10px 0; }#globalNarrationTable tbody tr td{font-size: 12px;color: black;padding-bottom: 15px ;text-align: justify;vertical-align: middle;border:1px solid black !important;}#divFooter{text-align:center;position: fixed;bottom:12;display:block; color:#A3A3A3;}</style><style>@page {size: auto;margin: 0;}</style>';
        var printContents = '<div id="order-details-wrapper" style="padding: 30px 30px 0px 40px;">' + headerContents + printStyle + mainContents +'</div>';
        var win = window.open('','printwindow');
        win.document.write(printContents);
        win.print();
        // $("#voucherView").addClass('table table-striped table-bordered');
        win.close();
      });
  });

</script>


{{-- EndPrint Page --}}
<style type="text/css">
#divFooter{ text-align: center; width:100%; }
@media screen {
#divFooter { display: none; }
}
#voucherView{
font-size:12px;
margin-bottom:0;
margin-left:0;
width: 100%;
}
#voucherView thead tr th,
#voucherView tbody tr td{
text-align: center;
color: black /*!important*/;
vertical-align: middle;
border:1px solid black !important;
/*padding-bottom: 100px;*/
/*height: 20%;*/
}
#voucherView thead tr th{
color: black /*!important*/;
font-size: 13px;
background-color: white !important;
}
#voucherView tbody tr td{
padding: 6px 0;  10px top & bottom padding, 0px left & right ;  /*for td height*/
}
#voucherView tr{background-color:  white !important;}
#voucherView tr:hover { background-color:    white !important;          /* lightyellow */ }
#voucherView tr td.amount{text-align: right;padding-right: 5px;}
#voucherView tr td.name{text-align: left; padding-left: 5px;}
#globalNarrationTable tbody tr td{
font-size: 12px;
color: black;
text-align: justify;
padding-bottom: 15px ;
vertical-align: middle;
border:1px solid black !important;
}
#globalNarrationTable tbody tr{background-color:  white !important;}
.userInfoMainDiv{
width:100%%;
}
.userInfoDiv{
width:33.33%;
color: black;
text-align: center;
float: left;
/*padding-top: 40px;*/
/*margin-left: 10px;*/
}
#voucherInfoTable{
color: black;
font-size: 12px;
width: 100%;
}
#voucherInfoTable tbody tr td{ text-align: left; }
/*#voucherInfoTable tbody tr td:last-child{
text-align: right;
}*/
</style>
@endsection