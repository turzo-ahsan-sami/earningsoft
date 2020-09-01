@extends($route['layout'])
@section('title', '| Add Member')
@section('content')
<div class="row add-data-form">
  <div class="col-md-12">
    <div class="col-md-10 col-md-offset-1 fullbody">
        <div class="panel panel-default panel-border">
         <div class="panel-heading">
            <div class="panel-title">New Holiday Setting</div>
        </div>
        <div class="panel-body">
            {!! Form::open(['url'=>'']) !!}
            <div class="row" style="padding-bottom: 5px;">
                <div class="col-md-7">
                    {!! Form::label('year','Year:' , ['class' => 'control-label col-md-2','style'=>'padding-top:5px;']) !!}
                    <div class="col-md-3">
                        {!! Form::select('year',$yearList ,null,['id'=>'year','class'=>'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <table id="yearCalenderTable" class="table" style="color: black;">
                        <thead>
                            <tr>
                                <th>Day<br>No</th>
                                <th>Date</th>
                                <th>Holiday Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php                                
                            $dayNo = 1;
                            @endphp

                            @while($startDate->lte($endDate))
                            @if($startDate->day==1)
                            <tr>
                                <td></td>
                                <td colspan="3" class="name" style="background-color: #a3b1c9;font-size: 13px;font-weight: bold;border-right: 1px solid black;">{{$startDate->format('F')}}</td>
                            </tr>
                            @endif                             
                            <tr>
                                <td>{{$dayNo}}</td>
                                <td class="name" width="170">
                                    {!! Form::hidden('day[]',$dayNo) !!}
                                    {!! Form::checkbox('dayCheckBox[]', $dayNo, true,['weekDay'=>$startDate->dayOfWeek,'date'=>$startDate->format('Y-m-d'),'class'=>'day', 'id'=>'weeklyHolidayCheckBox']) !!}
                                    {!! Form::label('day',$startDate->format('d-m-Y').','.$startDate->format('l') , ['class' => 'control-label']) !!}
                                </td>
                                <td>
                                    {!! Form::hidden('weeklyHolidayText[]','false',['class'=>'weeklyHolidayText']) !!}
                                    {!! Form::hidden('govHolidayText[]','false',['class'=>'govHolidayText']) !!}
                                    <span class="weeklySpan"></span><span class="govHolidaySpan"></span>
                                </td>
                                <td class="descriptionTd name">
                                    {!! Form::hidden('descriptionText[]',null,['class'=>'descriptionText']) !!}
                                    <span class="descriptionSpan"></span>
                                </td>
                            </tr>
                            @php
                            $startDate->addDay();
                            $dayNo++;
                            @endphp
                            @endwhile
                        </tbody>
                    </table>                                
                </div>
                <div class="col-md-5">
                    <h4 class="title">Holiday List</h4>
                    <div class="weeklyHoliday">
                        {!! Form::checkbox('weeklyHoliday', null, false,['id'=>'weeklyHoliday']) !!}
                        {!! Form::label('weeklyHoliday','Weekly Holiday' , ['class' => 'control-label']) !!}
                    </div>
                    <div class="weeklyHolidayList">
                        <table id="weeklyHolidayListTable">
                            <tbody>
                                <tr>
                                    <td>
                                        {!! Form::checkbox('friday', null, false,['class'=>'weekDay','weekDayNo'=>5]) !!}
                                        {!! Form::label('friday','Mark all Friday' , ['class' => 'control-label']) !!}
                                    </td>
                                    <td>
                                        {!! Form::checkbox('saturday', null, false,['class'=>'weekDay','weekDayNo'=>6]) !!}
                                        {!! Form::label('saturday','Mark all Saturday' , ['class' => 'control-label']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {!! Form::checkbox('sunday', null, false,['class'=>'weekDay','weekDayNo'=>0]) !!}
                                        {!! Form::label('sunday','Mark all Sunday' , ['class' => 'control-label']) !!}
                                    </td>
                                    <td>
                                        {!! Form::checkbox('monday', null, false,['class'=>'weekDay','weekDayNo'=>1]) !!}
                                        {!! Form::label('monday','Mark all Monday' , ['class' => 'control-label']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {!! Form::checkbox('tuesday', null, false,['class'=>'weekDay','weekDayNo'=>2]) !!}
                                        {!! Form::label('tuesday','Mark all Tuesday' , ['class' => 'control-label']) !!}
                                    </td>
                                    <td>
                                        {!! Form::checkbox('wednesday', null, false,['class'=>'weekDay','weekDayNo'=>3]) !!}
                                        {!! Form::label('wednesday','Mark all Wednesday' , ['class' => 'control-label']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {!! Form::checkbox('thursday', null, false,['class'=>'weekDay','weekDayNo'=>4]) !!}
                                        {!! Form::label('thursday','Mark all Thursday' , ['class' => 'control-label']) !!}
                                    </td>                                                
                                </tr>
                            </tbody>
                        </table>                                   
                        
                    </div>
                    <br>
                    <div class="govHoliday">
                        {!! Form::checkbox('govHoliday', null, false,['id'=>'govHoliday']) !!}
                        {!! Form::label('govHoliday','Fixed Goverment Holiday' , ['class' => 'control-label']) !!}
                    </div>                                
                    <ul id="govHolidayList">
                        @foreach ($govHolidays as $govHoliday)
                        @php
                        $dayNo = date("z",strtotime($govHoliday->date.'-'.$selectedYear)) + 1;
                        @endphp
                        <li dayNo="{{$dayNo}}" description="{{$govHoliday->description}}">{{$govHoliday->date.', '.$govHoliday->title}}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <ul class="pager wizard pull-right" style="padding-right: 42.5%;">                                        
                <input id="submit" class="btn btn-info" type="submit" value="Submit" disabled>
            </ul>
            {!! Form::close() !!}
        </div>
    </div>
</div>
</div>
</div>


{{-- Loadding Modal --}}
<div id="loadingModal" data-backdrop="static" data-keyboard="false" class="modal fade" style="margin-top:3%;background-color: black;opacity:0.8 !important;">
    <div class="modal-dialog" style="text-align: center; padding-top: 15%;"> 
        <div class="modal-body">
            <i id="loaddingLogo" class="fa fa-spinner fa-spin fa-3x fa-fw" style="font-size:70px;"></i>            
        </div>
    </div>
</div>
{{-- End Loadding Modal --}}


<style type="text/css">
#yearCalenderTable tbody tr td:nth-child(1){
    background-color: #696969 !important;
    font-size: 12px;
    color: white;
}
#yearCalenderTable tbody tr td:nth-child(1),  #yearCalenderTable thead tr th:nth-child(1){
    width: 50px;
}
#yearCalenderTable thead tr th{
    border-bottom: 1px solid white !important;
    padding: 2px;
}
#yearCalenderTable tbody tr td{
    border: 1px dotted black;
}
#yearCalenderTable tbody tr td:nth-child(1){
    border-left: 0px;
}
#yearCalenderTable tbody tr td:nth-child(4){
    border-right: 1px solid black;
}
.weeklyHolidayList{
    margin-left: 10px;
}
#weeklyHolidayListTable tbody tr td,.weeklyHoliday{
    width: 160px;
    color: black;
}
.title,#govHolidayList,.govHoliday{
    color: black;
}


</style>


	<!-- <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
        <script src="{{ asset('js/jquery-ui/jquery-ui.min.js') }}"></script> -->

        <script>
            $(document).on('change', '.day', function() {
            // if ($("#weeklyHoliday").is(':checked')) {
            //     element.closest('tr').find('.weeklyHolidayText').val('true');
            // }else {

            // }
            
        });
    </script>

    <script type="text/javascript">	
      $(document).ready(function(){
        $("input[type='checkbox']").prop('checked', false).trigger('change');
        $("#submit").prop('disabled', false);

        $(document).on('change', '#weeklyHolidayCheckBox', function() {

        });

        $(document).on('change', '.weekDay', function() {
            if ($("#weeklyHoliday").is(':checked')) {
                    // console.log('ok');
                    
                    if ($(this).is(':checked')) {
                        var weekdayNo = $(this).attr('weekdayno');
                        var element = $(".day[weekday='"+weekdayNo+"']");
                        element.prop('checked', true);
                        element.attr('fromWeeklyHoliday',1);
                        // is there orgHoliday then add with coma
                        element.each(function(index, el) {
                            var hasOrgText = $(el).closest('tr').find('.orgHolidayTitleSpan').length;
                            if (hasOrgText>0) {
                                $(el).closest('tr').find('.weeklySpan').html(',Weekly');
                            }
                            else{
                                $(el).closest('tr').find('.weeklySpan').html('Weekly');
                            }
                        });
                        
                        element.closest('tr').find('.weeklyHolidayText').val('true');
                        
                        
                    }
                    else{
                        var weekdayNo = $(this).attr('weekdayno');
                        var element = $(".day[weekday='"+weekdayNo+"'][fromOrgHoliday!=1]");
                        var elementCopy = $(".day[weekday='"+weekdayNo+"']");
                        element.prop('checked', false);
                        elementCopy.attr('fromWeeklyHoliday',0);
                        elementCopy.closest('tr').find('.weeklySpan').html('');
                        elementCopy.closest('tr').find('.weeklyHolidayText').val('false');
                        
                    }
                    $("#govHoliday").trigger('change');
                }
            });

        $("#weeklyHoliday").on('change', function(event) {
            if ($(this).is(':checked')) {
                $(".weekDay").trigger('change');
                
            }
            else{
                $(".day[fromOrgHoliday!=1]").prop('checked', false);
                $(".day").closest('tr').find('.weeklySpan').html('');
                $(".day").closest('tr').find('.weeklyHolidayText').val('false');
                $("#govHoliday").trigger('change');
            }
        });

        $("#govHoliday").on('change', function(event) {

            var year = $("#year").val();
            var isLeapYear = year%4==0 ? 1 : 0;

            if ($(this).is(':checked')) {

                $("#govHolidayList li").each(function(index, el) {
                    var govElement = $(el);
                    console.log(govElement);
                    
                    /*if (isLeapYear==1) {
                        if ($(el).attr('dayno')>=60) {
                            $(el).attr('dayno',parseInt($(el).attr('dayno')) + 1);
                        }                             
                    }*/
                    if (isLeapYear==1 && $(el).attr('dayno')>=60) {
                        var element = $(".day[value='"+(parseInt($(el).attr('dayno'))+1)+"']");
                    }
                    else{
                        var element = $(".day[value='"+$(el).attr('dayno')+"']");
                    }
                    
                    element.prop('checked', true);
                    element.attr('fromGovHoliday',1);
                    
                    var weeklyHolidayText = element.closest('tr').find('.weeklyHolidayText').val();
                    
                    if (weeklyHolidayText=='false') {                            
                        element.closest('tr').find('.govHolidaySpan').html('Goverment Holiday');
                    }
                    else{
                        element.closest('tr').find('.govHolidaySpan').html(',Goverment Holiday');
                    }
                        //////
                        element.each(function(index, el) {
                            var hasOrgText = $(el).closest('tr').find('.orgHolidayTitleSpan').length;
                            if (hasOrgText>0) {
                                $(el).closest('tr').find('.govHolidaySpan').html(',Goverment Holiday');
                                if (govElement.attr('description')!='') {
                                    $(el).closest('tr').find('.descriptionSpan').html(', '+govElement.attr('description'));
                                }
                                
                            }
                            else{
                                $(el).closest('tr').find('.govHolidaySpan').html('Goverment Holiday');
                                $(el).closest('tr').find('.descriptionSpan').html(govElement.attr('description'));
                            }
                        });
                        /////

                        element.closest('tr').find('.govHolidayText').val('true');
                        element.closest('tr').find('.descriptionText').val($(el).attr('description'));
                        //element.closest('tr').find('.descriptionSpan').html($(el).attr('description'));
                    });
            }
            else{
               $("#govHolidayList li").each(function(index, el) {

                if (isLeapYear==1 && $(el).attr('dayno')>=60) {
                    var element = $(".day[value='"+(parseInt($(el).attr('dayno'))+1)+"']");
                }
                else{
                    var element = $(".day[value='"+$(el).attr('dayno')+"']");
                }

                console.log(element.attr('fromWeeklyHoliday'));
                // var element = $(".day[value='"+$(el).attr('dayno')+"']");
                if (element.attr('fromWeeklyHoliday')==0 || element.attr('fromWeeklyHoliday')==null) {
                    element.prop('checked', false);
                }                        
                element.attr('fromGovHoliday',0);
                element.closest('tr').find('.govHolidaySpan').html('');
                element.closest('tr').find('.govHolidayText').val('false');
                element.closest('tr').find('.descriptionText').val('');
                element.closest('tr').find('.descriptionSpan').html('');
            });

           }
       });

        /*limit the weekly holiday to 2 days*/
        $(document).on('click', '.weekDay', function() {
            var numOfCheckedBox = $(".weekDay:checked").length;
            if (numOfCheckedBox>2) {
                $(this).prop('checked', false).trigger('change');
                alert("You can't select more than two days.");
            }
        });
        /*end limit the weekly holiday to 2 days*/




        $('form').submit(function(event) {
            event.preventDefault();
            
            createTextFiledOfTitleNDescription();
                // console.log('not got it');
                
                
                
                $.ajax({
                    url: './StoreHoliday',
                    type: 'POST',
                    dataType: 'json',
                    data: $(this).serialize(),
                })
                .done(function(data) {
                    // $("#submit").prop("disabled", true);
                    if (data.responseTitle == 'Warning!') {
                        toastr.warning(data.responseText, data.responseTitle);
                        // console.log(data.samityDayCheck);

                        var samityCheckedArray = new Array();
                        var countSamityText = 0;

                        for (let index = 0; index <= data.samityDayCheck.length; index++) {
                            if (countSamityText == 0) {
                                samityCheckedArray[index] = 'You have to change the samity day of this following '+ data.samityDayCheck.length +' samities at first and then please create holiday again!'
                                samityCheckedArray[index+1] = data.samityDayCheck[index];
                                countSamityText = 1;
                            }
                            else {
                                samityCheckedArray[index] = data.samityDayCheck[index];
                            }
                        }
                        alert(samityCheckedArray.join("\n"));
                        console.log(samityCheckedArray);
                        
                    }
                    else {
                        toastr.success(data.responseText, data.responseTitle, opts);       
                    }
                })
                .fail(function() {
                    alert("error");
                });
                
            });

        function createTextFiledOfTitleNDescription() {
            console.log('ok got it');
            
            $(".weeklyHolidayText").each(function(index, el) {

                var weeklySpan = $(el).closest('td').find('.weeklySpan').html();
                var govHolidaySpan = $(el).closest('td').find('.govHolidaySpan').html();
                var result = weeklySpan + govHolidaySpan;
                $(el).after("<input type='text' style='display:none;' name='finalTitle[]' value='"+result+"'>");
            });
        }



        /*On year change get data*/
        $("#year").change(function(event) {
                //$("#loadingModal").modal('show');
                $("input[type='checkbox']").prop('checked', false).trigger('change');

                var year = $(this).val();
                var csrf = "{{csrf_token()}}";

                $.ajax({
                    url: './GetHolidayYearDetails',
                    type: 'POST',
                    dataType: 'json',
                    data: {year: year, _token: csrf},
                })
                .done(function(data) {
                    //$("#loadingModal").modal('hide');
                    //alert(JSON.stringify(data['orgHolidays']));
                    //return false;
                    // first make the table of the year
                    $("#yearCalenderTable tbody").empty();
                    // makeYearTable(data['startDate'],data['endDate']);
                    // console.log(data['weeklyDaysCheck'].length);
                    if (data['weeklyDaysCheck'].length == 1) {
                        makeNewYearTable(data['startDate'],data['endDate']);
                    }
                    

                    // Set the weekly holidays if exits
                    if(data['weekDayNoArray']=='empty'){
                        $("input[type='checkbox']").prop('checked', false).trigger('change');
                    }
                    else{
                        console.log(data['weekDayNoArray']);
                        
                        $.each(data['weekDayNoArray'], function(index, weekDay) {
                         $(".weekDay[weekdayno='"+weekDay+"']").prop('checked', true);
                     });
                        console.log("Got it");
                        
                        makeExistingYearTable(data['startDate'],data['endDate'], data['weeklyDaysCheck']);
                        // makeNewYearTable(data['startDate'],data['endDate']);
                        // $("#weeklyHoliday").prop('checked', true).trigger('change');
                        console.log(data['weeklyDaysCheck'].length);
                        
                    }                    
                    

                    // Set the Org/Branch/Samity Holidays
                    $.each(data['orgHolidays'], function(index, orgHolidays) {
                        var element = $(".day[date='"+orgHolidays.date+"']");
                        element.prop('checked', true);
                        element.attr('fromOrgHoliday',1);
                        element.closest('tr').find('.weeklySpan').before("<span class='orgHolidayTitleSpan'>"+orgHolidays.holidayType+"</span>");
                        element.closest('tr').find('.descriptionSpan').before("<span class='orgHolidayDescriptionSpan'>"+orgHolidays.description+"</span>");
                    });

                    console.log(data['hasGovHolidays']);
                     // Set the gov. holidays if exits
                     if (data['hasGovHolidays']>0) {
                        $("#govHoliday").prop('checked', true).trigger('change');
                    }

                })
                .fail(function() {
                    alert("error");
                });
                
            });
        /*end On year change get data*/
        $("#year").trigger('change');

        function makeExistingYearTable(startDateString, endDateString, weeklyDaysCheck) {
            console.log("Entered");
            $("#weeklyHoliday").prop('checked', true).trigger('change');
            var count = 0;
            
            var startDate = new Date(startDateString);
            var endDate = new Date(endDateString);

            var dayNo = 1;
            var markUp = "";

            var monthNo = startDate.getMonth();
            var isMonthChanged = 1;

            var firstMonthMarkUp = "<tr>"+
            "<td></td>"+
            "<td colspan='3' class='name' style='background-color: #a3b1c9;font-size: 13px;font-weight: bold;border-right: 1px solid black;'>"+$.datepicker.formatDate('MM',startDate)+"</td>"+
            "</tr>";
            markUp = markUp + firstMonthMarkUp;

            while(startDate<=endDate){

                var count = 0;
                var isChecked = 0;
                
                for (var checkDaysWeekly = 0; checkDaysWeekly < weeklyDaysCheck.length; checkDaysWeekly++) {

                    var compareDateWeekly = $.datepicker.formatDate('yy-mm-dd',new Date(weeklyDaysCheck[checkDaysWeekly])) ;
                    var compareDateStart  = $.datepicker.formatDate('yy-mm-dd',startDate);
                    if (compareDateWeekly == compareDateStart && count <= 1) {

                        if (startDate.getMonth()!=monthNo) {
                            monthNo = startDate.getMonth();
                            isMonthChanged = 1;
                        }
                        else{
                            isMonthChanged = 0;
                        }

                        if (isMonthChanged==1) {
                            markUp = markUp + "<tr>"+
                            "<td></td>"+
                            "<td colspan='3' class='name' style='background-color: #a3b1c9;font-size: 13px;font-weight: bold;border-right: 1px solid black;'>"+$.datepicker.formatDate('MM',startDate)+"</td>"+
                            "</tr>";

                        }                   

                        console.log(startDate.getDay());
                        console.log($.datepicker.formatDate('yy-mm-dd',startDate));
                        
                        markUp = markUp + "<tr>"+
                        "<td>"+dayNo+"</td>"+
                        "<td class='name' width='170'>"+
                        "<input name='day[]' type='hidden' value='"+dayNo+"'>"+
                        "<input weekday='"+startDate.getDay()+"' date='"+$.datepicker.formatDate('yy-mm-dd',startDate)+"' class='P' name='dayCheckBox[]' type='checkbox' checked value='"+dayNo+"'>"+
                        "<label for='day' class='control-label'>"+$.datepicker.formatDate('dd-mm-yy, DD',startDate)+"</label>"+
                        "</td>"+
                        "<td>"+
                        "<input class='weeklyHolidayText' name='weeklyHolidayText[]' type='hidden' value='false'>"+
                        "<input class='govHolidayText' name='govHolidayText[]' type='hidden' value='false'>"+
                        "<span class='weeklySpan'></span><span class='govHolidaySpan'></span>"+
                        "</td>"+
                        "<td class='descriptionTd name'>"+
                        "<input class='descriptionText' name='descriptionText[]' type='hidden'>"+
                        "<span class='descriptionSpan'></span>"+
                        "</td>"+
                        "</tr>";
                        
                        ++count;
                        ++isChecked;
                    }
                    
                }

                if (isChecked == 0) {
                    if (startDate.getMonth()!=monthNo) {
                        monthNo = startDate.getMonth();
                        isMonthChanged = 1;
                    }
                    else{
                        isMonthChanged = 0;
                    }

                    if (isMonthChanged==1) {
                        markUp = markUp + "<tr>"+
                        "<td></td>"+
                        "<td colspan='3' class='name' style='background-color: #a3b1c9;font-size: 13px;font-weight: bold;border-right: 1px solid black;'>"+$.datepicker.formatDate('MM',startDate)+"</td>"+
                        "</tr>";

                    }                   

                    markUp = markUp + "<tr>"+
                    "<td>"+dayNo+"</td>"+
                    "<td class='name' width='170'>"+
                    "<input name='day[]' type='hidden' value='"+dayNo+"'>"+
                    "<input weekday='"+startDate.getDay()+"' date='"+$.datepicker.formatDate('yy-mm-dd',startDate)+"' class='day' name='dayCheckBox[]' type='checkbox' value='"+dayNo+"'>"+
                    "<label for='day' class='control-label'>"+$.datepicker.formatDate('dd-mm-yy, DD',startDate)+"</label>"+
                    "</td>"+
                    "<td>"+
                    "<input class='weeklyHolidayText' id='weeklyHolidayText' name='weeklyHolidayText[]' type='hidden' value='false'>"+
                    "<input class='govHolidayText' name='govHolidayText[]' type='hidden' value='false'>"+
                    "<span class='weeklySpan'></span><span class='govHolidaySpan'></span>"+
                    "</td>"+
                    "<td class='descriptionTd name'>"+
                    "<input class='descriptionText' name='descriptionText[]' type='hidden'>"+
                    "<span class='descriptionSpan'></span>"+
                    "</td>"+
                    "</tr>";
                }

                startDate.setDate(startDate.getDate() + 1);
                dayNo++       
            }
            $("#yearCalenderTable tbody").append(markUp);
            
        } /*end make table funtion*/

        function makeNewYearTable(startDateString, endDateString) {
            console.log("Got it");
            var count = 0;
            
            var startDate = new Date(startDateString);
            var endDate = new Date(endDateString);

            var dayNo = 1;
            var markUp = "";

            var monthNo = startDate.getMonth();
            var isMonthChanged = 1;

            var firstMonthMarkUp = "<tr>"+
            "<td></td>"+
            "<td colspan='3' class='name' style='background-color: #a3b1c9;font-size: 13px;font-weight: bold;border-right: 1px solid black;'>"+$.datepicker.formatDate('MM',startDate)+"</td>"+
            "</tr>";
            markUp = markUp + firstMonthMarkUp;

            while(startDate<=endDate){

                if (startDate.getMonth()!=monthNo) {
                    monthNo = startDate.getMonth();
                    isMonthChanged = 1;
                }
                else{
                    isMonthChanged = 0;
                }

                if (isMonthChanged==1) {
                    markUp = markUp + "<tr>"+
                    "<td></td>"+
                    "<td colspan='3' class='name' style='background-color: #a3b1c9;font-size: 13px;font-weight: bold;border-right: 1px solid black;'>"+$.datepicker.formatDate('MM',startDate)+"</td>"+
                    "</tr>";

                }                   

                markUp = markUp + "<tr>"+
                "<td>"+dayNo+"</td>"+
                "<td class='name' width='170'>"+
                "<input name='day[]' type='hidden' value='"+dayNo+"'>"+
                "<input weekday='"+startDate.getDay()+"' date='"+$.datepicker.formatDate('yy-mm-dd',startDate)+"' class='day' name='dayCheckBox[]' type='checkbox' value='"+dayNo+"'>"+
                "<label for='day' class='control-label'>"+$.datepicker.formatDate('dd-mm-yy, DD',startDate)+"</label>"+
                "</td>"+
                "<td>"+
                "<input class='weeklyHolidayText' name='weeklyHolidayText[]' type='hidden' value='false'>"+
                "<input class='govHolidayText' name='govHolidayText[]' type='hidden' value='false'>"+
                "<span class='weeklySpan'></span><span class='govHolidaySpan'></span>"+
                "</td>"+
                "<td class='descriptionTd name'>"+
                "<input class='descriptionText' name='descriptionText[]' type='hidden'>"+
                "<span class='descriptionSpan'></span>"+
                "</td>"+
                "</tr>";

                startDate.setDate(startDate.getDate() + 1);
                dayNo++       
            }
            $("#yearCalenderTable tbody").append(markUp);
            
        } /*end make table funtion*/


    }); /*ready*/
</script>
@endsection