@extends('layouts/gnr_layout')
@section('title', '| User Role')
@section('content')
@include('successMsg')

@php
    $pageNo = isset($_GET['page']) ? (int) $_GET['page']: 1;
    $branchSelected = isset($_GET['filBranch']) ? $_GET['filBranch'] : null;
    $roleSelected = isset($_GET['filRole']) ? $_GET['filRole'] : null;
    $empIdSelected = isset($_GET['filEmpId']) ? $_GET['filEmpId'] : null;
@endphp

<style type="text/css">
    .form-group, .form-control{
            font-size: 11px !important;
            color: black !important;
    }
    .form-control{
            padding: 5px !important;
    }
    #searchButton{
            font-size: 12px;
            margin-top: 20px;
    }
</style>

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
            <div class="panel-heading" style="padding-bottom:0px">
                <div class="panel-options">
                    <a href="{{url('addGnrUserRole/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add User Role</a>
                </div>
                <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">USER ROLE LIST</font></h1>
            </div>
        <div class="panel-body panelBodyView">

            <!-- Filtering Start-->
            {!! Form::open(array('url' => 'viewGnrUserRole', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'get')) !!}
                <div class="row">
                    <div class="col-md-12">

                        @if($userBranchId == 1)

                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="col-md-12">
                                    {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-md-12">
                                    {!! Form::select('filBranch', [''=>'--All--']+$branchList, $branchSelected ,['id'=>'filBranch','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
                                </div>
                            </div>
                        </div>

                        @endif

                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="col-md-12">
                                    {!! Form::label('', 'Role:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-md-12">
                                    {!! Form::select('filRole', [''=>'--All--']+$roleList, $roleSelected ,['id'=>'filRole','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="col-md-12">
                                    {!! Form::label('', 'Emp. ID:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-md-12">
                                    {!! Form::text('filEmpId', $empIdSelected ,['id'=>'filEmpId','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
                                </div>
                            </div>
                        </div>


                        <div class="col-md-1">
                            <div class="form-group">
                                {!! Form::label('', '', ['class' => 'control-label col-md-12']) !!}
                                <div class="col-md-12">
                                    {!! Form::submit('Search', ['id' => 'searchButton', 'class' => 'btn btn-primary btn-xs']); !!}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            {!! Form::close()  !!}
            <!-- filtering end-->

          <table class="table table-striped table-bordered" id="gnrUserRoleView">
            <thead>
                  <tr>
                    <th width="50">SL#</th>
                    <th>User Name</th>
                    <th>Branch</th>
                    <th>Role</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($userRoleLists as $key => $userRole)

                    @php
                        $empId = DB::table('users')->where('id',$userRole->userIdFK)->select('emp_id_fk','username')->first();
                        $employee = DB::table('hr_emp_general_info')->where('id',$empId->emp_id_fk)->select('emp_id','emp_name_english')->first();
                        if ($employee==null) {
                            $employeeName = $empId->username;
                        }
                        else{
                            $employeeName = $employee->emp_id.'-'.$employee->emp_name_english;
                        }
                        $getBranchInfos = DB::table('hr_emp_general_info')
                            ->join('hr_emp_org_info', 'hr_emp_general_info.id', '=', 'hr_emp_org_info.emp_id_fk')
                            ->where('hr_emp_general_info.emp_id', $employee->emp_id)
                            ->pluck('hr_emp_org_info.branch_id_fk')
                            ->toArray();
                        $branchNameInfos = DB::table('gnr_branch')
                            ->where('id', $getBranchInfos[0])
                            ->pluck('name')
                            ->toArray();
                        $branchCodeInfos = DB::table('gnr_branch')
                            ->where('id', $getBranchInfos[0])
                            ->pluck('branchCode')
                            ->toArray();
                        $roleName = DB::table('gnr_role')->select('name')->where('id',$userRole->roleId)->value('name');
                    @endphp

                        <td>{{($pageNo-1)*30+($key+1)}}</td>
                        <td class="name">{{$employeeName}}</td>
                        <td class="name">{{$branchCodeInfos[0]}}-{{$branchNameInfos[0]}}</td>
                        <td class="name">{{$roleName}}</td>


                        <td class="text-center" width="80">
                          <a href="javascript:;" class="edit-modal" userRoleId="{{$userRole->id}}"" userName="{{$employeeName}}">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>&nbsp
                          <a href="javascript:;" class="delete-modal" userRoleId="{{$userRole->id}}">
                            <span class="glyphicon glyphicon-trash"></span>
                          </a>
                        </td>
                      </tr>
                    @endforeach
                </tbody>
          </table>
            <div class="pull-right">
                {{ $userRoleLists->appends(request()->input())->links() }}
            </div>
        </div>
      </div>
  </div>
</div>
</div>
</div>


{{-- Valid Till Modal --}}
        <div id="validTillModal" class="modal fade" style="margin-top:3%" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Validation Period</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-horizontal form-group" style="padding-left: 20px;padding-right: 20px;">
                                <div class="form-group">
                                    {!! Form::label('validTill', 'Valid Till:', ['class' => 'col-sm-2 control-label']) !!}
                                    <div class="col-sm-3">
                                        {!! Form::radio('validType', '1', true) !!}
                                        {!! Form::label('', 'Life Time') !!}
                                    </div>
                                    <div class="col-sm-2">
                                        {!! Form::radio('validType', '2', false) !!}
                                        {!! Form::label('', 'Limited Time') !!}
                                    </div>
                                    <div class="col-sm-3">
                                        {!! Form::text('validTillDate', $value = null, ['class' => 'form-control', 'id' => 'validTillDate','placeholder' => 'Enter Valadation Date','readonly']) !!}
                                    </div>
                                    <div class="col-sm-2">
                                        {!! Form::text('validTillTime', $value = null, ['class' => 'form-control', 'id' => 'validTillTime','placeholder' => 'Enter Valadation Time','readonly']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal-footer">

                            <input type="hidden" name="actionTracker" id="actionTracker" moduleId="" functionId="" subFunctionId="">

                            <button id="modalSubmit" type="submit" class="btn actionBtn glyphicon glyphicon-check btn-success"><span > Confirm</button>
                            <button id="modalClose" class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>


                        </div>

                    </div>
                </div>
            </div>
        </div>
{{-- Valid Till Modal --}}

@php
    $roles = array(''=>'Select Role') + DB::table('gnr_role')->where('id','>=',2)->pluck('name','id')->toArray();
    $modules = DB::table('gnr_module')->select('id','name','code')->get();
    $subFunctions = DB::table('gnr_sub_function')->select('id','subfunctionName')->get();
@endphp

{{-- Edit Modal --}}
<div id="editModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-lg" style="width: 1050px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update User Role</h4>
            </div>
            <div class="modal-body">

                <div class="row" style="padding-bottom: 20px;">


                {!! Form::open(['url'=>'','class'=>'form-horizontal form-groups']) !!}
                    <div class="col-md-12">
                    {!! Form::hidden('EMuserRoleId',null,['id'=>'EMuserRoleId']) !!}

                    <div class="form-group">
                        {!! Form::label('userName', 'User:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('userName',null,['id'=>'userName','class'=>'form-control','readonly']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('roleId', 'Role:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::select('roleId',$roles,null,['id'=>'roleId','class'=>'form-control']) !!}
                        </div>
                    </div>



                            <div class="form-group">
                                {!! Form::label('module', 'Modules:', ['class' => 'col-sm-2 control-label']) !!}

                                <div class="col-sm-10">

                            {{-- /////////// --}}
                            <ul id="navTabs" class="nav nav-tabs">
                                @foreach ($modules as $key => $module1)
                                    <li class="@if($key==0){{"active"}}@endif">
                                        <a href="#{{$module1->code}}" data-toggle="tab" aria-expanded="@if($key==0){{"true"}}@else{{"false"}}@endif">
                                            <span class="visible-xs"><i class="fa-home"></i></span>
                                            <span class="hidden-xs">{{$module1->name}}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content">

                                @foreach ($modules as $index => $module2)
                                    <div class="tab-pane @if($index==0){{"active"}}@endif" id="{{$module2->code}}">
                                        @php
                                            $functions = DB::table('gnr_function')->where('moduleIdFK',$module2->id)->select('id','name')->orderBy('name')->get();
                                        @endphp

                                        {{-- Print Functions --}}
                                        <div>

                                        @foreach($functions as $function)
                                            {!! Form::label($function->name,$function->name) !!} <br>
                                            <div class="form-group" style="padding-left: 20px;">
                                            @foreach($subFunctions as $subFunction)
                                                <span class="checkboxSpan">
                                                {!! Form::checkbox($function->name, $subFunction->id, false,['moduleId'=>$module2->id,'functionId'=>$function->id,'class'=>'checkItem']) !!}
                                                {!! Form::label($subFunction->subfunctionName,$subFunction->subfunctionName) !!}
                                                </span>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endforeach
                                            </div>
                                            <br>
                                        @endforeach
                                        </div>
                                        {{-- End Print Functions --}}

                                    </div>
                                @endforeach
                            </div>
                            </div>
                            </div> {{-- form-group --}}
                            {{-- ///////////// --}}

                            <div class="form-group">
                                {!! Form::label('description', 'Description:', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::textArea('description', $value = null, ['class' => 'form-control', 'id' => 'description','rows'=>'2', 'placeholder' => 'Enter Description']) !!}
                                </div>
                            </div>

            </div> {{-- End col-12 --}}


                {!! Form::close() !!}



                </div>{{--row--}}



                {{-- View ModalFooter--}}
                <div class="modal-footer">

                     <button id="updateButton" class="btn actionBtn glyphicon glyphicon-check btn-success" type="button"><span> Update</span></button>
                    <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span id=""> Close</span></button>
                </div>


            </div> {{-- End View Modal Body--}}

        </div>
    </div>
</div>
{{-- End View Modal --}}

{{-- Delete Modal --}}
  <div id="deleteModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
        </div>
        <div class="modal-body">
          <h2>Are You Confirm to Delete This Record?</h2>

          <div class="modal-footer">
            {!! Form::hidden('DMuserRoleId',null,['id'=>'DMuserRoleId']) !!}
            <button id="DMconfirmButton" type="button" class="btn actionBtn glyphicon glyphicon-check btn-danger"> Confirm</button>
            <button  class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>

          </div>

        </div>
      </div>
    </div>
  </div>
  {{-- End Delete Modal --}}




<style type="text/css">
    table tbody tr td.name{
        text-align: left;
        padding-left: 5px;
    }
    .textBold{
        font-weight: bold;
        font-size: 15px;
    }
</style>






<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.edit-modal', function(event) {

            $("input:checkbox").prop('checked',false);

            var userRoleId = $(this).attr('userRoleId');
            var csrf = "{{csrf_token()}}";

            $("#EMuserRoleId").val(userRoleId);
            $("#userName").val($(this).attr('userName'));

            $.ajax({
                url: './getGnrUserRoleInfo',
                type: 'POST',
                dataType: 'json',
                data: {userRoleId: userRoleId, _token: csrf},
            })
            .done(function(data) {
                //alert(JSON.stringify(data));
                $("#roleId").val(data['roleId']);

                var defaultElements = new Array();

                /*Functionality from Role*/
                $.each(data['roleFuntionalityArray'], function(index, functionality) {
                    var functionality = functionality.split(':');
                    //console.log(functionality[1]);
                    var element = $("input:checkbox[moduleId="+functionality[0]+"][functionId="+functionality[1]+"][value="+functionality[2]+"]");
                    element.prop('checked',true);
                    element.closest('span').find('label').css('color', 'green');

                    var elementIdString = "input:checkbox[moduleId="+functionality[0]+"][functionId="+functionality[1]+"][value="+functionality[2]+"]";
                    defaultElements.push(elementIdString);
                });
                /*End Functionality from Role*/

                /*Functionality from Restricted List*/
                $.each(data['restrictedFunArray'], function(index, functionality) {
                    if (functionality.length>0) {
                        var functionality = functionality.split(':');
                        //console.log(functionality);
                        var element = $("input:checkbox[moduleId="+functionality[0]+"][functionId="+functionality[1]+"][value="+functionality[2]+"]");
                        element.prop('checked',false);
                    }

                });
                /*End Functionality from Restricted List*/


                /*Functionality from Additional List*/
                $.each(data['userAddFunArray'], function(index, functionality) {
                    if (functionality.length>0) {
                        var functionality = functionality.split(':');
                        var element = $("input:checkbox[moduleId="+functionality[0]+"][functionId="+functionality[1]+"][value="+functionality[2]+"]");
                        element.prop('checked',true);


                        var typeNDate = data['userAddFunTypeNDateArray'][index].split('/');
                        element.attr('validtype',typeNDate[0]);
                        element.attr('date',typeNDate[1]);
                        element.attr('time',typeNDate[1]);


                        element.closest('span').find('label').css('color', 'orange');
                    }
                });
                /*End Functionality from Additional List*/


                $("input:checkbox").not(defaultElements.join(',')).click(function(event) {
                    if ($(this).is(":checked")) {
                        $(this).closest('span').find('label').css('color', 'orange');

                        $("#actionTracker").attr('moduleId',$(this).attr('moduleId'));
                        $("#actionTracker").attr('functionId',$(this).attr('functionId'));
                        $("#actionTracker").attr('subFunctionId',$(this).val());

                        $(this).removeAttr('validType');
                        $(this).removeAttr('date');
                        $(this).removeAttr('time');



                        $( "#editModal" ).modal('hide');
                        $("#validTillModal").modal('show');
                        $('#validTillModal').modal({backdrop: 'static', keyboard: false});

                    }
                    else{
                        $(this).closest('span').find('label').css('color', 'black');
                    }

                });


                $("#editModal").modal('show');
            })
            .fail(function() {
                alert("error");
            })
        }); /*End Edit Modal*/


        /*If close the modal uncheck the action*/
        $("#modalClose").click(function(event) {
            var moduleId = $("#actionTracker").attr('moduleId');
            var functionId = $("#actionTracker").attr('functionId');
            var subFunctionId = $("#actionTracker").attr('subFunctionId');

            element = $("input:checkbox[moduleId="+moduleId+"][functionId="+functionId+"][value="+subFunctionId+"]");

            element.prop('checked', false);
            element.closest('span').find('label').css('color','black');
        });

        /*End If close the modal uncheck the action*/

        /*Validation Hour*/
        $("#validTillDate").datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange : "c:c+5",
                minDate: "dateToday",
                dateFormat: 'dd-mm-yy',
                disabled: true
        });

        $('#validTillTime').timepicker({
            'showDuration': true,
            'timeFormat': 'g:ia'
        });
        /*End Validation Date*/

        $("input[name=validType]").trigger('change');
        /*Valid Type Change*/
        $("input[name=validType]").change(function(event) {
            if($(this).val()==2){
                $("#validTillDate").datepicker("option","disabled",false);
                $("#validTillDate").css('cursor', 'pointer');

                $("#validTillTime").css('cursor', 'pointer');
                $("#validTillTime").css('pointer-events', 'auto');
            }
            else{
                $("#validTillDate").datepicker("option","disabled",true);
                $("#validTillDate").css('cursor', 'not-allowed');

                $("#validTillTime").css('cursor', 'no-drop');
                $("#validTillTime").css('pointer-events', 'none');

            }
        });
        /*End Valid Type Change*/

        /*On Click Modal Submit Button add time validation attr of the checkbox*/
        $("#modalSubmit").click(function(event) {
            var validType = $("input[name=validType]:checked").val();
            var date = $("#validTillDate").val();
            var time = $("#validTillTime").val();;
            var moduleId = $("#actionTracker").attr('moduleId');
            var functionId = $("#actionTracker").attr('functionId');
            var subFunctionId = $("#actionTracker").attr('subFunctionId');

            element = $("input:checkbox[moduleId="+moduleId+"][functionId="+functionId+"][value="+subFunctionId+"]");

            if (validType==2) {
                element.attr('validType',2);
                element.attr('date',date);
                element.attr('time',time);
            }
            else{
                element.attr('validType',1);
                element.attr('date','');
                element.attr('time','');
            }

            $("#validTillModal").modal('hide');
        });
        /*End On Click Modal Submit Button add time validation attr of the checkbox*/




        $('#validTillModal').on('hidden.bs.modal', function () {
          $( "#editModal" ).modal('show');
        });







        $(document).on('click', '.delete-modal', function(event) {
            $("#DMuserRoleId").val($(this).attr('userRoleId'));
            $("#deleteModal").modal('show');
        });




        $("#DMconfirmButton").click(function(event) {

            var userRoleId = $("#DMuserRoleId").val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './deleteGnrUserRole',
                type: 'POST',
                dataType: 'json',
                data: {userRoleId: userRoleId, _token: csrf},
            })
            .done(function() {
                location.reload();
            })
            .fail(function() {
                alert("error");
            })


        });


        /*Check/Uncheck All*/
        $(document).on('click', '.checkAll', function(event) {
            if($(this).is(":checked")){
                $(this).closest('div').find('.checkItem').prop('checked',true);
            }
            else{
                $(this).closest('div').find('.checkItem').prop('checked',false);
            }
        });
        /*End Check/Uncheck All*/

        /*If all checked then make the all check true */
        $(document).on('change', '.checkItem', function(event) {
            var numberOfSubFuntions = $(this).closest('div').find('.checkItem').length;
            var numberOfCheckedSubFuntions = $(this).closest('div').find('.checkItem:checked').length;

            if(numberOfSubFuntions == numberOfCheckedSubFuntions){
                $(this).closest('.functionDiv').find('.checkAll').prop('checked',true);
            }
            else{
                $(this).closest('.functionDiv').find('.checkAll').prop('checked',false);
            }
        });
        /*end If all checked then make the all check true */

        $(document).on('change', '.checkItem', function(event) {

            if ($(this).is(":checked")){

                $("#navTabs").closest('div').find('.error').remove();
            }
        });



        /*Submit the data*/
        $("#updateButton").click(function(event) {
            $(".error").remove();

            var userRoleId = $("#EMuserRoleId").val();
            var roleId = $("#roleId").val();
            var description = $("#description").val();

            var moduleIds = new Array();
            var functionIds = new Array();
            var subFunctionIds = new Array();
            var validTypes = new Array();
            var dates = new Array();
            var times = new Array();

            $(".checkItem:checked").each(function(index, el) {
                if ($(el).val()!='on') {
                    moduleIds.push($(el).attr('moduleId'));
                    functionIds.push($(el).attr('functionId'));
                    subFunctionIds.push($(el).val());

                    if($(el).get(0).hasAttribute('validType')){
                        validTypes.push($(el).attr('validType'));
                        dates.push($(el).attr('date'));
                        times.push($(el).attr('time'));
                    }

                }
            });

            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './editGnrUserRole',
                type: 'POST',
                dataType: 'json',
                data: {userRoleId: userRoleId, roleId: roleId, moduleIds: moduleIds, functionIds: functionIds, subFunctionIds: subFunctionIds, validTypes: validTypes, dates: dates, times: times, description: description, _token: csrf},
            })
            .done(function(data) {

                // Print Error
                    if(data.errors) {
                        $.each(data.errors, function(name, error) {
                             $("#"+name).after("<p class='error' style='color:red;'>* "+data.errors[name]+"</p>");
                        });
                    }
                    else{
                        location.href = "viewGnrUserRole";
                    }

            })
            .fail(function() {
                alert('Response Error');
            })

        });
        /*End Submit the data*/



        /*make the selected action on role select*/
        $("#roleId").change(function(event) {
            var roleId = $(this).val();
            $("input:checkbox").prop('checked',false);
            $(".checkboxSpan").find('label').css('color','black');
            $("input:checkbox").unbind("click");

            if(roleId!=''){

                var csrf = "{{csrf_token()}}";

                $.ajax({
                    url: './getGnrRoleInfo',
                    type: 'POST',
                    dataType: 'json',
                    data: {roleId: roleId, _token: csrf},
                })
                .done(function(data) {

                    var functionalityArray = data['functionality'];
                    var moduleIds = new Array();
                    var functionIds = new Array();
                    var subFunctionIds = new Array();

                    var defaultElements = new Array();

                    $.each(functionalityArray, function(index, functionalityString) {
                        var functionality = functionalityString.split(':');
                        moduleIds.push(functionality[0]);
                        functionIds.push(functionality[1]);
                        subFunctionIds.push(functionality[2]);
                    });
                    $.each(moduleIds, function(index, moduleId) {
                        var element = $("input:checkbox[moduleId="+moduleId+"][functionId="+functionIds[index]+"][value="+subFunctionIds[index]+"]");
                        element.prop('checked',true);
                        element.closest('span').find('label').css('color', 'green');

                        var elementIdString = "input:checkbox[moduleId="+moduleId+"][functionId="+functionIds[index]+"][value="+subFunctionIds[index]+"]";
                        defaultElements.push(elementIdString);
                    });



                    $("input:checkbox").not(defaultElements.join(',')).click(function(event) {
                        if ($(this).is(":checked")) {
                            $(this).closest('span').find('label').css('color', 'orange');

                            $("#actionTracker").attr('moduleId',$(this).attr('moduleId'));
                            $("#actionTracker").attr('functionId',$(this).attr('functionId'));
                            $("#actionTracker").attr('subFunctionId',$(this).val());

                            $(this).removeAttr('validType');
                            $(this).removeAttr('date');
                            $(this).removeAttr('time');
                            $("#validTillModal").modal('show');
                            $('#validTillModal').modal({backdrop: 'static', keyboard: false});
                        }
                        else{
                            $(this).closest('span').find('label').css('color', 'black');
                        }

                    });



                }) /*End Success*/
                .fail(function() {
                    alert("response error");
                })



            }
        });
        /*end make the selected action on role select*/


    });/*Ready*/
</script>






@include('dataTableScript')
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
