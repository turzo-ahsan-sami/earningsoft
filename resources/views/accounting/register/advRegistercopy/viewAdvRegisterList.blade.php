@extends('layouts/acc_layout')
@section('title', '|Advance Register')
@section('content')

          @php

            use App\accounting\AccAdvRegisterType;
            use App\accounting\AccAdvRegister;
            use App\gnr\gnr_house_Owner;
            use App\gnr\gnr_employee;
            use App\gnr\gnr_supplier;
          @endphp

<div class="row">
   <div class="col-md-12">
      <div class="" style="">
         <div class="">
           <div class="panel panel-default" style="background-color:#708090;">
             <div class="panel-heading" style="padding-bottom:0px;">
                 <div class="panel-options">
                        <a href="{{url('createAdvanceRegesterFrom/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Advance Register</a>
                 </div>

                 <h1 align="center" style="font-family: Antiqua;letter-spacing:2px;"><font color="white"> ADVANCE PAYMENT LIST</font></h1>
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
                                            <th>Payment Date</th>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Advance Type</th>
                                            <th>Project name</th>
                                            <th>Project Type</th>
                                            <th>Payment Type</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>

                                      </thead>
                              <tbody>


      @foreach ($accAdvRegister as $index=>$accAdvRegister)


                                {{-- expr --}}
  @php

        $result='';

        if($accAdvRegister->houseOwnerId) {
          $houseOwner= DB::table('gnr_house_Owner')->where('id',$accAdvRegister->houseOwnerId)->value('houseOwnerName');
          $result=$houseOwner;

        }

        elseif($accAdvRegister->supplierId) {
          $supplir=DB::table('gnr_supplier')->where('id',$accAdvRegister->supplierId)->value('name');
          $result =$supplir;

        }

        elseif($accAdvRegister->employeeId) {
        $employee = DB::table('hr_emp_general_info')->where('id',$accAdvRegister->employeeId)->value('emp_name_english');
        $result =$employee;
        }

        $advRegType= DB::table('acc_adv_register_type')->where('id',$accAdvRegister->advRegType)->value('name');
        $project= DB::table('gnr_project')->where('id',$accAdvRegister->projectId)->value('name');
       $projectType= DB::table('gnr_project_type')->where('id',$accAdvRegister->projectTypeId)->value('name');
  @endphp


  @php
        $name='';
        if($accAdvRegister->employeeId>0) {
              $employee =DB::table('hr_emp_general_info')->where('id',$accAdvRegister->employeeId)->value('emp_id');
            $name= $employee;
          }
          elseif($accAdvRegister->supplierId>0) {
             $supplier =DB::table('gnr_supplier')->where('id',$accAdvRegister->supplierId)->value('name');
             $name= $supplier;
        }

  @endphp
          <tr style:"float:left;">
              <td>{{$index+1}}</td>
              <td>{{date('d-m-Y',strtotime($accAdvRegister->advPaymentDate))}}</td>
              <td style="text-align:left;padding-left:2px;">{{$result}}</td>
              <td style="text-align:left;padding-left:2px;">{{$name}}</td>
              <td style="text-align:left;padding-left:2px;">{{$advRegType}}</td>
              <td style="text-align:left;padding-left:2px;">{{$project}}</td>
              <td style="text-align:left;padding-left:2px;">{{$projectType}}</td>
              <td>
                      @if($accAdvRegister->cashId>0)
                          {{'Cash'}}
                      @elseif($accAdvRegister->bankId>0)
                          {{'Bank'}}
                      @endif

              </td>
              <td style="text-align:right;padding-right:2px;">{{number_format($accAdvRegister->amount,2)}}</td>
              <td>       @if($accAdvRegister->status==0)
                          Paid
                        @else
                            Unpaid
                        @endif</td>
              <td width="80">

                <a href="javascript:;" class="view-modal" advanceReg="{{$accAdvRegister->id}}"><i class="fa fa-eye" aria-hidden="true"></i>
                </a>&nbsp;
                <a href="javascript:;" class="edit-modal" advanceReg="{{$accAdvRegister->id}}" @php if( $accAdvRegister->status==0){echo "style=\"pointer-events: none;cursor: not-allowed;\"";} @endphp><span class="glyphicon glyphicon-edit"></span> </a>&nbsp;
                <a href="javascript:;" class="receive-modal" advanceReg="{{$accAdvRegister->id}}" @php if( $accAdvRegister->status==0){echo "style=\"pointer-events: none;cursor: not-allowed;\"";} @endphp><span class="glyphicon glyphicon-shopping-cart"></span> </a>&nbsp;
                <a href="javascript:;" class="delete-modal" advanceReg="{{$accAdvRegister->id}}" @php if( $accAdvRegister->status==0){echo "style=\"pointer-events: none;cursor: not-allowed;\"";} @endphp><span class="glyphicon glyphicon-trash"></span>
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
              <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">View Advance Register Info</h4>
          </div>

         <div class="modal-body">
            <div class="row" style="padding-bottom: 20px;">
               <div class="col-md-12">
                 <div class="col-md-12" style="padding-right:2%;">
                    <div class="form-horizontal form-groups">
                      <input id="VMadvReg" type="hidden" name="advRegId" value="">

                      <div class="form-group">
                        {!! Form::label('advReg','AdvRegId', ['class' => 'col-sm-3 control-label']) !!}
                          <div class="col-sm-1 control-label">: </div>
                          <div class="col-sm-8">
                            {!! Form::text('advReg', null,['id'=>'VMadvRegisterId','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                               <p id='advReg' class="error" style=color: red;"></p>
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
                        {!! Form::label('advRegType','Advance Type', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label">: </div>
                        <div class="col-sm-8">
                           {!! Form::text('advRegType', null,['id'=>'VMregType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                           <p id='advRegType' class="error" style=color: red;"></p>

                        </div>
                      </div>

                      <div class="form-group" id="VMemployeeName1">
                        {!! Form::label('employeeName','Employee Name', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label" id="employee1">: </div>
                        <div class="col-sm-8">
                           {!! Form::text('employeeName', null,['id'=>'VMemployeeName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                           <p id='employeeName' class="error" style=color: red;"></p>
                        </div>
                      </div>

                      <div class="form-group" id="VMsupplierName1">
                        {!! Form::label('supplierName','Supplier Name', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label" id="supplier1">: </div>
                        <div class="col-sm-8">
                            {!! Form::text('supplierName', null,['id'=>'VMsupplierName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                            <p id='supplierName' class="error" style=color:red;"></p>
                        </div>
                      </div>

                      <div class="form-group" id="VMhouseOwnerName1">
                        {!! Form::label('houseOwnerName','House Owner Name', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label" id="houseOwner1">: </div>
                        <div class="col-sm-8">
                            {!! Form::text('houseOwnerName', null,['id'=>'VMhouseOwnerName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                            <p id='houseOwnerName' class="error" style="color:red;"></p>
                        </div>
                      </div>

                      <div class="form-group">
                        {!! Form::label('changePaymentType','Payment Type', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label" id="">: </div>
                        <div class="col-sm-8">
                            {!! Form::text('changePaymentType', null,['id'=>'MVchangePaymentType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                            <p id='changePaymentTypee' class="error" style="color:red;"></p>
                        </div>
                      </div>
                      <div class="form-group">
                        {!! Form::label('amount','Amount', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label">: </div>
                        <div class="col-sm-8">
                            {!! Form::text('amount', null,['id'=>'VMamount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                            <p id='amount' class="error" style="color:red;"></p>
                        </div>
                      </div>
                      <div class="form-group">
                        {!! Form::label('paymentDate','Payment Date', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label">: </div>
                       <div class="col-sm-8">
                           {!! Form::text('paymentDate', null,['id'=>'VMhpaymentDate','class'=>'form-control datepicker','type' => 'text','autocomplete'=>'off','readonly']) !!}
                            <p id='paymentDate' class="error" style="color: red;"></p>
                        </div>
                     </div>
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
              <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Update Advance Register</h4>
         </div>
      <div class="modal-body">
       <div class="panel-body float-left">
         <div class="row">
            <div class="col-md-12">
              {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

               <div class="col-md-12">
                 <div class="form-group">
                        {!! Form::label('advReg','AdvRegId', ['class' => 'col-sm-3 control-label']) !!}
                          <div class="col-sm-1 control-label">: </div>
                          <div class="col-sm-8">
                            {!! Form::text('advReg', null,['id'=>'EMadvRegisterId','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                               <p id='advReg' class="error" style="color: red;"></p>
                          </div>
                      </div>
                      <div class="form-group">
                        {!! Form::label('project','Project Name', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label">: </div>
                        <div class="col-sm-8">
                          {!! Form::text('project', null,['id'=>'EMproject','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                          <p id='projecte' class="error" style="color: red;"></p>

                        </div>
                      </div>

                      <div class="form-group">
                        {!! Form::label('projectType','Project Type', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label">: </div>
                        <div class="col-sm-8">
                           {!! Form::text('projectType', null,['id'=>'EMprojectType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                           <p id='projectType' class="error" style="color: red;"></p>

                        </div>
                      </div>

                      <div class="form-group">
                        {!! Form::label('advRegType','Advance Type', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label">: </div>
                        <div class="col-sm-8">
                           {!! Form::text('advRegType', null,['id'=>'EMregType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                           <p id='advRegType' class="error" style="color: red;"></p>

                        </div>
                      </div>

                      <div class="form-group" id="EMemployeeName1">
                        {!! Form::label('employeeName','Employee Name', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label" id="employee1">: </div>
                        <div class="col-sm-8">
                           {!! Form::text('employeeName', null,['id'=>'EMemployeeName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                           <p id='employeeName' class="error" style="color: red;"></p>
                        </div>
                      </div>

                      <div class="form-group" id="EMsupplierName1">
                        {!! Form::label('supplierName','Supplier Name', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label" id="supplier1">: </div>
                        <div class="col-sm-8">
                            {!! Form::text('supplierName', null,['id'=>'EMsupplierName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                            <p id='supplierName' class="error" style="color:red;"></p>
                        </div>
                      </div>

                      <div class="form-group" id="EMhouseOwnerName1">
                        {!! Form::label('houseOwnerName','House Owner Name', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label" id="houseOwner1">: </div>
                        <div class="col-sm-8">
                            {!! Form::text('houseOwnerName', null,['id'=>'EMhouseOwnerName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                            <p id='houseOwnerName' class="error" style="color:red;"></p>
                        </div>
                      </div>

                      <div class="form-group">
                        {!! Form::label('changePaymentType','Payment Type', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label" id="">: </div>
                        <div class="col-sm-8">
                            {!! Form::text('changePaymentType', null,['id'=>'EMchangePaymentType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                            <p id='changePaymentTypee' class="error" style="color:red;"></p>
                        </div>
                      </div>
                      <div class="form-group">
                        {!! Form::label('amount','Amount', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label">: </div>
                        <div class="col-sm-8">
                            {!! Form::text('amount', null,['id'=>'EMamount','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                            <p id='amount' class="error" style="color:red;"></p>
                        </div>
                      </div>
                      <div class="form-group">
                        {!! Form::label('paymentDate','Payment Date', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-1 control-label">: </div>
                       <div class="col-sm-8">
                           {!! Form::text('paymentDate', null,['id'=>'EMhpaymentDate','class'=>'form-control datepicker','type' => 'text','autocomplete'=>'off','readonly']) !!}
                            <p id='paymentDate' class="error" style="color: red;"></p>
                        </div>
                     </div>
                      <div class="modal-footer">
                         <input id="EMadvReg" type="hidden" name="advReg" value="">
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

<!-- recieve modal -->

        <div id="receiveModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Receive Payment</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                        {!! Form::open(array('url' => '', 'role' => 'form','id'=>'receiveForm','name'=>'receiveForm', 'class'=>'form-horizontal form-groups')) !!}
                        {!! Form::hidden('billTypeId',null,['id'=>'EMbillTypeId']) !!}
                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">



                                        <div class="form-group">
                                          {!! Form::label('project', 'Project', ['class' => 'col-sm-4 control-label','style'=>'font-size:14px']) !!}
                                            <div class="col-sm-8" style="padding-bottom:10px;">
                                              {!! Form::text('project',null, ['class'=>'form-control', 'id' => 'project','style'=>'font-size:12px !important','readonly']) !!}
                                              {!! Form::hidden('projectIdR',null,['id'=>'projectIdR']) !!}
                                              {!! Form::hidden('regIdFk',null,['id'=>'regIdFk']) !!}



                                            </div>

                                        </div>

                                        <div class="form-group">
                                               {!! Form::label('projectType', 'Project Type', ['class' => 'col-sm-4 control-label','style'=>'font-size:14px']) !!}
                                            <div class="col-sm-8">
                                              {!! Form::text('projectTypeR',null, ['class'=>'form-control', 'id' => 'projectTypeR','style'=>'font-size:12px !important','readonly']) !!}
                                              {!! Form::hidden('projectTypeIdHiddenR',null,['id'=>'projectTypeIdHiddenR']) !!}

                                            </div>
                                        </div>

                                        <div class="form-group">
                                           {!! Form::label('advRegType', 'Advance Type', ['class' => 'col-sm-4 control-label','style'=>'font-size:14px']) !!}
                                            <div class="col-sm-8">

                                                  {!! Form::text('advRegTypeR',null, ['class'=>'form-control', 'id' => 'advRegTypeR','readonly','style'=>'font-size:12px !important',]) !!}
                                                  {!! Form::hidden('advRegTypeIdHiddenR',null,['id'=>'advRegTypeIdHiddenR']) !!}
                                            </div>
                                        </div>








                                        <div class="form-group">
                                            {!! Form::label('advRegName', 'Employee Name', ['class' => 'col-sm-4 control-label', 'style'=>'font-size:14px']) !!}

                                          <div class="col-sm-8" style="padding-bottom:10px;">


                                              {!! Form::text('advRegName', null,['class'=>'form-control','id' => 'advRegName','readonly','style'=>'font-size:12px !important','style'=>'cursor:pointer']) !!}
                                              {!! Form::hidden('advRegNameIdHiddenR',null,['id'=>'advRegNameIdHiddenR']) !!}
                                              {!! Form::hidden('advRegNameTypeIdHiddenR',null,['id'=>'advRegNameIdHiddenR']) !!}



                                          </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('advRegNameId', 'Employee Id:', ['class' => 'col-sm-4 control-label','id' => 'advRegNameIdL', 'style'=>'font-size:14px']) !!}

                                          <div class="col-sm-8" style="padding-bottom:10px;">


                                              {!! Form::text('advRegNameId', null,['class'=>'form-control','id' => 'advRegNameId','readonly','style'=>'font-size:12px !important','style'=>'cursor:pointer']) !!}




                                          </div>
                                        </div>
                                        <!--hidden files here!-->
                                        <div class="form-group">
                                            <div class="col-sm-8">
                                                  {!! Form::hidden('advReceiveNumber',$advReceiveNumber, ['class'=>'form-control', 'id' => 'advReceiveNumber','readonly']) !!}
                                                <p id='EMmemberNamee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">

                                            <div class="col-sm-8">
                                                  {!! Form::hidden('advPaymentIdR',null, ['class'=>'form-control', 'id' => 'advPaymentIdR','readonly']) !!}
                                                  {!! Form::hidden('advPaymentIdHiddenR',null,['id'=>'advRegTypeIdHiddenR']) !!}

                                            </div>
                                        </div>

                                        <div class="form-group">


                                          <div class="col-sm-8">
                                                {!! Form::hidden('serviceCatagoryR',null, ['class'=>'form-control', 'id' => 'serviceCatagoryR','readonly']) !!}
                                                {!! Form::hidden('serviceCatagoryIdHiddenR',null,['id'=>'serviceCatagoryIdHiddenR']) !!}
                                          </div>

                                        </div>
                                              <!--hidden files end here!-->






                                    </div>{{--form-horizontal form-groups--}}
                                </div>{{--End 1st col-md-6--}}

                                <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                      <div class="form-group">
                                          {!! Form::label('paymentDate', 'Receive Date', ['class' => 'col-sm-4 control-label','style'=>'font-size:14px']) !!}

                                          <div class="col-sm-8" style="padding-bottom:5px;">
                                              {!! Form::text('paymentDate', null,['class'=>'form-control','id' => 'paymentDateR','readonly','style'=>'font-size:12px !important','style'=>'cursor:pointer']) !!}


                                          </div>
                                      </div>



                                      <div class="form-group">
                                          <div class="col-sm-4" style="font-size:15px">Receive Type</div>

                                          <div class="col-sm-8" style="padding-bottom:3px;">
                                              <input type="radio" name="advReceiveChange" value="a" id="cash" style="font-size:10px"/>Cash
                                              <input type="radio" name="advReceiveChange" value="b" id="vauchar" style="font-size:10px"/>Voucher
                                              <input type="radio" name="advReceiveChange" value="c" id="bank" style="font-size:10px"/>Bank

                                              <p id='advReceiveChangee' class="error" style="max-height:3px;color: red;"></p>
                                          </div>
                                      </div>


                                     <div class="form-group" id="cash2" style="display:none;">
                                         {!! Form::label('cash', 'Cash', ['class' => 'col-sm-4 control-label','style'=>'font-size:14px']) !!}

                                         <div class="col-sm-8">

                                             @php
                                                 $cash = DB::table('acc_account_ledger')->select('id','name')->where('accountTypeId',4)->get();
                                             @endphp
                                             <select name="cachfield" class="form-control input-sm" id="cachfield">
                                                 <option value="">Select Name</option>
                                                 @foreach($cash as $cashname)
                                                     <option value="{{$cashname->id}}">{{$cashname->name}}</option>
                                                 @endforeach
                                             </select>
                                             <p id='cashOn' class="error" style="max-height:3px;color: red;"></p>
                                         </div>
                                     </div>


                                     <div class="form-group" id="vauchar2" style="display:none;">
                                         {!! Form::label('vauchar', 'Vauchar Id', ['class' => 'col-sm-4 control-label','style'=>'font-size:14px']) !!}

                                         <div class="col-sm-8">
                                             {!! Form::text('vauchar', null, ['class'=>'form-control', 'id' => 'vauchar']) !!}
                                              <p id='vauchare' class="error" style="max-height:3px;color: red;"></p>

                                          </div>
                                     </div>

                         <div class="form-group" id="bank2" >
                             {!! Form::label('bank', 'Bank Name', ['class' => 'col-sm-4 control-label','style'=>'font-size:14px']) !!}

                             <div class="col-sm-8" style="padding-bottom:10px;">
                                 @php
                                     $bank = DB::table('acc_account_ledger')->select('id','name')->where('accountTypeId',5)->get();
                                 @endphp
                                 <select name="bank" class="form-control input-sm" id="bank">
                                     <option value=""> At First Select Bank Name</option>
                                     @foreach($bank as $bank)
                                        <option value="{{$bank->id}}">{{$bank->name}}</option>
                                     @endforeach
                                 </select>

                             </div>
                         </div>

                         <div class="form-group">
                             {!! Form::label('advChequeNumberL', 'Cheque No:', ['class' => 'col-sm-4 control-label','id' => 'advChequeNumberL','style'=>'font-size:14px']) !!}

                             <div class="col-sm-8" style="padding-bottom:10px;">
                                 {!! Form::text('advChequeNumber',null, ['class'=>'form-control', 'id' => 'advChequeNumber','autocomplete'=>'off']) !!}

                             </div>
                         </div>




                         <div class="form-group">
                             {!! Form::label('advReceiveAmount', 'Amount', ['class' => 'col-sm-4 control-label','style'=>'font-size:14px']) !!}

                             <div class="col-sm-8" style="padding-bottom:10px;">
                                 {!! Form::text('advReceiveAmount',null, ['class'=>'form-control', 'id' => 'advReceiveAmount','autocomplete'=>'off']) !!}

                             </div>
                         </div>










                                    </div>
                                </div>{{--End 2nd col-md-6--}}
                            </div>

                        </div>{{--row--}}


                        {{-- Edit ModalFooter--}}
                        <div class="modal-footer">

                            <button id="updateButton" class="btn actionBtn glyphicon glyphicon-check btn-success" type="submit" ><span> Update</span></button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span id=""> Close</span></button>

                        </div>
                        {!! Form::close() !!}


                    </div> {{-- End View Modal Body--}}

                </div>
            </div>
        </div>
        {{-- End Edit Modal --}}









<!-- - - - - - - - - - - -Delete Model- - - - - - - - - - - - - -->

   <div id="deleteModal" class="modal fade" style="margin-top:3%;">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Advance Register</h4>
            </div>
            <div class="modal-body ">
            <div class="row" style="padding-bottom:20px;"> </div>

            <h2>Are You Confirm to Delete This Record?</h2>

             <div class="modal-footer">
                 <input id="DMadvRegId" type="hidden" name="houseOwnerReg" value=""/>
                 <button type="button" class="btn btn-danger"  id="DMadvRegee"  data-dismiss="modal">confirm</button>

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
        if(hasAccess('deleteadvanceRegister')){
        $("#DMadvRegId").val($(this).attr('advanceReg'));
          $("#deleteModal").find('.modal-dialog').css('width', '60%');
          $('#deleteModal').modal('show');
        }
      });
      $(document).on('click', '.receive-modal', function(){

          $('#receiveModal').modal('show');
          $("#vauchar2").hide();
          $("#bank2").hide();
          $("#cash2").hide();
          $("#advChequeNumber").hide();
          $("#advChequeNumberL").hide();
          $("#advRegNameId").hide();
          $("#advRegNameIdL").hide();
          var advanceRegIdR=$(this).attr('advanceReg');
          $('#regIdFk').val(advanceRegIdR);
          var csrf = "{{csrf_token()}}";

          $.ajax({

             type: 'post',
             url: './viewAdvRegisterListReceiveModal',
             data:{advanceRegIdR:advanceRegIdR,_token: csrf},
             dataType: 'json',
             success: function(data) {
               console.log(data);
            $('#advRegName').val(data['result']);
            $('#advRegNameTypeIdHiddenR').val(data['$serviceCatagoryId']);
            if(data['serviceCatagoryId'] == 3)
            {
              $("#advRegNameId").show();
              $("#advRegNameIdL").show();
              $('#advRegNameId').val(data['employeeId']);
            }


            $('#advRegNameIdHiddenR').val(data['resultId']);

            $('#advReceiveAmount').val(data['payableAmount']);


            $('#project').val(data['project'].name);
            $('#projectIdR').val(data['project'].id);


            $('#projectTypeR').val(data["projectType"].name);
            $('#projectTypeIdHiddenR').val(data["projectType"].id);

           $('#advRegTypeR').val(data["registerType"].name);
           $('#advRegTypeIdHiddenR').val(data["registerType"].id);

            $('#advPaymentIdR').val(data["paymentId"]);

            $('#serviceCatagoryR').val(data["serviceCatagory"]);
            $('#serviceCatagoryIdHiddenR').val(data["serviceCatagoryId"]);



             },
             error: function(data) {
               alert("error");
             }
        }); /*end Ajax*/
          function toDate(dateStr) {
              var parts = dateStr.split("-");
              return new Date(parts[2], parts[1] - 1, parts[0]);
           }

          $("#paymentDateR").datepicker({
              changeMonth: true,
              changeYear: true,
              yearRange : "2017:c",
              minDate: new Date(2017, 07 - 1, 01),
              maxDate: "dateToday",
              dateFormat: 'dd-mm-yy',
              onSelect: function () {

              }
           });

          $('#advReceiveAmount').on('input', function(event) {
             this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
          });


          $('input[name="advReceiveChange"]').change(function() {
             var receiveType = $("input[name='advReceiveChange']:checked").val();
             if(receiveType=='a'){
                $("#cash2").show();
                $("#vauchar2").hide();
                $("#bank2").hide();
                $("#advChequeNumber").hide();
                $("#advChequeNumberL").hide();
             }
             else if(receiveType=='b'){
               $("#vauchar2").show();
               $("#bank2").hide();
               $("#cash2").hide();
               $("#advChequeNumber").hide();
               $("#advChequeNumberL").hide();
            }
            else if(receiveType=='c'){
              $("#bank2").show();
              $("#advChequeNumber").show();
              $("#advChequeNumberL").show();

              $("#vauchar2").hide();
              $("#cash2").hide();
            }
          });




      });

        $("#DMadvRegee").on('click',  function(){
          var advanceReg= $("#DMadvRegId").val();
          var csrf = "{{csrf_token()}}";
            $.ajax({
              url: './deleteadvanceRegister',
              type: 'POST',
              dataType: 'json',
              data: {advanceRegId:advanceReg, _token:csrf},
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


           function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
           }
           $("#project").change(function(){
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
                  error: function(_response) {
                     alert("error");
                  }
                   });/*End Ajax*/
                });/*End Change Project*/

                /*Store Information*/
                /*Store Information*/
               $('form').submit(function(event) {
                   event.preventDefault();

                    $("#save").prop("disabled", true);
                       $.ajax({
                            url: './storeAdvanceReceive',
                            type: 'POST',
                            dataType: 'json',
                            async:false,
                            data: $('form').serialize(),
                            success: function( data ) {
                              toastr.success("Success", opts);
                              setTimeout(function(){
                                     location.reload();
                                        }, 1500);

                            },
                           error: function(_response) {
                              alert("error");
                           }
                       });



                      $(document).on('input','input',function() {
                            $(this).closest('div').find('p').remove();
                       });
                      $(document).on('change','select',function() {
                          $(this).closest('div').find('p').remove();
                      });
                      $(document).on('change','input:radio',function() {
                          $(this).closest('div').find('p').remove();
                       });
                  });




    });
    </script>


<!--                          Project Change                     -->

 <script>
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
<!-- - - - - - - - - - - - view Modal  Data - - - - - - - - -->

<script type="text/javascript">

    $(document).ready(function() {
       function formateDate(argument){
          var date = $.datepicker.parseDate('yy-mm-dd',argument);
          return $.datepicker.formatDate("dd-mm-yy", date);
       }
      $(document).on('click', '.view-modal', function()  {
        if(hasAccess('viewAdvRegData')){
          var advanceReg = $(this).attr('advanceReg');
          var csrf = "{{csrf_token()}}";
          $("#viewModal").find('.modal-dialog').css('width', '60%');
          $("#viewModal").modal('show');
          $("#VMadvReg").val(advanceReg);
          $.ajax({
              url: './viewAdvRegData',
              type: 'POST',
              dataType: 'json',
              data: {id:advanceReg , _token: csrf},
              success: function(data) {

                  $("#VMadvRegisterId").val(data['accAdvRegister'].advRegId);
                  $("#VMregType").val(data['advRegType']);
                  $("#VMproject").val(data['project']);
                  $("#VMprojectType").val(data['projectType']);
                  $("#MVchangePaymentType").val(data['paymentTypeName']);
                  if(data['employee']!='') {
                      $("#VMemployeeName").val(data['employee']);
                      $("#VMemployeeName1").show();
                  }
                  else {
                      $("#VMemployeeName1").hide();
                  }
                  if(data['supplier']!=null) {
                      $("#VMsupplierName").val(data['supplier']);
                      $("#VMsupplierName1").show();
                  }
                  else{
                      $("#VMsupplierName1").hide();
                  }
                  if(data['houseOwner']!=null) {
                      $("#VMhouseOwnerName").val(data['houseOwner']);
                      $("#VMhouseOwnerName1").show();
                  }
                  else {
                      $("#VMhouseOwnerName1").hide();
                   }
                  $("#VMamount").val(data['accAdvRegister'].amount);
                  $("#VMhpaymentDate").val (formateDate(data['accAdvRegister'].advPaymentDate));
                  },
                  error: function(argument) {
                    alert('response error');
                  }

           });
        }
       });
    });

    </script>

    <!-- Edit Data For view  -->

    <script type="text/javascript">

    $(document).ready(function() {

       function formateDate(argument){
            var date = $.datepicker.parseDate('yy-mm-dd',argument);
            return $.datepicker.formatDate("dd-mm-yy", date);
        }

      $(document).on('click', '.edit-modal', function()  {
        if(hasAccess('getAdvRegInfo')){
          var advanceReg = $(this).attr('advanceReg');
          var csrf = "{{csrf_token()}}";
          $("#EMadvReg").val(advanceReg);
           $.ajax({
             url: './getAdvRegInfo',
             type: 'POST',
             dataType: 'json',
             data: {id:advanceReg , _token: csrf},
             success: function(data){
                $("#EMadvRegisterId").val(data['accAdvRegister'].advRegId);
                $("#EMregType").val(data['advRegType']);
                $("#EMproject").val(data['project']);
                $("#EMprojectType").val(data['projectType']);
                $("#EMchangePaymentType").val(data['paymentTypeName']);
                if(data['employee']!='') {
                  $("#EMemployeeName").val(data['employee']);
                  $("#EMemployeeName1").show();
                }
                else{
                  $("#EMemployeeName1").hide();
                }
                if(data['supplier']!=null) {
                  $("#EMsupplierName").val(data['supplier']);
                  $("#EMsupplierName1").show();
                }
                else{
                  $("#EMsupplierName1").hide();
                }
                if(data['houseOwner']!=null) {
                  $("#EMhouseOwnerName").val(data['houseOwner']);
                  $("#EMhouseOwnerName1").show();
                }
                else {
                  $("#EMhouseOwnerName1").hide();
                }
                $("#EMamount").val(data['accAdvRegister'].amount);
                $("#EMhpaymentDate").val (formateDate(data['accAdvRegister'].advPaymentDate));

                $('#EMamount').keyup(function(){
                   var abc = parseFloat($(this).val());
                   var totalValue = data['payableAmount']+abc;

                   if (data['reciveAmount']>=totalValue){
                       alert("you can not give Amount less than "+data['reciveAmount']);
                    }
                });
                $("#editModal").find('.modal-dialog').css('width', '60%');
                $("#editModal").modal('show');
              },
              error: function(argument) {
               alert('response error');
              }
        });
         }
       });
    });

    </script>


    <script type="text/javascript">
        $(document).ready(function() {
           $('input:radio[name=paymentTypeChange]').change(function() {
                var paymentTypeChange = $('input[name=paymentTypeChange]:checked').val();
                var csrf = "{{csrf_token()}}";
                $.ajax({
                    type: 'post',
                    url: './paymentTypeChange',
                    data:{paymentTypeChange:paymentTypeChange,_token: csrf},
                    dataType: 'json',
                    async:false,
                    success: function(response) {
                        $("#changePaymentType").empty();
                        $("#changePaymentType").append('<option selected="selected" value="">Select Name</option>');

                        $.each(response, function(key,ob) {
                          $("#changePaymentType").append("<option   value='"+ob.id+"'>"+ob.name+"</option>");

                     });
                    },
                    error: function(_response)  {
                           alert("error");
                    }
                });
            });
        });

</script>

<!-- get data for Edit

    <script type="text/javascript">

    $(document).ready(function() {
         function formateDate(argument){
            var date = $.datepicker.parseDate('yy-mm-dd',argument);
            return $.datepicker.formatDate("dd-mm-yy", date);
         }
         $(document).on('click', '.edit-modal', function(){

               $("#advRegTypee").empty();
               $("#EMprojecte").empty();
               $("#EMprojectTypee").empty();
               $("#advRegNamee").empty();
               $("#advRegAmounte").empty();
               $("#paymentDatee").empty();

               $("#updateButton").prop("disabled",false);
                 var advanceReg = $(this).attr('advanceReg');
                 var csrf = "{{csrf_token()}}";

                 $("#DMadvReg").val(advanceReg);

         // alert(JSON.stringify(data));

                $.ajax({
                  url: './getAdvRegInfo',
                  type: 'POST',
                  dataType: 'json',
                  async:false,
                  data: {id:advanceReg, _token: csrf},
                  success: function(data) {
                      $("#EMadvRegId").val(data['accAdvRegister'].advRegId);
                      $("#EMproject").val(data['accAdvRegister'].projectId);
                      $("#EMprojectType").val(data['accAdvRegister'].projectTypeId);
                      $("#EMadvRegType").val(data['accAdvRegister'].advRegType);
                      $("#EMadvRegId").val(data['accAdvRegister'].advRegId);


                      if(data['accAdvRegister'].houseOwnerId>0) {
                            $('input[name=advRegChange][value=1]').attr('checked','checked');
                            $("#advRegName").show(data['accAdvRegister'].houseOwnerId);

                        }
                      else if(data['accAdvRegister'].supplierId>0) {
                            $('input[name=advRegChange][value=2]').attr('checked','checked');
                       }
                      else if(data['accAdvRegister'].employeeId>0) {
                           $('input[name=advRegChange][value=3]').attr('checked','checked');
                       }
                        var test = $('input[name=advRegChange]').trigger('change');
                        $('#advRegName').val(data['index']);

                      if(data['accAdvRegister'].cashId>0) {
                          $('input[name=paymentTypeChange][value=cash]').attr('checked','checked');
                       }
                      else if(data['accAdvRegister'].bankId>0) {
                          $('input[name=paymentTypeChange][value=bank]').attr('checked','checked');
                       }

                   var test = $('input[name=paymentTypeChange]').trigger('change');
                   //alert(data['ind']);
             $('#changePaymentType').val(data['ind']);
             //alert(JSON.stringify(data));
             $("#EMadvRegAmount").val(data['accAdvRegister'].amount);
             $("#EMpaymentDate").val(formateDate(data['accAdvRegister'].advPaymentDate));
             //alert(JSON.stringify(data['payableAmount']));

             $('#EMadvRegAmount').keyup(function(){
                var abc = parseFloat($(this).val());
                var totalValue = data['payableAmount']+abc;
                  //alert(abc);

                  if (data['reciveAmount']>=totalValue){
                     alert("Less Then numbers above"+data['totalValue']);
                     $(this).val(data['totalValue']);
                  }
              });

                   $("#editModal").find('.modal-dialog').css('width', '60%');
                   $("#editModal").modal('show');
                },
              error: function(argument) {
                 alert('response error');
                  }
             });
           });
     });

  </script> -->


<!--                                 Update Modal Data                          -->


<!-- - - - - - - - -       Edit for view data - - - - - - - - -->

    <script type="text/javascript">

      $(document).ready(function() {
           $('input:radio[name=advRegChange]').change(function()  {
             var advRegChange = $('input[name=advRegChange]:checked').val();
             var csrf = "{{csrf_token()}}";
             $.ajax({
                type: 'post',
                url: './advanceRegisterChange',
                data:{advRegChange:advRegChange,_token: csrf},
                dataType: 'json',
                async:false,
                success: function(response) {
                  $("#advRegName").empty();
                  $("#advRegName").append('<option  value="">Select Name</option>');
                  $.each(response, function(key,ob)   {
                    if(advRegChange==1) {
                       $("#advRegName").append("<option value='"+ob.id+"'>"+ob.houseOwnerName+"</option>");
                    }
                    else if(advRegChange==2) {
                       $("#advRegName").append("<option value='"+ob.id+"'>"+ob.supplierCompanyName+"</option>"
                       );
                    }
                    else if(advRegChange==3) {
                       $("#advRegName").append("<option value='"+ob.id+"'>"+ob.emp_id+"-"+ob.emp_name_english+"</option>"
                        );
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
  $("#updateButton").on('click', function() {
      $("#updateButton").prop("disabled", true);
          var advRegId = $("#EMadvReg").val();
          var advRegAmount = $("#EMamount").val();
          var paymentDate = $("#EMhpaymentDate").val();
          var csrf = "{{csrf_token()}}";
          $.ajax({
                url: './updateAdvRegInfo',
                type: 'POST',
                dataType: 'json',
                data: {id:advRegId,advRegAmount:advRegAmount,paymentDate:paymentDate,_token: csrf},
          })
          .done(function(data) {
              if (data.errors) {
                  $("#updateButton").prop("disabled", false);
                    if (data.errors['advRegType']) {
                        $("#advRegTypee").empty();
                        $("#advRegTypee").append('*'+data.errors['advRegType']);
                     }

                     if (data.errors['project']) {
                        $("#EMprojecte").empty();
                        $("#EMprojecte").append('*'+data.errors['project']);
                       }

                     if (data.errors['peojectType'])  {
                        $("#EMprojectTypee").empty();
                        $("#EMprojectTypee").append('*'+data.errors['peojectType']);
                      }
                     if (data.errors['advRegName'])  {
                        $("#advRegNamee").empty();
                        $("#advRegNamee").append('*'+data.errors['advRegName']);
                      }
                     if (data.errors['advRegAmount']) {
                        $("#advRegAmounte").empty();
                        $("#advRegAmounte").append('*'+data.errors['advRegAmount']);
                      }

                    if (data.errors['paymentDate']) {
                        $("#paymentDatee").empty();
                        $("#paymentDatee").append('*'+data.errors['paymentDate']);
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

            $(document).on('input','input',function() {
                 $(this).closest('div').find('p').remove();
             });
            $(document).on('change','select',function() {
               $(this).closest('div').find('p').remove();
            });
            $(document).on('change','radio',function() {
               $(this).closest('div').find('p').remove();
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
