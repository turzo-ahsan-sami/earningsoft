@extends('layouts/acc_layout')
@section('title', '| Income Statement')
@section('content')

<style type="text/css">
    #accIncomeStatementTable{
        font-family: arial !important;
    }

    #accIncomeStatementTable td{
        padding-left: 10px;
        padding-right: 10px;
    }
</style>

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
            <div class="panel-options">
                <button id="printIcon" class="btn btn-info pull-right"  target="_blank" style="font-size: 14px; margin-top: 10px; border: 3px solid #525659; border-radius: 25px;">
                    <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                </button>
            </div>
            <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Statement of Comprehensive Income</h3>
        </div>

        <div class="panel-body panelBodyView">
            <!-- Filtering Start-->
            <div class="row">
                <div class="col-md-12">
                    <div class="row">

                        {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'ladgerReportForm', 'method'=>'get')) !!}

                        @if($userBranchId == 1)
                        <div class="col-md-1">
                            <div class="form-group" style="font-size: 13px; color:#212F3C">
                                <div class="col-md-12">
                                    {!! Form::label('', 'Report Level:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-md-12">
                                    {!! Form::select('filReportLevel',$reportLevelList, null ,['id'=>'filReportLevel','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus']) !!}
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1" id="areaDiv" style="display: none;">
                            <div class="form-group" style="font-size: 13px; color:#212F3C">
                                <div class="col-md-12">
                                    {!! Form::label('', 'Area:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-md-12">                                  
                                    <select id="filArea" name="filArea" class="form-control input-sm">
                                        <option value="">--Select Area--</option>
                                        @foreach ($dbAreas as $area)
                                            <option value="{{$area->id}}">{{$area->name}}</option>
                                            @php
                                                $barnchIds = str_replace(['"','[',']'], '', $area->branchId);
                                                $barnchIds = explode(',', $barnchIds);
                                                $branches = $dbBranches
                                                              ->whereIn('id',$barnchIds);
                                            @endphp
                                            @foreach ($branches as $branch)
                                                <option value="" disabled="disabled">&nbsp {{$branch->branchCode.' - '.$branch->name}}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1" id="zoneDiv" style="display: none;">
                            <div class="form-group" style="font-size: 13px; color:#212F3C">
                                <div class="col-md-12">
                                    {!! Form::label('', 'Zone:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-md-12">                                  
                                    <select id="filZone" name="filZone" class="form-control input-sm">
                                        <option value="">--Select Zone--</option>
                                        @foreach ($dbZones as $zone)
                                            <option value="{{$zone->id}}">{{$zone->name}}</option>
                                            @php
                                                $areaIds = str_replace(['"','[',']'], '', $zone->areaId);
                                                $areaIds = explode(',', $areaIds);
                                                $areas = $dbAreas
                                                              ->whereIn('id',$areaIds);
                                            @endphp
                                            @foreach ($areas as $area)
                                                <option value="" disabled="disabled">&nbsp {{str_pad($area->code,3,'0',STR_PAD_LEFT).' - '.$area->name}}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1" id="regionDiv" style="display: none;">
                            <div class="form-group" style="font-size: 13px; color:#212F3C">
                                <div class="col-md-12">
                                    {!! Form::label('', 'Region:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-md-12">                                  
                                    <select id="filRegion" name="filRegion" class="form-control input-sm">
                                        <option value="">--Select Region--</option>
                                        @foreach ($dbRegions as $region)
                                            <option value="{{$region->id}}">{{$region->name}}</option>
                                            @php
                                                $zoneIds = str_replace(['"','[',']'], '', $region->zoneId);
                                                $zoneIds = explode(',', $zoneIds);
                                                $zones = $dbZones
                                                              ->whereIn('id',$zoneIds);
                                            @endphp
                                            @foreach ($zones as $zone)
                                                <option value="" disabled="disabled">&nbsp {{str_pad($zone->code,3,'0',STR_PAD_LEFT).' - '.$zone->name}}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1" id="branchDiv">
                            <div class="form-group" style="font-size: 13px; color:#212F3C">
                                <div class="col-md-12">
                                    {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-md-12">
                                    {!! Form::select('filBranch', [''=>'--All--','allBranch'=>'All Branch Office']+$branchList, null ,['id'=>'filBranch','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
                                    <p id='filBranchE' style="max-height:3px; color:red;"></p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($userBranchId==1)
                        <div class="col-md-1">
                            <div class="form-group" style="font-size: 13px; color:#212F3C">
                                {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                <div class="col-sm-12">
                                    {!! Form::select('filProject',$projectList,null,['id'=>'filProject','class'=>'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($userBranchId==1)
                        <div class="col-md-1">
                            <div class="form-group" style="font-size: 13px; color:#212F3C">
                                {!! Form::label('', 'Pro. Type:', ['class' => 'control-label col-sm-12']) !!}
                                <div class="col-sm-12">
                                    {!! Form::select('filProjectType',$projectTypeList,null,['id'=>'filProjectType','class'=>'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-1">
                            <div class="form-group" style="font-size: 13px; color:black;">
                                 <div style="text-align: center;" class="col-sm-12">
                                    {!! Form::label('', 'Depth Level:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-sm-12">
                                    {!! Form::select('depthLevel',['5'=>'All',1=>'Level-1',2=>'Level-2',3=>'Level-3',4=>'Level-4'],null ,['id'=>'depthLevel','class'=>'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="form-group" style="font-size: 13px; color:black;">
                                <div style="text-align: center;" class="col-sm-12">
                                    {!! Form::label('', 'Round Up:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-sm-12">
                                    {!! Form::select('roundUp',['1'=>'Yes','2'=>'No'], null,['id'=>'roundUp','class'=>'form-control input-sm']) !!}

                                </div>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="form-group" style="font-size: 13px; color:black;">
                                <div style="text-align: center;" class="col-sm-12">
                                    {!! Form::label('', '\'0\' Balance:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-sm-12">
                                    {!! Form::select('withZero',['1'=>'No','2'=>'Yes'], null,['id'=>'withZero','class'=>'form-control input-sm']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="form-group" style="font-size: 13px; color:black;">
                                <div style="text-align: center;" class="col-sm-12">
                                    {!! Form::label('', 'Search By:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-sm-12">
                                    {!! Form::select('searchMethod',['Fiscal Year'=>'Fiscal Year','Date Range'=>'Date Range','Current Year'=>'Current Year'], null,['id'=>'searchMethod','class'=>'form-control input-sm']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1" style="display: none;" id="fiscalYearDiv">
                            <div class="form-group" style="font-size: 13px; color:black">
                                {!! Form::label('', ' ', ['class' => 'control-label col-sm-12']) !!}
                                <div class="col-sm-12" style="padding-top: 18px;">
                                    {!! Form::select('filFiscalYear', $fiscalYearList, null, array('class'=>'form-control input-sm', 'id' => 'filFiscalYear')) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2" style="display: none;" id="dateRangeDiv">
                            <div class="form-group" style="font-size: 13px; color:black">
                                <div style="text-align: center; padding-top: 5px;" class="col-sm-12">
                                    {!! Form::label('', ' ', ['class' => 'control-label']) !!}
                                </div>

                                <div class="col-sm-12" style="padding-top: 0px;">
                                    <div class="form-group">
                                        <div class="col-sm-6" id="dateFromDiv">
                                            {!! Form::text('dateFrom', '',['id'=>'dateFrom','placeholder'=>'From','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                            <p id="dateFrome" style="color: red;display: none;">*Required</p>
                                        </div>
                                        <div class="col-sm-6" id="dateToDiv">
                                            {!! Form::text('dateTo','',['id'=>'dateTo','placeholder'=>'To','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                            <p id="dateToe" style="color: red;display: none;">*Required</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-md-1"></div> --}}

                        <div class="col-md-1">
                            <div class="form-group" style="">
                                {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                <div class="col-sm-12" style="padding-top: 13%;">
                                    {!! Form::submit('search', ['id' => 'incomeStatementSearch', 'class' => 'btn btn-primary btn-s', 'style'=>'font-size:12px']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>

                        {!! Form::close()  !!}

                        {{-- end Div of ledgerSearch --}}

                        <div class="col-md-10"></div>
                    </div>
                </div>

            </div>
            <!-- filtering end-->
            <div class="row">
                <div class="col-md-12"  id="reportingDiv">
                    
                </div>
            </div>


        </div>
      </div>
  </div>
  </div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        /*on change filReportLevel hide/show contents*/
    $("#filReportLevel").change(function(event) {
        if ($(this).val()=="Branch") {
            $("#branchDiv").show();  
            $("#areaDiv").hide();  
            $("#zoneDiv").hide();  
            $("#regionDiv").hide(); 
            
            $("#filArea").prop('required', false);
            $("#filZone").prop('required', false);
            $("#filRegion").prop('required', false); 
        }
        else if($(this).val()=="Area"){
            $("#branchDiv").hide(); 
            $("#areaDiv").show(); 
            $("#zoneDiv").hide(); 
            $("#regionDiv").hide(); 

            //$("#filBranch").prop('required', false);
            $("#filArea").prop('required', true);
            $("#filZone").prop('required', false);
            $("#filRegion").prop('required', false);
        }
        else if($(this).val()=="Zone"){
            $("#branchDiv").hide(); 
            $("#areaDiv").hide(); 
            $("#zoneDiv").show(); 
            $("#regionDiv").hide(); 

            //$("#filBranch").prop('required', false);
            $("#filArea").prop('required', false);
            $("#filZone").prop('required', true);
            $("#filRegion").prop('required', false);
        }
        else if($(this).val()=="Region"){
            $("#branchDiv").hide(); 
            $("#areaDiv").hide(); 
            $("#zoneDiv").hide(); 
            $("#regionDiv").show(); 

            //$("#filBranch").prop('required', false);
            $("#filArea").prop('required', false);
            $("#filZone").prop('required', false);
            $("#filRegion").prop('required', true);
        }        
    });
    /*end on change filReportLevel hide/show contents*/
    $("#filReportLevel").trigger('change');

    $("#searchMethod").change(function(event) {
            var searchMethod = $(this).val();

            //Fiscal Year
            if(searchMethod=='Fiscal Year'){
                $("#fiscalYearDiv").show();
                $("#dateRangeDiv").hide();
            }

            //Date Range
            else if(searchMethod=='Date Range'){
                $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").show();
                $("#dateFromDiv").show();

                $("#dateToDiv").attr("class", "col-sm-6");
                $("#dateFrom").show();
                //$("#dateFrom").val("");

                $("#dateFrom").datepicker("option","minDate",new Date(Date.parse("2010-07-01")));
            }
            //Current Year
            else if(searchMethod=='Current Year'){

                $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").show();
                $("#dateFromDiv").hide();
                $("#dateToDiv").attr("class", "col-sm-12");
                $("#dateToDiv").show();

            }
        });

        $("#searchMethod").trigger('change');


         /* Date Range From */
        $("#dateFrom").datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange : "2010:c",
                minDate: new Date(2010, 07 - 1, 01),
                maxDate: "dateToday",
                dateFormat: 'dd-mm-yy',
                onSelect: function () {
                    $('#dateFrome').hide();
                    $("#dateTo").datepicker("option","minDate",new Date(toDate($(this).val())));
                    $( "#dateTo" ).datepicker( "option", "disabled", false );
                    $("#searchMethod").trigger('change');
                }
            });
        /* Date Range From */


        /* Date Range To */
        $("#dateTo").datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange : "2010:c",
                maxDate: "dateToday",
                dateFormat: 'dd-mm-yy',
                onSelect: function () {
                    $('#dateToe').hide();
                    $("#searchMethod").trigger('change');
                }

            });

    $("form").submit(function( event ) {
        event.preventDefault();

        var serializeValue=$(this).serialize();
        
        // $('#loadingModal').show();
        $("#reportingDiv").load('{{URL::to("incomeStatementReport")}}'+'?'+serializeValue);
    });


    });
</script>

@endsection


