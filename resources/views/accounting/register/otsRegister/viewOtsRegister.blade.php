@extends('layouts/acc_layout')
@section('title', '| OTS Register')
@section('content')

@php
  //var_dump($infos);
@endphp
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addOtsRegister/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add OTS</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">OTS LIST</font></h1>
        </div>

        <div class="panel-body panelBodyView">

        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#otsTable").dataTable().yadcf([

            ]);*/
            $("#otsTable").dataTable({


                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });


       });

          </script>
        </div>
          <table class="table table-striped table-bordered" id="otsTable" style="color: black;">
                    <thead>
                      <tr>
                        <th width="30">SL#</th>
                        <th>Opening Date</th>
                        <th>Name of Account Holder</th>
                        <th>Account Number</th>
                        <th>Effective Date</th>
                        <th>Branch Location</th>
                        <th>Period</th>
                        <th>Interest Rate (%)</th>
                        <th>Amount (Tk)</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>

                    </thead>
                    <tbody>
                     @foreach($infos as $index => $info)
                     @php
                        $branchName = DB::table('gnr_branch')->where('id',$info->branchId_fk)->value('name');
                        $natureOfPayment = DB::table('acc_ots_period')->where('id',$info->periodId_fk)->value('name');
                     @endphp
                     <tr>
                        <td>{{$index+1}}</td>
                        <td>{{date('d-m-Y',strtotime($info->openingDate))}}</td>
                        <td class="name">{{$info->name}}</td>
                        <td>{{$info->accNo}}</td>
                        <td>{{date('d-m-Y',strtotime($info->effectiveDate))}}</td>
                        <td>{{$branchName}}</td>
                        <td>{{$natureOfPayment}}</td>
                        <td>{{number_format($info->interestRate,2)}}</td>
                        <td class="amount">{{number_format($info->amount,2,'.',',')}}</td>
                        {{-- <td>

                        @if($info->status==0)
                           <button class="btn btn-info"  style="width: 2px; line-height: 2px; text-align: center; font-size: 12px;background-color: #8F493D !important;  border: 0px solid #525659; border-radius: 10px;" disabled>
                                <span><i class="fa fa-times" aria-hidden="true"></i></span>
                            </button>
                        @else
                            <button class="btn btn-info"  style="width: 2px; line-height: 2px; text-align: center; font-size: 12px;background-color: green !important;  border: 0px solid #525659; border-radius: 10px;" disabled>
                                <span><i class="fa fa-check" aria-hidden="true"></i></span>
                            </button>
                        @endif

                        </td> --}}



                         <td>

                        @if($info->status==0)
                          <span><i class="fa fa-times" aria-hidden="true" style="color:red;font-size: 1.3em;"></i></span>
                        @else
                            <span><i class="fa fa-check" aria-hidden="true" style="color:green;font-size: 1.3em;"></i></span>
                        @endif

                        </td>


                        <td>

                        <a href="javascript:;" class="view-modal" accId="{{$info->id}}">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                          </a>&nbsp;
                          <a href="javascript:;" class="edit-modal" accId="{{$info->id}}" @if($info->status==0)style="pointer-events: none;"@endif>
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;

                        <a href="javascript:;" class="delete-modal" accId="{{$info->id}}" @if($info->status==0)style="pointer-events: none;"@endif>
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
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Account Details</h4>
                    </div>
                    <div class="modal-body">

                        <div id="printingContent">
                        <div class="row" style="padding-bottom: 20px;">


                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                        <div class="form-group">
                                            {!! Form::label('memberName', 'Member Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('memberName', null,['id'=>'VMmemberName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('spouseOrFatherName', 'Spouse/Father Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('spouseOrFatherName', null,['id'=>'VMspouseOrFatherName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('nidNo', 'NID No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('nidNo', null,['id'=>'VMnidNo','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('mobileNo', 'Mobile No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('mobileNo', null,['id'=>'VMmobileNo','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>



                                        <div class="form-group">
                                            {!! Form::label('branchLocation', 'Branch Location:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('branchLocation', null,['id'=>'VMbranchLocation','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('employeeReference', 'Employee Reference:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('employeeReference', null,['id'=>'VMemployeeReference','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            {!! Form::label('certificateNo', 'Certificate No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                {!! Form::text('certificateNo', null,['id'=>'VMcertificateNo','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('status', 'Status:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                {!! Form::text('status', null,['id'=>'VMstatus','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                       {{--  <div class="form-group">
                                            {!! Form::label('address', 'Address:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                {!! Form::textarea('address', null,['id'=>'VMaddress','class'=>'form-control','type' => 'text','autocomplete'=>'off','rows' => 2,'readonly']) !!}
                                            </div>
                                        </div> --}}


                                    </div>{{--form-horizontal form-groups--}}
                                </div>{{--End 1st col-md-6--}}

                                <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">


                                        <div class="form-group">
                                            {!! Form::label('accNo', 'Account No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('accNo', null,['id'=>'VMaccNo','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('openingDate', 'Opening Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('openingDate', null,['id'=>'VMopeningDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('maturityDate', 'Maturity Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('maturityDate', null,['id'=>'VMmaturityDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('paymentNature', 'Payment Nature:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('paymentNature', null,['id'=>'VMpaymentNature','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('interestRate', 'Interest Rate:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('interestRate', null,['id'=>'VMinterestRate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('amount', 'Principal Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('amount', null,['id'=>'VMamount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('interestAmount', 'Interest Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('interestAmount', null,['id'=>'VMinterestAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('interestPaidAmount', 'Interest Paid Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('interestPaidAmount', null,['id'=>'VMinterestPaidAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                       {{--  <div class="form-group">
                                            {!! Form::label('dueAmount', 'Due Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('dueAmount', null,['id'=>'VMdueAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div> --}}

                                        <div class="form-group">
                                            {!! Form::label('payableAmount', 'Payable Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('payableAmount', null,['id'=>'VMpayableAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>



                                        {{-- <div class="form-group">
                                            {!! Form::label('openingBalance', 'Opening Balance:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('openingBalance', null,['id'=>'VMopeningBalance','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div> --}}





                                    </div>
                                </div>{{--End 2nd col-md-6--}}
                            </div>
                            </div>{{--row--}}
                            </div>{{-- End Print Div --}}


                        {{-- View ModalFooter--}}
                        <div class="modal-footer">
                             <button id="printButton" class="btn actionBtn glyphicon glyphicon-print btn-success" type="button"><span> Print</span></button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span> Close</span></button>
                        </div>


                    </div> {{-- End View Modal Body--}}

                </div>
            </div>
        </div>

        {{-- End View Modal --}}


{{-- Edit Modal --}}
        <div id="editModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update Account Details</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                        {!! Form::open(array('url' => 'editOts', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                        {!! Form::hidden('EMaccountId',null,['id'=>'EMaccountId']) !!}
                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                        <div class="form-group">
                                            {!! Form::label('memberName', 'Member Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('EMmemberName', null,['id'=>'EMmemberName','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                                <p id='EMmemberNamee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('spouseOrFatherName', 'Spouse/Father Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('EMspouseOrFatherName', null,['id'=>'EMspouseOrFatherName','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('nidNo', 'NID No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('EMnidNo', null,['id'=>'EMnidNo','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                                <p id='EMnidNoe' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('mobileNo', 'Mobile No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('EMmobileNo', null,['id'=>'EMmobileNo','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                                <p id='EMmobileNoe' style="max-height:3px;"></p>
                                            </div>
                                        </div>



                                        <div class="form-group">
                                            {!! Form::label('branchLocation', 'Branch Location:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                 @php
                                                  $branches = DB::table('gnr_branch')->select('id','name','branchCode')->get();
                                              @endphp
                                              <select id="EMbranchLocation" name="EMbranchLocation" class="form-control">
                                                  <option value="">Select Branch</option>
                                                  @foreach($branches as $branch)
                                                  <option value="{{$branch->id}}">{{str_pad($branch->branchCode,3,'0',STR_PAD_LEFT).'-'.$branch->name}}</option>
                                                  @endforeach
                                              </select>

                                              <p id='EMbranchLocatione' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('employeeReference', 'Employee Reference:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                        @php
                                            $employees = DB::table('hr_emp_general_info')->select('id','emp_id','emp_name_english')->get();
                                        @endphp
                                        <select id="EMemployeeReference" name="EMemployeeReference" class="form-control">
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $employee)
                                            <option value="{{$employee->id}}">{{$employee->emp_id.'-'.$employee->emp_name_english}}</option>
                                            @endforeach
                                        </select>

                                        <p id='EMemployeeReferencee' style="max-height:3px;"></p>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('EMcertificateNo', 'Certificate No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                {!! Form::text('EMcertificateNo', null,['id'=>'EMcertificateNo','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                                <p id='EMcertificateNoe' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        {{-- <div class="form-group">
                                            {!! Form::label('address', 'Address:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                {!! Form::textarea('address', null,['id'=>'EMaddress','class'=>'form-control','type' => 'text','autocomplete'=>'off','rows' => 2,'readonly']) !!}
                                            </div>
                                        </div> --}}


                                    </div>{{--form-horizontal form-groups--}}
                                </div>{{--End 1st col-md-6--}}

                                <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">


                                        <div class="form-group">
                                            {!! Form::label('accNo', 'Account No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('EMaccNo', null,['id'=>'EMaccNo','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                                <p id='EMaccNoe' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('paymentNature', 'Payment Nature:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                            @php
                                                $periods = DB::table('acc_ots_period')->select('name','id','interestRate','months')->get();
                                            @endphp
                                            <select id="EMperiod" name="EMperiod" class="form-control">
                                                <option value="" interestRate="0" months="0">Select Period</option>
                                                @foreach($periods as $period)
                                                <option value="{{$period->id}}" interestRate="{{number_format($period->interestRate,2)}}" months="{{$period->months}}">{{$period->name}}</option>
                                                @endforeach
                                            </select>
                                                <p id='EMperiode' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('interestRate', 'Interest Rate:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('interestRate', null,['id'=>'EMinterestRate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('amount', 'Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('EMamount', null,['id'=>'EMamount','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                                <p id='EMamounte' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('openingBalance', 'Opening Balance:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('EMopeningBalance', null,['id'=>'EMopeningBalance','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('openingDate', 'Opening Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('EMopeningDate', null,['id'=>'EMopeningDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly','style'=>'cursor:pointer']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('maturityDate', 'Maturity Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('EMmaturityDate', null,['id'=>'EMmaturityDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('EMeffectiveDate', 'Effective Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('EMeffectiveDate', null, ['class'=>'form-control', 'id' => 'EMeffectiveDate','readonly','style'=>'cursor:pointer']) !!}

                                            </div>
                                    </div>



                                    </div>
                                </div>{{--End 2nd col-md-6--}}
                            </div>

                        </div>{{--row--}}


                        {{-- Edit ModalFooter--}}
                        <div class="modal-footer">

                            <button id="updateButton" class="btn actionBtn glyphicon glyphicon-check btn-success" type="submit"><span> Update</span></button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span id=""> Close</span></button>

                        </div>
                        {!! Form::close() !!}


                    </div> {{-- End View Modal Body--}}

                </div>
            </div>
        </div>
        {{-- End Edit Modal --}}

{{-- Delete Modal --}}
  <div id="deleteModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
        </div>
        <div class="modal-body">
          <h2>Are You Confirm to Delete This Record?</h2>

          <div class="modal-footer">
            {{-- {!! Form::open(['url' => 'deleteOts/']) !!} --}}
            <input id="DMaccId" type="hidden" name="accId" value="">
            <button id="DMconfirmButton" type="button" class="btn btn-danger"> Confirm</button>
            <button  class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
            {{-- {!! Form::close() !!} --}}

          </div>

        </div>
      </div>
    </div>
  </div>
  {{-- End Delete Modal --}}



   <div style="display: none;text-align: center;" id="hiddenTitle">
   <h3 style="text-align: center;padding: 0px;margin: 0px;">Ambala Foundation</h3>
   <p style="text-align: center;padding: 0px;margin: 0px;font-size: 10px;">House # 62, Block # Ka, Piciculture Housing Society, Shyamoli, Dhaka-1207</p>
   <br>
   {{-- <h4 style="text-align: center;padding: 0px;margin: 0px;">OTS Account Opening Report</h4>  --}}
    {{-- <h5 style="text-align: center;">{{$selectedBranchName}}</h5>  --}}
   <h5 style="text-align: center;font-size: 14;padding: 0px;margin: 0px;">OTS Account Details</h5>
   <div>
    <p style="font-size: 10px;text-align: right;"><span style="font-weight: bold;">Print Date:</span> {{date("d-m-Y h:i:sa")}}</p>
   </div>
   <br>
</div>





<script type="text/javascript">
  $(document).ready(function() {

    function num(argument){
        if(argument!=null){
            return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        else{
            return "null";
        }


}


    /*View Modal*/

 $(document).on('click', '.view-modal', function() {

    if(hasAccess('otsGetAccountInfo')){

      var accId = $(this).attr('accId');
      var csrf = "{{csrf_token()}}";
      $.ajax({
        url: './otsGetAccountInfo',
        type: 'POST',
        dataType: 'json',
        data: {accId: accId, _token: csrf},
        success: function(data) {

         $("#VMmemberName").val(data['memeber'].name);
         $("#VMspouseOrFatherName").val(data['memeber'].spouseOrFatherName);
         $("#VMnidNo").val(data['memeber'].nidNo);
         $("#VMmobileNo").val(data['memeber'].mobileNo);
         $("#VMbranchLocation").val(data['branchName']);
         $("#VMemployeeReference").val(data['employeeName']);

         $("#VMcertificateNo").val(data['account'].certificateNo);

         $("#VMaccNo").val(data['account'].accNo);
         $("#VMpaymentNature").val(data['paymentNature']);
         $("#VMinterestRate").val(num(data['account'].interestRate));

         $("#VMamount").val(num(data['account'].amount));
         $("#VMinterestAmount").val(num(data['totalInterests']));
         $("#VMinterestPaidAmount").val(num(data['totalPayments']));
         $("#VMpayableAmount").val(num(data['payableAmount']));

         $("#VMopeningDate").val(data['openingDate']);
         $("#VMmaturityDate").val(data['matureDate']);

         if (data['account'].status==1) {
            $("#VMstatus").val("Active");
         }
         else{
            $("#VMstatus").val("Closed");
         }


         $("#viewModal").find('.modal-dialog').css("width","70%");
         $("#viewModal").modal('show');

        },
        error: function(argument) {
          alert('response error');
        }
      });
    }

    });
    /*End View Modal*/

    $('#viewModal').on('hidden.bs.modal', function () {
        $("#hiddenTitle").hide();
    });




    /*Filter Employee Base on Branch*/
        $("#EMbranchLocation").change(function() {
            var branchId = $("#EMbranchLocation option:selected").val();

            var csrf = "{{csrf_token()}}";
            $.ajax({
                url: './getEmployeeBaseOnBranch',
                type: 'POST',
                dataType: 'json',
                data: {branchId: branchId, _token:csrf},
                success: function(employee) {
                    //alert(JSON.stringify(employee));
                    $("#EMemployeeReference").empty();
                    $('#EMemployeeReference').append("<option value=''>Select Employee</option>");
                    $.each(employee, function(index, emp) {
                         $('#EMemployeeReference').append("<option value='"+ emp.id+"'>"+emp.emp_id+"-"+emp.emp_name_english+"</option>");
                    });

                },
                error: function(argument) {
                    alert('response error')
                }
            });


        });
        /*End Filter Employee Base on Branch*/


    /*Edit Modal*/

        $(document).on('click', '.edit-modal', function() {
        if(hasAccess('otsGetAccountInfoToUpdate')){

         /*Hide the error if filed is filed*/

        $('#EMmemberNamee').hide();
        $('#EMspouseOrFatherNamee').hide();
        $('#EMnidNoe').hide();
        $('#EMmobileNoe').hide();
        $('#EMaccNoe').hide();
        $('#EMamounte').hide();
        $('#EMdateFrome').hide();


        $('select').on('change', function () {
        var branchLocation = $("#EMbranchLocation").val();
        if(branchLocation){$('#EMbranchLocatione').hide();}else{$('#EMbranchLocatione').show();}

        var employeeReference = $("#EMemployeeReference").val();
        if(employeeReference){$('#EMemployeeReferencee').hide();}else{$('#EMemployeeReferencee').show();}

        var period = $("#EMperiod").val();
        if(period){$('#EMperiode').hide();}else{$('#EMperiode').show();}

        });
        /*End Hide the error if filed is filed*/




      var accId = $(this).attr('accId');
      var csrf = "{{csrf_token()}}";
      $.ajax({
        url: './otsGetAccountInfoToUpdate',
        type: 'POST',
        dataType: 'json',
        data: {accId: accId, _token: csrf},
        success: function(data) {
          $("#EMbranchLocation").val(data['memeber'].branchId_fk);
          $("#EMemployeeReference").val(data['memeber'].employeeId_fk);
          $("#EMbranchLocation").trigger('change');

         $("#EMaccountId").val(accId);
         $("#EMmemberName").val(data['memeber'].name);
         $("#EMspouseOrFatherName").val(data['memeber'].spouseOrFatherName);
         $("#EMnidNo").val(data['memeber'].nidNo);
         $("#EMmobileNo").val(data['memeber'].mobileNo);
         //$("#EMemployeeReference").val(data['memeber'].employeeId_fk).attr("selected", "selected");


         $("#EMaccNo").val(data['account'].accNo);
         $("#EMcertificateNo").val(data['account'].certificateNo);
         $("#EMperiod").val(data['account'].periodId_fk);
         $("#EMinterestRate").val(data['account'].interestRate);
         $("#EMamount").val(data['account'].amount);
         $("#EMopeningBalance").val(data['account'].openingBalance);
         $("#EMopeningDate").val(data['openingDate']);
         $("#EMmaturityDate").val(data['matureDate']);
         $("#EMeffectiveDate").val(data['effectiveDate']);


         $("#editModal").modal('show');

        },
        error: function(argument) {
          alert('response error');
        }
      });

      }
    });
    /*End Edit Modal*/



    /*Delete Modal*/

      $(document).on('click', '.delete-modal', function() {
      if(hasAccess('deleteOts')){
      $("#DMaccId").val($(this).attr('accId'));
      $("#deleteModal").modal('show');

      }
    });
    /*End Delete Modal*/

    /*Delete The record*/
    $("#DMconfirmButton").on('click',  function() {
        var accId = $("#DMaccId").val();
        var csrf = "{{csrf_token()}}";

        $.ajax({
            url: './deleteOts',
            type: 'POST',
            dataType: 'json',
            data: {accId: accId, _token:csrf},
        })
        .done(function() {
            location.reload();
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });


    });
    /*End Delete The record*/


 function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

        function GetFormattedDate(CurrentDate) {
            var date = new Date(CurrentDate);
            return( pad(date.getDate(),2) + '-'+ pad((date.getMonth() + 1),2) +'-' +  date.getFullYear());
        }


        /*Calculate Mature Date*/

       $("#EMperiod").on('change',  function() {
            // change interest rate
            $("#EMinterestRate").val($("#EMperiod option:selected").attr('interestRate'));

            // chnage period length
            var months = parseInt($("#EMperiod option:selected").attr('months'));
            var dateFrom = $("#EMopeningDate").val();

            if (dateFrom!="") {
                parts = dateFrom.split("-");
                var CurrentDate = new Date(parts[2],parts[1]-1,parts[0]);
                CurrentDate.setMonth(CurrentDate.getMonth() + months);
                $("#EMmaturityDate").val(GetFormattedDate(CurrentDate));
            }

       });

        /*End Calculate Mature Date*/

    /*Date From*/
         $("#EMopeningDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1995:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                // $("#dateFrome").hide();

                  var months = parseInt($("#EMperiod option:selected").attr('months'));
                   var dateFrom = $("#EMopeningDate").val();

                    parts = dateFrom.split("-");
                    var CurrentDate = new Date(parts[2],parts[1]-1,parts[0]);
                    var CurrentDate2 = new Date(parts[2],parts[1]-1,parts[0]);
                    //$("#EMeffectiveDate").datepicker('option','minDate',CurrentDate2);
                    CurrentDate.setMonth(CurrentDate.getMonth() + months);
                    $("#EMmaturityDate").val(GetFormattedDate(CurrentDate));
                }

        });
        /*End Date From*/

        /*Effective Date*/
         $("#EMeffectiveDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1995:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy'
        });
        /*End Effective Date*/



        /*Validate Update*/
       /* $("#updateButton").click(function(event) {
            event.preventDefault();
        });*/
        /*End Validate Update*/


         /*Submit the form*/
        $("form").submit(function(event) {

            event.preventDefault();

            $.ajax({
                 type: 'post',
                 url: './editOts',
                 data: $('form').serialize(),
                 dataType: 'json',
                success: function( _response ){

                 if (_response.errors) {
                    if (_response.errors['EMmemberName']) {
                        $('#EMmemberNamee').empty();
                        $('#EMmemberNamee').show();
                        $('#EMmemberNamee').append('<span class="errormsg" style="color:red;">'+_response.errors.EMmemberName+'</span>');
                        //return false;
                    }
                    if (_response.errors['EMspouseOrFatherName']) {
                        $('#EMspouseOrFatherNamee').empty();
                        $('#EMspouseOrFatherNamee').show();
                        $('#EMspouseOrFatherNamee').append('<span class="errormsg" style="color:red;">'+_response.errors.EMspouseOrFatherName+'</span>');
                        //return false;
                    }
                    if (_response.errors['EMnidNo']) {
                        $('#EMnidNoe').empty();
                        $('#EMnidNoe').show();
                        $('#EMnidNoe').append('<span class="errormsg" style="color:red;">'+_response.errors.EMnidNo+'</span>');
                        //return false;
                    }
                    if (_response.errors['EMmobileNo']) {
                        $('#EMmobileNoe').empty();
                        $('#EMmobileNoe').show();
                        $('#EMmobileNoe').append('<span class="errormsg" style="color:red;">'+_response.errors.EMmobileNo+'</span>');
                        //return false;
                    }
                    if (_response.errors['EMbranchLocation']) {
                        $('#EMbranchLocatione').empty();
                        $('#EMbranchLocatione').show();
                        $('#EMbranchLocatione').append('<span class="errormsg" style="color:red;">'+_response.errors.EMbranchLocation+'</span>');
                        //return false;
                    }
                    if (_response.errors['EMemployeeReference']) {
                        $('#EMemployeeReferencee').empty();
                        $('#EMemployeeReferencee').show();
                        $('#EMemployeeReferencee').append('<span class="errormsg" style="color:red;">'+_response.errors.EMemployeeReference+'</span>');
                        //return false;
                    }
                    if (_response.errors['EMaccNo']) {
                        $('#EMaccNoe').empty();
                        $('#EMaccNoe').show();
                        $('#EMaccNoe').append('<span class="errormsg" style="color:red;">'+_response.errors.EMaccNo+'</span>');
                        //return false;
                    }
                    if (_response.errors['EMcertificateNo']) {
                        $('#EMcertificateNoe').empty();
                        $('#EMcertificateNoe').show();
                        $('#EMcertificateNoe').append('<span class="errormsg" style="color:red;">'+_response.errors.EMcertificateNo+'</span>');
                        //return false;
                    }
                    if (_response.errors['EMperiod']) {
                        $('#EMperiode').empty();
                        $('#EMperiode').show();
                        $('#EMperiode').append('<span class="errormsg" style="color:red;">'+_response.errors.EMperiod+'</span>');
                        //return false;
                    }
                    if (_response.errors['EMamount']) {
                        $('#EMamounte').empty();
                        $('#EMamounte').show();
                        $('#EMamounte').append('<span class="errormsg" style="color:red;">'+_response.errors.EMamount+'</span>');
                        //return false;
                    }
                    if (_response.errors['EMopeningDate']) {
                        $('#EMopeningDatee').empty();
                        $('#EMopeningDatee').show();
                        $('#EMopeningDatee').append('<span class="errormsg" style="color:red;">'+_response.errors.EMopeningDate+'</span>');
                        //return false;
                    }

            } else {

                    window.location.href = '{{url('otsRegisterList/')}}';
                    }
                },
                error: function( data ){
                    // Handle error
                    //alert(_response.errors);
                    alert('error');

                }
            });
        });
        /*End Submit the form*/




        /*Hide the error if filed is filed*/
        $("input").keyup(function(){
        var memberName = $("#EMmemberName").val();
        if(memberName){$('#EMmemberNamee').hide();}else{$('#EMmemberNamee').show();}

        var spouseOrFatherName = $("#EMspouseOrFatherName").val();
        if(spouseOrFatherName){$('#EMspouseOrFatherNamee').hide();}else{$('#EMspouseOrFatherNamee').show();}

        var nidNo = $("#EMnidNo").val();
        if(nidNo){$('#EMnidNoe').hide();}else{$('#EMnidNoe').show();}

        var mobileNo = $("#EMmobileNo").val();
        if(mobileNo){$('#EMmobileNoe').hide();}else{$('#EMmobileNoe').show();}

        var accNo = $("#EMaccNo").val();
        if(accNo){$('#EMaccNoe').hide();}else{$('#EMaccNoe').show();}

        var certificateNo = $("#EMcertificateNo").val();
        if(certificateNo.length<=11){$('#EMcertificateNoe').hide();}else{$('#EMcertificateNoe').show();}

        var amount = $("#EMamount").val();
        if(amount){$('#EMamounte').hide();}else{$('#EMamounte').show();}

        var dateFrom = $("#EMdateFrom").val();
        if(dateFrom){$('#EMdateFrome').hide();}else{$('#EMdateFrome').show();}

        });



        $('select').on('change', function () {
        var branchLocation = $("#EMbranchLocation").val();
        if(branchLocation){$('#EMbranchLocatione').hide();}else{$('#EMbranchLocatione').show();}

        var employeeReference = $("#EMemployeeReference").val();
        if(employeeReference){$('#EMemployeeReferencee').hide();}else{$('#EMemployeeReferencee').show();}

        var period = $("#EMperiod").val();
        if(period){$('#EMperiode').hide();}else{$('#EMperiode').show();}

        });
        /*End Hide the error if filed is filed*/



        /*Validate Number Filed*/
        $("#EMamount,#EMnidNo,#EMmobileNo,#EMopeningBalance,#EMcertificateNo").on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        /*End Validate Number Filed*/


        /*Print Document*/
        $("#printButton").click(function() {

  /*Make Print Table*/
            tableMarkup = "<table id='printingTable' style='color: black;font-size:11px;border-collapse: collapse;margin-bottom:100px;' border='1px solid black;'>"+
"   <tbody>"+
"       <tr>"+
"           <td width='25%'>Account Name:</td>"+
"           <td width='25%'>"+$('#VMmemberName').val()+"</td>"+
"           <td width='25%'>Opening Date:</td>"+
"           <td width='25%'>"+$('#VMopeningDate').val()+"</td>"+
"       </tr>"+


"       <tr>"+
"           <td width='25%'>Account No:</td>"+
"           <td width='25%'>"+$('#VMaccNo').val()+"</td>"+
"           <td width='25%'>Maturity Date:</td>"+
"           <td width='25%'>"+$('#VMmaturityDate').val()+"</td>"+
"       </tr>"+

"       <tr>"+
"           <td width='25%'>Spouse/Father Name:</td>"+
"           <td width='25%'>"+$('#VMspouseOrFatherName').val()+"</td>"+
"           <td width='25%'>Payment Nature:</td>"+
"           <td width='25%'>"+$('#VMpaymentNature').val()+"</td>"+
"       </tr>"+

"       <tr>"+
"          <td width='25%'>NID No:</td>"+
"           <td width='25%'>"+$('#VMnidNo').val()+"</td>"+
"          <td width='25%'>Interest Rate:</td>"+
"           <td width='25%'>"+$('#VMinterestRate').val()+"</td>"+
"       </tr>"+

"       <tr>"+
"         <td width='25%'>Mobile No:</td>"+
"           <td width='25%'>"+$('#VMmobileNo').val()+"</td>"+
"            <td width='25%'>Principal Amount (TK):</td>"+
"           <td width='25%'>"+$('#VMamount').val()+"</td>"+
"       </tr>"+
"       <tr>"+
"           <td width='25%'>Branch Location:</td>"+
"           <td width='25%'>"+$('#VMbranchLocation').val()+"</td>"+
"           <td width='25%'>Interest Amount (TK):</td>"+
"           <td width='25%'>"+$('#VMinterestAmount').val()+"</td>"+
"       </tr>"+

"       <tr>"+
"           <td width='25%'>Certificate No:</td>"+
"           <td width='25%'>"+$('#VMcertificateNo').val()+"</td>"+
"          <td width='25%'>Interest Paid Amount (TK):</td>"+
"           <td width='25%'>"+$('#VMinterestPaidAmount').val()+"</td>"+
"       </tr>"+

"       <tr>"+
"           <td width='25%'>Status:</td>"+
"           <td width='25%'>"+$('#VMstatus').val()+"</td>"+
"           <td width='25%'>Payable Amount (TK):</td>"+
"           <td width='25%'>"+$('#VMpayableAmount').val()+"</td>"+
"       </tr>"+




"   </tbody>"+
"</table><br><br>";
            /*End Make Print Table*/


            var printStyle = '<style>#printingTable{float:left;height:auto;padding:0px;width:100%;font-size:16px;page-break-inside:auto;}  thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px} tr{ page-break-inside:avoid; page-break-after:auto }</style><style media="print">@page{size:landscape;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style><style></style>';

         printStyle = printStyle +  "<style>#printingTable tbody tr td{text-align: left; padding-left: 5px;}"+
     "#printingTable tbody tr td:nth-child(2){padding-right: 10px; border-right: 1px solid black;}"+
     "#printingTable tbody tr td:nth-child(1),#printingTable tbody tr td:nth-child(3){font-weight: bold;}"+
     "#printingTable tbody tr td{line-height:25px;}"+
 " @page {size: A5 landscape;}</style>";


            var footerContents = "<div class='row' style='font-size:12px;'>Prepared By <span style='display:inline-block; width: 36%;'></span> Checked By <span style='display:inline-block; width: 36%;'></span> Approved By</div>";

    $("#hiddenTitle").show();
    var titleDiv = document.getElementById("hiddenTitle").innerHTML;
     printContents = '<div id="order-details-wrapper">' + titleDiv + tableMarkup + footerContents +'</div>';

    var win = window.open('','printwindow');
    win.document.write(printContents+printStyle);
    win.print();
    win.close();

        });
        /*End Print Document*/








  });/*End Ready*/
</script>

<style type="text/css">
    #otsTable tr td.name{
        text-align: left;
        padding-left: 5px;
    }
    #otsTable tr td.amount{
        text-align: right;
        padding-right: 5px;
    }
    #printingTable tbody tr td:nth-child(1){
        text-align: left;
        padding-left: 5px;
    }
</style>


@include('dataTableScript')
@endsection
