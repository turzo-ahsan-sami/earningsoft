@extends('layouts/acc_layout')
@section('title', '| House Owner Register')
@section('content')

    <div class="row add-data-form">
        <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('viewHouseOwnerRegisterList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>House Owner List</a>
                </div>

                <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">House Owner Register</div>
                </div>

                <div class="panel-body">
                    <div class="row"> 
                        <div class="col-md-8">
                            {!! Form::open(array('url' =>'', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                         
                            <div class="form-group">
                                {!! Form::label('houseOwnerName', 'House Owner Name', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-1 control-label">: </div>
                                <div class="col-sm-8"> 

                                    {!! Form::text('houseOwnerName', null, ['class'=>'form-control', 'id' =>'houseOwnerName']) !!}
                                    <p id='houseOwnerNameE' class="error" style="max-height:3px;color: red;"></p>
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('projectName','Project', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-1 control-label">: </div>
                                <div class="col-sm-8">
                                                 
                                    @php
                                        $projects = DB::table('gnr_project')->select('id','name','projectCode')->get();
                                    @endphp

                                    <select name="projectName" class="form-control input-sm" id="projectName">

                                        <option value="">Select Project</option>                                         
                                        @foreach($projects as $project)

                                            <option value="{{$project->id}}">{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT).'-'.$project->name}}</option>
                                        @endforeach
                                                   
                                                    
                                    </select>                               
                                        <p id='projectNameE' class="error" style="max-height:3px;color: red;"></p>
                                </div>
                            </div>

                            <div class="form-group">

                                {!! Form::label('branchName', 'Branch', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-1 control-label">: </div>
                                <div class="col-sm-8">
                                    @php
                                        $branches = DB::table('gnr_branch')->select('id','name','branchCode')->get();
                                    @endphp

                                    <select name="branchName" class="form-control input-sm" id="branchName">
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)

                                            <option value="{{$branch->id}}">{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT).'-'.$branch->name}}</option>

                                        @endforeach
                                                           
                                    </select>                               
                                    <p id='branchNameE' class="error" style="max-height:3px;color: red;"></p>
                                </div>
                            </div> 

                            <div class="form-group">

                                {!! Form::label('phoneNumber', 'Mobile Number', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-1 control-label">: </div>
                                <div class="col-sm-8">                                    
                                    {!! Form::text('phoneNumber', null, ['class'=>'form-control', 'id' => 'emailAddress']) !!}

                                    <p id='phoneNumberE' class="error" style="max-height:3px;color: red;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('emailAddress', 'Email Address', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-1 control-label">: </div>
                                <div class="col-sm-8">                                    
                                    {!! Form::text('emailAddress', null, ['class'=>'form-control', 'id' => 'emailAddress']) !!}

                                    <p id='emailAddressE' class="error" style="max-height:3px;color: red;"></p>
                                </div>

                            </div>          
                            <div class="form-group">
                                {!! Form::label('bankAccount', 'Bank Account Info', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-1 control-label">: </div>
                                <div class="col-sm-8"> 
                                    {!! Form::textArea('bankAccount', null, ['class'=>'form-control', 'rows'=>'2','id' => 'bankAccount']) !!}

                                    <p id='bankAccountE' class="error" style="max-height:3px;color: red;"></p>
                                </div>

                            </div>
                            <div class="form-group">

                                {!! Form::label('branchAddress', 'Branch Address', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-1 control-label">: </div>
                                <div class="col-sm-8">                                    
                                    {!! Form::textArea('branchAddress', null, ['class'=>'form-control', 'rows'=>'2','id' => 'branchAddress']) !!}

                                    <p id='branchAddressE' class="error" style="max-height:3px;color: red;"></p>
                                </div>

                            </div> 

                            <div class="form-group">
                                <div class="col-sm-12 text-right" style="padding-right: 30px;">
                                    {!! Form::submit('Submit', ['id' => 'save', 'class' => 'btn btn-info','type'=>'button']) !!}
                                    <a href="{{url('viewHouseOwnerRegisterList/')}}" class="btn btn-danger closeBtn">Close</a>
                                </div>
                            </div>

                        {!! Form::close() !!}
                    </div>
                    <div class="col-md-4 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="80%" height="" style="float:right"></div>
                </div>
            </div>
        </div>
        <div class="footerTitle" style="border-top:1px solid white"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        /*Store Information*/
        $('form').submit(function(event) {
            event.preventDefault();
            $("#save").prop("disabled", true);
            $.ajax({
                url: './addHouseOwner',
                type: 'POST',
                dataType: 'json',
                data: $('form').serialize(),
            })
            .done(function(data) {
                if (data.errors) {
                    if (data.errors['houseOwnerName']) {
                        $("#houseOwnerNameE").empty();
                        $("#houseOwnerNameE").append('* '+data.errors['houseOwnerName']);
                         }
                    if (data.errors['projectName']) {
                        $("#projectNameE").empty();
                        $("#projectNameE").append('* '+data.errors['projectName']);
                    }
                    
                    if (data.errors['branchName']) {
                        $("#branchNameE").empty();
                        $("#branchNameE").append('* '+data.errors['branchName']);
                    }
                }
                else{
               
                    location.href = "viewHouseOwnerRegisterList";
                }
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
           
            })    
        });

            $(document).on('input','input',function() {
                $(this).closest('div').find('p').remove();
            });
            $(document).on('change','select',function() {
                $(this).closest('div').find('p').remove();
            });

            $(document).on('input','textarea',function() {
                $(this).closest('div').find('p').remove();
            });
       });
       
</script>

<!--  Change Project -->

<script>
    $(document).ready(function() {

       function pad (str, max) {
           str = str.toString();

            return str.length < max ? pad("0" + str, max) : str;
        }

        $("#projectName").change(function() {
            var projectId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                success: function( data ) {
                    $("#branchName").empty();
                    $("#branchName").prepend('<option selected="selected" value="">Select Branch </option>');

                    $.each(data['branchList'], function (key, branchObj) {

                        if (branchObj.id==1) {
                            $('#branchName').append("<option value='"+ branchObj.id+"'selected='selected'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                        }
                        else {
                            $('#branchName').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>"); 
                        }
                                
                    });
                 },
                error: function(_response){
                    alert("error");
                }
           });
       });
    });

</script>

@endsection





