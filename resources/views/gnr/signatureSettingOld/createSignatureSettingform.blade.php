@extends('layouts/gnr_layout')
@section('title', '|Signature Setting')
@section('content')

<div class="row add-data-form">
    <div class="col-md-2"></div>
    <div class="col-md-8 fullbody">
        <div class="viewTitle" style="border-bottom: 1px solid white;">
            <a href="{{url('SignatureSettingList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Signature Setting List</a>
        </div>
        <div class="panel panel-default panel-border">
            <div class="panel-heading">
                <div class="panel-title">Signature Setting</div>
            </div>
            <div class="panel-body">
                <div class="row"> 
                    <div class="col-md-8">
                        {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                        <div class="form-group">
                            {!! Form::label('module', 'Module Name', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8"> 

                                @php
                                    $modules = DB::table('gnr_module')->select('name','id')->get();
                                @endphp 
                                <select  class="form-control" id='module' name="module">
                                    <option value="">Select module Name</option>
                                    @foreach($modules as $module )
                                        <option  value="{{$module->id}}">{{$module->name}}</option>
                                    @endforeach
                                </select>
                                <p id='moduleeee' class="error" style="max-height:3px;color: red;"></p>

                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('group', 'Group Name', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8"> 

                                @php
                                    $groups = DB::table('gnr_group')->select('name','id')->get();
                                @endphp   
                                <select  class="form-control" id='group' name="group">
                                    <option value="">Select group Name</option>
                                    @foreach($groups as $group )
                                        <option  value="{{$group->id}}">{{$group->name}}</option>
                                    @endforeach
                                </select>
                                    <p id='groupee' class="error" style="max-height:3px;color: red;"></p>

                            </div>
                        </div>
                         
                        <div class="form-group">
                            {!! Form::label('company', 'Company Name', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8"> 
                                @php
                                    $companes = DB::table('gnr_company')->select('name','id')->get();
                                @endphp
                                <select  class="form-control" id='company' name="company">
                                    <option value="">Select company Name</option>
                                    @foreach($companes as $company )
                                        <option  value="{{$company->id}}">{{$company->name}}</option>
                                    @endforeach
                                </select>
                                <p id='companye' class="error" style="max-height:3px;color: red;"></p>

                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('project', 'Project', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8"> 

                                @php
                                    $projects = DB::table('gnr_project')->select('projectCode','name','id')->orderBy('projectCode')->get();
                                @endphp 
                                    <select  class="form-control" id='project' name="project">
                                        <option value="">Select Project Name</option>
                                        @foreach($projects as $project )
                                            <option  value="{{$project->id}}">{{$project->projectCode.'-'.$project->name}}</option>
                                        @endforeach
                                    </select>
                                    <p id='projecte' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                        </div>
                        <div class="form-group">

                            {!! Form::label('projectType', 'Project Type', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8"> 
                                @php
                                    $projectTypes =DB::table('gnr_project_type')->select('projectTypeCode','name','id')->orderBy('projectTypeCode')->get();
                                @endphp
                                <select class="form-control" id="projectType" name="projectType">
                                    <option value="">Slect Project Type</option>
                                    @foreach( $projectTypes as  $projectType)
                                        <option  value="{{$projectType->id}}">{{$projectType->projectTypeCode.'-'.$projectType->name}}</option>
                                    @endforeach
                                </select>

                                <p id='projectTypee' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('branch', 'Branch', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8">
                                @php
                                    $branches =DB::table('gnr_branch')->select('name','id')->get();
                                @endphp 
                                    <select class="form-control" id="branch" name="branch">
                                        <option value="">Select Branch</option>
                                        @foreach($branches as  $branch)
                                            <option  value="{{$branch->id}}">{{$branch->name}}</option>
                                        @endforeach

                                    </select>
                                    <p id='branche' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                        </div>
                        <div class="form-group" style="padding-bottom:15px;">
                            {!! Form::label('numOfSignature', 'No. Of Signature ', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8">                                    
                                <select class="form-control" id="numOfSignature" name="numOfSignature">
                                    <option value="">Select Signature Row</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>

                                <p id='numOfSignaturee' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                        </div> 
                    </div>
                    <table  class="table table-striped table-bordered dataTable no-footer">
                        <thead>
                            <th>Signature</th>
                            <th>Role</th>
                            <th>Employee Name</th>
                            <th>Action</th>
                        </thead> 
                        <tbody>
              
                            <tr id ="signature1">
                           
                                <td>1st </td>
                                <td>
                                    <div>
                                        {!! Form::label('', '', ['class' => 'col-sm-3 control-label']) !!}
                                        <div> 
                                            @php
                                                $roles =DB::table('gnr_role')->select('name','id')->get();
                                            @endphp 
                                            <select class="form-control role1" id="role" name="roleName">
                                                <option value="">Select Role</option>
                                                @foreach($roles as  $roles)
                                                    <option  value="{{$roles->id}}">{{$roles->name}}</option>
                                                @endforeach

                                            </select>
                                            <p id='roleeee' class="error" style="max-height:3px;color: red;"></p>

                                        </div>
                                    </div>
                                </td>
                                <td>
                                
                                    <div>

                                        {!! Form::label('', '', ['class' => 'col-sm-3 control-label']) !!}
                                        <div>
                                            @php
                                                $employees =DB::table('hr_emp_general_info')->select('emp_name_english','id')->get();
                                            @endphp 
                                            <select class="form-control employee1" id="employee" name="employeeName">
                                                <option value="">Select employee</option>
                                                @foreach($employees as  $employee)
                                                    <option  value="{{$employee->id}}">{{$employee->emp_name_english}}</option>
                                                @endforeach

                                            </select>

                                            <p id='employeee' class="error" style="max-height:3px;color: red;"></p>
                                        </div>
                                    </div>
                                </td> 
                                <td>
                                 
                                    <div>

                                        {!! Form::label('', '', ['class' => 'col-sm-3 control-label']) !!}
                                       

                                        <div> 

                                            @php
                                              $signatureRoles =DB::table('gnr_signature_role')->select('name','id')->get();
                                            @endphp 

                                          
                                            <select class="form-control signatureRole1 dpdown" id="signatureRoleName" name="signatureRoleName">
                                                <option value="">Select Signature</option>
                                                @foreach($signatureRoles as  $signatureRole)
                                                    <option  value="{{$signatureRole->id}}">{{$signatureRole->name}}</option>
                                                @endforeach

                                            </select>

                                            <p id='signatureRoleNamee' class="error" style="max-height:3px;color: red;"></p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
    
                            <tr id ="signature2">
                                <td>2nd </td>
                                <td>
                                    <div>
                                        {!! Form::label('', '', ['class' => 'col-sm-3 control-label']) !!}
                                        <div> 
                                            @php
                                                $roles =DB::table('gnr_role')->select('name','id')->get();
                                            @endphp

                                            <select class="form-control role1" id="roleA" name="role1 ">
                                                <option value="">Select Role</option>
                                                @foreach($roles as  $roles)
                                                    <option  value="{{$roles->id}}">{{$roles->name}}</option>
                                                @endforeach
                                            </select>

                                             <p id='roleNamee1' class="error" style="max-height:3px;color: red;"></p>

                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {!! Form::label('', '', ['class' => 'col-sm-3 control-label']) !!}
                                        <div> 

                                            @php
                                                $employees =DB::table('hr_emp_general_info')->select('emp_id','emp_name_english','id')->get();
                                            @endphp 

                                      
                                            <select class="form-control employee1" id="employeeA" name="employee1">
                                                <option value="">Select employee</option>
                                                @foreach($employees as  $employee)
                                                    <option  value="{{$employee->id}}">{{$employee->emp_name_english}}</option>
                                                @endforeach

                                            </select>

                                            <p id='employeee1' class="error" style="max-height:3px;color: red;"></p>
                                        </div>
                                    </div>
                                </td> 
                                <td>
                                    <div>
                                        {!! Form::label('', '', ['class' => 'col-sm-3 control-label']) !!}
                                        <div> 

                                            @php
                                               $signatureRoles =DB::table('gnr_signature_role')->select('name','id')->get();
                                            @endphp 
                                            <select class="form-control signatureRole1 dpdown" id="signatureRoleAD" name="signatureRole1">
                                                <option value="">Select Signature Role</option>
                                                @foreach($signatureRoles as  $signatureRole)
                                                    <option  value="{{$signatureRole->id}}">{{$signatureRole->name}}</option>
                                                @endforeach

                                            </select>

                                            <p id='signatureRolee1' class="error" style="max-height:3px;color: red;"></p>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr id ="signature3">
                                <td>3rd </td>
                                <td>
                                    <div>
                                        {!! Form::label('', '', ['class' => 'col-sm-3 control-label']) !!}
                                        <div> 

                                            @php
                                               $roles =DB::table('gnr_role')->select('name','id')->get();
                                            @endphp 
                                            <select class="form-control role1" id="roleB" name="role2 ">
                                                <option value="">Select Role</option>
                                                @foreach($roles as  $roles)
                                                    <option  value="{{$roles->id}}">{{$roles->name}}</option>
                                                @endforeach

                                            </select>
                                            <p id='roleeB' class="error" style="max-height:3px;color: red;"></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {!! Form::label('', '', ['class' => 'col-sm-3 control-label']) !!}
                                        <div> 

                                            @php
                                                $employees =DB::table('hr_emp_general_info')->select('emp_name_english','id')->get();
                                            @endphp 

                                          
                                            <select class="form-control employee1" id="employeeB" name="employee2">
                                                <option value="">Select employee</option>
                                                @foreach($employees as  $employee)
                                                    <option  value="{{$employee->id}}">{{$employee->emp_name_english}}</option>
                                                @endforeach
                                            </select>

                                            <p id='employeeeB' class="error" style="max-height:3px;color: red;"></p>
                                        </div>
                                    </div>
                                </td> 
                                <td>
                                    <div>
                                        {!! Form::label('', '', ['class' => 'col-sm-3 control-label']) !!}
                                        <div> 
                                            @php
                                              $signatureRoles =DB::table('gnr_signature_role')->select('name','id')->get();
                                            @endphp 
                                            <select class="form-control signatureRole1 dpdown" id="signatureRoleB" name="signatureRole2">
                                                <option value="">Select Signature Role</option>
                                                @foreach($signatureRoles as  $signatureRole)
                                                    <option  value="{{$signatureRole->id}}">{{$signatureRole->name}}</option>
                                                @endforeach

                                            </select>

                                            <p id='signatureRoleeB' class="error" style="max-height:3px;color: red;"></p>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr id="signature4">
                                <td> 4th </td>  
                                <td>
                                    <div>
                                        {!! Form::label('', '', ['class' => 'col-sm-3 control-label']) !!}
                                        <div> 

                                            @php
                                               $roles =DB::table('gnr_role')->select('name','id')->get();
                                            @endphp
                                            <select class="form-control role1" id="roleC" name="role3" >
                                                <option value="">Select Role</option>
                                                @foreach($roles as  $roles)
                                                    <option  value="{{$roles->id}}">{{$roles->name}}</option>
                                                @endforeach

                                            </select>

                                            <p id='roleeC' class="error" style="max-height:3px;color: red;"></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {!! Form::label('', '', ['class' => 'col-sm-3 control-label']) !!}
                                        <div> 

                                            @php
                                              $employees =DB::table('hr_emp_general_info')->select('emp_name_english','id')->get();
                                            @endphp 

                                      
                                            <select class="form-control employee1" id="employeeC" name="employee3">
                                                <option value="">Select employee</option>
                                                @foreach($employees as  $employee)
                                                    <option  value="{{$employee->id}}">{{$employee->emp_name_english}}</option>
                                                @endforeach

                                            </select>

                                            <p id='employeeeC' class="error" style="max-height:3px;color: red;"></p>
                                        </div>
                                    </div>
                                </td> 
                                <td>
                                    <div>

                                        {!! Form::label('', '', ['class' => 'col-sm-3 control-label']) !!}

                                        <div> 

                                            @php
                                              $signatureRoles =DB::table('gnr_signature_role')->select('name','id')->get();
                                            @endphp
                                            <select class="form-control signatureRole1 dpdown" id="signatureRoleC" name="signatureRole3">
                                            <option value="">Select Signature Role</option>
                                                @foreach($signatureRoles as  $signatureRole)
                                                    <option  value="{{$signatureRole->id}}">{{$signatureRole->name}}</option>
                                                 @endforeach
                                            </select>
                                            <p id='signatureRoleeC' class="error" style="max-height:3px;color: red;"></p>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="form-group" style="padding-top:15px;">
                       <div class="col-sm-12 text-right" style="padding-right: 20px;">
                           {!! Form::submit('Submit', ['id' => 'save', 'class' => 'btn btn-info','type'=>'button']) !!}
                           <a href="{{url('SignatureSettingList/')}}" class="btn btn-danger closeBtn">Close</a>
                       </div>

                    </div>

                {!! Form::close() !!}
                           
               </div>
            </div>
        </div>
        <div class="footerTitle" style="border-top:1px solid white"></div>
    </div>
<div class="col-md-2"></div>
</div>
    <script>
        $(document).ready(function() {
            $('#signature1').hide(); 
            $('#signature2').hide();
            $('#signature3').hide();
            $('#signature4').hide();
            $('#numOfSignature').bind("change keyup",function() {
                var signatureOption = $("#numOfSignature option:selected");
                var signatureValue = signatureOption.val();

                if(signatureValue =='') {
                    $('#signature1').hide();
                    $('#signature2').hide();
                    $('#signature3').hide();
                    $('#signature4').hide();
                }
                else if(signatureValue =='1') {
                    $('#signature1').show();
                    $('#signature2').hide();
                    $('#signature3').hide();
                    $('#signature4').hide();
                }
                else if(signatureValue =='2'){
                    $('#signature1').show(); 
                    $('#signature2').show();
                    $('#signature3').hide();
                    $('#signature4').hide();
                } 
                else if(signatureValue =='3'){
                    $('#signature1').show(); 
                    $('#signature2').show();
                    $('#signature3').show();
                    $('#signature4').hide();
                }  
                else if(signatureValue =='4'){
                    $('#signature1').show(); 
                    $('#signature2').show();
                    $('#signature3').show();
                    $('#signature4').show();
                }  
            });
        });
    </script>

    <script type="text/javascript">
        var $selects = $(".dpdown").change(function() {
            var selectedValues = [];
            $.each($selects, function(index, select) {
                var value = $(select).val();
                if (value != "Select") {
                    selectedValues.push(value);
                }  
            });
            $selects
            .find("option")
            .prop("disabled", false);
            $.each(selectedValues, function(index, value) {
                $selects
                .find("option[value='" + value +"']")
                .not(":selected")
                .prop("disabled", true);
            });
        });
    </script>

 <!--  Change Employee Name -->
    <script type="text/javascript">
        $(document).ready(function(){
            $("#branch").change(function(){
                var branchId = $(this).val();
                var csrf = "<?php echo csrf_token(); ?>";
                $.ajax({
                    type: 'post',
                    url: './changeGnrUserBranch',
                    data: {branchId:branchId,_token: csrf},
                    async: false,
                    dataType: 'json',
                    success: function( data ){
                        $("#employee").empty();
                        $("#employee").prepend('<option selected="selected" value="">All</option>');
                        $.each(data['employeeName'], function (key, branchObj) {
                            $('#employee').append("<option value='"+ branchObj.id+"'>"+(branchObj.emp_id+'-'+branchObj.emp_name_english)+"</option>");
                        });
                        $("#role").val('');
                    },
                    error: function(_response){
                        alert("error");
                    }
                });/*End Ajax*/
             });/*End Change employee name*/
        });
    </script> 


     <!--  Change Employee Name -->
    <script type="text/javascript">
        $(document).ready(function(){
            $("#role").change(function(){
                var roleId = $(this).val();
                var branchId = $('#branch').val();
                var csrf = "<?php echo csrf_token(); ?>";
                $.ajax({
                    type: 'post',
                    url: './changeGnrUserRole',
                    data: {roleId:roleId,branchId:branchId,_token: csrf},
                    async: false,
                    dataType: 'json',
                    success: function( data ){
                        $("#employee").empty();
                        $("#employee").prepend('<option selected="selected" value="">All</option>');

                        $.each(data['employee'], function (key, branchObj) {                       
                            $('#employee').append("<option value='"+ branchObj.id+"'>"+(branchObj.emp_id+'-'+branchObj.emp_name_english)+"</option>");
                        });
                    },
                    error: function(_response){
                        alert("error");
                    }
                });/*End Ajax*/
             });/*End Change employee name*/
        });
    </script> 

     </script>

 <!--  Change Employee Name -->
    <script type="text/javascript">
        $(document).ready(function(){
            $("#branch").change(function(){
                var branchId = $(this).val();
                var csrf = "<?php echo csrf_token(); ?>";
                $.ajax({
                    type: 'post',
                    url: './changeGnrUserBranch',
                    data: {branchId:branchId,_token: csrf},
                    async: false,
                    dataType: 'json',
                    success: function( data ){
                        $("#employeeA").empty();
                        $("#employeeA").prepend('<option selected="selected" value="">All</option>');
                        $.each(data['employeeName'], function (key, branchObj) {
                            $('#employeeA').append("<option value='"+ branchObj.id+"'>"+(branchObj.emp_id+'-'+branchObj.emp_name_english)+"</option>");
                        });
                        $("#roleA").val('');
                    },
                    error: function(_response){
                        alert("error");
                    }
                });/*End Ajax*/
             });/*End Change employee name*/
        });
    </script> 


     <!--  Change Employee Name -->
    <script type="text/javascript">
        $(document).ready(function(){
            $("#roleA").change(function(){
                var roleId = $(this).val();
                var branchId = $('#branch').val();
                var csrf = "<?php echo csrf_token(); ?>";
                $.ajax({
                    type: 'post',
                    url: './changeGnrUserRole',
                    data: {roleId:roleId,branchId:branchId,_token: csrf},
                    async: false,
                    dataType: 'json',
                    success: function( data ){
                        $("#employeeA").empty();
                        $("#employeeA").prepend('<option selected="selected" value="">All</option>');
                        $.each(data['employee'], function (key, branchObj) {                       
                            $('#employeeA').append("<option value='"+ branchObj.id+"'>"+(branchObj.emp_id+'-'+branchObj.emp_name_english)+"</option>");
                        });
                    },
                    error: function(_response){
                        alert("error");
                    }
                });/*End Ajax*/
             });/*End Change employee name*/
        });
    </script> 
 <!--  Change Employee Name -->
    <script type="text/javascript">
        $(document).ready(function(){
            $("#branch").change(function(){
                var branchId = $(this).val();
                var csrf = "<?php echo csrf_token(); ?>";
                $.ajax({
                    type: 'post',
                    url: './changeGnrUserBranch',
                    data: {branchId:branchId,_token: csrf},
                    async: false,
                    dataType: 'json',
                    success: function( data ){
                        $("#employeeB").empty();
                        $("#employeeB").prepend('<option selected="selected" value="">All</option>');
                        $.each(data['employeeName'], function (key, branchObj) {
                            $('#employeeB').append("<option value='"+ branchObj.id+"'>"+(branchObj.emp_id+'-'+branchObj.emp_name_english)+"</option>");
                        });
                        $("#roleB").val('');
      
                    },
                    error: function(_response){
                        alert("error");
                    }
                });/*End Ajax*/
             });/*End Change employee name*/
        });
    </script> 
     <!--  Change Employee Name -->
    <script type="text/javascript">
        $(document).ready(function(){
            $("#roleB").change(function(){
                var roleId = $(this).val();
                var branchId = $('#branch').val();
                var csrf = "<?php echo csrf_token(); ?>";
                $.ajax({
                    type: 'post',
                    url: './changeGnrUserRole',
                    data: {roleId:roleId,branchId:branchId,_token: csrf},
                    async: false,
                    dataType: 'json',
                    success: function( data ){
                        $("#employeeB").empty();
                        $("#employeeB").prepend('<option selected="selected" value="">All</option>');
                        $.each(data['employee'], function (key, branchObj) {                       
                            $('#employeeB').append("<option value='"+ branchObj.id+"'>"+(branchObj.emp_id+'-'+branchObj.emp_name_english)+"</option>");
                        });
      
                    },
                    error: function(_response){
                        alert("error");
                    }
                });/*End Ajax*/
             });/*End Change employee name*/
        });
    </script> 


     <!--  Change Employee Name -->
    <script type="text/javascript">
        $(document).ready(function(){
            $("#branch").change(function(){
                var branchId = $(this).val();
                var csrf = "<?php echo csrf_token(); ?>";
                $.ajax({
                    type: 'post',
                    url: './changeGnrUserBranch',
                    data: {branchId:branchId,_token: csrf},
                    async: false,
                    dataType: 'json',
                    success: function( data ){
                        $("#employeeC").empty();
                        $("#employeeC").prepend('<option selected="selected" value="">All</option>');
                        $.each(data['employeeName'], function (key, branchObj) {
                            $('#employeeC').append("<option value='"+ branchObj.id+"'>"+(branchObj.emp_id+'-'+branchObj.emp_name_english)+"</option>");
                        });
                        $("#roleC").val('');
      
                    },
                    error: function(_response){
                        alert("error");
                    }
                });/*End Ajax*/
             });/*End Change employee name*/
        });
    </script>
  <!--  Change Employee Name -->
    <script type="text/javascript">
        $(document).ready(function(){
            $("#roleC").change(function(){
                var roleId = $(this).val();
                var branchId = $('#branch').val();
                var csrf = "<?php echo csrf_token(); ?>";
                $.ajax({
                    type: 'post',
                    url: './changeGnrUserRole',
                    data: {roleId:roleId,branchId:branchId,_token: csrf},
                    async: false,
                    dataType: 'json',
                    success: function( data ){
                        $("#employeeC").empty();
                        $("#employeeC").prepend('<option selected="selected" value="">All</option>');
                        $.each(data['employee'], function (key, branchObj) {                       
                            $('#employeeC').append("<option value='"+ branchObj.id+"'>"+(branchObj.emp_id+'-'+branchObj.emp_name_english)+"</option>");
                        });
      
                    },
                    error: function(_response){
                        alert("error");
                    }
                });/*End Ajax*/
            });/*End Change employee name*/

        });
    </script>

    
<script type="text/javascript">
  /*Store Information*/
    $('form').submit(function(event) {
        event.preventDefault();
        $("#save").prop("disabled", true);
        var roleIds = new Array();    
        var empolyeeIds = new Array();    
        var signatureRoleIds = new Array(); 
        var numberOfSignature = $('#numOfSignature').val();
        for(i=0;i<numberOfSignature;i++){
            roleIds.push($(".role1").eq(i).val());
        }
        for(i=0;i<numberOfSignature;i++){
            empolyeeIds.push($(".employee1").eq(i).val());
        }
        for(i=0;i<numberOfSignature;i++){
            signatureRoleIds.push($(".signatureRole1").eq(i).val());
        }
        var module = $("#module").val();
        var group = $("#group").val();
        var company = $("#company").val();
        var project = $("#project").val();
        var projectType = $("#projectType").val();
        var branch = $("#branch").val();
        var numOfSignature = $("#numOfSignature").val();
        var roleName = $("#role").val();
        var employeeName = $("#employee").val();
        var signatureRoleName = $("#signatureRoleName").val();
        var role1 = $("#roleA").val();
        var employee1 = $("#employeeA").val();
        var signatureRole1= $("#signatureRoleAD").val();
        var role2 = $("#roleB").val();
        var employee2 = $("#employeeB").val();
        var signatureRole2= $("#signatureRoleB").val();
        var role3 = $("#roleC").val();
        var employee3 = $("#employeeC").val();
        var signatureRole3= $("#signatureRoleC").val();
    
        var csrf = "{{csrf_token()}}";
            $.ajax({
                url: './storeSignatureSetting',
                type: 'POST',
                dataType: 'json',
                data:{module: module,group: group,company:company,project:project,projectType:projectType,branch:branch,numOfSignature:numOfSignature, roleIds: roleIds, empolyeeIds: empolyeeIds, signatureRoleIds:signatureRoleIds,roleName:roleName,employeeName:employeeName,signatureRoleName:signatureRoleName,role1:role1,employee1:employee1,signatureRole1:signatureRole1,role2:role2,employee2:employee2,signatureRole2:signatureRole2,role3:role3,employee3:employee3,signatureRole3:signatureRole3,_token:csrf},

                })
                .done(function(data) {
                    if(data.errors)  {
                        if (data.errors['module']) {
                            $("#moduleeee").empty();
                            $("#moduleeee").append(data.errors['module']);
                        } 
                        if (data.errors['group']) { 
                            $("#groupee").empty();
                            $("#groupee").append('*'+data.errors['group']);
                        }
                        if (data.errors['company']) { 
                            $("#companye").empty();
                            $("#companye").append('*'+data.errors['company']);
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
                 
                        if (data.errors['roleName']) { 
                            $("#roleeee").empty();
                            $("#roleeee").append('*'+data.errors['roleName']);
                        } 
                        if (data.errors['employeeName']) { 
                            $("#employeee").empty();
                            $("#employeee").append('*'+data.errors['employeeName']);
                        }  
                        if (data.errors['signatureRoleName']) { 
                            $("#signatureRoleNamee").empty();
                            $("#signatureRoleNamee").append('*'+data.errors['signatureRoleName']);
                        } 
                        if (data.errors['role1']) { 
                            $("#roleNamee1").empty();
                            $("#roleNamee1").append('*'+data.errors['role1']);
                        }
                        if (data.errors['employee1']) { 
                            $("#employeee1").empty();
                            $("#employeee1").append('*'+data.errors['employee1']);
                        }  
                        if (data.errors['signatureRole1']) { 
                            $("#signatureRolee1").empty();
                            $("#signatureRolee1").append('*'+data.errors['signatureRole1']);
                        } 
                        if (data.errors['role2']) { 
                            $("#roleeB").empty();
                            $("#roleeB").append('*'+data.errors['role2']);
                        } 
                        if (data.errors['employee2']) { 
                            $("#employeeeB").empty();
                            $("#employeeeB").append('*'+data.errors['employee2']);
                        }  
                        if (data.errors['signatureRole2']) { 
                            $("#signatureRoleeB").empty();
                            $("#signatureRoleeB").append('*'+data.errors['signatureRole2']);
                        } 

                        if (data.errors['role3']) { 
                            $("#roleeC").empty();
                            $("#roleeC").append('*'+data.errors['role3']);
                        } 
                        if (data.errors['employee3']) { 
                            $("#employeeeC").empty();
                            $("#employeeeC").append('*'+data.errors['employee3']);
                        }  
                        if (data.errors['signatureRole3']) { 
                            $("#signatureRoleeC").empty();
                            $("#signatureRoleeC").append('*'+data.errors['signatureRole3']);
                        } 
                    }
                    else {
                        location.href = 'SignatureSettingList';
                    }
                });

            });
 
    </script>

 <!-- Project  Change  -->

    <script type="text/javascript">
        $(document).ready(function(){ 
            function pad (str, max) {
                str = str.toString();
                return str.length < max ? pad("0" + str, max) : str;
            }
            $("#project").change(function() {
                var project = $(this).val();
                var csrf = "<?php echo csrf_token(); ?>";
                $.ajax({
                    type: 'post',
                    url: './famsAddProductOnChangeProject',
                    data: {projectId:project,_token: csrf},
                    dataType: 'json',
                    success: function( data ) {
                        $("#projectType").empty();
                        $("#projectType").prepend('<option selected="selected" value="">Select Project Type</option>');
                        $.each(data['projectTypeList'], function (key, projectObj) {
                            $('#projectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");                      
                        });
                    },
                    error: function(_response){
                        alert("error");
                    }
                });/*End Ajax*/
            });/*End Change Project*/
        });
    </script>

<!-- Branch Change  -->

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
                    data: {projectId:projectId,projectTypeId: projectTypeId,_token: csrf},
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

@endsection




