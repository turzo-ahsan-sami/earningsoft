@extends('layouts/acc_layout')
@section('title', '| Loan Register')
@section('content')



<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addLoanRegisterAccount/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Loan Register</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">LOAN REGISTER LIST</font></h1>
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
                        <th>Date</th>
                        <th>Bank/Donor</th>
                        <th>Product</th>                        
                        <th>Project Type</th>                        
                        <th>Account Number</th>
                        <th>Phase</th>
                        <th>Cycle</th>
                        <th>Amount (Tk)</th>
                        <th>Interest Rate (%)</th>
                        <th>Num. Of Inst.</th>
                        <th>Installment Start Date</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                      
                    </thead>
                    <tbody>
                     @foreach($loanAccounts as $index => $loanAccount)
                     @php
                        $bankName = DB::table('gnr_bank')->where('id',$loanAccount->bankId_fk)->value('name');
                        $productName = DB::table('gnr_loan_product')->where('id',$loanAccount->loanProductId_fk)->value('name');
                        $projectTypeName = DB::table('gnr_project_type')->where('id',$loanAccount->projectTypeId_fk)->value('name');
                        $installmentStartDate = Carbon\Carbon::parse($loanAccount->loanDate)->addMonthsNoOverflow($loanAccount->grasePeriod);
                        $isAnyPayment = (int) DB::table('acc_loan_register_payments')->where('accId_fk',$loanAccount->id)->value('id');
                     @endphp
                        <tr>
                            <td>{{$index+1}}</td>
                            <td>{{$loanAccount->loanDate}}</td>
                            <td class="name">{{$bankName}}</td>
                            <td class="name">{{$productName}}</td>
                            <td class="name">{{$projectTypeName}}</td>
                            <td>{{$loanAccount->accNo}}</td>
                            <td>{{$loanAccount->phase}}</td>                            
                            <td>{{$loanAccount->cycle}}</td>
                            
                            <td class="amount">{{number_format($loanAccount->loanAmount,2,'.',',')}}</td>
                            <td>{{number_format($loanAccount->interestRate,2)}}</td>

                            <td>{{$loanAccount->numOfInstallment}}</td>  
                            <td>{{date('d-m-Y',strtotime($installmentStartDate))}}</td>  
                            <td>
                             @if($loanAccount->status==0)
                              <span><i class="fa fa-times" aria-hidden="true" style="color:red;font-size: 1.3em;"></i></span>
                            @else                            
                                <span><i class="fa fa-check" aria-hidden="true" style="color:green;font-size: 1.3em;"></i></span>
                            @endif 
                            </td> 




                            <td width="80">

                            <a href="javascript:;" class="view-modal" accountId="{{$loanAccount->id}}">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                              </a>&nbsp; 

                              @php
                              if ($isAnyPayment>0) {
                                $editClassName = 'edit-modal disabled';
                                $deleteClassName = 'delete-modal disabled';
                              }
                                  else{
                                     $editClassName = 'edit-modal';
                                     $deleteClassName = 'delete-modal';
                                  }
                              @endphp


                              <a href="javascript:;" class="{{$editClassName}}" accountId="{{$loanAccount->id}}">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>&nbsp;

                            <a href="javascript:;" class="{{$deleteClassName}}" accountId="{{$loanAccount->id}}">
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
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Account Info</h4>
            </div>
            <div class="modal-body">

                <div class="row" style="padding-bottom: 20px;">

                <div class="form-horizontal form-groups">
                    <div class="col-md-12">
                
                    <div class="col-md-6">
                    
                    

                    <div class="form-group">
                        {!! Form::label('donor', 'Donor:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                                                         
                            {!! Form::text('donor',null, ['class'=>'form-control', 'id' => 'VMdonor','readonly']) !!}
                            
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('loanProduct', 'Loan Product:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                                                         
                            {!! Form::text('loanProduct',null, ['class'=>'form-control', 'id' => 'VMloanProduct','readonly']) !!}
                            
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8"> 

                        {!! Form::text('branch',null, ['class'=>'form-control', 'id' => 'VMbranch','readonly']) !!}                       
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('project', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">

                        {!! Form::text('project',null, ['class'=>'form-control', 'id' => 'VMproject','readonly']) !!}
                        
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        {!! Form::text('projectType',null, ['class'=>'form-control', 'id' => 'VMprojectType','readonly']) !!}
                       
                        </div>
                    </div>
                
                    
                    
                     <div class="form-group">
                            {!! Form::label('accNo', 'Account No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('accNo', null, ['class'=>'form-control', 'id' => 'VMaccNo','readonly']) !!}
                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('agreementDate', 'Agreement/Sanction Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('agreementDate', null, ['class'=>'form-control', 'id' => 'VMagreementDate','readonly']) !!}
                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('loanSanctionNumber', 'Loan Sanction Number:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('loanSanctionNumber', null, ['class'=>'form-control', 'id' => 'VMloanSanctionNumber','readonly']) !!}
                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('loanDate', 'Loan Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('loanDate', null, ['class'=>'form-control', 'id' => 'VMloanDate','readonly']) !!}
                                
                            </div>
                    </div>
                    

                   
                </div> {{-- End of 1st coloum --}}

                <div class="col-md-6">


               <div class="form-group">
                            {!! Form::label('interestRate', 'Interest Rate:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('interestRate', null, ['class'=>'form-control', 'id' => 'VMinterestRate','readonly']) !!}
                                
                            </div>
                    </div>




                    <div class="form-group">
                            {!! Form::label('loanAmount', 'Loan Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('loanAmount', null, ['class'=>'form-control', 'id' => 'VMloanAmount','readonly']) !!}
                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('cycle', 'Cycle:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('cycle', null, ['class'=>'form-control', 'id' => 'VMcycle','readonly']) !!}
                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('phase', 'Phase:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('phase', null, ['class'=>'form-control', 'id' => 'VMphase','readonly']) !!}
                                
                            </div>
                    </div>

                   

                    <div class="form-group">
                            {!! Form::label('repaymentFrequency', 'Repayment Frequency:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8"> 
                              {!! Form::text('repaymentFrequency', null, ['class'=>'form-control', 'id' => 'VMrepaymentFrequency','readonly']) !!}
                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('loanDuration', 'Loan Duration:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('loanDuration', null, ['class'=>'form-control', 'id' => 'VMloanDuration','readonly']) !!}
                                                             
                            </div>
                    </div> 

                    <div class="form-group">
                            {!! Form::label('gracePeriod', 'Grace Period:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('gracePeriod', null, ['class'=>'form-control', 'id' => 'VMgracePeriod','readonly']) !!}
                                                             
                            </div>
                    </div>  

                    <div class="form-group">
                            {!! Form::label('numOfInstallment', 'Number Of Installment:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('numOfInstallment', null, ['class'=>'form-control', 'id' => 'VMnumOfInstallment','readonly']) !!}
                                                              
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('status', 'Status:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                           
                              {!! Form::text('status', null, ['class'=>'form-control', 'id' => 'VMstatus','readonly']) !!}
                                                             
                            </div>
                    </div>

                    <div id="VMrebateAmountDiv" class="form-group">
                            {!! Form::label('rebateAmount', 'Rebate Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                           
                              {!! Form::text('rebateAmount', null, ['class'=>'form-control', 'id' => 'VMrebateAmount','readonly']) !!}
                                                             
                            </div>
                    </div>


                </div> {{-- End of 2nd coloum --}}

               </div> {{-- End col-12 --}}


                
            </div>
            

                </div>{{--row--}}

                 <table id="VMinstallmentTable" class="table table-striped table-bordered" style="color: black !important;">
                   
                    

                                <thead>
                               <tr>
                                   <th rowspan="2">Inst. No</th>
                                   <th colspan="4">Schedule</th>
                                   <th colspan="4">Payment</th>
                                   <th colspan="3">Due Amount</th>
                                   <th rowspan="2">Status</th>
                               </tr>
                               <tr>
                                   <th>Inst. Date</th>
                                   <th>Principal</th>
                                   <th>Interest</th>
                                   <th>Total</th>

                                   <th>Date</th>
                                   <th>Principal</th>
                                   <th>Interest</th>
                                   <th>Total</th>

                                   <th>Principal</th>
                                   <th>Interest</th>
                                   <th>Total</th>


                                  
                               </tr>
                           </thead>
                           <tbody>
                               
                           </tbody>

                           <tfoot>
                               
                           </tfoot>
               </table>

                {{-- View ModalFooter--}}
                <div class="modal-footer">
               
                    <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span> Close</span></button>
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
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update Account</h4>
            </div>
            <div class="modal-body">
            {!! Form::open(['url'=>'','class'=>'form-horizontal form-groups']) !!}

                <div class="row" style="padding-bottom: 20px;">

                
                    <div class="col-md-12">
                
                    <div class="col-md-6">
                    
                    {!! Form::hidden('accId',null,['id'=>'EMaccId']) !!}

                    <div class="form-group">
                        {!! Form::label('donor', 'Donor:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                          $donorList = array(''=>'Select Donor') + DB::table('gnr_bank')->pluck('name','id')->toArray();
                        @endphp                                  
                            {!! Form::select('donor',$donorList ,null, ['class'=>'form-control', 'id' => 'EMdonor']) !!}
                            <p id='donore' class="error"></p>
                        </div>
                    </div>

                    {!! Form::hidden('donorType',null,['id'=>'EMdonorType']) !!}

                    <div class="form-group">
                        {!! Form::label('loanProduct', 'Loan Product:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                          $loanProductList = array(''=>'Select Loan Product') + DB::table('gnr_loan_product')->pluck('name','id')->toArray();
                        @endphp                                  
                            {!! Form::select('loanProduct',$loanProductList ,null, ['class'=>'form-control', 'id' => 'EMloanProduct']) !!}
                            <p id='loanProducte' class="error"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                          /*$donorBranchList = array(''=>'Select Branch') + DB::table('gnr_bank_branch')->pluck('name','id')->toArray();*/
                          $donorBranchList = DB::table('gnr_bank_branch')->select('name','id','bankId_fk')->get();
                          $bankShortNameList = array();
                          foreach ($donorBranchList as $key => $donorBranch) {
                            $bankShortName = DB::table('gnr_bank')->where('id',$donorBranch->bankId_fk)->value('shortName');
                              array_push($bankShortNameList, $bankShortName);
                          }
                        @endphp

                        <select id="EMbranch" name="branch" class="form-control">
                            <option value="">Select Branch</option>
                            @foreach($donorBranchList as $index => $branch)
                            <option value="{{$branch->id}}">{{$branch->name.'-'.$bankShortNameList[$index]}}</option>
                            @endforeach
                        </select>
                           
                            <p id='branche' class="error"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('project', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        @php
                          $projects = DB::table('gnr_project')->select('id','name','projectCode')->get();
                        @endphp
                         <select name="project" class="form-control input-sm" id="EMproject">
                            <option value="">Select Project</option>                                         
                            @foreach($projects as $project)
                            <option value="{{$project->id}}">{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT).'-'.$project->name}}</option>
                            @endforeach
                        </select>                               
                            <p id='projecte' class="error"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        @php
                          $projectTypes = DB::table('gnr_project_type')->select('id','name','projectTypeCode')->get();
                        @endphp
                         <select name="projectType" class="form-control input-sm" id="EMprojectType">
                            <option value="">Select Project Type</option>                                         
                            @foreach($projectTypes as $projectType)
                            <option value="{{$projectType->id}}">{{str_pad($projectType->projectTypeCode,3,"0",STR_PAD_LEFT).'-'.$projectType->name}}</option>
                            @endforeach
                        </select>                               
                            <p id='projectTypee' class="error"></p>
                        </div>
                    </div>
                
                    
                    
                     <div class="form-group">
                            {!! Form::label('accNo', 'Account No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('accNo', null, ['class'=>'form-control', 'id' => 'EMaccNo']) !!}
                                <p id='accNoe' class="error"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('agreementDate', 'Agreement/Sanction Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('agreementDate', null, ['class'=>'form-control', 'id' => 'EMagreementDate','readonly','style'=>'cursor:pointer']) !!}
                                <p id='agreementDatee' class="error"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('loanSanctionNumber', 'Loan Sanction Number:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('loanSanctionNumber', null, ['class'=>'form-control', 'id' => 'EMloanSanctionNumber']) !!}
                                <p id='loanSanctionNumbere' class="error"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('loanDate', 'Loan Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                 
                            {!! Form::text('loanDate', null, ['class'=>'form-control', 'id' => 'EMloanDate','readonly','style'=>'cursor:pointer']) !!}
                                <p id='loanDatee' class="error"></p>
                            </div>
                    </div>
                    

                   
                </div> {{-- End of 1st coloum --}}

                <div class="col-md-6">


               <div class="form-group">
                            {!! Form::label('interestRate', 'Interest Rate:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('interestRate', null, ['class'=>'form-control', 'id' => 'EMinterestRate']) !!}
                                <p id='interestRatee' class="error"></p>
                            </div>
                    </div>




                    <div class="form-group">
                            {!! Form::label('loanAmount', 'Loan Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('loanAmount', null, ['class'=>'form-control', 'id' => 'EMloanAmount']) !!}
                                <p id='loanAmounte' class="error"></p>
                            </div>
                    </div>

                    

                    <div class="form-group">
                            {!! Form::label('phase', 'Phase:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('phase', null, ['class'=>'form-control', 'id' => 'EMphase']) !!}
                                <p id='phasee' class="error"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('cycle', 'Cycle:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                
                           {!! Form::text('cycle', null, ['class'=>'form-control', 'id' => 'EMcycle']) !!}
                                <p id='cyclee' class="error"></p>
                            </div>
                    </div>

                    

                   

                    <div class="form-group">
                            {!! Form::label('repaymentFrequency', 'Repayment Frequency:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8"> 
                            @php
                               $monthArray = array(''=>'Select Duration','1'=>'1 month','2'=>'2 months','3'=>'3 months','4'=>'4 months','5'=>'5 months','6'=>'6 months','7'=>'7 months','8'=>'8 months','9'=>'9 months','10'=>'10 months','11'=>'11 months','12'=>'12 months');
                           @endphp    

                                {!! Form::select('repaymentFrequency', $monthArray,null, ['class'=>'form-control', 'id' => 'EMrepaymentFrequency']) !!}
                                <p id='repaymentFrequencye' class="error"></p>
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('loanDuration', 'Loan Duration (Month):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('loanDuration', null, ['class'=>'form-control', 'id' => 'EMloanDuration']) !!}
                                <p id='loanDuratione' class="error"></p>                                
                            </div>
                    </div> 

                    <div class="form-group">
                            {!! Form::label('gracePeriod', 'Grace Period (Month):', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('gracePeriod', null, ['class'=>'form-control', 'id' => 'EMgracePeriod']) !!}
                                <p id='gracePeriode' class="error"></p>                                
                            </div>
                    </div>  

                    <div class="form-group">
                            {!! Form::label('numOfInstallment', 'Number Of Installment:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">                                    
                                {!! Form::text('numOfInstallment', null, ['class'=>'form-control', 'id' => 'EMnumOfInstallment']) !!}
                                <p id='numOfInstallmente' class="error"></p>                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('status', 'Status:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            @php
                                $statusArray = array(''=>'Select Status','1'=>'Activate','0'=>'Deactivate');
                             @endphp                                 
                                {!! Form::select('status', $statusArray,1, ['class'=>'form-control', 'id' => 'EMstatus']) !!}
                                <p id='statuse' class="error"></p>                                
                            </div>
                    </div>                

                </div> {{-- End of 2nd coloum --}}

               <p id="inappropriatee" class="error" style="font-size: 18px;"></p>

            
                
            </div>
            

                </div>{{--row--}}



                 <div class="form-group">
                        <div class="col-sm-12 text-right" style="padding-right: 30px;">
                            {!! Form::button('Create Schedule', ['id' => 'createSchedule', 'class' => 'btn btn-info','type'=>'button']) !!}
                        </div>
                </div> 

                <br>  
                <br>  

                <div style="padding-right: 15px;padding-left: 15px;">

                <table id="EMinstallmentTable" class="table table-striped table-bordered" style="color: black !important;">
                   
                    <thead>
                               <tr>
                                   <th rowspan="2">Installment No</th>
                                   <th colspan="4">Schedule</th>
                                   <th colspan="3">Payment</th>
                                   <th colspan="3">Due Amount</th>
                                   <th rowspan="2">Repay Status</th>
                               </tr>
                               <tr>
                                   <th>Installment Date</th>
                                   <th>Principal</th>
                                   <th>Interest</th>
                                   <th>Total</th>

                                   <th>Principal</th>
                                   <th>Interest</th>
                                   <th>Total</th>

                                   <th>Principal</th>
                                   <th>Interest</th>
                                   <th>Total</th>


                                  
                               </tr>
                           </thead>
                   <tbody>
                       
                   </tbody>
               </table>
               </div>



                {{-- Edit ModalFooter--}}
                <div class="modal-footer" style="padding-right: 15px;">
                <button id="updateButton" class="btn actionBtn glyphicon glyphicon-check btn-success edit" paymentId="0" type="button"> Update</button>
                    <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span> Close</span></button>
                </div>

                {!! Form::close() !!}
            </div> {{-- End Edit Modal Body--}}

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





<script type="text/javascript">
  $(document).ready(function() {

    function num(argument) {
        return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

   function formateDate(argument){
            if (argument==null) {
                return "-";
            }
            else{
                var date = $.datepicker.parseDate('yy-mm-dd',argument);
            return $.datepicker.formatDate("dd-mm-yy", date);
            }
            
        }

    function pad (str, max) {
          str = str.toString();
          return str.length < max ? pad("0" + str, max) : str;
        }

    function GetFormattedDate(CurrentDate) {
        var date = new Date(CurrentDate);
        return( pad(date.getDate(),2) + '-'+ pad((date.getMonth() + 1),2) +'-' +  date.getFullYear());
    }



    /*View Modal*/    
    
    $(document).on('click', '.view-modal', function() {
      if(hasAccess('getLoanRegisterInfo')){

      var accId = $(this).attr('accountId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './getLoanRegisterInfo',
        type: 'POST',
        dataType: 'json',
        data: {accId: accId, _token: csrf},
        success: function(data) {

          if (data.accessDenied) {
              showAccessDeniedMessage();
              return false;
          }
          
            $("#VMdonor").val(data['donorName']);            
            $("#VMloanProduct").val(data['productName']);
            $("#VMbranch").val(data['branchName']);
            $("#VMproject").val(data['projectName']);
            $("#VMprojectType").val(data['projectTypeName']);
            $("#VMaccNo").val(data['account'].accNo);


            var agreementDate =  $.datepicker.formatDate("dd-mm-yy", $.datepicker.parseDate('yy-mm-dd',data['account'].agreementDate));
            $("#VMagreementDate").val(agreementDate);

            $("#VMloanSanctionNumber").val(data['account'].loanSanctionNumber);

            var loanDate =  $.datepicker.formatDate("dd-mm-yy", $.datepicker.parseDate('yy-mm-dd',data['account'].loanDate));
            $("#VMloanDate").val(loanDate);


            $("#VMinterestRate").val(num(data['account'].interestRate));
            $("#VMloanAmount").val(num(data['account'].loanAmount));

           
            $("#VMphase").val(data['account'].phase);
            $("#VMcycle").val(data['account'].cycle);
            
           

            if (data['account'].repaymentFrequency>1) {
                $("#VMrepaymentFrequency").val(data['account'].repaymentFrequency+' months');
            }
            else{
                $("#VMrepaymentFrequency").val(data['account'].repaymentFrequency+' month');
            }

            if (data['account'].loanDuration>1) {
                $("#VMloanDuration").val(data['account'].loanDuration+' months');
            }
            else{
                $("#VMloanDuration").val(data['account'].loanDuration+' month');
            }

            if (data['account'].grasePeriod>1) {
                $("#VMgracePeriod").val(data['account'].grasePeriod+' months');
            }
            else{
                $("#VMgracePeriod").val(data['account'].grasePeriod+' month');
            }
            
            
            
            $("#VMnumOfInstallment").val(data['account'].numOfInstallment);

            if (data['account'].status==1) {
                $("#VMstatus").val('Activated');
            }
            else{
                $("#VMstatus").val('Deactivated');
            }



            /*Make the Schedule Table*/
            if (data['donorType']==1) {
              key = "cycle";
            }
            else{
              key = "accNo";
            }
             
             var loanProductId = data['account'].loanProductId_fk;
             var loanAccId = data['account'].id;
             var phase = data['account'].phase;
             var cycle = data['account'].cycle;
             var csrf = "{{csrf_token()}}";



              $.ajax({
             url: './getLoanAccountNpaymentInfo',
             type: 'POST',
             dataType: 'json',
             data: {key: key,loanProductId: loanProductId,loanAccId: loanAccId,phase: phase,cycle: cycle, _token: csrf},
         })
         .done(function(data) {


           $("#VMrebateAmount").val(data['rebateAmount']);
            

            if (data['isRebate']==1) {
              $("#VMrebateAmountDiv").show();
            }
            else{
              $("#VMrebateAmountDiv").hide();
            }



             /*Make Schedule Table*/
            $("#VMinstallmentTable tbody").empty();
            $("#VMinstallmentTable tfoot").empty();

            var tTotalSchedulePricipal = 0;
            var tTotalScheduleInterest = 0;
            var tTotalSchedule = 0;
            var tTotalPaymentPricipal = 0;
            var tTotalPaymentInterest = 0;
            var tTotalPayment = 0;
            var tTotalDuePrincipal = 0;
            var tTotalDueInterest = 0;
            var tTotalDue = 0;


            $.each(data['schedules'], function(index, schedule) {

                 markup = "<tr><td>"+schedule.scheduleNumber+"</td><td>"+formateDate(schedule.paymentDate)+"</td><td>"+num(schedule.principalAmount)+"</td><td>"+num(schedule.interestAmount)+"</td><td>"+num(schedule.totalAmount)+"</td><td>"+formateDate(data['paymentDate'][index])+"</td><td>"+num(data['principalPaymentAmount'][index])+"</td><td>"+num(data['interestPaymentAmount'][index])+"</td><td>"+num(data['totalPaidAmount'][index])+"</td><td>"+num(data['principalDueAmount'][index])+"</td><td>"+num(data['interestDueAmount'][index])+"</td><td>"+num(data['totalDueAmount'][index])+"</td><td>"+schedule.paymentStatus+"</td></tr>";
                 $("#VMinstallmentTable tbody").append(markup);

                tTotalSchedulePricipal = tTotalSchedulePricipal + schedule.principalAmount;
                tTotalScheduleInterest = tTotalScheduleInterest + schedule.interestAmount;
                tTotalSchedule = tTotalSchedule + schedule.totalAmount;
                tTotalPaymentPricipal = tTotalPaymentPricipal + data['principalPaymentAmount'][index];
                tTotalPaymentInterest = tTotalPaymentInterest + data['interestPaymentAmount'][index];
                tTotalPayment = tTotalPayment + data['totalPaidAmount'][index];
                tTotalDuePrincipal = tTotalDuePrincipal + data['principalDueAmount'][index];
                tTotalDueInterest = tTotalDueInterest + data['interestDueAmount'][index];
                tTotalDue = tTotalDue + data['totalDueAmount'][index];
            });

            totalRowMarkup = "<tr><td colspan='2'>Total</td><td>"+num(tTotalSchedulePricipal)+"</td><td>"+num(tTotalScheduleInterest)+"</td><td>"+num(tTotalSchedule)+"</td><td></td><td>"+num(tTotalPaymentPricipal)+"</td><td>"+num(tTotalPaymentInterest)+"</td><td>"+num(tTotalPayment)+"</td><td>"+num(tTotalDuePrincipal)+"</td><td>"+num(tTotalDueInterest)+"</td><td>"+num(tTotalDue)+"</td><td></td></tr>";

            $("#VMinstallmentTable tfoot").append(totalRowMarkup);
            /*End Make Schedule Table*/
          

            
             
           /* $("#VMinstallmentTable tbody").empty();
            $.each(data['schedules'], function(index, schedule) {
              


                 markup = "<tr><td>"+schedule.scheduleNumber+"</td><td>"+formateDate(schedule.paymentDate)+"</td><td>"+num(schedule.principalAmount)+"</td><td>"+num(schedule.interestAmount)+"</td><td>"+num(schedule.totalAmount)+"</td><td>"+num(data['principalPaymentAmount'][index])+"</td><td>"+num(data['interestPaymentAmount'][index])+"</td><td>"+num(data['totalPaidAmount'][index])+"</td><td>"+num(data['principalDueAmount'][index])+"</td><td>"+num(data['interestDueAmount'][index])+"</td><td>"+num(data['totalDueAmount'][index])+"</td><td>"+schedule.paymentStatus+"</td></tr>";
                 $("#VMinstallmentTable tbody").append(markup);
            });*/
          })

            

            /*End Make the Schedule Table*/

       

        $("#viewModal").find('.modal-dialog').css('width', '80%');
         $("#viewModal").modal('show');

        },
        error: function(argument) {
          alert('response error');
        }
      });

      }
    });
    /*End View Modal*/


 /*Edit Modal*/    
    
    $(document).on('click', '.edit-modal', function() {
      if(hasAccess('getLoanRegisterInfoToUpdate')){

      var accId = $(this).attr('accountId');
      var csrf = "{{csrf_token()}}";

      $("#EMaccId").val(accId);

      $("#updateButton").prop('disabled', false);

      /*Remove all errors*/
      $(".error").empty();
      /*End Remove all errors*/
      
      $.ajax({
        url: './getLoanRegisterInfoToUpdate',
        type: 'POST',
        dataType: 'json',
        async: false,
        data: {accId: accId, _token: csrf},
        success: function(data) {
          
        $("#EMdonor").val(data['account'].bankId_fk).trigger('change');
        $("#EMdonorType").val(data['donorType']);
        $("#EMloanProduct").val(data['account'].loanProductId_fk);
        $("#EMbranch").val(data['account'].bankBranchId_fk);
        $("#EMproject").val(data['account'].projectId_fk).trigger('change');
        $("#EMprojectType").val(data['account'].projectTypeId_fk);
        $("#EMaccNo").val(data['account'].accNo);


        var agreementDate =  $.datepicker.formatDate("dd-mm-yy", $.datepicker.parseDate('yy-mm-dd',data['account'].agreementDate));
        $("#EMagreementDate").val(agreementDate);

        $("#EMloanSanctionNumber").val(data['account'].loanSanctionNumber);

        var loanDate =  $.datepicker.formatDate("dd-mm-yy", $.datepicker.parseDate('yy-mm-dd',data['account'].loanDate));
        $("#EMloanDate").val(loanDate);


        $("#EMinterestRate").val(data['account'].interestRate);
        $("#EMloanAmount").val(data['account'].loanAmount);
        $("#EMphase").val(data['account'].phase);
        $("#EMcycle").val(data['account'].cycle);
        $("#EMrepaymentFrequency").val(data['account'].repaymentFrequency);        
        $("#EMloanDuration").val(data['account'].loanDuration);
        $("#EMgracePeriod").val(data['account'].grasePeriod);
        $("#EMnumOfInstallment").val(data['account'].numOfInstallment);
        $("#EMstatus").val(data['account'].status);

        

            /*Make the Schedule Table*/
            if (data['donorType']==1) {
              key = "cycle";
            }
            else{
              key = "accNo";
            }
             
             var loanProductId = data['account'].loanProductId_fk;
             var loanAccId = data['account'].id;
             var phase = data['account'].phase;
             var cycle = data['account'].cycle;
             var csrf = "{{csrf_token()}}";

              $.ajax({
             url: './getLoanAccountNpaymentInfo',
             type: 'POST',
             dataType: 'json',
             async: false,
             data: {key: key,loanProductId: loanProductId,loanAccId: loanAccId,phase: phase,cycle: cycle, _token: csrf},
         })
         .done(function(data) {
          
             
            $("#EMinstallmentTable tbody").empty();
            $.each(data['schedules'], function(index, schedule) {

              if ($("#EMdonorType").val()==0) {
                markup = "<tr><td>"+schedule.scheduleNumber+"</td><td><input name='tPaymentDate[]' class='tInitialPaymentDate' value='"+formateDate(schedule.paymentDate)+"' style='cursor:pointer;text-align:center' readonly></td><td><input name='tPrincipalAmount[]' value='"+schedule.principalAmount+"' style='display:none;'>"+num(schedule.principalAmount)+"</td><td><input name=tInterestAmount[] value='"+schedule.interestAmount+"' style='display:none;'>"+num(schedule.interestAmount)+"</td><td>"+num(schedule.totalAmount)+"</td><td>"+num(data['principalPaymentAmount'][index])+"</td><td>"+num(data['interestPaymentAmount'][index])+"</td><td>"+num(data['totalPaidAmount'][index])+"</td><td>"+num(data['principalDueAmount'][index])+"</td><td>"+num(data['interestDueAmount'][index])+"</td><td>"+num(data['totalDueAmount'][index])+"</td><td>"+schedule.paymentStatus+"</td></tr>";
              }
              else{
                 markup = "<tr><td>"+schedule.scheduleNumber+"</td><td><input name='tPaymentDate[]' class='tInitialPaymentDate' value='"+formateDate(schedule.paymentDate)+"' style='display:none;' readonly>"+formateDate(schedule.paymentDate)+"</td><td><input name='tPrincipalAmount[]' value='"+schedule.principalAmount+"' style='display:none;'>"+num(schedule.principalAmount)+"</td><td><input name=tInterestAmount[] value='"+schedule.interestAmount+"' style='display:none;'>"+num(schedule.interestAmount)+"</td><td>"+num(schedule.totalAmount)+"</td><td>"+num(data['principalPaymentAmount'][index])+"</td><td>"+num(data['interestPaymentAmount'][index])+"</td><td>"+num(data['totalPaidAmount'][index])+"</td><td>"+num(data['principalDueAmount'][index])+"</td><td>"+num(data['interestDueAmount'][index])+"</td><td>"+num(data['totalDueAmount'][index])+"</td><td>"+schedule.paymentStatus+"</td></tr>";
              }
            

                 
                 $("#EMinstallmentTable tbody").append(markup);
            });
          })
          
            /*End Make the Schedule Table*/

           /* if ($("#EMdonorType").val()==0) {
              $(".tInitialPaymentDate").datepicker({
                        changeMonth: true,
                        changeYear: true,
                        yearRange : "2000:c",
                        maxDate: "dateToday",
                        dateFormat: 'dd-mm-yy',
                        onSelect: function() {
                            
                        }
                    });
            }*/

            
        
                

         $("#editModal").find('.modal-dialog').css('width', '80%');
         $("#editModal").modal('show');

        },
        error: function(argument) {
          alert('response error');
        }
      });
}
});
    /*End Edit Modal*/



    $("#EMloanAmount,#EMinterestRate").on('input', function() {            
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        $("#EMcycle,#EMphase,#EMloanDuration,#EMgracePeriod,#EMnumOfInstallment").on('input', function() {            
            this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
        });




     $("#EMagreementDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "2000:c",
        maxDate: "dateToday",
        dateFormat: 'dd-mm-yy'
    });
     $("#EMloanDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "2000:c",
        maxDate: "dateToday",
        dateFormat: 'dd-mm-yy',
        onSelect: function(){
          $("#updateButton").prop('disabled', true);
        }
    });


     /*Filter Branch Location and loan product On selecting Bank*/
         $("#EMdonor").on('change', function() {
             var donor = $("#EMdonor option:selected").val();
             var csrf = "{{csrf_token()}}";

             $.ajax({
                 url: './accFdrGetBranchLocationBaseOnBank',
                 type: 'POST',
                 dataType: 'json',
                 async: false,
                 data: {bank: donor, _token: csrf},
             })
             .done(function(branch) {
                
                $("#EMbranch").empty();
                $("#EMbranch").append("<option value=''>Select Branch</option>");
                $.each(branch, function(index, branch) {
                     $("#EMbranch").append("<option value='"+branch.id+"'>"+branch.name+'-'+branch.bankName+"</option>");
                });
               

                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });



             $.ajax({
                 url: './getLoanProductBaseOnDonor',
                 type: 'POST',
                 dataType: 'json',
                 async:false,
                 data: {donor: donor, _token: csrf},
             })
             .done(function(loanProduct) {
                $("#EMloanProduct").empty();
                $("#EMloanProduct").append("<option value=''>Select Loan Product</option>");

                $.each(loanProduct, function(index, product) {
                     $("#EMloanProduct").append("<option value='"+product.id+"'>"+product.name+"</option>");
                });

                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });


             /*Get Donar Type*/
             $.ajax({
                 url: './gnrGetBankInfo',
                 type: 'POST',
                 dataType: 'json',
                 async: false,
                 data: {bankId: donor, _token: csrf},
             })
             .done(function(bank) {
                $("#EMdonorType").val(bank.isDonor);
                if (bank.isDonor==1) {
                    $("#EMaccNo").val('');
                    $("#EMphase").val('');
                    $("#EMcycle").val('');
                    $("#EMaccNo").prop('readonly',true);
                    $("#EMphase").prop('readonly',false);
                    $("#EMcycle").prop('readonly',false);
                }
                else{
                    $("#EMaccNo").val('');
                    $("#EMphase").val('');
                    $("#EMcycle").val('');
                    $("#EMaccNo").prop('readonly',false);
                    $("#EMphase").prop('readonly',true);
                    $("#EMcycle").prop('readonly',true);
                 
                }
                console.log("success");
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });
             
             /*End Get Donar Type*/
             
             
         });
         /*End Filter Branch Location and loan product On selecting Bank*/


         /* Change Project*/
         $("#EMproject").change(function(){
            
            var projectId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                async: false,
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
        });
         /*End Change Project*/




  /*Create Schedule Button*/
         $('#createSchedule').on('click',function(){

             $.ajax({
                 url: './gnrLoanRegisterAccountValidateFirstStep',
                 type: 'POST',
                 dataType: 'json',
                 data: $('form').serialize(),
             })
             .done(function(data) {
                 
                  
                  if (data.errors) {

                    $("#submitButton").prop('disabled',true);

                    $("#scheduleTable tbody").empty();

                    if (data.errors['donor']) {
                        $("#donore").empty();
                        $("#donore").append('* '+data.errors['donor']);
                    }
                    if (data.errors['loanProduct']) {
                        $("#loanProducte").empty();
                        $("#loanProducte").append('* '+data.errors['loanProduct']);
                    }
                    if (data.errors['branch']) {
                        $("#branche").empty();
                        $("#branche").append('* '+data.errors['branch']);
                    }
                    if (data.errors['project']) {
                        $("#projecte").empty();
                        $("#projecte").append('* '+data.errors['project']);
                    }
                    if (data.errors['projectType']) {
                        $("#projectTypee").empty();
                        $("#projectTypee").append('* '+data.errors['projectType']);
                    }
                    if (data.errors['accNo']) {
                        $("#accNoe").empty();
                        $("#accNoe").append('* '+data.errors['accNo']);
                    }
                    if (data.errors['agreementDate']) {
                        $("#agreementDatee").empty();
                        $("#agreementDatee").append('* '+data.errors['agreementDate']);
                    }
                    if (data.errors['loanSanctionNumber']) {
                        $("#loanSanctionNumbere").empty();
                        $("#loanSanctionNumbere").append('* '+data.errors['loanSanctionNumber']);
                    }
                    if (data.errors['loanDate']) {
                        $("#loanDatee").empty();
                        $("#loanDatee").append('* '+data.errors['loanDate']);
                    }
                    if (data.errors['interestRate']) {
                        $("#interestRatee").empty();
                        $("#interestRatee").append('* '+data.errors['interestRate']);
                    }
                    if (data.errors['loanAmount']) {
                        $("#loanAmounte").empty();
                        $("#loanAmounte").append('* '+data.errors['loanAmount']);
                    }

                    if (data.errors['cycle']) {
                        $("#cyclee").empty();
                        $("#cyclee").append('* '+data.errors['cycle']);
                    }

                    if (data.errors['phase']) {
                        $("#phasee").empty();
                        $("#phasee").append('* '+data.errors['phase']);
                    }
                    
                   
                    if (data.errors['repaymentFrequency']) {
                        $("#repaymentFrequencye").empty();
                        $("#repaymentFrequencye").append('* '+data.errors['repaymentFrequency']);
                    }
                    if (data.errors['loanDuration']) {
                        $("#loanDuratione").empty();
                        $("#loanDuratione").append('* '+data.errors['loanDuration']);
                    }
                    if (data.errors['gracePeriod']) {
                        $("#gracePeriode").empty();
                        $("#gracePeriode").append('* '+data.errors['gracePeriod']);
                    }
                    if (data.errors['numOfInstallment']) {
                        $("#numOfInstallmente").empty();
                        $("#numOfInstallmente").append('* '+data.errors['numOfInstallment']);
                    }
                    if (data.errors['status']) {
                        $("#statuse").empty();
                        $("#statuse").append('* '+data.errors['status']);
                    }

                    if (data.errors['inappropriate']) {
                        $("#inappropriatee").empty();
                        $("#inappropriatee").append('* '+data.errors['inappropriate']);
                    }

                 } /*end has Errors*/
                 else{

                 

                    $("#submitButton").prop('disabled',false);


                  /*Make the Table*/
                  $("#EMinstallmentTable tbody").empty();
                  

                  var loanDate = $.datepicker.parseDate('dd-mm-yy',$("#EMloanDate").val());
                  var paymentDate = $.datepicker.parseDate('dd-mm-yy',$("#EMloanDate").val());
                  var previousPaymentDate = $.datepicker.parseDate('dd-mm-yy',$("#EMloanDate").val());
                  var gracePeriod = parseInt($("#EMgracePeriod").val());
                  var repaymentFrequency = parseInt($("#EMrepaymentFrequency").val());
                  var numOfInstallment = parseInt($("#EMnumOfInstallment").val());
                 
                  var loanAmount = 0;
                  var principalAmount = 0;
                  if ($("#EMloanAmount").val()!='') {
                    loanAmount = parseFloat($("#EMloanAmount").val());
                  }

                  
                  principalAmount = loanAmount/numOfInstallment;

                  var interestRate = 0;
                  if ($("#EMinterestRate").val()!='') {
                    interestRate = parseFloat($("#EMinterestRate").val());
                  }
                  
                  var interestAmount = 0;

                  var donorType = $("#EMdonorType").val();

                   
                  var i = 1;
                  for(i=1;i<=numOfInstallment;i++){

                     if (i==1) {
                        paymentDate.setMonth(paymentDate.getMonth() + gracePeriod);
                        var dateText = $.datepicker.formatDate("dd-mm-yy", paymentDate);                        
                        interestAmount = loanAmount * interestRate * gracePeriod / (100*12);                        
                        }
                        else{
                            previousPaymentDate = new Date(paymentDate);//$.datepicker.parseDate(paymentDate);
                            paymentDate.setMonth(paymentDate.getMonth() + repaymentFrequency);
                            var dateText = $.datepicker.formatDate("dd-mm-yy", paymentDate);
                            interestAmount = loanAmount * interestRate * repaymentFrequency / (100*12);

                        }

                    loanAmount = loanAmount - principalAmount;

                    if (donorType==1) {  
                    var total = parseFloat(principalAmount.toFixed(2))+ parseFloat(interestAmount.toFixed(2));
                    
                    markup = "<tr><td><input name='tInstallmentNumber[] value='"+i+"' style='display:none;'>"+i+"</td><td><input name='tPaymentDate[]' value='"+dateText+"' style='display:none;'>"+dateText+"</td><td><input name='tPrincipalAmount[]' value='"+(principalAmount)+"' style='display:none;'>"+num(principalAmount)+"</td><td><input name=tInterestAmount[] value='"+interestAmount+"' style='display:none;'>"+num(interestAmount)+"</td><td>"+num(total)+"</td><td>"+num(0)+"</td><td>"+num(0)+"</td><td>"+num(0)+"</td><td>"+num(principalAmount)+"</td><td>"+num(interestAmount)+"</td><td>"+num(total)+"</td><td>Unpaid</td></tr>";

                    }

                    else{
                        markup = "<tr><td><input name='tInstallmentNumber[] value='"+i+"' style='display:none;'>"+i+"</td><td><input name='tPaymentDate[]' class='tPaymentDate' value='"+dateText+"' style='cursor:pointer;text-align:center;' readonly></td><td><input name='tPrincipalAmount[]' class='tPrincipalAmount' value=''></td><td><input name=tInterestAmount[] class='tInterestAmount' value='' ></td><td class='tTotalAmount'></td><td>"+num(0)+"</td><td>"+num(0)+"</td><td>"+num(0)+"</td><td class='tDuePrincipal'></td><td class='tDueInterest'></td><td class='tDueTotal'></td><td>Unpaid</td></tr>";
                    }
                   
                    $("#EMinstallmentTable tbody").append(markup); 

                    $(".tPrincipalAmount").on('input', function() {            
                        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

                        var tPrincipalAmount = 0;
                        var tInterestAmount = 0;
                        if ($(this).val()!='') {
                            tPrincipalAmount = parseFloat($(this).val());
                        }
                        if ($(this).closest('tr').find('.tInterestAmount').val()!='') {
                            tInterestAmount = parseFloat($(this).closest('tr').find('.tInterestAmount').val());
                        }

                        var tTotalAmount = tPrincipalAmount + tInterestAmount;

                        $(this).closest('tr').find('.tTotalAmount').html(num(tTotalAmount));

                        $(this).closest('tr').find('.tDuePrincipal').html(num(this.value));
                        $(this).closest('tr').find('.tDueTotal').html(num(tTotalAmount));
                    });


                    $(".tInterestAmount").on('input', function() {            
                        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

                        var tPrincipalAmount = 0;
                        var tInterestAmount = 0;
                        if ($(this).val()!='') {
                            tInterestAmount = parseFloat($(this).val());
                        }
                        if ($(this).closest('tr').find('.tPrincipalAmount').val()!='') {
                            tPrincipalAmount = parseFloat($(this).closest('tr').find('.tPrincipalAmount').val());
                        }

                        var tTotalAmount = tPrincipalAmount + tInterestAmount;

                        $(this).closest('tr').find('.tTotalAmount').html(num(tTotalAmount));

                         $(this).closest('tr').find('.tDueInterest').html(num(this.value));
                        $(this).closest('tr').find('.tDueTotal').html(num(tTotalAmount));
                    });

                    if ($("#EMdonorType").val()==0) {

                    $(".tPaymentDate").datepicker({
                        changeMonth: true,
                        changeYear: true,
                        yearRange : "-20:+20",
                        /*maxDate: "dateToday",*/
                        dateFormat: 'dd-mm-yy',
                        onSelect: function() {                          
                           
                        }
                    });
                  }

                    $("#updateButton").prop('disabled',false);
                  }
                  /*End Make the Table*/





                    
                    //location.href = "viewLoanRegisterAccount";
                 }

             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });

             
                 
             
         });
         /*End Create Schedule Button*/


         /*Disable Update Button when input changes*/
         $("#EMinterestRate,#EMloanAmount,#EMloanDuration,#EMgracePeriod,#EMnumOfInstallment").on('input', function() {
           $("#updateButton").prop('disabled', true);
           $("#inappropriatee").empty();         
         });
         $("#EMrepaymentFrequency").on('change', function() {
           $("#updateButton").prop('disabled', true);
           $("#inappropriatee").empty();         
         });

         $(document).on('change', 'select', function() {
           $("#updateButton").prop('disabled', true);
           
         });
         /*End Disable Update Button when input changes*/




    /*Update the data*/
    $("#updateButton").on('click', function() {

        $("#submitButton").prop('disabled',true);
          
            var flag = 1;

             $(".tPrincipalAmount").each(function() {
                if ($(this).val()=='') {
                    flag = 0;
                }
            });

            $(".tInterestAmount").each(function() {
                if ($(this).val()=='') {
                    flag = 0;
                }
            });

            if (flag==0) {
                alert("Please fill all the input fileds.");
            }
            else{

              
            
              $.ajax({
                url: './editLoanRegisterAccount',
                type: 'POST',
                dataType: 'json',
                data: $('form').serialize(),
              })
              .done(function(data) {
                if (data.accessDenied) {
                    showAccessDeniedMessage();
                    return false;
                }
                location.reload();
                console.log("success");
              })
              .fail(function() {
                console.log("error");
              })
              .always(function() {
                console.log("complete");
              });
              
            }
       
        var csrf = "{{csrf_token()}}";

        
        
    });
    /*End Update the data*/

      







    /*Delete Modal*/    
    
      $(document).on('click', '.delete-modal', function() {
        if(hasAccess('deleteLoanRegisterAccount')){
      
      $("#DMaccId").val($(this).attr('accountId'));
      $("#deleteModal").modal('show');
    }

      
    });
    /*End Delete Modal*/

    /*Delete The record*/
    $("#DMconfirmButton").on('click',  function() {
        var accId = $("#DMaccId").val();
        var csrf = "{{csrf_token()}}";

        $.ajax({
            url: './deleteLoanRegisterAccount',
            type: 'POST',
            dataType: 'json',
            data: {accId: accId, _token:csrf},
        })
        .done(function(data) {
          if (data.accessDenied) {
              showAccessDeniedMessage();
              return false;
          }
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


 /*On input/change Hide the Errors*/
         $(document).on('input', 'input', function() {
             $(this).closest('div').find('.error').empty();
         });
          $(document).on('change', 'select', function() {
             $(this).closest('div').find('.error').empty();
         });
         /*End On input/change Hide the Errors*/



  });/*End Ready*/
</script>
@include('dataTableScript')

<style type="text/css">
    #otsTable tr td.name{
        text-align: left;
        padding-left: 5px;
    }
    #otsTable tr td.amount{
        text-align: right;
        padding-right: 5px;
    }
    #VMinstallmentTable thead tr th,#EMinstallmentTable thead tr th{
      padding: 3px;
    }
    #VMinstallmentTable thead tr th,#EMinstallmentTable thead tr th{
      border: 1px solid white;
    }
     .disabled {
   pointer-events: none;
   cursor: default;
}
.tInterestAmount,.tPrincipalAmount{
        text-align: center;
    }
  .error{
    color: red;
  }

  #VMinstallmentTable tfoot tr td{
        font-weight: bold;
        padding: 4px;
        text-align: right;
        padding-right: 5px;
    }
    #VMinstallmentTable tfoot tr td{
        font-weight: bold;
        padding: 4px;
    }
    #VMinstallmentTable tfoot tr td:nth-child(1){
        text-align: center;
        padding-right: 0px;
    }
    #VMinstallmentTable tbody tr td{
        text-align: right;
        padding-right: 5px;
    }
    #VMinstallmentTable tbody tr td:nth-child(1),
    #VMinstallmentTable tbody tr td:nth-child(2),
    #VMinstallmentTable tbody tr td:nth-child(6),
    #VMinstallmentTable tbody tr td:nth-child(13)
    {
        text-align: center;
        padding-right: 0px;
    }
</style>



@endsection
