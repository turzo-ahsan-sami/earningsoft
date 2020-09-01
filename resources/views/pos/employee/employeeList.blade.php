@extends('layouts/pos_layout')
@section('title', '| Employee')
@section('content')
@include('successMsg')
@php
$pageNo = isset($_GET['page']) ? (int) $_GET['page']: 1;
//var_dump($hrAllEmployeeInfos);
if(isset($_GET['filter_branch'])){
      $filter_branch = $_GET['filter_branch'];
    } else {
      $filter_branch = '';
    }
  if(isset($_GET['search_project_id_fk'])){
      $search_project_id_fk = $_GET['search_project_id_fk'];
    } else {
      $search_project_id_fk = '';
    } 
if(isset($_GET['search_position_id_fk'])){
      $search_position_id_fk = $_GET['search_position_id_fk'];
    } else {
      $search_position_id_fk = '';
    }

    if(isset($_GET['filter_status'])){
      $filter_status = $_GET['filter_status'];
    } else {
      $filter_status = '';
    }
    if(isset($_GET['filter_nid_or_birth'])){
      $filter_nid_or_birth = $_GET['filter_nid_or_birth'];
    } else {
      $filter_nid_or_birth = '';
    }
    
    if(isset($_GET['filter_name_or_id'])){
      $filter_name_or_id = $_GET['filter_name_or_id'];
    } else {
      $filter_name_or_id = '';
    }
@endphp
<style type="text/css">
    input[type="search"]{
        height: 25px !important; 
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                            <a href="{{url('pos/posAddHrEmployee/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Employee</a>
                        </div>
                        <?php
                            /*echo '<pre>';
                                print_r($hrAllEmployeeInfos);
                            echo '</pre>';*/
                        ?>
                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px;"><font color="white">Employee LIST</font></h1>
                    </div>
                    <div class="panel-body panelBodyView">
                        
                         {!! Form::open(array('url' => 'pos/posHrEmployeeList/', 'role' => 'form', 'class'=>'form-horizontal form-groups','method'=>'get')) !!}
                        <div class="col-md-12" style="background-color: #ffff; padding:10px;">
                            <div class="col-sm-2">
                                <?php 
                                $gnrProjects = array('' => '--Project--') + DB::table('gnr_project')->pluck('name','id')->all(); 
                                ?>      
                                {!! Form::select('search_project_id_fk', ($gnrProjects), null, array('class'=>'form-control', 'id' => 'search_project_id_fk')) !!}
                            </div>
                            <div class="col-sm-2">
                                <?php 
                                 if(isset($_GET['search_project_id_fk'])){
                                $branchIdArr = DB::table('gnr_branch')->select('id','name','branchCode')->where('projectId',$search_project_id_fk)->where('id','!=',1)->get(); 
                                    }
                                ?>

                                <select id="filter_branch" name="filter_branch" class="form-control">
                                     <option value="">Select any</option>
                                <?php if(isset($_GET['search_project_id_fk'])): ?>
                                <option value="1" @if(1==$filter_branch){{"selected=selected"}}@endif>00-Head Office</option>
                                @foreach($branchIdArr as $searchBranch)
                                    <option value="{{$searchBranch->id}}"@if($searchBranch->id==$filter_branch){{"selected=selected"}}@endif>{{str_pad($searchBranch->branchCode,3,"00",STR_PAD_LEFT)}}-{{$searchBranch->name}}</option>
                                @endforeach
                            <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <?php 
                                $gnrpositions = DB::table('hr_settings_position')->select('name','id')->get(); 
                                ?>      
                                <select id="search_position_id_fk" name="search_position_id_fk" class="form-control">
                                    <option value="">--Designation--</option>
                                    @foreach($gnrpositions as $gnrposition)
                                    <option value="{{$gnrposition->id}}"@if($gnrposition->id==$search_position_id_fk){{"selected=selected"}}@endif>{{$gnrposition->name}}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-md-2">
                                <input class="form-control" placeholder="NID or Birth Certificate" name="filter_nid_or_birth" id="filter_nid_or_birth" type="text" value="<?php if(isset($_GET['filter_nid_or_birth'])) echo $filter_nid_or_birth; ?>">
                            </div>
                            <div class="col-md-2">
                                <input class="form-control" placeholder="Name or ID" name="filter_name_or_id" id="filter_name_or_id" type="text" value="<?php if(isset($_GET['filter_name_or_id'])) echo $filter_name_or_id; ?>">
                            </div>
                            <div class="col-md-1">
                                <select class="form-control" name="filter_status" id="filter_status" >
                                    <option value="" selected="selected">All</option>
                                    <option value="Active" <?php if($filter_status=='Active') echo 'selected=selected'; ?>>Active</option>
                                    <option value="Inactive" <?php if($filter_status=='Inactive') echo 'selected=selected'; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> Search</button>
                            </div>
                        </div> 
                       {!! Form::close() !!}
                       <div class="clearfix">&nbsp;</div>
                        <div>

                            <script type="text/javascript">
                            /*jQuery(document).ready(function($)
                            {
                              $("#HrEmployee").dataTable().yadcf([
                      
                              ]);
                            });*/
                            $(document).ready(function() {
                                    $('#HrEmployee').DataTable( {
                                        "paging":   false,
                                        "ordering": false,
                                        "info":     false
                                    } );
                                } );
                            </script>
                        </div>

                        <table class="table table-striped table-bordered" id="HrEmployee" style="color:black;">
                            <thead>
                                <tr>
                                    <th width="30">SL#</th>
                                    <th>Name</th>
                                    <th>Employee Id</th>
                                    <th>Branch</th>
                                    <th>Designation</th>
                                    <th>Job Duration</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                {{ csrf_field() }}
                            </thead>
                            <tbody>
                                @foreach($hrAllEmployeeInfos as $key => $hrAllEmployeeInfo)
                                <tr>
                                <td class="text-center slNo">{{($pageNo-1)*15+($key+1)}}</td>
                                <td class="text-center">{{$hrAllEmployeeInfo->emp_name_english}}</td>
                                <td class="">{{$hrAllEmployeeInfo->emp_id}}</td>
                                <?php 
                                    $branchName = DB::table('gnr_branch')->where('id',$hrAllEmployeeInfo->branch_id_fk)->value('name');
                                ?>
                                <td class="">{{$branchName}}</td>
                                <?php 
                                    $positionName = DB::table('hr_settings_position')->where('id',$hrAllEmployeeInfo->position_id_fk)->value('name');
                                ?>
                                <td class="">{{$positionName}}</td>
                                <td class="">
                                <?php
                                      
                                        //JOB DURATION CALCULATION.
                                        $joiningDate = new DateTime($hrAllEmployeeInfo->joining_date);
                                        $curDate = new DateTime(date('Y-m-d'));
                                        $interval = $joiningDate->diff($curDate);
                                        $interval->format('%y Year %m Month %d Days');

                                        $yearTitle = ['Year', 'Years'];
                                        $monthTitle = ['Month', 'Months'];
                                        $dayTitle = ['Day', 'Days'];

                                        $interval->format('%y')>1?$year = $yearTitle[1]:$year = $yearTitle[0];
                                        $interval->format('%m')>1?$month = $monthTitle[1]:$month = $monthTitle[0];
                                        $interval->format('%d')>1?$day = $dayTitle[1]:$day = $dayTitle[0];

                                        if($interval->format('%y')<1)
                                            echo $interval->format(' %m '.$month.' %d '.$day);
                                        elseif($interval->format('%m')<1)
                                            echo $interval->format('%y '.$year. ' %d '.$day);
                                        elseif($interval->format('%d')<1)
                                            echo $interval->format('%y '.$year. ' %m '.$month);
                                        else
                                            echo $interval->format('%y '.$year.' %m '.$month.' %d '.$day);
                                    ?>

                                </td>
                                <td>
                                    <?php
                                        if($hrAllEmployeeInfo->status =='Active'){
                                       echo '<span class="btn btn-success btn-xs"><i class="fa fa-check"></i></span>';
                                        } else if ($hrAllEmployeeInfo->status =='Inactive') {
                                          echo '<span class="btn btn-danger btn-xs"><i class="fa fa-exclamation-circle"></i></span>';  
                                        }
                                     ?>
                                </td>
                                <td>
                                <a href="{{url('pos/hrDetailsEmployee/').'/'.$hrAllEmployeeInfo->id}}" class="btn btn-xs btn-warning"><i class="fa fa-eye"></i></a>
                                <a id="editIcone" href="javascript:;" class="btn btn-xs btn-info edit-modal" epmloyeeid="{{$hrAllEmployeeInfo->id}}"><i class="fa fa-pencil"></i></a>
                                <a href="javascript:;"  deleteemployeeid="{{$hrAllEmployeeInfo->id}}" class="btn btn-xs btn-danger delete-modal"><i class="fa fa-times"></i>
                                </a>
                                </td>
                                </tr>
                                @endforeach 
                            </tbody>
                        </table>
                        <div>
                           {{  $hrAllEmployeeInfos->appends(request()->input())->links()  }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="editModal" class="modal fade" style="margin-top:3%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Update Employee Info</h4>
            </div>
                <div class="modal-body">
                    <div class="row add-data-form">
                    <div class="col-md-12">
                           <div class="col-md-1"></div>
                            <div class="col-md-10 fullbody">
                                <div class="panel panel-default panel-border">
                                    <div class="panel-body">
                                                    {!! Form::open(array('url' => 'posAddHrEmployee', 'role' => 'form', 'class'=>'form-horizontal form-groups','id'=>'updateForm')) !!}
                                                    <input id="EMepmloyeeId" type="hidden" name="EMepmloyeeId" value="" />
                                                <div class="row">
                                                    <div><h3><u>Personal Info</u></h3></div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            {!! Form::label('emp_id', 'Employee ID :', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                {!! Form::text('emp_id', $value = null, ['class' => 'form-control', 'id' => 'emp_id', 'type' => 'text', 'placeholder' => 'Employee ID','autocomplete'=>'off']) !!}
                                                                <p id='emp_ide' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('father_name', 'Father &#39;s Name :', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                {!! Form::text('father_name', $value = null, ['class' => 'form-control', 'id' => 'father_name', 'type' => 'text', 'placeholder' => 'Father Name','autocomplete'=>'off']) !!}
                                                                <p id='father_namee' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            {!! Form::label('paymentType', 'Sex:', ['class' => 'col-md-4 control-label']) !!}
                                                            <div class="col-md-8">
                                                                 <label><input type="radio" class="radio_button" id="sex_male" name="sex" value="Male"> Male </label>
                                                                <label><input type="radio" class="radio_button" id="sex_female" name="sex" value="Female"> Female </label>
                                                                <p id='sexe' style="max-height:4px; color:red;"></p>
                                                            </div> 
                                                            
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            {!! Form::label('date_of_birth', 'Date of Birth :', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                {!! Form::text('date_of_birth', $value = null, ['class' => 'form-control', 'id' => 'date_of_birth', 'placeholder' => 'Date of Birth','autocomplete'=>'off']) !!}
                                                                <p id='date_of_birthe' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            {!! Form::label('nid_no', 'NID No. :', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                {!! Form::text('nid_no', $value = null, ['class' => 'form-control', 'id' => 'nid_no', 'type' => 'text', 'placeholder' => 'NID No.','autocomplite'=>'off','autocomplete'=>'off']) !!}
                                                                <p id='nid_noe' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('mobile_no', 'Mobile :', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                {!! Form::text('mobile_no', $value = null, ['class' => 'form-control', 'id' => 'mobile_no', 'type' => 'text', 'placeholder' => 'Mobile No','autocomplete'=>'off']) !!}
                                                                <p id='mobile_noe' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                         <h4><u>Present Address</u></h4>
                                                        <div class="form-group">
                                                            {!! Form::label('pre_div_id', 'Division:', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                <?php 
                                                                $preDivisions = array('' => 'Select any') + DB::table('gnr_division')->pluck('name','id')->all(); 
                                                                ?>      
                                                                {!! Form::select('pre_div_id', ($preDivisions), null, array('class'=>'form-control', 'id' => 'pre_div_id')) !!}
                                                                <p id='pre_div_ide' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('pre_dis_id', 'District:', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                <select id="pre_dis_id" name="pre_dis_id" class="form-control">
                                                                     <option value="">Select any</option>
                                                                </select>
                                                                <p id='pre_dis_ide' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('pre_upa_id', 'Upzilla:', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                <select id="pre_upa_id" name="pre_upa_id" class="form-control">
                                                                     <option value="">Select any</option>
                                                                </select>
                                                                <p id='pre_upa_ide' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('pre_uni_id', 'Union:', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                <select id="pre_uni_id" name="pre_uni_id" class="form-control">
                                                                     <option value="0">Select any</option>
                                                                </select>
                                                                <p id='pre_uni_ide' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('present_address', 'Address :', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                {!! Form::textarea('present_address', $value = null, ['class' => 'form-control', 'id' => 'present_address', 'placeholder' => 'Present Address','autocomplete'=>'off','cols'=>'50','rows'=>'3']) !!}
                                                                <p id='present_addresse' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                   </div>
                                                       
                                                   <div class="col-md-6">
                                                        <div class="form-group">
                                                            {!! Form::label('employeeName', 'Employee Name:', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                 {!! Form::text('employeeName', $value = null, ['class' => 'form-control', 'id' => 'employeeName', 'type' => 'text','placeholder' =>'Employee Name','autocomplete'=>'off']) !!}
                                                                <p id='employeeNamee' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('mother_name', 'Mother &#39;s Name :', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                {!! Form::text('mother_name', $value = null, ['class' => 'form-control', 'id' => 'mother_name', 'type' => 'text', 'placeholder' => 'Mother Name','autocomplete'=>'off']) !!}
                                                                <p id='mother_namee' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('religion', 'Religion :', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="religion" name="religion">
                                                                    <option value="">Select any</option>
                                                                    <option value="Islam">Islam</option>
                                                                    <option value="Hindu">Hindu</option>
                                                                    <option value="Buddha">Buddha</option>
                                                                    <option value="Christian">Christian</option>
                                                                </select>
                                                                <p id='religione' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('blood_group', 'Blood Group :', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="blood_group" name="blood_group">
                                                                    <option value="" selected="selected">Select any</option>
                                                                    <option value="A+">A+</option>
                                                                    <option value="B+">B+</option>
                                                                    <option value="AB+">AB+</option>
                                                                    <option value="A-">A-</option>
                                                                    <option value="B-">B-</option>
                                                                    <option value="AB-">AB-</option>
                                                                    <option value="O+">O+</option>
                                                                    <option value="O-">O-</option>
                                                                </select>
                                                                <p id='blood_groupe' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('birth_certificate_no', 'Birth Certificate No.:', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                {!! Form::text('birth_certificate_no', $value = null, ['class' => 'form-control', 'id' => 'birth_certificate_no', 'type' => 'text', 'placeholder' => 'Birth Certificate No.','autocomplete'=>'off']) !!}
                                                                <p id='birth_certificate_noe' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('email', 'Email :', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'text', 'placeholder' => 'Email','autocomplete'=>'off']) !!}
                                                                <p id='emaile' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>

                                                         <h4>
                                                            <u>Permanent Address</u> <span style="font-size: 12px;">
                                                            <input type="checkbox" name="sameasaddress"> Same as Present Address</span>
                                                        </h4>
                                                       <div class="form-group">
                                                            {!! Form::label('per_div_id', 'Division:', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                <?php 
                                                                $perDivisions = array('' => 'Select any') + DB::table('gnr_division')->pluck('name','id')->all(); 
                                                                ?>      
                                                                {!! Form::select('per_div_id', ($perDivisions), null, array('class'=>'form-control', 'id' => 'per_div_id')) !!}
                                                                <p id='per_div_ide' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('per_dis_id', 'District:', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                <select id="per_dis_id" name="per_dis_id" class="form-control">
                                                                     <option value="">Select any</option>
                                                                </select>
                                                                <p id='per_dis_ide' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('per_upa_id', 'Upzilla:', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                <select id="per_upa_id" name="per_upa_id" class="form-control">
                                                                     <option value="">Select any</option>
                                                                </select>
                                                                <p id='per_upa_ide' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('per_uni_id', 'Union:', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                <select id="per_uni_id" name="per_uni_id" class="form-control">
                                                                     <option value="0">Select any</option>
                                                                </select>
                                                                <p id='per_uni_ide' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('permanent_address', 'Address :', ['class' => 'col-sm-4 control-label']) !!}
                                                            <div class="col-sm-8">
                                                                {!! Form::textarea('permanent_address', $value = null, ['class' => 'form-control', 'id' => 'permanent_address', 'placeholder' => 'Permanent Address','autocomplete'=>'off','cols'=>'50','rows'=>'3']) !!}
                                                                <p id='permanent_addresse' style="max-height:4px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div><h3><u>Organization Info</u></h3></div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        {!! Form::label('company_id_fk', 'Company:', ['class' => 'col-sm-4 control-label']) !!}
                                                        <div class="col-sm-8">
                                                            <?php 
                                                            $gnrCompanys = array('' => 'Select any') + DB::table('gnr_company')->pluck('name','id')->all(); 
                                                            ?>      
                                                            {!! Form::select('company_id_fk', ($gnrCompanys), null, array('class'=>'form-control', 'id' => 'company_id_fk')) !!}
                                                            <p id='company_id_fke' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        {!! Form::label('project_type_id_fk', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                                                        <div class="col-sm-8">
                                                            <?php 
                                                            /*$gnrProjectTypes = array('' => 'Select any') + DB::table('gnr_project_type')
                                                                    ->select(DB::raw("CONCAT(projectTypeCode ,' - ', name ) AS name"),'id')->pluck('name','id')->all(); */
                                                            ?>      
                                                            <select id="project_type_id_fk" name="project_type_id_fk" class="form-control">
                                                                     <option value="">Select any</option>
                                                                </select>
                                                            <p id='project_type_id_fke' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        {!! Form::label('position_id_fk', 'Position:', ['class' => 'col-sm-4 control-label']) !!}
                                                        <div class="col-sm-8">
                                                            <?php 
                                                            $gnrpositions = array('' => 'Select any') + DB::table('hr_settings_position')->pluck('name','id')->all(); 
                                                            ?>      
                                                            {!! Form::select('position_id_fk', ($gnrpositions), null, array('class'=>'form-control', 'id' => 'position_id_fk')) !!}
                                                            <p id='position_id_fke' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="status" class="col-sm-4 control-label">Status</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="status" name="status"><option value="Active" selected="selected">Active</option><option value="Inactive">Inactive</option></select>
                                                            <p id='status' style="max-height:3px;"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        {!! Form::label('project_id_fk', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
                                                        <div class="col-sm-8">
                                                            <?php 
                                                            $gnrProjects = array('' => 'Select any') + DB::table('gnr_project')->pluck('name','id')->all(); 
                                                            ?>      
                                                            {!! Form::select('project_id_fk', ($gnrProjects), null, array('class'=>'form-control', 'id' => 'project_id_fk')) !!}
                                                            <p id='project_id_fke' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        {!! Form::label('branch_id_fk', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                                                        <div class="col-sm-8">
                                                            <?php 
                                                            $gnrBranches = array('' => 'Select any') + DB::table('gnr_branch')
                                                                    ->select(DB::raw("CONCAT('0',branchCode ,' - ', name ) AS name"),'id')->pluck('name','id')->all(); 
                                                            ?>      
                                                            {!! Form::select('branch_id_fk', ($gnrBranches), null, array('class'=>'form-control', 'id' => 'branch_id_fk')) !!}
                                                            <p id='branch_id_fke' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        {!! Form::label('joining_date', 'Joining Date :', ['class' => 'col-sm-4 control-label']) !!}
                                                        <div class="col-sm-8">
                                                            {!! Form::text('joining_date', $value = null, ['class' => 'form-control', 'id' => 'joining_date', 'type' => 'text','placeholder' => 'Joining Date','autocomplete'=>'off']) !!}
                                                            <p id='joining_datee' style="max-height:4px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div><h3><u>Login Info</u></h3></div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        {!! Form::label('user_id', 'User ID :', ['class' => 'col-sm-4 control-label']) !!}
                                                        <div class="col-sm-8">
                                                            {!! Form::text('user_id', $value = null, ['class' => 'form-control', 'id' => 'user_id', 'type' => 'text', 'placeholder' => 'User ID','autocomplete'=>'off']) !!}
                                                            <p id='user_ide' style="max-height:4px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        {!! Form::label('user_password', 'Password :', ['class' => 'col-sm-4 control-label']) !!}
                                                        <div class="col-sm-8">
                                                            {!! Form::text('user_password', $value = null, ['class' => 'form-control', 'id' => 'user_password', 'type' => 'text', 'placeholder' => ' Password Keep empty for unchanged','autocomplete'=>'off']) !!}
                                                            <p id='user_passworde' style="max-height:4px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                <div class="form-group">
                                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-4 control-label']) !!}
                                                    <div class="col-sm-8">
                                                        {!! Form::submit('Update', ['id' => 'update', 'class' => 'btn btn-info']); !!}
                                                        <a href="{{url('pos/posHrEmployeeList/')}}" class="btn btn-danger closeBtn">Close</a>
                                                        <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                                    </div>
                                                </div>
                                            {!! Form::close() !!}
                                    </div>
                                </div>
                            
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                </div>
            </div>    
        </div>
    </div>
</div>

<div id="deleteModal" class="modal fade" style="margin-top:3%;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete HR Employee</h4>
            </div>

           <div class="modal-body ">
                <div class="row" style="padding-bottom:20px;"> </div>
                <h2>Are You Confirm to Delete This Record?</h2>

                <div class="modal-footer">
                    <input id="DMemployeeId" type="hidden"  value=""/>
                    <button type="button" class="btn btn-danger"  id="DMemployee"  data-dismiss="">confirm</button>
                    <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                </div>

            </div>
        </div>
    </div>
</div>




@include('dataTableScript')
<script type="text/javascript">
$(document).ready(function(){ 
        $(document).on('click', '.delete-modal', function(){
            $("#DMemployeeId").val($(this).attr('deleteemployeeid'));
            $('#deleteModal').modal('show');
        });
        $("#DMemployee").on('click',  function() {
            var employeeId= $("#DMemployeeId").val();
            //alert(employeeId);
            var csrf = "{{csrf_token()}}";
            $.ajax({
                url: './hrDeleteEmployee',
                type: 'POST',
                dataType: 'json',
                data: {id:employeeId, _token:csrf},
            })
            .done(function(data) {
                 window.location.href = '{{url('pos/posHrEmployeeList/') }}';
            })
            .fail(function(){
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
        });     
    });   

    $(function(){
    /*Edit Modal Start*/
        $(document).on('click', '.edit-modal', function() {
            var epmloyeeId = $(this).attr('epmloyeeid');
            //alert(epmloyeeId);
            var csrf = "{{csrf_token()}}";
            $("#EMepmloyeeId").val(epmloyeeId);
                $.ajax({
                    url: './employeeGetInfo',
                    type: 'POST',
                    dataType: 'json',
                    data: {epmloyeeId:epmloyeeId , _token: csrf},
                    success: function(data) {
                        //alert(JSON.stringify(data));

                        if(data.selectedPreDistricts) {
                          $.each(data.selectedPreDistricts,function(index,value){
                            $('#pre_dis_id').append("<option value='"+value.id+"'>"+value.name+"</option>");
                          });
                        }
                        if(data.selectedPreUpzillas) {
                          $.each(data.selectedPreUpzillas,function(index,value){
                            $('#pre_upa_id').append("<option value='"+value.id+"'>"+value.name+"</option>");
                          });
                        }
                        
                        if(data.selectedPreUnions) {
                          $.each(data.selectedPreUnions,function(index,value){
                            $('#pre_uni_id').append("<option value='"+value.id+"'>"+value.name+"</option>");
                          });
                        }

                        if(data.selectedPerDistricts) {
                          $.each(data.selectedPerDistricts,function(index,value){
                            $('#per_dis_id').append("<option value='"+value.id+"'>"+value.name+"</option>");
                          });
                        }
                        if(data.selectedPerUpzillas) {
                          $.each(data.selectedPerUpzillas,function(index,value){
                            $('#per_upa_id').append("<option value='"+value.id+"'>"+value.name+"</option>");
                          });
                        }
                        
                        if(data.selectedPerUnions) {
                          $.each(data.selectedPerUnions,function(index,value){
                            $('#per_uni_id').append("<option value='"+value.id+"'>"+value.name+"</option>");
                          });
                        }
                        
                        if(data.selectedPojectTypes) {
                          $.each(data.selectedPojectTypes,function(index,value){
                            $('#project_type_id_fk').append("<option value='"+value.id+"'>"+value.name+"</option>");
                          });
                        }

                        $("#emp_id").val(data.hrAllEmployeeGetInfos.emp_id);
                        $("#employeeName").val(data.hrAllEmployeeGetInfos.emp_name_english);
                        $("#father_name").val(data.hrAllEmployeeGetInfos.father_name_english);
                        $("#mother_name").val(data.hrAllEmployeeGetInfos.mother_name_english);
                        if(data.hrAllEmployeeGetInfos.sex=='Male') {
                        $("#sex_male").attr('checked','checked'); 
                        } else if(data.hrAllEmployeeGetInfos.sex=='Female') {
                        $("#sex_female").attr('checked','checked'); 
                        }
                        $("#religion").val(data.hrAllEmployeeGetInfos.religion);
                        $("#date_of_birth").val(data.hrAllEmployeeGetInfos.date_of_birth);
                        $("#blood_group").val(data.hrAllEmployeeGetInfos.blood_group);
                        $("#mobile_no").val(data.hrAllEmployeeGetInfos.mobile_number);
                        $("#nid_no").val(data.hrAllEmployeeGetInfos.nid_no);
                        $("#email").val(data.hrAllEmployeeGetInfos.email);
                        $("#birth_certificate_no").val(data.hrAllEmployeeGetInfos.birth_certificate_no);
                        $("#present_address").val(data.hrAllEmployeeGetInfos.present_address);
                        $("#permanent_address").val(data.hrAllEmployeeGetInfos.permanent_address);
                        $("#pre_div_id").val(data.hrAllEmployeeGetInfos.pre_div_id);
                        $("#pre_dis_id").val(data.hrAllEmployeeGetInfos.pre_dis_id);
                        $("#pre_upa_id").val(data.hrAllEmployeeGetInfos.pre_upa_id);
                        $("#pre_uni_id").val(data.hrAllEmployeeGetInfos.pre_uni_id);
                        $("#per_div_id").val(data.hrAllEmployeeGetInfos.per_div_id);
                        $("#per_dis_id").val(data.hrAllEmployeeGetInfos.per_dis_id);
                        $("#per_upa_id").val(data.hrAllEmployeeGetInfos.per_upa_id);
                        $("#per_uni_id").val(data.hrAllEmployeeGetInfos.per_uni_id);
                        $("#per_uni_id").val(data.hrAllEmployeeGetInfos.per_uni_id);
                        $("#joining_date").val(data.hrAllEmployeeGetInfos.joining_date);
                        $("#company_id_fk").val(data.hrAllEmployeeGetInfos.company_id_fk);
                        $("#project_id_fk").val(data.hrAllEmployeeGetInfos.project_id_fk);
                        $("#project_type_id_fk").val(data.hrAllEmployeeGetInfos.project_type_id_fk);
                        $("#branch_id_fk").val(data.hrAllEmployeeGetInfos.branch_id_fk);
                        $("#position_id_fk").val(data.hrAllEmployeeGetInfos.position_id_fk);
                        $("#status").val(data.hrAllEmployeeGetInfos.status);
                        $("#user_id").val(data.hrAllEmployeeGetInfos.username);
                        $("#editModal").find('.modal-dialog').css('width', '80%');
                        $('#editModal').modal('show');

                   },
                      error: function(argument) {
                        //alert('response error');
                }
            });
        });
});
/*edit Modal End*/
function sameasaddress(){
            //sameasaddress
            var pre_div_id = $('select[name="pre_div_id"]');
            var pre_dis_id = $('select[name="pre_dis_id"]');
            var pre_upa_id = $('select[name="pre_upa_id"]');
            var pre_uni_id = $('select[name="pre_uni_id"]');
            var present_address = $('textarea[name="present_address"]');

            var per_div_id = $('select[name="per_div_id"]');
            per_div_id.val(pre_div_id.val());

            var per_dis_id = $('select[name="per_dis_id"]');
            per_dis_id.html(pre_dis_id.html());
            per_dis_id.val(pre_dis_id.val());

            var per_upa_id = $('select[name="per_upa_id"]');
            per_upa_id.html(pre_upa_id.html());
            per_upa_id.val(pre_upa_id.val());

            var per_uni_id = $('select[name="per_uni_id"]');
            per_uni_id.html(pre_uni_id.html());
            per_uni_id.val(pre_uni_id.val());

            var permanent_address = $('textarea[name="permanent_address"]');
            permanent_address.val(present_address.val());
        }

        $(document).on('click','input[name="sameasaddress"]',function(){
            if($(this).is(':checked')){
                sameasaddress();
            }
        });
 $(function(){
    $( "#date_of_birth,#joining_date" ).datepicker({
      dateFormat: "yy-mm-dd",  
      changeMonth: true,
      changeYear: true,
      showOtherMonths: true,
      selectOtherMonths: true,
      maxDate: "0",
      yearRange: "-40:+0"
    });
//== START PRESENT ADDRESS FILTERING==============
    $("#pre_div_id").change(function(event) {
        var pre_div_id = $("#pre_div_id").val();
        //alert(pre_div_id);
        $("#pre_dis_id").empty();
        $("#pre_dis_id").prepend('<option value="">Select District</option>');
        $.ajax({
            url: './preDistricFiltering',
            type: 'POST',
            dataType: 'json',
            data: {pre_div_id: pre_div_id},
        })
        .done(function(data) {
            //alert(JSON.stringify(data));
            if(data) {
              $.each(data.preDistricData,function(index,value){
                $('#pre_dis_id').append("<option value='"+value.id+"'>"+value.name+"</option>");
              });
            }
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    });

$("#pre_dis_id").change(function(event) {
        var pre_div_id = $("#pre_div_id").val();
        var pre_dis_id = $("#pre_dis_id").val();
        //alert(pre_dis_id);
        $("#pre_upa_id").empty();
        $("#pre_upa_id").prepend('<option value="">Select Upzilla</option>');
        $.ajax({
            url: './preUpzillaDataFiltering',
            type: 'POST',
            dataType: 'json',
            data: {pre_div_id:pre_div_id,pre_dis_id: pre_dis_id},
        })
        .done(function(data) {
            //alert(JSON.stringify(data));
            if(data) {
              $.each(data.preUpzillaData,function(index,value){
                $('#pre_upa_id').append("<option value='"+value.id+"'>"+value.name+"</option>");
              });
            }
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    });

$("#pre_upa_id").change(function(event) {


        var pre_div_id = $("#pre_div_id").val();
        var pre_dis_id = $("#pre_dis_id").val();
        var pre_upa_id = $("#pre_upa_id").val();
        //alert(pre_dis_id);
        $("#pre_uni_id").empty();
        $("#pre_uni_id").prepend('<option value="0">Select Union</option>');
        $.ajax({
            url: './preUnionDataFiltering',
            type: 'POST',
            dataType: 'json',
            data: {pre_div_id:pre_div_id,pre_dis_id: pre_dis_id,pre_upa_id:pre_upa_id},
        })
        .done(function(data) {
            //alert(JSON.stringify(data));
            if(data) {
              $.each(data.preUnionData,function(index,value){
                $('#pre_uni_id').append("<option value='"+value.id+"'>"+value.name+"</option>");
              });
            }
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    });


//=====END ADDRESS FILTERING==============
});

    $(function(){
//== START PERMANENT ADDRESS FILTERING==============
    $("#per_div_id").change(function(event) {
        var per_div_id = $("#per_div_id").val();
        //alert(pre_div_id);
        $("#per_dis_id").empty();
        $("#per_dis_id").prepend('<option value="">Select District</option>');
        $.ajax({
            url: './preDistricFiltering',
            type: 'POST',
            dataType: 'json',
            data: {pre_div_id: per_div_id},
        })
        .done(function(data) {
            //alert(JSON.stringify(data));
            if(data) {
              $.each(data.preDistricData,function(index,value){
                $('#per_dis_id').append("<option value='"+value.id+"'>"+value.name+"</option>");
              });
            }
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    });

$("#per_dis_id").change(function(event) {
        var per_div_id = $("#per_div_id").val();
        var per_dis_id = $("#per_dis_id").val();
        //alert(pre_dis_id);
        $("#per_upa_id").empty();
        $("#per_upa_id").prepend('<option value="">Select Upzilla</option>');
        $.ajax({
            url: './preUpzillaDataFiltering',
            type: 'POST',
            dataType: 'json',
            data: {pre_div_id:per_div_id,pre_dis_id: per_dis_id},
        })
        .done(function(data) {
            //alert(JSON.stringify(data));
            if(data) {
              $.each(data.preUpzillaData,function(index,value){
                $('#per_upa_id').append("<option value='"+value.id+"'>"+value.name+"</option>");
              });
            }
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    });

$("#per_upa_id").change(function(event) {


        var per_div_id = $("#per_div_id").val();
        var per_dis_id = $("#per_dis_id").val();
        var per_upa_id = $("#per_upa_id").val();
        //alert(pre_dis_id);
        $("#per_uni_id").empty();
        $("#per_uni_id").prepend('<option value="0">Select Union</option>');
        $.ajax({
            url: './preUnionDataFiltering',
            type: 'POST',
            dataType: 'json',
            data: {pre_div_id:per_div_id,pre_dis_id: per_dis_id,pre_upa_id:per_upa_id},
        })
        .done(function(data) {
            //alert(JSON.stringify(data));
            if(data) {
              $.each(data.preUnionData,function(index,value){
                $('#per_uni_id').append("<option value='"+value.id+"'>"+value.name+"</option>");
              });
            }
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
});


$("#project_id_fk").change(function(event) {
        var project_id_fk = $("#project_id_fk").val();
        
        //alert(project_id_fk);
        $("#project_type_id_fk").empty();
        $("#project_type_id_fk").prepend('<option>Select any</option>');
        $.ajax({
            url: './projectTypeFiltering',
            type: 'POST',
            dataType: 'json',
            data: {project_id_fk:project_id_fk},
        })
        .done(function(data) {
            //alert(JSON.stringify(data));
            if(data) {
              $.each(data.projectTypeData,function(index,value){
                $('#project_type_id_fk').append("<option value='"+value.id+"'>"+value.name+"</option>");
              });
            }
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
});


//=====END ADDRESS FILTERING==============
});
    $('#updateForm').submit(function(event) {
            event.preventDefault();
            //alert($('form').serialize());
            $("#update").prop("disabled", true);
            $.ajax({
                url: './updateHrEployeeInfo',
                type: 'POST',
                dataType: 'json',
                data: $('#updateForm').serialize(),

            })
            .done(function(data) {
                if(data.errors)  {
                    if (data.errors['emp_id']) {
                        $("#emp_ide").empty();
                        $("#emp_ide").append('*'+data.errors['emp_id']);
                    } 
                    if (data.errors['father_name']) {
                        $("#father_namee").empty();
                        $("#father_namee").append('*'+data.errors['father_name']);
                     }
                    if (data.errors['sex']) {
                        $("#sexe").empty();
                        $("#sexe").append('*'+data.errors['sex']);
                    } 
                    if (data.errors['date_of_birth']) {
                         $("#date_of_birthe").empty();
                         $("#date_of_birthe").append('*'+data.errors['date_of_birth']);
                     } 
                    if (data.errors['nid_no']) {
                        $("#nid_noe").empty();
                        $("#nid_noe").append('*'+data.errors['nid_no']);
                     }

                    if (data.errors['mobile_no']) {
                        $("#mobile_noe").empty();
                        $("#mobile_noe").append('*'+data.errors['mobile_no']);
                    }  
                    if (data.errors['pre_div_id']) {
                        $("#pre_div_ide").empty();
                        $("#pre_div_ide").append('*'+data.errors['pre_div_id']);
                    }
                    if (data.errors['pre_dis_id']) {
                        $("#pre_dis_ide").empty();
                        $("#pre_dis_ide").append('*'+data.errors['pre_dis_id']);
                    }
                    if (data.errors['pre_upa_id']) {
                        $("#pre_upa_ide").empty();
                        $("#pre_upa_ide").append('*'+data.errors['pre_upa_id']);
                    }
                    if (data.errors['present_address']) {
                        $("#present_addresse").empty();
                        $("#present_addresse").append('*'+data.errors['present_address']);
                    } 
                    if (data.errors['company_id_fk']) {
                        $("#company_id_fke").empty();
                        $("#company_id_fke").append('*'+data.errors['company_id_fk']);
                    }  
                    if (data.errors['project_type_id_fk']) {
                        $("#project_type_id_fke").empty();
                        $("#project_type_id_fke").append('*'+data.errors['project_type_id_fk']);
                    }  
                    if (data.errors['position_id_fk']) {
                        $("#position_id_fke").empty();
                        $("#position_id_fke").append('*'+data.errors['position_id_fk']);
                    }  
                    if (data.errors['user_id']) {
                        $("#user_ide").empty();
                        $("#user_ide").append('*'+data.errors['user_id']);
                    }  
                    if (data.errors['present_address']) {
                        $("#present_addresse").empty();
                        $("#present_addresse").append('*'+data.errors['present_address']);
                    }  
                    if (data.errors['employeeName']) {
                        $("#employeeNamee").empty();
                        $("#employeeNamee").append('*'+data.errors['employeeName']);
                    }
                    if (data.errors['mother_name']) {
                        $("#mother_namee").empty();
                        $("#mother_namee").append('*'+data.errors['mother_name']);
                    }
                    if (data.errors['religion']) {
                        $("#religione").empty();
                        $("#religione").append('*'+data.errors['religion']);
                    } 
                    if (data.errors['blood_group']) {
                        $("#blood_groupe").empty();
                        $("#blood_groupe").append('*'+data.errors['blood_group']);
                    } 
                    if (data.errors['birth_certificate_no']) {
                        $("#birth_certificate_noe").empty();
                        $("#birth_certificate_noe").append('*'+data.errors['birth_certificate_no']);
                    } 
                    if (data.errors['email']) {
                        $("#emaile").empty();
                        $("#emaile").append('*'+data.errors['email']);
                    } 
                    if (data.errors['per_div_id']) {
                        $("#per_div_ide").empty();
                        $("#per_div_ide").append('*'+data.errors['per_div_id']);
                    } 
                    if (data.errors['per_dis_id']) {
                        $("#per_dis_ide").empty();
                        $("#per_dis_ide").append('*'+data.errors['per_dis_id']);
                    } 
                    if (data.errors['per_upa_id']) {
                        $("#per_upa_ide").empty();
                        $("#per_upa_ide").append('*'+data.errors['per_upa_id']);
                    } 
                    if (data.errors['permanent_address']) {
                        $("#permanent_addresse").empty();
                        $("#permanent_addresse").append('*'+data.errors['permanent_address']);
                    } 
                    if (data.errors['project_id_fk']) {
                        $("#project_id_fke").empty();
                        $("#project_id_fke").append('*'+data.errors['project_id_fk']);
                    }
                    if (data.errors['branch_id_fk']) {
                        $("#branch_id_fke").empty();
                        $("#branch_id_fke").append('*'+data.errors['branch_id_fk']);
                    } 
                    if (data.errors['joining_date']) {
                        $("#joining_datee").empty();
                        $("#joining_datee").append('*'+data.errors['joining_date']);
                    }  
                    if (data.errors['user_password']) {
                        $("#user_passworde").empty();
                        $("#user_passworde").append('*'+data.errors['user_password']);
                    } 

                } else  {
                    window.location.href = '{{url('pos/posHrEmployeeList/') }}';
                }
            });

           $(document).on('input','input',function() {
               $(this).closest('div').find('p').remove();
            });
            $(document).on('input','textarea',function() {
               $(this).closest('div').find('p').remove();
            });


           $(document).on('change','select',function() {
               $(this).closest('div').find('p').remove();
           });

           $(document).on('change','input:radio',function() {
               $(this).closest('div').find('p').remove();
            });


          });
//=========FILTERING FOR BRANCH BY PROJECT=========================
$(function(){
    function pad (str, max) {
               str = str.toString();
               return str.length < max ? pad("0" + str, max) : str;
           }
     $("#search_project_id_fk").change(function(event) {
         var search_project_id_fk = $("#search_project_id_fk").val();
         //alert(search_project_id_fk);
         $.ajax({
             url: './branchFilteringByProject',
             type: 'POST',
             dataType: 'json',
             data: {search_project_id_fk: search_project_id_fk},
         })
         .done(function(data) {
            //alert(JSON.stringify(data));
            $('#filter_branch').empty();
            $('#filter_branch').prepend('<option value="">Select any</option><option value="1">00-Head Oficce</option>');
            if(data) {
              $.each(data.branchData,function(index,value){
                $('#filter_branch').append("<option value='"+value.id+"'>"+pad(value.branchCode,3)+'-'+value.name+"</option>");
              });
            }
             console.log("success");
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
@endsection