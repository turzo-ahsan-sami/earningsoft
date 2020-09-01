<style media="screen">

.sub-total td{
    font-weight: bold;
    background-color: #9cb1d3;
}
.total td{
    background-color: #7291C9;
    font-size: 13px !important;
}

</style>

<div id="printDiv">
    <!--This div is going to print company details and  ots account Statement   !-->
    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}

        <span style="font-size:14px;">{{ $bankBookReportLoadTableArr['company']->name }}</span><br/>
        <span style="font-size:11px;">{{ $bankBookReportLoadTableArr['company']->address }}</span><br/>
        <span style="text-decoration: underline;  font-size:14px;">Bank Book Report</span></br>

    </div>

    <div class="row">       {{-- div for Reporting Info --}}

        <div class="col-md-12"  style="font-size: 12px;">

            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Ledger Head: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{$bankBookReportLoadTableArr['ledgerHead']}}</span>
                </span>
               
            </span>
            <br>
            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Project: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{$bankBookReportLoadTableArr['projectName']}}</span>
                </span>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Reporting Date : <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{date('d-m-Y',strtotime($bankBookReportLoadTableArr['dateFrom']))." to ".date('d-m-Y',strtotime($bankBookReportLoadTableArr['dateTo']))}}</span>
                </span>
            </span>
            <br/>
            <span>
                <!--<span style="color: black; float: left;">
                    <span style="font-weight: bold;">Project Type: <?php echo str_repeat('&nbsp;', 5);?></span>
                    <span>{{$bankBookReportLoadTableArr['projectType']}}</span>
                </span>-->
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Branch: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{$bankBookReportLoadTableArr['branchName']}}</span>
                </span>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 5);?></span>
                    <span>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</span>
                </span>
            </span>

        </div>
    </div><br>

    <table id="bankBookReportTable" width="100%" class="table table-striped table-bordered" style="color:black; border-collapse: collapse;" border= "1px solid ash;">
        <thead>
            <tr>
                <th style="width: 4%;">SL#</th>
                <th style="padding: 12px 20px; width: 6%;">Date</th>
                <th style="width: 10%;">Voucher Code</th>
                <th style="width: 30%;">Account Head</th>
                <th style="width: 22%;">Narration/Cheque Details</th>
                <th style="width: 8%;">Debit Amount</th>
                <th style="width: 8%;">Credit Amount</th>
                <th style="width: 8%;">Balance</th>
                <th style="padding: 12px 10px; width: 4%;">Dr/Cr</th>
            </tr>
        </thead>

        <tbody>
            {{-- opening balance row --}}
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: left;">Opening Balance</td>
                <td></td>
                <td class="amount">{{ number_format(0, 2, '.', ',') }}</td>
                <td class="amount">{{ number_format(0, 2,  '.', ',') }}</td>
                <td class="amount">{{ number_format(abs($openingBalance), 2, '.', ',') }}</td>
                <td>
                    @if($openingBalance < 0) {{ "Cr" }} @else {{ "Dr" }} @endif
                </td>
            </tr>

            @php
            $sl = 1;
            $totalDebit = 0;
            $totalCredit = 0;
            $balanceAmount = $openingBalance;
            @endphp

            @foreach ($voucherInfos as $voucherInfo)

            @php
                    // account head
                    // if ($ledgerId == null) {
            $ledgerNameWithCode = in_array($voucherInfo->debitAcc, $ledgerIdArr) ? $ledgersInfo[$voucherInfo->creditAcc] : $ledgersInfo[$voucherInfo->debitAcc];
                    // }
                    // elseif($ledgerId != $voucherInfo->debitAcc){
                    //     $ledgerNameWithCode = $ledgersInfo[$voucherInfo->debitAcc];
                    // }
                    // elseif($ledgerId != $voucherInfo->creditAcc){
                    //     $ledgerNameWithCode = $ledgersInfo[$voucherInfo->creditAcc];
                    // }
                    // elseif($voucherInfo->debitAcc == $voucherInfo->creditAcc) {
                    //     $ledgerNameWithCode = $ledgersInfo[$voucherInfo->debitAcc];
                    // }

                    // debit and credit amount
            if (in_array($voucherInfo->debitAcc, $ledgerIdArr) && $voucherInfo->debitAcc != $voucherInfo->creditAcc) {
                $debitAmount = $voucherInfo->amount;
                $creditAmount = 0;
                $totalDebit += $voucherInfo->amount;
                $balanceAmount += $voucherInfo->amount;
            }
            elseif (in_array($voucherInfo->creditAcc, $ledgerIdArr) && $voucherInfo->debitAcc != $voucherInfo->creditAcc) {
                $debitAmount = 0;
                $creditAmount = $voucherInfo->amount;
                $totalCredit += $voucherInfo->amount;
                $balanceAmount -= $voucherInfo->amount;
            }
            elseif ($voucherInfo->debitAcc == $voucherInfo->creditAcc) {
                $debitAmount = $voucherInfo->amount;
                $creditAmount = $voucherInfo->amount;
                $totalDebit += $voucherInfo->amount;
                $totalCredit += $voucherInfo->amount;
            }

                    // balance type(dr/cr)
            $balanceType = $balanceAmount < 0 ? 'Cr' : 'Dr';
            @endphp

            <tr>
                <td>{{ $sl++ }}</td>
                <td style="text-align: left;">{{ Carbon\Carbon::parse($voucherInfo->voucherDate)->format('d-m-Y') }}</td>
                <td style="text-align: left;">{{$voucherInfo->voucherCode}}</td>
                <td style="text-align: left;">{{ $ledgerNameWithCode }}</td>
                <td style="text-align: left;">{{$voucherInfo->globalNarration}}</td>
                <td class="amount">{{ number_format($debitAmount, 2) }}</td>
                <td class="amount">{{ number_format($creditAmount, 2) }}</td>
                <td class="amount">{{ number_format( abs($balanceAmount), 2) }}</td>
                <td>{{ $balanceType }}</td>
            </tr>

            @endforeach

            {{-- sub-total --}}
            <tr class="sub-total">
                <td colspan="5"> <strong>Sub Total</strong></td>
                <td class="amount">{{ number_format($totalDebit, 2, '.', ',') }}</td>
                <td class="amount">{{ number_format($totalCredit, 2, '.', ',') }}</td>
                <td class="amount"> {{ number_format(abs($balanceAmount), 2, '.', ',') }}</td>
                <td>
                    @if($balanceAmount < 0) {{ "Cr" }} @else {{ "Dr" }} @endif
                </td>
            </tr>
            {{-- total --}}
            <tr class="total">
                <td colspan="5"> <strong>Total</strong> </td>
                <td class="amount"><strong>{{ number_format($totalDebit, 2, '.', ',') }}</strong></td>
                <td class="amount"><strong>{{ number_format($totalCredit, 2, '.', ',') }}</strong></td>
                <td class="amount"><strong>{{ number_format(abs($balanceAmount),2, '.', ',') }}</strong></td>
                <td>
                    <strong>@if($balanceAmount < 0) {{ "Cr" }} @else {{ "Dr" }} @endif</strong>
                </td>
            </tr>
        </tbody>
    </table>

</div>

<script>
    $(document).ready(function() {

        $('.amount').each(function(){
            // console.log($(this).text())
            if ($(this).text() == 0.00) {
                $(this).text('-');
            }
        });
    });

</script>
