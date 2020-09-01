@extends('layouts/gnr_layout')
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
                       {{--  <div class="panel-options">
                            <a href="" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>All Employee</a>
                        </div> --}}
                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">Profile Details</font></h1>
                    </div>
                    <div class="panel-body panelBodyView">
                        <div class="row">
                              <div class="col-md-6"> 
                                <table class="table table-striped table-bordered" id="" style="color:black;">
                                    <tr><th><u>Personal Information:</u></th><td></td><td></td></tr>
                                        <tr>
                                            <th>Employee Id</th><td>:</td><td>{{$hrAllEmployeeDetails->employeeId}}</td>
                                        </tr>
                                        <tr>
                                            <th>Name</th><td>:</td><td>{{$hrAllEmployeeDetails->name}}</td>
                                        </tr>
                                        <tr>
                                            <th>Father's Name</th><td>:</td><td>{{$hrAllEmployeeDetails->fatherName}}</td>
                                        </tr>
                                       
                                        <tr>
                                            <th>Sex</th><td>:</td><td>{{$hrAllEmployeeDetails->gender}}</td>
                                        </tr>
                                       
                                        <tr>
                                            <th>Date of birth</th><td>:</td><td><?php echo date ('d/m/Y',strtotime($hrAllEmployeeDetails->dateOfBirth)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Mobile Number</th><td>:</td><td>{{$hrAllEmployeeDetails->phone}}</td>
                                        </tr>
                                        <tr>
                                            <th>Nid no</th><td>:</td><td>{{$hrAllEmployeeDetails->nationalId}}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th><td>:</td><td>{{$hrAllEmployeeDetails->email}}</td>
                                        </tr>
                                       
                                        <tr>
                                            <th>Present Address</th><td>:</td><td>{{$hrAllEmployeeDetails->presentAddress}}</td>
                                        </tr>
                                        <tr>
                                            <th>Permanent Address</th><td>:</td><td>{{$hrAllEmployeeDetails->parmanentAddress}}</td>
                                        </tr>
                                        <tr>
                                            <th>Image</th><td>:</td><td>  <img src="{{ asset("images/employee/$hrAllEmployeeDetails->image") }}" width="60" id="blah"></td>
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
                                            $branchName = DB::table('gnr_branch')->where('id',$hrAllEmployeeDetails->branchId)->value('name');
                                            ?>
                                            <th>Branch</th><td>:</td><td>{{$branchName}}</td>
                                        </tr>
                                        <tr>
                                            <?php
                                            $departmentName = DB::table('gnr_department')->where('id',$hrAllEmployeeDetails->department_id_fk)->value('name');
                                            ?>
                                            
                                            <th>Department</th><td>:</td><td>{{$departmentName}}</td>
                                        </tr>
                                        <tr>
                                            <?php
                                            $positionName = DB::table('gnr_position')->where('id',$hrAllEmployeeDetails->position_id_fk)->value('name');
                                            ?>
                                            
                                            <th>Position</th><td>:</td><td>{{$positionName}}</td>
                                        </tr>
                                        <tr>
                                            <th>Status </th>
                                            <td>:</td>
                                            <td>
                                                @if($hrAllEmployeeDetails->status ==1)
                                                    active

                                                @else
                                                Inactive
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><a href="{{url('gnr/hrEditEmployee/').'/'.$hrAllEmployeeDetails->id}}" class="btn btn-info pull-middle addViewBtn"><i class="fa fa-edit addIcon"></i>Edit</a></td>
                                          {{--   <td colspan="3"> <a id="editIcone" href="" class="btn btn-info"><i class="fa fa-edit"></i></a>Edit</td> --}}
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