<style media="screen">

    .level-final td {
        font-size: .92em;
    }
    .level-final td.title {
        padding-left: 5px !important;
    }
    .total td {
        padding-left: 5px !important;
        font-size: 1.15em;
    }
    .amount-title {
        width: 15%;
    }
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

</style>

<div id="printDiv">
    <!--This div is going to print company details and  ots account Statement   !-->
    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}

        <span style="font-size:14px;">{{ $loadBudgetTableArr['company']->name }}</span><br/>
        <span style="font-size:11px;">{{ $loadBudgetTableArr['company']->address }}</span><br/>
        <span style="text-decoration: underline;  font-size:14px;">Fiscal Year Budget</span></br>
        <span style="text-decoration: underline;  font-size:14px;">As on Fiscal Year {{ $loadBudgetTableArr['fiscalYearName'] }}</span></br>

    </div>

    <div class="row">       {{-- div for Reporting Info --}}

        <div class="col-md-12"  style="font-size: 12px;">

            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{ $loadBudgetTableArr['accProjectName'] }}</span>
                </span>
                <br>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Acoount Type: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{ $loadBudgetTableArr['accountTypeName'] }}</span>
                </span>


            </span>

            <span>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Currency: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{ $loadBudgetTableArr['currencyName'] }}</span>
                </span>
                <br>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{ \Carbon\Carbon::now()->format('d-m-Y g:i A') }}</span>
                </span>

            </span>

        </div>
    </div><br>

    {!! Form::open(array('url' => '', 'id' => 'budget-form', 'role' => 'form')) !!}

    <table id="budgetTable" class="table table-striped table-bordered" style="color:black; border-collapse: collapse;" border= "1px solid ash;">
        <thead>
            <tr>
                <th rowspan="2">Transaction Heads</th>
                <th colspan="2">Current Year Closing</th>
                <th colspan="2">Next Year Budget</th>
            </tr>
            <tr>
                <th class="amount-title">Dr</th>
                <th class="amount-title">Cr</th>
                <th class="amount-title">Dr</th>
                <th class="amount-title">Cr</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($ledgerWiseData as $key => $item)
                <tr class="ledgerTr level level-final">
                    <td class="title" style="text-align: left;">{{ $item['name'].' ['. $item['code']. ']' }}</td>
                    <td class="amount" data-amount="{{ $item['debitBalance'] }}">{{ number_format($item['debitBalance'], 2) }}</td>
                    <td class="amount" data-amount="{{ $item['creditBalance'] }}">{{ number_format($item['creditBalance'], 2) }}</td>
                    <td class="budget">
                        {!! Form::text('debit-'.$item['id'], $item['budgetDebitAmount'] ,['class'=>'form-control input-sm text-right','autocomplete'=>'off', 'placeholder'=>'0.00']) !!}
                    </td>
                    <td class="budget">
                        {!! Form::text('credit-'.$item['id'], $item['budgetCreditAmount'] ,['class'=>'form-control input-sm text-right','autocomplete'=>'off', 'placeholder'=>'0.00']) !!}
                    </td>
                </tr>
            @endforeach

        </tbody>

        <thead>
            <tr style="font-weight: bold;" class="total">
                <td style="text-align: center;">Total</td>
                <td class="text-right amount" data-amount="{{ $totalBalance['debit'] }}" style="padding-right: 5px">
                    {{ number_format($totalBalance['debit'], 2) }}
                </td>
                <td class="text-right amount" data-amount="{{ $totalBalance['credit'] }}" style="padding-right: 5px">
                    {{ number_format($totalBalance['credit'], 2) }}
                </td>
                <td class="text-right amount" data-amount="{{ $totalBalance['budgetDebit'] }}" style="padding-right: 5px">
                    {{ $totalBalance['budgetDebit'] }}
                </td>
                <td class="text-right amount" data-amount="{{ $totalBalance['budgetCredit'] }}" style="padding-right: 5px">
                    {{ $totalBalance['budgetCredit'] }}
                </td>
            </tr>
        </thead>

    </table>
    {!! Form::hidden('fiscalYearId', $loadBudgetTableArr['fiscalYearId']) !!}
    {!! Form::hidden('accProjectId', $loadBudgetTableArr['accProjectId']) !!}

    <div class="col-md-12 text-right">
        <div class="form-group" style="font-size: 13px; padding: 4px 12px;">
            {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
            <div class="col-sm-12" style="padding-top: 13px;">
                {!! Form::submit('Save', ['id' => 'budgetSubmit', 'class' => 'btn btn-primary btn-md', 'style'=>'font-size:12px']); !!}
            </div>
        </div>
    </div>

    {!! Form::close()  !!}

</div>

<script>

$('#budget-form').submit(function( event ) {
    event.preventDefault();
    $.ajax({
        type: 'post',
        url: './addBudgetItem',
        data: $('#budget-form').serialize(),
        dataType: 'json',
        success: function( _response ){
            alert(_response);
            window.location.href = '{{url('viewBudget/')}}';
        },
        error: function( _response ){
            alert(_response.errors);
        }
    });
});

$(document).ready(function() {

    $('.amount').each(function(){
        // console.log($(this).text())
        if ($(this).text() == 0.00) {
            $(this).text('-');
        }
    });
});

</script>
