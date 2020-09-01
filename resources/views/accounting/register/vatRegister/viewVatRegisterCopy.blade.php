@extends('layouts/acc_layout')
@section('title', '| VAT GENERATE LIST')
@section('content')
<?php use Carbon\Carbon; ?>


<div class="row" id="vatGenerateList">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('accVatRegister/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add VAT</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">VAT GENERATE LIST</font></h1>

        </div>
        <script type="text/javascript">
        jQuery(document).ready(function($)
        {
         /* $("#otsTable").dataTable().yadcf([

          ]);*/
          $("#vatTable").dataTable({


                 "oLanguage": {
                "sEmptyTable": "No Records Available",
                "sLengthMenu": "Show _MENU_ "
                }
              });



     });

        </script>

        <div class="panel-body panelBodyView">

          <table class="table table-striped table-bordered" id="vatTable" style="color: black;">
                    <thead>
                      <tr>
                        <th rowspan="2">SL#</th>
                        <th rowspan="2">Voucher Date</th>
                        <th rowspan="2">Voucher No</th>
                        <th rowspan="2">Account Head</th>


                        <th colspan="2" rowspan="1" >Bill Information</th>
                        <th rowspan="2">VAT Type</th>
                        <th rowspan="2">VAT Rate (%)</th>
                        <th rowspan="2">VAT Amount (Tk)</th>
                        <th rowspan="2">Status</th>
                        <th rowspan="2">Action</th>
                      </tr>
                      <tr id="billInformation">
                        <th rowspan="1" colspan="1">Bill Date</th>
                        <th rowspan="1" colspan="1">Bill Amount</th>
                      </tr>

                    </thead>
                    <tbody>
                  @php $count=1; @endphp
                   @foreach($viewVatRegisters as $viewVatRegister)
                     @if($viewVatRegister->softDel==0)
                     <tr>
                        <td>{{$count}}</td>
                        <td>{{Carbon::parse($viewVatRegister->voucherDate)->format('d-m-Y')}}</td>
                        <td >{{$viewVatRegister->voucherNo}}</td>
                        <td style="text-align:left;">{{$viewVatRegister->ledger}}</td>

                        <td>{{Carbon::parse($viewVatRegister->billDate)->format('d-m-Y')}}</td>

                        <td style="text-align:right;">{{$viewVatRegister->billAmount}}</td>
                          <td>{{$viewVatRegister->serviceName}}</td>
                        <td>{{$viewVatRegister->vatInterestRate}}</td>
                        <td class="amount">{{$viewVatRegister->vatAmount}}</td>
                        {{-- <td>


                           <button class="btn btn-info"  style="width: 2px; line-height: 2px; text-align: center; font-size: 12px;background-color: #8F493D !important;  border: 0px solid #525659; border-radius: 10px;" disabled>
                                <span><i class="fa fa-times" aria-hidden="true"></i></span>
                            </button>

                            <button class="btn btn-info"  style="width: 2px; line-height: 2px; text-align: center; font-size: 12px;background-color: green !important;  border: 0px solid #525659; border-radius: 10px;" disabled>
                                <span><i class="fa fa-check" aria-hidden="true"></i></span>
                            </button>


                        </td> --}}



                         <td>

                     @if($viewVatRegister->status == 0)
                          <p style="color:Black;">Unpaid</p>
                       @else
                            <span><i class="" aria-hidden="true" style="color:black;font-size: 1.3;">Paid</i></span>
                    @endif

                        </td>


                        <td>
                          <a href="javascript:;" class="view-modal" vatBillTypeIdViewModal="{{$viewVatRegister->id}}">
                             <i class="fa fa-eye" aria-hidden="true"></i>
                         </a>&nbsp;


                           @if($viewVatRegister->status == 0)
                           <a href="javascript:;" class="pay-modal" vatBillTypeIdPayModal="{{$viewVatRegister->id}}"  vatAmount="{{$viewVatRegister->vatAmount}}">
                              <span class="glyphicon glyphicon-shopping-cart"></span>


                          </a>&nbsp;



                               <a href="javascript:;" class="edit-modal" vatBillTypeIdeditModal="{{$viewVatRegister->id}}" vatBillTypeIdNo="{{$viewVatRegister->billTypeIdFk}}">
                                  <span class="glyphicon glyphicon-edit"></span>

                              </a>&nbsp;

                              <a href="javascript:;" class="delete-modal" vatBillTypeIdDeleteModal="{{$viewVatRegister->id}}">
                                 <span class="glyphicon glyphicon-trash"></span>
                             </a>
                             @else
                             <a >
                                <span class="glyphicon glyphicon-shopping-cart"></span>

                            </a>&nbsp;



                            <a href="javascript:;" class="edit-modal" vatBillTypeIdeditModal="{{$viewVatRegister->id}}" vatBillTypeIdNo="{{$viewVatRegister->billTypeIdFk}}">
                               <span class="glyphicon glyphicon-edit"></span>
                           </a>&nbsp;

                                <a >
                                   <span class="glyphicon glyphicon-trash"></span>
                               </a>
                        @endif


                      </td>
                     </tr>
                     @endif
                     @php $count++; @endphp
                     @endforeach


                </tbody>
          </table>
          {{--{{$viewVatRegisters->links() }}--}}
        </div>
      </div>
  </div>
  </div>
</div>
</div>

{{-- VAT PAY Modal --}}


        <div id="payModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">VAT Payment</h4>
                    </div>
                    <div class="modal-body">
                      <div class="row" style="padding-bottom: 20px;">
                     {!! Form::open(array('url' => '','id'=>'payForm', 'role' => 'form', 'class'=>'form-horizontal form-groups','name'=>'submitForm')) !!}
                          {!! Form::hidden('vatId',null,['id'=>'PMvatId']) !!}
                          <div class="col-md-12">
                              <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                  <div class="form-horizontal form-groups">

                                      <div class="form-group">
                                          {!! Form::label('paymentDate', 'Payment Date:', ['class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">
                                              {!! Form::text('paymentDate', null,['id'=>'PMpaymentDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','required']) !!}
                                          </div>
                                      </div>

                                      <div class="form-group">
                                          {!! Form::label('vatAmount', 'VAT Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">
                                              {!! Form::text('vatAmount', null,['id'=>'PMvatAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','required']) !!}
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          {!! Form::label('chalanNo', 'Chalan No:', ['class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">
                                              {!! Form::text('chalanNo', null,['id'=>'PMchalanNo','class'=>'form-control','type' => 'text','autocomplete'=>'off','required']) !!}
                                          </div>
                                      </div>

                                      <div class="form-group">
                                          {!! Form::label('paymentId', 'Payment ID:', ['class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">
                                              {!! Form::text('paymentId', null,['id'=>'PMpaymentId','class'=>'form-control','type' => 'text','autocomplete'=>'off','required']) !!}
                                          </div>
                                      </div>











                                  </div>{{--form-horizontal form-groups--}}
                              </div>{{--End 1st col-md-6--}}

                              <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                  <div class="form-horizontal form-groups">

                                       <div class="form-group">
                                          {!! Form::label('depositBank', 'Deposit Bank:', ['class' => 'col-sm-4 control-label']) !!}
                                              <div class="col-sm-8">
                                                {!! Form::text('depositBank', null,['id'=>'PMdepositBank','class'=>'form-control','type' => 'text','autocomplete'=>'off','required']) !!}
                                              </div>
                                      </div>


                                    <div class="form-group">
                                        {!! Form::label('paymentType', 'Payment Type:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">

                                              <select id="PMpaymentType" name="paymentType" class="form-control" required>
                                                  <option value="">Select Payment Type</option>
                                                  <option value="0">Cash</option>
                                                  <option value="1">Bank</option>
                                                </select>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                       {!! Form::label('accountNo', 'Account No:', ['id'=>'LaccountNo','class' => 'col-sm-4 control-label']) !!}
                                           <div class="col-sm-8">
                                            {!! Form::select('accountNo', $accountNo, null ,['id'=>'PMaccountNo','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus','required']) !!}
                                          </div>
                                   </div>
                                   <div class="form-group">
                                      {!! Form::label('chequeNo', 'Cheque No:', ['id'=>'LcheckNo','class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">
                                            {!! Form::text('chequeNo', null,['id'=>'PMcheckNo','class'=>'form-control','type' => 'text','autocomplete'=>'off','required']) !!}
                                          </div>
                                  </div>



                                  </div>
                              </div>{{--End 2nd col-md-6--}}
                          </div>
                          </div>{{--row--}}





                        <div class="modal-footer">

                            <button id="payButton" class="btn actionBtn glyphicon glyphicon-check btn-success" type="submit"><span> PAY</span></button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span id=""> Close</span></button>

                        </div>
                        {!! Form::close() !!}


                    </div>

                </div>
            </div>
        </div>


        {{-- End VAT Pay Modal --}}












{{-- View Modal --}}

        <div id="viewModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">VAT Details</h4>
                    </div>
                    <div class="modal-body">

                        <div id="printingContent">
                        <div class="row" style="padding-bottom: 20px;">


                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                        <div class="form-group">
                                            {!! Form::label('supplierName', 'Name Of Supplier:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('supplierName', null,['id'=>'VMsupplierName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('billNo', 'Bill No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('billNo', null,['id'=>'VMbillNo','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('billDate', 'Bill Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('billDate', null,['id'=>'VMbillDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('billType', 'Bill Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('billType', null,['id'=>'VMbillType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>



                                        <div class="form-group">
                                            {!! Form::label('billAmount', 'Bill Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('billAmount', null,['id'=>'VMbillAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>



                                    </div>{{--form-horizontal form-groups--}}
                                </div>{{--End 1st col-md-6--}}

                                <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">
                                      <div class="form-group">
                                          {!! Form::label('vatRate', 'VAT Rate:', ['class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">
                                              {!! Form::text('vatRate', null,['id'=>'VMvatRate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                          </div>
                                      </div>

                                       <div class="form-group">
                                          {!! Form::label('vatAmount', 'VAT Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">

                                              {!! Form::text('vatAmount', null,['id'=>'VMvatAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          {!! Form::label('status', 'Status:', ['class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">

                                              {!! Form::text('status', null,['id'=>'VMstatus','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                          </div>
                                      </div>



                                        <div class="form-group">
                                            {!! Form::label('entryByEmpl', 'Entry By:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('entryByEmpl', null,['id'=>'VMentryByEmpl','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('authorizedByEmpl', 'Authorized By:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('authorizedByEmpl', null,['id'=>'VMauthorizedByEmpl','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>



















                                    </div>
                                </div>{{--End 2nd col-md-6--}}
                            </div>
                            </div>{{--row--}}
                            </div>{{-- End Print Div --}}


                        {{-- View ModalFooter--}}
                        <div class="modal-footer">

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
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update VAT List</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                        {!! Form::open(array('url'=>'./accViewVatBillTypeFullUpdate','role' => 'form', 'class'=>'form-horizontal form-groups', 'name'=>'updateForm','id'=>'updateForm')) !!}

                        {!! Form::hidden('billTypeId',null,['id'=>'EMbillTypeId']) !!}
                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                        <div class="form-group">
                                            {!! Form::label('voucherDate', 'Voucher Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('voucherDate', null,['id'=>'EMvoucherDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','required']) !!}
                                                <p id='EMmemberNamee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('voucherNo', 'Voucher No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('EMvoucherNo', null,['id'=>'EMvoucherNo','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                                <p id='EMmemberNamee' style="max-height:3px;"></p>
                                            </div>

                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('billDate', 'Bill Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('billDate', null,['id'=>'EMbillDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','required']) !!}
                                                <p id='EMnidNoe' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('billType', 'BILL Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                <select  id="EMbillType" name="billType" class="form-control" required>>
                                                    <option value="">Select Option</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('vatRate', 'VAT Rate:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('vatRate', null,['id'=>'EMvatRate','class'=>'form-control','type' => 'text','autocomplete'=>'off','required']) !!}
                                                <p id='EMaccNoe' style="max-height:3px;"></p>
                                            </div>
                                        </div>






                                        <div class="form-group">
                                            {!! Form::label('billAmount', 'Bill Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                {!! Form::text('billAmount', null,['id'=>'EMbillAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','required']) !!}

                                            </div>
                                        </div>






                                    </div>{{--form-horizontal form-groups--}}
                                </div>{{--End 1st col-md-6--}}

                                <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">



                                        <div class="form-group">
                                            {!! Form::label('vatAmount', 'VAT Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                            {!! Form::text('vatAmount', null,['id'=>'EMvatAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','required']) !!}
                                                <p id='EMperiode' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('a/chead', 'Account Head:', ['class' => 'col-sm-4 control-label', ]) !!}
                                            <div class="col-sm-8">

                                            {!! Form::text('EMAccountHead', null,['id'=>'EMAccountHead','class'=>'form-control','type' => 'text','autocomplete'=>'off','required','readonly']) !!}
                                                <p id='EMperiode' style="max-height:3px;"></p>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            {!! Form::label('status', 'Status:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('status', null,['id'=>'EMstatus','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('project', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                {!! Form::text('project', null,['id'=>'EMproject','class'=>'form-control','type' => 'text','autocomplete'=>'off', 'readonly']) !!}

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                {!! Form::text('projectType', null,['id'=>'EMprojectType','class'=>'form-control','type' => 'text','autocomplete'=>'off', 'readonly']) !!}

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('billNo', 'Bill No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">

                                                {!! Form::text('billNo', null,['id'=>'EMbillNo','class'=>'form-control','type' => 'text','autocomplete'=>'off', 'readonly']) !!}

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
            {!! Form::open(['url' => '','id'=>'deleteForm', 'name' =>'deleteForm']) !!}

            <button id="DMconfirmButton"  class="btn btn-danger" type="submit"> <span>Confirm</span></button>
            <button  class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
            {!! Form::close() !!}

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
  $('#paginationSelect').change(function(event){
     var paginationSelect= $('#paginationSelect').val();

     $.ajax({
       type: 'get',
       url: "./accViewVatRegister",
       data: {paginationSelect: paginationSelect}
     });

   });

  /*Starts edit modal */
  $(document).on('click', '.edit-modal', function() {

       var billTypeId =$(this).attr('vatBillTypeIdNo');
       var vatBillTypeIdeditModalHidden   =$(this).attr('vatBillTypeIdeditModal');
       $("#EMbillTypeId").val(vatBillTypeIdeditModalHidden);


       var csrf = "{{csrf_token()}}";
       $("#editModal").modal('show');

    $.ajax({
      type: 'post',
      url: "./accViewVatBillTypeGetAjax",
      data: {vatBillTypeIdeditModalHidden: vatBillTypeIdeditModalHidden, _token: csrf},
        success: function (data){


          $("#EMvoucherNo").val(data['voucherNo']);
          $("#EMvoucherDate").val(data['voucherDate']);
          $("#EMbillDate").val(data['billDate']);
          $("#EMAccountHead").val(data['ledger']);
          $("#EMbillAmount").val(data['billAmount']);
          $("#EMvatAmount").val(data['vatAmount']);
          $("#EMvatRate").val(data['vatInterestRate']);
          if(data['status']==0)
            {$("#EMstatus").val('Unpaid');
            }
          else{
            $("#EMstatus").val('Paid');
          }
            $("#EMproject").val(data['projectName']);
            $("#EMprojectType").val(data['projectTypeName']);
            $("#EMbillNo").val(data['billNo']);



        },
        error:  function (data){
              alert("error");
        }
 });
 console.log(billTypeId);
   $.ajax({
     type: 'post',
     url: "./accViewVatBillTypeUpdateAjax",
     data: {billTypeId: billTypeId, _token: csrf},
     success: function (data){
       console.log(data);

       $('#EMbillType').empty();
       //data[0];
        $("#EMbillType").append("<option value='"+data['selectedBillType'].id+"'>"+data['selectedBillType'].billTypeName+"</option>");
       $.each(data['ajaxBiilType'], function( key,obj){
         //alert(JSON.stringify(obj));
           $("#EMbillType").append("<option value='"+obj.id+"'>"+obj.serviceName+"</option>");
       });

     },
     error:  function (data){
           alert("error");
     }
});


    function toDate(dateStr) {
        var parts = dateStr.split("-");
        return new Date(parts[2], parts[1] - 1, parts[0]);
     }

    $("#EMvoucherDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "2017:c",
        minDate: new Date(2017, 07 - 1, 01),
        maxDate: "dateToday",
        dateFormat: 'dd-mm-yy',
        onSelect: function () {

        }
     });
     $("#EMbillDate").datepicker({
         changeMonth: true,
         changeYear: true,
         yearRange : "2017:c",
         minDate: new Date(2017, 07 - 1, 01),
         maxDate: "dateToday",
         dateFormat: 'dd-mm-yy',
         onSelect: function () {

         }
      });
     $("#EMbillType").click(function(event){
        var billType=$('#EMbillType').val();
        var billAmount=$('#EMbillAmount').val();

       $.ajax({
            type: 'post',
            url: "./vatCalculationFromBillType",
            data: {billType:billType},
              success: function (data){
                $.each(data, function( key,obj){
                      $('#EMvatRate').val(obj.vatRate);
                      var vat=obj.vatRate;
                      var total= Math.round(billAmount*(vat/100));
                      $('#EMvatAmount').val(total);

                });
              },
              error:  function (data){

              }
       });
     });

     $("#EMbillAmount").change(function(event){

           var vat= $('#EMvatRate').val();
           var billAmount=$('#EMbillAmount').val();
           var total= Math.round(billAmount*(vat/100));

           $('#EMvatAmount').val(total);

     });

     /*Submit update the form*/

     $("#updateForm").submit(function(event) {

         event.preventDefault();

         $.ajax({
              type: 'post',
              url: './accViewVatBillRegisterFullUpdate',
              data: $('#updateForm').serialize(),
              dataType: 'json',
             success: function( _response ){


                  alert('success');
                   location.reload();



             },
             error: function( data ){
                 // Handle error
                 //alert(_response.errors);
                 alert('error');

             }
         });
     });




         /*End Submit update the form*/

  });

/*ends edit modal*/


  $(document).on('click', '.pay-modal', function() {
    $('#PMaccountNo').hide();
     $('#LaccountNo').hide();
     $('#PMcheckNo').hide();
     $('#LcheckNo').hide();

     function toDate(dateStr) {
         var parts = dateStr.split("-");
         return new Date(parts[2], parts[1] - 1, parts[0]);
      }

     $("#PMpaymentDate").datepicker({
         changeMonth: true,
         changeYear: true,
         yearRange : "2017:c",
         minDate: new Date(2017, 07 - 1, 01),
         maxDate: "dateToday",
         dateFormat: 'dd-mm-yy',
         onSelect: function () {

         }
      });

    var vatBillTypeIdPayModal = $(this).attr('vatBillTypeIdPayModal');
    var vatAmount= $(this).attr('vatAmount');
    var csrf = "{{csrf_token()}}";
    $.ajax({
      url: './accAddVatregisterPayVatBillNoGenerate',
      type: 'POST',
      dataType: 'json',
      data: {vatBillTypeIdPayModal: vatBillTypeIdPayModal, _token: csrf},
      success: function(data) {
          $("#PMpaymentId").val("VAT"+data);


      },
      error: function(argument) {
        alert('response error');
      }
    });
    $("#PMvatAmount").val(vatAmount);
    $("#PMvatId").val(vatBillTypeIdPayModal);



   $("#payModal").modal('show');



  $("#PMpaymentType").click(function(event){
    var paymentType=$('#PMpaymentType').val();
    if(paymentType == 1)
    {
      $("#PMaccountNo").show();
      $("#LaccountNo").show();
      $('#PMcheckNo').show();
      $('#LcheckNo').show();

    }
    else{
      $("#PMaccountNo").hide();
      $("#LaccountNo").hide();
      $('#PMcheckNo').hide();
      $('#LcheckNo').hide();
    }


  });

    /*Submit the form*/

        $("#payForm").submit(function(event) {

            event.preventDefault();

            $.ajax({
                 type: 'post',
                 url: './accAddVatregisterPayVat',
                 data: $('#payForm').serialize(),
                 dataType: 'json',
                success: function( _response ){


                     alert('success');
                     location.reload();



                },
                error: function( data ){
                    // Handle error
                    //alert(_response.errors);
                    alert('error');

                }
            });
        });
        /*End Submit the form*/




  });
  /*End Payment Modal*/



  /*Starts view modal */
  $(document).on('click', '.view-modal', function() {
    var viewModalId   =$(this).attr('vatBillTypeIdViewModal');
      var csrf = "{{csrf_token()}}";
    $.ajax({
      url: './accViewVatBillRegisterViewModal',
      type: 'POST',
      dataType: 'json',
      data: {viewModalId : viewModalId , _token: csrf},
      success: function(data) {

        $("#VMsupplierName").val(data["name"]);
        $("#VMbillNo").val(data["billNo"]);
        $("#VMbillDate").val(data["billDate"]);
        $("#VMbillAmount").val(data["billAmount"]);
        $("#VMvatRate").val(data["vatInterestRate"]);
        $("#VMvatAmount").val(data["vatAmount"]);
        $("#VMentryByEmpl").val(data["emp_name_english"]);
        if(data["status"]==0)
        {
          $("#VMstatus").val("Unpaid");
        }
        else{
          $("#VMstatus").val("Paid");
        }
       $("#viewModal").find('.modal-dialog').css("width","70%");
       $("#viewModal").modal('show');
       $("#VMbillType").val(data["serviceName"]);

      },
      error: function(argument) {
        alert('Employee Not Found');
      }
    });



});
/*Starts delete modal */
$(document).on('click', '.delete-modal', function() {
   $("#deleteModal").modal('show');
    var DeleteModalId=$(this).attr('vatBillTypeIdDeleteModal');
    var csrf = "{{csrf_token()}}";


   /*Submit delete the form*/

   $("#deleteForm").submit(function(event) {

       event.preventDefault();

       $.ajax({
            type: 'post',
            url: './accViewVatBillRegisterDeleteModal',
            data: {DeleteModalId : DeleteModalId , _token: csrf},
            dataType: 'json',
           success: function( _response ){


                alert('success');
                 location.reload();



           },
           error: function( data ){
               // Handle error
               //alert(_response.errors);
               alert('error');

           }
       });
   });




       /*End Submit delete the form*/



});
/*ends delete modal */



});
</script>

<style type="text/css">

    #printingTable tbody tr td:nth-child(1){
        text-align: left;
        padding-left: 5px;
    }
</style>

@include('dataTableScript')
<script>
$( document ).ready(function() {

	    $(".table thead tr th").css('border','1px solid white');
	});


</script>


@endsection
