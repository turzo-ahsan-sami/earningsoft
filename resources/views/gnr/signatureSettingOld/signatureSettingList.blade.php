@extends('layouts/gnr_layout')
@section('title', '|Signature Setting')
@section('content')

  <div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px;">
                        <div class="panel-options">
                            <a href="{{url('addSignatureSetting/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Signature Setting</a>
                        </div>

                        <h1 align="center" style="font-family: Antiqua;letter-spacing:2px;"><font color="white"> SIGNATURE SETTING</font></h1>
                    </div>
        
                    <div class="panel-body panelBodyView">       
                    <div>
                        <script type="text/javascript">

                            jQuery(document).ready(function($) {
                                   $("#AdvReg").dataTable({              
                                    
                                     "oLanguage": {

                                    "sEmptyTable": "No Records Available",
                                    "sLengthMenu": "Show _MENU_ "

                                    }

                                  });
                            });
                            
                        </script>

                    </div>


                    <table class="table table-striped table-bordered" id="AdvReg" style="color:black;">
                        <thead>
                            <tr>
                                <th width="30">SL#</th>
                                <th>Module Name</th>
                                <th>Group Name</th>
                                <th>Company Name</th>
                                <th>Project name</th>
                                <th>Project Type</th>
                                <th>Branch</th>
                                <th>Action</th>
                                           
                            </tr>
                        </thead>
                        <tbody>
                                
               
                            @foreach ($gnrSignatureSettings as $index=>$gnrSignatureSettings)

        
                                {{-- expr --}}
                                @php
                                    $moduleName= DB::table('gnr_module')->where('id',$gnrSignatureSettings->moduleId)->value('name');
                                    $groupName= DB::table('gnr_group')->where('id',$gnrSignatureSettings->groupId)->value('name');
                                    $companyName= DB::table('gnr_company')->where('id',$gnrSignatureSettings->companyId)->value('name');
                                    $projectName= DB::table('gnr_project')->where('id',$gnrSignatureSettings->projectId)->value('name');
                                    $projectTypeName= DB::table('gnr_project_type')->where('id',$gnrSignatureSettings->projectTypeId)->value('name');
                                    $branchName= DB::table('gnr_branch')->where('id',$gnrSignatureSettings->branchId)->value('name');
                                @endphp

                                <tr style:"float:left;">
                                    <td>{{$index+1}}</td>
                                    <td>{{$moduleName}}</td>
                                    <td>{{$groupName}}</td>
                                    <td>{{$companyName}}</td>
                                    <td>{{$projectName}}</td>
                                    <td>{{$projectTypeName}}</td>
                                    <td>{{$branchName}}</td>
                                  
                                  
                                    <td width="80">
                                        <a href="javascript:;" class="view-modal" signatureSetting="{{$gnrSignatureSettings->id}}"><i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>&nbsp; 
                                        <a href="javascript:;" class="edit-modal" signatureSetting="{{$gnrSignatureSettings->id}}"><span class="glyphicon glyphicon-edit"></span> </a>&nbsp;
                                        <a href="javascript:;" class="delete-modal" signatureSetting="{{$gnrSignatureSettings->id}}"><span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                    </td>
                                                                      
                                </tr>
                                           
                            @endforeach

                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Model -->
 

 <div id="viewModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">View Signature Setting Info</h4>
            </div>

            <div class="modal-body">
                <div class="row" style="padding-bottom: 20px;"> 
                    <div class="col-md-12">
                        <div class="col-md-12" style="padding-right:2%;">
                            <div class="form-horizontal form-groups">
                                <input id="VMsignature" type="hidden" name="signature" value="">
                                <div class="form-group">
                                    {!! Form::label('module','Module Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8">                                     
                                        {!! Form::text('module', null,['id'=>'VMmodule','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                        <p id='modulee' class="error" style=color: red;"></p>
                                    </div>

                                </div>

                                <div class="form-group">
                                    {!! Form::label('group','Group Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8">                                     
                                        {!! Form::text('group', null,['id'=>'VMgroup','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                        <p id='groupe' class="error" style=color: red;"></p>
                                    </div>

                                </div>
                                <div class="form-group">
                                    {!! Form::label('company','Company Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8">                                     
                                        {!! Form::text('company', null,['id'=>'VMcompany','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                        <p id='company' class="error" style=color: red;"></p>
                                    </div>

                                </div>

                                <div class="form-group">

                                    {!! Form::label('project','Project Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>

                                    <div class="col-sm-8">                                               
                                        {!! Form::text('project', null,['id'=>'VMproject','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}

                                        <p id='projecte' class="error" style=color: red;"></p>

                                    </div>
                                </div>

                                <div class="form-group">

                                    {!! Form::label('projectType','Project Type', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>

                                    <div class="col-sm-8">                                               
                                        {!! Form::text('projectType', null,['id'=>'VMprojectType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}

                                        <p id='projectType' class="error" style=color: red;"></p>

                                    </div>

                                </div>

                                <div class="form-group">

                                    {!! Form::label('branch','Branch Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>

                                    <div class="col-sm-8">                                               
                                        {!! Form::text('branch', null,['id'=>'VMbranch','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}

                                        <p id='branch' class="error" style=color: red;"></p>

                                    </div>
                                </div>

                                <table style="width:100%; margin-left:15px;padding-top: 10px;" class="table table-striped table-bordered dataTable no-footer"  id="table1">
                                    <thead>
                                        <th>Sigtnature</th>
                                        <th>Role</th>
                                        <th>Employee Name</th>
                                        <th>Action</th>
                                    </thead> 
                                    <tbody>
                      
                                    </tbody>
                     
                                </table>

                                <div class="modal-footer">

                                    <button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                                </div>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="col-md-3"></div>
        </div>
    </div>
</div>
                               

                             
{{--                           Edit Modal                       --}}

 <div id="editModal" class="modal fade" style="margin-top:3%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Update Signature Setting</h4>
            </div>
            <div class="modal-body">
                <div class="panel-body float-left">
                    <div class="row">   
                        <div class="col-md-12">
                            {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                            <div class="col-md-12">

                                <div class="form-group">
                                    {!! Form::label('module', 'Module Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8"> 
                                        @php

                                            $modules = DB::table('gnr_module')->select('name','id')->get();

                                        @endphp 
                                        <select  class="form-control" id='EMmodule' name="module">
                                            <option value="">Select Module Name</option>

                                            @foreach($modules as $module )

                                                  <option  value="{{$module->id}}">{{$module->name}}</option>

                                            @endforeach
                                        </select>

                                        <p id='EMmodulee' class="error" style="max-height:3px;color: red;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('group', 'Group Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8"> 
                                        @php

                                            $groups = DB::table('gnr_group')->select('name','id')->get();

                                        @endphp 
                                        <select  class="form-control" id='EMgroup' name="group">

                                            <option value="">Select Group Name</option>

                                            @foreach($groups as $group )
                                                  <option  value="{{$group->id}}">{{$group->name}}</option>
                                            @endforeach

                                        </select>

                                        <p id='EMprojecte' class="error" style="max-height:3px;color: red;"></p>

                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('company', 'Company Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8"> 
                                        @php

                                            $companys = DB::table('gnr_company')->select('name','id')->get();

                                        @endphp 
                                        <select  class="form-control" id='EMcompany' name="company">
                                            <option value="">Select Project Name</option>

                                            @foreach($companys as $company )

                                                  <option  value="{{$company->id}}">{{$company->name}}</option>

                                            @endforeach

                                        </select>

                                        <p id='EMcompanyee' class="error" style="max-height:3px;color: red;"></p>

                                    </div>
                                </div>
        
                                <div class="form-group">
                                    {!! Form::label('project', 'Project', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8"> 
                                        @php

                                            $projects = DB::table('gnr_project')->select('projectCode','name','id')->get();

                                        @endphp 
                                        <select  class="form-control" id='EMproject' name="project">
                                            <option value="">Select Project Name</option>

                                            @foreach($projects as $project )

                                                <option  value="{{$project->id}}">{{$project->projectCode.'-'.$project->name}}</option>

                                            @endforeach
                                        </select>

                                        <p id='EMprojectIde' class="error" style="max-height:3px;color: red;"></p>

                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('projectType', 'Project Type', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8"> 
                                        @php
                                            $projectTypes =DB::table('gnr_project_type')->select('projectTypeCode','name','id')->get();
                                        @endphp 
                                        <select class="form-control" id="EMprojectType" name="projectType">

                                            <option>Slect Project Type</option>
                                            @foreach( $projectTypes as  $projectType)
                                                <option  value="{{$projectType->id}}">{{$projectType->projectTypeCode.'-'.$projectType->name}}</option>
                                            @endforeach

                                        </select>
                                        <p id='EMprojectTypeIde' class="error" style="max-height:3px;color: red;"></p>

                                    </div>

                                </div>

                                <div class="form-group">
                                    {!! Form::label('branch', 'Branch Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8"> 
                                        @php

                                           $branchs = DB::table('gnr_branch')->select('name','id')->get();

                                        @endphp 
                                        <select  class="form-control" id='EMbranch' name="branch">

                                            <option value="">Select branch Name</option>

                                            @foreach($branchs as $branch )

                                                <option  value="{{$branch->id}}">{{$branch->name}}</option>

                                            @endforeach
                                        </select>

                                        <p id='EMbranche' class="error" style="max-height:3px;color: red;"></p>

                                    </div>
                                </div>

                                <div class="form-group">

                                    {!! Form::label('numOfSignature', 'Number Of Signature', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8"> 

                                        <select class="form-control" id="EMnumOfSignature" name="numOfSignature">
                                            <option value="">Select Signature Row</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>

                                        </select>

                                        <p id='numOfSignaturee' class="error" style="max-height:3px;color: red;"></p>

                                    </div>
                                </div>

                                @php
                                       $roleId = DB::table('gnr_role')->pluck('name','id')->toArray();
                                       $employeeId = DB::table('hr_emp_general_info')->pluck('emp_name_english','id')->toArray();
                                       $signatureId = DB::table('gnr_signature_role')->pluck('name','id')->toArray();

                                @endphp

       

                                <table style="width:100%; margin-left:15px;padding-top: 10px;" class="table table-striped table-bordered dataTable no-footer" id="table2">

                                    <thead>
                                        <th>Signature</th>
                                        <th>Role</th>
                                        <th>Employee Name</th>
                                        <th>Action</th>
                                    </thead>

                                    <p id='RowInfo' class="error" style="max-height:3px;color: red;"></p>

                                    <tbody>
                                       
                                    </tbody>
                                  
                                </table>

                                <div class="modal-footer">
                                   <input id="EMsignatureSetting" type="hidden" name="signatureSetting" value="">
                                   <button type="button" id="updateButton" class="btn btn-success"><span class="glyphicon glyphicon-edit" style="padding-right:4px;"></span>Update</button>
                                   <button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                                </div>
                             
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 

<!-- - - - - - - - - - - -Delete Model- - - - - - - - - - - - - -->


   <div id="deleteModal" class="modal fade" style="margin-top:3%;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Signature Setting</h4>
                </div>
                <div class="modal-body ">
                    <div class="row" style="padding-bottom:20px;"> </div>

                        <h2>Are You Confirm to Delete This Record?</h2>

                    <div class="modal-footer">

                        <input id="DMsignatureSettingId" type="hidden" name="houseOwnerReg" value=""/>
                        <button type="button" class="btn btn-danger"  id="DMsignatureSetting"  data-dismiss="modal">confirm</button>

                        <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>

                    </div>
                </div>
            </div>
        </div>
  </div>

{{--end delete modal--}}

  <script>         
    $(document).ready(function(){ 
        $(document).on('click', '.delete-modal', function(){

            $("#DMsignatureSettingId").val($(this).attr('signatureSetting'));
            $("#deleteModal").find('.modal-dialog').css('width', '60%');
            $('#deleteModal').modal('show');

        });
        $("#DMsignatureSetting").on('click',  function(){ 
            var signatureSetting= $("#DMsignatureSettingId").val();
            var csrf = "{{csrf_token()}}";
            $.ajax({
                url: './deleteDMsignatureSetting',
                type: 'POST',
                dataType: 'json',
                data: {id:signatureSetting, _token:csrf},
            })
            .done(function(data){ 
                location.reload();
            })
            .fail(function(){
                console.log("error");
            })
            .always(function(){
                console.log("complete");
            });
        });

    });     
</script>  

<!--                          Project Change                     -->

    <script>

             /* Change Project*/

    $(document).ready(function() {
        function pad (str, max) {
            str = str.toString();

            return str.length < max ? pad("0" + str, max) : str;
        }

        $("#EMprojectName").change(function() {
            var projectId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";
            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                success: function(data) {
                    $.each(data['branchList'], function (key, branchObj) {

                        if (branchObj.id==1) {

                            $('#EMbranchName').append("<option value='"+ branchObj.id+"'selected='selected'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");

                            }

                        else {

                            $('#EMbranchName').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                        }
                    });

                },
                 error: function(_response) {
                    alert("error");
                }
            });
        });
    });
</script>


<script type="text/javascript">
    $(document).ready(function(){ 

        function pad (str, max) {
            str = str.toString();
            return str.length < max ? pad("0" + str, max) : str;
        }

        $("#projectType").change(function(){
            var projectId = $("#Project").val();
            var projectTypeId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProjectType',
                data: {projectId:projectId,projectTypeId:projectTypeId,_token: csrf},
                dataType: 'json',
                success: function( data ){ 

                    $("#branch").empty();
                    $("#branch").prepend('<option value="0">All Branches</option>');
                    $("#branch").prepend('<option selected="selected" value="">All</option>');
                    $.each(data['branchList'], function (key, branchObj) {
                                
                        $('#branch').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/
        });/*End Change Project Type*/
  });
</script>

<!-- - - - - - - - - - - - view Modal  Data - - - - - - - - -->

<script type="text/javascript">

    $(document).ready(function() { 
        $(document).on('click', '.view-modal', function()  {
            var signatureSetting = $(this).attr('signatureSetting');
            var csrf = "{{csrf_token()}}";
            $("#viewModal").find('.modal-dialog').css('width', '60%');
            $("#viewModal").modal('show');
            $("#VMsignature").val(signatureSetting);

            $.ajax({
                url: './viewSignatureSetting',
                type: 'POST',
                dataType: 'json',
                async: false,
                data: {id:signatureSetting , _token: csrf},
                success: function(data) {
                    $("#VMmodule").val(data['moduleName']);
                    $("#VMgroup").val(data['groupName']);
                    $("#VMcompany").val(data['companyName']);
                    $("#VMproject").val(data['projectName']);
                    $("#VMprojectType").val(data['projectTypeName']);
                    $("#VMbranch").val(data['branchName']);
                    $('#table1 tbody').empty();

                    for(i=0;i<data['employee'].length;i++){
                        
                        $('#table1 tbody').append('<tr><td>'+(i+1)+'</td><td>'+ data['role'][i]+' </td><td>'+ data['employee'][i]+'</td><td>'+ data['signatureRole'][i]+'</td></tr>');
                    }
                },
                error: function(argument) {
                    alert('response error');
                }

            });
        });
    });

    </script>

<!-- get data for Edit -->

<script type="text/javascript">

    $(document).ready(function() { 
        $(document).on('click', '.edit-modal', function(){

            var signatureSetting = $(this).attr('signatureSetting');
            var roleId       = <?php echo json_encode($roleId); ?>;
            var employeeId   = <?php echo json_encode($employeeId); ?>;
            var signatureId  = <?php echo json_encode($signatureId); ?>;
            var csrf = "{{csrf_token()}}";

            $("#EMsignatureSetting").val(signatureSetting);

                $.ajax({
                    url: './getDataSignatureSettingToUpdate',
                    type: 'POST',
                    dataType: 'json',
                    async: false,
                    data: {id:signatureSetting , _token: csrf},
                    success: function(data) {
                        $("#EMmodule").val(data['moduleId']);
                        $("#EMgroup").val(data['groupId']);
                        $("#EMcompany").val(data['companyId']);
                        $("#EMproject").val(data['projectId']);
                        $("#EMprojectType").val(data['projectTypeId']);
                        $("#EMbranch").val(data['branchId']);
                        $("#EMbranch").val(data['branchId']);
                        $("#EMnumOfSignature").val(data['gnrSignatureSetting'].signatureNum);
                        $('#table2 tbody').empty();

                        for(i=0;i< $("#EMnumOfSignature").val();i++){

                            $('#table2 tbody').append('<tr><td>'+(i+1)+'</td><td><select class="roleName form-control"><option value="">Select Role</option></select> </td><td><select class="employeeName form-control"><option>Select</option></select></td><td><select class="signatureRole form-control"><option>Select</option></select></td></tr>');
                        }


        $("#EMbranch").change(function() {
      
            var branchId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './changeGnrUserBranch',
                data: {branchId:branchId,_token: csrf},
                async: false,
                dataType: 'json',
                success: function( data ){
                
                    $(".employeeName").empty();
                    $(".employeeName").append('<option selected="selected" value="">All</option>');

                    $.each(data['employeeName'], function (key, roleObj) {                       
                        $('.employeeName').append("<option value='"+ roleObj.id+"'>"+(roleObj.emp_id+'-'+roleObj.emp_name_english)+"</option>");
                     
                    });
                    $(".roleName").val('');
                },
                error: function(_response){
                    alert("error");
                }
   
            });/*End Ajax*/
   
      });/*End Change employee name*/


        $(document).on('change',".roleName",function(){
            var roleId = $(this).val();
            var branchId = $('#EMbranch').val();
            var csrf = "<?php echo csrf_token(); ?>";
            var thisRole = $(this);
         
            $.ajax({
                type: 'post',
                url: './changeGnrUserRole',
                data: {roleId:roleId,branchId:branchId,_token: csrf},
                async: false,
                dataType: 'json',
                success: function( data ) {
                    thisRole.closest('tr').find(".employeeName").empty();
                    thisRole.closest('tr').find(".employeeName").append('<option selected="selected" value="">All</option>');
   
                    $.each(data['employee'], function (key, roleObj) {                       
                        thisRole.closest('tr').find('.employeeName').append("<option value='"+ roleObj.id+"'>"+(roleObj.emp_id+'-'+roleObj.emp_name_english)+"</option>");
                     
                    });
                },
                error: function(_response){
                   alert("error");
                }
   
            });/*End Ajax*/
   
       });/*End Change employee name*/
  

        $.each(roleId, function(index, roleName) {
            $(".roleName").append("<option value='"+index+"'>"+roleName+"</option>");
        });

        $.each(employeeId, function(index, employeeName) {
            $(".employeeName").append("<option value='"+index+"'>"+employeeName+"</option>");
        });

        $.each(signatureId, function(index, signatureName) {
            $(".signatureRole").append("<option value='"+index+"'>"+signatureName+"</option>");
                           
        }); 

        $.each(data['roleId'], function(index, val) {
                           
            $(".roleName").eq(index).val(val).trigger('change');
        });

        $.each(data['employeeId'], function(index, val) {
                           
            $(".employeeName").eq(index).val(val);
        });
                                  //alert(JSON.stringify(data['signatureRoleId']));
        $.each(data['signatureRoleId'], function(index, val) {
                           
            $(".signatureRole").eq(index).val(val);
        });

        $("#editModal").find('.modal-dialog').css('width', '60%');
        $("#editModal").modal('show');

        $("#EMnumOfSignature").change(function(){
                                 
            $("#EMnumOfSignature option:selected").val();
            $('#table2 tbody').empty();

            for(i=0;i<$("#EMnumOfSignature option:selected").val();i++) {

                $('#table2 tbody').append('<tr><td>'+(i+1)+'</td><td><select class="roleName form-control"><option>Select Role</option></select> </td><td><select class="employeeName form-control"><option>Select</option></select></td><td><select class="signatureRole form-control"><option>Select</option></select></td></tr>');
            }

            $.each(roleId, function(index, roleName) {
                $(".roleName").append("<option value='"+index+"'>"+roleName+"</option>");
                             //alert(roleId);
            });

            $.each(employeeId, function(index, employeeName) {

                $(".employeeName").append("<option value='"+index+"'>"+employeeName+"</option>");
            });

            $.each(signatureId, function(index, signatureName) {
                $(".signatureRole").append("<option value='"+index+"'>"+signatureName+"</option>");
                             
            });

            $.each(data['roleId'], function(index, val) {
                           
                $(".roleName").eq(index).val(val).trigger('change');
            });

            $.each(data['employeeId'], function(index, val) {
                           
                $(".employeeName").eq(index).val(val);
            });

            $.each(data['signatureRoleId'], function(index, val) {
                           
                $(".signatureRole").eq(index).val(val);
            });

            });

        }
        });
    });
});

  </script>

<!--             Update Modal Data         -->


<!-- - - - - - - - -       Edit for view data - - - - - - - - -->

    
 <script type="text/javascript">
  

$(document).ready(function(){ 
    $("#updateButton").on('click', function() {
        $("#updateButton").prop("disabled", true);

        $(".error").remove();
        var roleIds = new Array();    
        var empolyeeIds = new Array();    
        var signatureRoleIds = new Array();
        var id = $("#EMsignatureSetting").val();
        var moduleId = $("#EMmodule").val();
        var groupId = $("#EMgroup").val();
        var companyId= $("#EMcompany").val();
        var projectId = $("#EMproject").val();
        var peojectTypeId = $("#EMprojectType").val();
        var branchId= $("#EMbranch").val();
        var signatureNum = $("#EMnumOfSignature").val();
        var csrf = "{{csrf_token()}}";
          
        for(i=0;i<signatureNum;i++) {

            roleIds.push($(".roleName").eq(i).val());
         }

        for(i=0;i<signatureNum;i++) {
            empolyeeIds.push($(".employeeName").eq(i).val());
        }

        for(i=0;i<(signatureNum);i++) {
            signatureRoleIds.push($(".signatureRole").eq(i).val());
       }

  
        $.ajax({
            url: './signatureSettingInfoupdate',
            type: 'POST',
            dataType: 'json',
            data: {id:id,moduleId:moduleId,groupId:groupId,companyId:companyId,projectId:projectId,peojectTypeId:peojectTypeId,branchId:branchId,roleIds:roleIds,empolyeeIds:empolyeeIds,signatureRoleIds:signatureRoleIds,signatureNum:signatureNum,_token: csrf},
 
        })

        .done(function(data) {
            if (data.errors) {
                $("#updateButton").prop("disabled", false);
                if (data.errors['module']) {
                    $("#modulee").empty();
                    $("#modulee").append('*'+data.errors['module']);
                }

                if (data.errors['group']) {
                    $("#groupe").empty();
                    $("#groupe").append('*'+data.errors['group']);
                }

                if (data.errors['company']) {
                    $("#companye").empty();
                    $("#companye").append('*'+data.errors['company']);
                }

                if (data.errors['project']) {
                    $("#projecte").empty();
                    $("#projecte").append('*'+data.errors['project']);
                }

                if (data.errors['project']) {
                    $("#projecte").empty();
                    $("#projecte").append('*'+data.errors['project']);
                }

                if (data.errors['projectType']) {
                    $("#projectTypee").empty();
                    $("#projectTypee").append('*'+data.errors['projectType']);
                }

                if (data.errors['branch']) {
                    $("#branche").empty();
                    $("#branche").append('*'+data.errors['branch']);
                }

                if (data.errors['numOfSignature']) {
                    $("#numOfSignaturee").empty();
                    $("#numOfSignaturee").append('*'+data.errors['numOfSignature']);
                }
            }
            else {

                location.reload();
            }
            console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
        .always(function() {
            console.log("complete");
        })
         
    });
});

</script> 

<!-- Project Type change -->
 <script type="text/javascript">
    $(document).ready(function(){ 

        function pad (str, max) {
            str = str.toString();

            return str.length < max ? pad("0" + str, max) : str;
        }

        $("#EMproject").change(function(){
            
            var project = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:project,_token: csrf},
                dataType: 'json',
                success: function( data ){

                    $("#EMprojectType").empty();
                    $("#EMprojectType").prepend('<option selected="selected" value="">Select Project Type</option>');
                    $.each(data['projectTypeList'], function (key, projectObj) {
                          $('#EMprojectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");                      
                    });
                },
                error: function(_response){
                    alert("error");
                }

           });/*End Ajax*/

        });/*End Change Project*/
    });
</script>



@include('dataTableScript')
@endsection

