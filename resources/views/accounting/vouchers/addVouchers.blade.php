@extends('layouts/acc_layout')
@section('title', '| New Voucher')
@section('content')
<?php
    $now = date('d-m-Y');
    $user = Auth::user();
    Session::put('branchId', $user->branchId);
    $branchId = Session::get('branchId');

    Session::put('id', $user->emp_id_fk);
    $userId = Session::get('id');
    //dd($userId);

    $branch = DB::table('gnr_branch')->where('id',$branchId)->select('name','companyId','branchCode')->first();
    //dd($branch);
    //dd($user->company_id_fk);
     
?>

<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('/viewVoucher')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Voucher List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs nav-tabs-justified"  style="padding: 0px 25px 0px 25px; ">
                                <li class="active" style="padding-right: 0px;">
                                    <a href="#debitTab" data-toggle="tab">
                                        <span class="visible-xs"><i class="fa-envelope-o"></i></span>
                                        <span class="hidden-xs"><strong>Debit Voucher</strong></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#creditTab" data-toggle="tab">
                                        <span class="visible-xs"><i class="fa-cog"></i></span>
                                        <span class="hidden-xs"><strong>Credit Voucher</strong></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#jounalVoucherTab" data-toggle="tab" id="jounnalVoucher">
                                        <span class="visible-xs"><i class="fa-home fa fa-briefcase"></i></span>
                                        <span class="hidden-xs"><strong>Journal Voucher</strong></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#contraVoucherTab" data-toggle="tab">
                                        <span class="visible-xs"><i class="fa-user fa fa-university"></i></span>
                                        <span class="hidden-xs"><strong>Contra voucher</strong></span>
                                    </a>
                                </li>
                                {{-- <li>
                                    <a href="#fundTransferTab" data-toggle="tab">
                                        <span class="visible-xs"><i class="fa-bell-o"></i></span>
                                        <span class="hidden-xs"><strong>Fund Transfer</strong></span>
                                    </a>
                                </li> --}}
                            </ul>

                            <div class="tab-content">
{{-- =======================================================Start DebitVoucher Form======================================================= --}}
                                <div class="tab-pane active" id="debitTab">
                                    <div class="panel-heading">
                                        <div class="panel-title col-md-4">Debit Voucher</div>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-4"><h4 style="color: black;"><?php echo "Total Amount: ";?><span id="totalAmountColumnPV">0.0</span><?php echo " Tk";?></h4></div>
                                        {{--<div class="col-md-1"></div>--}}
                                        <div class=" col-md-3"><h4 style="color: black;"><?php echo "Branch: ";?><strong >{{$branch->name}}</strong></h4></div>
                                    </div>
                                    <div class="row">
                                        {!! Form::open(array('url' => '', 'role' => 'form','enctype' => 'multipart/form-data', 'class'=>'form-horizontal form-groups')) !!}

                                        {{ Form::hidden('debitTab', 1, array('id' => 'debitTabValue')) }}

                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('projectIdPV', 'Project:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id = "projectIdPV" name="projectIdPV">
                                                                    <option value="">Select Project</option>
                                                                    @foreach($projects as $project)
                                                                        <option value="{{$project->id}}">{{$project->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <p id='projectIdPVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-3" hidden>
                                                        <div class="form-group">        {{--project type--}}
                                                            {!! Form::label('projectTypeIdPV', 'Project Type:', ['class' => 'col-sm-12 control-label']) !!}

                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id = "projectTypeIdPV" name="projectTypeIdPV">
                                                                    <option value="">Select Project Type</option>
                                                                    @foreach($projectTypes as $projectType)
                                                                        <option value="{{$projectType->id}}">{{$projectType->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <p id='projectTypeIdPVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('voucherDatePV', 'Voucher Date:', ['class' => 'col-sm-12 control-label']) !!}

                                                            <div class="col-sm-12">
                                                                {!! Form::text('voucherDatePV', null , ['class' => 'form-control softwareDate', 'id' => 'voucherDatePV'])!!}
                                                                <p id='voucherDatePVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3" id="">
                                                        <div class="form-group">
                                                            {!! Form::label('voucherCodePV', 'Voucher Code:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!! Form::text('voucherCodePV', $value = null, ['class' => 'form-control', 'id' => 'voucherCodePV', 'type' => 'text', 'disabled' => 'disabled']) !!}
                                                                <p id='voucherCodePVe' style="max-height:3px; color:red;"></p>
                                                                {{-- <p id='insertNewCollectionWrapper'></p> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('creditAccPV', 'Credit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id = "creditAccPV" name="creditAccPV">
                                                                    <option value="">Please Select Project First</option>
                                                                    {{-- @foreach($ledgersOfCashAndBank as $ledgerOfCashAndBank)
                                                                        <option value="{{$ledgerOfCashAndBank->id}}">{{$ledgerOfCashAndBank->name}}</option>
                                                                    @endforeach --}}
                                                                </select>
                                                                <p id='creditAccPVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('debitAccPV', 'Debit Account:', ['class' => 'col-sm-12 control-label']) !!}

                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id = "debitAccPV" name="debitAccPV">
                                                                    <option value="">Please Select Project First</option>
                                                                    {{-- <option value="">Select Debit Account</option>
                                                                    @foreach($ledgersOfAssetNLiabilityNCapitalFundNExpense as $ledgerOfAssetNLiabilityNCapitalFundNExpense)
                                                                        <option value="{{$ledgerOfAssetNLiabilityNCapitalFundNExpense->id}}">{{$ledgerOfAssetNLiabilityNCapitalFundNExpense->name}}</option>
                                                                    @endforeach --}}
                                                                </select>
                                                                <p id='debitAccPVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('amountPV', 'Amount:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!!  Form::text('amountPV', $value = null, ['class' => 'form-control', 'id' => 'amountPV', 'type' => 'text','min'=>'1', 'placeholder' => 'Enter Amount']) !!}
                                                                <p id='amountPVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('narrationPV', 'Narration/ Cheque Details:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!! Form::text('narrationPV', $value = null, ['class' => 'form-control', 'id' => 'narrationPV', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                                                <p id='narrationPVe' style="max-height:3px; color: red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div style="padding-right: 30px;">
                                                    <button class="btn btn-info" id="addPV" style="float: right; " type="button">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="padding-bottom: 20px">
                                            <div class="col-md-0"></div>
                                            <div class="col-md-12"  style="padding: 0px 45px 0px 45px; ">
                                                <table id="addPVTable" class="table table-striped table-bordered">
                                                    <thead>
                                                    <tr id="headerRowPV">
                                                        <th style="padding: 10px 5px; width: 30%;text-align:center;">Credit Account</th>
                                                        <th style="padding: 10px 5px; width: 30%;text-align:center;">Debit Account</th>
                                                        <th style="padding: 10px 5px; width: 12%;text-align:center;">Amount</th>
                                                        <th style="padding: 10px 5px; width: 20%;text-align:center;">Narration / Cheque Details</th>
                                                        <th style="padding: 10px 5px; width: 8%;text-align:center;">Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <th colspan="5"></th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <p id='tablePVe' style="max-height:3px; color: red;"></p>
                                            </div>
                                            <div class="col-md-0"></div>
                                        </div>
                                        <div class="col-md-12"></div>
                                        <div class="col-md-12" style="padding: 0px 30px 20px 30px; " >
                                            <div class="form-group">
                                                {!! Form::label('globalNarrationPV', 'Global Narration Details:', ['class' => 'col-sm-12 control-label']) !!}
                                                <div class="col-sm-12">
                                                    {!! Form::text('globalNarrationPV', $value = null, ['class' => 'form-control', 'id' => 'globalNarrationPV', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                                    <p id='globalNarrationPVe' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="col-md-12" style="padding: 0px 30px 20px 30px; " >
                                                <div class="form-group">
                                                    {!! Form::label('imagePV', 'Voucher Image', ['class' => 'col-sm-12 control-label']) !!}
                                                    <div class="col-sm-6">
                                                        <div id="addMorePVId" class="fetchPVData">
                                                            <input type="file" name="imagePV[]"  class="imageuploadPV chaneBeforeImagePV beforeImgInput">
                                                        </div>
                                                        <p id='imagePVe' style="max-height:3px;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12" style="padding: 0px 20px 15px 30px; ">
                                                 <p><input type="button" style="" name="addMorePV" id="addMorePV" class="btn btn-info" value="Add More"></p>
                                            </div>
                                        <div class="form-group" >
                                            {{-- {!! Form::label('submitPV', ' ', ['class' => 'col-sm-9 control-label']) !!} --}}
                                            {!! Form::label('submitPV', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9 text-right" style="padding-right: 45px;">
                                                <input type="button" name="" id="submitPV" class="btn btn-info" value="Submit">
                                                <input type="button" name="" id="submitNPrintPV" class="btn btn-success" value="Submit & Print">
                                                {{--{!! Form::submit('submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}--}}
                                                {{--{{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}--}}
                                                <a href="{{url('/viewVoucher')}}" class="btn btn-danger closeBtn">Close</a>
                                                <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>

{{-- ======================================End Debit Voucher Form====================================== --}}

{{-- ===================================================Start CreditVoucher Form=================================================== --}}

                                <div class="tab-pane" id="creditTab">
                                    <div class="panel-heading">
                                        <div class="panel-title col-md-4">Credit Voucher</div>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-4"><h4 style="color: black;"><?php echo "Total Amount: ";?><span id="totalAmountColumnRV">0.0</span><?php echo " Tk";?></h4></div>
                                        {{--<div class="col-md-1"></div>--}}
                                        <div class=" col-md-3"><h4 style="color: black;"><?php echo "Branch: ";?><strong >{{$branch->name}}</strong></h4></div>
                                    </div>

                                    <div class="row">
                                        {!! Form::open(array('url' => '', 'role' => 'form','enctype' => 'multipart/form-data', 'class'=>'form-horizontal form-groups')) !!}

                                        {{ Form::hidden('creditTab', 2, array('id' => 'creditTabValue')) }}

                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div>
                                                        @if (Session::has('responseText'))
                                                            <strong>Note!</strong> {!! Session::get('responseText') !!}
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('projectIdRV', 'Project:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id = "projectIdRV" name="projectIdRV">
                                                                    <option value="">Select Project</option>
                                                                    @foreach($projects as $project)
                                                                        <option value="{{$project->id}}">{{$project->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <p id='projectIdRVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
 
                                                    <div class="col-md-3" hidden>
                                                        <div class="form-group">        {{--project type--}}
                                                            {!! Form::label('projectTypeIdRV', 'Project Type:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id = "projectTypeIdRV" name="projectTypeIdRV">
                                                                    <option value="">Select Project Type</option>
                                                                    @foreach($projectTypes as $projectType)
                                                                        <option value="{{$projectType->id}}">{{$projectType->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <p id='projectTypeIdRVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('voucherDateRV', 'Voucher Date:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!! Form::text('voucherDateRV',  null, ['class' => 'form-control softwareDate', 'id' => 'voucherDateRV'])!!}
                                                                <p id='voucherDateRVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('voucherCodeRV', 'Voucher Code:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!! Form::text('voucherCodeRV', $value = null, ['class' => 'form-control', 'id' => 'voucherCodeRV', 'type' => 'text', 'disabled' => 'disabled']) !!}
                                                                <p id='voucherCodeRVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('debitAccRV', 'Debit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id="debitAccRV" name="debitAccRV">
                                                                    <option value="">Please Select Project First</option>
                                                                    {{-- <option value="">Select Debit Account</option>
                                                                    @foreach($ledgersOfCashAndBank as $ledgerOfCashAndBank)
                                                                        <option value="{{$ledgerOfCashAndBank->id}}">{{$ledgerOfCashAndBank->name}}</option>
                                                                    @endforeach --}}
                                                                </select>
                                                                <p id='debitAccRVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('creditAccRV', 'Credit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id = "creditAccRV" name="creditAccRV">
                                                                    <option value="">Please Select Project First</option>
                                                                   {{--  <option value="">Select Credit Account</option>
                                                                    @foreach($ledgersOfAssetNLiabilityNCapitalFundNIncome as $ledgerOfAssetNLiabilityNCapitalFundNIncome)
                                                                        <option value="{{$ledgerOfAssetNLiabilityNCapitalFundNIncome->id}}">{{$ledgerOfAssetNLiabilityNCapitalFundNIncome->name}}</option>
                                                                    @endforeach --}}
                                                                </select>
                                                                <p id='creditAccRVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('amountRV', 'Amount:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!!  Form::text('amountRV', $value = null, ['class' => 'form-control', 'id' => 'amountRV', 'type' => 'text','min'=>'1', 'placeholder' => 'Enter Amount']) !!}
                                                                <p id='amountRVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('narrationRV', 'Narration/ Cheque Details:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!! Form::text('narrationRV', $value = null, ['class' => 'form-control', 'id' => 'narrationRV', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                                                <p id='narrationRVe' style="max-height:3px; color: red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="form-group">
                                                <div style="padding-right: 30px;">
                                                    <button class="btn btn-info" id="addRV" style="float: right; " type="button">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="padding-bottom: 20px">
                                            <div class="col-md-0"></div>
                                            <div class="col-md-12" style="padding: 0px 45px 0px 45px; " >
                                                <table id="addRVTable" class="table table-striped table-bordered">
                                                    <thead>
                                                    <tr id="headerRowRV">
                                                        <th style="padding: 10px 5px; width: 30%; text-align:center;">Debit Account</th>
                                                        <th style="padding: 10px 5px; width: 30%; text-align:center;" >Credit Account</th>
                                                        <th style="padding: 10px 5px; width: 12%; text-align:center;">Amount</th>
                                                        <th style="padding: 10px 5px; width: 20%; text-align:center;">Narration / Cheque Details</th>
                                                        <th style="padding: 10px 5px; width: 8%; text-align:center;">Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <th colspan="5"></th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <p id='tableRVe' style="max-height:3px; color: red;"></p>
                                            </div>
                                            <div class="col-md-0"></div>
                                        </div>

                                        <div class="col-md-12"></div>
                                        <div class="col-md-12" style="padding: 0px 30px 20px 30px; " >
                                            <div class="form-group">
                                                {!! Form::label('globalNarrationRV', 'Global Narration Details:', ['class' => 'col-sm-12 control-label']) !!}
                                                <div class="col-sm-12">
                                                    {!! Form::text('globalNarrationRV', $value = null, ['class' => 'form-control', 'id' => 'globalNarrationRV', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                                    <p id='globalNarrationRVe' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="col-md-12" style="padding: 0px 30px 20px 30px; " >
                                            <div class="form-group">
                                                {!! Form::label('imageRV', 'Voucher Image', ['class' => 'col-sm-12 control-label']) !!}
                                                <div class="col-sm-6">
                                                    <div id="addMoreRVId" class="fetchRVData">
                                                        <input type="file" name="imageRV[]"  class="imageuploadRV chaneBeforeImageRV beforeImgInputRV">
                                                    </div>
                                                    <p id='imageRVe' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="padding: 0px 20px 15px 30px; ">
                                             <p><input type="button" style="" name="addMoreRV" id="addMoreRV" class="btn btn-info" value="Add More"></p>
                                        </div>

                                        <div class="form-group" >
                                            {!! Form::label('submitRV', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9 text-right" style="padding-right: 45px;">
                                                <input type="button" name="" id="submitRV" class="btn btn-info" value="Submit">
                                                <input type="button" name="" id="submitNPrintRV" class="btn btn-success" value="Submit & Print">
                                                {{--{!! Form::submit('submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}--}}
                                                {{--{{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}--}}
                                                <a href="{{url('/viewVoucher')}}" class="btn btn-danger closeBtn">Close</a>
                                                <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}
                                    </div>
                                </div>
{{-- =========================================================End CreditVoucher Form========================================================= --}}

{{-- ========================================================Start journalVoucher Form======================================================== --}}

                                <div class="tab-pane" id="jounalVoucherTab">
                                    <div class="panel-heading">
                                        <div class="panel-title col-md-4">Journal Voucher</div>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-4"><h4 style="color: black;"><?php echo "Total Amount: ";?><span id="totalAmountColumnJV">0.0</span><?php echo " Tk";?></h4></div>
                                        {{--<div class="col-md-1"></div>--}}
                                        <div class=" col-md-3"><h4 style="color: black;"><?php echo "Branch: ";?><strong >{{$branch->name}}</strong></h4></div>
                                    </div>

                                    <div class="row">
                                        {!! Form::open(array('url' => '', 'role' => 'form','enctype' => 'multipart/form-data', 'class'=>'form-horizontal form-groups')) !!}

                                        {{ Form::hidden('jounnalTab', 3, array('id' => 'jounalTabValue')) }}


                                        <div class="col-md-12">
                                            {{--                                {!! Form::open(array('url' => '', 'role' => 'form','enctype' => 'multipart/form-data', 'class'=>'form-horizontal form-groups')) !!}--}}

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div>
                                                        @if (Session::has('responseText'))
                                                            <strong>Note!</strong> {!! Session::get('responseText') !!}
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('projectIdJV', 'Project:', ['class' => 'col-sm-12 control-label']) !!}

                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id = "projectIdJV" name="projectIdJV">
                                                                    <option value="">Select Project</option>
                                                                    @foreach($projects as $project)
                                                                        <option value="{{$project->id}}">{{$project->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <p id='projectIdJVe' style="max-height:3px; color:red;"></p>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-3" hidden>
                                                        <div class="form-group">        {{--project type--}}
                                                            {!! Form::label('projectTypeIdJV', 'Project Type:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12" hidden>
                                                                <select class ="form-control" id = "projectTypeIdJV" name="projectTypeIdJV">
                                                                    <option value="">Select Project Type</option>
                                                                    @foreach($projectTypes as $projectType)
                                                                        <option value="{{$projectType->id}}">{{$projectType->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <p id='projectTypeIdJVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('voucherDateJV', 'Voucher Date:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!! Form::text('voucherDateJV', null, ['class' => 'form-control softwareDate', 'id' => 'voucherDateJV'])!!}
                                                                <p id='voucherDateJVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('voucherCodeJV', 'Voucher Code:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!! Form::text('voucherCodeJV', $value = null, ['class' => 'form-control', 'id' => 'voucherCodeJV', 'type' => 'text', 'disabled' => 'disabled']) !!}
                                                                <p id='voucherCodeJVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('debitAccJV', 'Debit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                <select class ="selectpicker form-control" id ="debitAccJV" name="debitAccJV">
                                                                    <option value="">Please Select Project First</option>
                                                                    {{-- <option value="">Select Debit Account</option>
                                                                    @foreach($ledgersOfAccountType as $ledgerOfAccountType)
                                                                        <option value="{{$ledgerOfAccountType->id}}">{{$ledgerOfAccountType->name}}</option>
                                                                    @endforeach --}}
                                                                </select>
                                                                <p id='debitAccJVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('creditAccJV', 'Credit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                <select class ="selectpicker form-control" id = "creditAccJV" name="creditAccJV">
                                                                    <option value="">Please Select Project First</option>
                                                                    {{-- <option value="">Select Credit Account</option>
                                                                    @foreach($ledgersOfAccountType as $ledgerOfAccountType)
                                                                        <option value="{{$ledgerOfAccountType->id}}">{{$ledgerOfAccountType->name}}</option>
                                                                    @endforeach --}}
                                                                </select>
                                                                <p id='creditAccJVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('amountJV', 'Amount:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!!  Form::text('amountJV', $value = null, ['class' => 'form-control', 'id' => 'amountJV', 'type' => 'text','min'=>'1', 'placeholder' => 'Enter Amount']) !!}
                                                                <p id='amountJVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('narrationJV', 'Narration/ Cheque Details:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!! Form::text('narrationJV', $value = null, ['class' => 'form-control', 'id' => 'narrationJV', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                                                <p id='narrationJVe' style="max-height:3px; color: red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="form-group">
                                                {{--                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-12 control-label']) !!}--}}

                                                <div style="padding-right: 30px;">
                                                    <button class="btn btn-info" id="addJV" style="float: right; " type="button">Add</button>

                                                </div>
                                            </div>

                                        </div>

                                        <div class="row" style="padding-bottom: 20px">
                                            <div class="col-md-0"></div>
                                            <div class="col-md-12" style="padding: 0px 45px 0px 45px; " >
                                                <table id="addJVTable" class="table table-striped table-bordered">
                                                    <thead>
                                                    <tr id="headerRowJV">
                                                        <th style="padding: 10px 5px; width: 30%; text-align:center;">Debit Account</th>
                                                        <th style="padding: 10px 5px; width: 30%; text-align:center;" >Credit Account</th>
                                                        <th style="padding: 10px 5px; width: 12%; text-align:center;">Amount</th>
                                                        <th style="padding: 10px 5px; width: 20%; text-align:center;">Narration / Cheque Details</th>
                                                        <th style="padding: 10px 5px; width: 8%; text-align:center;">Actions</th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    <tr>
                                                        <th colspan="5"></th>

                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <p id='tableJVe' style="max-height:3px; color: red;"></p>
                                            </div>
                                            <div class="col-md-0"></div>
                                        </div>

                                        <div class="col-md-12"></div>

                                        <div class="col-md-12" style="padding: 0px 30px 20px 30px; "  >
                                            <div class="form-group">
                                                {!! Form::label('globalNarrationJV', 'Global Narration Details:', ['class' => 'col-sm-12 control-label']) !!}
                                                <div class="col-sm-12">
                                                    {!! Form::text('globalNarrationJV', $value = null, ['class' => 'form-control', 'id' => 'globalNarrationJV', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                                    <p id='globalNarrationJVe' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="col-md-12" style="padding: 0px 30px 20px 30px; " >
                                            <div class="form-group">
                                                {!! Form::label('imageJV', 'Voucher Image', ['class' => 'col-sm-12 control-label']) !!}
                                                <div class="col-sm-6">
                                                    <div id="addMoreJVId" class="fetchJVData">
                                                        <input type="file" name="imageJV[]"  class="imageuploadJV chaneBeforeImageJV beforeImgInputJV">
                                                    </div>
                                                    <p id='imageJVe' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="padding: 0px 20px 15px 30px; ">
                                            <p><input type="button" style="" name="addMoreJV" id="addMoreJV" class="btn btn-info" value="Add More"></p>
                                        </div>


                                        <div class="form-group" >
                                            {!! Form::label('submitJV', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9 text-right" style="padding-right: 45px;">
                                                <input type="button" name="" id="submitJV" class="btn btn-info" value="Submit">
                                                <input type="button" name="" id="submitNPrintJV" class="btn btn-success" value="Submit & Print">
                                                {{--{!! Form::submit('submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}--}}
                                                {{--{{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}--}}
                                                <a href="{{url('/viewVoucher')}}" class="btn btn-danger closeBtn">Close</a>
                                                <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                </div>
{{-- ========================================================End journalVoucher Form======================================================== --}}

{{-- ===================================================Start contraVoucher Form=================================================== --}}

                                <div class="tab-pane" id="contraVoucherTab">
                                    <div class="panel-heading">
                                        <div class="panel-title col-md-4">Contra Voucher</div>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-4"><h4 style="color: black;"><?php echo "Total Amount: ";?><span id="totalAmountColumnCV">0.0</span><?php echo " Tk";?></h4></div>
                                        {{--<div class="col-md-1"></div>--}}
                                        <div class=" col-md-3"><h4 style="color: black;"><?php echo "Branch: ";?><strong >{{$branch->name}}</strong></h4></div>
                                    </div>

                                    <div class="row">
                                        {!! Form::open(array('url' => '', 'role' => 'form','enctype' => 'multipart/form-data', 'class'=>'form-horizontal form-groups')) !!}

                                        {{ Form::hidden('contraTab', 4, array('id' => 'contraTabValue')) }}

                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div>
                                                        @if (Session::has('responseText'))
                                                            <strong>Note!</strong> {!! Session::get('responseText') !!}
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('projectIdCV', 'Project:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id = "projectIdCV" name="projectIdCV">
                                                                    <option value="">Select Project</option>
                                                                    @foreach($projects as $project)
                                                                        <option value="{{$project->id}}">{{$project->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <p id='projectIdCVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3" hidden>
                                                        <div class="form-group">        {{--project type--}}
                                                            {!! Form::label('projectTypeIdCV', 'Project Type:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id = "projectTypeIdCV" name="projectTypeIdCV">
                                                                    <option value="">Select Project Type</option>
                                                                    @foreach($projectTypes as $projectType)
                                                                        <option value="{{$projectType->id}}">{{$projectType->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <p id='projectTypeIdCVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('voucherDateCV', 'Voucher Date:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!! Form::text('voucherDateCV',  null, ['class' => 'form-control softwareDate', 'id' => 'voucherDateCV'])!!}
                                                                <p id='voucherDateCVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('voucherCodeCV', 'Voucher Code:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!! Form::text('voucherCodeCV', $value = null, ['class' => 'form-control', 'id' => 'voucherCodeCV', 'type' => 'text', 'disabled' => 'disabled']) !!}
                                                                <p id='voucherCodeCVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('debitAccCV', 'Debit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id = "debitAccCV" name="debitAccCV">
                                                                    <option value="">Please Select Project First</option>
                                                                    {{-- <option value="">Select Debit Account</option>
                                                                    @foreach($ledgersOfCashAndBank as $ledgerOfCashAndBank)
                                                                        <option value="{{$ledgerOfCashAndBank->id}}">{{$ledgerOfCashAndBank->name}}</option>
                                                                    @endforeach --}}
                                                                </select>
                                                                <p id='debitAccCVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('creditAccCV', 'Credit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                <select class ="form-control" id = "creditAccCV" name="creditAccCV">
                                                                    <option value="">Please Select Project First</option>
                                                                    {{-- <option value="">Select Credit Account</option>
                                                                    @foreach($ledgersOfCashAndBank as $ledgerOfCashAndBank)
                                                                        <option value="{{$ledgerOfCashAndBank->id}}">{{$ledgerOfCashAndBank->name}}</option>
                                                                    @endforeach --}}
                                                                </select>
                                                                <p id='creditAccCVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('amountCV', 'Amount:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!!  Form::text('amountCV', $value = null, ['class' => 'form-control', 'id' => 'amountCV', 'type' => 'text','min'=>'1', 'placeholder' => 'Enter Amount']) !!}
                                                                <p id='amountCVe' style="max-height:3px; color:red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('narrationCV', 'Narration/ Cheque Details:', ['class' => 'col-sm-12 control-label']) !!}
                                                            <div class="col-sm-12">
                                                                {!! Form::text('narrationCV', $value = null, ['class' => 'form-control', 'id' => 'narrationCV', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                                                <p id='narrationCVe' style="max-height:3px; color: red;"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="form-group">
                                                <div style="padding-right: 30px;">
                                                    <button class="btn btn-info" id="addCV" style="float: right; " type="button">Add</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="padding-bottom: 20px">
                                            <div class="col-md-0"></div>
                                            <div class="col-md-12" style="padding: 0px 45px 0px 45px; ">
                                                <table id="addCVTable" class="table table-striped table-bordered">
                                                    <thead>
                                                    <tr id="headerRowCV">
                                                        <th style="padding: 10px 5px; width: 30%; text-align:center;">Debit Account</th>
                                                        <th style="padding: 10px 5px; width: 30%; text-align:center;" >Credit Account</th>
                                                        <th style="padding: 10px 5px; width: 12%; text-align:center;">Amount</th>
                                                        <th style="padding: 10px 5px; width: 20%; text-align:center;">Narration / Cheque Details</th>
                                                        <th style="padding: 10px 5px; width: 8%; text-align:center;">Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <th colspan="5"></th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <p id='tableCVe' style="max-height:3px; color: red;"></p>
                                            </div>
                                            <div class="col-md-0"></div>
                                        </div>
                                        <div class="col-md-12"></div>
                                        <div class="col-md-12" style="padding: 0px 30px 20px 30px; " >
                                            <div class="form-group">
                                                {!! Form::label('globalNarrationCV', 'Global Narration Details:', ['class' => 'col-sm-12 control-label']) !!}
                                                <div class="col-sm-12">
                                                    {!! Form::text('globalNarrationCV', $value = null, ['class' => 'form-control', 'id' => 'globalNarrationCV', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                                    <p id='globalNarrationCVe' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                          <div class="col-md-12" style="padding: 0px 30px 20px 30px; " >
                                            <div class="form-group">
                                                {!! Form::label('imageCV', 'Voucher Image', ['class' => 'col-sm-12 control-label']) !!}
                                                <div class="col-sm-6">
                                                    <div id="addMoreCVId" class="fetchCVData">
                                                        <input type="file" name="imageCV[]"  class="imageuploadCV chaneBeforeImageCV beforeImgInputCV">
                                                    </div>
                                                    <p id='imageCVe' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="padding: 0px 20px 15px 30px; ">
                                             <p><input type="button" style="" name="addMoreCV" id="addMoreCV" class="btn btn-info" value="Add More"></p>
                                        </div>

                                        <div class="form-group" >
                                            {!! Form::label('submitCV', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9 text-right" style="padding-right: 45px;">
                                                <input type="button" name="" id="submitCV" class="btn btn-info" value="Submit">
                                                <input type="button" name="" id="submitNPrintCV" class="btn btn-success" value="Submit & Print">
                                                {{--{!! Form::submit('submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}--}}
                                                {{--{{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}--}}
                                                <a href="{{url('/viewVoucher')}}" class="btn btn-danger closeBtn">Close</a>
                                                <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}
                                    </div>
                                </div>
{{--====================================================End contraVoucher Form====================================================--}}

{{--=============================================Starts fundTransferVoucher Form=============================================--}}

                                <div class="tab-pane" id="fundTransferTab">
                                    <div class="panel-heading">
                                        <div class="panel-title col-md-4">Fund Transfer Voucher</div>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-4">
                                            <h4 style="color: black;"><?php echo "Total Amount: ";?><span
                                                        id="totalAmountColumnFT">0.0</span><?php echo " Tk";?></h4>
                                        </div>
                                        {{--<div class="col-md-1"></div>--}}
                                        <div class=" col-md-3"><h4 style="color: black;"><?php echo "Branch: ";?><strong>{{$branch->name}}</strong></h4>
                                        </div>
                                    </div>

                                    {{--<div class="row">--}}
                                        {!! Form::open(array('url' => '', 'role' => 'form','enctype' => 'multipart/form-data', 'class'=>'form-horizontal form-groups')) !!}

                                        {{ Form::hidden('fundTransferTab', 5, array('id' => 'fundTransferTabValue')) }}

                                        <div class="col-md-12">
                                            <div class="row">
                                                {{--<div class="col-md-12">--}}
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {!! Form::label('projectIdFT', 'Project:', ['class' => 'col-sm-12 control-label']) !!}
                                                        <div class="col-sm-12">
                                                            <select class="form-control" id="projectIdFT" name="projectIdFT">
                                                                <option value="">Select Project</option>
                                                                @foreach($projects as $project)
                                                                    <option value="{{$project->id}}">{{$project->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <p id='projectIdFTe' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 hidden">
                                                    <div class="form-group">        {{--project type--}}
                                                        {!! Form::label('projectTypeIdFT', 'Project Type:', ['class' => 'col-sm-12 control-label']) !!}
                                                        <div class="col-sm-12">
                                                            <select class="form-control" id="projectTypeIdFT" name="projectTypeIdFT">
                                                                <option value="">Select Project Type</option>
                                                                @foreach($projectTypes as $projectType)
                                                                    <option value="{{$projectType->id}}">{{$projectType->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <p id='projectTypeIdFTe' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {!! Form::label('voucherDateFT', 'Voucher Date:', ['class' => 'col-sm-12 control-label']) !!}
                                                        <div class="col-sm-12">
                                                            {!! Form::text('voucherDateFT',  null, ['class' => 'form-control softwareDate', 'id' => 'voucherDateFT', 'readonly' ])!!}
                                                            <p id='voucherDateFTe' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {!! Form::label('voucherCodeFT', 'Voucher Code:', ['class' => 'col-sm-12 control-label']) !!}
                                                        <div class="col-sm-12">
                                                            {!! Form::text('voucherCodeFT', $value = null, ['class' => 'form-control', 'id' => 'voucherCodeFT', 'type' => 'text', 'disabled' => 'disabled']) !!}
                                                            <p id='voucherCodeFTe' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {!! Form::label('targetBranchFT', 'Target Branch:', ['class' => 'col-sm-12 control-label']) !!}
                                                        <?php
                                                        // $branchesInfo = DB::table('gnr_branch')->select('id', 'name', 'branchCode')->get();
                                                        ?>
                                                        <div class="col-sm-12">

                                                            <select class="form-control" id="targetBranchFT" name="targetBranchFT">
                                                                <option value="">Please Select Project First</option>
                                                                {{-- @foreach($branchesInfo as $branchInfo)
                                                                    <option value="{{$branchInfo->id}}">{{str_pad($branchInfo->branchCode,3,"0",STR_PAD_LEFT)."-".$branchInfo->name}}</option>
                                                                @endforeach --}}
                                                            </select>
                                                            <p id='targetBranchFTe' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {!! Form::label('targetBranchHeadFT', 'Target Branch Cash/Bank:', ['class' => 'col-sm-12 control-label']) !!}
                                                        <div class="col-sm-12">
                                                            <select class="form-control" id="targetBranchHeadFT" name="targetBranchHeadFT">
                                                                <option value="">Please Select Project First</option>
                                                                {{-- <option value="">Select Credit Account</option>
                                                                @foreach($ledgersOfCashAndBank as $ledgerOfCashAndBank)
                                                                    <option value="{{$ledgerOfCashAndBank->id}}">{{$ledgerOfCashAndBank->name}}</option>
                                                                @endforeach --}}
                                                            </select>
                                                            <p id='targetBranchHeadFTe' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {!! Form::label('narrationFT', 'Narration/ Cheque Details:', ['class' => 'col-sm-12 control-label']) !!}
                                                        <div class="col-sm-12">
                                                            {!! Form::text('narrationFT', $value = null, ['class' => 'form-control', 'id' => 'narrationFT', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                                            <p id='narrationFTe' style="max-height:3px; color: red;"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{--</div> col-12--}}

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {!! Form::label('debitAccFT', 'Debit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                        <div class="col-sm-12">
                                                            <select class="form-control" id="debitAccFT" name="debitAccFT">
                                                                <option value="">Please Select Project First</option>
                                                                {{-- <option value="">Select Debit Account</option>
                                                                @foreach($ledgersOfCashAndBank as $ledgerOfCashAndBank)
                                                                    <option value="{{$ledgerOfCashAndBank->id}}">{{$ledgerOfCashAndBank->name}}</option>
                                                                @endforeach --}}
                                                            </select>
                                                            <p id='debitAccFTe' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {!! Form::label('creditAccFT', 'Credit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                        <div class="col-sm-12">
                                                            <select class="form-control" id="creditAccFT" name="creditAccFT">
                                                                <option value="">Please Select Project First</option>
                                                                {{-- <option value="">Select Credit Account</option>
                                                                @foreach($ledgersOfCashAndBank as $ledgerOfCashAndBank)
                                                                    <option value="{{$ledgerOfCashAndBank->id}}">{{$ledgerOfCashAndBank->name}}</option>
                                                                @endforeach --}}
                                                            </select>
                                                            <p id='creditAccFTe' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {!! Form::label('amountFT', 'Amount:', ['class' => 'col-sm-12 control-label']) !!}
                                                        <div class="col-sm-12">
                                                            {!!  Form::text('amountFT', $value = null, ['class' => 'form-control', 'id' => 'amountFT', 'type' => 'text','min'=>'1', 'placeholder' => 'Enter Amount']) !!}
                                                            <p id='amountFTe' style="max-height:3px; color:red;"></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {!! Form::label('', '', ['class' => 'col-sm-12 control-label']) !!}
                                                        <div class="col-sm-12" style="padding-top: 20px;">
                                                            <button class="btn btn-info" id="addFT" style="float: right; " type="button">Add</button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                        {{-- <div class="form-group">
                                            <div style="padding-right: 30px;">
                                                <button class="btn btn-info" id="addFT" style="float: right; " type="button">Add</button>
                                            </div>
                                        </div> --}}
                                        {{--</div>--}}

                                        <div class="row" style="padding-bottom: 20px">
                                            <div class="col-md-0"></div>
                                            <div class="col-md-12" style="padding: 0px 28px; ">
                                                <table id="addFTTable" class="table table-striped table-bordered">
                                                    <thead>
                                                    <tr id="headerRowFT">
                                                        <th style="padding: 10px 5px; width: 12%; text-align:center;">Target Branch</th>
                                                        <th style="padding: 10px 5px; width: 15%; text-align:center;">Target Cash/ Bank</th>
                                                        <th style="padding: 10px 5px; width: 20%; text-align:center;">Debit Account</th>
                                                        <th style="padding: 10px 5px; width: 20%; text-align:center;">Credit Account</th>
                                                        <th style="padding: 10px 5px; width: 10%; text-align:center;">Amount</th>
                                                        <th style="padding: 10px 5px; width: 15%; text-align:center;">Narration</th>
                                                        <th style="padding: 10px 5px; width: 8%; text-align:center;">Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <th colspan="7"></th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <p id='tableFTe' style="max-height:3px; color: red;"></p>
                                            </div>
                                            <div class="col-md-0"></div>
                                        </div>
                                        <div class="col-md-12"></div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('globalNarrationFT', 'Global Narration Details:', ['class' => 'col-sm-12 control-label']) !!}
                                                <div class="col-sm-12">
                                                    {!! Form::text('globalNarrationFT', $value = null, ['class' => 'form-control', 'id' => 'globalNarrationFT', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                                    <p id='globalNarrationFTe' style="max-height:3px; color:red;"></p>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="col-md-12" style="padding: 0px 30px 20px 16px; " >
                                            <div class="form-group">
                                                {!! Form::label('imageFT', 'Voucher Image', ['class' => 'col-sm-12 control-label']) !!}
                                                <div class="col-sm-6">
                                                    <div id="addMoreFTId" class="fetchFTData">
                                                        <input type="file" name="imageFT[]"  class="imageuploadFT chaneBeforeImageFT beforeImgInputFT">
                                                    </div>
                                                    <p id='imageFTe' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="padding: 0px 20px 15px 16px; ">
                                             <p><input type="button" style="" name="addMoreFT" id="addMoreFT" class="btn btn-info" value="Add More"></p>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('submitFT', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9 text-right" style="padding-right: 45px;">
                                                <input type="button" name="" id="submitFT" class="btn btn-info" value="Submit">
                                                <input type="button" name="" id="submitNPrintFT" class="btn btn-success" value="Submit & Print">
                                                {{--{!! Form::submit('submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}--}}
                                                {{--{{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}--}}
                                                <a href="{{url('/viewVoucher')}}" class="btn btn-danger closeBtn">Close</a>
                                                <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    {{--</div>--}}
                                </div>              {{-- FundTransferTab --}}
{{-- =============================================Ends fundTransferVoucher Form============================================= --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>

{{-- <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>   --}}

{{-- <script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js')}}"></script> --}}
{{-- <script src="{{ asset('js/select2/select2.min.js')}}"></script> --}}
<script type="text/javascript">


    //$("#creditAccPV").next("span").removeClass('select2-hidden-accessible');
    // $('#creditAccPV').next().addClass('select2-hidden-accessible');

    // $('#creditAccPV').next().removeClass('select2-hidden-accessible');
    //$('#creditAccPV').next().next().addClass('select2-hidden-accessibleAAA');
    // alert($('#creditAccPV').next().html());

    // $(document.body).on("change","#creditAccJV, #creditAccCV",function(){
    //     //alert(this.value);
    //     if (this.id == "creditAccJV") {
    //         var idVal="JV";
    //         var ideVal="JVe";
    //     }else if (this.id == "creditAccCV") {
    //         var idVal="CV";
    //         var ideVal="CVe";
    //     }

    //     var debitAcc = $("#debitAcc"+idVal).val();
    //     var creditAcc = $("#creditAcc"+idVal).val();
    //     if(debitAcc==creditAcc){


    //         $('#debitAcc'+ideVal).show();
    //         $('#debitAcc'+ideVal).html("Debit and Credit Are Same!!! Please Select Again");
    //         $('#creditAcc'+ideVal).show();
    //         $('#creditAcc'+ideVal).html("Debit and Credit Are Same!!! Please Select Again");

    //         $('#creditAcc'+idVal).val(null).trigger("change");
    //         $('#debitAcc'+idVal).val(null).trigger("change");




    //         // var projectId=$('#projectId'+idVal).val();
    //         // var branchId="{{$branchId}}";
    //         // var check="fromDrCr";
    //         // alert(projectId+" "+branchId+" "+check+" ");
    //         // changeProjectTypeNLedgers(projectId, branchId, idVal, check);
    //         return false;
    //     }else{
    //         $('#debitAcc'+ideVal).hide();
    //         $('#creditAcc'+ideVal).hide();
    //     }

    // });

// ============================================Starts JavaScript for Debit Voucher============================================
$(document).ready(function(){

 //    $('#imagePV').on('change', function() {
 //       var total_file=document.getElementById("imagePV").files.length;
 //       for(var i=0;i<total_file;i++)
 // {
 //   var some =  $('#image_pre').append("<img src='"+URL.createObjectURL(event.target.files[i])+"'><br>");
 //   console.log(some);
 // }

 //    });

    // var currSoftDate="{{$currSoftDate}}";
    // $('.softwareDate').val(currSoftDate);
    // $('.softwareDate').prop('disabled', 'true');


    // $("#debitAccPV, #creditAccPV, #debitAccRV, #creditAccRV, #debitAccJV, #creditAccJV, #debitAccCV, #creditAccCV, #debitAccFT, #creditAccFT").select2();
    // $("#debitAccPV, #creditAccPV, #debitAccRV, #creditAccRV, #debitAccJV, #creditAccJV, #debitAccCV, #creditAccCV, #debitAccFT, #creditAccFT").next("span").css("width","100%");


    // function pad (str, max) {
    //     str = str.toString();
    //     return str.length < max ? pad("0" + str, max) : str;
    // }
     var dateRange = <?php echo json_encode($dateRange) ?>;
    $("#voucherDatePV, #voucherDateRV, #voucherDateJV, #voucherDateCV").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "2016:c",
        minDate: dateRange.startDate,
        maxDate: dateRange.endDate,
        dateFormat: 'dd-mm-yy',
        onSelect: function () {
            $('#voucherDatePVe, #voucherDateRVe, #voucherDateJVe, #voucherDateCVe, #voucherDateFTe').hide();
        }
    });

// ================================================General=============

// ==================================================Keyup (Amount & Narration)========================================================
    $("#amountPV, #amountRV, #amountJV, #amountCV, #amountFT").keyup(function(){

        if(this.id == "amountPV") {
            var idVal="PV";
            var ideVal="PVe";
        }else if (this.id == "amountRV") {
            var idVal="RV";
            var ideVal="RVe";
        }else if (this.id == "amountJV") {
            var idVal="JV";
            var ideVal="JVe";
        }else if (this.id == "amountCV") {
            var idVal="CV";
            var ideVal="CVe";
        }else if (this.id == "amountFT") {
            var idVal="FT";
            var ideVal="FTe";
        }
        var msg="The Field is Required Please Fill";
        var amount = $("#amount"+idVal).val();
        if(amount){ $('#amount'+ideVal).hide(); }else{ $('#amount'+ideVal).show(); $('#amount'+ideVal).html(msg); return false;}
    });
    $("#narrationPV, #narrationRV, #narrationJV, #narrationCV, #narrationFT, #globalNarrationPV, #globalNarrationRV, #globalNarrationJV, #globalNarrationCV, #globalNarrationFT").keyup(function(){

        if(this.id == "narrationPV" || this.id == "globalNarrationPV") {
            var idVal="PV";
            var ideVal="PVe";
        }else if (this.id == "narrationRV" || this.id == "globalNarrationRV") {
            var idVal="RV";
            var ideVal="RVe";
        }else if (this.id == "narrationJV" || this.id == "globalNarrationJV") {
            var idVal="JV";
            var ideVal="JVe";
        }else if (this.id == "narrationCV" || this.id == "globalNarrationCV") {
            var idVal="CV";
            var ideVal="CVe";
        }else if (this.id == "narrationFT" || this.id == "globalNarrationFT") {
            var idVal="FT";
            var ideVal="FTe";
        }

        var msg="The Field is Required Please Fill";
        if(this.id == "narration"+idVal) {
            var narration = $("#narration"+idVal).val();
            if(narration){$('#narration'+ideVal).hide();}else{$('#narration'+ideVal).show(); $('#narration'+ideVal).html(msg); return false;}
        }else if(this.id == "globalNarration"+idVal){
            var globalNarration = $("#globalNarration"+idVal).val();
            if(globalNarration){$('#globalNarration'+ideVal).hide();}else{$('#globalNarration'+ideVal).show(); $('#globalNarration'+ideVal).html(msg); return false;}
        }

    });
// ==================================================END sKeyup (Amount & Narration)========================================================

    $('select').on('change', function (e) {

        var projectIdPV = $("#projectIdPV").val();
        if(projectIdPV){$('#projectIdPVe').hide();}else{$('#projectIdPVe').show();}
        // var projectTypeIdPV = $("#projectTypeIdPV").val();
        // if(projectTypeIdPV){$('#projectTypeIdPVe').hide();}else{$('#projectTypeIdPVe').show();}

        var projectIdRV = $("#projectIdRV").val();
        if(projectIdRV){$('#projectIdRVe').hide();}else{$('#projectIdRVe').show();}
        // var projectTypeIdRV = $("#projectTypeIdRV").val();
        // if(projectTypeIdRV){$('#projectTypeIdRVe').hide();}else{$('#projectTypeIdRVe').show();}

        var projectIdJV = $("#projectIdJV").val();
        if(projectIdJV){$('#projectIdJVe').hide();}else{$('#projectIdJVe').show();}
        // var projectTypeIdJV = $("#projectTypeIdJV").val();
        // if(projectTypeIdJV){$('#projectTypeIdJVe').hide();}else{$('#projectTypeIdJVe').show();}

        var projectIdCV = $("#projectIdCV").val();
        if(projectIdCV){$('#projectIdCVe').hide();}else{$('#projectIdCVe').show();}
        // var projectTypeIdCV = $("#projectTypeIdCV").val();
        // if(projectTypeIdCV){$('#projectTypeIdCVe').hide();}else{$('#projectTypeIdCVe').show();}

        var projectIdFT = $("#projectIdFT").val();
        if(projectIdFT){$('#projectIdFTe').hide();}else{$('#projectIdFTe').show();}
        // var projectTypeIdFT = $("#projectTypeIdFT").val();
        // if(projectTypeIdFT){$('#projectTypeIdFTe').hide();}else{$('#projectTypeIdFTe').show();}
    });
    $('#amountPV, #amountRV, #amountJV, #amountCV, #amountFT').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    });


// ===========================================Remove TR===========================================

    $(document).on('click', '.removeButtonPV, .removeButtonRV, .removeButtonJV, .removeButtonCV, .removeButtonFT', function () {

        if($(this).attr('class') == "removeButtonPV") {
            var idVal="PV";
            var ideVal="PVe";
        }else if ($(this).attr('class') == "removeButtonRV") {
            var idVal="RV";
            var ideVal="RVe";
        }else if ($(this).attr('class') == "removeButtonJV") {
            var idVal="JV";
            var ideVal="JVe";
        }else if ($(this).attr('class') == "removeButtonCV") {
            var idVal="CV";
            var ideVal="CVe";
        }else if ($(this).attr('class') == "removeButtonFT") {
            var idVal="FT";
            var ideVal="FTe";
        }

        var tdAmount = parseFloat($(this).closest('tr').find('.amountColumn'+idVal).text());
        $("#totalAmountColumn"+idVal).text((parseFloat($("#totalAmountColumn"+idVal).html())-tdAmount).toFixed(2));
        $(this).closest('tr').remove();
        return false;
    });
// ===================================Remove TR================================================



//  =========================================For General=========================================
// =========================================insert value into the table=========================================

    var statusOfTargetHead = "firstLoad";
    // alert(statusOfTargetHead);

    function tbodyAppend(idVal, ideVal){

        var msg="The Field is Required";
        var projectId = $("#projectId"+idVal+" option:selected").val();
        if(projectId){ $('#projectId'+ideVal).hide(); }else{ $('#projectId'+ideVal).show(); $('#projectId'+ideVal).html(msg); return false;}

        var projectTypeId = $("#projectTypeId"+idVal+" option:selected").val();
        if(projectTypeId){ $('#projectTypeId'+ideVal).hide(); }else{ $('#projectTypeId'+ideVal).show(); $('#projectTypeId'+ideVal).html(msg); return false;}

        var voucherDate = $("#voucherDate"+idVal).val();
        if(voucherDate){ $('#voucherDate'+ideVal).hide(); }else{ $('#voucherDate'+ideVal).show(); $('#voucherDate'+ideVal).html(msg); return false;}

        var voucherCode = $("#voucherCode"+idVal).val();
        if(voucherCode){ $('#voucherCode'+ideVal).hide(); }else{ $('#voucherCode'+ideVal).show(); $('#voucherCode'+ideVal).html(msg); return false;}

        var debitAcc = $("#debitAcc"+idVal).val();
        if(debitAcc){ $('#debitAcc'+ideVal).hide(); }else{ $('#debitAcc'+ideVal).show(); $('#debitAcc'+ideVal).html(msg); return false;}

        var creditAcc = $("#creditAcc"+idVal).val();
        if(creditAcc){ $('#creditAcc'+ideVal).hide(); }else{ $('#creditAcc'+ideVal).show(); $('#creditAcc'+ideVal).html(msg); return false;}

        var amount = $("#amount"+idVal).val();
        if(amount){ $('#amount'+ideVal).hide(); }else{ $('#amount'+ideVal).show(); $('#amount'+ideVal).html(msg); return false;}

        var narration = $("#narration"+idVal).val();
        if(narration){$('#narration'+ideVal).hide();}else{$('#narration'+ideVal).show(); $('#narration'+ideVal).html(msg); return false;}

        if(debitAcc==creditAcc){
            $('#creditAcc'+ideVal+', #debitAcc'+ideVal).show();
            $('#creditAcc'+ideVal+', #debitAcc'+ideVal).html("Debit and Credit Are Same!!! Please Select Again");
            $('#creditAcc'+idVal+', #debitAcc'+idVal).val('').trigger('change');
            // $('#debitAcc'+idVal).val("");
            // $('#creditAcc'+ideVal).show();
            // $('#creditAcc'+ideVal).html("Debit and Credit Are Same!!! Please Select Again");
            // $('#creditAcc'+idVal).val("");
            return false;
        }else{
            $('#debitAcc'+ideVal).hide();
            $('#creditAcc'+ideVal).hide();
        }

        // var debitAcc = $("#debitAcc"+idVal+" option:selected").val();
        // var creditAcc = $("#creditAcc"+idVal+" option:selected").val();
        var debitAccName = $("#debitAcc"+idVal+" option:selected").html();
        var creditAccName = $("#creditAcc"+idVal+" option:selected").html();

        var amount = parseFloat($("#amount"+idVal).val());
        var totalAmount = parseFloat($("#totalAmountColumn"+idVal).text());
        var narration = $("#narration"+idVal).val();

        totalAmount =amount+totalAmount;
        $("#totalAmountColumn"+idVal).text(totalAmount.toFixed(2));

        // alert(idVal);
        if (idVal=="PV") {
            var markup =
                "<tr class='valueRow"+idVal+"'>" +
                    "<td style='padding: 8px 5px; text-align:left;' class='creditAcc"+idVal+"'>"+
                    "<input type='hidden' class='creditAccInput"+idVal+"' value='"+creditAcc+"'>"+creditAccName+"" +
                    "</td>" +
                    "<td style='padding: 8px 5px; text-align:left;' class='debitAcc"+idVal+"'>" +
                    "<input type='hidden' class='debitAccInput"+idVal+"' value='"+debitAcc+"'>"+debitAccName+"" +
                    "</td>" +
                    "<td style='padding: 8px 5px; text-align:right;' class='amountColumn"+idVal+"' >" +amount+ "</td>" +
                    "<td style='padding: 8px 5px; text-align:Left;' class='narration"+idVal+"'>"+narration+"</td>" +
                    // "<td style='padding: 8px 5px; text-align:center;' ><button class='removeButton"+idVal+"'>Delete</button></td>" +
                    "<td style='padding: 8px 5px; text-align: center;'><a href='javascript:;' class='removeButton"+idVal+"'><i class=' glyphicon glyphicon-trash' style='color:red; font-size:14px'><i></a></td>" +
                "</tr>";

        }else if(idVal=="FT"){
            var targetBranch = $("#targetBranch"+idVal+" option:selected").val();
            var targetBranchHead = $("#targetBranchHead"+idVal+" option:selected").val();
            var targetBranchName = $("#targetBranch"+idVal+" option:selected").html();
            // alert(targetBranchHead);
            if(targetBranchHead==0){
                var targetBranchHeadName = "N/A";
                statusOfTargetHead = "nonCash";
            }else{
                var targetBranchHeadName = $("#targetBranchHead"+idVal+" option:selected").html();
                statusOfTargetHead = "cashNBank";
            }

            var markup =
                "<tr class='valueRow"+idVal+"'>" +
                    "<td style='padding: 8px 5px; text-align:left;' class='targetBranch"+idVal+"'>" +
                    "<input type='hidden' class='targetBranchInput"+idVal+"' value='"+targetBranch+"'>"+targetBranchName+"" +
                    "</td>" +
                    "<td style='padding: 8px 5px; text-align:left;' class='targetBranchHead"+idVal+"'>" +
                    "<input type='hidden' class='targetBranchHeadInput"+idVal+"' value='"+targetBranchHead+"'>"+targetBranchHeadName+"" +
                    "</td>" +
                    "<td style='padding: 8px 5px; text-align:left;' class='debitAcc"+idVal+"'>" +
                    "<input type='hidden' class='debitAccInput"+idVal+"' value='"+debitAcc+"'>"+debitAccName+"" +
                    "</td>" +
                    "<td style='padding: 8px 5px; text-align:left;' class='creditAcc"+idVal+"'>"+
                    "<input type='hidden' class='creditAccInput"+idVal+"' value='"+creditAcc+"'>"+creditAccName+"" +
                    "</td>" +
                    "<td style='padding: 8px 5px; text-align:right;' class='amountColumn"+idVal+"' >" +amount+ "</td>" +
                    "<td style='padding: 8px 5px; text-align:Left;' class='narration"+idVal+"'>"+narration+"</td>" +
                    // "<td style='padding: 8px 5px; text-align:center;' ><button class='removeButton"+idVal+"'>Delete</button></td>" +
                    "<td style='padding: 8px 5px; text-align: center;'><a href='javascript:;' class='removeButton"+idVal+"'><i class=' glyphicon glyphicon-trash' style='color:red; font-size:14px'><i></a></td>" +
                "</tr>";
        }else{
            var markup =
                "<tr class='valueRow"+idVal+"'>" +
                    "<td style='padding: 8px 5px; text-align:left;' class='debitAcc"+idVal+"'>" +
                    "<input type='hidden' class='debitAccInput"+idVal+"' value='"+debitAcc+"'>"+debitAccName+"" +
                    "</td>" +
                    "<td style='padding: 8px 5px; text-align:left;' class='creditAcc"+idVal+"'>"+
                    "<input type='hidden' class='creditAccInput"+idVal+"' value='"+creditAcc+"'>"+creditAccName+"" +
                    "</td>" +
                    "<td style='padding: 8px 5px; text-align:right;' class='amountColumn"+idVal+"' >" +amount+ "</td>" +
                    "<td style='padding: 8px 5px; text-align:Left;' class='narration"+idVal+"'>"+narration+"</td>" +
                    // "<td style='padding: 8px 5px; text-align:center;' ><button class='removeButton"+idVal+"'>Delete</button></td>" +
                    "<td style='padding: 8px 5px; text-align: center;'><a href='javascript:;' class='removeButton"+idVal+"'><i class=' glyphicon glyphicon-trash' style='color:red; font-size:14px'><i></a></td>" +
                "</tr>";
        }
        $("#headerRow"+idVal).after(markup);

        // $('#debitAcc'+idVal).val("");
        // $('#creditAcc'+idVal).val("");
        $('#amount'+idVal).val("");
        $('#narration'+idVal).val("");

        $('#projectId'+idVal).prop("disabled", true);
        $('#projectTypeId'+idVal).prop("disabled", true);
        $('#voucherDate'+idVal).prop("disabled", true);
        $('#voucherDate'+idVal).css("cursor","not-allowed");

        var projectId=$('#projectId'+idVal).val();
        var check="fromAddButton";
        if(idVal=="FT"){
            var projectId = $("#projectIdFT").val();
            var branchId = $("#targetBranchFT").val();
            var targetBranchHead = $("#targetBranchHeadFT").val();
            // alert(statusOfTargetHead);
            // var userBranchId="{{$branchId}}";

            var branchIdArray = new Array();
            branchIdArray.push(targetBranch);
            // branchIdArray.push(branchId);
            var check="fromAddButton";

            // $("#targetBranchHeadFT").remove(val('0'));
            // if ( $("#targetBranchHeadFT").val() != 0 ) {
            //     $("#targetBranchHeadFT option[value='0']").remove();
            // }else if( $("#targetBranchHeadFT").val() == 0 ){
            //     $("#targetBranchHeadFT").prop('disabled', 'true');
            // }
            changeLedgersByTargetBranch(projectId, branchIdArray, targetBranchHead, check);
        }else{
            // var branchId="{{$branchId}}";
            // alert(projectId+":"+branchId+":"+idVal+":"+check);
            // changeProjectTypeNLedgers(projectId, branchId, idVal, check);

            $('#creditAcc'+idVal+', #debitAcc'+idVal).val('').trigger('change');
        }


    }

    $("#addPV, #addRV, #addJV, #addCV, #addFT").click(function(){

        if(this.id == "addPV") {
            var idVal="PV";
            var ideVal="PVe";
        }else if (this.id == "addRV") {
            var idVal="RV";
            var ideVal="RVe";
        }else if (this.id == "addJV") {
            var idVal="JV";
            var ideVal="JVe";
        }else if (this.id == "addCV") {
            var idVal="CV";
            var ideVal="CVe";
        }else if (this.id == "addFT") {
            var idVal="FT";
            var ideVal="FTe";
        }
        tbodyAppend(idVal, ideVal);

    });

// =========================================End insert value into the table=========================================

// ==============================Change Project(get ProjectType & ledgers) & ProjectType(get Voucher Code)==============================

    var code = null;

    var branchCode = ("<?php echo $branch->branchCode; ?>").toString();
    var userBranchCode = "<?php echo $branch->branchCode; ?>";
    var branchCodePad = "000";
    var newBranchCode = branchCodePad.substring(0, branchCodePad.length - branchCode.length) + branchCode;
    // alert(newBranchCode);

    var branchId="{{$branchId}}";

    function changeProjectTypeNLedgers(projectId, branchId, idVal, check) {
        //alert(projectId);
        var csrf = "{{csrf_token()}}";
        $.ajax({
            type: 'post',
            url: './getProjectTypeNLedgersInfo',
            data: { projectId: projectId,branchId: branchId, _token: csrf},
            dataType: 'json',
            success: function (data) {
                //alert(JSON.stringify(data));

                if (check=="fromProject") {
                    //if (userBranchCode==0) {
                        var projectTypeList=data['projectTypeList'];
                        $("#projectTypeId"+idVal).empty();
                        $.each(projectTypeList, function( value ,index){
                            $('#projectTypeId'+idVal).append("<option value='"+index+"'>"+value+"</option>");
                        });
                        //$("#voucherCode"+idVal).val('');
                    //}
                }

                $("#creditAcc"+idVal).empty();
                $("#creditAcc"+idVal).prepend('<option selected="selected" value="">Select  Account</option>');
                $("#debitAcc"+idVal).empty();
                $("#debitAcc"+idVal).prepend('<option selected="selected" value="">Select  Account</option>');

                if (idVal=="PV") {
                    var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                    var ledgersOfAssetNLiabilityNCapitalFundNExpense=data['ledgersOfAssetNLiabilityNCapitalFundNExpense'];
                    $.each(ledgersOfCashAndBank, function(key, obj){
                        $('#creditAcc'+idVal).append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                    });
                    $.each(ledgersOfAssetNLiabilityNCapitalFundNExpense, function(key, obj){
                        $('#debitAcc'+idVal).append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                    });
                }else if (idVal=="RV") {
                    var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                    var ledgersOfAssetNLiabilityNCapitalFundNIncome=data['ledgersOfAssetNLiabilityNCapitalFundNIncome'];
                    $.each(ledgersOfCashAndBank, function(key, obj){
                        $('#debitAcc'+idVal).append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                    });
                    $.each(ledgersOfAssetNLiabilityNCapitalFundNIncome, function(key, obj){
                        $('#creditAcc'+idVal).append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                    });
                }else if (idVal=="JV") {
                    // var ledgersOfAllAccountType=data['ledgersOfAllAccountType'];
                    var ledgersOfNonCashNIncome=data['ledgersOfNonCashNIncome'];
                    var ledgersOfNonCashNExpense=data['ledgersOfNonCashNExpense'];
                    $.each(ledgersOfNonCashNIncome, function(key, obj){
                        // $('#creditAcc'+idVal).append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        $('#debitAcc'+idVal).append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                    });
                    $.each(ledgersOfNonCashNExpense, function(key, obj){
                        $('#creditAcc'+idVal).append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        // $('#debitAcc'+idVal).append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                    });
                }else if (idVal=="CV") {
                    var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                    $.each(ledgersOfCashAndBank, function(key, obj){
                        $('#creditAcc'+idVal).append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        $('#debitAcc'+idVal).append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                    });
                }

            },
            error: function(_response){
                alert("Error");
            }
        });
    }          //END changeProjectTypeNLedgers Function

    $("#projectIdPV, #projectIdRV, #projectIdJV, #projectIdCV").change(function () {

        // var userBranchId="{{$branchId}}";
        // if (userBranchId!=1) {return false;}

        if(this.id == "projectIdPV") {
            var idVal="PV";
            var projectId = this.value;
            //alert(projectId);
        }else if (this.id == "projectIdRV") {
            var idVal="RV";
            var projectId = this.value;
        }else if (this.id == "projectIdJV") {
            var idVal="JV";
            var projectId = this.value;
        }else if (this.id == "projectIdCV") {
            var idVal="CV";
            var projectId = this.value;
        }
        var check="fromProject";
        changeProjectTypeNLedgers(projectId, branchId, idVal, check);
    });

    $("#projectIdFT").change(function () {
        var projectId = this.value;
        $("#voucherCodeFT").val('');
        var userBranchId = "{{$branchId}}";
        var csrf = "{{csrf_token()}}";

        $.ajax({
            type: 'post',
            url: './getBranchNProjectTypeByProject',
            data: { projectId: projectId, _token: csrf},
            dataType: 'json',
            success: function (data) {
                // alert(JSON.stringify(data));
                if (userBranchId==1) {
                    var projectTypeList=data['projectTypeList'];
                    $("#projectTypeIdFT").empty();
                    //$("#projectTypeIdFT").prepend('<option  value="">Select Project Type</option>');
                    $.each(projectTypeList, function(key, obj){
                        $('#projectTypeIdFT').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                    });
                }
                // $("#voucherCodeFT").val('');

                var branchList=data['branchList'];
                $("#targetBranchFT").empty();
                $("#targetBranchFT").prepend('<option  value="">Select Branch</option>');
                if(userBranchId!=1){
                    $("#targetBranchFT").append('<option  value="1">000 - Head Office</option>');
                }
                $.each(branchList, function(key, obj){
                    if(userBranchId!=obj.id){
                        $('#targetBranchFT').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
                    }
                });
                $("#creditAccFT, #debitAccFT, #targetBranchHeadFT").empty();
                $("#creditAccFT, #debitAccFT, #targetBranchHeadFT").prepend('<option value="">Please Select Branch First</option>');

            },
            error: function(_response){
                alert("Error");
            }
        });
    });

    function changeLedgersByTargetBranch(projectId, branchIdArray, targetBranchHead, check){
        var csrf = "{{csrf_token()}}";
        // alert(check);
        $.ajax({
            type: 'post',
            url: './getLedgersByBranches',
            data: { projectId: projectId, branchIdArray: branchIdArray, _token: csrf},
            dataType: 'json',
            success: function (data) {
                // alert(JSON.stringify(data));
                if(check=="targetBranchHeadFT"){
                    var tBHeadFTVal=$("#targetBranchHeadFT").val();
                    // $("#targetBranchHeadFT").empty();
                    // $("#targetBranchHeadFT").prepend('<option selected="selected" value="">Select  Account</option>');
                    // alert(statusOfTargetHead);
                    if(statusOfTargetHead=="firstLoad"){
                        // var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        // $("#targetBranchHeadFT").append('<option  value="0">Non Cash</option>');
                        // $.each(ledgersOfCashAndBank, function(key, obj){
                        //     $('#targetBranchHeadFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        // });
                    }else if(statusOfTargetHead=="nonCash"){
                        $("#targetBranchHeadFT").append('<option  value="0">Non Cash</option>');
                    }else if(statusOfTargetHead=="cashNBank"){
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#targetBranchHeadFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }

                    $("#targetBranchHeadFT").val(tBHeadFTVal);

                    $("#creditAccFT, #debitAccFT").empty();
                    $("#creditAccFT, #debitAccFT").prepend('<option selected="selected" value="">Select  Account</option>');
                    // alert(targetBranchHead);
                    if(targetBranchHead==0){
                        var ledgersWithOutCashNBank=data['ledgersWithOutCashNBank'];
                        $.each(ledgersWithOutCashNBank, function(key, obj){
                            $('#creditAccFT, #debitAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }else{
                        // var ledgersWithOutCashNBank=data['ledgersWithOutCashNBank'];
                        // $.each(ledgersWithOutCashNBank, function(key, obj){
                        //     $('#debitAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        // });
                        var ledgersOfCashAndBank = data['ledgersOfCashAndBank'];
                        var ledgersOfFTDebitCashAndBank = data['ledgersOfFTDebitCashAndBank'];

                        $.each(ledgersOfCashAndBank, function(key, obj){
                            // alert(obj);
                            $('#creditAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");

                        });
                        $.each(ledgersOfFTDebitCashAndBank, function(key, obj){
                            // alert(obj);
                            $('#debitAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");

                        });
                    }
                }else if(check=="fromAddButton"){
                    var tBHeadFTVal=$("#targetBranchHeadFT").val();
                    $("#targetBranchHeadFT").empty();
                    $("#targetBranchHeadFT").prepend('<option selected="selected" value="">Select  Account</option>');

                    if(statusOfTargetHead=="nonCash"){
                        $("#targetBranchHeadFT").append('<option  value="0">Non Cash</option>');
                    }else if(statusOfTargetHead=="cashNBank"){
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#targetBranchHeadFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }
                    $("#targetBranchHeadFT").val(tBHeadFTVal);

                    $("#creditAccFT, #debitAccFT").empty();
                    $("#creditAccFT, #debitAccFT").prepend('<option selected="selected" value="">Select  Account</option>');
                    if(targetBranchHead==0){
                        var ledgersWithOutCashNBank=data['ledgersWithOutCashNBank'];
                        $.each(ledgersWithOutCashNBank, function(key, obj){
                            $('#creditAccFT, #debitAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }else{
                        var ledgersOfFTDebitCashAndBank = data['ledgersOfFTDebitCashAndBank'];
                        $.each(ledgersOfFTDebitCashAndBank, function(key, obj){
                            $('#debitAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#creditAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }
                }else if(check=="targetBranchFT"){
                    $("#targetBranchHeadFT").empty();
                    $("#targetBranchHeadFT").prepend('<option selected="selected" value="">Select  Account</option>');

                    if(statusOfTargetHead=="firstLoad"){
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        $("#targetBranchHeadFT").append('<option  value="0">Non Cash</option>');
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#targetBranchHeadFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }else if(statusOfTargetHead=="nonCash"){
                        $("#targetBranchHeadFT").append('<option  value="0">Non Cash</option>');
                    }else if(statusOfTargetHead=="cashNBank"){
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#targetBranchHeadFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }

                    $("#creditAccFT, #debitAccFT").empty();
                    $("#creditAccFT, #debitAccFT").prepend('<option value="">Please Select Target Branch Cash/Bank First</option>');
                }

            },
            error: function(_response){
                alert("Error");
            }
        });
    }
    $("#targetBranchFT").change(function () {
        var projectId = $("#projectIdFT").val();
        var branchId = this.value;
        var targetBranchHead = 0;

        var branchIdArray = new Array();
        branchIdArray.push(branchId);

        var idVal="FT";
        var check="targetBranchFT";

        changeLedgersByTargetBranch(projectId, branchIdArray, targetBranchHead, check);
    });

    // $(document).on('change', '#targetBranchHeadFT', function() {
    $("#targetBranchHeadFT").change(function () {
        var projectId = $("#projectIdFT").val();
        var branchId = $("#targetBranchFT").val();
        var userBranchId="{{$branchId}}";
        var targetBranchHead = this.value;
        // alert(targetBranchHead);

        var branchIdArray = new Array();
        branchIdArray.push(userBranchId);
        // branchIdArray.push(branchId);
        var check="targetBranchHeadFT";
        changeLedgersByTargetBranch(projectId, branchIdArray, targetBranchHead, check);


    });

    function voucherCode(projectId, voucherTypeId, branchId, idVal){
       //alert(projectId);
        var csrf = "{{csrf_token()}}";

        $.ajax({
            type: 'post',
            url: './getVoucherCode',
            data: { voucherTypeId: voucherTypeId, projectId: projectId, branchId: branchId, _token: csrf},
            dataType: 'json',
            success: function (data) {
                // alert(JSON.stringify(data));
                var singleVoucherCount=data['singleVoucherCount'];
                var shortName=data['shortName'];
                var projectTypeCode=data['projectTypeCode'];
                var voucherCode;
                if(singleVoucherCount==0){
                    voucherCode=singleVoucherCount+1;
                }else{
                    var singleVoucherCountSplit=singleVoucherCount.split('.');
                    $.each(singleVoucherCountSplit, function (key, dataArray) {
                        if(key==3){
                            voucherCode=parseInt(dataArray)+1;
                        }
                    });
                }
                //alert(voucherCode);
                var voucherCode = voucherCode.toString();
                var pad = "00000";
                var newCode = pad.substring(0, pad.length - voucherCode.length) + voucherCode;
                //alert(newCode);

                var tempValue = shortName+"."+newBranchCode+"."+projectTypeCode+"."+newCode;
                //alert(tempValue);
                $("#voucherCode"+idVal).val(tempValue);


            },
            error: function(_response){
                alert("Error");
            }
        });
    }       //END voucherCode Function


    $("#projectIdPV, #projectIdRV, #projectIdJV, #projectIdCV, #projectIdFT").change(function () {

        if(this.id == "projectIdPV") {
            var idVal="PV";
            var ideVal="PVe";
            var projectId = this.value;
            //alert(projectId);
            var voucherTypeId=$("#debitTabValue").val();
        }else if (this.id == "projectIdRV") {
            var idVal="RV";
            var ideVal="RVe";
            var projectId = this.value;
            //alert(projectId);
            var voucherTypeId=$("#creditTabValue").val();
        }else if (this.id == "projectIdJV") {
            var idVal="JV";
            var ideVal="JVe";
            var projectId = this.value;
            //alert(projectId);
            var voucherTypeId=$("#jounalTabValue").val();
        }else if (this.id == "projectIdCV") {
            var idVal="CV";
            var ideVal="CVe";
            var projectId = this.value;
            //alert(projectId);
            var voucherTypeId=$("#contraTabValue").val();
        }else if (this.id == "projectIdFT") {
            var idVal="FT";
            var ideVal="FTe";
            var projectId = this.value;
            //alert(projectId);
            var voucherTypeId = $("#fundTransferTabValue").val();
            // var voucherTypeId = "";
            // if($branchId == 1){
            //     voucherTypeId=$("#fundTransferTabValue").val();
            // }else{
            //     // fundTransferForBranchTabValue
            //     voucherTypeId=$("#fundTransferForBranchTabValue").val();
            // }
        }

        var projectId= $("#projectId"+idVal).val();
        //alert(projectId);
        if (!projectId) {
            $("#projectId"+idVal).val("");
            $("#projectId"+ideVal).show();
            $("#projectId"+ideVal).html("Select Project First");
            return false;
        }

        if(projectId==''){
            $("#voucherCode"+idVal).val('');
        }else{
            voucherCode(projectId, voucherTypeId, branchId, idVal);
        }


    });

// ==============================End Change Project(get ProjectType & ledgers) & ProjectType(get Voucher Code)==============================



//======================================================Send & Save into DB->Table ======================================================

     //var total_file = 0;
    var max_fields = 10;
    var i = 1;
    var check = 0;
    var tweetParentsArray = [];

    $(".chaneBeforeImagePV").on('change',function(){
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only Images are allowed");
            $('.imageuploadPV').val('');
        }else{
            var total_file=document.getElementsByClassName("imageuploadPV").length;
            $('.fetchPVData').append('<div class="col-sm-3" id ="imagePvRemove"  style="margin-top:10px;"><img src="'+URL.createObjectURL(event.target.files[0])+'" height="70" width="70"><button class="beforeRemovePvImage"   style="float:right; color:red; font-size:8px;">X</button></div>');
            $('.beforeImgInput').hide();

            $('.fetchPVData').on("click",".beforeRemovePvImage",function(){
                $(this).parent('div').remove();
                $('.beforeImgInput').remove();
            });
        }
    });

    $('#addMorePV').on('click',function(){
        if(i < max_fields){
            $('#addMorePVId').append('<div class="col-sm-12 extraImgInput" style="padding-left:0px; margin-top:10px;"><div class="col-sm-4" style="padding-left:0px;"><input type="file" name="imagePV[]"  class="imageuploadPV imagePVChange"></div><div class="col-sm-3"><button class="remove_field" style="float:right; color:red">X</button></br></div></div>');
            i++;
           "</br>"
        } else{
            alert('Maximum '+max_fields+' images can be uploaded.');
        }
        $(".imagePVChange").on('change',function(){
            var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                alert("Only Images are allowed");
                $('.imageuploadPV').val('');
            }else{
                var total_file=document.getElementsByClassName("imageuploadPV").length;
                for(var i=0; i<total_file; i++){
                    $('.fetchPVData').append('<div class="col-sm-3" id ="imagePvRemove"  style="margin-top:10px;"><img src="'+URL.createObjectURL(event.target.files[i])+'" height="70" width="70"><button class="removePvImage" data-id="'+(total_file-1)+'" style="float:right; color:red; font-size:8px;">X</button></div>');
                    $('.extraImgInput').hide();
                }
            }
        });
    });

    $('#addMorePVId').on("click",".remove_field", function(e){
        e.preventDefault();
       $(this).parent('div').parent('div').remove();
          i--;
    })
    $('#addMorePVId').on("click",".removePvImage",function(){
        console.log(tweetParentsArray);
        tweetParentsArray.push($(this).attr("data-id"));
        $(this).parent('div').remove();
    });

    $("#submitPV, #submitNPrintPV").click(function(){
        if (this.id == 'submitPV') {
            // alert('submitPV  clicked');
            var btnValue=0;
        }
        else if (this.id == 'submitNPrintPV') {
            // alert('submitNPrintPV clicked');
            var btnValue=1;
        }
        $("#submitPV, #submitNPrintPV").prop("disabled", true);

        //Get all the vlaues
        var prepBy = "<?php echo $userId; ?>";
        var branchId = "<?php echo $branchId; ?>";
        var companyId = "<?php echo $branch->companyId; ?>";
        var projectId = $("#projectIdPV option:selected").val();
        var projectTypeId = $("#projectTypeIdPV").val();
        var voucherDate = $("#voucherDatePV").val();
        var voucherCode = $("#voucherCodePV").val();
        var creditAcc = $("#creditAccPV").val();
        var voucherTypeId = $("#debitTabValue").val();
        var globalNarration = $("#globalNarrationPV").val();

        var csrf = "<?php echo csrf_token(); ?>";

        var tableCreditAcc = new Array();
        var tableDebitAcc = new Array();
        var tableAmount = new Array();
        var tableNarration = new Array();

        $("#addPVTable tr.valueRowPV").each(function(){
            tableDebitAcc.push($(this).find('.debitAccInputPV').val());
            tableCreditAcc.push($(this).find('.creditAccInputPV').val());
            tableAmount.push($(this).find('.amountColumnPV').html());
            tableNarration.push($(this).find('.narrationPV').html());
        });
            var amountColumn = $(".amountColumnPV").html(); //alert(amountColumnPV);

            formData = new FormData();

            var totalFiles = document.getElementsByClassName("imageuploadPV").length;
            console.log(tweetParentsArray);

            for(var index = 0; index < totalFiles; index++){
                for (var i = 0; i < tweetParentsArray.length; i++) {
                    if (tweetParentsArray[i] == index) {
                        ++check;
                    }
                }
                if (check == 0) {
                    formData.append('image[]',document.getElementsByClassName("imageuploadPV")[index].files[0]);
                }

            };

            formData.append('prepBy', prepBy);
            formData.append('voucherTypeId', voucherTypeId);
            formData.append('amountColumn', amountColumn);
            formData.append('branchId', branchId);
            formData.append('companyId', companyId);
            formData.append('projectId', projectId);
            formData.append('voucherDate', voucherDate);
            formData.append('voucherCode', voucherCode);
            formData.append('projectId', projectId);
            formData.append('projectTypeId', projectTypeId);
            formData.append('tableAmount', JSON.stringify(tableAmount));
            formData.append('tableDebitAcc', JSON.stringify(tableDebitAcc));
            formData.append('tableCreditAcc', JSON.stringify(tableCreditAcc));
            formData.append('tableNarration', JSON.stringify(tableNarration));
            formData.append('globalNarration', globalNarration);
            formData.append('_token', csrf);


        $.ajax({
            processData: false,
            contentType: false,
            type: 'post',
            url: './addVoucherItem',
            data: formData,
            // data: { prepBy: prepBy, voucherTypeId:voucherTypeId, amountColumn:amountColumn,
            //     // voucherIdPV:voucherIdPV,
            //     branchId: branchId, companyId: companyId, projectId: projectId, projectTypeId: projectTypeId, voucherDate: voucherDate, voucherCode: voucherCode, tableCreditAcc:tableCreditAcc, tableDebitAcc: tableDebitAcc, tableAmount: tableAmount, tableNarration: tableNarration, globalNarration:globalNarration, _token: csrf },
            dataType: 'json',
            success: function( _response ){

                // alert(JSON.stringify(_response));
                if (_response.errors) {
                    $("#submitPV, #submitNPrintPV").prop("disabled", false);
                    if (_response.errors['projectId']) {
                        $('#projectIdPVe').empty();
                        $('#projectIdPVe').append('<span style="color:red;">'+_response.errors.projectId+'</span>');
                        return false;
                    }
                    if (_response.errors['projectTypeId']) {
                        $('#projectTypeIdPVe').empty();
                        $('#projectTypeIdPVe').append('<span style="color:red;">'+_response.errors.projectTypeId+'</span>');
                        return false;
                    }
                    if (_response.errors['voucherCode']) {
                        $('#voucherCodePVe').empty();
                        $('#voucherCodePVe').append('<span style="color:red;">'+_response.errors.voucherCode+'</span>');
                        return false;
                    }
                    if (_response.errors['amountColumn']) {
                        $('#tablePVe').empty();
                        $('#tablePVe').append('<span style="color:red;">'+_response.errors.amountColumn+'</span>');
                        return false;
                    }
                    if (_response.errors['globalNarration']) {
                        $('#globalNarrationPVe').empty();
                        $('#globalNarrationPVe').append('<span style="color:red;">'+_response.errors.globalNarration+'</span>');
                        return false;
                    }
                } else {

                    if (btnValue==0) {
                        window.location.href = '{{url('viewVoucher/')}}';
                    }else if(btnValue==1){
                         {{-- // window.location.href = '{{url('printJournalVoucher/')}}'+'/.encrypt($'+_response+')';  --}}
                        window.location.href = '{{url('printVoucher/')}}'+'/'+_response;
                        // alert(_response);
                    }
                    // window.location.href = '{{url('viewVoucher/')}}';
                }
            },
            error: function( _response ){
                // Handle error
                alert(_response.errors);
            }

        });

    });

//=========================================END JavaScript for Debit Voucher//=========================================



//=========================================Starts JavaScript for Credit Voucher==============================================

// =====================================================Send & Save into DB->Table=====================================================

   $(".chaneBeforeImageRV").on('change',function(){
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only Images are allowed");
            $('.imageuploadRV').val('');
        }else{
            var total_file=document.getElementsByClassName("imageuploadPV").length;
            $('.fetchRVData').append('<div class="col-sm-3" id ="imageRvRemove"  style="margin-top:10px;"><img src="'+URL.createObjectURL(event.target.files[0])+'" height="70" width="70"><button class="beforeRemoveRvImage"   style="float:right; color:red; font-size:8px;">X</button></div>');
            $('.beforeImgInputRV').hide();

            $('.fetchRVData').on("click",".beforeRemoveRvImage",function(){
                $(this).parent('div').remove();
                $('.beforeImgInputRV').remove();
            });
        }
    });

     $('#addMoreRV').on('click',function(){
        if(i < max_fields){
            $('#addMoreRVId').append('<div class="col-sm-12 extraImgInput" style="padding-left:0px; margin-top:10px;"><div class="col-sm-4" style="padding-left:0px;"><input type="file" name="imageRV[]"  class="imageuploadRV imageRVChange"></div><div class="col-sm-3"><button class="remove_field" style="float:right; color:red">X</button></br></div></div>');
            i++;
           "</br>"
        } else{
            alert('Maximum '+max_fields+' images can be uploaded.');
        }
        $(".imageRVChange").on('change',function(){
            var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                alert("Only Images are allowed");
                $('.imageuploadRV').val('');
            }else{
               var total_file=document.getElementsByClassName("imageuploadRV").length;
                for(var i=0; i<total_file; i++){
                    $('.fetchRVData').append('<div class="col-sm-3" id ="imageRvRemove"  style="margin-top:10px;"><img src="'+URL.createObjectURL(event.target.files[i])+'" height="70" width="70"><button class="removeRvImage" data-id="'+(total_file-1)+'" style="float:right; color:red; font-size:8px;">X</button></div>');
                    $('.extraImgInput').hide();
                }
            }
        });
    });

    $('#addMoreRVId').on("click",".remove_field", function(e){
        e.preventDefault();
       $(this).parent('div').parent('div').remove();
          i--;
    })
    $('#addMoreRVId').on("click",".removeRvImage",function(){
        tweetParentsArray.push($(this).attr("data-id"));
        $(this).parent('div').remove();
     });



    $("#submitRV, #submitNPrintRV").click(function(){
        if (this.id == 'submitRV') {
            var btnValue=0;
        }
        else if (this.id == 'submitNPrintRV') {
            var btnValue=1;
        }
        $("#submitRV, #submitNPrintRV").prop("disabled", true);

        //Get all the vlaues
        var prepBy = "<?php echo $userId; ?>";
        var branchId = "<?php echo $branchId; ?>";
        var companyId = "<?php echo $branch->companyId; ?>";
        var projectId = $("#projectIdRV option:selected").val();
        var projectTypeId = $("#projectTypeIdRV option:selected").val();
        var voucherDate = $("#voucherDateRV").val();
        var voucherCode = $("#voucherCodeRV").val();
        // var debitAcc = $("#debitAccRV").val();
        var voucherTypeId = $("#creditTabValue").val();
        var globalNarration = $("#globalNarrationRV").val();

        var csrf = "<?php echo csrf_token(); ?>";
        var tableDebitAcc = new Array();
        var tableCreditAcc = new Array();
        var tableAmount = new Array();
        var tableNarration = new Array();

        $("#addRVTable tr.valueRowRV").each(function(){
            tableDebitAcc.push($(this).find('.debitAccInputRV').val());
            tableCreditAcc.push($(this).find('.creditAccInputRV').val());
            tableAmount.push($(this).find('.amountColumnRV').html());
            tableNarration.push($(this).find('.narrationRV').html());
        });
        var amountColumn = $(".amountColumnRV").html(); //alert(amountColumnRV);

            formData = new FormData();

            var totalFiles = document.getElementsByClassName("imageuploadRV").length;

            for(var index = 0; index < totalFiles; index++){
                for (var i = 0; i < tweetParentsArray.length; i++) {
                    if (tweetParentsArray[i] == index) {
                        ++check;
                    }
                }
                if (check == 0) {
                    formData.append('image[]',document.getElementsByClassName("imageuploadRV")[index].files[0]);
                }

            };

            formData.append('prepBy', prepBy);
            formData.append('voucherTypeId', voucherTypeId);
            formData.append('amountColumn', amountColumn);
            formData.append('branchId', branchId);
            formData.append('companyId', companyId);
            formData.append('projectId', projectId);
            formData.append('voucherDate', voucherDate);
            formData.append('voucherCode', voucherCode);
            formData.append('projectId', projectId);
            formData.append('projectTypeId', projectTypeId);
            formData.append('tableAmount', JSON.stringify(tableAmount));
            formData.append('tableDebitAcc', JSON.stringify(tableDebitAcc));
            formData.append('tableCreditAcc', JSON.stringify(tableCreditAcc));
            formData.append('tableNarration', JSON.stringify(tableNarration));
            formData.append('globalNarration', globalNarration);
            formData.append('_token', csrf);

        $.ajax({
            processData: false,
            contentType: false,
            type: 'post',
            url: './addVoucherItem',
            data: formData,


            //data: { prepBy: prepBy, voucherTypeId:voucherTypeId, amountColumn:amountColumn,
                // // voucherId:voucherId,
                // branchId: branchId, companyId: companyId, projectId: projectId, projectTypeId: projectTypeId, voucherDate: voucherDate, voucherCode: voucherCode, tableDebitAcc: tableDebitAcc, tableCreditAcc: tableCreditAcc, tableAmount: tableAmount, tableNarration: tableNarration, globalNarration:globalNarration, _token: csrf },
            dataType: 'json',

            success: function( _response ){
                // alert(JSON.stringify(_response));
                if (_response.errors) {
                    $("#submitPV, #submitNPrintPV").prop("disabled", false);
                    if (_response.errors['projectId']) {
                        $('#projectIdRVe').empty();
                        $('#projectIdRVe').append('<span style="color:red;">'+_response.errors.projectId+'</span>');
                        return false;
                    }
                    if (_response.errors['projectTypeId']) {
                        $('#projectTypeIdRVe').empty();
                        $('#projectTypeIdRVe').append('<span style="color:red;">'+_response.errors.projectTypeId+'</span>');
                        return false;
                    }
                    if (_response.errors['voucherCode']) {
                        $('#voucherCodeRVe').empty();
                        $('#voucherCodeRVe').append('<span style="color:red;">'+_response.errors.voucherCode+'</span>');
                        return false;
                    }
                    if (_response.errors['amountColumn']) {
                        $('#tableRVe').empty();
                        $('#tableRVe').append('<span style="color:red;">'+_response.errors.amountColumn+'</span>');
                        return false;
                    }
                    if (_response.errors['globalNarration']) {
                        $('#globalNarrationRVe').empty();
                        $('#globalNarrationRVe').append('<span style="color:red;">'+_response.errors.globalNarration+'</span>');
                        return false;
                    }
                } else {

                // alert(_response);
                    if (btnValue==0) {
                        window.location.href = '{{url('viewVoucher/')}}';
                    }else if(btnValue==1){
                        window.location.href = '{{url('printVoucher/')}}'+'/'+_response;
                    }
                    // window.location.href = '{{url('viewVoucher/')}}';
//                        document.getElementById("msg").innerHTML =responseText ;
                }
            },
            error: function( _response ){
                // Handle error
                alert(_response.errors);
            }
        });
    });
// ==============================================END JavaScript for Credit Voucher==============================================



// ===========================================Starts JavaScript for Journal Voucher===============================================

// ===================================================Send & Save into DB->Table===================================================
   $(".chaneBeforeImageJV").on('change',function(){
        //var total_file=document.getElementsByClassName("imageuploadPV").length;
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                alert("Only Images are allowed");
                $('.imageuploadJV').val('');
            }else{
            $('.fetchJVData').append('<div class="col-sm-3" id ="imageJvRemove"  style="margin-top:10px;"><img src="'+URL.createObjectURL(event.target.files[0])+'" height="70" width="70"><button class="beforeRemoveJvImage"   style="float:right; color:red; font-size:8px;">X</button></div>');
            $('.beforeImgInputJV').hide();

            $('.fetchJVData').on("click",".beforeRemoveJvImage",function(){
                $(this).parent('div').remove();
                $('.beforeImgInputJV').remove();
            });
        }
    });

    $('#addMoreJV').on('click',function(){
        if(i < max_fields){
            $('#addMoreJVId').append('<div class="col-sm-12 extraImgInput" style="padding-left:0px; margin-top:10px;"><div class="col-sm-4" style="padding-left:0px;"><input type="file" name="imageJV[]"  class="imageuploadJV imageJVChange"></div><div class="col-sm-3"><button class="remove_field" style="float:right; color:red">X</button></br></div></div>');
            i++;
           "</br>"
        } else{
            alert('Maximum '+max_fields+' images can be uploaded.');
        }
        $(".imageJVChange").on('change',function(){
            var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                alert("Only Images are allowed");
                $('.imageuploadJV').val('');
            }else{
                var total_file=document.getElementsByClassName("imageuploadJV").length;
                for(var i=0; i<total_file; i++){
                    $('.fetchJVData').append('<div class="col-sm-3" id ="imageJvRemove"  style="margin-top:10px;"><img src="'+URL.createObjectURL(event.target.files[i])+'" height="70" width="70"><button class="removeJvImage" data-id="'+(total_file-1)+'" style="float:right; color:red; font-size:8px;">X</button></div>');
                    $('.extraImgInput').hide();
                }
            }
        });
    });

    $('#addMoreJVId').on("click",".remove_field", function(e){
        e.preventDefault();
       $(this).parent('div').parent('div').remove();
          i--;
    })
    $('#addMoreJVId').on("click",".removeJvImage",function(){
        tweetParentsArray.push($(this).attr("data-id"));
        $(this).parent('div').remove();
    });


//        var p=0;
    $("#submitJV, #submitNPrintJV").click(function(){
//            $('form').submit(function( event ) {
        // alert("submit bottom is pressed" + $(this));
//                event.preventDefault();
        if (this.id == 'submitJV') {
            // alert('submitJV  clicked');
            var btnValue=0;
        }
        else if (this.id == 'submitNPrintJV') {
            // alert('submitNPrintJV clicked');
            var btnValue=1;
        }
        $("#submitJV, #submitNPrintJV").prop("disabled", true);

        //Get all the vlaues
        var prepBy = "<?php echo $userId; ?>";
        var branchId = "<?php echo $branchId; ?>";
        var companyId = "<?php echo $branch->companyId; ?>";
        var projectId = $("#projectIdJV option:selected").val();
        var projectTypeId = $("#projectTypeIdJV option:selected").val();
        var voucherDate = $("#voucherDateJV").val();
        var voucherCode = $("#voucherCodeJV").val();
        var voucherTypeId = $("#jounalTabValue").val();
//            var amountColumn = $(".amountColumn").html(); alert(amountColumn);
        var globalNarration = $("#globalNarrationJV").val();

        var csrf = "<?php echo csrf_token(); ?>";

        //Get Table Data for Journal Details Table
        var tableDebitAcc = new Array();
        var tableCreditAcc = new Array();
        var tableAmount = new Array();
        var tableNarration = new Array();
        //alert("submit bottom is pressed" + $(this));
        $("#addJVTable tr.valueRowJV").each(function(){
            tableDebitAcc.push($(this).find('.debitAccInputJV').val());
            tableCreditAcc.push($(this).find('.creditAccInputJV').val());
            tableAmount.push($(this).find('.amountColumnJV').html());
            tableNarration.push($(this).find('.narrationJV').html());
        });

        // alert(branchId);
        // alert(companyId);
        // alert(projectId);
        // alert(projectTypeId);
        // alert(voucherDate);
        // alert(voucherCode);
        // alert(globalNarration);

        // alert(tableDebitAcc);
        // alert(tableCreditAcc);
        // alert(tableAmount);
        // alert(tableNarration);

        var amountColumn = $(".amountColumnJV").html(); //alert(amountColumn);

            formData = new FormData();

            var totalFiles = document.getElementsByClassName("imageuploadJV").length;

            for(var index = 0; index < totalFiles; index++){
                for (var i = 0; i < tweetParentsArray.length; i++) {
                    if (tweetParentsArray[i] == index) {
                        ++check;
                    }
                }
                if (check == 0) {
                    formData.append('image[]',document.getElementsByClassName("imageuploadJV")[index].files[0]);
                }

            };

            formData.append('prepBy', prepBy);
            formData.append('voucherTypeId', voucherTypeId);
            formData.append('amountColumn', amountColumn);
            formData.append('branchId', branchId);
            formData.append('companyId', companyId);
            formData.append('projectId', projectId);
            formData.append('voucherDate', voucherDate);
            formData.append('voucherCode', voucherCode);
            formData.append('projectId', projectId);
            formData.append('projectTypeId', projectTypeId);
            formData.append('tableAmount', JSON.stringify(tableAmount));
            formData.append('tableDebitAcc', JSON.stringify(tableDebitAcc));
            formData.append('tableCreditAcc', JSON.stringify(tableCreditAcc));
            formData.append('tableNarration', JSON.stringify(tableNarration));
            formData.append('globalNarration', globalNarration);
            formData.append('_token', csrf);

        $.ajax({
            processData: false,
            contentType: false,
            type: 'post',
            url: './addVoucherItem',
            data: formData,



            // data: $('form').serialize(),
            // data: { prepBy: prepBy, voucherTypeId:voucherTypeId, amountColumn:amountColumn,
            //     // voucherId:voucherId,
            //     branchId: branchId, companyId: companyId, projectId: projectId, projectTypeId: projectTypeId, voucherDate: voucherDate, voucherCode: voucherCode, tableDebitAcc: tableDebitAcc, tableCreditAcc: tableCreditAcc, tableAmount: tableAmount, tableNarration: tableNarration, globalNarration:globalNarration, _token: csrf },

            dataType: 'json',

            success: function( _response ){
                // alert(JSON.stringify(_response));
                if (_response.errors) {
                    $("#submitJV, #submitNPrintJV").prop("disabled", false);
                    if (_response.errors['projectId']) {
                        $('#projectIdJVe').empty();
                        $('#projectIdJVe').append('<span style="color:red;">'+_response.errors.projectId+'</span>');
                        return false;
                    }
                    if (_response.errors['projectTypeId']) {
                        $('#projectTypeIdJVe').empty();
                        $('#projectTypeIdJVe').append('<span style="color:red;">'+_response.errors.projectTypeId+'</span>');
                        return false;
                    }
                    if (_response.errors['voucherCode']) {
                        $('#voucherCodeJVe').empty();
                        $('#voucherCodeJVe').append('<span style="color:red;">'+_response.errors.voucherCode+'</span>');
                        return false;
                    }
                    if (_response.errors['amountColumn']) {
                        $('#tableJVe').empty();
                        $('#tableJVe').append('<span style="color:red;">'+_response.errors.amountColumn+'</span>');
                        return false;
                    }
                    if (_response.errors['globalNarration']) {
                        $('#globalNarrationJVe').empty();
                        $('#globalNarrationJVe').append('<span style="color:red;">'+_response.errors.globalNarration+'</span>');
                        return false;
                    }

                } else {
                    if (btnValue==0) {
                        window.location.href = '{{url('viewVoucher/')}}';
                    }else if(btnValue==1){
                        window.location.href = '{{url('printVoucher/')}}'+'/'+_response;
                    }
                    // window.location.href = '{{url('viewVoucher/')}}';
//                        document.getElementById("msg").innerHTML =responseText ;
                }
            },
            error: function( _response ){
                // Handle error
                alert("errors");
            }
        });
    });
// ============================================End JavaScript for Journal Voucher============================================


// =============================================Starts JavaScript for Contra Voucher=============================================
// ====================================================Send & Save into DB->Table==================================================

     $(".chaneBeforeImageCV").on('change',function(){
        //var total_file=document.getElementsByClassName("imageuploadPV").length;
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only Images are allowed");
            $('.imageuploadCV').val('');
        }else{
            $('.fetchCVData').append('<div class="col-sm-3" id ="imageCvRemove"  style="margin-top:10px;"><img src="'+URL.createObjectURL(event.target.files[0])+'" height="70" width="70"><button class="beforeRemoveCvImage"   style="float:right; color:red; font-size:8px;">X</button></div>');
            $('.beforeImgInputCV').hide();

            $('.fetchCVData').on("click",".beforeRemoveCvImage",function(){
                $(this).parent('div').remove();
                $('.beforeImgInputCV').remove();
            });
        }
    });

    $('#addMoreCV').on('click',function(){
        if(i < max_fields){
            $('#addMoreCVId').append('<div class="col-sm-12 extraImgInput" style="padding-left:0px; margin-top:10px;"><div class="col-sm-4" style="padding-left:0px;"><input type="file" name="imageCV[]"  class="imageuploadCV imageCVChange"></div><div class="col-sm-3"><button class="remove_field" style="float:right; color:red">X</button></br></div></div>');
            i++;
           "</br>"
        } else{
            alert('Maximum '+max_fields+' images can be uploaded.');
        }
        $(".imageCVChange").on('change',function(){
            var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                alert("Only Images are allowed");
                $('.imageuploadCV').val('');
            }else{
                var total_file=document.getElementsByClassName("imageuploadCV").length;
                for(var i=0; i<total_file; i++){
                    $('.fetchCVData').append('<div class="col-sm-3" id ="imageCvRemove"  style="margin-top:10px;"><img src="'+URL.createObjectURL(event.target.files[i])+'" height="70" width="70"><button class="removeCvImage" data-id="'+(total_file-1)+'" style="float:right; color:red; font-size:8px;">X</button></div>');
                    $('.extraImgInput').hide();
                }
            }
        });
    });

    $('#addMoreCVId').on("click",".remove_field", function(e){
        e.preventDefault();
       $(this).parent('div').parent('div').remove();
          i--;
    })
    $('#addMoreCVId').on("click",".removeCvImage",function(){
        tweetParentsArray.push($(this).attr("data-id"));
        $(this).parent('div').remove();
    });


    $("#submitCV, #submitNPrintCV").click(function(){

        if (this.id == 'submitCV') {
            // alert('submitCV  clicked');
            var btnValue=0;
        }
        else if (this.id == 'submitNPrintCV') {
            // alert('submitNPrintCV clicked');
            var btnValue=1;
        }
        $("#submitCV, #submitNPrintCV").prop("disabled", true);

        //Get all the vlaues
        var prepBy = "<?php echo $userId; ?>";
        var branchId = "<?php echo $branchId; ?>";
        var companyId = "<?php echo $branch->companyId; ?>";
        var projectId = $("#projectIdCV option:selected").val();
        var projectTypeId = $("#projectTypeIdCV option:selected").val();
        var voucherDate = $("#voucherDateCV").val();
        var voucherCode = $("#voucherCodeCV").val();
        var voucherTypeId = $("#contraTabValue").val();
        var globalNarration = $("#globalNarrationCV").val();

        var csrf = "<?php echo csrf_token(); ?>";

        var tableDebitAcc = new Array();
        var tableCreditAcc = new Array();
        var tableAmount = new Array();
        var tableNarration = new Array();

        $("#addCVTable tr.valueRowCV").each(function(){
            tableDebitAcc.push($(this).find('.debitAccInputCV').val());
            tableCreditAcc.push($(this).find('.creditAccInputCV').val());
            tableAmount.push($(this).find('.amountColumnCV').html());
            tableNarration.push($(this).find('.narrationCV').html());
        });
        var amountColumn = $(".amountColumnCV").html(); //alert(amountColumn);
        formData = new FormData();
        var totalFiles = document.getElementsByClassName("imageuploadCV").length;

            for(var index = 0; index < totalFiles; index++){
                for (var i = 0; i < tweetParentsArray.length; i++) {
                    if (tweetParentsArray[i] == index) {
                        ++check;
                    }
                }
                if (check == 0) {
                    formData.append('image[]',document.getElementsByClassName("imageuploadCV")[index].files[0]);
                }

            };

            formData.append('prepBy', prepBy);
            formData.append('voucherTypeId', voucherTypeId);
            formData.append('amountColumn', amountColumn);
            formData.append('branchId', branchId);
            formData.append('companyId', companyId);
            formData.append('projectId', projectId);
            formData.append('voucherDate', voucherDate);
            formData.append('voucherCode', voucherCode);
            formData.append('projectId', projectId);
            formData.append('projectTypeId', projectTypeId);
            formData.append('tableAmount', JSON.stringify(tableAmount));
            formData.append('tableDebitAcc', JSON.stringify(tableDebitAcc));
            formData.append('tableCreditAcc', JSON.stringify(tableCreditAcc));
            formData.append('tableNarration', JSON.stringify(tableNarration));
            formData.append('globalNarration', globalNarration);
            formData.append('_token', csrf);

        $.ajax({
            processData: false,
            contentType: false,
            type: 'post',
            url: './addVoucherItem',
            data: formData,

            // data:
            // { prepBy: prepBy, voucherTypeId:voucherTypeId, amountColumn:amountColumn,
            //     // voucherId:voucherId,
            //     branchId: branchId, companyId: companyId, projectId: projectId, projectTypeId: projectTypeId, voucherDate: voucherDate, voucherCode: voucherCode, tableDebitAcc: tableDebitAcc, tableCreditAcc: tableCreditAcc, tableAmount: tableAmount, tableNarration: tableNarration, globalNarration:globalNarration, _token: csrf },

            dataType: 'json',

            success: function( _response ){
                // alert(JSON.stringify(_response));
                if (_response.errors) {
                    $("#submitCV, #submitNPrintCV").prop("disabled", false);
                    if (_response.errors['projectId']) {
                        $('#projectIdCVe').empty();
                        $('#projectIdCVe').append('<span style="color:red;">'+_response.errors.projectId+'</span>');
                        return false;
                    }
                    if (_response.errors['projectTypeId']) {
                        $('#projectTypeIdCVe').empty();
                        $('#projectTypeIdCVe').append('<span style="color:red;">'+_response.errors.projectTypeId+'</span>');
                        return false;
                    }
                    if (_response.errors['voucherCode']) {
                        $('#voucherCodeCVe').empty();
                        $('#voucherCodeCVe').append('<span style="color:red;">'+_response.errors.voucherCode+'</span>');
                        return false;
                    }
                    if (_response.errors['amountColumn']) {
                        $('#tableCVe').empty();
                        $('#tableCVe').append('<span style="color:red;">'+_response.errors.amountColumn+'</span>');
                        return false;
                    }
                    if (_response.errors['globalNarration']) {
                        $('#globalNarrationCVe').empty();
                        $('#globalNarrationCVe').append('<span style="color:red;">'+_response.errors.globalNarration+'</span>');
                        return false;
                    }
                } else {

                    if (btnValue==0) {
                        window.location.href = '{{url('viewVoucher/')}}';
                    }else if(btnValue==1){
                        window.location.href = '{{url('printVoucher/')}}'+'/'+_response;
                    }
                    // window.location.href = '{{url('viewVoucher/')}}';
                }
            },
            error: function( _response ){
                // Handle error
                alert(_response.errors);
            }
        });
    });
// ==============================================END JavaScript for Contra Voucher==============================================


// ===========================================Starts JavaScript for Fund Transfer Voucher===============================================

// ===================================================Send & Save into DB->Table===================================================
//        var p=0;
    // $(".chaneBeforeImageFT").on('change',function(){
    //     $('.fetchFTData').append('<div class="col-sm-3" id ="imageFtRemove"  style="margin-top:10px;"><img src="'+URL.createObjectURL(event.target.files[0])+'" height="70" width="70"><button class="beforeRemoveFtImage"   style="float:right; color:red; font-size:8px;">X</button></div>');
    //     $('.beforeImgInputFT').hide();

    //     $('.fetchFTData').on("click",".beforeRemoveFtImage",function(){
    //         $(this).parent('div').remove();
    //         $('.beforeImgInputFT').remove();
    //     });
    // });

    // $('#addMoreFT').on('click',function(){
    //     if(i < max_fields){
    //         $('#addMoreFTId').append('<div class="col-sm-12 extraImgInput" style="padding-left:0px; margin-top:10px;"><div class="col-sm-4" style="padding-left:0px;"><input type="file" name="imageFT[]"  class="imageuploadFT imageFTChange"></div><div class="col-sm-3"><button class="remove_field" style="float:right; color:red">X</button></br></div></div>');
    //         i++;
    //        "</br>"
    //     } else{
    //         alert('Maximum '+max_fields+' images can be uploaded.');
    //     }
    //     $(".imageFTChange").on('change',function(){
    //        var total_file=document.getElementsByClassName("imageuploadFT").length;
    //         for(var i=0; i<total_file; i++){
    //             $('.fetchFTData').append('<div class="col-sm-3" id ="imageFTRemove"  style="margin-top:10px;"><img src="'+URL.createObjectURL(event.target.files[i])+'" height="70" width="70"><button class="removeFTImage" data-id="'+(total_file-1)+'" style="float:right; color:red; font-size:8px;">X</button></div>');
    //             $('.extraImgInput').hide();
    //         }
    //     });
    // });

    // $('#addMoreFTId').on("click",".remove_field", function(e){
    //     e.preventDefault();
    //    $(this).parent('div').parent('div').remove();
    //       i--;
    // })
    // $('#addMoreFTId').on("click",".removeFTImage",function(){
    //     console.log(tweetParentsArray);
    //     tweetParentsArray.push($(this).attr("data-id"));
    //     $(this).parent('div').remove();
    // });


    //$("#submitFT, #submitNPrintFT").click(function(){
        // if (this.id == 'submitFT') {
        //     var btnValue=0;
        // }
        // else if (this.id == 'submitNPrintFT') {
        //     var btnValue=1;
        // }
        //$("#submitFT, #submitNPrintFT").prop("disabled", true);

        //Get all the vlaues
        // var prepBy = "{{$userId}}";
        // var branchId = "{{$branchId}}";
        // var companyId = "{{$branch->companyId}}";
        // var projectId = $("#projectIdFT option:selected").val();
        // var projectTypeId = $("#projectTypeIdFT option:selected").val();
        // var voucherDate = $("#voucherDateFT").val();
        // var voucherCode = $("#voucherCodeFT").val();
        // var voucherTypeId = $("#fundTransferTabValue").val();
        // var voucherTypeId = "";
        // if($branchId == 1){
        //     voucherTypeId=$("#fundTransferTabValue").val();
        // }else{
        //     // undTransferForBranchTabValue
        //     voucherTypeId=$("#fundTransferForBranchTabValue").val();
        // }

        // var globalNarration = $("#globalNarrationFT").val();

        // var csrf = "<?php echo csrf_token(); ?>";

        //Get Table Data for Journal Details Table
        // var tableTargetBranch = new Array();
        // var tableTargetBranchHead = new Array();
        // var tableDebitAcc = new Array();
        // var tableCreditAcc = new Array();
        // var tableAmount = new Array();
        // var tableNarration = new Array();
        // $("#addFTTable tr.valueRowFT").each(function(){
        //     tableTargetBranch.push($(this).find('.targetBranchInputFT').val());
        //     tableTargetBranchHead.push($(this).find('.targetBranchHeadInputFT').val());
        //     tableDebitAcc.push($(this).find('.debitAccInputFT').val());
        //     tableCreditAcc.push($(this).find('.creditAccInputFT').val());
        //     tableAmount.push($(this).find('.amountColumnFT').html());
        //     tableNarration.push($(this).find('.narrationFT').html());
        //     // alert(tableTargetBranch.push(JSON.stringify($(this).find('.targetBranchInputFT').val())));
        // });
        // alert(tableTargetBranch);

        // alert(tableTargetBranch);
        // alert(tableTargetBranchHead);
        // alert(tableDebitAcc);
        // alert(tableCreditAcc);
        // alert(tableAmount);
        // alert(tableNarration);

        //var amountColumn = $(".amountColumnFT").html(); //alert(amountColumn);
            // formData = new FormData();
            // var totalFiles = document.getElementsByClassName("imageuploadFT").length;

           // for(var index = 0; index < totalFiles; index++){
           //      for (var i = 0; i < tweetParentsArray.length; i++) {
           //          if (tweetParentsArray[i] == index) {
           //              ++check;
           //          }
           //      }
           //      if (check == 0) {
           //          formData.append('image[]',document.getElementsByClassName("imageuploadFT")[index].files[0]);
           //      }

           //  };

            // formData.append('prepBy', prepBy);
            // formData.append('voucherTypeId', voucherTypeId);
            // formData.append('amountColumn', amountColumn);
            // formData.append('branchId', branchId);
            // formData.append('companyId', companyId);
            // formData.append('projectId', projectId);
            //  formData.append('projectTypeId', projectTypeId);
            // formData.append('voucherDate', voucherDate);
            // formData.append('voucherCode', voucherCode);
            // formData.append('tableTargetBranch', JSON.stringify(tableTargetBranch));
            // formData.append('tableTargetBranchHead', JSON.stringify(tableTargetBranchHead));
            // formData.append('tableDebitAcc', JSON.stringify(tableDebitAcc));
            // formData.append('tableCreditAcc', JSON.stringify(tableCreditAcc));
            //  formData.append('tableAmount', JSON.stringify(tableAmount));
            // formData.append('tableNarration', JSON.stringify(tableNarration));
            // formData.append('globalNarration', globalNarration);
            // formData.append('_token', csrf);


        // $.ajax({
        //     processData: false,
        //     contentType: false,
        //     type: 'post',
        //     url: './addFTVoucherItem',
        //     data: formData,
//                data: $('form').serialize(),
            // data: { prepBy: prepBy, voucherTypeId:voucherTypeId, amountColumn:amountColumn, branchId: branchId, companyId: companyId, projectId: projectId, projectTypeId: projectTypeId, voucherDate: voucherDate, voucherCode: voucherCode, tableTargetBranch: tableTargetBranch, tableTargetBranchHead: tableTargetBranchHead, tableDebitAcc: tableDebitAcc, tableCreditAcc: tableCreditAcc, tableAmount: tableAmount, tableNarration: tableNarration, globalNarration:globalNarration, _token: csrf },
            //dataType: 'json',

            // success: function( _response ){
            //     // alert(JSON.stringify(_response));
            //     if (_response.errors) {
            //         $("#submitFT, #submitNPrintFT").prop("disabled", false);
            //         if (_response.errors['projectId']) {
            //             $('#projectIdFTe').empty();
            //             $('#projectIdFTe').append('<span style="color:red;">'+_response.errors.projectId+'</span>');
            //             return false;
            //         }
            //         if (_response.errors['projectTypeId']) {
            //             $('#projectTypeIdFTe').empty();
            //             $('#projectTypeIdFTe').append('<span style="color:red;">'+_response.errors.projectTypeId+'</span>');
            //             return false;
            //         }
            //         if (_response.errors['voucherDate']) {
            //             $('#voucherDateFTe').empty();
            //             $('#voucherDateFTe').append('<span style="color:red;">'+_response.errors.voucherCode+'</span>');
            //             return false;
            //         }
            //         if (_response.errors['voucherCode']) {
            //             $('#voucherCodeFTe').empty();
            //             $('#voucherCodeFTe').append('<span style="color:red;">'+_response.errors.voucherCode+'</span>');
            //             return false;
            //         }
            //         if (_response.errors['amountColumn']) {
            //             $('#tableFTe').empty();
            //             $('#tableFTe').append('<span style="color:red;">'+_response.errors.amountColumn+'</span>');
            //             return false;
            //         }
            //         if (_response.errors['globalNarration']) {
            //             $('#globalNarrationFTe').empty();
            //             $('#globalNarrationFTe').append('<span style="color:red;">'+_response.errors.globalNarration+'</span>');
            //             return false;
            //         }

            //     } else {
            //     // alert(_response);
            //         if (btnValue==0) {
            //             window.location.href = '{{url('viewVoucher/')}}';
            //         }else if(btnValue==1){
            //             window.location.href = '{{url('printVoucher/')}}'+'/'+_response;
            //         }

            //     }
            // },
            // error: function( _response ){
            //     // Handle error
            //     alert("errors");
            // }
       // });


    //});
// ============================================End JavaScript for Fund Transfer Voucher============================================


});     //End ready Function

</script>

@endsection
