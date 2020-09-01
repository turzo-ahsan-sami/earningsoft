@extends('layouts/acc_layout')
@section('title', '| Update Budget For Asset & Liabilities')
@section('content')

  <style media="screen">
  .table > thead > tr > th,
  .table > tbody > tr > th,
  .table > tfoot > tr > th,
  .table > thead > tr > td,
  .table > tbody > tr > td,
  .table > tfoot > tr > td  {

    line-height:1;

  }

  .table thead tr th {
    position: relative;
    word-break: normal !important;
    text-transform: capitalize !important;
  }

  .left{
    float: left;
  }
  .right{
    float: right;
  }
  .center{
    text-align:left;
    margin:0 auto !important;
    display:inline-block
  }
  .left{
    text-align: left;
    margin-left: 80px !important;
    display: inline-block;
  }

  th, td {
    padding: 2px !important;
  }

  .page-container .main-content {
    word-break: normal !important;
  }
  </style>

  <div class="container-fluid">

    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default" style="background-color:#708090;">
          <div class="panel-heading" style="padding-bottom:0px">
            <div class="panel-options">
              <div class="panel-options">
                  {{-- <a href="{{url('PassBookBalance')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Back To The Pass Book List</a> --}}
              </div>
            </div>
            <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">
              Update Budget For {{$BudgetName}}
            </h3>
          </div>

          @php
            // dd($child);companyIdFk
            $fiscalYearMonths = DB::table('gnr_fiscal_year')
              ->where('companyId', $companyId[0])
              ->orderByRaw('id DESC')
              ->limit(1)
              ->pluck('fyStartDate')
              ->toArray();

            $fiscalYearName = DB::table('gnr_fiscal_year')
              ->where('companyId', $companyId[0])
              ->orderByRaw('id DESC')
              ->limit(1)
              ->pluck('name')
              ->toArray();

            $savedDate = date("Y-m-d");

            // print_r($fiscalYearMonths);
            $fiscalYearMonthsDays = $fiscalYearMonths[0];
            list($year, $month, $day) = explode('-', $fiscalYearMonths[0]);
            list($year1, $month1, $day1) = explode('-', $fiscalYearMonths[0]);
            $month = ltrim($month, 0);
            $month1 = ltrim($month1, 0);
          @endphp

          <!-- Filtering Start-->
        {!! Form::open(array('url' => 'accRevisedBudgetSubmit', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'POST')) !!}
        {{-- @method('POST')
        @csrf --}}
          <div class="row text-center" >
            {{-- Start of Branch Name --}}
            <div class="col-md-12">
              {{-- Start of the Submit Button --}}

              <table class="table table-bordered" border="1pt solid ash" style=" text-align: center; font-family: arial; color:black; border-collapse: collapse;" width="100%">
                <thead style="font-size:11px;">
                  <tr>
                    <th colspan="1" rowspan="1">
                      <div align="center">Name
                      </div>
                    </th>
                    <th colspan="1" rowspan="1">
                      <div align="center">Code
                      </div>
                    </th>

                    @for ($i=0; $i < 12; $i++)
                      @php
                      // dd($month);
                        switch ($month) {
                          case 1:
                            $monthName = 'January';
                            ++$month;
                            ++$year;
                            break;
                          case 2:
                            $monthName = 'February';
                            ++$month;
                            break;
                          case 3:
                            $monthName = 'March';
                            ++$month;
                            break;
                          case 4:
                            $monthName = 'April';
                            ++$month;
                            break;
                          case 5:
                            $monthName = 'May';
                            ++$month;
                            break;
                          case 6:
                            $monthName = 'June';
                            ++$month;
                            break;
                          case 7:
                            $monthName = 'July';
                            ++$month;
                            break;
                          case 8:
                            $monthName = 'August';
                            ++$month;
                            break;
                          case 9:
                            $monthName = 'September';
                            ++$month;
                            break;
                          case 10:
                            $monthName = 'October';
                            ++$month;
                            break;
                          case 11:
                            $monthName = 'Novomber';
                            ++$month;
                            break;
                          case 12:
                            $monthName = 'December';
                            $month = 1;
                            break;
                        }
                      @endphp
                      <th colspan="1" rowspan="1">
                        <div align="center">{{$monthName}}, {{ltrim($year, 20)}}
                          @php
                            $date1 = $monthName.','.$year;
                          @endphp
                          <input type="hidden" name="dateDatas[]" value="{{$date1}}">
                        </div>
                      </th>
                    @endfor
                  </tr>
                </thead>



                @php
                  $loanTracker = 0;
                  $savingsTracker = 0;
                  $loanNameTracker = 0;

                  $count1 = 0;
                  $count2 = 0;
                  $count3 = 0;
                  $parantId = 0;
                  $countKey = -1;
                @endphp

                <tbody style="font-size:10px !important;">
                  @if (sizeof($BudgetValues) > 0)
                    @foreach ($BudgetValues as $key1 => $BudgetValue)
                        @if (substr("$BudgetValue->code", 0, 1) == 1)
                          @if ($count2 == 0)
                            @php
                              $headCode = DB::table('acc_account_ledger')
                                ->select('code')
                                ->where('code', '=', 10000)
                                ->pluck('code')
                                ->toArray();
                            @endphp
                            <tr>
                              <td style="text-align: left !important; padding-left: 5px!important;"> <strong>Asset</strong> </td>
                              <td> <strong>{{$headCode[0]}}</strong> </td>

                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                            </tr>
                          @endif
                          @php
                            $count2 = 1;
                          @endphp
                          @php
                          $parent = DB::table('acc_account_ledger')
                            ->select('name')
                            ->where('id', $BudgetValue->parentId)
                            ->pluck('name')
                            ->toArray();

                          $parentCode = DB::table('acc_account_ledger')
                            ->select('code')
                            ->where('id', $BudgetValue->parentId)
                            ->pluck('code')
                            ->toArray();
                          @endphp
                          @if ($parantId != $BudgetValue->parentId)
                            <tr>
                              <td style="text-align: left !important; padding-left: 5px!important;"> <strong>{{$parent[0]}}</strong> </td>
                              <td><strong>{{$parentCode[0]}}</strong></td>

                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                            </tr>
                            @php
                              $parantId = $BudgetValue->parentId;
                            @endphp
                          @endif
                        @elseif (substr("$BudgetValue->code", 0, 1) == 2)
                          @if ($count3 == 0)
                            @php
                              $headCode = DB::table('acc_account_ledger')
                                ->select('code')
                                ->where('code', '=', 20000)
                                ->pluck('code')
                                ->toArray();
                            @endphp
                            <tr>
                              <td style="text-align: left !important; padding-left: 5px!important;"> <strong>Liabilities</strong> </td>
                              <td> <strong>{{$headCode[0]}}</strong> </td>

                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                            </tr>
                          @endif
                          @php
                            $count3 = 1;
                          @endphp
                          @php
                          $parent = DB::table('acc_account_ledger')
                            ->select('name')
                            ->where('id', $BudgetValue->parentId)
                            ->pluck('name')
                            ->toArray();

                          $parentCode = DB::table('acc_account_ledger')
                            ->select('code')
                            ->where('id', $BudgetValue->parentId)
                            ->pluck('code')
                            ->toArray();
                          @endphp
                          @if ($parantId != $BudgetValue->parentId)
                            <tr>
                              <td style="text-align: left !important; padding-left: 5px!important;"> <strong>{{$parent[0]}}</strong> </td>
                              <td><strong>{{$parentCode[0]}}</strong></td>

                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                            </tr>
                            @php
                              $parantId = $BudgetValue->parentId;
                            @endphp
                          @endif
                        @else
                          @php
                          $parent = DB::table('acc_account_ledger')
                            ->select('name')
                            ->where('id', $BudgetValue->parentId)
                            ->pluck('name')
                            ->toArray();

                          $parentCode = DB::table('acc_account_ledger')
                            ->select('code')
                            ->where('id', $BudgetValue->parentId)
                            ->pluck('code')
                            ->toArray();
                          @endphp
                          @if ($parantId != $BudgetValue->parentId)
                            <tr>
                              <td style="text-align: left !important; padding-left: 5px!important;"> <strong>{{$parent[0]}}</strong> </td>
                              <td><strong>{{$parentCode[0]}}</strong></td>

                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                            </tr>
                            @php
                              $parantId = $BudgetValue->parentId;
                            @endphp
                          @endif
                        @endif
                      <tr>
                        <td style="text-align: left !important; padding-left: 15px!important;"><input type="hidden" name="name[]" value="{{$BudgetValue->ladgerName}}"> {{$BudgetValue->ladgerName}} </td>
                        <td><input type="hidden" name="code[]" value="{{$BudgetValue->code}}"> {{$BudgetValue->code}} </td>

                        <input type="hidden" name="companyId[]" value="{{$BudgetValue->companyIdFk}}">
                        <input type="hidden" name="branchId[]" value="100">
                        <input type="hidden" name="parentId[]" value="{{$BudgetValue->parentId}}">
                        <input type="hidden" name="iswGroupHead[]" value="{{$BudgetValue->isGroupHead}}">
                        <input type="hidden" name="ordering[]" value="{{$BudgetValue->ordering}}">
                        <input type="hidden" name="fiscalYear[]" value="{{$fiscalYearName[0]}}">
                        <input type="hidden" name="updatedDate[]" value="{{$savedDate}}">
                        <input type="hidden" name="approvedDate[]" value="0000-00-00">
                        <input type="hidden" name="acountTypeId[]" value="{{$BudgetValue->accountTypeId}}">

                        @foreach ($BudgetAmount as $key2 => $Budget)
                          @if ($key1 == $key2)
                            @foreach ($Budget as $key3 => $Budget1)
                              @foreach ($Budget1 as $key => $Budget2)
                                @php
                                // if ($key2 == 1) {
                                //   dd($BudgetAmount, $Budget, $key2);
                                // }
                                // dd($BudgetAmount, $Budget, $key2);
                                switch ($month1) {
                                  case 1:
                                    // ++$month1;
                                    ++$year1;
                                      $date = $month1.', '.$year1;
                                      ++$month1;
                                    break;
                                  case 2:
                                    $date = $month1.', '.$year1;
                                    ++$month1;
                                    break;
                                  case 3:
                                    $date = $month1.', '.$year1;
                                    ++$month1;
                                    break;
                                  case 4:
                                    $date = $month1.', '.$year1;
                                    ++$month1;
                                    break;
                                  case 5:
                                    $date = $month1.', '.$year1;
                                    ++$month1;
                                    break;
                                  case 6:
                                    $date = $month1.', '.$year1;
                                    ++$month1;
                                    break;
                                  case 7:
                                    $date = $month1.', '.$year1;
                                    ++$month1;
                                    break;
                                  case 8:
                                    $date = $month1.', '.$year1;
                                    ++$month1;
                                    break;
                                  case 9:
                                    $date = $month1.', '.$year1;
                                    ++$month1;
                                    break;
                                  case 10:
                                    $date = $month1.', '.$year1;
                                    ++$month1;
                                    break;
                                  case 11:
                                    $date = $month1.', '.$year1;
                                    ++$month1;
                                    break;
                                  case 12:
                                    $date = $month1.', '.$year1;
                                    $month1 = 1;
                                    break;
                                  }
                                @endphp
                                @php
                                  // global $count1;
                                  if ($countKey != $key2) {
                                    ++$count1;
                                    $countKey = $key2;
                                  }
                                  // ++$count1;
                                  // echo $count1;
                                @endphp
                                <td><input type="text" name="budget[{{$count1}}][{{$month1}}, {{$year1}}]" value="{{$Budget2}}"></td>
                              @endforeach
                            @endforeach
                            @php
                              list($year1, $month1, $day1) = explode('-', $fiscalYearMonths[0]);
                              $month1 = ltrim($month1, 0);
                            @endphp
                          @endif
                        @endforeach
                      </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="14">NO DATA FOUND !</td>
                    </tr>
                  @endif
                </tbody>

              </table>

              <div class="col-md-12">
                <div class="form-group">
                  {!! Form::label('', '', ['class' => 'control-label col-md-12', 'style' => 'color:#708090; padding-top: 25px;']) !!}
                  <div class="col-md-12">
                    <input class="btn btn-primary" type="submit" name="Submit" value="Submit" id="reportSubmit">
                    <input class="btn btn-warning" type="reset" name="reset" value="Reset" onclick="window.location.href='accBudget'">
                    <input class="btn btn-danger" type="reset" name="reset" value="Cancel" onclick="window.location.href='acc/home'">
                  </div>
                </div>
              </div>
              {{-- End of the Submit button --}}
            </div>

          {{-- {!! Form::close()  !!} --}}
          <!-- filtering end-->
        </div>
        {!! Form::close()  !!}

        {{-- @php
          dd(sizeof($memberInformations));
        @endphp --}}

        </div>
      </div>
    </div>
    <br>
    <br>

     <div class="row">
       {{-- <div class="table-responsive"> --}}
     </div>

   </div>

  </div>


  <script type="text/javascript">

  // alert('Deleted');

  @if(Session::has('message'))
    var type = "{{ Session::get('alert-type', 'info') }}";
    switch(type){
        case 'info':
            toastr.info("{{ Session::get('message') }}");
            break;
        case 'warning':
            toastr.warning("{{ Session::get('message') }}");
            break;
        case 'success':
            toastr.success("{{ Session::get('message') }}");
            break;
        case 'error':
            toastr.error("{{ Session::get('message') }}");
            // setTimeout(function(){
            //     window.location.href="acc/home";
            // }, 3000);
            break;
    }
    if (type == 'success') {
      setTimeout(function(){
          window.location.href="acc/home";
      }, 3000);
    }
  @endif

  </script>

@endsection
