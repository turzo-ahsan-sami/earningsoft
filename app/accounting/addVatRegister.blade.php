<!--***********************************************
*  Programmer: Himel Dey                          *
*  Ambala IT                                      *
*  Topic: VAT Register                            *
*  Date: 5/20/2018 Time: 10:31 AM                 *
***********************************************!-->
@extends('layouts/acc_layout')
@section('title', '| OTS Register')
@section('content')

<div class="row add-data-form">
<div class="col-md-1"></div>
<div class="col-md-10 fullbody">
<div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="./accViewVatRegister" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>VAT Register List</a>
</div>

<div class="panel panel-default panel-border">
	<div class="panel-heading">
	    <div class="panel-title">VAT Register</div>
	</div>


<div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                    <div class="col-md-6">
                         <div class="form-group">
                              {!! Form::label('supplier', 'Supplier:', ['class' => 'col-sm-3 control-label']) !!}
                              <div class="col-sm-9">
                                  <select id="supplier" name="supplier" class="form-control" required>
                                      <option value="">Select Supplier</option>
                                      @foreach($supplierLists as $supplierList)
                                         <option value="{{$supplierList->id}}" >{{$supplierList->name}}</option>
                                      @endforeach
                                    </select>
                                    <p id='supplierPara' style="max-height:3px;"></p>
                              </div>
                         </div>
                         <div class="form-group">
                              {!! Form::label('projects', 'Projects:', ['class' => 'col-sm-3 control-label']) !!}
                              <div class="col-sm-9">
                                  <select id="projects" name="projects" class="form-control" required>
                                      <option value="">--Select Project--</option>
                                      @foreach($projects as $project)
                                         <option value="{{$project->id}}" >{{$project->name}}</option>
                                      @endforeach
                                    </select>
                                    <p id='projectsPara' style="max-height:3px;"></p>
                              </div>
                         </div>
                         <div class="form-group">
                              {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-3 control-label']) !!}
                              <div class="col-sm-9">
                                  <select id="projectType" name="projectType" class="form-control" required>
                                      <option value="">--Select Project Type--</option>
                                  </select>
                                    <p id='projectTypePara' style="max-height:3px;"></p>
                              </div>
                         </div>

                         <div class="form-group">
                                {!! Form::label('billDate', 'Bill Date:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('billDate', null, ['class'=>'form-control', 'id' => 'billDate','readonly','style'=>'cursor:pointer','required']) !!}
                                    <p id='billDatePara' style="max-height:3px;"></p>
                                </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('billNo', 'Bill No:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('billNo', null, ['class'=>'form-control', 'id' => 'billNo','required']) !!}
                                <p id='billNoPara' style="max-height:3px;"></p>
                            </div>
                       </div>

               </div>

                <div class="col-md-6">
                  <div class="form-group">
                        {!! Form::label('billType', 'Bill Type:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                            <select id="billType" name="billType" class="form-control" required>
                               <option value="">--Select Bill Type--</option>
                              @foreach($billTypes as $billType)
                                 <option value="{{$billType->id}}" >{{$billType->serviceName}}</option>
                              @endforeach


                            </select>
                            <p id='billTypePara' style="max-height:3px;"></p>
                        </div>
                  </div>
                  <div class="form-group">
                          {!! Form::label('voucherDate', 'Voucher Date:', ['class' => 'col-sm-3 control-label']) !!}
                          <div class="col-sm-9">

                         {!! Form::text('voucherDate', null, ['class'=>'form-control', 'id' => 'voucherDate','readonly','style'=>'cursor:pointer','required']) !!}
                              <p id='voucherDatePara' style="max-height:3px;"></p>
                          </div>
                  </div>
                   <div class="form-group">
                          {!! Form::label('voucherNo', 'Voucher No:', ['class' => 'col-sm-3 control-label']) !!}
                          <div class="col-sm-9">

                          {!! Form::text('voucherNo', null, ['class'=>'form-control', 'id' => 'voucherNo','required']) !!}
                              <p id='voucherNoPara' style="max-height:3px;"></p>
                          </div>
                  </div>
                  <div class="form-group">
                     {!! Form::label('billAmount', 'Bill Amount:', ['class' => 'col-sm-3 control-label']) !!}
                     <div class="col-sm-9">
                         {!! Form::number('billAmount', '', ['class'=>'form-control', 'id' => 'billAmount','required' ]) !!}
                         <p id='billAmountPara' style="max-height:3px;"></p>
                     </div>
                 </div>
                 <div class="form-group">
                      {!! Form::label('vat', 'VAT:', ['class' => 'col-sm-3 control-label']) !!}
                      <div class="col-sm-4">
                         {!! Form::text('vat', '', ['class'=>'form-control', 'id' => 'vat','readonly','required']) !!}
                            <p id='vatPara' style="max-height:3px;"></p>
                      </div>
                      <div class="col-sm-5">
                         {!! Form::text('total', '', ['class'=>'form-control', 'id' => 'total','readonly','required']) !!}

                          <p id='totalPara' style="max-height:3px;"></p>
                     </div>


                 </div>
                </div>
                 <div class="form-group">
                        <div class="col-sm-12 text-right">
                            {!! Form::submit('Submit', ['id' => 'save', 'class' => 'btn btn-info','type'=>'button']) !!}
                            <a href="./accViewVatRegister" class="btn btn-danger closeBtn">Close</a>
                            <input type="hidden" value="{{ csrf_token() }}" name="_token">
                        </div>
                </div>

            {!! Form::close() !!}

            </div>
        </div>
     </div>
</div>
<div class="footerTitle" style="border-top:1px solid white"></div>
</div>
<div class="col-md-1"></div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    var suffix="";
    var prefix="";
    var midfix="";
    var completeNo="";

    function toDate(dateStr) {
        var parts = dateStr.split("-");
        return new Date(parts[2], parts[1] - 1, parts[0]);
     }

    $("#billDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "2017:c",
        minDate: new Date(2017, 07 - 1, 01),
        maxDate: "dateToday",
        dateFormat: 'dd-mm-yy',
        onSelect: function () {
          $('#billDatePara').hide();
          $("#billDate").datepicker("option","minDate",new Date(toDate($(this).val())));
          $( "#billDate" ).datepicker( "option", "disabled", false );
        }
     });

     $("#voucherDate").datepicker({
         changeMonth: true,
         changeYear: true,
         yearRange : "2017:c",
         minDate: new Date(2017, 07 - 1, 01),
         maxDate: "dateToday",
         dateFormat: 'dd-mm-yy',
         onSelect: function () {
           $('#voucherDatePara').hide();
           $("#voucherDate").datepicker("option","minDate",new Date(toDate($(this).val())));
           $( "#voucherDate" ).datepicker( "option", "disabled", false );
         }
      });


      $("#billType").click(function(event){
         var billType=$('#billType').val();
         var billAmount=$('#billAmount').val();

        $.ajax({
             type: 'post',
             url: "./vatCalculationFromBillType",
             data: {billType:billType},
               success: function (data){
                 $.each(data, function( key,obj){
                       $('#vat').val(obj.vatRate);
                       var vat=obj.vatRate;
                       var total= Math.round(billAmount*(vat/100));
                       $('#total').val(total);

                 });
               },
               error:  function (data){

               }
        });
      });



      $("#billAmount").change(function(event){

            var vat= $('#vat').val();
            var billAmount=$('#billAmount').val();
            var total= Math.round(billAmount*(vat/100));

            $('#total').val(total);

      });


      $("#projects").click(function(event){
         var projectId=$('#projects').val();

           $.ajax({
             type: 'post',
             url: "./accVatProjectTypeFiltering",
             data: {projectId:projectId},
               success: function (data){
                $("#projectType").empty();
                 $('#projectType').append("<option value=''>--Select Project Type--</option>");
                 $.each(data, function( key,obj){
                         if(obj.projectCode < 10)
                         {

                         prefix="0"+obj.projectCode;
                       }
                         else{
                           prefix=obj.projectCode;
                         }
                       completeNo=prefix+"-"+midfix+"-"+suffix;
                         $("#billNo").val(completeNo);


                       $('#projectType').append("<option value='"+obj.id+"'>"+obj.name+"</option>");

                 });


               },
               error:  function (data){

               }
        });
      });



      $("#supplier").click(function(event){
         var supplier=$('#supplier').val();


        $.ajax({
             type: 'post',
             url: "./accVatRegisterBillNoGenerate",
             data: {supplier:supplier},
               success: function (data){
                     suffix=data;
                     completeNo=prefix+"-"+midfix+"-"+suffix;
                      $("#billNo").val(completeNo);
               },
               error:  function (data){

               }
        });
      });


      $("#projectType").click(function(event){
         var projectType=$('#projectType').val();


        $.ajax({
             type: 'post',
             url: "./accVatRegisterBillNoGenerateProjectType",
             data: {projectType:projectType},
               success: function (data){
                 console.log(data);
                    midfix=data;
                  completeNo=prefix+"-"+midfix+"-"+suffix;
                    $("#billNo").val(completeNo);
               },
               error:  function (data){

               }
        });
      });



      /*Submit the form*/

      $("form").submit(function(event) {

          event.preventDefault();

          $.ajax({
               type: 'post',
               url: './accAddVatregister',
               data: $('form').serialize(),
               dataType: 'json',
              success: function( _response ){


                   alert('success');
                   suffix="";
                   prefix="";
                   midfix="";
                   completeNo="";
                 $('#entryForm')[0].reset();


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
</script>

@endsection
