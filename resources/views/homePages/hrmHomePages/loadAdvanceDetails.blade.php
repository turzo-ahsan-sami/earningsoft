@extends('hr_main')
@section('title', '| Advance Details' )
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
                                <div class="panel-title">{{ $advTypeName }} Details</div>
                            </div>

                            <div class="panel-body">

                                {{-- @if(count($data['receive']) > 0) --}}
                                    {{-- <h2 >Advanced Salary Loan Receive</h2> --}}
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">Sl</th>
                                                <th>Date</th>
                                                <th>Payment</th>
                                                <th>Receive</th>
                                                <th>Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @php
                                            $sl = 1;
                                            $balance = 0;
											$paymentTotal = 0;
											$receiveTotal = 0;
                                            @endphp
                                            @foreach ($data as $key => $item)
												@php
													$balance += $item['payment'] - $item['receive'];
												@endphp
                                                <tr>
                                                    <td>{{ $sl++ }}</td>
                                                    <td class="text-center">{{ $item['date'] }}</td>
                                                    <td class="amount">{{ number_format($item['payment'], 2) }}</td>
                                                    <td class="amount">{{ number_format($item['receive'], 2) }}</td>
                                                    <td class="amount">{{ number_format($balance, 2) }}</td>
                                                </tr>
												@php
													$paymentTotal += $item['payment'];
													$receiveTotal += $item['receive'];
												@endphp
                                            @endforeach

                                            <tr>
                                                <td colspan="2" class="text-center text-strong">Total:</td>
                                                <td class="amount text-strong">{{ number_format($paymentTotal, 2) }}</td>
                                                <td class="amount text-strong">{{ number_format($receiveTotal, 2) }}</td>
                                                <td class="amount text-strong">{{ number_format($paymentTotal - $receiveTotal, 2) }}</td>
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
