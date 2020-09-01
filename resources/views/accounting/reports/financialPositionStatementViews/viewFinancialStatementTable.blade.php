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
    text-transform: capitalize !important;
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

        <span style="font-size:14px;">{{ $financialPositionLoadTableArr['company']->name }}</span><br/>
        <span style="font-size:11px;">{{ $financialPositionLoadTableArr['company']->address }}</span><br/>
        <span style="text-decoration: underline;  font-size:14px;">Statement of Financial Position</span></br>
        <span style="text-decoration: underline;  font-size:14px;">As on {{ date('jS F, Y',strtotime($financialPositionLoadTableArr['dateTo'])) }}</span></br>

    </div>

    <div class="row">       {{-- div for Reporting Info --}}

        <div class="col-md-12"  style="font-size: 12px;">

            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{$financialPositionLoadTableArr['projectName']}}</span>
                </span>
                {{-- <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Reporting Date : {{ str_repeat('&nbsp;', 3) }}</span>
                    <span>{{date('d-m-Y',strtotime($financialPositionLoadTableArr['dateFrom']))." to ".date('d-m-Y',strtotime($financialPositionLoadTableArr['dateTo']))}}</span>
                </span> --}}
                
            </span>
            <br/>
            <span>
                <span>
                    <span style="color: black; float: left;">
                        <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                        <span>{{$financialPositionLoadTableArr['branchName']}}</span>
                    </span>
                </span>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 5);?></span>
                    <span>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</span>
                </span>
            </span>
            <br>
            {{-- <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Branch Name: {{ str_repeat('&nbsp;', 3) }}</span>
                    <span>{{$financialPositionLoadTableArr['branchName']}}</span>
                </span>
            </span> --}}

        </div>
    </div><br>

    <table id="financialPositionReportTable" class="table table-striped table-bordered" width="100%" style="color:black; border-collapse: collapse;" border= "1px solid ash;">
        <thead>
            <tr style="vertical-align: top;">
                <th>Particulars</th>
                <th>
                    Previous Year <br> {{ $financialPositionLoadTableArr['previousFiscalYearName'] != null ? '('.$financialPositionLoadTableArr['previousFiscalYearName']. ')' : '' }}
                </th>
                <th>Current Year <br> ({{ $financialPositionLoadTableArr['thisFiscalYearName'] }})</th>
            </tr>
        </thead>

        <tbody>

            {{-- display html tree --}}
            {!! $treeView !!}

            {{-- surplus row --}}
            <tr class="level-constant text-bold">
                <td style="text-align: left;">Surplus/Defict from Income Statement</td>
                <td class="amount" data-amount="{{ $previousYearSurplus }}">{{ number_format($previousYearSurplus, 2) }}</td>
                <td class="amount" data-amount="{{ $currentYearSurplus }}">{{ number_format($currentYearSurplus, 2) }}</td>
            </tr>
            {{-- equity row --}}
            <tr class="total text-bold">
                <td style="text-align: left;">TOTAL EQUITY/CAPITAL FUND</td>
                <td class="amount" data-amount="{{ $totalCapitalFund['previousYear'] + $previousYearSurplus }}">
                    {{ number_format($totalCapitalFund['previousYear'] + $previousYearSurplus, 2) }}
                </td>
                <td class="amount" data-amount="{{ $totalCapitalFund['currentYear'] + $currentYearSurplus }}">
                    {{ number_format($totalCapitalFund['currentYear'] + $currentYearSurplus, 2) }}
                </td>
            </tr>
            {{-- total liability row --}}
            <tr class="text-bold total">
                <td style="text-align: left;">TOTAL LIABILITIES & EQUITY</td>
                <td class="amount" data-amount="{{ $totalLiabilities['previousYear'] + $totalCapitalFund['previousYear'] + $previousYearSurplus }}">
                    {{ number_format($totalLiabilities['previousYear'] + $totalCapitalFund['previousYear'] + $previousYearSurplus, 2) }}
                </td>
                <td class="amount" data-amount="{{ $totalLiabilities['currentYear'] + $totalCapitalFund['currentYear'] + $currentYearSurplus }}">
                    {{ number_format($totalLiabilities['currentYear'] + $totalCapitalFund['currentYear'] + $currentYearSurplus, 2) }}
                </td>
            </tr>
        </tbody>
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
        var toastrHtml = "<tr><td colspan='2' class='text-bold text-center' style='font-size: 1.1em;'>Report showing on date {{ date('jS F, Y',strtotime($financialPositionLoadTableArr['dateTo'])) }}</td></tr><tr class = 'toastr-tr'><td colspan='2' class='text-bold text-center' style='font-size: 1.2em;'>Month End Unprocessed</td></tr>";
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

    $('#financialPositionReportTable tr').each(function() {

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
