@extends('layouts/gnr_layout')
@section('title', '| Add Module')
@section('content')

	<div class="row add-data-form">
		<div class="col-md-12">
            <div class="col-md-8 col-md-offset-2 fullbody">
            	<div class="viewTitle" style="border-bottom:1px solid white;">
                    <a href="{{ url('viewArea/') }}" class="btn btn-info pull-right addViewBtn">
                    	<i class="glyphicon glyphicon-th-list viewIcon"></i>
                    	Module Lists
                    </a>
                </div>
                <div class="panel panel-default panel-border">
                	<div class="panel-heading">
                        <div class="panel-title">New Module</div>
                    </div>
                    <div class="panel-body">
                    	<div class="row">
                            <div class="col-md-12">
                        		{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
                                                
                                    <div class="col-md-2">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('projectName', 'Project:', ['class' => 'control-label pull-left']) !!}
                                            </div> 
                                            @php
                                                $projectNames = DB::table('gnr_project')->select('id','name','projectCode')->orderBy('projectCode')->get();  
                                            @endphp 
                                            <div class="col-sm-12">
                                              <select name="projectName" class="form-control input-sm" id="projectName">
                                                <option value="">Select Project</option>
                                                  @foreach ($projectNames as $projectName)
                                                    <option value="{{$projectName->id}}">{{str_pad($projectName->projectCode ,3, "0", STR_PAD_LEFT)}}-{{$projectName->name}}</option>
                                                   @endforeach  
                                              </select>
                                              <p id='projectNamee' style="max-height:2px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('branchName', 'Branch:', ['class' => 'control-label pull-left']) !!}
                                            </div> 
                                            @php
                                               $branchNames = DB::table('gnr_branch')->select('id','name','branchCode')->orderBy('branchCode')->get();  
                                            @endphp  
                                            <div class="col-sm-12">
                                                <select name="branchName" class="form-control input-sm" id="branchName">
                                                    <option value="">All</option>
                                                    @foreach ($branchNames as $branchName)
                                                       <option value="{{$branchName->id}}">{{str_pad($branchName->branchCode,3,"0",STR_PAD_LEFT)}}-{{$branchName->name}}</option>
                                                    @endforeach  
                                              </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('moduleName', 'Module:', ['class' => 'control-label pull-left']) !!}
                                            </div> 
                                            @php
                                               $moduleNames = DB::table('gnr_module')->select('id','name','code')->orderBy('code')->get();  
                                            @endphp  
                                            <div class="col-sm-12">
                                                <select name="moduleName" class="form-control input-sm" id="moduleName">
                                                    <option value="">Select Module</option>>
                                                    @foreach ($moduleNames as $moduleName)
                                                        <option value="{{$moduleName->id}}">{{$moduleName->name}}</option>
                                                    @endforeach 
                                              </select>
                                              <p id='moduleNamee' style="max-height:2px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('applicableAll', 'Applicable To All:', ['class' => 'control-label pull-left']) !!}
                                            </div> 
                                            <div class="col-sm-12">
                                                {{ Form::checkbox('applicableAll',1, null, ['class' => '','id'=>'applicableToAll']) }}
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-md-2" style="display:none" id='divStartDate'>
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('startDate', 'Start Date:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-sm-12">
                                                {!! Form::text('startDate', null, ['class' => 'form-control input-sm','readonly','autocomplete'=>'off']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12 text-right" style="padding-right: 30px;">
                                            {!! Form::button('Search', ['id' => 'search', 'class' => 'btn btn-info','type'=>'button']) !!}
                                        </div>
                                    </div>

                                     <div id="printDiv">
   
       
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" id="table" style="padding-left:1000px;">
                                            <thead>
                                                <tr>
                                                    <th>SL</th> 
                                                    <th>Branch Name</th>
                                                    <th>Start Date</th>
                                                   
                                                </tr>
                                            </thead>
                                            <tbody id="tbody">

                                            </tbody>
                                            <tfoot>
                                                <td style="text-decoration-style: none; "></td>
                                                <td style="text-align:center;"></td>
                                                <td style="text-align:center;">{!! Form::button('Search', ['id' => 'searchBtn', 'class' => 'btn btn-info','type'=>'button']) !!}</td>
                                            </tfoot>
                                        </table>
                                    </div> 
                        		{!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>
   
@endsection
<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>

<script type="text/javascript">
   $(document).ready(function() {
     $(function() {
        $('#startDate').datepicker({
          maxDate: "dateToday",
          dateFormat: 'dd-mm-yy',
          onSelect: function() {
            $(this).closest('div').find('.error').remove();
          }
        });
    })
  });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        function pad (str, max) {
            str =str.toString();

            return str.length < max ? pad("0" + str, max) : str;
        }
        $('#projectName').change(function(){
            var projectId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";
            $.ajax({
                url: './chamgeProjectName',
                type: 'POST',
                dataType:'json',
                data: {projectId:projectId,_token: csrf},
                success: function( data ) {
                    //alert(JSON.stringify(data['branchIds']));
                    $("#branchName").empty();
                    $("#branchName").prepend('<option selected="selected" value="">All</option>');

                    $.each(data['branchName'], function (key,branchObj) {

                        $('#branchName').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                    });
                 },
                error: function(_response){
                    alert("error");
                }
            })
        });
    });

</script>
<script type="text/javascript">
    $(document).ready(function(){
        $( "#search" ).click(function(event) {
            $('#table tbody').empty();
            $.ajax({
                url: './serchModule',
                type: 'POST',
                dataType: 'json',
                data: $('form').serialize(),
            })
            .done(function(data) {
                for(i=0;i<data['branchName'].length;i++){               
                    $('#table tbody').append('<tr><td>'+(i+1)+'</td><td class="hidden">'+data['branchName'][i].id+' </td><td class="branchName">'+data['branchName'][i].name+' </td><td style="text-align:center;"><input class="searchdate" style="background:#BDCAD9; text-decoration:none; text-align:center; readonly" /></td></tr>');
                   
                    $('.searchdate').val($('#startDate').val());
                    $('.searchdate').prop('disabled',true);
                    $(function() {
                        $('.searchdate').datepicker({
                            maxDate: "dateToday",
                            dateFormat: 'dd-mm-yy',
         
                        });
                    })
                }

            })
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $( "#searchBtn" ).click(function(event) {
            $.ajax({
                url: './validationProject',
                type: 'POST',
                dataType: 'json',
                data: $('form').serialize(),
            })
            .done(function(data) {
                alert(JSON.stringify(data));
                if(data.errors)  {
                    if (data.errors['projectName']) {
                        $("#projectNamee").empty();
                        $("#projectNamee").append('*'+data.errors['projectName']);
                    }
                    if (data.errors['moduleName']) { 
                        $("#moduleNamee").empty();
                        $("#moduleNamee").append('*'+data.errors['moduleName']);
                    } 
                }
                else{
                    $('#printDiv').show();
                    $('.field').val($('#startDate').val());
                }
            })
        });
    });
</script>



<script type="text/javascript">
    $(document).ready(function(){
        $("#applicableToAll").click(function() {
            var abc = $('#applicableToAll').val();
            if($(this).is(":checked")){
                $('#divStartDate').show();
                
                 $('.searchdate').prop('disabled',true);
            }else{
                $('#divStartDate').hide();
                $('#startDate').val('');
                $('.searchdate').prop('disabled',false);
                 
            }
            
        });
    });
    
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#branchName").select(function() {
            var propziro = $('#branchName').val();
            if(propziro!=''){
                $('#applicableToAll').prop('disabled',true);
            }
        });
    });
</script>

