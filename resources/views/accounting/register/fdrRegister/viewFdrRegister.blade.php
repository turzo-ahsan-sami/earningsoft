@extends('layouts/acc_layout')
@section('title', '| FDR Register')
@section('content')

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addFdrRegister/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add FDR</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">FDR ACCOUNT LIST</font></h1>
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
                        <th>Account Number</th>
                        <th>Account Name</th>
                        <th>FDR Type</th>                        
                        <th>Bank Name</th>
                        {{-- <th>Branch Location</th> --}}
                        <th>Interest Rate (%)</th>
                        <th>Duration</th>
                        <th>Principal Amount (Tk)</th>
                        <th>Action</th>
                      </tr>
                      
                    </thead>
                    <tbody>
                     @foreach($frdAccounts as $index => $frdAccount)
                     @php
                         $fdrTypeName = DB::table('acc_fdr_type')->where('id',$frdAccount->fdrTypeId_fk)->value('name');
                         $bankName = DB::table('gnr_bank')->where('id',$frdAccount->bankId_fk)->value('name');
                         $bankBranchName = DB::table('gnr_bank_branch')->where('id',$frdAccount->bankBranchId_fk)->value('name');
                     @endphp
                        <tr>
                            <td>{{$index+1}}</td>
                            <td>{{$frdAccount->openingDate}}</td>
                            <td>{{$frdAccount->accNo}}</td>
                            <td class="name">{{$frdAccount->accName}}</td>
                            <td class="name">{{$fdrTypeName}}</td>
                            <td class="name">{{$bankName}}</td>
                            {{-- <td class="name">{{$bankBranchName}}</td> --}}
                            <td class="amount">{{number_format($frdAccount->interestRate,2)}}</td>
                            <td>{{str_pad($frdAccount->duration,2,'0',STR_PAD_LEFT)}} @if($frdAccount->duration>1){{"Months"}}@else{{"Month"}}@endif</td>
                            <td class="amount">{{number_format($frdAccount->principalAmount,2,'.',',')}}</td>
                            <td width="80">

                            <a href="javascript:;" class="view-modal" accountId="{{$frdAccount->id}}">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                              </a>&nbsp; 
                              <a href="javascript:;" class="edit-modal" accountId="{{$frdAccount->id}}">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>&nbsp;

                            <a href="javascript:;" class="delete-modal" accountId="{{$frdAccount->id}}">
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

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                        <div class="form-group">
                                            {!! Form::label('accNo', 'Account Number:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('accNo', null,['id'=>'VMaccNo','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('accName', 'Account Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('accName', null,['id'=>'VMaccName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('fdrType', 'FDR Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('fdrType', null,['id'=>'VMfdrType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            {!! Form::label('project', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('project', null,['id'=>'VMproject','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            {!! Form::label('projectType', 'Pro. Type:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('projectType', null,['id'=>'VMprojectType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        

                                         <div class="form-group">
                                            {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">  
                                                                            
                                                {!! Form::text('branch',null, ['class'=>'form-control', 'id' => 'VMbranch','readonly']) !!}
                                                
                                            </div>
                                        </div>

                                       

                                     

                                    </div>{{--form-horizontal form-groups--}}
                                </div>{{--End 1st col-md-6--}}

                                <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">


                                     <div class="form-group">
                                            {!! Form::label('bankName', 'Bank Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('bankName', null,['id'=>'VMbank','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('bankBranch', 'Bank Branch Location:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                                
                                                {!! Form::text('bankBranch', null,['id'=>'VMbankBranch','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                    <div class="form-group">
                                            {!! Form::label('principalAmount', 'Principal Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('principalAmount', null,['id'=>'VMprincipalAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                       

                                       

                                        <div class="form-group">
                                            {!! Form::label('interestRate', 'Interest Rate:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('interestRate', null,['id'=>'VMinterestRate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        

                                        <div class="form-group">
                                            {!! Form::label('duration', 'Duration:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('duration', null,['id'=>'VMduration','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('openingDate', 'Opening Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('openingDate', null,['id'=>'VMopeningDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('matureDate', 'Maturity Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('matureDate', null,['id'=>'VMmatureDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        

                                    </div>
                                </div>{{--End 2nd col-md-6--}}
                            </div>

                        </div>{{--row--}}

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
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update Account</h4>
            </div>
            <div class="modal-body">

                <div class="row" style="padding-bottom: 20px;">

                    <div class="col-md-12">
                        <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                            <div class="form-horizontal form-groups">

                            {!! Form::hidden('fdrRowId',null,['id'=>'EMfdrRowId']) !!}

                                <div class="form-group">
                                    {!! Form::label('accNo', 'Account Number:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">                                               
                                        {!! Form::text('accNo', null,['id'=>'EMaccNo','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                        <p id='accNoe' class="error" style="max-height:3px;color: red;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('accName', 'Account Name:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">                                               
                                        {!! Form::text('accName', null,['id'=>'EMaccName','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                        <p id='accNamee' class="error" style="max-height:3px;color: red;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('fdrType', 'FDR Type:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">  
                                    @php
                                      $fdrTypeList = array(''=>'Select FDR Type') + DB::table('acc_fdr_type')->pluck('name','id')->toArray();
                                    @endphp                                  
                                        {!! Form::select('fdrType',$fdrTypeList ,null, ['class'=>'form-control', 'id' => 'EMfdrType']) !!}
                                        <p id='fdrTypee' class="error" style="max-height:3px;color: red;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                        {!! Form::label('project', 'Project:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                        @php
                          $projects = DB::table('gnr_project')->select('id','name','projectCode')->get();
                        @endphp
                         <select name="project" class="form-control input-sm" id="EMproject">
                            <option value="">Select Project</option>                                         
                            @foreach($projects as $project)
                            <option value="{{$project->id}}">{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT).'-'.$project->name}}</option>
                            @endforeach
                        </select>                               
                            <p id='projecte' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                        @php
                          $projectTypes = DB::table('gnr_project_type')->select('id','name','projectTypeCode')->get();
                        @endphp
                         <select name="projectType" class="form-control input-sm" id="EMprojectType">
                            <option value="">Select Project Type</option>                                         
                            @foreach($projectTypes as $projectType)
                            <option value="{{$projectType->id}}">{{str_pad($projectType->projectTypeCode,3,"0",STR_PAD_LEFT).'-'.$projectType->name}}</option>
                            @endforeach
                        </select>                               
                            <p id='projectTypee' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                        @php
                          $branches = DB::table('gnr_branch')->where('id',1)->select('id','name','branchCode')->get();
                        @endphp
                         <select name="branch" class="form-control input-sm" id="EMbranch">
                            {{-- <option value="">Select Branch</option>    --}}                                      
                            @foreach($branches as $branch)
                            <option value="{{$branch->id}}">{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT).'-'.$branch->name}}</option>
                            @endforeach
                        </select>                               
                            <p id='branche' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                    </div>

                        


                                

                             

                            </div>{{--form-horizontal form-groups--}}
                        </div>{{--End 1st col-md-6--}}

                        <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                            <div class="form-horizontal form-groups">


                            <div class="form-group">
                                {!! Form::label('bankName', 'Bank:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                @php                                   
                                    $bankList = array(''=>'Select Bank') + DB::table('gnr_bank')->pluck('name','id')->toArray();                                    
                                @endphp  
                                    {!! Form::select('bank', $bankList,null, ['class'=>'form-control', 'id' => 'EMbank']) !!}
                                    <p id='banke' class="error" style="max-height:3px;color: red;"></p>
                                </div>
                            </div>


                        <div class="form-group">
                        {!! Form::label('bankBranch', 'Bank Branch Location:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                        @php
                            $bankBranchList = DB::table('gnr_bank_branch')->select('name','id','bankId_fk')->get();
                        @endphp

                        <select id="EMbankBranch" name="bankBranch" class="form-control">
                             <option value="">Select Location</option>
                             @foreach($bankBranchList as $bankBranch)
                                @php
                                $bankShortName = DB::table('gnr_bank')->where('id',$bankBranch->bankId_fk)->value('shortName');
                                @endphp
                             <option value="{{$bankBranch->id}}">{{$bankBranch->name.'-'.$bankShortName}}</option>>
                             @endforeach
                         </select>                            
                            
                            <p id='bankBranche' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                    </div>


                               {{--  <div class="form-group">
                                    {!! Form::label('bankBranch', 'Bank Branch Location:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                    @php
                                        $bankBranchList = array(''=>'Select Location') + DB::table('gnr_bank_branch')->pluck('name','id')->toArray();
                                    @endphp   
                                        {!! Form::select('bankBranch', $bankBranchList,null, ['class'=>'form-control', 'id' => 'EMbankBranch']) !!}                                        
                                        <p id='bankBranche' class="error" style="max-height:3px;color: red;"></p>
                                        
                                    </div>
                                </div> --}}


                            <div class="form-group">
                                    {!! Form::label('principalAmount', 'Principal Amount:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('principalAmount', null,['id'=>'EMprincipalAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                        <p id='principalAmounte' class="error" style="max-height:3px;color: red;"></p>
                                    </div>
                                </div>                             

                               

                                <div class="form-group">
                                    {!! Form::label('interestRate', 'Interest Rate:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('interestRate', null,['id'=>'EMinterestRate','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                        <p id='interestRatee' class="error" style="max-height:3px;color: red;"></p>
                                    </div>
                                </div>

                                

                                <div class="form-group">
                                    {!! Form::label('duration', 'Duration:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                    @php
                                       $monthArray = array(''=>'Select Duration','1'=>'1 month','2'=>'2 months','3'=>'3 months','4'=>'4 months','5'=>'5 months','6'=>'6 months','7'=>'7 months','8'=>'8 months','9'=>'9 months','10'=>'10 months','11'=>'11 months','12'=>'12 months');
                                   @endphp
                                        {!! Form::select('duration', $monthArray,null,['id'=>'EMduration','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                        <p id='duratione' class="error" style="max-height:3px;color: red;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('openingDate', 'Opening Date:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('openingDate', null,['id'=>'EMopeningDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly','style'=>'cursor:pointer']) !!}
                                        <p id='openingDatee' class="error" style="max-height:3px;color: red;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('matureDate', 'Maturity Date:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('matureDate', null,['id'=>'EMmatureDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                    </div>
                                </div>

                                

                            </div>
                        </div>{{--End 2nd col-md-6--}}
                    </div>

                </div>{{--row--}}

                {{-- View ModalFooter--}}
                <div class="modal-footer">
                <button id="updateButton" class="btn actionBtn glyphicon glyphicon-check btn-success edit" paymentId="0" type="button"> Update</button>
                    <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span> Close</span></button>
                </div>


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


    /*View Modal*/    
    
    $(document).on('click', '.view-modal', function() {
        if(hasAccess('fdrGetAccountInfo')){

      var accId = $(this).attr('accountId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './fdrGetAccountInfo',
        type: 'POST',
        dataType: 'json',
        data: {accId: accId, _token: csrf},
        success: function(data) {

        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }
          
         $("#VMaccNo").val(data['account'].accNo);
         $("#VMaccName").val(data['account'].accName);
         $("#VMfdrType").val(data['fdrTypeName']);
         $("#VMbank").val(data['bankName']);
         $("#VMbankBranch").val(data['bankBranchName']);
         
         $("#VMproject").val(data['projectName']);
         $("#VMprojectType").val(data['projectTypeName']);
         $("#VMbranch").val(data['branchName']);

         $("#VMprincipalAmount").val(num(data['account'].principalAmount));
         $("#VMinterestRate").val(num(data['account'].interestRate));
         $("#VMduration").val(data['duration']);         
         $("#VMopeningDate").val(data['openingDate']);
         $("#VMmatureDate").val(data['matureDate']);


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
        if(hasAccess('fdrGetAccountInfoToUpdate')){

      var accId = $(this).attr('accountId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './fdrGetAccountInfoToUpdate',
        type: 'POST',
        dataType: 'json',
        data: {accId: accId, _token: csrf},
        success: function(data) {
          
        if (data.accessDenied) {
            showAccessDeniedMessage();
            return false;
        }

        $("#EMfdrRowId").val(data['account'].id);
         $("#EMaccNo").val(data['account'].accNo);
         $("#EMaccName").val(data['account'].accName);
         $("#EMfdrType").val(data['account'].fdrTypeId_fk);
         $("#EMbank").val(data['account'].bankId_fk);
         $("#EMbankBranch").val(data['account'].bankBranchId_fk);

         $("#EMproject").val(data['account'].projectId_fk);
         $("#EMprojectType").val(data['account'].projectTypeId_fk);
         /*$("#EMbranch").val(data['account'].branchId_fk);*/
         

         $("#EMprincipalAmount").val(data['account'].principalAmount);
         $("#EMinterestRate").val(data['account'].interestRate);
         $("#EMduration").val(data['account'].duration)     
         $("#EMopeningDate").val(data['openingDate']);
         $("#EMmatureDate").val(data['matureDate']);

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

        $("#EMprincipalAmount,#EMinterestRate").on('input', function() {            
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        $("#EMduration").on('input', function() {            
            this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
        });


        /*Calculate Mature Date*/
         function calculateMatureDate() {
             var months = 0;
             if ($("#EMduration").val()!='') {
                months = parseInt($("#EMduration").val());
             }
             var openingDate =  $("#EMopeningDate").val();

             if (openingDate!='') {
                var d = $.datepicker.parseDate('dd-mm-yy', $("#EMopeningDate").val());
                if(months!=0 || months!=""){
                    d.setMonth(d.getMonth() + parseInt(months));
                }

                var matureDate = $('#EMmatureDate');
                matureDate.datepicker({
                    dateFormat: 'dd-mm-yy'
                });
                matureDate.datepicker('setDate', d);
                $("#EMmatureDate").datepicker( "option", "disabled", true );

             }
         }

         /*End Calculate Mature Date*/


         $("#EMopeningDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                $("#EMopeningDatee").empty();

                if($("#EMduration").val()!=''){
                    calculateMatureDate();
                }
            }
        });

         $("#EMduration").on('change', function() {             
             calculateMatureDate();
         });


         /*Add Location*/
         $("#addToLocation").on('click', function(event) {
                if ($("#bankBranchList option:selected").val()!='') {
                    $("#EMbankBranch").val($("#bankBranchList option:selected").text());
                }          
         });
         /*End Add Location*/

          /*Add Bank Name*/
         $("#addTobank").on('click', function(event) {
            
                if ($("#bankList option:selected").val()!='') {
                    $("#EMbankName").val($("#bankList option:selected").text());
                }          
         });
         /*End Add Bank Name*/


        /*Filter Branch Location On selecting Bank*/
         $("#EMbank").on('change', function() {
             var bank = $("#EMbank option:selected").val();
             var csrf = "{{csrf_token()}}";

             $.ajax({
                 url: './accFdrGetBranchLocationBaseOnBank',
                 type: 'POST',
                 dataType: 'json',
                 data: {bank: bank, _token: csrf},
             })
             .done(function(branch) {

                if (branch.accessDenied) {
                    showAccessDeniedMessage();
                    return false;
                }
                
                $("#EMbankBranch").empty();
                $("#EMbankBranch").append("<option value=''>Select Location</option>");
                $.each(branch, function(index, branch) {
                     $("#EMbankBranch").append("<option value='"+branch.id+"'>"+branch.name+'-'+branch.bankName+"</option>");
                });
               

                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });
             
         });
         /*End Filter Branch Location On selecting Bank*/




    /*Delete Modal*/    
    
      $(document).on('click', '.delete-modal', function() {
        if(hasAccess('deleteAccFdr')){
      
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
            url: './deleteAccFdr',
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
           var months = parseInt($("#EMperiod option:selected").attr('months'));
           var dateFrom = $("#EMopeningDate").val();
           
           if (dateFrom!="") {
            parts = dateFrom.split("-");
            var CurrentDate = new Date(parts[2],parts[1]-1,parts[0]);
            CurrentDate.setMonth(CurrentDate.getMonth() + months);
            $("#EMmatureDate").val(GetFormattedDate(CurrentDate));
        }
           
       });
       
        /*End Calculate Mature Date*/

    /*Opening Date*/
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
                    $("#EMmatureDate").val(GetFormattedDate(CurrentDate));
                }                 
                    
        });
        /*End Opening Date*/

    

       




        /*Hide the error if filed is filed*/
       
        /*End Hide the error if filed is filed*/



      


        /*Update the data*/
        $("#updateButton").on('click', function() {
            var accId = $("#EMfdrRowId").val();
            var accNo = $("#EMaccNo").val();
            var accName = $("#EMaccName").val();
            var fdrTypeId = $("#EMfdrType").val();

            var project = $("#EMproject").val();
            var projectType = $("#EMprojectType").val();
            var branch = $("#EMbranch").val();

            var bank = $("#EMbank").val();
            var bankBranch = $("#EMbankBranch").val();
            var principalAmount = $("#EMprincipalAmount").val();
            var interestRate = $("#EMinterestRate").val();
            var duration = $("#EMduration").val();
            var openingDate = $("#EMopeningDate").val();
            var matureDate = $("#EMmatureDate").val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './editAccFdrAccount',
                type: 'POST',
                dataType: 'json',
                data: {accId: accId,accNo: accNo,accName: accName,fdrTypeId: fdrTypeId,bank: bank,bankBranch: bankBranch,principalAmount: principalAmount,interestRate: interestRate,duration: duration,openingDate: openingDate,matureDate: matureDate,project: project, projectType: projectType, branch:branch, _token: csrf},
            })
            .done(function(data) {

                if (data.accessDenied) {
                    showAccessDeniedMessage();
                    return false;
                }
                
                if (data.errors) {
                    if (data.errors['fdrTypeId']) {
                        $("#fdrTypee").empty();
                        $("#fdrTypee").append('* '+data.errors['fdrTypeId']);
                    }
                    if (data.errors['accNo']) {
                        $("#accNoe").empty();
                        $("#accNoe").append('* '+data.errors['accNo']);
                    }
                     if (data.errors['accName']) {
                        $("#accNamee").empty();
                        $("#accNamee").append('* '+data.errors['accName']);
                    }
                    if (data.errors['project']) {
                        $("#projecte").empty();
                        $("#projecte").append('* '+data.errors['project']);
                    }
                    if (data.errors['projectType']) {
                        $("#projectTypee").empty();
                        $("#projectTypee").append('* '+data.errors['projectType']);
                    }
                    if (data.errors['branch']) {
                        $("#branche").empty();
                        $("#branche").append('* '+data.errors['branch']);
                    }
                    if (data.errors['bank']) {
                        $("#banke").empty();
                        $("#banke").append('* '+data.errors['bank']);
                    }
                    if (data.errors['bankBranch']) {
                        $("#bankBranche").empty();
                        $("#bankBranche").append('* '+data.errors['bankBranch']);
                    }
                    if (data.errors['principalAmount']) {
                        $("#principalAmounte").empty();
                        $("#principalAmounte").append('* '+data.errors['principalAmount']);
                    }
                    if (data.errors['interestRate']) {
                        $("#interestRatee").empty();
                        $("#interestRatee").append('* '+data.errors['interestRate']);
                    }
                    if (data.errors['openingDate']) {
                        $("#openingDatee").empty();
                        $("#openingDatee").append('* '+data.errors['openingDate']);
                    }
                    if (data.errors['duration']) {
                        $("#duratione").empty();
                        $("#duratione").append('* '+data.errors['duration']);
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
            .always(function() {
                console.log("complete");
            });
            
        });
        /*End Update the data*/




         /* Change Project*/
         $("#EMproject").change(function(){
            
            var projectId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                success: function( data ){

                    if (data.accessDenied) {
                        showAccessDeniedMessage();
                        return false;
                    }

                    $("#EMprojectType").empty();
                    $("#EMprojectType").prepend('<option selected="selected" value="">Select Project Type</option>');


                   /* $("#EMbranch").empty();
                    $("#EMbranch").prepend('<option selected="selected" value="">Select Branch</option>');*/
                   

                    $.each(data['projectTypeList'], function (key, projectObj) {
                                
                            $('#EMprojectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");                       
                    });

                    /*$.each(data['branchList'], function (key, branchObj) {

                        if (branchObj.id==1) {
                            $('#EMbranch').append("<option value='"+ branchObj.id+"'selected='selected'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                        }
                        else{
                            $('#EMbranch').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>"); 
                        }
                                
                                                  
                    });*/

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/
        });
         /*End Change Project*/

     /* Change Project Type*/
         /*$("#EMprojectType").change(function(){
            var projectId = $("#EMproject option:selected").val();
            var projectTypeId = $(this).val();


            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProjectType',
                data: {projectId:projectId,projectTypeId: projectTypeId,_token: csrf},
                dataType: 'json',
                success: function( data ){ 

                     $("#EMbranch").empty();
                    $("#EMbranch").append('<option selected="selected" value="">Select Branch</option>');
                    

                     $.each(data['branchList'], function (key, branchObj) {

                        if (branchObj.id==1) {
                            $('#EMbranch').append("<option value='"+ branchObj.id+"'selected='selected'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                        }
                        else{
                            $('#EMbranch').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>"); 
                        }
                                
                           
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });

         });*//*End Change Project Type*/

        




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
</style>


@include('dataTableScript')
@endsection
