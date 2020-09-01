<div id="printDiv">
    <!--This div is going to print company details and  ots account Statement   !-->
    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}

        <span style="font-size:14px;">{{$cashFlowStatementLoadTableArr['company']->name}}</span><br/>
        <span style="font-size:11px;">{{$cashFlowStatementLoadTableArr['company']->address}}</span><br/>
        <span style="text-decoration: underline;  font-size:14px;">Statement Of Cash Flows</span></br>
        @if ($searchType == 1)
            <span>
                <span style="font-weight: bold;">Fiscal Year:<?php echo str_repeat('&nbsp;', 1);?></span>
                <span>{{ $cashFlowStatementLoadTableArr['fiscalYearsSelected1']->name }}</span>
            </span>
        @elseif ($searchType == 2)
            <span>
                <span style="font-weight: bold;">As on Current Year Upto:<?php echo str_repeat('&nbsp;', 1);?></span>
                <span>{{date('d F, Y',strtotime($cashFlowStatementLoadTableArr['dateTo']))}}</span>
            </span>
        @endif

    </div>

    <div class="row">       {{-- div for Reporting Info --}}

        <div class="col-md-12"  style="font-size: 12px;">

            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{$cashFlowStatementLoadTableArr['projectName']}}</span>
                </span>
                <span style="color: black; float: right;">

                    <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{$cashFlowStatementLoadTableArr['branchName']}}</span>

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

        </div>
    </div>

    <br>

    @php
    // current year
    $subTotalCYCashItemsExpenses = 0;
    $subTotalCYNonCashItemsIncome = 0;
    $subTotalCYNetCashOperating = 0;
    $subTotalCYNetCashInvesting = 0;
    $subTotalCYNetCashFinancial = 0;
    // previous year
    $subTotalPYCashItemsExpenses = 0;
    $subTotalPYNonCashItemsIncome = 0;
    $subTotalPYNetCashOperating = 0;
    $subTotalPYNetCashInvesting = 0;
    $subTotalPYNetCashFinancial = 0;
    // this month
    $subTotalThisMonthCashItemsExpenses = 0;
    $subTotalThisMonthNonCashItemsIncome = 0;
    $subTotalThisMonthNetCashOperating = 0;
    $subTotalThisMonthNetCashInvesting = 0;
    $subTotalThisMonthNetCashFinancial = 0;
    // cumulative
    $subTotalCumulativeCashItemsExpenses = 0;
    $subTotalCumulativeNonCashItemsIncome = 0;
    $subTotalCumulativeNetCashOperating = 0;
    $subTotalCumulativeNetCashInvesting = 0;
    $subTotalCumulativeNetCashFinancial = 0;
    @endphp

    <table class="table table-striped table-bordered" id="otsTable" style="color: black;">
        <thead>
            <tr>
                <th rowspan="1">Particular</th>
                @if ($searchType == 1)
                    <th rowspan="1">Previous Year<br> ({{$cashFlowStatementLoadTableArr['fiscalYearsSelected2']->name}})</th>
                    <th rowspan="1">Current Year<br> ({{$cashFlowStatementLoadTableArr['fiscalYearsSelected1']->name}})</th>
                @elseif ($searchType == 2)
                    <th rowspan="1">Current Month</th>
                    <th rowspan="1">Current Year<br> ({{$cashFlowStatementLoadTableArr['fiscalYearsSelected1']->name}})</th>
                    <th rowspan="1">Cumulative</th>
                @endif
            </tr>

        </thead>
        <tbody>

            {{-- surplus rows --}}
            <tr class="text-bold">
                <td style="text-align: left; padding-left: 10px;" colspan="7" rowspan="1">A. Cash flows from operating activities</td>
            </tr>

            @if ($searchType == 1)
                <tr>
                    <td style="text-align: left; padding-left: 10px;">Surplus</td>

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($pySurplusAmount)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($cySurplusAmount)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($pySurplusAmount) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($cySurplusAmount) }}</td>
                    @endif

                </tr>
            @elseif ($searchType == 2)
                <tr>
                    <td style="text-align: left; padding-left: 10px;">Surplus</td>

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($thisMonthSurplusAmount)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($cySurplusAmount)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($cumulativeSurplusAmount)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($thisMonthSurplusAmount) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($cySurplusAmount) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($cumulativeSurplusAmount) }}</td>
                    @endif

                </tr>
            @endif

            {{-- non cash items expenses rows --}}
            <tr class="text-bold">
                <td style="text-align: left; padding-left: 10px;" colspan="7" rowspan="1">Add: Amount considered as non cash items Expenses</td>
            </tr>
            @foreach ($nonCashItemsExpensesBalance as $key => $item)

                @if ($searchType == 1)
                    <tr>
                        <td style="text-align: left; padding-left: 10px;">{{ $item['name'].' ['.$item['code'].']' }}</td>

                        @if ($roundUp == 1)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['pyBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cyBalance'])) }}</td>
                        @else
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['pyBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cyBalance']) }}</td>
                        @endif

                    </tr>
                    @php
                    $subTotalCYCashItemsExpenses += $item['cyBalance'];
                    $subTotalPYCashItemsExpenses += $item['pyBalance'];
                    @endphp
                @elseif ($searchType == 2)
                    <tr>
                        <td style="text-align: left; padding-left: 10px;">{{ $item['name'].' ['.$item['code'].']' }}</td>

                        @if ($roundUp == 1)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['thisMonthBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cyBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cumulativeBalance'])) }}</td>
                        @else
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['thisMonthBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cyBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cumulativeBalance']) }}</td>
                        @endif

                    </tr>
                    @php
                    $subTotalThisMonthCashItemsExpenses += $item['thisMonthBalance'];
                    $subTotalCYCashItemsExpenses += $item['cyBalance'];
                    $subTotalCumulativeCashItemsExpenses += $item['cumulativeBalance'];
                    @endphp
                @endif

            @endforeach
            <tr class="text-bold">
                <td style="text-align: left; padding-left: 10px;">Sub-total of non cash items expenses</td>
                @if ($searchType == 1)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalPYCashItemsExpenses)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalCYCashItemsExpenses)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalPYCashItemsExpenses) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalCYCashItemsExpenses) }}</td>
                    @endif

                @elseif ($searchType == 2)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalThisMonthCashItemsExpenses)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalCYCashItemsExpenses)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalCumulativeCashItemsExpenses)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalThisMonthCashItemsExpenses) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalCYCashItemsExpenses) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalCumulativeCashItemsExpenses) }}</td>
                    @endif

                @endif

            </tr>

            {{-- non cash items income rows --}}
            <tr class="text-bold">
                <td style="text-align: left; padding-left: 10px;" colspan="7" rowspan="1">Less: Amount considered as non cash items income</td>
            </tr>

            @if ($searchType == 1)
                <tr>
                    <td style="text-align: left; padding-left: 10px;">{{ $nonCashItemsIncomeBalance['name'].' ['.$nonCashItemsIncomeBalance['code'].']' }}</td>

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($nonCashItemsIncomeBalance['pyBalance'])) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($nonCashItemsIncomeBalance['cyBalance'])) }}</td>
                    @else

                        @if ($nonCashItemsIncomeBalance['pyBalance'] == 0)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($nonCashItemsIncomeBalance['pyBalance']) }}</td>
                        @else
                            <td class="amount">({{ App\Service\EasyCode::negativeReplace($nonCashItemsIncomeBalance['pyBalance']) }})</td>
                        @endif
                        @if ($nonCashItemsIncomeBalance['cyBalance'] == 0)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($nonCashItemsIncomeBalance['cyBalance']) }}</td>
                        @else
                            <td class="amount">({{ App\Service\EasyCode::negativeReplace($nonCashItemsIncomeBalance['cyBalance']) }})</td>
                        @endif

                    @endif

                </tr>
                @php
                $subTotalCYNonCashItemsIncome += $nonCashItemsIncomeBalance['cyBalance'];
                $subTotalPYNonCashItemsIncome += $nonCashItemsIncomeBalance['pyBalance'];
                @endphp
            @elseif ($searchType == 2)
                <tr>
                    <td style="text-align: left; padding-left: 10px;">{{ $nonCashItemsIncomeBalance['name'].' ['.$nonCashItemsIncomeBalance['code'].']' }}</td>

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($nonCashItemsIncomeBalance['thisMonthBalance'])) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($nonCashItemsIncomeBalance['cyBalance'])) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($nonCashItemsIncomeBalance['cumulativeBalance'])) }}</td>
                    @else

                        @if ($nonCashItemsIncomeBalance['thisMonthBalance'] == 0)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($nonCashItemsIncomeBalance['thisMonthBalance']) }}</td>
                        @else
                            <td class="amount">({{ App\Service\EasyCode::negativeReplace($nonCashItemsIncomeBalance['thisMonthBalance']) }})</td>
                        @endif

                        @if ($nonCashItemsIncomeBalance['cyBalance'] == 0)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($nonCashItemsIncomeBalance['cyBalance']) }}</td>
                        @else
                            <td class="amount">({{ App\Service\EasyCode::negativeReplace($nonCashItemsIncomeBalance['cyBalance']) }})</td>
                        @endif

                        @if ($nonCashItemsIncomeBalance['cumulativeBalance'] == 0)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($nonCashItemsIncomeBalance['cumulativeBalance']) }}</td>
                        @else
                            <td class="amount">({{ App\Service\EasyCode::negativeReplace($nonCashItemsIncomeBalance['cumulativeBalance']) }})</td>
                        @endif

                    @endif

                </tr>
                @php
                $subTotalThisMonthNonCashItemsIncome += $nonCashItemsIncomeBalance['thisMonthBalance'];
                $subTotalCYNonCashItemsIncome += $nonCashItemsIncomeBalance['cyBalance'];
                $subTotalCumulativeNonCashItemsIncome += $nonCashItemsIncomeBalance['cumulativeBalance'];
                @endphp
            @endif

            <tr class="text-bold">
                <td style="text-align: left; padding-left: 10px;">Sub-total of non cash items income</td>
                @if ($searchType == 1)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalPYNonCashItemsIncome)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalCYNonCashItemsIncome)) }}</td>
                    @else

                        @if ($subTotalPYNonCashItemsIncome == 0)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalPYNonCashItemsIncome) }}</td>
                        @else
                            <td class="amount">({{ App\Service\EasyCode::negativeReplace($subTotalPYNonCashItemsIncome) }})</td>
                        @endif

                        @if ($subTotalCYNonCashItemsIncome == 0)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalCYNonCashItemsIncome) }}</td>
                        @else
                            <td class="amount">({{ App\Service\EasyCode::negativeReplace($subTotalCYNonCashItemsIncome) }})</td>
                        @endif

                    @endif

                @elseif ($searchType == 2)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalThisMonthNonCashItemsIncome)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalCYNonCashItemsIncome)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalCumulativeNonCashItemsIncome)) }}</td>
                    @else

                        @if ($subTotalThisMonthNonCashItemsIncome == 0)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalThisMonthNonCashItemsIncome) }}</td>
                        @else
                            <td class="amount">({{ App\Service\EasyCode::negativeReplace($subTotalThisMonthNonCashItemsIncome) }})</td>
                        @endif

                        @if ($subTotalCYNonCashItemsIncome == 0)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalCYNonCashItemsIncome) }}</td>
                        @else
                            <td class="amount">({{ App\Service\EasyCode::negativeReplace($subTotalCYNonCashItemsIncome) }})</td>
                        @endif

                        @if ($subTotalCumulativeNonCashItemsIncome == 0)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalCumulativeNonCashItemsIncome) }}</td>
                        @else
                            <td class="amount">({{ App\Service\EasyCode::negativeReplace($subTotalCumulativeNonCashItemsIncome) }})</td>
                        @endif
                        
                    @endif

                @endif
            </tr>

            {{-- net cash operating activities rows --}}
            @foreach ($netCashOperatingActivitiesBalance as $key => $item)
                @if ($searchType == 1)
                    <tr>
                        <td style="text-align: left; padding-left: 10px;">{{ $item['name'].' ['.$item['code'].']' }}</td>

                        @if ($roundUp == 1)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['pyBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cyBalance'])) }}</td>
                        @else
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['pyBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cyBalance']) }}</td>
                        @endif

                    </tr>
                    @php
                    $subTotalCYNetCashOperating += $item['cyBalance'];
                    $subTotalPYNetCashOperating += $item['pyBalance'];
                    @endphp
                @elseif ($searchType == 2)
                    <tr>
                        <td style="text-align: left; padding-left: 10px;">{{ $item['name'].' ['.$item['code'].']' }}</td>

                        @if ($roundUp == 1)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['thisMonthBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cyBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cumulativeBalance'])) }}</td>
                        @else
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['thisMonthBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cyBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cumulativeBalance']) }}</td>
                        @endif

                    </tr>
                    @php
                    $subTotalThisMonthNetCashOperating += $item['thisMonthBalance'];
                    $subTotalCYNetCashOperating += $item['cyBalance'];
                    $subTotalCumulativeNetCashOperating += $item['cumulativeBalance'];
                    @endphp
                @endif

            @endforeach

            @php
                $totalCYCashOperating = $cySurplusAmount + $subTotalCYCashItemsExpenses - $subTotalCYNonCashItemsIncome + $subTotalCYNetCashOperating;
                $totalPYCashOperating = $pySurplusAmount + $subTotalPYCashItemsExpenses - $subTotalPYNonCashItemsIncome + $subTotalPYNetCashOperating;
                $totalThisMonthCashOperating = $thisMonthSurplusAmount + $subTotalThisMonthCashItemsExpenses - $subTotalThisMonthNonCashItemsIncome + $subTotalThisMonthNetCashOperating;
                $totalCumulativeCashOperating = $cumulativeSurplusAmount + $subTotalCumulativeCashItemsExpenses - $subTotalCumulativeNonCashItemsIncome + $subTotalCumulativeNetCashOperating;
            @endphp

            <tr class="text-bold">
                <td style="text-align: left; padding-left: 10px;">Net cash used in Operating Activities</td>
                @if ($searchType == 1)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($totalPYCashOperating)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($totalCYCashOperating)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($totalPYCashOperating) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($totalCYCashOperating) }}</td>
                    @endif

                @elseif ($searchType == 2)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($totalThisMonthCashOperating)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($totalCYCashOperating)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($totalCumulativeCashOperating)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($totalThisMonthCashOperating) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($totalCYCashOperating) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($totalCumulativeCashOperating) }}</td>
                    @endif

                @endif
            </tr>

            {{-- end of section A --}}

            {{-- net cash invessting activities rows --}}
            <tr class="text-bold">
                <td style="text-align: left; padding-left: 10px;" colspan="7" rowspan="1">B. Cash flows from Investing Activities</td>
            </tr>
            @foreach ($netCashInvestingActivitiesBalance as $key => $item)
                @if ($searchType == 1)
                    <tr>
                        <td style="text-align: left; padding-left: 10px;">{{ $item['name'].' ['.$item['code'].']' }}</td>

                        @if ($roundUp == 1)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['pyBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cyBalance'])) }}</td>
                        @else
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['pyBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cyBalance']) }}</td>
                        @endif

                    </tr>
                    @php
                    $subTotalCYNetCashInvesting += $item['cyBalance'];
                    $subTotalPYNetCashInvesting += $item['pyBalance'];
                    @endphp
                @elseif ($searchType == 2)
                    <tr>
                        <td style="text-align: left; padding-left: 10px;">{{ $item['name'].' ['.$item['code'].']' }}</td>

                        @if ($roundUp == 1)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['thisMonthBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cyBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cumulativeBalance'])) }}</td>
                        @else
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['thisMonthBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cyBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cumulativeBalance']) }}</td>
                        @endif

                    </tr>
                    @php
                    $subTotalThisMonthNetCashInvesting += $item['thisMonthBalance'];
                    $subTotalCYNetCashInvesting += $item['cyBalance'];
                    $subTotalCumulativeNetCashInvesting += $item['cumulativeBalance'];
                    @endphp
                @endif

            @endforeach
            <tr class="text-bold">
                <td style="text-align: left; padding-left: 10px;">Net cash used in Investing Activities</td>
                @if ($searchType == 1)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalPYNetCashInvesting)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalCYNetCashInvesting)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalPYNetCashInvesting) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalCYNetCashInvesting) }}</td>
                    @endif


                @elseif ($searchType == 2)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalThisMonthNetCashInvesting)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalCYNetCashInvesting)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalCumulativeNetCashInvesting)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalThisMonthNetCashInvesting) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalCYNetCashInvesting) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalCumulativeNetCashInvesting) }}</td>
                    @endif

                @endif

            </tr>

            {{-- net cash financing activities rows --}}
            <tr class="text-bold">
                <td style="text-align: left; padding-left: 10px;" colspan="7" rowspan="1">C. Cash flows from Financing Activities</td>
            </tr>
            @foreach ($netCashFinancingActivitiesBalance as $key => $item)
                @if ($searchType == 1)
                    <tr>
                        <td style="text-align: left; padding-left: 10px;">{{ $item['name'].' ['.$item['code'].']' }}</td>

                        @if ($roundUp == 1)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['pyBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cyBalance'])) }}</td>
                        @else
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['pyBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cyBalance']) }}</td>
                        @endif

                    </tr>
                    @php
                    $subTotalCYNetCashFinancial += $item['cyBalance'];
                    $subTotalPYNetCashFinancial += $item['pyBalance'];
                    @endphp
                @elseif ($searchType == 2)
                    <tr>
                        <td style="text-align: left; padding-left: 10px;">{{ $item['name'].' ['.$item['code'].']' }}</td>

                        @if ($roundUp == 1)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['thisMonthBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cyBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cumulativeBalance'])) }}</td>
                        @else
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['thisMonthBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cyBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cumulativeBalance']) }}</td>
                        @endif

                    </tr>
                    @php
                    $subTotalThisMonthNetCashFinancial += $item['thisMonthBalance'];
                    $subTotalCYNetCashFinancial += $item['cyBalance'];
                    $subTotalCumulativeNetCashFinancial += $item['cumulativeBalance'];
                    @endphp
                @endif

            @endforeach
            {{-- net cash financing activities rows --}}
            <tr class="text-bold">
                <td style="text-align: left; padding-left: 10px;">Net cash used in Financing Activities</td>
                @if ($searchType == 1)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalPYNetCashFinancial)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalCYNetCashFinancial)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalPYNetCashFinancial) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalCYNetCashFinancial) }}</td>
                    @endif

                @elseif ($searchType == 2)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalThisMonthNetCashFinancial)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalCYNetCashFinancial)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($subTotalCumulativeNetCashFinancial)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalThisMonthNetCashFinancial) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalCYNetCashFinancial) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($subTotalCumulativeNetCashFinancial) }}</td>
                    @endif

                @endif

            </tr>

            @php
                $cyNetCashIncreaseDecrease = $totalCYCashOperating + $subTotalCYNetCashInvesting + $subTotalCYNetCashFinancial;
                $pyNetCashIncreaseDecrease = $totalPYCashOperating + $subTotalPYNetCashInvesting + $subTotalPYNetCashFinancial;
                $thisMonthNetCashIncreaseDecrease = $totalThisMonthCashOperating + $subTotalThisMonthNetCashInvesting + $subTotalThisMonthNetCashFinancial;
                $cumulativeNetCashIncreaseDecrease = $totalCumulativeCashOperating + $subTotalCumulativeNetCashInvesting + $subTotalCumulativeNetCashFinancial;
            @endphp

            {{-- net cash increase/decrease --}}
            <tr class="text-bold">
                <td style="text-align: left; padding-left: 10px;">D. Net cash increase/decrease (A+B+C)</td>
                @if ($searchType == 1)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($pyNetCashIncreaseDecrease)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($cyNetCashIncreaseDecrease)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($pyNetCashIncreaseDecrease) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($cyNetCashIncreaseDecrease) }}</td>
                    @endif

                @elseif ($searchType == 2)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($thisMonthNetCashIncreaseDecrease)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($cyNetCashIncreaseDecrease)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($cumulativeNetCashIncreaseDecrease)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($thisMonthNetCashIncreaseDecrease) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($cyNetCashIncreaseDecrease) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($cumulativeNetCashIncreaseDecrease) }}</td>
                    @endif

                @endif

            </tr>

            {{-- net cash and bank --}}
            @foreach ($netCashAndBankBalance as $key => $item)
                @if ($searchType == 1)
                    <tr>
                        <td style="text-align: left; padding-left: 10px;">Add. {{ $item['name'].' ['.$item['code'].']' }} at Beginning of the year</td>

                        @if ($roundUp == 1)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['pyBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cyBalance'])) }}</td>
                        @else
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['pyBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cyBalance']) }}</td>
                        @endif

                    </tr>
                    @php
                    $netCashAndBankCurrentYear = $item['cyBalance'];
                    $netCashAndBankPreviousYear = $item['pyBalance'];
                    @endphp
                @elseif ($searchType == 2)
                    <tr>
                        <td style="text-align: left; padding-left: 10px;">{{ $item['name'].' ['.$item['code'].']' }}</td>

                        @if ($roundUp == 1)
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['thisMonthBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cyBalance'])) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($item['cumulativeBalance'])) }}</td>
                        @else
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['thisMonthBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cyBalance']) }}</td>
                            <td class="amount">{{ App\Service\EasyCode::negativeReplace($item['cumulativeBalance']) }}</td>
                        @endif

                    </tr>
                    @php
                    $netCashAndBankCurrentMonth = $item['thisMonthBalance'];
                    $netCashAndBankCurrentYear = $item['cyBalance'];
                    $netCashAndBankCumulative = $item['cumulativeBalance'];
                    @endphp
                @endif

            @endforeach
            <tr class="text-bold">
                <td style="text-align: left; padding-left: 10px;">Cash And Bank Balance at the end of the year</td>
                @if ($searchType == 1)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($pyNetCashIncreaseDecrease + $netCashAndBankPreviousYear)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($cyNetCashIncreaseDecrease + $netCashAndBankCurrentYear)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($pyNetCashIncreaseDecrease + $netCashAndBankPreviousYear) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($cyNetCashIncreaseDecrease + $netCashAndBankCurrentYear) }}</td>
                    @endif

                @elseif ($searchType == 2)

                    @if ($roundUp == 1)
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($thisMonthNetCashIncreaseDecrease + $netCashAndBankCurrentMonth)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($cyNetCashIncreaseDecrease + $netCashAndBankCurrentYear)) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace(round($cumulativeNetCashIncreaseDecrease + $netCashAndBankCumulative)) }}</td>
                    @else
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($thisMonthNetCashIncreaseDecrease + $netCashAndBankCurrentMonth) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($cyNetCashIncreaseDecrease + $netCashAndBankCurrentYear) }}</td>
                        <td class="amount">{{ App\Service\EasyCode::negativeReplace($cumulativeNetCashIncreaseDecrease + $netCashAndBankCumulative) }}</td>
                    @endif
                    @endif


            </tr>
        </tbody>
    </table>
</div>

@if ($withZero == 0)
    <script type="text/javascript">

        $('#otsTable tr').each(function() {

            var tdFirstValue  = $(this).find('td:eq(1)').text();
            var tdSecondValue = $(this).find('td:eq(2)').text();
            var tdThirdValue  = $(this).find('td:eq(3)').text();

            if (tdFirstValue == '0.00' && tdSecondValue == '0.00') {
                $(this).css('display', 'none');
            }

            @if($searchType == 2)
                if (tdFirstValue == '0.00' && tdSecondValue == '0.00' && tdThirdValue == '0.00') {
                    $(this).css('display', 'none');
                }
            @endif

        });

    </script>
@endif
