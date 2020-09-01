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
.total td{
    padding-left: 5px !important;
    font-size: 1.05em;
}

</style>

<div id="printDiv">
    <!--This div is going to print company details and  ots account Statement   !-->
    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}

        <span style="font-size:14px;">{{ $trialBalanceLoadTableArr['company']->name }}</span><br/>
        <span style="font-size:11px;">{{ $trialBalanceLoadTableArr['company']->address }}</span><br/>
        <span style="text-decoration: underline;  font-size:14px;">Trial Balance</span></br>
        <span style="text-decoration: underline;  font-size:14px;">As on {{ date('jS F, Y',strtotime($trialBalanceLoadTableArr['dateTo'])) }}</span></br>

    </div>

    <div class="row">       {{-- div for Reporting Info --}}

        <div class="col-md-12"  style="font-size: 12px;">

            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{$trialBalanceLoadTableArr['projectName']}}</span>
                </span>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Reporting Date : <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{date('d-m-Y',strtotime($trialBalanceLoadTableArr['dateFrom']))." to ".date('d-m-Y',strtotime($trialBalanceLoadTableArr['dateTo']))}}</span>
                </span>
            </span>
            <br/>
            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{$trialBalanceLoadTableArr['branchName']}}</span>
                </span>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 5);?></span>
                    <span>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</span>
                </span>
            </span>
            <br>

        </div>
    </div><br>

    <table id="trialBalanceReportTable" width="100%" class="table table-striped table-bordered" style="color:black; border-collapse: collapse;" border= "1px solid ash;">
        <thead>
            <tr style="vertical-align: top;">
                <th rowspan="2">Particulars</th>
                <th rowspan="2">Notes</th> {{-- Extra column for Notes --}}
                <th colspan="2">Balance at the begining</th>
                <th colspan="2">During this period</th>
                <th colspan="2">Closing Balance (Cumulative)</th>
            </tr>
            <tr>
                <th>Dr</th>
                <th>Cr</th>
                <th>Dr</th>
                <th>Cr</th>
                <th>Dr</th>
                <th>Cr</th>
            </tr>
        </thead>

        <tbody>
            {{-- display html tree --}}
            {!! $treeView !!}
        </tbody>

        <thead>
            @php
            $totalOpDebit = $totalBalanceArr['totalOpDebit'];
            $totalOpCredit = $totalBalanceArr['totalOpCredit'];
            $totalCurrentDebit = $totalBalanceArr['totalCurrentDebit'];
            $totalCurrentCredit = $totalBalanceArr['totalCurrentCredit'];
            $totalCumulativeDebit = $totalBalanceArr['totalCumulativeDebit'];
            $totalCumulativeCredit = $totalBalanceArr['totalCumulativeCredit'];

            if ($roundUp == 1) {
                $totalOpDebit = round($totalOpDebit);
                $totalOpCredit = round($totalOpCredit);
                $totalCurrentDebit = round($totalCurrentDebit);
                $totalCurrentCredit = round($totalCurrentCredit);
                $totalCumulativeDebit = round($totalCumulativeDebit);
                $totalCumulativeCredit = round($totalCumulativeCredit);
            }

            @endphp
            <tr style="font-weight: bold;" class="total">
                <td style="text-align: center;">Total</td>
                <td></td> {{-- Extra column for Notes --}}
                <td class="text-right amount" data-amount="{{ $totalOpDebit }}" style="padding-right: 5px">
                    {{ number_format($totalOpDebit, 2) }}
                </td>
                <td class="text-right amount" data-amount="{{ $totalOpCredit }}" style="padding-right: 5px">
                    {{ number_format($totalOpCredit, 2) }}
                </td>
                <td class="text-right amount" data-amount="{{ $totalCurrentDebit }}" style="padding-right: 5px">
                    {{ number_format($totalCurrentDebit, 2) }}
                </td>
                <td class="text-right amount" data-amount="{{ $totalCurrentCredit }}" style="padding-right: 5px">
                    {{ number_format($totalCurrentCredit, 2) }}
                </td>
                <td class="text-right amount" data-amount="{{ $totalCumulativeDebit }}" style="padding-right: 5px">
                    {{ number_format($totalCumulativeDebit, 2) }}
                </td>
                <td class="text-right amount" data-amount="{{ $totalCumulativeCredit }}" style="padding-right: 5px">
                    {{ number_format($totalCumulativeCredit, 2) }}
                </td>
            </tr>
        </thead>
    </table>

    @php
    $newArr = json_encode($monthEndUnprocessedBranchesByMonth);
    @endphp

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

                // console.log($(this))
                var num = parseFloat($(this).data('amount'));
                // console.log(num);
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
        var toastrHtml = "<tr><td colspan='2' class='text-bold text-center' style='font-size: 1.1em;'>Report showing on date {{ date('jS F, Y',strtotime($trialBalanceLoadTableArr['dateTo'])) }}</td></tr><tr class = 'toastr-tr'><td colspan='2' class='text-bold text-center' style='font-size: 1.2em;'>Month End Unprocessed</td></tr>";
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

    $('#trialBalanceReportTable tr').each(function() {

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
