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
        .amount-title{
            width: 5%;
        }
        .incomeTotal {
           text-align: right;
           padding-left: 5px;
        }
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

    </style>
    @php
     // $branchName = DB::table('gnr_branch')->where('id', $branchId)->value('name');
    @endphp
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
                        <span style="font-weight: bold;">Acoount Type: <?php echo str_repeat('&nbsp;', 3);?></span>
                        <span>{{ $loadBudgetTableArr['accountTypeName'] }}</span>
                    </span>
                     <br>
                    <span style="color: black; float: left;">
                        <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                        <span>{{ $loadBudgetTableArr['branchName'] }}</span>
                    </span>


                </span>

                <span>
                    <span style="color: black; float: right;">
                        <span style="font-weight: bold;">Currency: <?php echo str_repeat('&nbsp;', 3);?></span>
                        {{-- <span>{{ $loadBudgetTableArr['currencyName'] }}</span> --}}
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

        @if($loadBudgetTableArr['accountType'] == 1 ||  $loadBudgetTableArr['accountType'] == 6 || $loadBudgetTableArr['accountType'] == 9)
        <table id="budgetTable" class="table table-striped table-bordered" style="color:black; border-collapse: collapse;" border= "1px solid ash;">
            <thead>
                <tr>
                    <th rowspan="2" width="20%">Transaction Heads</th>
                    <th colspan="2" width="15%">Current Year Closing</th>
                    <th colspan="2" width="15%">Next Year Budget</th>
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
                            {!! Form::text('debit-'.$item['id'], number_format('0.00', 2) ,['class'=>'form-control txtCal debit budget-input input-sm text-right','autocomplete'=>'off']) !!}
                        </td>
                        <td class="budget">
                            {!! Form::text('credit-'.$item['id'], number_format('0.00', 2) ,['class'=>'form-control txtCalCredit credit text-right budget-input','autocomplete'=>'off']) !!}
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
                    <td class="text-right total_sum_value" data-amount="" style="padding-right: 5px">0.00</td>
                    <td class="text-right total_sum_value_credit" data-amount="" style="padding-right: 5px">0.00</td>
                </tr>
            </thead>

        </table>
        @else
        <div class="table-responsive">
            <table id="incomeTable" class="table table-striped table-bordered" style="color:black; border-collapse: collapse;" border= "1px solid ash;">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 15%">Transaction Heads</th>
                    <th colspan="2" style="width: 10%">Current Year Closing</th>
                    <th colspan="12" style="width: 65%">Monthly Budget</th>
                    <th rowspan="2" style="width: 5%">Total</th>
                </tr>
                <tr>
                    <th class="amount-title">Dr</th>
                    <th class="amount-title">Cr</th>
                    @foreach($loadBudgetTableArr['monthsArr'] as $monthsArr)
                    <th class="amount-title" style="font-size: 10px;">{{$monthsArr}}</th>
                    @endforeach
                   
                </tr>
            </thead>

            <tbody>

                @foreach ($ledgerWiseData as $key => $item)
                    <tr class="ledgerTr level level-final">
                        <td class="title" style="text-align: left;" name="tdName"><span class="ledgerTd">{{ $item['name'].' ['. $item['code']. ']' }}</span></td>
                        <td class="amount" data-amount="{{ $item['debitBalance'] }}">{{ number_format($item['debitBalance'], 2) }}</td>
                        <td class="amount" data-amount="{{ $item['creditBalance'] }}">{{ number_format($item['creditBalance'], 2) }}</td>
                        @php
                            $i = 0;
                        @endphp
                        @foreach($loadBudgetTableArr['monthsArr'] as $monthsArr)
                        <td class="budget">
                            {!! Form::text('month-'.$item['id'], number_format('0.00', 2) ,['class'=>'form-control budget-input input-sm text-right txtCal check  month-data-'.$i ,'autocomplete'=>'off']) !!}
                        </td>
                        @php
                            $i++;
                        @endphp
                         @endforeach
                         <td>
                               {!! Form::text('month-'.$item['id'], number_format('0.00', 2) ,['class'=>'form-control incomeTotal' ,'readonly'=>'readonly','autocomplete'=>'off']) !!}
                         </td>
                        {{--  <td data-amount="" >0.00</td> --}}
                    </tr>
                @endforeach
            </tbody>

            <thead>
                <tr style="font-weight: bold;" class="total">
                    <td style="text-align: center;">Total</td>
                    <td class="text-right amount" data-amount="{{$totalBalance['debit'] }}" style="padding-right: 5px">
                        {{ number_format($totalBalance['debit'], 2) }}
                    </td>
                    <td class="text-right amount" data-amount="{{ $totalBalance['credit'] }}" style="padding-right: 5px">
                        {{ number_format($totalBalance['credit'], 2) }}
                    </td>
                    @php
                        $j = 0;
                    @endphp
                    @foreach($loadBudgetTableArr['monthsArr'] as $monthData)
                   <td class="text-right month-total-{{$j}}" style="padding-right: 5px"></td>
                   @php
                        $j++;
                    @endphp
                    @endforeach
                    <td class="text-right amount" data-amount="{{ $totalBalance['budgetCredit'] }}" style="padding-right: 5px" colspan="12">
                        {{ $totalBalance['budgetCredit'] }}
                    </td>
                </tr>
            </thead>

        </table>
        </div>
        @endif
        {!! Form::hidden('fiscalYearId', $loadBudgetTableArr['fiscalYearId']) !!}
        {!! Form::hidden('projectId', $loadBudgetTableArr['projectId']) !!}
        {!! Form::hidden('accountType', $loadBudgetTableArr['accountType']) !!}
        {!! Form::hidden('branchId', $loadBudgetTableArr['branchId']) !!}

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
        var debit = {};
        var credit = {};
        var month = {};
        var total = {};
        var a= [];
        event.preventDefault();
       
        var fiscalYearId = {{$loadBudgetTableArr['fiscalYearId']}};
        var projectId = {{$loadBudgetTableArr['projectId']}};
        var accountType = {{ $loadBudgetTableArr['accountType']}};
        var branchId = {{ $loadBudgetTableArr['branchId']}};
        //alert(branchId);
      
        $('#incomeTable .ledgerTr').each(function(){  
            var totalMonth = [];
            var totalsValue = []; 
            //row selected 
            var selectRow = $(this);  
            //row wise value
            var months = $(selectRow).children('td').find('.txtCal');
            //var totalValue = $(selectRow).children('td').find('.incomeTotal').val();
            //totalsValue.push(totalValue);
           
            //looping minths
            months.each(function(){
                 totalMonth.push($(this).val());
            })
            //month and total push in arrary
            month[$(selectRow).children('td').find('.txtCal').attr('name')] = totalMonth;
            total[$(selectRow).children('td').find('.txtCal').attr('name')] =$(selectRow).children('td').find('.incomeTotal').val();    
        });
        
            $('.debit').each(function(){ 
               debit[$(this).attr("name")] = $(this).val(); 
            });
       
       
            $('.credit').each(function(){
                credit[$(this).attr("name")] = $(this).val();
            });
        
            var csrf = "{{csrf_token()}}";


            formData = new FormData();
            formData.append('fiscalYearId', fiscalYearId);
            formData.append('projectId', projectId);
            formData.append('accountType', accountType);
            formData.append('branchId', branchId);
            formData.append('debit', JSON.stringify(debit));
            formData.append('credit', JSON.stringify(credit));
            formData.append('month', JSON.stringify(month));
            formData.append('total', JSON.stringify(total));
            formData.append('_token', csrf);
       
            $.ajax({
                processData: false,
                contentType: false,
                type: 'post',
                url: './addBudgetItem',
                data: formData,
                dataType: 'json',
                success: function( _response ){
                    alert(_response);
                    window.location.href = '{{url('viewBudget/')}}';
                },
                error: function( _response ){
                    alert("errors._response");
                   //window.location.href = '{{url('addBudget/')}}';
                }
            });
    });


    $(document).ready(function() {
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

        $(".budget").keypress(function (e) {
            if((e.which >= 48 && e.which <= 57) || e.which == 8 || e.which == 9 || e.which == 37 || e.which == 39 || e.which == 46 || e.which == 190){
                // console.log('ok');

            }else{
                e.preventDefault();
                alert('Please input only number');
            }
            
            if($(this).val().indexOf(' . ') !== -1 && e.keycode == 190){
             e.preventDefault(); 
            }
                
        });
        //asset equity
            $("#incomeTable").on('input', '.txtCal', function () {
                var calculated_total_sum = 0;
                var getValue = $(this).closest('tr').find('.txtCal');
                 console.log(getValue);
                $(getValue).each(function () {
                    var get_textbox_value = $(this).val();
                     console.log(get_textbox_value);
                        //alert(get_textbox_value);
                           if ($.isNumeric(get_textbox_value)) {
                              calculated_total_sum += parseFloat(get_textbox_value);
                            }                  
                });
                var total = calculated_total_sum.toFixed(2);
                $(this).closest('tr').find('.incomeTotal').val(total);
            });
        //asset equity

        
        $("#budgetTable").on('input', '.txtCal', function () {
            var calculated_total_sum = 0;
            
            $('#budgetTable .txtCal').each(function () {
                var get_textbox_value = $(this).val();
                 //console.log(get_textbox_value);
                    //alert(get_textbox_value);
                       if ($.isNumeric(get_textbox_value)) {
                          calculated_total_sum += parseFloat(get_textbox_value);
                        } 
            });
            
            var total = calculated_total_sum.toFixed(2);

            $('.total_sum_value').html(total);

        });
        $("#budgetTable").on('input', '.txtCalCredit', function () {
            var calculated_total_sum = 0;
            
            $('#budgetTable .txtCalCredit').each(function () {
                var get_textbox_value = $(this).val();
               
                   if ($.isNumeric(get_textbox_value)) {
                      calculated_total_sum += parseFloat(get_textbox_value);
                    } 
            });
            var total = calculated_total_sum.toFixed(2);
            $('.total_sum_value_credit').html(total);

        });


        //get total  month value from database
        var totalMonthCal = 0;
        $('#incomeTable .incomeTotal').each(function(){
          var totalMonth = $(this).val(); 
          totalMonthCal += parseFloat(totalMonth);
        });
       
        var monthFormat = totalMonthCal.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        $(".incomeTotalSum").html(monthFormat);

        //get month value from database
        var monthCalArr = [0,1,2,3,4,5,6,7,8,9,10,11];
        $(monthCalArr).each(function(index ,value){
            totalMonthData  = 0;
            var monthsDataInfo =  $('#incomeTable .month-data-' + value);
            $(monthsDataInfo).each(function(){
                var singleMonthData = $(this).val();
                totalMonthData += parseFloat(singleMonthData);
            });
            var monthDataFormat = totalMonthData.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            console.log(monthDataFormat);
            $('.month-total-'+value).html(monthDataFormat);  
        })
        
        $("#incomeTable").on('input', '.txtCal', function () {
            var monthCalArr = [0,1,2,3,4,5,6,7,8,9,10,11];
            $(monthCalArr).each(function(index ,value){
                totalMonthData  = 0;
                var monthsDataInfo =  $('#incomeTable .month-data-' + value);
                $(monthsDataInfo).each(function(){
                    var singleMonthData = $(this).val();
                    totalMonthData += parseFloat(singleMonthData);
                });
                var monthDataFormat = totalMonthData.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                console.log(monthDataFormat);
                $('.month-total-'+value).html(monthDataFormat);  
            })
        });

    });
    // income value summation
    </script>
