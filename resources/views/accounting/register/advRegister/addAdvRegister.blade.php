@extends('layouts/acc_layout')
@section('title', '| Advance Register')
@section('content')

        @php

          use App\accounting\AccAdvRegister;

        @endphp


<div class="row add-data-form">
  <div class="col-md-2"></div>
   <div class="col-md-8 fullbody">
     <div class="viewTitle" style="border-bottom: 1px solid white;">
        <a href="{{url('viewAdvRegisterList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>ADVANCE REGISTER LIST</a>
      </div>
      <div class="panel panel-default panel-border">
        <div class="panel-heading">
          <div class="panel-title">Advance Register</div>
        </div>

   <div class="panel-body">
      <div class="row"> 
         <div class="col-md-8">

             {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                         
                         <div class="form-group">
                              {!! Form::label('advRegId', 'Advance Reg Id', ['class' => 'col-sm-3 control-label']) !!}
                              <div class="col-sm-1 control-label">: </div>
                              <div class="col-sm-8"> 
                                  {!! Form::text('advRegId',$advRegNumber, ['class'=>'form-control', 'id' => 'advRegId','readonly']) !!}
                              </div>
                         </div>

                            <div class="form-group">
                                   {!! Form::label('project', 'Project', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-1 control-label">: </div>
                                    <div class="col-sm-8"> 

                                @php
                                      $projects = DB::table('gnr_project')->select('projectCode','name','id')->groupBy('projectCode')->get();
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
                                            <option>Slect Project Type</option>
                                        @foreach( $projectTypes as  $projectType)
                                            <option  value="{{$projectType->id}}">{{$projectType->projectTypeCode.'-'.$projectType->name}}</option>
                                        @endforeach

                                        </select>

                                        <p id='projectTypee' class="error" style="max-height:3px;color: red;"></p>

                                    </div>

                            </div>


                             <div class="form-group">

                                    {!! Form::label('advRegType', 'Register Type', ['class' => 'col-sm-3 control-label']) !!}
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

                          <div class="form-group">
                            <div class="col-sm-3">Payment Type</div>
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8">
                                <input type="radio" name="paymentTypeChange" value="cash" id="paymentTypeChange" />Cash
                                <input type="radio" name="paymentTypeChange" value="bank" id="paymentTypeChange" />Bank
                                <p id='paymentTypeChangee' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                          </div> 
                          <div class="form-group">
                               {!! Form::label('changePaymentType', 'Name', ['class' => 'col-sm-3 control-label']) !!}
                               <div class="col-sm-1 control-label">: </div>
                               <div class="col-sm-8">  
                                   <select name="changePaymentType" class="form-control input-sm" id="changePaymentType">
                                      <option value=""> At First Select Payment Type</option>
                                    </select>
                                   <p id='changePaymentTypee' class="error" style="max-height:3px;color: red;"></p>
                               </div>
                          </div>

                          <div class="form-group">
                              {!! Form::label('advRegAmount', 'Amount', ['class' => 'col-sm-3 control-label']) !!}
                               <div class="col-sm-1 control-label">: </div>
                               <div class="col-sm-8">                                    
                                    {!! Form::text('advRegAmount', null, ['class'=>'form-control', 'id' => 'advRegAmount']) !!}
                                     <p id='advRegAmounte' class="error" style="max-height:3px;color: red;"></p>
                               </div>
                          </div> 


                            <div class="form-group">
                                 {!! Form::label('paymentDate', 'Payment Date', ['class' => 'col-sm-3 control-label']) !!}
                                 <div class="col-sm-1 control-label">: </div>
                                 <div class="col-sm-8">  
                                    {!! Form::text('paymentDate', null, ['class'=>'form-control','id' => 'paymentDate','readonly','style'=>'cursor:pointer']) !!}

                                    <p id='paymentDatee' class="error" style="max-height:3px;color: red;"></p>
                                 </div>

                            </div>


                            <div class="form-group">
                              <div class="col-sm-12 text-right" style="padding-right: 30px;">
                                 {!! Form::submit('Submit', ['id' => 'save', 'class' => 'btn btn-info','type'=>'button']) !!}
                                 <a href="{{url('viewAdvRegisterList/')}}" class="btn btn-danger closeBtn">Close</a>
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
     

            $('input:radio[name=advRegChange]').change(function(){
                   
                var advRegChange = $(this).val();
                var houseOwner=$("#houseOwner").val();
                var csrf = "{{csrf_token()}}";
    
                $.ajax({
                            type: 'post',
                            url: './advanceRegisterChange',
                            data:{advRegChange:advRegChange,_token: csrf},
                            dataType: 'json',
                            success: function(response){


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
            });          

        });
        
    });
        
</script>

<script type="text/javascript">

    $(document).ready(function() {
       $('input:radio[name=paymentTypeChange]').change(function(){
                   
                var paymentTypeChange = $(this).val();
                var csrf = "{{csrf_token()}}";
               
               
                    $.ajax({
                            type: 'post',
                            url: './paymentTypeChange',
                            data:{paymentTypeChange:paymentTypeChange,_token: csrf},
                            dataType: 'json',
                            success: function(response) {


                 $("#changePaymentType").empty();
                 $("#changePaymentType").append('<option selected="selected" value="">Select Name</option>');

                 $.each(response, function(key,ob) {
                  if(paymentTypeChange=='cash') {

                 $("#changePaymentType").append("<option value='"+ob.id+"'>"+ob.name+"</option>");
                 }

                  else if(paymentTypeChange=='bank') {

                 $("#changePaymentType").append("<option value='"+ob.id+"'>"+ob.name+"</option>");

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
  

   $(document).ready(function() {
        $(function() {


  $('#paymentDate').datepicker({
             maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function(){
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
                     url: './storeAdvanceReg',
                     type: 'POST',
                     dataType: 'json',
                     data: $('form').serialize(),

                    })
                .done(function(data) {
               
                 if(data.errors)  {
                  $("#save").prop("disabled", false);
                    if (data.errors['advRegType']) {
                         $("#advRegTypee").empty();
                         $("#advRegTypee").append('*'+data.errors['advRegType']);
                     } 

                     if (data.errors['advRegName']) { 
                        $("#advRegNamee").empty();
                        $("#advRegNamee").append('*'+data.errors['advRegName']);
                    } 
                    if (data.errors['project']) { 
                        $("#projecte").empty();
                        $("#projecte").append('*'+data.errors['project']);
                    } 

                    if (data.errors['projectType']) { 
                        $("#projectTypee").empty();
                        $("#projectTypee").append('*'+data.errors['projectType']);
                    }
                    if (data.errors['paymentTypeChange']) { 
                        $("#paymentTypeChangee").empty();
                        $("#paymentTypeChangee").append('*'+data.errors['paymentTypeChange']);
                    } 
                    if (data.errors['changePaymentType']) { 
                        $("#changePaymentTypee").empty();
                        $("#changePaymentTypee").append('*'+data.errors['changePaymentType']);
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
                        
                        location.href = 'viewAdvRegisterList';
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

   $("#project").change(function() {
            
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
                error: function(_response){
                    alert("error");
                }

           });/*End Ajax*/

        });/*End Change Project*/
           
});
</script>



@endsection




