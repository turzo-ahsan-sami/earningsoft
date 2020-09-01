@extends('hr_main')
@section('title', '| EDPS Details' )
@section('content')

<style media="screen">
    .amount{
        text-align: right;
        padding-right: 5px !important;
    }
</style>
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            <div class="panel panel-default panel-border">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="panel-heading">
                                <div class="panel-title">Employee DPS Details</div>
                            </div>

                            <div class="panel-body">

                                {{-- @if(count($data['receive']) > 0) --}}
                                    {{-- <h2 >Advanced Salary Loan Receive</h2> --}}
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">Sl</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Interest</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @php
                                            $sl = 1;
                                            $total = 0;
											$ownTotal = 0;
											$interestTotal = 0;
                                            @endphp
                                            @foreach ($edpsInfo as $key => $info)
												@php
													$total += $info['own'] + $info['interest'];
												@endphp
                                                <tr>
                                                    @if ($info['date'] == 'Opening Balance')
                                                        <td class="text-center" colspan="2">{{ $info['date'] }}</td>
                                                    @else
                                                        <td class="text-center">{{ $sl++ }}</td>
                                                        <td class="text-center">{{ $info['date'] }}</td>
                                                    @endif
                                                    <td class="amount">{{ number_format($info['own'], 2) }}</td>
                                                    <td class="amount">{{ number_format($info['interest'], 2) }}</td>
                                                    <td class="amount">{{ number_format($total, 2) }}</td>
                                                </tr>
												@php
													$ownTotal += $info['own'];
													$interestTotal += $info['interest'];
												@endphp
                                            @endforeach

                                            <tr>
                                                <td colspan="2" class="text-center text-strong">Total:</td>
                                                <td class="amount text-strong">{{ number_format($ownTotal, 2) }}</td>
                                                <td class="amount text-strong">{{ number_format($interestTotal, 2) }}</td>
                                                <td class="amount text-strong">{{ number_format($ownTotal + $interestTotal, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                {{-- @endif --}}

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>
@endsection
