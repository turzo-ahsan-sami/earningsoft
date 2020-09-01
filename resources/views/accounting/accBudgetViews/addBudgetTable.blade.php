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

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        /* display: none; <- Crashes Chrome on hover */
        -webkit-appearance: none;
        margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
    }

    input[type=number] {
        -moz-appearance:textfield; /* Firefox */
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
                    <span>{{ $loadBudgetTableArr['projectName'] }}</span>
                </span>
                <br>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Branch: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{ $loadBudgetTableArr['branchName'] }}</span>
                </span>
            </span>

            <span>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">Acoount Type: <?php echo str_repeat('&nbsp;', 3);?></span>
                    <span>{{ $loadBudgetTableArr['accountTypeName'] }}</span>
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
                <th rowspan="2" style="width: 20%;">Ledger Heads</th>
                <th colspan="{{ count($loadBudgetTableArr['monthsArray']) }}" style="width: 70%;">Monthly Budget</th>
                <th rowspan="2" style="width: 10%;">Total</th>
            </tr>
            <tr>
                @foreach ($loadBudgetTableArr['monthsArray'] as $key => $month)
                    <th>{{ $month }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            {{-- display html tree --}}
            {!! $treeView !!}
        </tbody>

    </table>
    {!! Form::hidden('fiscalYearId', $loadBudgetTableArr['fiscalYearId']) !!}
    {!! Form::hidden('projectId', $loadBudgetTableArr['projectId']) !!}
    {!! Form::hidden('branchId', $loadBudgetTableArr['branchId']) !!}
    {!! Form::hidden('accountType', $loadBudgetTableArr['accountType']) !!}

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

    $(".budget-input").on('input',function(){
        var projectId = "{{ $loadBudgetTableArr['projectId'] }}";
        var name = $(this).attr("name");
        var month = name.split('_')[0];
        var inputVal = $(this).val();
        var ledgerTr = $(this).closest('tr');
        var parent = ledgerTr.attr('data-parent');

        calculateTotal(ledgerTr);
        checkDependentLedgers(projectId, name, inputVal);
        levelDataCalculator(month, parent);
    });

    $(".budget-input").on("keypress keyup blur",function (event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    $('.budget-input').on({
        focus: function () {
            if (this.value == '0.00') this.value = '';
        },
        blur: function () {
            if (this.value === '') {
                this.value = '0.00';
            }
            else {
                $(this).val(Number(this.value).toFixed(2));
            }
        }
    });

    $('#budget-form').submit(function( event ) {
        event.preventDefault();
        $('#loadingModal').show();
        $.ajax({
            type: 'post',
            url: './addBudgetItem',
            data: $('#budget-form').serialize(),
            dataType: 'json',
            success: function( _response ){
                $('#loadingModal').hide();
                toastr.success(_response.responseText, opts);
                setTimeout(function(){
                    window.location.href = '{{url('viewBudget/')}}';
                }, 2000);
            },
            error: function( _response ){
                alert(_response.errors);
            }
        });
    });

    function calculateTotal(ledgerTr) {

        var totalValue = 0;
        var monthColumns = ledgerTr.find('.budget-input');
        var totalValueDisplay = ledgerTr.find('.total-budget');

        monthColumns.each(function() {
            var value = Number($(this).val());
            if (!isNaN(value)) totalValue += value;
        });

        totalValueDisplay.val(totalValue.toFixed(2));

    }

    function levelDataCalculator(month, parent) {

        var parentClass = month+'_amount';
        var parentName = month+'_val';
        var tdClass = month+'_amount';
        var tdName = month+'_val';
        var parentValue = 0;
        var parentTotalValue = 0;

        if ($('tr[data-parent="'+parent+'"]').hasClass('level-final') == true) {

            $('tr[data-parent="'+parent+'"]').each(function(){

                var ledgerId = $(this).attr('data-id');
                var inputAttr = month+'_'+ledgerId;
                var val = Number($('input[name="'+inputAttr+'"]').val());
                var totalVal = Number($('input[name="total_'+ledgerId+'"]').val());
                parentValue += val;
                parentTotalValue += totalVal;

            });

            // month column
            $('tr[data-id="'+parent+'"]').find('.'+parentClass).text(parentValue.toFixed(2));
            $('tr[data-id="'+parent+'"]').find('input[name="'+tdName+'"]').val(parentValue.toFixed(2));
            // total column
            $('tr[data-id="'+parent+'"]').find('.total-amount').text(parentTotalValue.toFixed(2));
            $('tr[data-id="'+parent+'"]').find('input[name="total_val"]').val(parentTotalValue.toFixed(2));

        }
        else {

            $('tr[data-parent="'+parent+'"]').each(function(){

                var val = Number($(this).find('input[name="'+tdName+'"]').val());
                var totalVal = Number($(this).find('input[name="total_val"]').val());
                parentValue += val;
                parentTotalValue += totalVal;

            });

            // month column
            $('tr[data-id="'+parent+'"]').find('.'+parentClass).text(parentValue.toFixed(2));
            $('tr[data-id="'+parent+'"]').find('input[name="'+tdName+'"]').val(parentValue.toFixed(2));
            // total column
            $('tr[data-id="'+parent+'"]').find('.total-amount').text(parentTotalValue.toFixed(2));
            $('tr[data-id="'+parent+'"]').find('input[name="total_val"]').val(parentTotalValue.toFixed(2));

        }

        parent = $('tr[data-id="'+parent+'"]').attr('data-parent');

        if (parent != 0) {
            levelDataCalculator(month, parent);
        }

    }

    function checkDependentLedgers(projectId, name, inputVal) {

        $.ajax({
            type: 'post',
            url: './checkDependentLedgers',
            data: {projectId: projectId, name: name, val: inputVal},
            dataType: 'json',
            success: function(data){
                if (data.length > 0) {
                    $.each(data, function(key, val) {
                        var name = val.name;
                        var month = name.split('_')[0];
                        var value = Number(val.value).toFixed(2);
                        var ledgerTr = $('input[name="'+name+'"]').closest('tr');
                        var parent = ledgerTr.attr('data-parent');

                        $('input[name="'+name+'"]').val(value);
                        calculateTotal(ledgerTr);
                        levelDataCalculator(month, parent);

                        if (val.nextLevelExist > 0) {
                            var ledgerTr = $('input[name="'+name+'"]').closest('tr');
                            checkDependentLedgers(projectId, name, value);
                        }
                    });
                }

            }
        });

    }

</script>
