<!--***********************************************
*  Programmer: Himel Dey                          *
*  Ambala IT                                      *
*  Topic: VAT Register                            *
*  Date: 5/20/2018 Time: 10:31 AM                 *
***********************************************!-->
@extends('layouts/acc_layout')
@section('title', '| BILL Register')
@section('content')

<div class="row add-data-form">
<div class="col-md-1"></div>
<div class="col-md-10 fullbody">
<div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="./accViewVatBillType" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>Bill Type List</a>
</div>

<div class="panel panel-default panel-border">
	<div class="panel-heading">
	    <div class="panel-title">Bill Type</div>

	</div>


<div class="panel-body">
            <div class="row">
              <div class="col-sm-1" style="padding-left:25px;">
                  <select id="type" name="type" class="form-control" required>
                      <option value="">Type</option>
                      <option value="0">VAT</option>
                      <option value="1">TAX</option>
                    </select>
              </div>

                <div class="col-md-12">

                {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                    <div class="col-md-6">
                         <div class="form-group">
                              {!! Form::label('billTypeName', 'Bill Type Name:', ['class' => 'col-sm-3 control-label']) !!}
                              <div class="col-sm-9">
                                    {!! Form::text('billTypeName', null, ['class'=>'form-control', 'id' => 'billName','required']) !!}
                                    <p id='billTypeNamePara' style="max-height:3px;"></p>
                              </div>
                         </div>

                   <div class="form-group">
                       {!! Form::label('vatTaxCode', 'Code:', ['class' => 'col-sm-3 control-label','id'=>'LvatTaxCode']) !!}
                       <div class="col-sm-9">
                           {!! Form::text('vatTaxCode', null, ['class'=>'form-control', 'id' => 'vatTaxCode']) !!}
                           <p id='vatCodePara' style="max-height:3px;"></p>
                       </div>
                  </div>
                   <div class="form-group">
                       {!! Form::label('taxRate', 'TAX Rate:', ['class' => 'col-sm-3 control-label','id'=>'LtaxRate']) !!}
                       <div class="col-sm-9">
                           {!! Form::number('taxRate', null, ['class'=>'form-control', 'id' => 'taxRate','min="0"']) !!}
                           <p id='vatRatePara' style="max-height:3px;"></p>
                       </div>
                  </div>



               </div>

                <div class="col-md-6">
                  <div class="form-group">
                          {!! Form::label('activationDate', 'Activation Date:', ['class' => 'col-sm-3 control-label']) !!}
                          <div class="col-sm-9">

                         {!! Form::text('activationDate', null, ['class'=>'form-control', 'id' => 'activationDate','readonly','style'=>'cursor:pointer','required']) !!}
                              <p id='activationDatePara' style="max-height:3px;"></p>
                          </div>
                  </div>


                <div class="form-group">
                    {!! Form::label('vatRate', 'VAT Rate:', ['class' => 'col-sm-3 control-label','id'=>'LvatRate']) !!}
                    <div class="col-sm-9">
                        {!! Form::number('vatRate', null, ['class'=>'form-control', 'id' => 'vatRate','min="0"']) !!}
                        <p id='vatRatePara' style="max-height:3px;"></p>
                    </div>
               </div>




                </div>
                 <div class="form-group">
                        <div class="col-sm-12 text-right">
                            {!! Form::submit('Submit', ['id' => 'save', 'class' => 'btn btn-info','type'=>'button']) !!}
                            <a href="./accViewVatBillType" class="btn btn-danger closeBtn">Close</a>
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
  $("#taxRate").hide();

  $("#LtaxRate").hide();

  $("#vatRate").hide();

  $("#LvatRate").hide();


 $("#type").click(function(event){
    var type=$("#type").val();
 if(type==1)
 {
   $("#taxRate").show();
   $("#taxCode").show();
   $("#LtaxRate").show();
   $("#LtaxCode").show();
   $("#vatRate").hide();

   $("#LvatRate").hide();


 }
 else if(type==0){
   $("#vatRate").show();
   $("#vatCode").show();
   $("#LvatRate").show();
   $("#LvatCode").show();
   $("#taxRate").hide();

   $("#LtaxRate").hide();

 }
});

    function toDate(dateStr) {
        var parts = dateStr.split("-");
        return new Date(parts[2], parts[1] - 1, parts[0]);
     }

    $("#activationDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "2017:c",
        minDate: new Date(2017, 07 - 1, 01),
        maxDate: "dateToday",
        dateFormat: 'dd-mm-yy',
        onSelect: function () {
          $('#activationDatePara').hide();
          $("#activationDate").datepicker("option","minDate",new Date(toDate($(this).val())));
          $( "#activationDate" ).datepicker( "option", "disabled", false );
        }
     });



     /*Submit the form*/

     $("form").submit(function(event) {

         event.preventDefault();

         $.ajax({
              type: 'post',
              url: './addVatBillType',
              data: $('form').serialize(),
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
</script>

@endsection
