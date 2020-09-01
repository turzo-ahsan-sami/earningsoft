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
.total td, .surplus-row td, .cash-bank-row td, .title-row td{
    padding-left: 5px !important;
    font-size: 1.05em;
}
.text-left {
    text-align: left !important;
}

</style>

<div id="printDiv">
    <!--This div is going to print company details and  ots account Statement   !-->
    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}

        <span style="font-size:14px;">{{ $cashFlowStatementLoadTableArr['company']->name }}</span><br/>
        <span style="font-size:11px;">{{ $cashFlowStatementLoadTableArr['company']->address }}</span><br/>
        <span style="text-decoration: underline;  font-size:14px;">Statement of Cash Flow</span></br>
        <span style="text-decoration: underline;  font-size:14px;">As on {{ date('jS F, Y',strtotime($cashFlowStatementLoadTableArr['dateTo'])) }}</span></br>

    </div>

    <div class="row">       {{-- div for Reporting Info --}}

        <div class="col-md-12"  style="font-size: 12px;">

            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{$cashFlowStatementLoadTableArr['projectName']}}</span>
                </span>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Reporting Date : <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{date('d-m-Y',strtotime($cashFlowStatementLoadTableArr['dateFrom']))." to ".date('d-m-Y',strtotime($cashFlowStatementLoadTableArr['dateTo']))}}</span>
                </span>
            </span>
            <br/>
            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Project Type: <?php echo str_repeat('&nbsp;', 5);?></span>
                    <span>{{$cashFlowStatementLoadTableArr['projectType']}}</span>
                </span>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 5);?></span>
                    <span>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</span>
                </span>
            </span>
            <br>
            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{$cashFlowStatementLoadTableArr['branchName']}}</span>
                </span>
            </span>

        </div>
    </div><br>

    <table id="cashFlowStatementTable" width="100%" class="table table-striped table-bordered" style="color:black; border-collapse: collapse;" border= "1px solid ash;" >
        {{-- search type fiscal year --}}
        @if ($searchType == 1)
        <thead>
            <tr style="vertical-align: top;">
                <th>Particulars</th>
                <th>Previous Year <br> ({{ $cashFlowStatementLoadTableArr['previousFiscalYearName'] }})</th>
                <th>Current Year <br> ({{ $cashFlowStatementLoadTableArr['thisFiscalYearName'] }})</th>
            </tr>
        </thead>

        <tbody>
            {{-- section A --}}
            <tr class="title-row text-bold">
                <td class="text-left" colspan="3">A. Cash flows from operating activities</td>
            </tr>
            {{-- surplus --}}
            <tr class="surplus-row text-bold">
                <td class="text-left">Surplus for the period</td>
                <td class="amount" data-amount="{{ $previousYearSurplus }}">
                    {{ number_format($previousYearSurplus, 2) }}
                </td>
                <td class="amount" data-amount="{{ $currentYearSurplus }}">
                    {{ number_format($currentYearSurplus, 2) }}
                </td>
            </tr>
            {{-- ///////////////non cash item expenses/////////////////// --}}
            <tr class="title-row text-bold">
                <td class="text-left" colspan="3">Add: Amount considered as non cash items expenses</td>
            </tr>

            @foreach ($allCashNonCashArr as $segmentType => $segmentLedgerArray)

            {!! $treeView[$segmentType] !!}

            {{-- non cash expense --}}
            @if ($segmentType == 'non-cash-expense')

            @php
            $subTotalNonCashExpensePY = $subTotal[$segmentType]['previousYear'];
            $subTotalNonCashExpenseCY = $subTotal[$segmentType]['currentYear'];
            @endphp
            <tr class="total text-bold">
                <td class="text-left">Sub-total of non cash items expenses</td>
                <td class="amount" data-amount="{{ $subTotalNonCashExpensePY }}">
                    {{ number_format($subTotalNonCashExpensePY, 2) }}
                </td>
                <td class="amount" data-amount="{{ $subTotalNonCashExpenseCY }}">
                    {{ number_format($subTotalNonCashExpenseCY, 2) }}
                </td>
            </tr>
            <tr class="title-row text-bold">
                <td class="text-left" colspan="3">Less: Amount considered as non cash items income</td>
            </tr>
            {{-- non cash income --}}
            @elseif ($segmentType == 'non-cash-income')

            @php
            $subTotalNonCashIncomePY = $subTotal[$segmentType]['previousYear'];
            $subTotalNonCashIncomeCY = $subTotal[$segmentType]['currentYear'];
            @endphp
            <tr class="total text-bold">
                <td class="text-left">Sub-total of non cash items income</td>
                <td class="amount" data-amount="{{ $subTotalNonCashIncomePY }}">
                    {{ number_format($subTotalNonCashIncomePY, 2) }}
                </td>
                <td class="amount" data-amount="{{ $subTotalNonCashIncomeCY }}">
                    {{ number_format($subTotalNonCashIncomeCY, 2) }}
                </td>
            </tr>
            {{-- cash operating activities --}}
            @elseif ($segmentType == 'cash-operating')
            @php
            $subTotalCashOperatingPY = $subTotal[$segmentType]['previousYear'];
            $subTotalCashOperatingCY = $subTotal[$segmentType]['currentYear'];
            $netCashOperatingPY = $previousYearSurplus + $subTotalNonCashExpensePY + $subTotalCashOperatingPY + $subTotalNonCashIncomePY;
            $netCashOperatingCY = $currentYearSurplus + $subTotalNonCashExpenseCY + $subTotalCashOperatingCY + $subTotalNonCashIncomeCY;
            @endphp
            <tr class="total text-bold">
                <td class="text-left">Net Cash used in operating activities</td>
                <td class="amount" data-amount="{{ $netCashOperatingPY }}">
                    {{ number_format($netCashOperatingPY, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashOperatingCY }}">
                    {{ number_format($netCashOperatingCY, 2) }}
                </td>
            </tr>
            <tr class="title-row text-bold">
                <td class="text-left" colspan="3">B. Cash flows from investing ativities</td>
            </tr>
            {{-- cash investing activities --}}
            @elseif ($segmentType == 'cash-investing')
            @php
            $netCashInvestingPY = $subTotal[$segmentType]['previousYear'];
            $netCashInvestingCY = $subTotal[$segmentType]['currentYear'];
            @endphp
            <tr class="total text-bold">
                <td class="text-left">Net Cash used in investing activities</td>
                <td class="amount" data-amount="{{ $netCashInvestingPY }}">
                    {{ number_format($netCashInvestingPY, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashInvestingCY }}">
                    {{ number_format($netCashInvestingCY, 2) }}
                </td>
            </tr>
            <tr class="title-row text-bold">
                <td class="text-left" colspan="3">C. Cash flows from financing ativities</td>
            </tr>
            @elseif ($segmentType == 'cash-financing')
            @php
            $netCashFinancingPY = $subTotal[$segmentType]['previousYear'];
            $netCashFinancingCY = $subTotal[$segmentType]['currentYear'];
            $netCashIncreaseDecreasePY = $netCashOperatingPY + $netCashInvestingPY + $netCashFinancingPY;
            $netCashIncreaseDecreaseCY = $netCashOperatingCY + $netCashInvestingCY + $netCashFinancingCY;
            @endphp
            <tr class="total text-bold">
                <td class="text-left">Net Cash used in financing activities</td>
                <td class="amount" data-amount="{{ $netCashFinancingPY }}">
                    {{ number_format($netCashFinancingPY, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashFinancingCY }}">
                    {{ number_format($netCashFinancingCY, 2) }}
                </td>
            </tr>
            <tr class="total text-bold">
                <td class="text-left">D. Net Cash increase/decrease (A+B+C)</td>
                <td class="amount" data-amount="{{ $netCashIncreaseDecreasePY }}">
                    {{ number_format($netCashIncreaseDecreasePY, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashIncreaseDecreaseCY }}">
                    {{ number_format($netCashIncreaseDecreaseCY, 2) }}
                </td>
            </tr>
            @endif

            @endforeach {{-- array loop--}}

            {{-- cash and bank balance --}}
            <tr class="cash-bank-row text-bold">
                <td class="text-left text-bold">Add. Cash and Bank balance at the begining</td>
                <td class="amount" data-amount="{{ $previousYearOpeningBalanceCashNBank }}">
                    {{ number_format($previousYearOpeningBalanceCashNBank, 2) }}
                </td>
                <td class="amount" data-amount="{{ $currentYearOpeningBalanceCashNBank }}">
                    {{ number_format($currentYearOpeningBalanceCashNBank, 2) }}
                </td>
            </tr>
            <tr class="total text-bold">
                <td class="text-left">Cash and Bank balance at the end</td>
                <td class="amount" data-amount="{{ $netCashIncreaseDecreasePY + $previousYearOpeningBalanceCashNBank }}">
                    {{ number_format($netCashIncreaseDecreasePY + $previousYearOpeningBalanceCashNBank, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashIncreaseDecreaseCY + $currentYearOpeningBalanceCashNBank }}">
                    {{ number_format($netCashIncreaseDecreaseCY + $currentYearOpeningBalanceCashNBank, 2) }}
                </td>
            </tr>

        </tbody>
        {{-- search type current year --}}
        @elseif ($searchType == 2)
        <thead>
            <tr style="vertical-align: top;">
                <th>Particulars</th>
                <th>This Month</th>
                <th>This Year</th>
                <th>Cumulative</th>
            </tr>
        </thead>

        <tbody>
            {{-- section A --}}
            <tr class="title-row text-bold">
                <td class="text-left" colspan="4">A. Cash flows from operating activities</td>
            </tr>
            {{-- surplus --}}
            <tr class="surplus-row text-bold">
                <td class="text-left">Surplus for the period</td>
                <td class="amount" data-amount="{{ $thisMonthSurplus }}">
                    {{ number_format($thisMonthSurplus, 2) }}
                </td>
                <td class="amount" data-amount="{{ $thisYearSurplus }}">
                    {{ number_format($thisYearSurplus, 2) }}
                </td>
                <td class="amount" data-amount="{{ $cumulativeSurplus }}">
                    {{ number_format($cumulativeSurplus, 2) }}
                </td>
            </tr>
            {{-- ///////////////non cash item expenses/////////////////// --}}
            <tr class="title-row text-bold">
                <td class="text-left" colspan="4">Add: Amount considered as non cash items expenses</td>
            </tr>

            @foreach ($allCashNonCashArr as $segmentType => $segmentLedgerArray)

            {!! $treeView[$segmentType] !!}

            {{-- non cash expense --}}
            @if ($segmentType == 'non-cash-expense')
            @php
            $subTotalNonCashExpenseThisMonth = $subTotal[$segmentType]['thisMonth'];
            $subTotalNonCashExpenseThisYear = $subTotal[$segmentType]['thisYear'];
            $subTotalNonCashExpenseCumulative = $subTotal[$segmentType]['cumulative'];
            @endphp
            <tr class="total text-bold">
                <td class="text-left">Sub-total of non cash items expenses</td>
                <td class="amount" data-amount="{{ $subTotalNonCashExpenseThisMonth }}">
                    {{ number_format($subTotalNonCashExpenseThisMonth, 2) }}
                </td>
                <td class="amount" data-amount="{{ $subTotalNonCashExpenseThisYear }}">
                    {{ number_format($subTotalNonCashExpenseThisYear, 2) }}
                </td>
                <td class="amount" data-amount="{{ $subTotalNonCashExpenseCumulative }}">
                    {{ number_format($subTotalNonCashExpenseCumulative, 2) }}
                </td>
            </tr>
            <tr class="title-row text-bold">
                <td class="text-left" colspan="4">Less: Amount considered as non cash items income</td>
            </tr>
            {{-- non cash income --}}
            @elseif ($segmentType == 'non-cash-income')
            @php
            $subTotalNonCashIncomeThisMonth = $subTotal[$segmentType]['thisMonth'];
            $subTotalNonCashIncomeThisYear = $subTotal[$segmentType]['thisYear'];
            $subTotalNonCashIncomeCumulative = $subTotal[$segmentType]['cumulative'];
            @endphp
            <tr class="total text-bold">
                <td class="text-left">Sub-total of non cash items income</td>
                <td class="amount" data-amount="{{ $subTotalNonCashIncomeThisMonth }}">
                    {{ number_format($subTotalNonCashIncomeThisMonth, 2) }}
                </td>
                <td class="amount" data-amount="{{ $subTotalNonCashIncomeThisYear }}">
                    {{ number_format($subTotalNonCashIncomeThisYear, 2) }}
                </td>
                <td class="amount" data-amount="{{ $subTotalNonCashIncomeCumulative }}">
                    {{ number_format($subTotalNonCashIncomeCumulative, 2) }}
                </td>
            </tr>
            {{-- cash operating activities --}}
            @elseif ($segmentType == 'cash-operating')
            @php
            $subTotalCashOperatingThisMonth = $subTotal[$segmentType]['thisMonth'];
            $subTotalCashOperatingThisYear = $subTotal[$segmentType]['thisYear'];
            $subTotalCashOperatingCumulative = $subTotal[$segmentType]['cumulative'];
            $netCashOperatingThisMonth = $thisMonthSurplus + $subTotalNonCashExpenseThisMonth + $subTotalCashOperatingThisMonth + $subTotalNonCashIncomeThisMonth;
            $netCashOperatingThisYear = $thisYearSurplus + $subTotalNonCashExpenseThisYear + $subTotalCashOperatingThisYear + $subTotalNonCashIncomeThisYear;
            $netCashOperatingCumulative = $cumulativeSurplus + $subTotalNonCashExpenseCumulative + $subTotalCashOperatingCumulative + $subTotalNonCashIncomeCumulative;
            @endphp
            <tr class="total text-bold">
                <td class="text-left">Net Cash used in operating activities</td>
                <td class="amount" data-amount="{{ $netCashOperatingThisMonth }}">
                    {{ number_format($netCashOperatingThisMonth, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashOperatingThisYear }}">
                    {{ number_format($netCashOperatingThisYear, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashOperatingCumulative }}">
                    {{ number_format($netCashOperatingCumulative, 2) }}
                </td>
            </tr>
            <tr class="title-row text-bold">
                <td class="text-left" colspan="4">B. Cash flows from investing ativities</td>
            </tr>
            {{-- cash investing activities --}}
            @elseif ($segmentType == 'cash-investing')
            @php
            $netCashInvestingThisMonth = $subTotal[$segmentType]['thisMonth'];
            $netCashInvestingThisYear = $subTotal[$segmentType]['thisYear'];
            $netCashInvestingCumulative = $subTotal[$segmentType]['cumulative'];
            @endphp
            <tr class="total text-bold">
                <td class="text-left">Net Cash used in investing activities</td>
                <td class="amount" data-amount="{{ $netCashInvestingThisMonth }}">
                    {{ number_format($netCashInvestingThisMonth, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashInvestingThisYear }}">
                    {{ number_format($netCashInvestingThisYear, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashInvestingCumulative }}">
                    {{ number_format($netCashInvestingCumulative, 2) }}
                </td>
            </tr>
            <tr class="title-row text-bold">
                <td class="text-left" colspan="4">C. Cash flows from financing ativities</td>
            </tr>
            @elseif ($segmentType == 'cash-financing')
            @php
            $netCashFinancingThisMonth = $subTotal[$segmentType]['thisMonth'];
            $netCashFinancingThisYear = $subTotal[$segmentType]['thisYear'];
            $netCashFinancingCumulative = $subTotal[$segmentType]['cumulative'];
            $netCashIncreaseDecreaseThisMonth = $netCashOperatingThisMonth + $netCashInvestingThisMonth + $netCashFinancingThisMonth;
            $netCashIncreaseDecreaseThisYear = $netCashOperatingThisYear + $netCashInvestingThisYear + $netCashFinancingThisYear;
            $netCashIncreaseDecreaseCumulative = $netCashOperatingCumulative + $netCashInvestingCumulative + $netCashFinancingCumulative;
            @endphp
            <tr class="total text-bold">
                <td class="text-left">Net Cash used in financing activities</td>
                <td class="amount" data-amount="{{ $netCashFinancingThisMonth }}">
                    {{ number_format($netCashFinancingThisMonth, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashFinancingThisYear }}">
                    {{ number_format($netCashFinancingThisYear, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashFinancingCumulative }}">
                    {{ number_format($netCashFinancingCumulative, 2) }}
                </td>
            </tr>
            <tr class="total text-bold">
                <td class="text-left">D. Net Cash increase/decrease (A+B+C)</td>
                <td class="amount" data-amount="{{ $netCashIncreaseDecreaseThisMonth }}">
                    {{ number_format($netCashIncreaseDecreaseThisMonth, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashIncreaseDecreaseThisYear }}">
                    {{ number_format($netCashIncreaseDecreaseThisYear, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashIncreaseDecreaseCumulative }}">
                    {{ number_format($netCashIncreaseDecreaseCumulative, 2) }}
                </td>
            </tr>
            @endif

            @endforeach {{-- array loop--}}

            {{-- cash and bank balance --}}
            <tr class="cash-bank-row text-bold">
                <td class="text-left text-bold">Add. Cash and Bank balance at the begining</td>
                <td class="amount" data-amount="{{ $thisMonthOpeningCashNBankBalance }}">
                    {{ number_format($thisMonthOpeningCashNBankBalance, 2) }}
                </td>
                <td class="amount" data-amount="{{ $thisYearOpeningCashNBankBalance }}">
                    {{ number_format($thisYearOpeningCashNBankBalance, 2) }}
                </td>
                <td class="amount" data-amount="{{ $cumOpeningCashNBankBalance }}">
                    {{ number_format($cumOpeningCashNBankBalance, 2) }}
                </td>
            </tr>
            <tr class="total text-bold">
                <td class="text-left">Cash and Bank balance at the end</td>
                <td class="amount" data-amount="{{ $netCashIncreaseDecreaseThisMonth + $thisMonthOpeningCashNBankBalance }}">
                    {{ number_format($netCashIncreaseDecreaseThisMonth + $thisMonthOpeningCashNBankBalance, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashIncreaseDecreaseThisYear + $thisYearOpeningCashNBankBalance }}">
                    {{ number_format($netCashIncreaseDecreaseThisYear + $thisYearOpeningCashNBankBalance, 2) }}
                </td>
                <td class="amount" data-amount="{{ $netCashIncreaseDecreaseCumulative + $cumOpeningCashNBankBalance }}">
                    {{ number_format($netCashIncreaseDecreaseCumulative + $cumOpeningCashNBankBalance, 2) }}
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
.toast-warning {
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
        // console.log($(this).text())
        if ($(this).text() == 0.00) {
            $(this).text('-');
        }
    });
});

    function toastrFunc(newArr){
        var toastrHtml = "<tr><td colspan='2' class='text-bold text-center' style='font-size: 1.1em;'>Report showing on date {{ date('jS F, Y',strtotime($cashFlowStatementLoadTableArr['dateTo'])) }}</td></tr><tr class = 'toastr-tr'><td colspan='2' class='text-bold text-center' style='font-size: 1.2em;'>Month End Unprocessed</td></tr>";
        $.each(newArr, function(key, element){
            toastrHtml += "<tr class = 'toastr-tr'><td style='width: 130px'>" + key + "</td>";
            toastrHtml += "<td style='width: 370px'>" + generateBranch(element) + "</td></tr>";
        });

        var finalStr = "<table>" + toastrHtml + "</table>";
        toastr.warning(finalStr);
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

    $('#cashFlowStatementTable tr').each(function() {

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
