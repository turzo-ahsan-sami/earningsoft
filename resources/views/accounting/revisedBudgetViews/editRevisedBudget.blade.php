    @extends('layouts/acc_layout')
    @section('title', '| Edit Budget')
    @section('content')
        <style type="text/css">
            #budgetTable{
                font-family: arial !important;
            }

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
       //dd($ledgerWiseData);
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="" style="">
                <div class="">
                    <div class="panel panel-default" style="background-color:#708090;">
                        <div class="panel-heading" style="padding-bottom:0px">
                            <div class="panel-options">
                                <button id="printIcon" class="btn btn-info pull-right print-icon" style="">
                                    <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                                </button>
                            </div>
                            <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Edit Budget</h3>
                        </div>

                        <div class="panel-body panelBodyView" ><!--start of panel body-->

                            <div class="viewTitle">
                                <a href="{{url('/viewBudget')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                                </i>Budget List</a>
                            </div>

                            <div id="printDiv">
                                <!--This div is going to print company details and  ots account Statement   !-->
                                <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span style="font-size:14px;">{{ $loadBudgetTableArr['company']->name }}</span><br/>
                                    <span style="font-size:11px;">{{ $loadBudgetTableArr['company']->address }}</span><br/>
                                    <span style="text-decoration: underline;  font-size:14px;">Fiscal Year Budget</span></br>

                                </div>

                                <div class="row">

                                    <div class="col-md-12"  style="font-size: 12px;">

                                        <span>
                                            <span style="color: black; float: left;">
                                                <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                                <span>{{ $loadBudgetTableArr['projectName'] }}</span>
                                            </span>
                                            <br>
                                            <span style="color: black; float: left;">
                                                <span style="font-weight: bold;">Fiscal Year: <?php echo str_repeat('&nbsp;', 3);?></span>
                                                <span>{{ $loadBudgetTableArr['fiscalYearName'] }}</span>
                                            </span>

                                             <br>
                                            <span style="color: black; float: left;">
                                                <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                                <span>{{ $loadBudgetTableArr['branchName'] }}</span>
                                            </span>
                                            <br>
                                            <span style="color: black; float: left;">
                                                <span style="font-weight: bold;">Account Type: <?php echo str_repeat('&nbsp;', 3);?></span>
                                                <span>{{ $loadBudgetTableArr['accountTypeName'] }}</span>
                                            </span>

                                        </span>

                                        <span>
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
                                                    {!! Form::text('debit-'.$item['id'],  number_format($item['budgetDebitAmount'],2, '.',''),['class'=>'form-control txtCal debit budget-input input-sm text-right','autocomplete'=>'off']) !!}
                                                </td>
                                                <td class="budget">
                                                    {!! Form::text('credit-'.$item['id'],   number_format($item['budgetCreditAmount'],2, '.',''),['class'=>'form-control txtCalCredit credit text-right budget-input','autocomplete'=>'off']) !!}
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
                                                <td class="text-right total_sum_value" data-amount="{{ $totalBalance['budgetDebit'] }}" style="padding-right: 5px">0.00</td>
                                                <td class="text-right total_sum_value_credit" data-amount="{{ $totalBalance['budgetCredit'] }}" style="padding-right: 5px">0.00</td>
                                            </tr>
                                        </thead>

                                    </table>
                                        @else
                                         <table id="incomeTable" class="table table-striped table-bordered" style="color:black; border-collapse: collapse;" border= "1px solid ash;">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" style="width: 10%">Transaction Heads</th>
                                                    <th colspan="2" style="width: 40%">Current Year Closing</th>
                                                    <th colspan="12" style="width: 50%">Monthly Budget</th>
                                                    <th rowspan="2" style="width: 50%">Total</th>
                                                </tr>
                                                <tr>
                                                    <th class="amount-title">Dr</th>
                                                    <th class="amount-title">Cr</th>
                                                    @foreach($loadBudgetTableArr['monthsArr'] as $monthsArr)
                                                    <th>{{$monthsArr}}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>

                                        <tbody>
                                            @foreach ($ledgerWiseData as $key => $item)
                                                <tr class="ledgerTr level level-final">
                                                    <td class="title" style="text-align: left;" name="tdName"><span class="ledgerTd">{{ $item['name'].' ['. $item['code']. ']' }}</span></td>
                                                    <td class="amount" data-amount="{{ $item['debitBalance'] }}">{{ number_format($item['debitBalance'], 2) }}</td>
                                                    <td class="amount" data-amount="{{ $item['creditBalance'] }}">{{ number_format($item['creditBalance'], 2) }}</td>
                                                    @if(count($item['monthsData']) > 0)
                                                       @foreach($item['monthsData'] as $monthData)
                                                            <td class="budget">
                                                                {!! Form::text('month-'.$item['id'], $monthData ,['class'=>'form-control budget-input input-sm text-right txtCal check' ,'autocomplete'=>'off']) !!}
                                                            </td>

                                                        @endforeach
                                                    @else
                                                         @foreach($loadBudgetTableArr['monthsArr'] as $monthData)
                                                            <td class="budget">
                                                                {!! Form::text('month-'.$item['id'], number_format(0, 2) ,['class'=>'form-control budget-input input-sm text-right txtCal check' ,'autocomplete'=>'off']) !!}
                                                            </td>

                                                        @endforeach
                                                    @endif
                                                        @if($loadBudgetTableArr['accountType'] == 12 )
                                                            <td>
                                                                {!! Form::text('month-'.$item['id'],  number_format($item['budgetCreditAmount'],2,'.', '') ,['class'=>'form-control incomeTotal' ,'readonly'=>'readonly','autocomplete'=>'off']) !!}
                                                            </td>
                                                        @elseif($loadBudgetTableArr['accountType'] == 13 )
                                                            <td>
                                                               {!! Form::text('month-'.$item['id'],number_format($item['budgetDebitAmount'],2,'.', '') ,['class'=>'form-control incomeTotal' ,'readonly'=>'readonly','autocomplete'=>'off']) !!}
                                                            </td>
                                                        @endif
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
                                                <td class="text-right amount" data-amount="" style="padding-right: 5px">
                                                    {{ number_format($totalBalance['credit'], 2) }}
                                                </td>
                                                <td class="text-right amount" data-amount="" style="padding-right: 5px">
                                                   
                                                </td>
                                                <td class="text-right amount" data-amount="" style="padding-right: 5px" colspan="12">
                                                    
                                                </td>
                                            </tr>
                                        </thead>
                                </table>
                                @endif
                               
                               

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

                        </div><!--end of panel body-->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

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
                 //console.log('ok');

            }else{
                e.preventDefault();
                alert('Please input only number');
            }
            
            if($(this).val().indexOf(' . ') !== -1 && e.keycode == 190){
             e.preventDefault(); 
            }
                
       });
        


        $('#budget-form').submit(function( event ) {
            event.preventDefault();
            var debit = {};
            var credit = {};
            var month = {};
            var total = {};
            var budgetId = {{$budgetInfo->id}};
            //alert(budgetId);
            event.preventDefault();
           
           
      
            $('#incomeTable .ledgerTr').each(function(){  
                var totalMonth = [];
                var totalsValue = []; 
                //row selected 
                var selectRow = $(this);  
                //row wise value
                var months = $(selectRow).children('td').find('.txtCal');
               
               
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
           
            formData.append('debit', JSON.stringify(debit));
            formData.append('budgetId', budgetId);
            formData.append('credit', JSON.stringify(credit));
            formData.append('month', JSON.stringify(month));
            formData.append('total', JSON.stringify(total));
            formData.append('_token', csrf);

             $.ajax({
                processData: false,
                contentType: false,
                type: 'post',
                url: '../editRevisedBudgetItem',
                data: formData,
                dataType: 'json',
                success: function( _response ){
                    alert(_response);
                    window.location.href = '{{url('viewRevisedBudget/')}}';
                },
                error: function( _response ){
                    alert("errors._response");
                   //window.location.href = '{{url('addBudget/')}}';
                }
            });
       
            // $.ajax({
            //     type: 'post',
            //     url: '../editBudgetItem',
            //     data: $('#budget-form').serialize(),
            //     dataType: 'json',
            //     success: function( _response ){
            //         alert(_response);
            //         window.location.href = '{{url('viewBudget/')}}';
            //     },
            //     error: function( _response ){
            //         alert(_response.errors);
            //     }
            // });
        });
        //asset liability and equity calculation
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
        //income and expanses
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
        // $('.amount').each(function(){
        //     // console.log($(this).text())
        //     if ($(this).text() == 0.00) {
        //         $(this).text('-');
        //     }
        // });

        

        // print script
        $("#printIcon").click(function(event) {

            var mainContents = document.getElementById("reportingDiv").innerHTML;

            var headerContents = '';

            var printStyle = '<style>.amount{text-align: right;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px;} table, td,th {border:1px solid #222;} .text-bold{font-weight:bold} table{float:left;height:auto;padding:0px;border-collapse: collapse;width:100%;font-size:11px;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;}  tr{ page-break-inside:avoid; page-break-after:auto }</style><style media="print">@page{margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}} .level-1 td, .level-2 td, .level-3 td, .level-4 td, .level-constant td{font-weight: bold !important;padding-left: 5px !important;text-transform: uppercase;}.level-1 td{font-size: 1.04em;}.level-2 td{font-size: 1.00em;/* padding-left: 15px !important; */}.level-3 td{font-size: .96em;/* padding-left: 25px !important; */}.level-4 td{font-size: .92em;/* padding-left: 35px !important; */}.level-constant td{font-size: .88em;/* padding-left: 45px !important; */}.level-transformed td{font-weight: normal !important;font-size: .88em;padding-left: 5px !important;text-transform: capitalize;}.level-final td{font-weight: normal !important;text-transform: none;}.total td{padding-left: 5px !important;font-size: 1.05em;}</style>';

            // var mainContents = document.getElementById("printDiv").innerHTML;

            var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By<span style='display:inline-block; width: 33%; padding-top:40px;'></span> Checked By<span style='display:inline-block; width: 33%; padding-right:15px; padding-top:40px;'></span>Approved By</div>";

            var printContents = '<div id="order-details-wrapper" style="padding: 10px;">' + printStyle + mainContents + footerContents +'</div>';

            var win = window.open('','printwindow');
            win.document.write(printContents);
            win.print();
            win.close();
        });

        $("#loadingModal").hide();

    }); /* Ready to print */
    </script>

    @endsection
