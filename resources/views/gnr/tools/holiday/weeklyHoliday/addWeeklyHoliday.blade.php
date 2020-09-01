@extends($route['layout'])
@section('title', '| Add Member')
@section('content')
@php
$maxDateAccDayEnd = DB::table('acc_day_end')->select('date','id')->orderBy('date', 'desc')->first();
$stromaxDateAccDayEnd=strtotime($maxDateAccDayEnd->date);
$maxDateMnfDayEnd = DB::table('mfn_day_end')->select('date','id')->orderBy('date', 'desc')->first();
///echo(max(2,4,6,8,10) . "<br>");
$stromaxDateMnfDayEnd=strtotime($maxDateMnfDayEnd->date);

$maxDateMnfWeeklyHoliday = DB::table('mfn_setting_weekly_holiday')->select('dateFrom','id')->orderBy('dateFrom', 'desc')->first();
///echo(max(2,4,6,8,10) . "<br>");
$stromaxDateMnfWeeklyHoliday=strtotime($maxDateMnfWeeklyHoliday->dateFrom);
//$date = "04-15-2013";
$max=max($stromaxDateAccDayEnd,$stromaxDateMnfDayEnd,$stromaxDateMnfWeeklyHoliday);
$maxData=date("d-m-Y",$max); 
$maxDate = date('d-m-Y',strtotime($maxData . "+1 days"));

@endphp


<div class="row add-data-form">
  <div class="col-md-12">
    <div class="col-md-10 col-md-offset-1 fullbody">
     <div class="viewTitle" style="border-bottom:1px solid white;">
        <a href="{{ url($route['path'].'/viewWeeklyHoliday/') }}" class="btn btn-info pull-right addViewBtn">
         <i class="glyphicon glyphicon-th-list viewIcon"></i>
         Weekly Holiday List
     </a>
 </div>
 <div class="panel panel-default panel-border">
     <div class="panel-heading">
        <div class="panel-title">New Weekly Holiday Configuration</div>
    </div>
    <div class="panel-body">
     <div class="row">
        <div class="col-md-12">
          {!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
          <div class="form-group">
            {!! Form::label('weeklyHolidayIds', 'Day:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-6">
             {!! Form::checkbox('weeklyHolidayIds[]', '1', false) !!}
             {!! Form::label('saturday', 'Saturday', ['class' => 'control-label']) !!}  &nbsp &nbsp
             {!! Form::checkbox('weeklyHolidayIds[]', '2', false) !!}
             {!! Form::label('sunday', 'Sunday', ['class' => 'control-label']) !!}  &nbsp &nbsp
             {!! Form::checkbox('weeklyHolidayIds[]', '3', false) !!}
             {!! Form::label('monday', 'Monday', ['class' => 'control-label']) !!}
             &nbsp &nbsp
             {!! Form::checkbox('weeklyHolidayIds[]', '4', false) !!}
             {!! Form::label('tuesday', 'Tuesday', ['class' => 'control-label']) !!}
             &nbsp &nbsp
             {!! Form::checkbox('weeklyHolidayIds[]', '5', false) !!}
             {!! Form::label('wednesday', 'Wednesday', ['class' => 'control-label']) !!}
             &nbsp &nbsp
             {!! Form::checkbox('weeklyHolidayIds[]', '6', false) !!}
             {!! Form::label('thursday', 'Thursday', ['class' => 'control-label']) !!}
             &nbsp &nbsp
             {!! Form::checkbox('weeklyHolidayIds[]', '7', false) !!}
             {!! Form::label('thursday', 'Friday', ['class' => 'control-label']) !!}
         </div>
     </div>
     {{ Form::hidden('invisible', 'secret', array('id' => 'invisible_id')) }}


     <div class="form-group">
        {!! Form::label('dateFrom', 'Weekly Holiday Date From:', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-6">
            {!! Form::text('dateFrom',null,['id'=>'dateFrom','class'=>'form-control','style'=>'cursor:pointer;','readonly']) !!}
        </div>
    </div>



    <div class="form-group">
        <div class="col-sm-8">
            <ul class="pager wizard pull-right">                                        
                <input id="submit" class="btn btn-info" type="submit" value="Submit">
                <a href="{{ url($route['path'].'/viewWeeklyHoliday/') }}" class="btn btn-danger closeBtn">Close</a>
            </ul>
        </div>
    </div>


    {!! Form::close() !!}
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<style type="text/css">
.branchDiv,.samityDiv{
    display: none;
}
</style>
{{-- <script src="{{ asset('js/jquery-1.11.1.min.js') }}"></script> --}}
<script type="text/javascript">	
  $(document).ready(function(){
    $('form').submit(function(event){
        event.preventDefault();
        $(".error").remove();
        $("#submit").prop('disabled', true);
                // $('#loadingModal').show();
                
                $.ajax({
                   type: 'post',
                   url: './storeWeeklyHoliday',
                   dataType: 'json',
                   data: $('form').serialize(),
                   success: function(data) {
                    $('#loadingModal').hide();
                       // Print Error
                       if(data.errors) {
                        $("#submit").prop('disabled', false);
                        $.each(data.errors, function(name, error) {
                         $("#"+name).after("<p class='error' style='color:red;'>* "+data.errors[name]+"</p>");
                     });
                    }
                    else {

                        toastr.success(data.responseText, data.responseTitle, opts);
                        
                        setTimeout(function(){
                            window.location.href = '{{ url($route['path'].'/viewWeeklyHoliday') }}';
                        }, 2000);
                    }
                },
                error: function(_response) {
                    alert(_response.errors);
                }
            });

            });

    $("#dateFrom").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "c-10:c+10",
        dateFormat: 'dd-mm-yy',
        minDate: "<?php echo $maxDate ?>",
        // onSelect: function() {
        //     var date = $(this).datepicker('getDate');
        //     //$("#dateTo").datepicker('option','minDate',date);
        //     $(this).closest('div').find('.error').remove();
        // }
    });

    $("#dateTo").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "c-10:c+10",
        dateFormat: 'dd-mm-yy',
        onSelect: function() {
            var date = $(this).datepicker('getDate');
            $("#dateFrom").datepicker('option','maxDate',date);
            $(this).closest('div').find('.error').remove();                           
        }
    });

    /* hide/show branch,samity div*/
    $(document).on('change', 'input[name="applicableFor"]', function(event) {
     var selectedValue = $('input[name="applicableFor"]:checked').val();
     if (selectedValue=='org') {
        $(".branchDiv").hide();
        $(".samityDiv").hide();
    }
    else if(selectedValue=='branch'){
        $(".branchDiv").show();
        $(".samityDiv").hide();  
    }
    else if(selectedValue=='samity'){
        $(".branchDiv").hide();
        $(".samityDiv").show();  
    }
});
    /* end hide/show branch,samity div*/

             // Hide Eddor
             $(document).on('input', 'input', function() {
                $(this).closest('div').find('.error').remove();
            });
             $(document).on('change', 'select', function() {
                $(this).closest('div').find('.error').remove();
            });


         }); /*end ready*/
     </script>
     @endsection