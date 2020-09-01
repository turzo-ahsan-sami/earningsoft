@extends('layouts/acc_layout')
@section('title', '| Creaste Budget')
@section('content')

{{-- @include('microfin/reports/passBookBalanceViews/PassBookBalanceAjax') --}}


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
            Budget for {{$BudgetName}}
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
      {!! Form::open(array('url' => 'accBudgetSubmit', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'POST')) !!}
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
              @endphp

              <tbody style="font-size:10px !important;">
                <input type="hidden" name="fiscalYear" value="{{$fiscalYearMonthsDays}}">
                <?php function eachRow($child, $month1, $year1, $fiscalYearName, $savedDate, $count, $requestedBranchId) { ?>
                  <tr>
                    @if ($child->isGroupHead == 1)
                      @if (substr_count($child->code, 0) != 3)
                        <td style="text-align: left !important; padding-left: 5px!important;">
                          <strong>{{$child->name}}</strong>
                        </td>
                        <td>{{$child->code}}</td>

                        @for ($i=0; $i < 12; $i++)
                          <td></td>
                        @endfor
                      @endif
                    @else
                      @php
                        global $count1;
                        ++$count1;
                      @endphp
                      <td style="text-align: left !important; padding-left: 15px!important;">{{$child->name}}</td>
                      <input type="hidden" name="name[]" value="{{$child->name}}">
                      <td>{{$child->code}}</td>
                      <input type="hidden" name="code[]" value="{{$child->code}}">

                      <input type="hidden" name="companyId[]" value="{{$child->companyIdFk}}">
                      <input type="hidden" name="branchId[]" value="{{$requestedBranchId}}">
                      <input type="hidden" name="parentId[]" value="{{$child->parentId}}">
                      <input type="hidden" name="iswGroupHead[]" value="{{$child->isGroupHead}}">
                      <input type="hidden" name="ordering[]" value="{{$child->ordering}}">
                      <input type="hidden" name="fiscalYear[]" value="{{$fiscalYearName[0]}}">
                      <input type="hidden" name="createdDate[]" value="{{$savedDate}}">
                      <input type="hidden" name="approvedDate[]" value="0000-00-00">
                      <input type="hidden" name="acountTypeId[]" value="{{$child->accountTypeId}}">

                      @for ($i=0; $i < 12; $i++)
                        <td>
                          {{-- @if ($child->isGroupHead != 1) --}}
                            <input type="text" name="budget[{{$count1}}][{{$month1}}, {{$year1}}]" value="0" required>
                          {{-- @endif --}}
                        </td>
                        @php
                          switch ($month1) {
                            case 1:
                              $month1Name = 'January';
                              ++$month1;
                              ++$year1;
                              break;
                            case 2:
                              $month1Name = 'February';
                              ++$month1;
                              break;
                            case 3:
                              $month1Name = 'March';
                              ++$month1;
                              break;
                            case 4:
                              $month1Name = 'April';
                              ++$month1;
                              break;
                            case 5:
                              $month1Name = 'May';
                              ++$month1;
                              break;
                            case 6:
                              $month1Name = 'June';
                              ++$month1;
                              break;
                            case 7:
                              $month1Name = 'July';
                              ++$month1;
                              break;
                            case 8:
                              $month1Name = 'August';
                              ++$month1;
                              break;
                            case 9:
                              $month1Name = 'September';
                              ++$month1;
                              break;
                            case 10:
                              $month1Name = 'October';
                              ++$month1;
                              break;
                            case 11:
                              $month1Name = 'Novomber';
                              ++$month1;
                              break;
                            case 12:
                              $month1Name = 'December';
                              $month1 = 1;
                              break;
                          }
                        @endphp
                      @endfor
                      @php
                        // ++$count1;
                      @endphp
                    @endif

                  </tr>
                <?php } ?>

                @php
                  $count = 0;
                @endphp

                @if (sizeof($assetLedgers) > 0)
                  
                  @foreach ($assetLedgers as $ledger)
                    @php
                      ++$count;
                    @endphp
                    @php
                      eachRow($ledger, $month1, $year1, $fiscalYearName, $savedDate, $count, $requestedBranchId);
                    @endphp

                    @if ($ledger->isGroupHead==1)
                      @php
                        $children1=DB::table('acc_account_ledger')->where('parentId', $ledger->id)->orderBy('ordering', 'asc')->get();
                      @endphp

                      @foreach ($children1 as $child1)
                        @php
                          ++$count;
                        @endphp
                        @if ($child1->isGroupHead==1)
                          @php
                            $children2=DB::table('acc_account_ledger')->where('parentId', $child1->id)->orderBy('ordering', 'asc')->get();
                            eachRow($child1, $month1, $year1, $fiscalYearName, $savedDate, $count, $requestedBranchId);
                          @endphp

                          @foreach ($children2 as $child2)
                            @php
                              ++$count;
                            @endphp
                            @if ($child2->isGroupHead==1)
                              @php
                                $children3=DB::table('acc_account_ledger')->where('parentId', $child2->id)->orderBy('ordering', 'asc')->get();
                                eachRow($child2, $month1, $year1, $fiscalYearName, $savedDate, $count, $requestedBranchId);
                              @endphp

                              @foreach ($children3 as $key => $child3)
                                @php
                                  ++$count;
                                @endphp
                                @if ($child3->isGroupHead==1)
                                  @php
                                    $children4=DB::table('acc_account_ledger')->where('parentId', $child3->id)->orderBy('ordering', 'asc')->get();
                                    eachRow($child3, $month1, $year1, $fiscalYearName, $savedDate, $count, $requestedBranchId);
                                  @endphp

                                  @foreach ($children4 as $key => $child4)
                                    @php
                                      ++$count;
                                    @endphp

                                    @if ($child4->isGroupHead==1)

                                      @php
                                        $children5=DB::table('acc_account_ledger')->where('parentId', $child4->id)->orderBy('ordering', 'asc')->get();
                                        eachRow($child4, $month1, $year1, $fiscalYearName, $savedDate, $count, $requestedBranchId);
                                      @endphp

                                      @foreach ($children5 as $key => $child5)
                                        @php
                                          ++$count;
                                        @endphp
                                        @if ($child5->isGroupHead==1)
                                          @php
                                            $children6=DB::table('acc_account_ledger')->where('parentId', $child5->id)->orderBy('ordering', 'asc')->get();
                                            eachRow($child5, $month1, $year1, $fiscalYearName, $savedDate, $count, $requestedBranchId);
                                          @endphp

                                          @foreach ($children6 as $key => $child6)
                                            @php
                                              ++$count;
                                            @endphp
                                            @php
                                              eachRow($child6, $month1, $year1, $fiscalYearName, $savedDate, $count, $requestedBranchId);
                                            @endphp
                                          @endforeach
                                        @else
                                          @php
                                            eachRow($child5, $month1, $year1, $fiscalYearName, $savedDate, $count, $requestedBranchId);
                                          @endphp

                                        @endif
                                      @endforeach
                                    @else
                                      @php
                                        eachRow($child4, $month1, $year1, $fiscalYearName, $savedDate, $count, $requestedBranchId);
                                      @endphp

                                    @endif
                                  @endforeach
                                @else
                                  @php
                                    eachRow($child3, $month1, $year1, $fiscalYearName, $savedDate, $count, $requestedBranchId);
                                  @endphp

                                @endif
                              @endforeach
                            @else
                              @php
                                eachRow($child2, $month1, $year1, $fiscalYearName, $savedDate, $count);
                              @endphp

                            @endif
                          @endforeach
                        @else
                          @php
                            eachRow($child1, $month1, $year1, $fiscalYearName, $savedDate, $count);
                          @endphp

                        @endif
                      @endforeach

                    @else
                      @php
                        eachRow($ledger, $month1, $year1, $fiscalYearName, $savedDate);
                      @endphp
                    @endif
                  @endforeach

                @else
                  <tr>
                    <td colspan="7">No DATA FOUND !</td>
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
                  <input class="btn btn-danger" type="reset" name="reset" value="Cancel" onclick="window.location.href='addBudgetPreview'">
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
        window.location.href="addBudgetPreview";
    }, 3000);
  }
@endif

</script>

@endsection
