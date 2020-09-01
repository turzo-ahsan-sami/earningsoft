@extends('layouts/acc_layout')
@section('title', '| Add Budget Preview')
@section('content')

<div class="container-fluid">

  <!-- Start of the Dropdown form -->
  <div class="row">
    <div class="col-md-12">
      <div class="" style="">
        <div class="">
          <div class="panel panel-default" style="background-color:#708090;">
            <div class="panel-heading" style="padding-bottom:0px">
              <div class="panel-options">
                <a href="{{url('budgetPreview')}}" class="btn btn-info pull-right addViewBtn"
                style=""><i class="fa fa-minus-circle" aria-hidden="true" style="color: #00BFFF;font-size: 1.3em;"></i>&nbsp;Cancel Budget</a>
              </div>
              <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">
                Add Budget
              </h3>
            </div>

            <div class="panel-body panelBodyView">
              <!-- Filtering Start-->
              {!! Form::open(array('url' => 'accBudget', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'get')) !!}

              <div class="row">
                {{-- Start of Branch Name --}}
                <div class="col-md-12">
                  <div class="col-md-1" id="branchDiv">
                    <div class="form-group">
                      <div class="col-md-12">
                        {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left', 'style' => 'color:black']) !!}
                      </div>
                      <div class="col-md-12">
                        <select name="searchBranch" class="form-control input-sm" id="searchBranch" style="color: black;" required="true">>
                          <option value=""> -- Select Branch -- </option>
                          @foreach($BranchDatas as $BranchData)
                          <option value="{{ $BranchData->id }}"> {{ str_pad($BranchData->branchCode,3,0,STR_PAD_LEFT) }}-{{ $BranchData->name }} </option>             //Loop for showing the data in the Branch selection option
                          @endforeach
                        </select>
                        {{-- <p id='filBranchE' style="max-height:5px; color:red;"></p> --}}
                      </div>
                    </div>
                  </div>
                  {{-- End of Branch Name --}}

                  <div class="col-md-1">
                    <div class="form-group">
                      <div class="col-md-12">
                        {!! Form::label('', 'Year:', ['class' => 'control-label pull-left', 'style' => 'color:black']) !!}
                      </div>
                      <div class="col-md-12">
                        <select name="searchYear" class="form-control input-sm" id="searchYear" style="color: black;" required="true">>
                          <option value=""> -- Select Fiscal Year -- </option>
                          @foreach ($fiscalYear as $key => $fiscalYear1)
                          <option value="{{$fiscalYear1->name}}">{{$fiscalYear1->name}}</option>
                          @endforeach
                        </select>
                        {{-- <p id='filBranchE' style="max-height:5px; color:red;"></p> --}}
                      </div>
                    </div>
                  </div>
                  {{-- End of Status --}}

                  <div class="col-md-1">
                    <div class="form-group">
                      <div class="col-md-12">
                        {!! Form::label('', 'Type:', ['class' => 'control-label pull-left', 'style' => 'color:black']) !!}
                      </div>
                      <div class="col-md-12">
                        <select name="searchBudget" class="form-control input-sm" id="searchBudget" style="color: black;" required="true">>
                          <option value=""> -- Select Budget Type -- </option>
                          @foreach ($budgetTypeName as $key => $budgetTypeName1)
                          @if ($key != 0)
                          <option value="{{$budgetTypeName1->code}}">{{$budgetTypeName1->code}} - {{$budgetTypeName1->name}}</option>
                          @endif

                          @endforeach
                        </select>
                        {{-- <p id='filBranchE' style="max-height:5px; color:red;"></p> --}}
                      </div>
                    </div>
                  </div>
                  {{-- End of Status --}}

                  {{-- Start of the Submit Button --}}
                  <div class="col-md-1">
                    <div class="form-group">
                      {!! Form::label('', '', ['class' => 'control-label col-md-12', 'style' => 'color:#708090; padding-top: 25px;']) !!}
                      <div class="col-md-12">
                        {{-- {!! Form::submit('Show Report', ['id' => 'reportSubmit', 'class' => 'btn btn-primary btn-xs']); !!} --}}
                        <input class="btn btn-primary" type="submit" name="Submit" value="Show" id="reportSubmit">
                      </div>
                    </div>
                  </div>
                  {{-- End of the Submit button --}}
                </div>

                {!! Form::close()  !!}
                <!-- filtering end-->
              </div>
            </div>
            <div class="row" id="reportingDiv">

            </div>
          </div>      {{-- panel-body panelBodyView DIV --}}
        </div>
      </div>
    </div>
  </div>
  <!-- End of Dropdown form -->

  {{-- @include('microfin/reports/mfnFieldOfficerReport/mfnFieldreport1_new_table'); --}}
</div>

<script type="text/javascript">
//
// $("form").submit(function( event ) {
//    event.preventDefault();
//
//    var serializeValue=$(this).serialize();
//    //alert(serializeValue);
//
//    $('#loadingModal').show();
//    $("#reportingDiv").load('{{URL::to("accBudget")}}'+'?'+serializeValue);
// });

$(document).ready(function() {

  $("#printIcon").click(function(event) {

    var mainContents = document.getElementById("reportingDiv").innerHTML;
    var headerContents = '';

    var printStyle = '<style>thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px}  tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

    var printContents = '<div id="order-details-wrapper">' + headerContents +printStyle+ mainContents +'</div>';

    var win = window.open('','printwindow');
    win.document.write(printContents);
    win.print();
    win.close();
  });

  $("#loadingModal").hide();


}); /* Ready to print */

</script>


@endsection
