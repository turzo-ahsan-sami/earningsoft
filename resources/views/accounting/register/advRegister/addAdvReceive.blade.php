@extends('layouts/acc_layout')
@section('title', '| Advance Receive')
@section('content')

        @php

          use App\accounting\AccAdvRegister;

        @endphp


<div class="row add-data-form">
  <div class="col-md-2"></div>
    <div class="col-md-8 fullbody">
      <div class="viewTitle" style="border-bottom: 1px solid white;">

          <a href="{{url('viewAdvanceReceivelist/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>ADVANCE RECEIVE LIST</a>
      </div>
      <div class="panel panel-default panel-border">
        <div class="panel-heading">
          <div class="panel-title">Advance Receive</div>
        </div>

      <div class="panel-body">
        <div class="row"> 
          <div class="col-md-8">
            {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
            <div class="form-group">
                {!! Form::label('advReceiveNumber', 'Advance Receive Id', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-1 control-label">: </div>
                <div class="col-sm-8"> 
                    {!! Form::text('advReceiveNumber',$advReceiveNumber, ['class'=>'form-control', 'id' => 'advReceiveNumber','readonly']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('project', 'Project', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-1 control-label">: </div>
                <div class="col-sm-8"> 
                    @php
                       $projects = DB::table('gnr_project')->select('projectCode','name','id')->orderBy('projectCode')->get();
                    @endphp 

                    <select  class="form-control" id='project' name="project">
                        <option value="">Select Project Name</option>
                        @foreach($projects as $project )

                           <option  value="{{$project->id}}">{{$project->projectCode.'-'.$project->name}}</option>

                        @endforeach
                    </select>
                   <p id='projecte' class="error" style="max-height:3px;color: red;"></p>

               </div>
           </div>

           <div class="form-group">
               {!! Form::label('projectType', 'Project Type', ['class' => 'col-sm-3 control-label']) !!}
               <div class="col-sm-1 control-label">: </div>
               <div class="col-sm-8"> 

                   @php
                      $projectTypes =DB::table('gnr_project_type')->select('projectTypeCode','name','id')->get();
                   @endphp 

                   <select class="form-control" id="projectType" name="projectType">
                       <option>Select Project Type</option>
                       @foreach( $projectTypes as  $projectType)
                          <option  value="{{$projectType->id}}">{{$projectType->projectTypeCode.'-'.$projectType->name}}</option>
                       @endforeach
                   </select>
                   <p id='projectTypee' class="error" style="max-height:3px;color: red;"></p>

               </div>
           </div>

           <div class="form-group">
               {!! Form::label('advRegType', 'Advance Type', ['class' => 'col-sm-3 control-label']) !!}
               <div class="col-sm-1 control-label">: </div>
               <div class="col-sm-8"> 

                   @php
                      $advTypeList = array(''=>'Select Advance Type') + DB::table('acc_adv_register_type')->pluck('name','id')->toArray();
                   @endphp 

                   {!! Form::select('advRegType', $advTypeList ,null, ['class'=>'form-control', 'id' => 'advRegType']) !!}

                   <p id='advRegTypee' class="error" style="max-height:3px;color: red;"></p>

               </div>
           </div>


           <div class="form-group">
               <div class="col-sm-3"> Services Category</div>
               <div class="col-sm-1 control-label">: </div>
               <div class="col-sm-8">
                   <input type="radio" name="advRegChange" value="1" id="houseOwner" /> HouseOwner
                   <input type="radio" name="advRegChange" value="2" id="supplier" /> Supplier
                   <input type="radio" name="advRegChange" value="3" id="employee" /> Employee

                    <p id='advReg' class="error" style="max-height:3px;color: red;"></p>
               </div>
           </div> 
                            
           <div class="form-group">
               {!! Form::label('advRegName', 'Name', ['class' => 'col-sm-3 control-label']) !!}
               <div class="col-sm-1 control-label">: </div>
               <div class="col-sm-8">  
                   <select name="advRegName" class="form-control input-sm" id="advRegName">

                       <option value=""> At First Select Services Category</option>

                   </select>

                   <p id='advRegNamee' class="error" style="max-height:3px;color: red;"></p>
               </div>
           </div>

           <div class="form-group" id="advAmount1" style="display:none;">
              {!! Form::label('advAmount', 'Total Advance Amount', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-1 control-label">: </div>
              <div class="col-sm-8"> 
                  {!! Form::text('advAmount',null, ['class'=>'form-control', 'id' => 'advAmount','readonly']) !!}
              </div>
           </div>

          <div class="form-group" id="advAmount2" style="display:none;">
              {!! Form::label('payableAmount', 'Payable Amount', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-1 control-label">: </div>
              <div class="col-sm-8"> 
                  {!! Form::text('payableAmounte',null, ['class'=>'form-control', 'id' => 'payableAmount','readonly']) !!}
              </div>
          </div>

          <div class="form-group">
              <div class="col-sm-3">Receive Type</div>
              <div class="col-sm-1 control-label">: </div>
              <div class="col-sm-8">
                  <input type="radio" name="advReceiveChange" value="a" id="cash" />Cash
                  <input type="radio" name="advReceiveChange" value="b" id="vauchar" />Voucher
                  <input type="radio" name="advReceiveChange" value="c" id="bank" />Bank

                  <p id='advReceiveChangee' class="error" style="max-height:3px;color: red;"></p>
              </div>
          </div>


          <div class="form-group" id="cash2" style="display:none;">
              {!! Form::label('cash', 'Cash', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-1 control-label">: </div>
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
              {!! Form::label('vauchar', 'Vauchar Id', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-1 control-label">: </div>
              <div class="col-sm-8">                                    
                  {!! Form::text('vauchar', null, ['class'=>'form-control', 'id' => 'vauchar']) !!}
                   <p id='vauchare' class="error" style="max-height:3px;color: red;"></p>

               </div>
          </div> 

          <div class="form-group" id="bank2" style="display:none;">
              {!! Form::label('bank', 'Bank Name', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-1 control-label">: </div>
              <div class="col-sm-8"> 
                  @php          
                      $bank = DB::table('acc_account_ledger')->select('id','name')->where('accountTypeId',5)->get();
                  @endphp                                                             
                  <select name="bank" class="form-control input-sm" id="bank">
                      <option value=""> At First Select Bank Name</option>
                      @foreach($bank as $bank)
                         <option value="{{$bank->id}}">{{$bank->name}}</option>
                      @endforeach       
                  </select>
                  <p id='banke' class="error" style="max-height:3px;color: red;"></p>
              </div>
          </div>
          <div class="form-group">
              {!! Form::label('advReceiveAmount', 'Amount', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-1 control-label">: </div>
              <div class="col-sm-8">                                    
                  {!! Form::text('advReceiveAmount',null, ['class'=>'form-control', 'id' => 'advReceiveAmount','autocomplete'=>'off']) !!}
                  <p id='advReceiveAmounte' class="error" style="max-height:3px;color: red;"></p>
              </div>
          </div> 

          <div class="form-group">
              {!! Form::label('paymentDate', 'Receive Date', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-1 control-label">: </div>
              <div class="col-sm-8"> 
                  {!! Form::text('paymentDate', null,['class'=>'form-control','id' => 'paymentDate','readonly','style'=>'cursor:pointer']) !!}

                  <p id='paymentDatee' class="error" style="max-height:3px;color: red;"></p>
              </div>
          </div>

          <div class="form-group">
              <div class="col-sm-12 text-right" style="padding-right: 30px;">
                  {!! Form::submit('Submit', ['id' => 'save', 'class' => 'btn btn-info','type'=>'button']) !!}
                  <a href="{{url('viewAdvanceReceivelist/')}}" class="btn btn-danger closeBtn">Close</a>
              </div>
          </div>

     {!! Form::close() !!}

   </div>
   <div class="col-md-4 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="80%" height="" style="float:right">
  </div>
                
         </div>
        </div>
       </div>
      <div class="footerTitle" style="border-top:1px solid white"></div>
    </div>
  <div class="col-md-2"></div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
       $('#advReceiveAmount').on('input', function(event) {
          this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
       });
       $('input[name="advRegChange"]').change(function() {
           var advRegChange = $(this).val();
           var houseOwner=$("#houseOwner").val();
           var csrf = "{{csrf_token()}}";
           $.ajax({
              type: 'post',
              url: './advanceReceiveChange',
              data:{advRegChange:advRegChange,_token: csrf},
              dataType: 'json',
              success: function(response) {
                $("#advRegName").empty();
                $("#advRegName").append('<option selected="selected" value="">Select Name</option>');
                $.each(response, function(key,ob) {
                   if(advRegChange==1) {
                      $("#advRegName").append("<option value='"+ob.id+"'>"+ob.houseOwnerName+"</option>");
                   }
                   else if(advRegChange==2) {
                      $("#advRegName").append("<option value='"+ob.id+"'>"+ob.supplierCompanyName+"</option>");
                   }
                   else if(advRegChange==3) {
                      $("#advRegName").append("<option value='"+ob.id+"'>"+ob.emp_id+"-"+ob.emp_name_english+"</option>");
                   }
                });
              },
              error: function(_response) {
                alert("error");
              }
         }); /*end Ajax*/  
  });

       $('input[name="advReceiveChange"]').change(function() {
          var receiveType = $("input[name='advReceiveChange']:checked").val();
          if(receiveType=='a'){
             $("#cash2").show();
             $("#vauchar2").hide();
             $("#bank2").hide();
          }
          else if(receiveType=='b'){
            $("#vauchar2").show();
            $("#bank2").hide();
            $("#cash2").hide();
         } 
         else if(receiveType=='c'){
           $("#bank2").show();
           $("#vauchar2").hide();
           $("#cash2").hide();
         } 
       });
    });
        
</script>

    <!--    - - - -Advance Amount Change  - - -->

<script type="text/javascript">

$(document).ready(function() {
   $("#advRegName").on("change", function() {
       var project = $("#project").val();
       var projectType = $("#projectType").val();
       var advRegType = $("#advRegType").val();
       var advRegChange = $('input[name=advRegChange]:checked').val();
       var advRegName = $("#advRegName").val();
       var csrf = "{{csrf_token()}}";
          $.ajax({
            type: 'post',
            url: './advanceReceiveAmountChange',
            data:{advRegChange:advRegChange,advRegName:advRegName,project:project,projectType:projectType,advRegType:advRegType,_token: csrf},
            dataType: 'json',
            async:false,
            success: function(data) {
              $("#advAmount").val(data['amount']); 
              $("#payableAmount").val(data['paidAmount']);
              $("#advReceiveAmount").attr('paidAmount',data['amount']);
              if(data['amount']>0){
                $("#advAmount1").show();
              }
              else{
                $("#advAmount1").hide(); 
              }
              if(data['paidAmount']>0){
                $("#advAmount2").show();
              }
              else{
                $("#advAmount2").hide(); 
              }
              $('#advReceiveAmount').keyup(function(){
                if ($(this).val() >data['paidAmount']){
                   alert("No numbers above "+data['paidAmount']);
                   $(this).val(data['paidAmount']);
                 }
              });
            },
            error: function(_response) {
              alert("error");
            }
       });
    })

});        
</script>

<script type="text/javascript">
   $(document).ready(function() {
     $(function() {
        $('#paymentDate').datepicker({
          maxDate: "dateToday",
          dateFormat: 'dd-mm-yy',
          onSelect: function() {
            $(this).closest('div').find('.error').remove();
          }
        });
    })
  });


</script>

<script type="text/javascript">
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
            })

           .done(function(data) {
              if(data.errors){
                $("#save").prop("disabled", false);
                if (data.errors['advRegType']) {
                    $("#advRegTypee").empty();
                    $("#advRegTypee").append('*'+data.errors['advRegType']);
                } 
                if (data.errors['advRegName']) { 
                   $("#advRegNamee").empty();
                   $("#advRegNamee").append('*'+data.errors['advRegName']);
                } 
                if (data.errors['advReceiveChange']) {
                   $("#advReceiveChangee").empty();
                   $("#advReceiveChangee").append('*'+data.errors['advReceiveChange']);
                }
                if (data.errors['project']) { 
                   $("#projecte").empty();
                   $("#projecte").append('*'+data.errors['project']);
                } 
                if (data.errors['projectType']) { 
                   $("#projectTypee").empty();
                   $("#projectTypee").append('*'+data.errors['projectType']);
                }
                if (data.errors['cachfield']) { 
                   $("#cashOn").empty();
                   $("#cashOn").append('*'+data.errors['cachfield']);
                }
                if (data.errors['vauchar']) { 
                   $("#vauchare").empty();
                   $("#vauchare").append('*'+data.errors['vauchar']);
                }
                if (data.errors['bank']) { 
                   $("#banke").empty();
                   $("#banke").append('*'+data.errors['bank']);
                }
                if (data.errors['advReceiveAmount']) { 
                   $("#advReceiveAmounte").empty();
                   $("#advReceiveAmounte").append('*'+data.errors['advReceiveAmount']);
                } 
                if (data.errors['paymentDate']) {
                   $("#paymentDatee").empty();
                   $("#paymentDatee").append('*'+data.errors['paymentDate']);
                } 
              }
             else {
                  location.href = 'viewAdvanceReceivelist';
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
 
 </script>

<script type="text/javascript">
    $(document).ready(function(){ 
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
     });
</script>


@endsection




