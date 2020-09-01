@extends('layouts/acc_layout')
@section('title', '| Ledger Report')
@section('content')
@include('successMsg')

<style>

#ladgerReportForm input{
    height:25px;
    border-radius: 5px;
}
#ladgerReportForm select{
    height:30px;
    border-radius: 5px;
}
#ledgerReportSubmit {
    padding: 2px;
    margin-top: 14px;
}

#ladgerReportInfoLeftDiv{
    color: black;
    font-size: 12px;
    /*width:41.66%;*/
    float: left;
    text-align: left;
}
#ladgerReportInfoRightDiv{
    color: black;
    font-size: 12px;
    /*width:41.66%;*/
    float: right;
    text-align: right;
}
/*.dataTables_filter, .dataTables_info { display: none; } */

</style>

<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options"> <?php $grandParent=0; ?>
                            {{-- <a href="{{url('addLedger/'.encrypt($grandParent))}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Ledger</a> --}}
                            {{-- <button id="printIcon" class="btn btn-info pull-right addViewBtn" style="font-size: 14px; margin-top: 10px; border: 3px solid #525659; border-radius: 25px;">
                                <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                            </button> --}}
                        </div>
                        <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">LEDGER REPORT</h3>
                    </div>

                    <div class="panel-body panelBodyView">

                        <!-- Filtering Start-->
                        <div class="row">
                            <div class="col-md-11">
                                <div class="row">

                                    <div class="col-md-2">
                                        <div class="row">

                                            {!! Form::open(array('url' => 'ledgerprojectIdReport/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'ladgerReportForm', 'method'=>'get')) !!}

                                            <div class="col-md-8">
                                                <div class="form-group" style="font-size: 13px; color:#212F3C">
                                                    {!! Form::label('', 'Ledger:', ['class' => 'control-label col-sm-12']) !!}
                                                    <div class="col-sm-12">
                                                        <select class="form-control" name="ledgerId">
                                                            <option value="">Select Ledger</option>
                                                            @foreach ($childrenLedger as $childLedger)
                                                                <option value={{$childLedger->id}}>{{$childLedger->name}}</option>
                                                                {{-- <option value={{$childLedger->id}}>{{$childLedger->code." - ".$childLedger->name}}</option> --}}
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>

                                            {{-- <div class="col-md-1"></div> --}}

                                            <div class="col-md-2">
                                                <div class="form-group" style="">
                                                    {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                                    <div class="col-sm-12" style="padding-top: 13%;">
                                                        {!! Form::submit('search', ['id' => 'ledgerReportSubmit', 'class' => 'btn btn-primary btn-xs']); !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2"></div>

                                            {!! Form::close()  !!}
                                        </div>
                                    </div>    {{-- end Div of ledgerSearch --}}


                                    <div class="col-md-10"></div>
                                </div>
                            </div>

                            <div class="col-md-1">
                                {{-- <div class="form-group">
                                  {!! Form::label('printList', '', ['class' => 'control-label col-sm-12 hidden']) !!}
                                    <div class="col-sm-12" style="padding-top: 25%; color: black">
                                      <button id="printList" style="background-color:transparent;border:none;float:left;">
                                        <i class="fa fa-print fa-lg" aria-hidden="true"></i>
                                      </button>
                                    </div>
                                </div> --}}
                            </div>              {{-- for print --}}



                        </div>
                        <!-- filtering end-->


                        <div>
                            <script type="text/javascript">
                                jQuery(document).ready(function($)
                                {
                                    /*$("#famsProductTable").dataTable().yadcf([

                                     ]);*/
                                    $("#accLedgerReportTable").dataTable({
                                        "sPaginationType": "full_numbers",
                                        "bFilter": true,
                                        "sDom":"lrtip",

                                        "lengthMenu": [[-1], ["All"]],
                                        // "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
                                        "oLanguage": {
                                            "sEmptyTable": "No Records Available",
                                            "sLengthMenu": ""
                                            // "sLengthMenu": "Show _MENU_"
                                        },
                                            // "columns": [
                                            //     { "width": "4%" },
                                            //     { "width": "6%" },
                                            //     { "width": "10%" },
                                            //     { "width": "30%" },
                                            //     null,
                                            //     { "width": "7%" },
                                            //     { "width": "7%" },
                                            //     { "width": "7%" },
                                            //     { "width": "4%" }
                                            // ]
                                    });

                                    $('#accLedgerReportTable_info').hide();
                                    $('#accLedgerReportTable_paginate').hide();

                                });
                            </script>

                        </div>

                        <?php
                        if ($firstLedgerId!=null || $firstLedgerId!="") {
                        $voucherDetails = DB::table('acc_voucher_details')->where('debitAcc',$firstLedgerId)->orWhere('creditAcc',$firstLedgerId)->get();
                        var_dump($voucherDetails);
                        $debitAmountArray=array();
                        $creditAmountArray=array();
                        ?>
                        <div id="printDiv">
                            <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
                                <?php
                                    // $company = DB::table('gnr_company')->where('id',$journalVoucher->companyId)->select('name','address')->first();
                                ?>
                                <span style="font-size:14px;">Ambala Foundation</span><br/>
                                <span style="font-size:11px;">House# 62, Block# Ka, Shyamoli, Dhaka-1207</span><br/>
                                <span style="text-decoration: underline;  font-size:14px;">Ledger Report</span>
                            </div>
                            <div class="row">       {{-- div for Ledger --}}

                                <div class="col-md-12"  style="font-size: 12px;" >
                                    <?php
                                    $ledgerHead = DB::table('acc_account_ledger')->where('id',$firstLedgerId)->select('name', 'code')->first();
                                        // $branch = DB::table('gnr_branch')->where('id',$journalVoucher->branchId)->select('name')->first();
                                        // $projectType = DB::table('gnr_project_type')->where('id',$journalVoucher->projectTypeId)->select('name')->first();
                                    ?>

                                    <span>
                                        <span style="color: black; float: left;">
                                            <span style="font-weight: bold;">Ledger Head:<?php echo str_repeat('&nbsp;', 5);?></span>
                                            <span>{{$ledgerHead->name}}</span>
                                        </span>
                                        <span style="color: black; float: right;">
                                            <span style="font-weight: bold;">Reporting Date:<?php echo str_repeat('&nbsp;', 6);?></span>
                                            <span>{{"01-04-2017 to 11-04-2017"}}</span>
                                        </span>
                                    </span>
                                    <br/>

                                    <span>
                                        <span style="color: black; float: left;">
                                            <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                            <span>{{-- {{$branch->name}} --}}</span>
                                        </span>
                                    </span>
                                    <br/>

                                    <span>
                                        <span style="color: black; float: left;">
                                            <span style="font-weight: bold;">Project Type: <?php echo str_repeat('&nbsp;', 4);?></span>
                                            <span>{{-- {{$projectType->name}} --}}</span>
                                        </span>
                                        <span style="color: black; float: right;">
                                            <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 22);?></span>
                                            <span>{{\Carbon\Carbon::now()->format('d-m-Y H:i:s')}}</span>
                                        </span>
                                    </span>
                                    <br/>

                                    <span>
                                        <span style="color: black; float: left;">
                                            <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                            <span>{{-- {{$branch->name}} --}}</span>
                                        </span>
                                    </span>

                                </div>

                            </div>

                            <div class="row" style=" margin: 15px 0px;">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered dt-responsive nowrap" border="1pt solid ash" style="border-collapse: collapse;" id="accLedgerReportTable">
                                        <thead>
                                        <tr>
                                            <th style="width: 4%;">SL#</th>
                                            <th style="width: 6%;">Date</th>
                                            <th style="width: 10%;">Voucher Code</th>
                                            <th style="width: 30%;">Account Head</th>
                                            <th style="width: 25%;">Narration/Cheque Details</th>
                                            <th style="width: 7%;">Dedit Amount</th>
                                            <th style="width: 7%;">Credit Amount</th>
                                            <th style="width: 7%;">Balance</th>
                                            <th style="width: 4%;">Dr/Cr</th>
                                        </tr>
                                        {{ csrf_field() }}
                                        </thead>
                                        <?php $no=0; ?>
                                        <tbody>
                                        <tr>
                                            <td>{{++$no}}</td>
                                            <td></td>
                                            <td></td>
                                            <td style="text-align: left;">Opening Balance</td>
                                            <td></td>
                                            <td style="text-align: right;">-</td>
                                            <td style="text-align: right;">-</td>
                                            <?php
                                            // echo number_format( , 2, '.', '');
                                            $openingBalanceAmount = DB::table('acc_opening_balance')->where('id', $firstLedgerId)->value('balanceAmount');
                                            $balanceAmount=$openingBalanceAmount;
                                            // var_dump($balanceAmount);
                                            ?>
                                            <td style="text-align: right;">{{number_format($openingBalanceAmount, 2, '.', ',')}}</td>
                                            <td>
                                                <?php
                                                if($balanceAmount<0){ echo "Cr"; }else{ echo "Dr"; }
                                                ?>
                                            </td>
                                        </tr>

                                        @foreach ($voucherDetails as $voucherDetail)

                                            <tr>
                                                <td class="">{{++$no}}</td>
                                                <?php
                                                $voucherInfo = DB::table('acc_voucher')->where('id', $voucherDetail->voucherId)->select('voucherDate','voucherCode')->first();
                                                ?>
                                                <td style="text-align: left;">{{$voucherInfo->voucherDate}}</td>
                                                <td style="text-align: left;">{{$voucherInfo->voucherCode}}</td>
                                                <td style="text-align: left;">
                                                    <?php
                                                    if($firstLedgerId!=$voucherDetail->debitAcc){
                                                        $ledgerInfo = DB::table('acc_account_ledger')->where('id', $voucherDetail->debitAcc)->select('name','code')->first();
                                                    }else if($firstLedgerId!=$voucherDetail->creditAcc){
                                                        $ledgerInfo = DB::table('acc_account_ledger')->where('id', $voucherDetail->creditAcc)->select('name','code')->first();
                                                    }
                                                    ?>
                                                    {{$ledgerInfo->name." [".$ledgerInfo->code."]"}}
                                                </td>
                                                <td style="text-align: left;">{{$voucherDetail->localNarration}}</td>

                                                <?php if($firstLedgerId==$voucherDetail->debitAcc){
                                                array_push($debitAmountArray,$voucherDetail->amount);
                                                $balanceAmount=$balanceAmount+$voucherDetail->amount;
                                                ?>
                                                <td style="text-align: right;">{{number_format( $voucherDetail->amount, 2, '.', ',')}}</td>
                                                <td style="text-align: right;">-</td>

                                                <?php }else if($firstLedgerId==$voucherDetail->creditAcc){
                                                array_push($creditAmountArray,$voucherDetail->amount);
                                                $balanceAmount=$balanceAmount-$voucherDetail->amount;
                                                ?>
                                                <td style="text-align: right;">-</td>
                                                <td style="text-align: right;">{{number_format( $voucherDetail->amount, 2, '.', ',')}}</td>

                                                <?php }?>

                                                {{-- <td>{{number_format( $balanceAmount, 2, '.', ',')}}</td> --}}
                                                <td style="text-align: right;">{{number_format( abs($balanceAmount), 2, '.', ',')}}</td>
                                                <td>
                                                    <?php if($balanceAmount<0){ echo "Cr"; }else{ echo "Dr";} ?>
                                                </td>
                                            </tr>

                                        @endforeach

                                        <tr style="font-weight: bold;">
                                            <td><span style="display: none;">{{++$no}}</span></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>Total</td>
                                            <td style="text-align: right;">{{number_format(array_sum($debitAmountArray), 2, '.', ',')}}</td>
                                            <td style="text-align: right;">{{number_format(array_sum($creditAmountArray), 2, '.', ',')}}</td>
                                            <?php
                                            $totalBalanceAmount=((array_sum($debitAmountArray)+$openingBalanceAmount)-array_sum($creditAmountArray));
                                            ?>
                                            <td style="text-align: right;"> {{number_format(abs($totalBalanceAmount), 2, '.', ',')}} </td>
                                            {{-- <td> {{number_format($totalBalanceAmount, 2, '.', ',')}} </td> --}}
                                            <td>
                                                <?php if($totalBalanceAmount<0){ echo "Cr";}else{ echo "Dr";} ?>
                                            </td>
                                        </tr>

                                        <?php
                                        // echo "DR:".array_sum($debitAmountArray);
                                        // echo ", CR:".array_sum($creditAmountArray);
                                        // echo ", Total:".((array_sum($debitAmountArray)+$openingBalanceAmount)-array_sum($creditAmountArray));
                                        ?>

                                        </tbody>
                                    </table>
                                </div>  {{-- TableResponsiveDiv --}}
                                <?php }?>
                            </div> {{-- rowDiv --}}
                        </div> {{-- printDiv --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('dataTableScript')

@endsection


<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>



<script type="text/javascript">
    $(function(){
        $("#printIcon").click(function(){

            // $("#accLedgerReportTable").removeClass('table table-striped table-bordered');
            // $('#accLedgerReportTable_info').hide();
            // $('#accLedgerReportTable_paginate').hide();


            var printStyle = '<style>#accLedgerReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt ash;page-break-inside:auto;} tr:last-child { font-weight: bold;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td {text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style>@page {size: auto;margin: 25;}</style>';

            var mainContents = document.getElementById("printDiv").innerHTML;
            var printContents = '<div id="order-details-wrapper" style="padding: 5px 5px 0px 15px;">' + printStyle + mainContents+'</div>';


            // var printContents = document.getElementById("printView").innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML ="" + printContents;
            window.print();
            document.body.innerHTML = originalContents;

            // $("#voucherView").addClass('table table-striped table-bordered');
            location.reload();
        // window.location.href = '{{url('ledgerReport?ledgerId=64/')}}';

        });
    });

</script>
