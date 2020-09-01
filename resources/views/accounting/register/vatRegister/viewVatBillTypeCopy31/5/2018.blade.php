@extends('layouts/acc_layout')
@section('title', '| Bill Type')
@section('content')
<?php use Carbon\Carbon; ?>


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('accVatBillType/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>ADD BILL</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">BILL TYPE LIST</font></h1>
        </div>

        <div class="panel-body panelBodyView">

        <div>
          <script type="text/javascript">
          /*jQuery(document).ready(function($)
          {
            /*$("#otsTable").dataTable().yadcf([

            ]);*//*
            $("#otsTable").dataTable({


                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });


       });
*/
          </script>
        </div>
          <table class="table table-striped table-bordered" id="otsTable" style="color: black;">
                    <thead>
                      <tr>
                        <th style="width:50px;">SL#</th>
                        <th rowspan="2">Name</th>
                        <th rowspan="2">Code</th>

                        <th rowspan="2">VAT Rate (%)</th>

                        <th rowspan="2">TAX Rate (%)</th>

                        <th rowspan="2">Active From</th>
                        <th rowspan="2">Status</th>
                        <th rowspan="2">Action</th>
                      </tr>


                    </thead>
                    <tbody>
                        @php $count=1; @endphp
                         @foreach($viewVatBillTypes as $viewVatBillType)
                      <tr>
                        <td>{{$count}}</td>
                        <td>{{$viewVatBillType->serviceName}}</td>
                        <td>{{$viewVatBillType->serviceCode}}</td>
                        <td >{{$viewVatBillType->vatRate}}%</td>

                            <td >{{$viewVatBillType->taxRate}}%</td>

                        <td >{{Carbon::parse($viewVatBillType->activeFrom)->format('d-m-Y')}}</td>
                       <td>
                         @if($viewVatBillType->status==0)
                            <span><i class="fa fa-times" aria-hidden="true" style="color:red;font-size: 1.3em;"></i></span>
                         @else
                            <span><i class="fa fa-check" aria-hidden="true" style="color:green;font-size: 1.3em;"></i></span>
                         @endif
                      </td>
                      <td>

                    <a href="javascript:;" class="view-modal" vatBillTypeId="{{$viewVatBillType->id}}">
                      <i class="fa fa-eye" aria-hidden="true"></i>
                    </a>&nbsp;

                          <a href="javascript:;" class="edit-modal" vatBillTypeIdEditModal="{{$viewVatBillType->id}}">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;



                      </td>
                     </tr>
                     @php $count++; @endphp
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
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Bill Type Details</h4>
                    </div>
                    <div class="modal-body">

                        <div id="printingContent">
                        <div class="row" style="padding-bottom: 20px;">


                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                        <div class="form-group">
                                            {!! Form::label('billName', 'Bill Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('billName', null,['id'=>'VMbillName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('activeFrom', 'Active From:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('activeFrom', null,['id'=>'VMactiveFrom','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('billCode', 'Code:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('billCode', null,['id'=>'VMbillCode','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('vatRate', 'VAT Rate:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('vatRate', null,['id'=>'VMvatRate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                  </div>{{--form-horizontal form-groups--}}
                              </div>{{--End 1st col-md-6--}}

                                <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">


                                      <div class="form-group">
                                          {!! Form::label('taxRate', 'TAX Rate:', ['class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">
                                              {!! Form::text('taxRate', null,['id'=>'VMtaxRate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                          </div>
                                      </div>

                                      <div class="form-group">
                                          {!! Form::label('status', 'Status:', ['class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">

                                              {!! Form::text('status', null,['id'=>'VMstatus','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
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
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update Bill Type</h4>
                    </div>
                    <div class="modal-body">
                      <div class="row" style="padding-bottom: 20px;">
                     {!! Form::open(array('url' => './accViewVatBillTypeModalUpdate','id'=>'updateForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                          {!! Form::hidden('supplierId',null,['id'=>'EMSupplierId']) !!}
                          <div class="col-md-12">
                              <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                  <div class="form-horizontal form-groups">

                                      <div class="form-group">
                                          {!! Form::label('billName', 'Bill Name:', ['class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">
                                              {!! Form::text('billName', null,['id'=>'EMbillName','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                          </div>
                                      </div>

                                      <div class="form-group">
                                          {!! Form::label('billCode', 'Code:', ['class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">
                                              {!! Form::text('billCode', null,['id'=>'EMbillCode','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          {!! Form::label('activeFrom', 'Active From:', ['class' => 'col-sm-4 control-label']) !!}
                                          <div class="col-sm-8">
                                              {!! Form::text('activeFrom', null,['id'=>'EMactiveFrom','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                          </div>
                                      </div>


                                  </div>{{--form-horizontal form-groups--}}
                              </div>{{--End 1st col-md-6--}}

                              <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                  <div class="form-horizontal form-groups">

                                    <div class="form-group">
                                        {!! Form::label('vatRate', 'VAT Rate:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('vatRate', null,['id'=>'EMvatRate','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('taxRate', 'TAX Rate:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('taxRate', null,['id'=>'EMtaxRate','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
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
            {{-- {!! Form::open(['url' => '']) !!} --}}
            <input id="DMdeleteId" type="hidden" name="accId" value="">
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
   <h5 style="text-align: center;font-size: 14;padding: 0px;margin: 0px;">BiLL Type Details</h5>
   <div>
    <p style="font-size: 10px;text-align: right;"><span style="font-weight: bold;">Print Date:</span> {{date("d-m-Y h:i:sa")}}</p>
   </div>
   <br>
</div>




<script type="text/javascript">
  $(document).ready(function() {
       var minDate1="";
       var UpdateSubmitId="";
       var deleteID="";
        function toDate(dateStr) {
            var parts = dateStr.split("-");
            return new Date(parts[2], parts[1] - 1, parts[0]);
         }
         $("#EMactiveFrom").datepicker({
             changeMonth: true,
             changeYear: true,
             minDate   : new Date('2017'),

             dateFormat: 'dd-mm-yy',
             onSelect: function () {
               $('#billDatePara').hide();
               $("#billDate").datepicker("option","minDate",new Date(toDate($(this).val())));
               $( "#billDate" ).datepicker( "option", "disabled", false );
             }
          });

    /*View Modal*/

    $(document).on('click', '.view-modal', function() {


     if(hasAccess('accViewVatBillTypeModal')){
      var vatBillTypeId = $(this).attr('vatBillTypeId');
      var csrf = "{{csrf_token()}}";
      $.ajax({
        url: './accViewVatBillTypeModal',
        type: 'POST',
        dataType: 'json',
        data: {vatBillTypeId: vatBillTypeId, _token: csrf},
        success: function(data) {
            $("#VMbillName").val(data['viewVatBillTypes'].serviceName);
            $("#VMbillCode").val(data['viewVatBillTypes'].serviceCode);
            $("#VMvatRate").val(data['viewVatBillTypes'].vatRate+"%");


            $("#VMtaxRate").val(data['viewVatBillTypes'].taxRate+"%");
            var status=data['viewVatBillTypes'].status;
            if(status ==1)
            {
            $("#VMstatus").val("Active");
          }
          else{
            $("#VMstatus").val("Closed");
          }

          $("#VMactiveFrom").val(data['viewVatBillTypes'].activeFromGeneral);

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



    /*Edit Modal*/

    $(document).on('click', '.edit-modal', function() {

      var vatBillTypeId = $(this).attr('vatBillTypeIdEditModal');

      var csrf = "{{csrf_token()}}";
      $.ajax({
        url: './accViewVatBillTypeModal',
        type: 'POST',
        dataType: 'json',
        data: {vatBillTypeId: vatBillTypeId, _token: csrf},
        success: function(data) {
           //minDate=data['viewVatBillTypes'].activeFromMin;
           $("#EMSupplierId").val(data['viewVatBillTypes'].id);
          $("#EMbillName").val(data['viewVatBillTypes'].serviceName);
          $("#EMbillCode").val(data['viewVatBillTypes'].serviceCode);
          $("#EMvatRate").val(data['viewVatBillTypes'].vatRate);
          $("#EMtaxRate").val(data['viewVatBillTypes'].taxRate);
          $("#EMactiveFrom").val(data['viewVatBillTypes'].activeFrom);
        //minDate1=GetFormattedDate(data['viewVatBillTypes'].activeFromMin);
        //activedate=data['viewVatBillTypes'].activeFromMin;
        //console.log(activedate);
        //$("#EMactiveFrom").datepicker('option','minDate',new Date());


          //var tempDate=$("#EMactiveFrom").val(data['viewVatBillTypes'].activeFromMin)
          //;
          // alert(data['viewVatBillTypes'].activeFromMin);
          // minDate1=$.datepicker.parseDate('dd/mm/yyyy', tempDate);
          //$("#EMactiveFrom").datepicker('option','minDate',new Date(data['viewVatBillTypes'].activeFromMin));

           //alert(new Date(data['viewVatBillTypes'].activeFromMin));
           // alert();



         $("#editModal").modal('show');

        },
        error: function(argument) {
          alert('response error');
        }
      });


    });
    /*End Edit Modal*/

    /*Print Document*/
    $("#printButton").click(function() {

  /*Make Print Table*/
        tableMarkup = "<table id='printingTable' style='color: black;font-size:11px;border-collapse: collapse;margin-bottom:100px;' border='1px solid black;'>"+
  "   <tbody>"+
  "       <tr>"+
  "           <td width='25%'>Bill Name:</td>"+
  "           <td width='25%'>"+$('#VMbillName').val()+"</td>"+
  "           <td width='25%'>VAT Rate:</td>"+
  "           <td width='25%'>"+$('#VMvatRate').val()+"</td>"+
  "       </tr>"+


  "       <tr>"+
  "           <td width='25%'>Code:</td>"+
  "           <td width='25%'>"+$('#VMbillCode').val()+"</td>"+
  "           <td width='25%'>Active From:</td>"+
  "           <td width='25%'>"+$('#VMactiveFrom').val()+"</td>"+
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
  $("#hiddenTitle").hide();

    });
    /*End Print Document*/














    /*Submit the form*/
    $("form").submit(function(event) {

       event.preventDefault();
        var dataDate=  $("#EMactiveFrom").val();
         console.log(dataDate);

       $.ajax({
            type: 'post',
            url: './accViewVatBillTypeModalUpdate',
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
