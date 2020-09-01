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
    
      </div> <!--panel-options -->

       <h1 align="center" style="font-family: Antiqua;letter-spacing:2px;"><font color="white">ADVANCE RECEIVE LIST</font></h1>

      </div><!--panel-heading-->

      <div class="panel-body panelBodyView">
       <div>
        <script type="text/javascript">
          jQuery(document).ready(function($){
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
                    <th>Receive Date</th>
                    <th>Name</th>
                    <th>Id</th>
                    <th>Advance Type</th>
                    <th>Project Name</th>
                    <th>Project Type</th>
                    <th>Receive Type</th>
                    <th>Receive Amount</th>
                    <th>Action</th>
                  </tr>
                </thead>

                   <tbody>

                     @foreach ($accAdvanceReceive as $index=>$accAdvanceReceive)

                        @php
                          $result='';
                            if($accAdvanceReceive->houseOwnerId) {
                              $houseOwner= DB::table('gnr_house_Owner')->where('id',$accAdvanceReceive->houseOwnerId)->value('houseOwnerName');
                              $result=$houseOwner;
                            }

                            elseif($accAdvanceReceive->supplierId) {
                              $supplir=DB::table('gnr_supplier')->where('id',$accAdvanceReceive->supplierId)->value('name');
                              $result =$supplir;
                            }
                            elseif($accAdvanceReceive->employeeId) {
                              $employee = DB::table('hr_emp_general_info')->where('id',$accAdvanceReceive->employeeId)->value('emp_name_english');
                              $result =$employee;
                            }
                            $advreceiveType= DB::table('acc_adv_register_type')->where('id',$accAdvanceReceive->regTypeId)->value('name');
                            $projectName= DB::table('gnr_project')->where('id',$accAdvanceReceive->projectId)->value('name');
                            $projectType= DB::table('gnr_project_type')->where('id',$accAdvanceReceive->projectTypeId)->value('name');

                       @endphp

                      @php
                           $name='';
                            if($accAdvanceReceive->employeeId>0) {
                                $employee =DB::table('hr_emp_general_info')->where('id',$accAdvanceReceive->employeeId)->value('emp_id');
                                $name= $employee;
                            }
                            elseif($accAdvanceReceive->supplierId>0) {
                                $supplier =DB::table('gnr_supplier')->where('id',$accAdvanceReceive->supplierId)->value('name');
                                $name= $supplier;
                            }

                      @endphp

                        <tr style:"float:left;">
                            <td>{{$index+1}}</td>
                            <td>{{date('d-m-Y',strtotime($accAdvanceReceive->receivePaymentDate))}}</td>
                            <td style="text-align:left;padding-left:2px;">{{$result}}</td>
                            <td style="text-align:left;padding-left:2px;">{{$name}}</td>
                            <td style="text-align:left;padding-left:2px;">{{$advreceiveType}}</td>
                            <td style="text-align:left;padding-left:2px;">{{$projectName}}</td>
                            <td style="text-align:left;padding-left:2px;">{{$projectType}}</td>
                            <td>

                                    @if($accAdvanceReceive->cashId>0)
                                        {{'Cash'}}
                                    @elseif($accAdvanceReceive->vaucharId>0)
                                        {{'Voucher'}}
                                    @elseif($accAdvanceReceive->bankId>0)
                                        {{'Bank'}}
                                    @endif

                            </td>

                            <td style="text-align:right;padding-left:2px;">{{number_format($accAdvanceReceive->amount,2)}}</td>
                            <td width="80"><a href="javascript:;" class="view-modal" advReceive="{{$accAdvanceReceive->id}}"><i class="fa fa-eye" aria-hidden="true"></i>
                                </a>&nbsp;
                                <a href="javascript:;" class="edit-modal" advReceive="{{$accAdvanceReceive->id}}"><span class="glyphicon glyphicon-edit"></span> </a>&nbsp;
                                <a href="javascript:;" class="delete-modal" advReceive="{{$accAdvanceReceive->id}}"><span class="glyphicon glyphicon-trash"></span>&nbsp;
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
         <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">View Advance Receive Info</h4>
       </div>

    <div class="modal-body">
      <div class="row" style="padding-bottom: 20px;">
        <div class="col-md-12">
         <div class="col-md-12" style="padding-right:2%;">
          <div class="form-horizontal form-groups">
           <input id="VMadvReg" type="hidden" name="advRegId" value="">

             <div class="form-group">
              {!! Form::label('advReceive','AdvReceive Id', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-1 control-label">: </div>
               <div class="col-sm-8">                                              {!! Form::text('advReceive', null,['id'=>'VMadvReceiveId','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
               <p id='advReceivee' class="error" style=color: red;"></p>

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

          {!! Form::label('projectType','Project Type Name', ['class' => 'col-sm-3 control-label']) !!}
          <div class="col-sm-1 control-label">: </div>
            <div class="col-sm-8">
               {!! Form::text('projectType', null,['id'=>'VMprojectType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
               <p id='projectTypee' class="error" style=color: red;"></p>
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

         <div class="form-group">
           {!! Form::label('paymentId','Advance Payment Id', ['class' => 'col-sm-3 control-label']) !!}
           <div class="col-sm-1 control-label">: </div>
            <div class="col-sm-8">
              {!! Form::text('paymentId', null,['id'=>'VMpaymentId','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
              <p id='paymentIde' class="error" style=color: red;"></p>
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
               <p id='houseOwnerName' class="error" style=color:red;"></p>
           </div>
        </div>
        <div class="form-group" id="VMcash1">
             {!! Form::label('cash','Cash', ['class' => 'col-sm-3 control-label']) !!}
           <div class="col-sm-1 control-label" id="cash1">: </div>
           <div class="col-sm-8">
              {!! Form::text('cash', null,['id'=>'VMcash','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
              <p id='cash' class="error" style=color:red;"></p>
           </div>
        </div>
        <div class="form-group" id="VMhVauchar1" style="display:none;">
              {!! Form::label('vauchar','Voucher', ['class' => 'col-sm-3 control-label']) !!}
             <div class="col-sm-1 control-label" id="vauchar1">: </div>
             <div class="col-sm-8">
                 {!! Form::text('vauchar', null,['id'=>'VMvauchar','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                 <p id='vauchar' class="error" style=color:red;"></p>
             </div>
        </div>
        <div class="form-group" id="VMhBank1">
              {!! Form::label('bank','Bank', ['class' => 'col-sm-3 control-label']) !!}
             <div class="col-sm-1 control-label" id="bank1">: </div>
             <div class="col-sm-8">
                {!! Form::text('bank', null,['id'=>'VMbank','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                <p id='bank' class="error" style=color:red;"></p>
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
             </div><!-- end Body -->
          <div class="col-md-3"></div>
        </div><!-- Model Content -->
    </div><!-- Modal Diolog -->
 </div><!-- ViewModal -->

{{--                           Edit Modal                       --}}

  <div id="editModal" class="modal fade" style="margin-top:3%;">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Update Advance Receive</h4>
    </div>

 <div class="modal-body">
  <div class="panel-body float-left">
    <div class="row">
      <div class="col-md-12">
       {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

      <div class="col-md-12">
           <input id="EMadvReceiveId" type="hidden"  value=""/>
        <div class="form-group">
            {!! Form::label('advReceiveId', 'Advance Receive Id', ['class' => 'col-sm-3 control-label']) !!}
          <div class="col-sm-1 control-label"> :</div>
            <div class="col-sm-8">
                {!! Form::text('advReceiveId',null, ['class'=>'form-control', 'id' => 'EMadvRecId','readonly']) !!}
            </div>
        </div>


        <div class="form-group">

          {!! Form::label('project','Project Name', ['class' => 'col-sm-3 control-label']) !!}
          <div class="col-sm-1 control-label">: </div>
            <div class="col-sm-8">
               {!! Form::text('project', null,['id'=>'EMproject','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
               <p id='projecte' class="error" style=color: red;"></p>
            </div>

        </div>

         <div class="form-group">

          {!! Form::label('projectType','Project Type Name', ['class' => 'col-sm-3 control-label']) !!}
          <div class="col-sm-1 control-label">: </div>
            <div class="col-sm-8">
               {!! Form::text('projectType', null,['id'=>'EMprojectType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
               <p id='projecttypee' class="error" style=color: red;"></p>
            </div>

        </div>



        <div class="form-group">
           {!! Form::label('advRegType','Advance Type', ['class' => 'col-sm-3 control-label']) !!}
           <div class="col-sm-1 control-label">: </div>
            <div class="col-sm-8">
              {!! Form::text('advRegType', null,['id'=>'EMregType','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
              <p id='advRegType' class="error" style=color: red;"></p>
            </div>
        </div>
        <div class="form-group">
           {!! Form::label('paymentId','Advance Payment Id', ['class' => 'col-sm-3 control-label']) !!}
           <div class="col-sm-1 control-label">: </div>
            <div class="col-sm-8">
              {!! Form::text('paymentId', null,['id'=>'EMpaymentId','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
              <p id='paymentId' class="error" style=color: red;"></p>
            </div>
        </div>


        <div class="form-group" id="EMemployeeName1">
           {!! Form::label('employeeName','Employee Name', ['class' => 'col-sm-3 control-label']) !!}
           <div class="col-sm-1 control-label" id="employee1">: </div>
            <div class="col-sm-8">
              {!! Form::text('employeeName', null,['id'=>'EMemployeeName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
              <p id='employeeName' class="error" style=color: red;"></p>
            </div>

        </div>

        <div class="form-group" id="EMsupplierName1">
            {!! Form::label('supplierName','Supplier Name', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-1 control-label" id="supplier1">: </div>
             <div class="col-sm-8">
               {!! Form::text('supplierName', null,['id'=>'EMsupplierName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                <p id='supplierName' class="error" style=color:red;"></p>
             </div>
        </div>

        <div class="form-group" id="EMhouseOwnerName1">
            {!! Form::label('houseOwnerName','House Owner Name', ['class' => 'col-sm-3 control-label']) !!}
           <div class="col-sm-1 control-label" id="houseOwner1">: </div>
           <div class="col-sm-8">
               {!! Form::text('houseOwnerName', null,['id'=>'EMhouseOwnerName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
               <p id='houseOwnerName' class="error" style=color:red;"></p>
           </div>
        </div>

        <div class="form-group" id="EMcash1">
             {!! Form::label('cash','Cash', ['class' => 'col-sm-3 control-label']) !!}
           <div class="col-sm-1 control-label" id="cash1">: </div>
           <div class="col-sm-8">
              {!! Form::text('cash', null,['id'=>'EMcash','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
              <p id='cash' class="error" style=color:red;"></p>
           </div>
        </div>

        <div class="form-group" id="EMhVauchar1" style="display:none;">
              {!! Form::label('vauchar','Voucher', ['class' => 'col-sm-3 control-label']) !!}
             <div class="col-sm-1 control-label" id="vauchar1">: </div>
             <div class="col-sm-8">
                 {!! Form::text('vauchar', null,['id'=>'EMvauchar','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                 <p id='vauchar' class="error" style=color:red;"></p>
             </div>
        </div>

        <div class="form-group" id="EMhBank1">
              {!! Form::label('bank','Bank', ['class' => 'col-sm-3 control-label']) !!}
             <div class="col-sm-1 control-label" id="bank1">: </div>
             <div class="col-sm-8">
                {!! Form::text('bank', null,['id'=>'EMbank','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                <p id='bank' class="error" style=color:red;"></p>
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
            <input id="DMadvReg" type="hidden" name="advReceive" value="">
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


<!-- - - - - - - - - - - -Delete Model- - - - - - - - - - - - - -->

   <div id="deleteModal" class="modal fade" style="margin-top:3%;">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Advance Receive</h4>
          </div>

         <div class="modal-body ">
           <div class="row" style="padding-bottom:20px;"> </div>
            <h2>Are You Confirm to Delete This Record?</h2>

          <div class="modal-footer">
             <input id="DMadvReceiveId" type="hidden"  value=""/>
             <button type="button" class="btn btn-danger"  id="DMadvRec"  data-dismiss="modal">confirm</button>
             <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
          </div>

         </div>
        </div>
      </div>
   </div>

{{--end delete modal--}}

<!--                          Project Change                     -->
<script>
          /* Change Project*/

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
              success: function(data){

                    $.each(data['branchList'], function (key, branchObj)  {
                       if (branchObj.id==1){
                            $('#EMbranchName').append("<option value='"+ branchObj.id+"'selected='selected'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                       }
                        else{

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
      function formateDate(argument) {
        var date = $.datepicker.parseDate('yy-mm-dd',argument);
        return $.datepicker.formatDate("dd-mm-yy", date);
      }
      $(document).on('click', '.view-modal', function(){
        if(hasAccess('viewAdvReceive')){
          var advReceive = $(this).attr('advReceive');
          var csrf = "{{csrf_token()}}";
          $("#viewModal").find('.modal-dialog').css('width', '60%');
          $("#viewModal").modal('show');
          $("#VMadvReg").val(advReceive);
          $.ajax({
            url: './viewAdvReceive',
            type: 'POST',
            dataType: 'json',
            data: {id:advReceive , _token: csrf},
            success: function(data) {
              $("#VMadvReceiveId").val(data['accAdvanceReceive'].advReceiveId);
              $("#VMpaymentId").val(data['advPaymentId']);
              $("#VMregType").val(data['advRegType']);
              if(data['employee']!='') {
                $("#VMemployeeName").val(data['employee']);
                $("#VMemployeeName1").show();
              }
              else{
                $("#VMemployeeName1").hide();
              }
              if(data['supplier']!=null) {
                $("#VMsupplierName").val(data['supplier']);
                $("#VMsupplierName1").show();
              }
              else{
                $("#VMsupplierName1").hide();
              }
                $("#VMproject").val(data['project']);
                $("#VMprojectType").val(data['projectType']);
              if(data['houseOwner']!=null){
                $("#VMhouseOwnerName").val(data['houseOwner']);
                $("#VMhouseOwnerName1").show();
              }
              else{
                 $("#VMhouseOwnerName1").hide();
              }
              if(data['cash']!=null && data['cash']!='') {
                 $("#VMcash").val(data['cash']);
                 $("#VMcash1").show();
                 $("#VMhVauchar1").hide();
             }
             else{
                 $("#VMcash1").hide();
             }
             if(data['accAdvanceReceive'].vaucharId !=null && data['accAdvanceReceive'].vaucharId !='' && data['accAdvanceReceive'].vaucharId>0){
               $("#VMvauchar").val(data['accAdvanceReceive'].vaucharId);
               $("#VMhVauchar1").show();
             }
             else {
               $("#VMhVauchar1").hide();
             }
             if(data['bank']!=null) {
               $("#VMbank").val(data['bank']);
               $("#VMhBank1").show();
               $("#VMhVauchar1").hide();
             }
             else {
                $("#VMhBank1").hide();
             }

            $("#VMamount").val(data['accAdvanceReceive'].amount);
            $("#VMhpaymentDate").val (formateDate(data['accAdvanceReceive'].receivePaymentDate));

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
            function formateDate(argument) {
               var date = $.datepicker.parseDate('yy-mm-dd',argument);
               return $.datepicker.formatDate("dd-mm-yy", date);
            }
            $('#EMamount').on('input', function(event) {
              this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
            });

             $(document).on('click', '.edit-modal', function(){
              if(hasAccess('getAdvRecInfo')){
                  var advReceive = $(this).attr('advReceive');
                  var csrf = "{{csrf_token()}}";
                  $("#EMadvReceiveId").val(advReceive);
                  $.ajax({
                     url: './getAdvRecInfo',
                     type: 'POST',
                     dataType: 'json',
                     data: {id:advReceive , _token: csrf},
                     success: function(data) {
                        $("#EMadvRecId").val(data['accAdvanceReceive'].advReceiveId);
                        $("#EMregType").val(data['advRegType']);
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
                        $("#EMproject").val(data['project']);
                        $("#EMprojectType").val(data['projectType']);
                        $("#EMpaymentId").val(data['advPaymentId']);

                        if(data['houseOwner']!=null){
                            $("#EMhouseOwnerName").val(data['houseOwner']);
                            $("#EMhouseOwnerName1").show();
                        }

                        else{
                            $("#EMhouseOwnerName1").hide();
                        }

                        if(data['cash']!=null && data['cash']!='') {
                            $("#EMcash").val(data['cash']);
                            $("#EMcash1").show();
                            $("#EMhVauchar1").hide();

                        }
                        else{
                             $("#EMcash1").hide();
                        }

                        if(data['accAdvanceReceive'].vaucharId !=null && data['accAdvanceReceive'].vaucharId !='' && data['accAdvanceReceive'].vaucharId>0){
                             $("#VMvauchar").val(data['accAdvanceReceive'].vaucharId);
                             $("#EMhVauchar1").show();
                            }
                         else
                         {
                              $("#EMhVauchar1").hide();
                          }
                          if(data['bank']!=null) {
                              $("#EMbank").val(data['bank']);
                              $("#EMhBank1").show();
                              $("#EMhVauchar1").hide();
                          }
                          else
                           {
                              $("#EMhBank1").hide();
                          }

                          $("#EMamount").val(data['accAdvanceReceive'].amount);
                          $("#EMhpaymentDate").val (formateDate(data['accAdvanceReceive'].receivePaymentDate));


                          $('#EMamount').keyup(function(){
                          if ($(this).val() >data['payableAmount']){
                             alert("No numbers above "+data['payableAmount']);
                             $(this).val(data['payableAmount']);
                          }
                       });
                         $("#editModal").find('.modal-dialog').css('width', '60%');
                         $("#editModal").modal('show');
                          },
                               error: function(argument) {
                                //alert('response error');
                    }

                });
                }
            });
        });
</script>

  <!--  <script type="text/javascript">

        $(document).ready(function(){

      $('input:radio[name=advReceiveChange]').change(function(){
       var advReceiveChange = $('input[name=advReceiveChange]:checked').val();
       var advReciveId = $('#EMadvReceiveId').val();
       var csrf = "{{csrf_token()}}";
       $.ajax({

           type: 'post',
           url: './advReceChange',
           data:{advReceiveChange:advReceiveChange,advReciveId:advReciveId,_token: csrf},
           dataType: 'json',
           async:false,

       success: function(response){

            if(advReceiveChange=='a') {
                 $("#EMcash2").show();
                 $("#EMbank5").hide();
                 $("#EMvauchar2").hide();
             }
             else if(advReceiveChange=='b') {
                 $("#EMvauchar2").show();
                 $("#EMcash2").hide();
                 $("#EMbank5").hide();
             }
               else if(advReceiveChange=='c') {
                      $("#EMbank5").show();
                      $("#EMvauchar2").hide();
                      $("#EMcash2").hide();
               }
          },
                           error: function(_response) {
                               alert("error");
                            }
               });
            });
     });

  </script>
   -->

<script type="text/javascript">

 $(document).ready(function() {
  $("#updateButton").on('click', function() {
            var receiveId         = $("#EMadvReceiveId").val();
            var advReceiveAmount  = $("#EMamount").val();
            var receivePayDate    = $("#EMhpaymentDate").val();
            var advReceive        = $('input[name=advReceiveChange]:checked').val();
            var advRegChange      =   $('input[name=advRegChange]:checked').val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                 url: './updateAdvReceiveInfo',
                 type: 'POST',
                 dataType: 'json',
                 data: {id:receiveId,advRegChange:advRegChange,advReceiveChange:advReceive,advReceiveAmount:advReceiveAmount,paymentDate:receivePayDate,_token: csrf},
             })

            .done(function(data) {
                if (data.errors) {

                    if (data.errors['amount'])  {
                        $("#EMamount").empty();
                        $("#EMamount").append('*'+data.errors['amount']);
                    }
                    if (data.errors['paymentDate']) {
                        $("#EMpaymentDate").empty();
                        $("#EMpaymentDate").append('*'+data.errors['paymentDate']);
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
});

</script>

<!-- Project Change -->

<script type="text/javascript">
  $(document).ready(function() {
          function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

       $("#EMproject").change(function() {

           var project = $(this).val();
           var csrf = "<?php echo csrf_token(); ?>";

           $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:project,_token: csrf},
                dataType: 'json',
                 success: function( data ) {
                    $("#EMprojectType").empty();
                    $("#EMprojectType").prepend('<option selected="selected" value="">Select Project Type</option>');

                   $.each(data['projectTypeList'], function (key, projectObj) {
                          $('#EMprojectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");
                    });

                },
                error: function(_response) {
                    alert("error");
                }

           });/*End Ajax*/

        });/*End Change Project*/
});
</script>

<script>

$(document).ready(function(){
  $(document).on('click', '.delete-modal', function()  {
    if(hasAccess('deleteadvReceive')){
      $("#DMadvReceiveId").val($(this).attr('advReceive'));
      $("#deleteModal").find('.modal-dialog').css('width', '60%');
      $('#deleteModal').modal('show');
      $("#DMadvRec").on('click',  function(){
         var advReceive= $("#DMadvReceiveId").val();

         var csrf = "{{csrf_token()}}";
            $.ajax({
              url: './deleteadvReceive',
              type: 'POST',
              dataType: 'json',
              data: {advReceive:advReceive,_token: csrf},
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
    }
  });
});

</script>

@include('dataTableScript')
@endsection
