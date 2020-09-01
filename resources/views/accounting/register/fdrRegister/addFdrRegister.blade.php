@extends('layouts/acc_layout')
@section('title', '| FDR Register')
@section('content')

<div class="row add-data-form">
<div class="col-md-1"></div>
<div class="col-md-10 fullbody">
<div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="{{url('fdrRegisterList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>FDR Account List</a>
</div>

<div class="panel panel-default panel-border">
	<div class="panel-heading">
	    <div class="panel-title">FDR Register</div>
	</div>


<div class="panel-body">
            <div class="row">                
                <div class="col-md-12">
                {!! Form::open(array('url' => 'storefdrRegisterAccount','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                    <div class="col-md-6">

                     <div class="form-group">
                        {!! Form::label('fdrId', 'FDR ID:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">                                    
                            {!! Form::text('fdrId', $fdrId, ['class'=>'form-control', 'id' => 'fdrId','readonly']) !!}
                            
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('fdrType', 'FDR Type:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">  
                        @php
                          $fdrTypeList = array(''=>'Select FDR Type') + DB::table('acc_fdr_type')->pluck('name','id')->toArray();
                        @endphp                                  
                            {!! Form::select('fdrType',$fdrTypeList ,null, ['class'=>'form-control', 'id' => 'fdrType']) !!}
                            <p id='fdrTypee' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                    </div>
                
                    
                    
                     <div class="form-group">
                            {!! Form::label('accNo', 'FDR No:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                 
                            {!! Form::text('accNo', null, ['class'=>'form-control', 'id' => 'accNo']) !!}
                                <p id='accNoe' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('accName', 'Account Name:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">                                    
                            {!! Form::text('accName', null, ['class'=>'form-control', 'id' => 'accName']) !!}
                            <p id='accNamee' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('project', 'Project:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                        @php
                          $projects = DB::table('gnr_project')->select('id','name','projectCode')->get();
                        @endphp
                         <select name="project" class="form-control input-sm" id="project">
                            <option value="">Select Project</option>                                         
                            @foreach($projects as $project)
                            <option value="{{$project->id}}">{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT).'-'.$project->name}}</option>
                            @endforeach
                        </select>                               
                            <p id='projecte' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                        @php
                          $projectTypes = DB::table('gnr_project_type')->select('id','name','projectTypeCode')->get();
                        @endphp
                         <select name="projectType" class="form-control input-sm" id="projectType">
                            <option value="">Select Project Type</option>                                         
                            @foreach($projectTypes as $projectType)
                            <option value="{{$projectType->id}}">{{str_pad($projectType->projectTypeCode,3,"0",STR_PAD_LEFT).'-'.$projectType->name}}</option>
                            @endforeach
                        </select>                               
                            <p id='projectTypee' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                        @php
                          $branches = DB::table('gnr_branch')->where('id',1)->select('id','name','branchCode')->get();
                        @endphp
                         <select name="branch" class="form-control input-sm" id="branch">
                            {{-- <option value="">Select Branch</option>   --}}                                       
                            @foreach($branches as $branch)
                            <option value="{{$branch->id}}" @if($branch->id==1){{"selected=selected"}}@endif>{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT).'-'.$branch->name}}</option>
                            @endforeach
                        </select>                               
                            <p id='branche' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                    </div>
                    

                    

                   
                </div> {{-- End of 1st coloum --}}

                <div class="col-md-6">


                <div class="form-group">
                        {!! Form::label('bankName', 'Bank:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                        @php
                            $bankList = array(''=>'Select Bank') + DB::table('gnr_bank')->pluck('name','id')->toArray();
                            
                        @endphp   
                            
                            {!! Form::select('bank', $bankList,null, ['class'=>'form-control', 'id' => 'bank']) !!}                         
                            <p id='banke' class="error" style="max-height:3px;color: red;"></p>                                             
                            
                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('bankBranch', 'Bank Branch Location:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                        @php
                            $bankBranchList = DB::table('gnr_bank_branch')->select('name','id','bankId_fk')->get();
                        @endphp

                        <select id="bankBranch" name="bankBranch" class="form-control">
                             <option value="">Select Location</option>
                             @foreach($bankBranchList as $bankBranch)
                                @php
                                $bankShortName = DB::table('gnr_bank')->where('id',$bankBranch->bankId_fk)->value('shortName');
                                @endphp
                             <option value="{{$bankBranch->id}}">{{$bankBranch->name.'-'.$bankShortName}}</option>>
                             @endforeach
                         </select>                            
                            
                            <p id='bankBranche' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                    </div>


                    <div class="form-group">
                            {!! Form::label('principalAmount', 'Principal Amount:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                
                           {!! Form::text('principalAmount', null, ['class'=>'form-control', 'id' => 'principalAmount']) !!}
                                <p id='principalAmounte' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('interestRate', 'Interest Rate:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            {!! Form::text('interestRate' ,null, ['class'=>'form-control', 'id' => 'interestRate']) !!}
                                <p id='interestRatee' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('openingDate', 'Opening Date:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                                    
                                {!! Form::text('openingDate', null, ['class'=>'form-control', 'id' => 'openingDate','readonly','style'=>'cursor:pointer']) !!}
                                <p id='openingDatee' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('duration', 'Duration (Months):', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9"> 
                            @php
                               $monthArray = array(''=>'Select Duration','1'=>'1 month','2'=>'2 months','3'=>'3 months','4'=>'4 months','5'=>'5 months','6'=>'6 months','7'=>'7 months','8'=>'8 months','9'=>'9 months','10'=>'10 months','11'=>'11 months','12'=>'12 months');
                           @endphp    

                                {!! Form::select('duration', $monthArray,null, ['class'=>'form-control', 'id' => 'duration']) !!}
                                <p id='duratione' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('matureDate', 'Mature Date:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                                    
                                {!! Form::text('matureDate', null, ['class'=>'form-control', 'id' => 'matureDate','readonly']) !!}
                                
                            </div>
                    </div>                   

                </div> {{-- End of 2nd coloum --}}



                
                 <div class="form-group">
                        <div class="col-sm-12 text-right" style="padding-right: 30px;">
                            {!! Form::submit('Submit', ['id' => 'save', 'class' => 'btn btn-info','type'=>'button']) !!}
                            <a href="{{url('fdrRegisterList/')}}" class="btn btn-danger closeBtn">Close</a>
                        </div>
                </div>

            {!! Form::close() !!}
                
            </div>

        </div>

    </div>

</div>
<div class="footerTitle" style="border-top:1px solid white"></div>
</div>
<div class="col-md-1"></div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        $("#principalAmount,#interestRate").on('input', function() {            
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        $("#duration").on('input', function() {            
            this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
        });




         /*Calculate Mature Date*/
         function calculateMatureDate() {
             var months = 0;
             if ($("#duration").val()!='') {
                months = parseInt($("#duration").val());
             }
             var openingDate =  $("#openingDate").val();

             if (openingDate!='') {
                var d = $.datepicker.parseDate('dd-mm-yy', $("#openingDate").val());
                if(months!=0 || months!=""){
                    d.setMonth(d.getMonth() + parseInt(months));
                }

                var matureDate = $('#matureDate');
                matureDate.datepicker({
                    dateFormat: 'dd-mm-yy'
                });
                matureDate.datepicker('setDate', d);
                $("#matureDate").datepicker( "option", "disabled", true );

             }
         }

         /*End Calculate Mature Date*/


         $("#openingDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                $("#openingDatee").empty();

                if($("#duration").val()!=''){
                    calculateMatureDate();
                }
            }
        });

         $("#duration").on('change', function() {             
             calculateMatureDate();
         });




         /*Store Information*/
         $('form').submit(function(event) {
             event.preventDefault();

             


             $.ajax({
                 url: './storefdrRegisterAccount',
                 type: 'POST',
                 dataType: 'json',
                 data: $('form').serialize(),
             })
             .done(function(data) {
                if (data.accessDenied) {
                    showAccessDeniedMessage();
                    return false;
                }
                 if (data.errors) {
                    if (data.errors['fdrType']) {
                        $("#fdrTypee").empty();
                        $("#fdrTypee").append('* '+data.errors['fdrType']);
                    }
                    if (data.errors['accNo']) {
                        $("#accNoe").empty();
                        $("#accNoe").append('* '+data.errors['accNo']);
                    }
                    if (data.errors['accName']) {
                        $("#accNamee").empty();
                        $("#accNamee").append('* '+data.errors['accName']);
                    }
                    if (data.errors['project']) {
                        $("#projecte").empty();
                        $("#projecte").append('* '+data.errors['project']);
                    }
                    if (data.errors['projectType']) {
                        $("#projectTypee").empty();
                        $("#projectTypee").append('* '+data.errors['projectType']);
                    }
                    if (data.errors['branch']) {
                        $("#branche").empty();
                        $("#branche").append('* '+data.errors['branch']);
                    }
                    if (data.errors['bank']) {
                        $("#bankNamee").empty();
                        $("#bankNamee").append('* '+data.errors['bank']);
                    }
                    if (data.errors['bankBranch']) {
                        $("#bankBranche").empty();
                        $("#bankBranche").append('* '+data.errors['bankBranch']);
                    }
                    if (data.errors['principalAmount']) {
                        $("#principalAmounte").empty();
                        $("#principalAmounte").append('* '+data.errors['principalAmount']);
                    }
                    if (data.errors['interestRate']) {
                        $("#interestRatee").empty();
                        $("#interestRatee").append('* '+data.errors['interestRate']);
                    }
                    if (data.errors['openingDate']) {
                        $("#openingDatee").empty();
                        $("#openingDatee").append('* '+data.errors['openingDate']);
                    }
                    if (data.errors['duration']) {
                        $("#duratione").empty();
                        $("#duratione").append('* '+data.errors['duration']);
                    }

                 } /*end has Errors*/
                 else{
                    
                    location.href = "fdrRegisterList";
                 }
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });
             
         });
         /*End Store Information*/


         /*On input/change Hide the Errors*/
         $(document).on('input', 'input', function() {
             $(this).closest('div').find('.error').empty();
         });
          $(document).on('change', 'select', function() {
             $(this).closest('div').find('.error').empty();
         });
         /*End On input/change Hide the Errors*/


         /*Filter Branch Location On selecting Bank*/
         $("#bank").on('change', function() {
             var bank = $("#bank option:selected").val();
             var csrf = "{{csrf_token()}}";

             $.ajax({
                 url: './accFdrGetBranchLocationBaseOnBank',
                 type: 'POST',
                 dataType: 'json',
                 data: {bank: bank, _token: csrf},
             })
             .done(function(branch) {
                if (branch.accessDenied) {
                    showAccessDeniedMessage();
                    return false;
                }
                
                $("#bankBranch").empty();
                $("#bankBranch").append("<option value=''>Select Location</option>");
                $.each(branch, function(index, branch) {
                     $("#bankBranch").append("<option value='"+branch.id+"'>"+branch.name+'-'+branch.bankName+"</option>");
                });
               

                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });
             
         });
         /*End Filter Branch Location On selecting Bank*/


 function pad (str, max) {
      str = str.toString();
      return str.length < max ? pad("0" + str, max) : str;
    }

          /* Change Project*/
         $("#project").change(function(){
            
            var projectId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                success: function( data ){
                    if (data.accessDenied) {
                        showAccessDeniedMessage();
                        return false;
                    }

                    $("#projectType").empty();
                    $("#projectType").prepend('<option selected="selected" value="">Select Project Type</option>');


                  /*  $("#branch").empty();
                    $("#branch").prepend('<option selected="selected" value="">Select Branch</option>');*/
                   

                    $.each(data['projectTypeList'], function (key, projectObj) {

                                
                            $('#projectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");                       
                    });

                    /*$.each(data['branchList'], function (key, branchObj) {

                        if (branchObj.id==1) {
                            $('#branch').append("<option value='"+ branchObj.id+"'selected='selected'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                        }
                        else{
                            $('#branch').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>"); 
                        }
                                
                                                  
                    });*/

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/
        });
         /*End Change Project*/

     /* Change Project Type*/
        /* $("#projectType").change(function(){
            var projectId = $("#project option:selected").val();
            var projectTypeId = $(this).val();


            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProjectType',
                data: {projectId:projectId,projectTypeId: projectTypeId,_token: csrf},
                dataType: 'json',
                success: function( data ){ 

                     $("#branch").empty();
                    $("#branch").append('<option selected="selected" value="">Select Branch</option>');
                    

                     $.each(data['branchList'], function (key, branchObj) {
                                
                        if (branchObj.id==1) {
                            $('#branch').append("<option value='"+ branchObj.id+"'selected='selected'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                        }
                        else{
                            $('#branch').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>"); 
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });

         });*//*End Change Project Type*/


        

    });/*End Ready*/
</script>


@endsection
