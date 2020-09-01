@extends('hr_main')
@section('title', '| Gratuity Details' )
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
                                <div class="panel-title">Gratuity Fund Details</div>
                            </div>

                            <div class="panel-body">

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                {{-- <th style="width: 50px;">Sl</th> --}}
                                                <th>Sl</th>
                                                <th>Date</th>
                                                <th>Basic Salary</th>
                                                <th>Job Age</th>
                                                <th>Add Current Period</th>
                                                <th>Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @php
                                            $sl = 1;
                                            $total = 0;
                                            @endphp
                                            @foreach ($gratuityInfo as $key => $info)
												@php
													$total += $info['org'];
												@endphp
                                                <tr>
                                                    @if ($info['date'] == 'Opening Balance')
                                                        <td class="text-center">{{ $info['date'] }}</td>
                                                        <td class="text-center">{{ $info['obDate'] }}</td>
                                                        <td class="amount"></td>
                                                        <td class="text-center">
                                                            {{ $info['obJobDuration']['year'].'y '. $info['obJobDuration']['month'].'m'}}
                                                        </td>
                                                        <td class="amount">{{ number_format($info['org'], 2) }}</td>
                                                        <td class="amount">{{ number_format($total, 2) }}</td>
                                                    @else
                                                        <td class="text-center">{{ $sl++ }}</td>
                                                        <td class="text-center">{{ $info['date'] }}</td>
                                                        <td class="amount">{{ number_format($info['basicSalary'], 2) }}</td>
                                                        <td class="text-center">
                                                            {{ $info['jobDuration']['year'].'y '. $info['jobDuration']['month'].'m'}}
                                                        </td>
                                                        <td class="amount">{{ number_format($info['org'], 2) }}</td>
                                                        <td class="amount">{{ number_format($total, 2) }}</td>
                                                    @endif

                                                </tr>

                                            @endforeach

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
