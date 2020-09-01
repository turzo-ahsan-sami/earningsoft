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
              <a href="{{url('addOtsPayment/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Payment</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">OTS PAYMENT LIST</font></h1>
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
                        <th>Payment ID</th>
                        <th>Amount</th>
                        <th>Action</th>
                      </tr>
                      
                    </thead>
                    <tbody>
                     @foreach($payments as $index => $payment)                    
                     <tr>
                        <td>{{$index+1}}</td>
                        <td>{{date('d-m-Y',strtotime($payment->paymentDate))}}</td>
                        <td>{{$payment->paymentId}}</td>
                        <td class="amount">{{number_format($payment->amount,2,'.',',')}}</td>
                        <td width="80">

                        <a href="javascript:;" class="view-modal" paymentId="{{$payment->id}}" amount="{{number_format($payment->amount,2,'.',',')}}">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                          </a>&nbsp; 
                          <a href="javascript:;" class="edit-modal" paymentId="{{$payment->id}}" amount="{{number_format($payment->amount,2,'.',',')}}">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;

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
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Payment Details</h4>
                    </div>
                    <div class="modal-body">

                        <div id="contectHolder">
                    
                        </div>

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
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Edit Payment</h4>
                    </div>
                    <div class="modal-body">

                        <div id="EMcontectHolder">
                    
                        </div>

                        {{-- View ModalFooter--}}
                        <div class="modal-footer">
                         <button class="btn actionBtn glyphicon glyphicon-check btn-success edit" paymentId="0" type="button"><span> Update</span></button>
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
            {!! Form::open(['url' => 'deleteOtsAccountPayment/']) !!}
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





<script type="text/javascript">
  $(document).ready(function() {


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
      if(hasAccess('getOtsAccountPaymentInfo')){

      var paymentId = $(this).attr('paymentId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './getOtsAccountPaymentInfo',
        type: 'POST',
        dataType: 'json',
        data: {paymentId: paymentId, _token: csrf},
        success: function(data) {
          
        $("#contectHolder").empty();

        $.each(data['branches'], function(index, branch) {

            textMarkup = "<br><div class='row viewModalLabel' style='color: black;'><div class='col-md-12'> <div class='form-horizontal form-groups'><div class='form-group'><label for='VMbranch' class='col-sm-2 control-label'>Branch:</label> <div class='col-sm-4'><input type='text' name='VMdepId' value='"+branch.name+"' class='form-control VMbranch' autocomplete='off' readonly></div></div></div></div></div>";

               if (index>0) {
                $(".VMtable:last").after(textMarkup);
              }
              else{
                $("#contectHolder").append(textMarkup);
              }



             tableMarkUp = "<br><table width='100%' class='table table-striped table-bordered VMtable'><thead><tr><th>SL#</th><th>Acount No</th><th>Account Holder</th><th>Account Nature</th><th>Principal Amount</th><th>Interest Rate (%)</th><th>Payment Amount</th></tr></thead><tbody class='tbody'></tbody><tr><td colspan='6'><span style='font-weight:bold;font-size:15;'>Total</span></td><td style='text-align:right;padding-right:5px;'><span class='totalPayment'style='font-weight:bold;font-size:15;'></span></td></tr></tbody></table>";

        $(".viewModalLabel:last").after(tableMarkUp);


        count = 1;
        totalPayment = 0;

        $.each(data['payments'], function(index2, payment) {

        if (payment.branchId==branch.id) {

             markup = "<tr style='line-height: 30px;'><td style='text-align:center;'>"+count+"</td><td style='text-align:center;'>"+payment.accNo+"</td><td style='text-align:left;padding-left:5px;'>"+payment.memberName+"</td><td style='text-align:center;'>"+payment.accNature+"</td><td style='text-align:right;padding-right:5px;'>"+num(payment.principalAmount)+"</td><td style='text-align:center;'>"+num(payment.interestRate)+"</td><td style='text-align:right;padding-right:5px;'>"+num(payment.amount)+"</td></tr>";

            $(".tbody:last").before(markup);

            count++;
            totalPayment = totalPayment + Number((payment.amount).toFixed(2));

        }  
        }); //End Each of Payment

        $(".totalPayment:last").html(num(totalPayment));

    }); //End Each of Branch

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



     /*Edit Modal*/    
    
    $(document).on('click', '.edit-modal', function() {
      if(hasAccess('getOtsAccountPaymentInfoToUpdate')){

      var paymentId = $(this).attr('paymentId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './getOtsAccountPaymentInfoToUpdate',
        type: 'POST',
        dataType: 'json',
        data: {paymentId: paymentId, _token: csrf},
        success: function(data) {
          
        $("#EMcontectHolder").empty();

        $.each(data['branches'], function(index, branch) {

            textMarkup = "<br><div class='row editModalLabel' style='color: black;'><div class='col-md-12'> <div class='form-horizontal form-groups'><div class='form-group'><label for='VMbranch' class='col-sm-2 control-label'>Branch:</label> <div class='col-sm-4'><input type='text' name='VMdepId' value='"+branch.name+"' class='form-control VMbranch' autocomplete='off' readonly></div></div></div></div></div>";

               if (index>0) {
                $(".EMtable:last").after(textMarkup);
              }
              else{
                $("#EMcontectHolder").append(textMarkup);
              }

             tableMarkUp = "<br><table width='100%' class='table table-striped table-bordered EMtable'><thead><tr><th>SL#</th><th>Acount No</th><th>Account Holder</th><th>Account Nature</th><th>Principal Amount</th><th>Interest Rate (%)</th><th>Due Amount</th><th>Payment Amount</th></tr></thead><tbody class='EMtbody'></tbody><tfoot><tr style='line-height: 14px;'><td colspan='7' style='text-align:center;'><span style='font-weight:bold;font-size:15;'>Total</span></td><td style='text-align:right;padding-right:5px;'><input class='EMtotalPaymentInput' style='display:none;' value=''><span class='EMtotalPayment'style='font-weight:bold;font-size:15;'></span></td></tr></tfoot></table>";

        $(".editModalLabel:last").after(tableMarkUp);


        count = 1;
        totalPayment = 0;

        $.each(data['payments'], function(index2, payment) {

        if (payment.branchId==branch.id) {
           


             markup = "<tr style='line-height: 30px;'><td style='text-align:center;'>"+count+"</td><td style='text-align:center;'><input style='display:none;' class='paymentDetailsId' value='"+payment.id+"'>"+payment.accNo+"</td><td style='text-align:left;padding-left:5px;'>"+payment.memberName+"</td><td style='text-align:center;'>"+payment.accNature+"</td><td style='text-align:right;padding-right:5px;'>"+num(payment.principalAmount)+"</td><td style='text-align:center;'>"+num(payment.interestRate)+"</td><td style='text-align:right;padding-right:5px;'><input style='display:none;' class='EMdueAmount' value='"+data['dues'][index2]+"'>"+num(data['dues'][index2])+"</td><td style='text-align:right;'><input class='EMpaymentAmount' hasError='0' type='text' value='"+payment.amount+"'></td></tr>";

            $(".EMtbody:last").append(markup);

            count++;
            totalPayment = totalPayment + Number((payment.amount).toFixed(2));

        }  
        }); //End Each of Payment

        $(".EMtotalPayment:last").html(num(totalPayment));
        $(".EMtotalPaymentInput:last").val(totalPayment);

    }); //End Each of Branch

        $("#editModal").find('.edit').attr('paymentId',paymentId);

        $(".modal-dialog").css('width','70%');
        $("#editModal").modal('show');

        },
        error: function(argument) {
          alert('response error');
        }
      });

      }
    });
    /*End Edit Modal*/



    /*Delete Modal*/    
    
      $(document).on('click', '.delete-modal', function() {
      if(hasAccess('deleteOtsAccountPayment')){
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


@include('dataTableScript')
@endsection
