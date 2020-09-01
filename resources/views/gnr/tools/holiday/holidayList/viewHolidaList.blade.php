@extends($route['layout'])
@section('title', '| Org./Branch/Samity Holiday')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default" style="background-color:#708090;">
            <div class="panel-heading" style="padding-bottom:0px">
                <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px">
                    <font color="white">{{$headTitle}}</font>
                </h1>
            </div>

            <div class="panel-body panelBodyView">

                <!-- Filtering Start-->
                {!! Form::open(array('url' => './gnr/viewHolidayListReportTable/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'get')) !!}

                    <div class="row">
                      {{-- Start of Branch Name --}}
                      <div class="col-md-12">

                        <div class="col-md-1">
                          <div class="form-group">
                            <div class="col-md-12">
                                {!! Form::label('', 'Type:', ['class' => 'control-label pull-left', 'style' => 'color:black']) !!}
                            </div>
                            <div class="col-md-12">
                              <select name="DateSelection" class="form-control input-sm" id="DateSelection" style="color: black;" required="true">
                                  <option value="Y"> Year </option>
                                  <option value="N"> Date Range </option>
                              </select>
                              <p id='filBranchE' style="max-height:5px; color:red;">
                                @if ($error != '')
                                    {{$error}}
                                @endif
                              </p>
                            </div>
                          </div>
                        </div>
                        {{-- End of Type --}}

                        <div class="feel-mee">
                            {{-- Start of the to date --}}
                            <div class="col-md-1">
                              <div class="form-group">
                                <div class="col-md-12">
                                    {!! Form::label('','Year:  &nbsp;&nbsp;&nbsp;&nbsp;', ['class' => 'control-label pull-left', 'style' => 'color:black']) !!}
                                </div>
                                <div class="col-md-12">
                                    <select name="searchYear" class="form-control input-sm" id="searchYear" style="color: black;">
                                        <option value=""> -- Select Year -- </option>
                                        @foreach($yearList as $year)
                                            <option value="{{$year}}"> {{$year}} </option>
                                        @endforeach
                                    </select>
                                </div>
                              </div>
                            </div>
                            {{-- End of the to date --}}
                        </div>

                        <div class="feel-mee" style="display: none;">
                            {{-- Start of the to date --}}
                            <div class="col-md-1">
                              <div class="form-group">
                                <div class="col-md-12">
                                    {!! Form::label('','From:  &nbsp;&nbsp;&nbsp;&nbsp;', ['class' => 'control-label pull-left', 'style' => 'color:black']) !!}
                                </div>
                                <div class="col-md-12">
                                  <input type="text" class="form-control input-sm" id="txtDate2" name="txtDate2" value="">
                                </div>
                              </div>
                            </div>
                            {{-- End of the to date --}}

                            {{-- Start of the to date --}}
                            <div class="col-md-1">
                              <div class="form-group">
                                <div class="col-md-12">
                                    {!! Form::label('','To:  &nbsp;&nbsp;&nbsp;&nbsp;', ['class' => 'control-label pull-left', 'style' => 'color:black']) !!}
                                </div>
                                <div class="col-md-12">
                                  <input type="text" class="form-control input-sm" id="txtDate1" name="txtDate1" value="">
                                  {{-- <p id='filMonthE' style="max-height:3px; color:red;"></p> --}}
                                </div>
                              </div>
                            </div>
                            {{-- End of the to date --}}
                        </div>

                        {{-- Start of the Submit Button --}}
                        <div class="col-md-1">
                          <div class="form-group">
                            {!! Form::label('', '', ['class' => 'control-label col-md-12', 'style' => 'color:#708090; padding-top: 25px;']) !!}
                            <div class="col-md-12">
                                {{-- {!! Form::submit('Show Report', ['id' => 'reportSubmit', 'class' => 'btn btn-primary btn-xs']); !!} --}}
                              <input class="btn btn-primary" type="submit" name="Submit" value="Show report" id="reportSubmit">
                            </div>
                          </div>
                        </div>
                        {{-- End of the Submit button --}}
                      </div>
                    </div>
                {!! Form::close()  !!}
                <!-- filtering end-->

            </div>
            <div class="row" id="reportingDiv">

            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $("#DateSelection").on("change",function(){
        var a = $(this).val();
        if (a == 'Y') {
            // $(".feel-me").addClass("hide");
            // alert('Y');
            // $("input:text").val("Glenn Quagmire");
            // $('#txtDate1').empty();
            // $('#txtDate2').empty();
        }else if (a == 'N') {
            // $(".feel-me").removeClass("hide");
            // alert('N');
            // $('#searchYear').empty();

        }

        $('.feel-mee').toggle();
    });


    $(function(){
        $( "#txtDate1" ).datepicker({
            dateFormat: "yy-mm-dd",
            showOtherMonths: true,
            selectOtherMonths: true,
            changeMonth: true,
            changeYear: true,
            yearRange: "-50:+0",
            maxDate: "dateToday"
        }).val();
    });

    $(function(){
        $( "#txtDate2" ).datepicker({
            dateFormat: "yy-mm-dd",
            showOtherMonths: true,
            selectOtherMonths: true,
            changeMonth: true,
            changeYear: true,
            yearRange: "-50:+0",
            maxDate: "dateToday"
        }).val();
    });


    $("form").submit(function( event ) {
       event.preventDefault();

       var serializeValue=$(this).serialize();
       //alert(serializeValue);

       $('#loadingModal').show();
       $("#reportingDiv").load('{{URL::to("./gnr/viewHolidayListReportTable/")}}'+'?'+serializeValue);
    });
</script>

@endsection
