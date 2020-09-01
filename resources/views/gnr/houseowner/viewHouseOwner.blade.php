@extends('layouts/gnr_layout')
@section('title', '| House Owner Info')
@section('content')
@php
  use App\gnr\GnrBranch;
@endphp


@php
  $foreignHouseOwnerIds = DB::table('acc_adv_register')->distinct()->pluck('houseOwnerId')->toArray();
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                            <a href="{{url('createHouseOwnerFrom/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add House Owner</a>
                        </div>
                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">HOUSE OWNER LIST</font></h1>
                    </div>
                    <div class="panel-body panelBodyView">       

                    <div>
                        <script type="text/javascript">
                            jQuery(document).ready(function($)  {
                                $("#bankTable").dataTable({ 
                                    "oLanguage": {
                                        "sEmptyTable": "No Records Available",
                                        "sLengthMenu": "Show _MENU_ "
                                    }
                                });  
                            });
                        </script>
                    </div>
                    <table class="table table-striped table-bordered" id="bankTable" style="color:black;">
                        <thead>
                            <tr>
                                <th width="30">SL#</th>
                                <th>Name</th>
                                <th>Project Name</th>
                                <th>Branch Name</th>
                                <th>Phone Number</th>
                                <th>Email Address</th>
                                <th>Bank Account Info</th>
                                <th>House Address</th>
                                <th>Action</th>
                            </tr>
                                      
                        </thead>
                        <tbody>
                            @foreach ($gnrHouseOwner as $index => $gnrHouseOwner)

                                @php
                                    $projectName = DB::table('gnr_project')->where('id',$gnrHouseOwner->projectId)->value('name');
                                    $branchName = DB::table('gnr_branch')->where('id',$gnrHouseOwner->branchId)->value('name');
                                @endphp

                                <tr style:"float:left;">
                                    <td>{{$index+1}}</td>
                                    <td style="text-align:left;padding-left:2px";>{{$gnrHouseOwner->houseOwnerName}}</td>
                                    <td style="text-align:left;padding-left:2px";>{{$projectName}}</td>
                                    <td style="text-align:left;padding-left:2px";>{{$branchName}}</td>
                                    <td>{{$gnrHouseOwner->phoneNumber}}</td>
                                    <td>{{$gnrHouseOwner->emailAddress}}</td>
                                    <td>{{$gnrHouseOwner->bankAccount}}</td>
                                    <td>{{$gnrHouseOwner->houseAddress}}</td>
                                    <td width="80">

                                        <a href="javascript:;" class="view-modal" houseOwnerId="{{$gnrHouseOwner->id}}">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>&nbsp; 

                                        <a href="javascript:;" class="edit-modal" houseOwnerId="{{ $gnrHouseOwner->id}}" registerTypeName="">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>&nbsp;

                                        @php
                                            if (in_array($gnrHouseOwner->id, $foreignHouseOwnerIds)) {
                                                $canDelete = 0;
                                            }
                                            else{
                                                $canDelete = 1;
                                            }   
                                        @endphp

                                        <a href="javascript:;" class="delete-modal" houseOwnerId="{{ $gnrHouseOwner->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
                                            <span class="glyphicon glyphicon-trash"></span>
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
{{-- View Modal --}} 
 <div id="viewModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">View House Owner Info</h4>
            </div>
            <div class="modal-body">
                <input id="VMhouseOwnerId" type="hidden" name="advRegTypeId" value="">
                <div class="row" style="padding-bottom: 20px;">
                    <div class="col-md-8">
                        <div class="col-md-12" style="padding-right:2%;">
                            <div class="form-horizontal form-groups">
                                <div class="form-group">
                                    {!! Form::label('houseOwnerName','House Owner Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8">                                               
                                        {!! Form::text('houseOwnerName', null,['id'=>'VMhouseOwnerName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                        <p id='houseOwnerName' class="error" style=color: red;"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="padding-left:2%;">  </div> 

                        <div class="col-md-12" style="padding-right:2%;">
                            <div class="form-horizontal form-groups">
                                <div class="form-group">
                                    {!! Form::label('projectName', 'Project Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8">
                                        {!! Form::text('projectName', null,['id'=>'VMprojectName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}

                                        <p id='projectName' class="error" style="color: red;"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="padding-left:2%;"></div> 

                        <div class="col-md-12" style="padding-right:2%;">
                            <div class="form-horizontal form-groups">
                                <div class="form-group">
                                    {!! Form::label('branchName', 'Branch Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>

                                    <div class="col-sm-8"> 
                                        {!! Form::text('branchName', null,['id'=>'VMbranchName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                        <p id='branchName' class="error" style="color: red;"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="padding-left:2%;"> </div>
                        <div class="col-md-12" style="padding-right:2%;">
                            <div class="form-horizontal form-groups">
                               <div class="form-group">
                                    {!! Form::label('phoneNumber', 'Mobile Number', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8">                        
                                        {!! Form::text('phoneNumber', null,['id'=>'VMphoneNumber','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                        <p id='phoneNumber' class="error" style="color: red;"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="padding-left:2%;"> </div>

                            <div class="col-md-12" style="padding-right:2%;">
                                <div class="form-horizontal form-groups">
                                    <div class="form-group">
                                        {!! Form::label('emailAddress', 'Email Address', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-1 control-label">: </div>
                                        <div class="col-sm-8">                                               
                                            {!! Form::text('emailAddress', null,['id'=>'VMemailAddress','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            <p id='emailAddress' class="error" style="color: red;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding-left:2%;"> </div>

                            <div class="col-md-12" style="padding-right:2%;">
                                <div class="form-horizontal form-groups">
                                    <div class="form-group">
                                        {!! Form::label('bankAccount', 'Bank Account Info', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-1 control-label">: </div>
                                        <div class="col-sm-8">                                               
                                            {!! Form::textArea('bankAccount', null,['id'=>'VMbankAccount','class'=>'form-control', 'rows'=>'2','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            <p id='bankAccount' class="error" style="color: red;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2" style="padding-left:2%;"> </div>

                            <div class="col-md-12" style="padding-right:2%;">
                                <div class="form-horizontal form-groups">
                                    <div class="form-group">
                                        {!! Form::label('branchAddress', 'House Address', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-1 control-label">: </div>
                                        <div class="col-sm-8">                       
                                            {!! Form::textArea('branchAddress', null,['id'=>'VMhouseAddress','class'=>'form-control','rows'=>'4','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            <p id='branchAddressE' class="error" style=color: red;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding-left:2%;"> </div> 
               
                        </div>
                            <div class="col-md-4 emptySpace vert-offset-top-0">
                                <img src="images/catalog/image15.png" width="80%" height="" style="float:right">
                            </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>

                </div>
            </div>
        </div>
    </div>
   
{{-- Edit Modal--}}
 <div id="editModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update House Owner Register</h4>
            </div>
            <div class="modal-body">
                <div class="row" style="padding-bottom: 20px;">
                    <div class="col-md-12">
                        <div class="col-md-12" style="padding-right:2%;">
                            <div class="form-horizontal form-groups">
                                <input id="EMhouseOwnerId" type="hidden" name="advRegTypeId" value="">
                                <div class="form-group">
                                    {!! Form::label('houseOwnerName','House Owner Name', ['class' => 'col-sm-3 control-label']) !!}

                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8">                        
                                        {!! Form::text('houseOwnerName', null,['id'=>'EMhouseOwnerName','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}

                                        <p id='houseOwnerNameE' class="error" style=color: red;"></p>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="padding-left:2%;"></div> 
                         
                        <div class="col-md-12" style="padding-right:2%;">
                            <div class="form-horizontal form-groups">
                                <div class="form-group">
                                    {!! Form::label('projectName', 'Project Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8">                        
                                        @php

                                           $projects = DB::table('gnr_project')->select('id','name','projectCode')->get();

                                        @endphp

                                        <select name="projectName" class="form-control input-sm" id="EMprojectName">

                                            <option value="">Select Project</option>                                         
                                            @foreach($projects as $project)

                                                <option value="{{$project->id}}"  @if($project->id==1){{"selected=selected"}}@endif>{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT).'-'.$project->name}}</option>

                                            @endforeach
                                        </select>     
                                        <p id='projectNameE' class="error" style="color: red;"></p>
                                    </div>
                                </div>     
                            </div>
                        </div>

                        <div class="col-md-6" style="padding-left:2%;"></div>
                        <div class="col-md-12" style="padding-right:2%;">
                            <div class="form-horizontal form-groups">
                                <div class="form-group">
                                    {!! Form::label('branchName', 'Branch Name', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>

                                    <div class="col-sm-8">       
                                        @php
                                            $branches = DB::table('gnr_branch')->select('id','name','branchCode')->get();
                                        @endphp
                                        <select name="branchName" class="form-control input-sm" id="EMbranchName">
                                            <option value="">Select Branch</option>                                         
                                            @foreach($branches as $branch)
                                                <option value="{{$branch->id}}" @if($branch->id==1){{"selected=selected"}}@endif>{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT).'-'.$branch->name}}</option>
                                            @endforeach
                                        </select>

                                          <p id='branchNameE' class="error" style="color: red;"></p>
                                    </div>
                                </div>     
                            </div>
                        </div>

                        <div class="col-md-6" style="padding-left:2%;"> </div>
                        <div class="col-md-12" style="padding-right:2%;">
                            <div class="form-horizontal form-groups">
                                <div class="form-group">

                                    {!! Form::label('phoneNumber', 'Phone Number', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8">                                               
                                        {!! Form::text('phoneNumber', null,['id'=>'EMphoneNumber','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                        <p id='phoneNumberE' class="error" style="color: red;"></p>
                                    </div>
                                </div>
                               
                            </div>
                        </div>

                        <div class="col-md-6" style="padding-left:2%;">  </div> 
                         
                        <div class="col-md-12" style="padding-right:2%;">
                            <div class="form-horizontal form-groups">
                                <div class="form-group">

                                    {!! Form::label('emailAddress', 'Email Address', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>

                                    <div class="col-sm-8">                                               
                                        {!! Form::text('emailAddress', null,['id'=>'EMemailAddress','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                        <p id='emailAddressE' class="error" style="color: red;"></p>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" style="padding-left:2%;">  </div> 
                        <div class="col-md-12" style="padding-right:2%;">
                            <div class="form-horizontal form-groups">
                                <div class="form-group">
                                    {!! Form::label('branchAddress', 'House Address', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8">
                                        {!! Form::textArea('branchAddress', null,['id'=>'EMhouseAddress','class'=>'form-control','rows'=>'4','type' => 'text','autocomplete'=>'off']) !!}
                                        <p id='branchAddressE' class="error" style=color: red;"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="padding-left:2%;">  </div> 
                         
                        <div class="col-md-12" style="padding-right:2%;">
                            <div class="form-horizontal form-groups">
                                <div class="form-group">
                                    {!! Form::label('bankAccount', 'Bank Account Info', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8">                                               
                                        {!! Form::textArea('bankAccount', null,['id'=>'EMbankAccount','class'=>'form-control', 'rows'=>'2','type' => 'text','autocomplete'=>'off']) !!}
                                        <p id='bankAccountE' class="error" style="color:red;"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="padding-left:2%;"> </div> 
                    </div>
                </div>
            </div>
                    <div class="modal-footer">

                        <input id="DMhouseOwnerId" type="hidden" name="advRegType" value="">
                        <button type="button" id="updateButton" class="btn btn-success"><span class="glyphicon glyphicon-edit" style="padding-right:4px;"></span>Update</button>

                        <button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--                             Delete Model  
                               -->
<div id="deleteModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Delete House Owner </h4>
            </div>
            <div class="modal-body">
                <div class="row" style="padding-bottom: 20px;">
                    <div class="col-md-12">
                        <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                            <div class="form-horizontal form-groups">
                                <input id="DMadvRegTypeId" type="hidden" name="advRegTypeId" value="">
                                 <h2>Are You Confirm to Delete This Record?</h2>
                            </div>
                        </div>
                       <div class="col-md-6" style="padding-left:2%;"></div> 
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input id="DMhouseOwnerId" type="hidden" name="houseOwnerReg" value="">
                <button type="button" class="btn btn-danger"  id="DMhouseOwner"  data-dismiss="modal">confrime</button>

                <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
            </div>

        </div>
    </div>
</div>
   
{{--end delete modal--}}
    <script>
        $(document).ready(function(){ 
            $(document).on('click', '.delete-modal', function(){
                $("#DMhouseOwnerId").val($(this).attr('houseOwnerId'));
                $('#deleteModal').modal('show');
            });

            $("#DMhouseOwner").on('click',  function() {
                var houseOwnerId= $("#DMhouseOwnerId").val();
                var csrf = "{{csrf_token()}}";

                $.ajax({
                    url: './deletehouseOwner',
                    type: 'POST',
                    dataType: 'json',
                    data: {id:houseOwnerId, _token:csrf},
                })

                .done(function(data) {
                    location.reload();

                })
                .fail(function() {
                    console.log("error");

                })
                .always(function() {
                     console.log("complete");
                });
            });   
        }); 
    </script>

<!--                            -Edit  Modal          -->
    <script type="text/javascript">
        $(document).ready(function(){ 
            $(document).on('click', '.edit-modal', function() {
                $("#editModal").find('.modal-dialog').css('width', '57%');
                $("#editModal").modal('show');
                var houseOwnerId = $(this).attr('houseOwnerId');
                var csrf = "{{csrf_token()}}";
                $("#EMhouseOwnerId").val(houseOwnerId);
         
                $.ajax({
                    url: './getHouseOwnerInfo',
                    type: 'POST',
                    dataType: 'json',
                    data: {id:houseOwnerId , _token: csrf},
                    success: function(data) {
                        $("#EMhouseOwnerName").val(data['gnrHouseOwner'].houseOwnerName);
                        $("#EMprojectName").val(data['gnrHouseOwner'].projectId,);
                        $("#EMbranchName").val(data['gnrHouseOwner'].branchId);
                        $("#EMphoneNumber").val(data['gnrHouseOwner'].phoneNumber);
                        $("#EMemailAddress").val(data['gnrHouseOwner'].emailAddress);
                        $("#EMhouseAddress").val(data['gnrHouseOwner'].houseAddress);
                        $("#EMbankAccount").val(data['gnrHouseOwner'].bankAccount);
                    },
                    error: function(argument) {
                        alert('response error');
                    }
                });
            });
        });

    </script>

    <script>
     /* Change Project*/

        $(document).ready(function() {
            function pad (str, max) {
                str = str.toString();
                return str.length < max ? pad("0" + str, max) : str;
            }

            $("#EMprojectName").change(function(){
               
                var projectId = $(this).val();
                var csrf = "<?php echo csrf_token(); ?>";

                $.ajax({
                    type: 'post',
                    url: './famsAddProductOnChangeProject',
                    data: {projectId:projectId,_token: csrf},
                    dataType: 'json',
                    success: function( data ){
                        $("#EMbranchName").empty();
                        $("#EMbranchName").prepend('<option selected="selected" value="">Select Branch </option>');
                        $.each(data['branchList'], function (key, branchObj) {

                            if (branchObj.id==1){
                                $('#EMbranchName').append("<option value='"+ branchObj.id+"'selected='selected'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                                
                            }

                            else {
                                $('#EMbranchName').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>"); 
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

    <script type="text/javascript">
        $(document).ready(function(){ 
            $("#updateButton").on('click', function() {
                $("#updateButton").prop("disabled", true);

                var houseOwnerId = $("#EMhouseOwnerId").val();
                var houseOwnerName= $("#EMhouseOwnerName").val();
                var projectName = $("#EMprojectName").val();
                var branchName = $("#EMbranchName").val();
                var phoneNumber = $("#EMphoneNumber").val();
                var emailAddress= $("#EMemailAddress").val();
                var houseAddress = $("#EMhouseAddress").val();
                var bankAccount = $("#EMbankAccount").val();
                var csrf = "{{csrf_token()}}";

                $.ajax({
                    url: './updateHouseOwnerInfo',
                    type: 'POST',
                    dataType: 'json',
                    data: {houseOwnerId:houseOwnerId,houseOwnerName:houseOwnerName,projectName:projectName,branchName:branchName,phoneNumber: phoneNumber,emailAddress:emailAddress,houseAddress: houseAddress,bankAccount:bankAccount, _token: csrf},
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
                        location.reload();
                    }
                    console.log("success");
                   })
                .fail(function() {
                    console.log("error");
                })

                .always(function()
                 {
                    console.log("complete");
                })
            });
        });

    </script>
        <!-- - - - - - - - - - - -    View  Modal - - - - - - - - - - - -->
    <script type="text/javascript">

        $(document).ready(function(){ 
            $(document).on('click', '.view-modal', function() {
                var houseOwnerId = $(this).attr('houseOwnerId');
                var csrf = "{{csrf_token()}}";
                $("#viewModal").find('.modal-dialog').css('width', '57%');
                $("#viewModal").modal('show');
                $("#VMhouseOwnerId").val(houseOwnerId);
                $.ajax({
                    url: './getHouseOwnerdata',
                    type: 'POST',
                    dataType: 'json',
                    data: {id:houseOwnerId , _token: csrf},
                    success: function(data) {
                        $("#VMhouseOwnerName").val(data['gnrHouseOwner'].houseOwnerName);
                        $("#VMprojectName").val(data['projectName']);
                        $("#VMbranchName").val(data['branchName']);
                        $("#VMphoneNumber").val(data['gnrHouseOwner'].phoneNumber);
                        $("#VMemailAddress").val(data['gnrHouseOwner'].emailAddress);
                        $("#VMhouseAddress").val(data['gnrHouseOwner'].houseAddress);
                        $("#VMbankAccount").val(data['gnrHouseOwner'].bankAccount);
                    },

                    error: function(argument) {
                        alert('response error');
                    }
                });
            });
        });

    </script>

@include('dataTableScript')
@endsection

