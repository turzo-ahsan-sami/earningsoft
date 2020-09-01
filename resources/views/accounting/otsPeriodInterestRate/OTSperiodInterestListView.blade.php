@extends('layouts/acc_layout')
@section('title', '|OTS Period Interest Rate')
@section('content')

@include('microfin/reports/advanceDueListViews/AdvanceDueListAjax')

<style media="screen">
th, td {
  padding: 2px !important;
}
</style>

<div class="container-fluid">

<!-- Start of the Dropdown form -->
  <div class="row">
    <div class="col-md-12">
      <div class="" style="">
        <div class="">

          <div class="panel panel-default" style="background-color:#708090;">
            <div class="panel-heading" style="padding-bottom:0px">

              <div class="panel-options">
                <div class="panel-options">
                    <a href="{{url('OTSperiodInterestHistoryForm')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Interest Rate</a>
                </div>
              </div>

              <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">
                OTS INTEREST RATE
              </h3>

            </div>

            <table class="table table-striped table-bordered" id="otsTable" style="color: black;">
              <thead>
                <tr>
                  <th rowspan="2">SE. NO.</th>
                  <th rowspan="2">Type</th>
                  <th rowspan="2">Interest Rate(%)</th>
                  <th colspan="2">Period</th>
                  <th rowspan="2">Status</th>
                </tr>
                <tr>
                  <th>start</th>
                  <th>End</th>
                </tr>
              </thead>

              @php
                $Count = 0;
              @endphp

              <tbody>
                @php
                  // echo sizeof($OTSperiodTable);
                @endphp
                @if (sizeof($OTSperiodTable)>0)
                  @foreach ($OTSperiodTable as $key => $OTSperiodTables)
                    <tr>
                      @foreach ($OTSperiodName as $key => $OTSperiodNames)
                        @if ($OTSperiodTables->otsPeriodIdFk == $OTSperiodNames->id)
                          @php
                            $Count = $Count + 1;
                          @endphp
                          <td style="width: 3%;">{{$Count}}</td>
                          <td style="text-align: left !important;">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp{{$OTSperiodNames->name}}</td>
                        @endif
                      @endforeach
                      {{-- <td>{{$OTSperiodTables->otsPeriodIdFk}}</td> --}}
                      <td>{{$OTSperiodTables->interestRate}}</td>
                      @php
                        $Date1 = date_create($OTSperiodTables->dateFrom);
                        $Date2 = date_create($OTSperiodTables->dateTo);
                        if ($OTSperiodTables->dateTo == '0000-00-00' || $OTSperiodTables->dateTo == null) {
                          $DateTo = '';
                        }
                        else {
                          $DateTo = date_format($Date2, "d-m-Y");
                        }
                        $DateFrom = date_format($Date1, "d-m-Y");
                      @endphp
                      <td>{{$DateFrom}}</td>
                      <td>{{$DateTo}}</td>
                      @if ($OTSperiodTables->status == 1)
                        <td style="width: 5%;"><span><i class="fa fa-check" aria-hidden="true" style="color:green;font-size: 1.3em;"></i></span></td>
                      @elseif ($OTSperiodTables->status == 0)
                        <td style="width: 5%;"><span><i class="fa fa-times" aria-hidden="true" style="color:red;font-size: 1.3em;"></i></span></td>
                      @endif
                      {{-- <td>{{$OTSperiodTables->status}}</td> --}}
                    </tr>
                    {{-- {!! $OTSperiodTable->render() !!} --}}
                  @endforeach
                @else
                  <tr>
                    <td>NO DATA FOUND !</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>

                  </tr>
                @endif
              </tbody>
            </table>
            {{-- <div class="dataTables_paginate paging_simple_numbers" id="otsTable_paginate">
              {!! $OTSperiodTable->render() !!}
            </div> --}}
            {!! $OTSperiodTable->render() !!}
            <br>
            <br>
            <br>
          </div>      {{-- panel-body panelBodyView DIV --}}
        </div>
      </div>
    </div>
  </div>
<!-- End of Dropdown form -->

{{-- @include('microfin/reports/mfnFieldOfficerReport/mfnFieldreport1_new_table'); --}}
</div>

@endsection
