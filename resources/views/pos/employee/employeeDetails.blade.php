@extends('layouts/pos_layout')
@section('title', '| Employee')
@section('content')
@include('successMsg')
<?php 
/*echo "<pre>";
        print_r($hrAllEmployeeDetails);
echo "</pre>";*/
?>
<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                            <a href="{{url('pos/posHrEmployeeList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>All Employee</a>
                        </div>
                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">Employee Details</font></h1>
                    </div>
                    <div class="panel-body panelBodyView">
                        <div class="row">
                              <div class="col-md-6"> 
                                <table class="table table-striped table-bordered" id="" style="color:black;">
                                    <tr><th><u>Personal Information:</u></th><td></td><td></td></tr>
                                        <tr>
                                            <th>Employee Id</th><td>:</td><td>{{$hrAllEmployeeDetails->emp_id}}</td>
                                        </tr>
                                        <tr>
                                            <th>Name</th><td>:</td><td>{{$hrAllEmployeeDetails->emp_name_english}}</td>
                                        </tr>
                                        <tr>
                                            <th>Father's Name</th><td>:</td><td>{{$hrAllEmployeeDetails->father_name_english}}</td>
                                        </tr>
                                        <tr>
                                            <th>Mother's Name</th><td>:</td><td>{{$hrAllEmployeeDetails->mother_name_english}}</td>
                                        </tr>
                                        <tr>
                                            <th>Sex</th><td>:</td><td>{{$hrAllEmployeeDetails->sex}}</td>
                                        </tr>
                                        <tr>
                                            <th>Religion</th><td>:</td><td>{{$hrAllEmployeeDetails->religion}}</td>
                                        </tr>
                                        <tr>
                                            <th>Date of birth</th><td>:</td><td><?php echo date ('d/m/Y',strtotime($hrAllEmployeeDetails->date_of_birth)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Blood Group</th><td>:</td><td>{{$hrAllEmployeeDetails->blood_group}}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile Number</th><td>:</td><td>{{$hrAllEmployeeDetails->mobile_number}}</td>
                                        </tr>
                                        <tr>
                                            <th>Nid no</th><td>:</td><td>{{$hrAllEmployeeDetails->nid_no}}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th><td>:</td><td>{{$hrAllEmployeeDetails->email}}</td>
                                        </tr>
                                        <tr>
                                            <th>Birth certificate no</th><td>:</td><td>{{$hrAllEmployeeDetails->birth_certificate_no}}</td>
                                        </tr>
                                        <tr>
                                            <th>Present Address</th><td>:</td><td>{{$hrAllEmployeeDetails->present_address}}</td>
                                        </tr>
                                        <tr>
                                            <th>Permanent Address</th><td>:</td><td>{{$hrAllEmployeeDetails->permanent_address}}</td>
                                        </tr>
                                        {{ csrf_field() }}
                                </table>
                            </div>
                             <div class="col-md-6"> 
                                <table class="table table-striped table-bordered" id="" style="color:black;">
                                    <tr><th><u>Organization Information:</u></th><td></td><td></td></tr>
                                        <tr>
                                           <?php
                                            $companyName = DB::table('gnr_company')->where('id',$hrAllEmployeeDetails->company_id_fk)->value('name');
                                            ?>
                                            <th>Company name</th><td>:</td><td>{{$companyName}}</td>
                                        </tr>
                                        <tr>
                                            <?php
                                            $projectName = DB::table('gnr_project')->where('id',$hrAllEmployeeDetails->project_id_fk)->value('name');
                                            ?>
                                            <th>Project name</th><td>:</td><td>{{$projectName}}</td>
                                        </tr>
                                        <tr>
                                            <?php
                                            $projectTypeName = DB::table('gnr_project_type')->where('id',$hrAllEmployeeDetails->project_type_id_fk)->value('name');
                                            ?>
                                            <th>Project type</th><td>:</td><td>{{$projectTypeName}}</td>
                                        </tr>
                                        <tr>
                                            <?php
                                            $branchName = DB::table('gnr_branch')->where('id',$hrAllEmployeeDetails->branch_id_fk)->value('name');
                                            ?>
                                            <th>Branch</th><td>:</td><td>{{$branchName}}</td>
                                        </tr>
                                        <tr>
                                            <?php
                                            $positionName = DB::table('hr_settings_position')->where('id',$hrAllEmployeeDetails->position_id_fk)->value('name');
                                            ?>
                                            
                                            <th>Position</th><td>:</td><td>{{$positionName}}</td>
                                        </tr>
                                        
                                        <tr>
                                            <th>Joining Date</th><td>:</td><td><?php echo date ('d/m/Y',strtotime($hrAllEmployeeDetails->joining_date)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status </th><td>:</td><td>{{$hrAllEmployeeDetails->status}}</td>
                                        </tr>
                                        <tr>
                                            <th>User Name</th><td>:</td><td>{{$hrAllEmployeeDetails->username}}</td>
                                        </tr>
                                     
                                        {{ csrf_field() }}
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection