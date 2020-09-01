@extends('layouts/acc_layout')
@section('title', '| OTS Register')
@section('content')


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addOtsPrincipalPayment/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Account Closing</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">OTS ACCOUNT CLOSING LIST</font></h1>
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
          <table  id="otsTable" style="color: black;" class="table table-striped table-bordered" style="color: black;font-size:11px;border-collapse: collapse;margin-bottom:50px;" border= "1px solid black;" cellpadding="0" cellspacing="0">
                    <thead>
                      <tr style="vertical-align: top;">
                        <th rowspan="2" width="30">SL#</th>
                        <th rowspan="2">Date</th>                      
                        <th rowspan="2">Account Name</th>
                        <th rowspan="2">Account ID</th>
                        <th rowspan="2">Branch</th>
                        <th rowspan="1" colspan="3">Payment & Deduction (Tk)</th>
                        <th rowspan="2" width="100">Net Payment</th>
                        <th rowspan="2">Action</th>
                      </tr>

                      <tr>
                        <th width="100">Principal Amount</th>                        
                        <th width="100">Interest Payment</th>                        
                        <th width="100">Closing Fee</th>                        
                        
                      </tr>
                      
                    </thead>
                    <tbody>
                     @foreach($payments as $index => $payment)                    
                     <tr>
                        <td>{{$index+1}}</td>
                        <td>{{date('d-m-Y',strtotime($payment->closingDate))}}</td>
                        <td class="name">{{$memberNames[$index]}}</td>
                        <td>{{$payment->accNo}}</td>
                        <td class="name">{{$branchNames[$index]}}</td>
                        
                        <td class="amount">{{number_format($principalAmounts[$index],2,'.',',')}}</td>
                        {{-- <td class="amount">{{number_format($interestDues[$index],2,'.',',')}}</td> --}}
                        <td class="amount">{{number_format($payment->dueAmount,2,'.',',')}}</td>
                        <td class="amount">{{number_format($payment->accClosingCharge,2,'.',',')}}</td>
                        <td class="amount">{{number_format($payment->amount,2,'.',',')}}</td>
                        <td width="80">

                        <a href="javascript:;" class="view-modal" paymentId="{{$payment->id}}" amount="{{number_format($payment->amount,2,'.',',')}}">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                          </a>&nbsp; 
                          {{-- <a href="javascript:;" class="edit-modal" paymentId="{{$payment->id}}" amount="{{number_format($payment->amount,2,'.',',')}}">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp; --}}

                        <a href="javascript:;" class="delete-modal" paymentId="{{$payment->id}}">
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

                        {!! Form::hidden('VMledgerId',null,['id'=>'VMledgerId']) !!}

                            <div class="col-md-12">
                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                        <div class="form-group">
                                            {!! Form::label('memberName', 'Account Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('memberName', null,['id'=>'VMmemberName','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('accNo', 'Account No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                                
                                                {!! Form::text('accNo', null,['id'=>'VMaccNo','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('branchLocation', 'Branch Location:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                               
                                                {!! Form::text('branchLocation', null,['id'=>'VMbranchLocation','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                       

                                         <div class="form-group">
                                            {!! Form::label('certificateNo', 'Certificate No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                               
                                                {!! Form::text('certificateNo', null,['id'=>'VMcertificateNo','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('openingDate', 'Opening Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('openingDate', null,['id'=>'VMopeningDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('closingDate', 'Closing Date:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('closingDate', null,['id'=>'VMclosingDate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            {!! Form::label('paymentNature', 'Payment Nature:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                                
                                                {!! Form::text('paymentNature', null,['id'=>'VMpaymentNature','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('status', 'Status:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">                                                
                                                {!! Form::text('status', null,['id'=>'VMstatus','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                     


                                    </div>{{--form-horizontal form-groups--}}
                                </div>{{--End 1st col-md-6--}}

                                <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">
                                       

                                        

                                        <div class="form-group">
                                            {!! Form::label('principalAmount', 'Principal Amount (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('principalAmount', null,['id'=>'VMprincipalAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('interestRate', 'Interest Rate (%):', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('interestRate', null,['id'=>'VMinterestRate','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            {!! Form::label('interestDue', 'Interest Payment (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('interestDue', null,['id'=>'VMinterestDue','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('closingCharge', 'Closing Fee (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('closingCharge', null,['id'=>'VMclosingCharge','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('paidAmount', 'Net Payment (Tk):', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('paidAmount', null,['id'=>'VMpaidAmount','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('paymentMode', 'Payment Mode:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('paymentMode', null,['id'=>'VMpaymentMode','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group" id="bankAccNumberDiv">
                                            {!! Form::label('bankAccNumber', 'Bank & Acc. No:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('bankAccNumber', null,['id'=>'VMbankAccNumber','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>
                                        <div class="form-group" id="chequeNumDiv">
                                            {!! Form::label('chequeNum', 'Cheque Number:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('chequeNum', null,['id'=>'VMchequeNum','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>{{--End 2nd col-md-6--}}
                            </div>

                        </div>{{--row--}}

                        {{-- View ModalFooter--}}
                        <div class="modal-footer">
                        <button id="printButton" class="btn actionBtn glyphicon glyphicon-print btn-success" type="button"><span> Print</span></button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span> Close</span></button>
                        </div>


                    </div> {{-- End View Modal Body--}}

                </div>
            </div>
        </div>
        {{-- End View Modal --}}






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
            {!! Form::open(['url' => 'deleteOtsAccountPrincipalPayment/']) !!}
            <input id="DMpaymentId" type="hidden" name="DMpaymentId" value="">
            <button  type="submit" class="btn btn-danger"><span id=""> Confirm</span></button>
            <button class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span> Close</span></button>
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
   <h5 style="text-align: center;font-size: 14;padding: 0px;margin: 0px;">OTS Account Closing Details</h5>
   <div>
    <p style="font-size: 10px;text-align: right;"><span style="font-weight: bold;">Print Date:</span> {{date("d-m-Y h:i:sa")}}</p>
   </div>
   <br>
</div> 





<script type="text/javascript">
  $(document).ready(function() {

    function num(argument){
      return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }


   /* function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

      function newDateFormate(argument) {
        var formattedDate = new Date(argument);
        var d = formattedDate.getDate();
        var m =  formattedDate.getMonth();
        m += 1;  // JavaScript months are 0-11
        var y = formattedDate.getFullYear();

        return (pad(d,2) + "-" + pad(m,2) + "-" + y);
      }*/

      function num(argument) {
          return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }


    /*View Modal*/    
    
    $(document).on('click', '.view-modal', function() {
        if(hasAccess('getOtsPrincipalPaymentInfo')){

      var paymentId = $(this).attr('paymentId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './getOtsPrincipalPaymentInfo',
        type: 'POST',
        dataType: 'json',
        data: {paymentId: paymentId, _token: csrf},
        success: function(data) {

          $("#VMmemberName").val(data['memberName']);
          $("#VMaccNo").val(data['accNo']);
          $("#VMbranchLocation").val(data['branchLocation']);
          $("#VMcertificateNo").val(data['certificateNo']);
          $("#VMopeningDate").val(data['openingDate']);
          $("#VMclosingDate").val(data['closingDate']);
          if (data['account'].status==1) {
            $("#VMstatus").val("Active");
          }
          else{
            $("#VMstatus").val("Closed");
          }
          

          $("#VMpaymentNature").val(data['accNature']);
          $("#VMprincipalAmount").val(data['principalAmount']);
          $("#VMinterestRate").val(num(data['account'].interestRate));
          $("#VMinterestDue").val(data['interestDue']);
          $("#VMclosingCharge").val(data['closingCharge']);
          $("#VMpaidAmount").val(data['paidAmount']);

          $("#VMledgerId").val(data['ledgerId']);

          if (data['ledgerId']==350) {
            $("#VMpaymentMode").val('Cash');
            $("#bankAccNumberDiv").hide();
            $("#chequeNumDiv").hide();
          }
          else{
            $("#VMpaymentMode").val("Bank");
            $("#bankAccNumberDiv").show();
            $("#chequeNumDiv").show();
            $("#VMbankAccNumber").val(data['bankAccNo']);
            $("#VMchequeNum").val(data['chequeNumber']);
          }

          
          

         $(".modal-dialog").css('width','70%');
         $("#viewModal").modal('show');




        },
        error: function(argument) {
          alert('response error');
        }
      });

      }
    });
    /*End View Modal*/







    /*Delete Modal*/    
    
      $(document).on('click', '.delete-modal', function() {
        if(hasAccess('deleteOtsAccountPrincipalPayment')){
      
      $("#DMpaymentId").val($(this).attr('paymentId'));
      $("#deleteModal").modal('show');
  }

      
    });
    /*End Delete Modal*/



        /*Validate Number Filed*/
        $(document).on('input','.EMpaymentAmount' ,function() {            
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        /*End Validate Number Filed*/





        /*Edit Amount in Edit Modal*/
        $(document).on('input', '.EMpaymentAmount', function() {
            
            var dueAmount = parseFloat($(this).closest('tr').find('.EMdueAmount').val());
            var paymentAmount = 0;
            if (this.value!="") {
                paymentAmount = parseFloat($(this).val());
            }


            if (paymentAmount>dueAmount) {
                alert('Payment Amount should be less than Due Amount!!');
                $(this).css('color', 'red');
                $(this).attr('hasError', '1');
            }
            else{
                $(this).css('color', 'black');
                $(this).attr('hasError', '0');
            }
        });
        /*End Edit Amount in Edit Modal*/



        /*Update Data*/
        $(document).on('click', '.edit', function() {
            var paymentId = $(this).attr('paymentId');
            var flag = 0;
            var hasError = 0;

           $(".EMtbody tr").each(function(index, row) {
               hasError = $(this).closest('tr').find('.EMpaymentAmount').attr('hasError');
               if (hasError==1) {
                flag = 1;
               }
           });

           

           //flag = 0 means there is no error
           if (flag==0) {
                var paymentDetailsId = new Array();
                var amount = new Array();
                var csrf = "{{csrf_token()}}";

                $(".EMtbody tr").each(function(index, row) {

                   paymentDetailsId.push(JSON.stringify($(this).closest('tr').find('.paymentDetailsId').val()));
                   amount.push(JSON.stringify($(this).closest('tr').find('.EMpaymentAmount').val()));
                  
               });

                
                if (paymentDetailsId.length>0) {
                    
                     $.ajax({
                        type: 'post',
                        url: './updateOtsAccountPayment',
                        data: {paymentId: paymentId, paymentDetailsId: paymentDetailsId, amount: amount, _token: csrf},
                        
                        dataType: 'json',
                        success: function( _response ) {
                            //alert(JSON.stringify(_response));
                            window.location.href = "viewOtsPayment";
                            
                            
                        },
                        error: function( _response ){
                       
                        }
                    }); /*End Ajax*/
                }

           }//End If
        });
        /*End Update Data*/







        /*Change Total Amount into Edit MOdal*/
        $(document).on('input', '.EMpaymentAmount', function() {
          var amount = 0;
          if (this.value!="") {
            amount = parseFloat(this.value);
          }

          var total = 0;



         $(this).parents('.EMtable').find('tbody tr').each(function(index, el) {


           var amount = 0;
            if ($(el).closest('tr').find('.EMpaymentAmount').val()!="") {
               amount = parseFloat($(el).closest('tr').find('.EMpaymentAmount').val()); 
            }
            
            total = total + amount;
         });

          $(this).closest('.EMtable').find('.EMtotalPaymentInput').val(total);
          $(this).closest('.EMtable').find('.EMtotalPayment').html(num(total));
          
        });
        /*End Change Total Amount into Edit MOdal*/


        $('#viewModal').on('hidden.bs.modal', function () {
            $("#hiddenTitle").hide();
        });






        /*Print Document*/
        $("#printButton").click(function() {

  /*Make Print Table*/
            tableMarkup = "<table id='printingTable' style='color: black;font-size:11px;border-collapse: collapse;margin-bottom:80px;' border='1px solid black;'>"+
"   <tbody>"+
"       <tr>"+
"           <td width='25%'>Account Name:</td>"+
"           <td width='25%'>"+$('#VMmemberName').val()+"</td>"+
"           <td width='25%'>Principal Amount (Tk):</td>"+
"           <td width='25%'>"+$('#VMprincipalAmount').val()+"</td>"+
"       </tr>"+


"       <tr>"+
"           <td width='25%'>Account No:</td>"+
"           <td width='25%'>"+$('#VMaccNo').val()+"</td>"+
"           <td width='25%'>Interest Rate (%):</td>"+
"           <td width='25%'>"+$('#VMinterestRate').val()+"</td>"+
"       </tr>"+

"       <tr>"+
"           <td width='25%'>Branch Location:</td>"+
"           <td width='25%'>"+$('#VMbranchLocation').val()+"</td>"+
"           <td width='25%'>Interest Payment (Tk):</td>"+
"           <td width='25%'>"+$('#VMinterestDue').val()+"</td>"+
"       </tr>"+

"       <tr>"+
"          <td width='25%'>Certificate No:</td>"+
"           <td width='25%'>"+$('#VMcertificateNo').val()+"</td>"+
"          <td width='25%'>Closing Fee (Tk):</td>"+
"           <td width='25%'>"+$('#VMclosingCharge').val()+"</td>"+
"       </tr>"+

"       <tr>"+
"         <td width='25%'>Opening Date:</td>"+
"           <td width='25%'>"+$('#VMopeningDate').val()+"</td>"+
"            <td width='25%'>Net Payment (Tk):</td>"+
"           <td width='25%'>"+$('#VMpaidAmount').val()+"</td>"+
"       </tr>"+
"       <tr>"+
"           <td width='25%'>Closing Date:</td>"+
"           <td width='25%'>"+$('#VMclosingDate').val()+"</td>"+
"           <td width='25%'>Payment Mode:</td>"+
"           <td width='25%'>"+$('#VMpaymentMode').val()+"</td>"+
"       </tr>"+

"       <tr>"+
"           <td width='25%'>Payment Nature:</td>"+
"           <td width='25%'>"+$('#VMpaymentNature').val()+"</td>";

    

  if ($('#VMledgerId').val()=='350') {
    
    tableMarkup = tableMarkup + 
                    "          <td width='25%'></td>"+
                    "           <td width='25%'></td>"+
                    "       </tr>";
  }
  else{
    tableMarkup = tableMarkup + 
                    "          <td width='25%'>Bank & Acc. No:</td>"+
                    "           <td width='25%'>"+$('#VMbankAccNumber').val()+"</td>"+
                    "       </tr>";
  }

  tableMarkup = tableMarkup + 


"          <tr><td width='25%'>Status:</td>"+
"           <td width='25%'>"+$('#VMstatus').val()+"</td>";



 if ($('#VMledgerId').val()=='350') {
    tableMarkup = tableMarkup + 
                    "          <td width='25%'></td>"+
                    "           <td width='25%'></td>"+
                    "       </tr>";
  }
  else{
    tableMarkup = tableMarkup + 
                    "          <td width='25%'>Cheque Number:</td>"+
                    "           <td width='25%'>"+$('#VMchequeNum').val()+"</td>"+
                    "       </tr>";
  }



tableMarkup = tableMarkup +

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
            
        });
        /*End Print Document*/

        




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
   
    .EMtable input { text-align: center; width: 80px; max-height: 20px;}
</style>

<style type="text/css">
    #otsTable thead tr th{padding: 2px;}
</style>


@include('dataTableScript')

<style type="text/css">
   #otsTable thead tr:nth-child(2) th{
      border-top-width: 1px !important;        
    }
     #otsTable thead tr:nth-child(2) th:nth-child(3){
      border-right-width: 1px !important;        
    }

    
</style>
@endsection
