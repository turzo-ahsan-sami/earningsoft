<style media="screen">
.level-1 td, .level-2 td, .level-3 td, .level-4 td, .level-constant td{
    font-weight: bold !important;
    padding-left: 5px !important;
    text-transform: uppercase;
}
.level-1 td{
    font-size: 1.04em;
}
.level-2 td{
    font-size: 1.00em;
    /* padding-left: 15px !important; */
}
.level-3 td{
    font-size: .96em;
    /* padding-left: 25px !important; */
}
.level-4 td{
    font-size: .92em;
    /* padding-left: 35px !important; */
}
.level-constant td{
    font-size: .88em;
    /* padding-left: 45px !important; */
}
.cash-N-bank td{
    font-size: .88em;
    padding-left: 5px !important;
}
.level-transformed td{
    font-weight: normal !important;
    font-size: .88em;
    padding-left: 5px !important;
    text-transform: capitalize;
}
.level-final td{
    font-weight: normal !important;
    text-transform: none;
}
.total td{
    padding-left: 5px !important;
    font-size: 1.05em;
}

</style>

<div id="printDiv">
    <!--This div is going to print company details and  ots account Statement   !-->
    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}

        <span style="font-size:14px;">{{ $receiptPaymentLoadTableArr['company']->name }}</span><br/>
        <span style="font-size:11px;">{{ $receiptPaymentLoadTableArr['company']->address }}</span><br/>
        <span style="text-decoration: underline;  font-size:14px;">Receipt Payment Report</span></br>
        <span style="text-decoration: underline;  font-size:14px;">As on {{ date('jS F, Y',strtotime($receiptPaymentLoadTableArr['dateTo'])) }}</span></br>

    </div>

    <div class="row">

        <div class="col-md-12"  style="font-size: 12px;">

            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{$receiptPaymentLoadTableArr['projectName']}}</span>
                </span>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Reporting Date : <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{date('d-m-Y',strtotime($receiptPaymentLoadTableArr['dateFrom']))." to ".date('d-m-Y',strtotime($receiptPaymentLoadTableArr['dateTo']))}}</span>
                </span>
            </span>
            <br/>
            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{$receiptPaymentLoadTableArr['branchName']}}</span>
                </span>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 5);?></span>
                    <span>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</span>
                </span>
            </span>
            <br>

        </div>
    </div><br>


    <table id="receiptPaymentReportTable" width="100%" class="table table-striped table-bordered" style="color:black; border-collapse: collapse;" border= "1px solid ash;">
        {{-- search type fiscal year --}}
        @if ($searchType == 1)
        <thead>
            <tr style="vertical-align: top;">
                <th>Particulars</th>
                <th>Notes</th>
                <th>
                    Previous Year <br> {{ $receiptPaymentLoadTableArr['previousFiscalYearName'] != null ? '('.$receiptPaymentLoadTableArr['previousFiscalYearName']. ')' : '' }}
                </th>
                <th>Current Year <br> ({{ $receiptPaymentLoadTableArr['thisFiscalYearName'] }})</th>
            </tr>
        </thead>

        <tbody>
            <tr class="cash-N-bank">
                <td style="text-align: left;">ASSET</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">OPENING BALANCE</td>
                <td></td>
                <td class="amount text-bold" data-amount="{{ $previousYearOpeningBalanceCash + $previousYearOpeningBalanceBank }}">
                    {{ number_format($previousYearOpeningBalanceCash + $previousYearOpeningBalanceBank, 2) }}
                </td>
                <td class="amount text-bold" data-amount="{{ $currentYearOpeningBalanceCash + $currentYearOpeningBalanceBank }}">
                    {{ number_format($currentYearOpeningBalanceCash + $currentYearOpeningBalanceBank, 2) }}
                </td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CASH IN HAND</td>
                <td></td>
                <td class="amount" data-amount="{{ $previousYearOpeningBalanceCash }}">
                    {{ number_format($previousYearOpeningBalanceCash, 2) }}
                </td>
                <td class="amount" data-amount="{{ $currentYearOpeningBalanceCash }}">
                    {{ number_format($currentYearOpeningBalanceCash, 2) }}
                </td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CASH AT BANK</td>
                <td></td>
                <td class="amount" data-amount="{{ $previousYearOpeningBalanceBank }}">
                    {{ number_format($previousYearOpeningBalanceBank, 2) }}
                </td>
                <td class="amount" data-amount="{{ $currentYearOpeningBalanceBank }}">
                    {{ number_format($currentYearOpeningBalanceBank, 2) }}
                </td>
            </tr>
            <tr class="total text-bold">
                <td style="text-align: left;">Receipt</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            {{-- display html tree --}}
            {!! $receiptTreeView !!}

            {{-- total receipt --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Total Receipt</td>
                <td></td>
                <td class="amount" data-amount="{{ $totalReceipt['previousYear'] }}">{{ number_format($totalReceipt['previousYear'], 2) }}</td>
                <td class="amount" data-amount="{{ $totalReceipt['currentYear'] }}">{{ number_format($totalReceipt['currentYear'], 2) }}</td>
            </tr>
            {{-- total = opening balance + total receipt --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Total</td>
                <td></td>
                <td class="amount" data-amount="{{ $previousYearOpeningBalanceCash + $previousYearOpeningBalanceBank + $totalReceipt['previousYear'] }}">
                    {{ number_format($previousYearOpeningBalanceCash + $previousYearOpeningBalanceBank + $totalReceipt['previousYear'], 2) }}
                </td>
                <td class="amount" data-amount="{{ $currentYearOpeningBalanceCash + $currentYearOpeningBalanceBank + $totalReceipt['currentYear'] }}">
                    {{ number_format($currentYearOpeningBalanceCash + $currentYearOpeningBalanceBank + $totalReceipt['currentYear'], 2) }}
                </td>
            </tr>

            <tr class="total text-bold">
                <td style="text-align: left;">Payment</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            {{-- display html tree --}}
            {!! $paymentTreeView !!}

            {{-- total payment --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Total Payment</td>
                <td></td>
                <td class="amount" data-amount="{{ $totalPayment['previousYear'] }}">{{ number_format($totalPayment['previousYear'], 2) }}</td>
                <td class="amount" data-amount="{{ $totalPayment['currentYear'] }}">{{ number_format($totalPayment['currentYear'], 2) }}</td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">ASSET</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CLOSING BALANCE</td>
                <td></td>
                <td class="amount text-bold" data-amount="{{ $previousYearClosingBalanceCash + $previousYearClosingBalanceBank }}">
                    {{ number_format($previousYearClosingBalanceCash + $previousYearClosingBalanceBank, 2) }}
                </td>
                <td class="amount text-bold" data-amount="{{ $currentYearClosingBalanceCash + $currentYearClosingBalanceBank }}">
                    {{ number_format($currentYearClosingBalanceCash + $currentYearClosingBalanceBank, 2) }}
                </td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CASH IN HAND</td>
                <td></td>
                <td class="amount" data-amount="{{ $previousYearClosingBalanceCash }}">
                    {{ number_format($previousYearClosingBalanceCash, 2) }}
                </td>
                <td class="amount" data-amount="{{ $currentYearClosingBalanceCash }}">
                    {{ number_format($currentYearClosingBalanceCash, 2) }}
                </td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CASH AT BANK</td>
                <td></td>
                <td class="amount" data-amount="{{ $previousYearClosingBalanceBank }}">
                    {{ number_format($previousYearClosingBalanceBank, 2) }}
                </td>
                <td class="amount" data-amount="{{ $currentYearClosingBalanceBank }}">
                    {{ number_format($currentYearClosingBalanceBank, 2) }}
                </td>
            </tr>
            {{-- total = opening balance + total receipt --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Total</td>
                <td></td>
                <td class="amount" data-amount="{{ $previousYearClosingBalanceCash + $previousYearClosingBalanceBank + $totalPayment['previousYear'] }}">
                    {{ number_format($previousYearClosingBalanceCash + $previousYearClosingBalanceBank + $totalPayment['previousYear'], 2) }}
                </td>
                <td class="amount" data-amount="{{ $currentYearClosingBalanceCash + $currentYearClosingBalanceBank + $totalPayment['currentYear'] }}">
                    {{ number_format($currentYearClosingBalanceCash + $currentYearClosingBalanceBank + $totalPayment['currentYear'], 2) }}
                </td>
            </tr>

        </tbody>
        {{-- search type current year --}}
        @elseif ($searchType == 2)
        <thead>
            <tr style="vertical-align: top;">
                <th>Particulars</th>
                <th>Notes</th>
                <th>This Month</th>
                <th>This Year</th>
                <th>Cumulative</th>
            </tr>
        </thead>

        <tbody>
            <tr class="cash-N-bank">
                <td style="text-align: left;">ASSET</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">OPENING BALANCE</td>
                <td></td>
                <td class="amount text-bold" data-amount="{{ $thisMonthOpeningCash + $thisMonthOpeningBank }}">
                    {{ number_format($thisMonthOpeningCash + $thisMonthOpeningBank, 2) }}
                </td>
                <td class="amount text-bold" data-amount="{{ $thisYearOpeningCash + $thisYearOpeningBank }}">
                    {{ number_format($thisYearOpeningCash + $thisYearOpeningBank, 2) }}
                </td>
                <td></td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CASH IN HAND</td>
                <td></td>
                <td class="amount" data-amount="{{ $thisMonthOpeningCash }}">
                    {{ number_format($thisMonthOpeningCash, 2) }}
                </td>
                <td class="amount" data-amount="{{ $thisYearOpeningCash }}">
                    {{ number_format($thisYearOpeningCash, 2) }}
                </td>
                <td></td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CASH AT BANK</td>
                <td></td>
                <td class="amount" data-amount="{{ $thisMonthOpeningBank }}">
                    {{ number_format($thisMonthOpeningBank, 2) }}
                </td>
                <td class="amount" data-amount="{{ $thisYearOpeningBank }}">
                    {{ number_format($thisYearOpeningBank, 2) }}
                </td>
                <td></td>
            </tr>
            <tr class="total text-bold">
                <td style="text-align: left;">Receipt</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            {{-- display html tree --}}
            {!! $receiptTreeView !!}

            {{-- total receipt --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Total Receipt</td>
                <td></td>
                <td class="amount" data-amount="{{ $totalReceipt['thisMonth'] }}">{{ number_format($totalReceipt['thisMonth'], 2) }}</td>
                <td class="amount" data-amount="{{ $totalReceipt['thisYear'] }}">{{ number_format($totalReceipt['thisYear'], 2) }}</td>
                <td class="amount" data-amount="{{ $totalReceipt['cumulative'] }}">{{ number_format($totalReceipt['cumulative'], 2) }}</td>
            </tr>
            {{-- total = opening balance + total receipt --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Total</td>
                <td></td>
                <td class="amount" data-amount="{{ $thisMonthOpeningCash + $thisMonthOpeningBank + $totalReceipt['thisMonth'] }}">
                    {{ number_format($thisMonthOpeningCash + $thisMonthOpeningBank + $totalReceipt['thisMonth'], 2) }}
                </td>
                <td class="amount" data-amount="{{ $thisYearOpeningCash + $thisYearOpeningBank + $totalReceipt['thisYear'] }}">
                    {{ number_format($thisYearOpeningCash + $thisYearOpeningBank + $totalReceipt['thisYear'], 2) }}
                </td>
                <td class="amount" data-amount="{{ $totalReceipt['cumulative'] }}">
                    {{ number_format($totalReceipt['cumulative'], 2) }}
                </td>
            </tr>
            {{-- payment start --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Payment</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            {{-- display html tree --}}
            {!! $paymentTreeView !!}

            {{-- total payment --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Total Payment</td>
                <td></td>
                <td class="amount" data-amount="{{ $totalPayment['thisMonth'] }}">{{ number_format($totalPayment['thisMonth'], 2) }}</td>
                <td class="amount" data-amount="{{ $totalPayment['thisYear'] }}">{{ number_format($totalPayment['thisYear'], 2) }}</td>
                <td class="amount" data-amount="{{ $totalPayment['cumulative'] }}">{{ number_format($totalPayment['cumulative'], 2) }}</td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">ASSET</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CLOSING BALANCE</td>
                <td></td>
                <td class="amount text-bold" data-amount="{{ $thisMonthClosingBalanceCash + $thisMonthClosingBalanceBank }}">
                    {{ number_format($thisMonthClosingBalanceCash + $thisMonthClosingBalanceBank, 2) }}
                </td>
                <td class="amount text-bold" data-amount="{{ $thisYearClosingBalanceCash + $thisYearClosingBalanceBank }}">
                    {{ number_format($thisYearClosingBalanceCash + $thisYearClosingBalanceBank, 2) }}
                </td>
                <td class="amount text-bold" data-amount="{{ $cumulativeClosingBalanceCash + $cumulativeClosingBalanceBank }}">
                    {{ number_format($cumulativeClosingBalanceCash + $cumulativeClosingBalanceBank, 2) }}
                </td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CASH IN HAND</td>
                <td></td>
                <td class="amount" data-amount="{{ $thisMonthClosingBalanceCash }}">
                    {{ number_format($thisMonthClosingBalanceCash, 2) }}
                </td>
                <td class="amount" data-amount="{{ $thisYearClosingBalanceCash }}">
                    {{ number_format($thisYearClosingBalanceCash, 2) }}
                </td>
                <td class="amount" data-amount="{{ $cumulativeClosingBalanceCash }}">
                    {{ number_format($cumulativeClosingBalanceCash, 2) }}
                </td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CASH AT BANK</td>
                <td></td>
                <td class="amount" data-amount="{{ $thisMonthClosingBalanceBank }}">
                    {{ number_format($thisMonthClosingBalanceBank, 2) }}
                </td>
                <td class="amount" data-amount="{{ $thisYearClosingBalanceBank }}">
                    {{ number_format($thisYearClosingBalanceBank, 2) }}
                </td>
                <td class="amount" data-amount="{{ $cumulativeClosingBalanceBank }}">
                    {{ number_format($cumulativeClosingBalanceBank, 2) }}
                </td>
            </tr>
            {{-- total = opening balance + total receipt --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Total</td>
                <td></td>
                <td class="amount" data-amount="{{ $thisMonthClosingBalanceCash + $thisMonthClosingBalanceBank + $totalPayment['thisMonth'] }}">
                    {{ number_format($thisMonthClosingBalanceCash + $thisMonthClosingBalanceBank + $totalPayment['thisMonth'], 2) }}
                </td>
                <td class="amount" data-amount="{{ $thisYearClosingBalanceCash + $thisYearClosingBalanceBank + $totalPayment['thisYear'] }}">
                    {{ number_format($thisYearClosingBalanceCash + $thisYearClosingBalanceBank + $totalPayment['thisYear'], 2) }}
                </td>
                <td class="amount" data-amount="{{ $cumulativeClosingBalanceCash + $cumulativeClosingBalanceBank + $totalPayment['cumulative'] }}">
                    {{ number_format($cumulativeClosingBalanceCash + $cumulativeClosingBalanceBank + $totalPayment['cumulative'], 2) }}
                </td>
            </tr>

        </tbody>
        {{-- search type date range --}}
        @elseif ($searchType == 3)
        <thead>
            <tr style="vertical-align: top;">
                <th>Particulars</th>
                <th>Notes</th>
                <th>Time Period <br> {{ $receiptPaymentLoadTableArr['dateFrom'] }} To {{ $receiptPaymentLoadTableArr['dateTo'] }}</th>
            </tr>
        </thead>

        <tbody>
            <tr class="cash-N-bank">
                <td style="text-align: left;">ASSET</td>
                <td></td>
                <td></td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">OPENING BALANCE</td>
                <td></td>
                <td class="amount text-bold" data-amount="{{ $dateRangeOpeningCash + $dateRangeOpeningBank }}">
                    {{ number_format($dateRangeOpeningCash + $dateRangeOpeningBank, 2) }}
                </td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CASH IN HAND</td>
                <td></td>
                <td class="amount" data-amount="{{ $dateRangeOpeningCash }}">
                    {{ number_format($dateRangeOpeningCash, 2) }}
                </td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CASH AT BANK</td>
                <td></td>
                <td class="amount" data-amount="{{ $dateRangeOpeningBank }}">
                    {{ number_format($dateRangeOpeningBank, 2) }}
                </td>
            </tr>
            <tr class="total text-bold">
                <td style="text-align: left;">Receipt</td>
                <td></td>
                <td></td>
            </tr>

            {{-- display html tree --}}
            {!! $receiptTreeView !!}

            {{-- total receipt --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Total Receipt</td>
                <td></td>
                <td class="amount" data-amount="{{ $totalReceipt['dateRange'] }}">{{ number_format($totalReceipt['dateRange'], 2) }}</td>
            </tr>
            {{-- total = opening balance + total receipt --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Total</td>
                <td></td>
                <td class="amount" data-amount="{{ $dateRangeOpeningCash + $dateRangeOpeningBank + $totalReceipt['dateRange'] }}">
                    {{ number_format($dateRangeOpeningCash + $dateRangeOpeningBank + $totalReceipt['dateRange'], 2) }}
                </td>
            </tr>

            <tr class="total text-bold">
                <td style="text-align: left;">Payment</td>
                <td></td>
                <td></td>
            </tr>

            {{-- display html tree --}}
            {!! $paymentTreeView !!}

            {{-- total payment --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Total Payment</td>
                <td></td>
                <td class="amount" data-amount="{{ $totalPayment['dateRange'] }}">{{ number_format($totalPayment['dateRange'], 2) }}</td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">ASSET</td>
                <td></td>
                <td></td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CLOSING BALANCE</td>
                <td></td>
                <td class="amount text-bold" data-amount="{{ $dateRangeClosingBalanceCash + $dateRangeClosingBalanceBank }}">
                    {{ number_format($dateRangeClosingBalanceCash + $dateRangeClosingBalanceBank, 2) }}
                </td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CASH IN HAND</td>
                <td></td>
                <td class="amount" data-amount="{{ $dateRangeClosingBalanceCash }}">
                    {{ number_format($dateRangeClosingBalanceCash, 2) }}
                </td>
            </tr>
            <tr class="cash-N-bank">
                <td style="text-align: left;">CASH AT BANK</td>
                <td></td>
                <td class="amount" data-amount="{{ $dateRangeClosingBalanceBank }}">
                    {{ number_format($dateRangeClosingBalanceBank, 2) }}
                </td>
            </tr>
            {{-- total = opening balance + total receipt --}}
            <tr class="total text-bold">
                <td style="text-align: left;">Total</td>
                <td></td>
                <td class="amount" data-amount="{{ $dateRangeClosingBalanceCash + $dateRangeClosingBalanceBank + $totalPayment['dateRange'] }}">
                    {{ number_format($dateRangeClosingBalanceCash + $dateRangeClosingBalanceBank + $totalPayment['dateRange'], 2) }}
                </td>
            </tr>

        </tbody>
        @endif

    </table>

    @php
    $newArr = json_encode($monthEndUnprocessedBranchesByMonth);
    @endphp

    {{-- {!! $newArr = $monthEndUnprocessedBranches->toJson() !!} --}}
</div>

<style media="screen">
.toastr-tr td {
    padding-bottom: 20px !important;
}
.toast-info {
    width: 500px !important;
}
</style>

<script>
    $(document).ready(function() {
        var newArr = {!! $newArr !!};
    //console.log(Object.keys(newArr).length);
    if(Object.keys(newArr).length > 0 ){
        toastrFunc(newArr);
    }
    var roundUp = {{ $roundUp }};
    if (roundUp == 1) {
        $('.amount').each(function(){
            var num = parseFloat($(this).data('amount'));
            var newNum = Math.round(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            $(this).text(newNum);
        });
    }
    // console.log(newArr);
    $('.amount').each(function(){
        if ($(this).text() == 0.00) {
            $(this).text('-');
        }
    });
});

    function toastrFunc(newArr){
        var toastrHtml = "<tr class = 'toastr-tr'><td colspan='2' class='text-bold text-center' style='font-size: 1.2em;'>Month End Unprocessed</td></tr>";
        $.each(newArr, function(key, element){
            toastrHtml += "<tr class = 'toastr-tr'><td style='width: 30%'>" + key + "</td>";
            toastrHtml += "<td style='width: 70%'>" + generateBranch(element) + "</td></tr>";
        });

        var finalStr = "<table>" + toastrHtml + "</table>";
        toastr.info(finalStr);
    }

    function generateBranch(elm){
        var initData = "";
        $.each(elm, function(index, val){
            initData += val+", ";
        });

        return initData.slice(0, -2);
    }

// remove depth levels
function removeOtherDepthLevels(){

    var lvl = {{ $depthLevel }};
    var nxtLvl = {{ $depthLevel + 1 }};
    var lvlClass = '.level-' + lvl;
    var nxtLvlClass = '.level-' + nxtLvl;
    var maxLvl = {{ $maxLevel }};
    // alert(maxLvl);

    if(lvl == 1) {
        $('.level').each(function(){
            $(this).hide();
        });
        $(lvlClass).each(function(){
            $(this).show();
        });
    }
    else if (lvl > 1) {

        $(lvlClass).each(function(){
            $(this).removeClass('level-constant');
            $(this).addClass('level-transformed');
        });

        $('.level-final').each(function(){
            $(this).hide();
        });

        for (var i = lvl; i < maxLvl; i++) {
            $(nxtLvlClass).each(function(){
                $(this).hide();
            });

            nxtLvl++;
            nxtLvlClass = '.level-' + nxtLvl;
        }

    }

}

removeOtherDepthLevels();
</script>

@if ($withZero == 0)
<script type="text/javascript">

    $('#receiptPaymentReportTable tr').each(function() {

        var sum = 0;
        var countAmount = 0;
        $(this).closest('tr').find('.amount').each(function(){
            countAmount++;
            sum += parseFloat($(this).text());
        });

        if(countAmount > 0 && sum == 0){
         $(this).closest('tr').css('display', 'none');
     }

 });

</script>
@endif
