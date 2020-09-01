@extends($route['layout'])
@section('title', '| Add Member')
@section('content')

<div class="row add-data-form">
  <div class="col-md-12">
    <div class="col-md-10 col-md-offset-1 fullbody">
     <div class="viewTitle" style="border-bottom:1px solid white;">
        <a href="{{ url($route['path'].'/viewOrgBranchSamityHoliday/') }}" class="btn btn-info pull-right addViewBtn">
         <i class="glyphicon glyphicon-th-list viewIcon"></i>
         Holiday Config. List
     </a>
 </div>
 <div class="panel panel-default panel-border">
     <div class="panel-heading">
        <div class="panel-title">New Holiday Configuration</div>
    </div>
    <div class="panel-body">
     <div class="row">
        <div class="col-md-12">
          {!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
          <div class="form-group">
            {!! Form::label('applicableFor', 'Applicable For:', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-6">
             {!! Form::radio('applicableFor', 'org', true) !!}
             {!! Form::label('organization', 'Organization', ['class' => 'control-label']) !!}  &nbsp &nbsp
             {!! Form::radio('applicableFor', 'branch', false) !!}
             {!! Form::label('branch', 'Branch', ['class' => 'control-label']) !!}
             &nbsp &nbsp
             {!! Form::radio('applicableFor', 'samity', false) !!}
             {!! Form::label('samity', 'Samity', ['class' => 'control-label']) !!}
         </div>
     </div>
     @php
     $branches = DB::table('gnr_branch')->select('name','id','branchCode')->orderBy('branchCode')->get();
     @endphp
     <div class="form-group branchDiv">
        {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-6">
           <select id="branch" name="branch" class="form-control">
               <option value=''>Select</option>
               @foreach($branches as $branch)
               <option value='{{$branch->id}}'>{{str_pad($branch->branchCode,3,'0',STR_PAD_LEFT) .'-'.$branch->name}}</option>
               @endforeach
           </select>
       </div>
   </div>

   @php
   $samities = DB::table('mfn_samity')->where('status','1')->select('name','id','code')->orderBy('code')->get();
   @endphp
   <div class="form-group samityDiv">
    {!! Form::label('samity', 'Samity:', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
       <select id="samity" name="samity" class="form-control">
           <option value=''>Select</option>
           @foreach($samities as $samity)
           <option value='{{$samity->id}}'>{{$samity->code.'-'.$samity->name}}</option>
           @endforeach
       </select>
   </div>
</div>

<div class="form-group">
    {!! Form::label('holidayDateFrom', 'Holiday Date From:', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::text('dateFrom',null,['id'=>'dateFrom','class'=>'form-control','style'=>'cursor:pointer;','readonly']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('holidayDateTo', 'Holiday Date To:', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::text('dateTo',null,['id'=>'dateTo','class'=>'form-control','readonly','style'=>'cursor:pointer;']) !!}
    </div>
</div>
@php
$holidayTypes = array(''=>'Select','Holiday'=>'Holiday','Others'=>'Others');
@endphp
<div class="form-group">
    {!! Form::label('holidayType', 'Holiday Type:', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::select('holidayType',$holidayTypes,null,['class'=>'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('description', 'Description:', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::textArea('description',null,['class'=>'form-control','rows'=>2]) !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-8">
        <ul class="pager wizard pull-right">                                        
            <input id="submit" class="btn btn-info" type="submit" value="Submit">
            <a href="{{ url($route['path'].'/viewOrgBranchSamityHoliday/') }}" class="btn btn-danger closeBtn">Close</a>
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
                   url: './storeOrgBranchSamityHoliday',
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
                            window.location.href = '{{ url($route['path'].'/viewOrgBranchSamityHoliday') }}';
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
        onSelect: function() {
            var date = $(this).datepicker('getDate');
            $("#dateTo").datepicker('option','minDate',date);
            $(this).closest('div').find('.error').remove();
        }
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