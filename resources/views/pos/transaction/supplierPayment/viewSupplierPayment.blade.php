@extends('layouts/pos_layout')
@section('title', '| Payment')
@section('content')
@include('successMsg')
<?php 
    $user = Auth::user();
    Session::put('branchId', $user->branchId);
    $gnrBranchId = Session::get('branchId');
    $logedUserName = $user->name;
?>
<style type="text/css">
  .disabled {
   pointer-events: none;
   cursor: default;
   opacity: 0.6;
}
</style>
<div class="row">
    <div class="col-md-12">
      <div class="" style="">
        <div class="">
          <div class="panel panel-default" style="background-color:#708090;">
              <div class="panel-heading"  style="padding-bottom:0px">
                <div class="panel-options">
                    <a href="{{url('pos/addSupplierPayment/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Payment</a>
                </div>
                <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PAYMENT LIST</font></h1>
              </div>
              <div class="panel-body panelBodyView"> 
                <table class="table table-striped table-bordered" id="posPaymentView" style="color:black;">
                          <thead>
                            <tr>
                              <th width="80">SL#</th>
                              <th width="25%">Supplier Name</th>
                              <th>Purchase Bill No</th>
                              <th>Payment Date</th>
                              <th>Payment Type</th>
                              <th width="15%">Total Purchase Amount</th>
                              <th width="15%">Paid Amount</th>
                              <th>Action</th>
                            </tr>
                            {{ csrf_field() }}
                          </thead>
                          <tbody>
                               <?php $no=0; ?>
                                @foreach($posPaymentInfo as $posPayment)
                                   <tr class="item{{$posPayment->id}}">
                                    <td class="text-center">{{++$no}}</td>
                                    <td style="text-align: left; padding-left: 5px;">{{$posPayment['supplier']['name']}}</td>
                                    <td style="text-align: center;">{{'PB000'.$posPayment->purchaseBillNo}}</td>
                                    <td>{{date('d-m-Y', strtotime($posPayment->paymentDate))}}</td>
                                    <td style="text-align: left; padding-left: 5px;">{{$posPayment['payment']['name']}}</td> 
                                    <td style="text-align: right; padding-right: 5px;">{{round($posPayment['purchase']['grossTotal'])}}</td>
                                     <td style="text-align: right; padding-right: 5px;">{{$posPayment->paidAmount}}</td> 
                                      @php 
                                        $lastId = App\pos\PosSupplierPayment::where('purchaseBillNo',$posPayment->purchaseBillNo)->where('companyId',Auth::user()->company_id_fk)->orderBy('paymentDate','desc')->max('id');      
                                     @endphp

                                      <?php if(($lastId > $posPayment->id) || ($posPayment->id==1)): ?>
                                          <td  class="text-center" width="80">
                                          <a id="editIcone" href="javascript:;" class="edit-modal disabled">
                                            <span class="glyphicon glyphicon-edit"></span>
                                          </a> &nbsp;
                                          <a id="deleteIcone" href="javascript:;" class="delete-modal disabled">
                                            <span class="glyphicon glyphicon-trash"></span>
                                          </a>
                                        </td>
                                      <?php else: ?>
                                        <td  class="text-center" width="80">
                                          <a id="editIcone" href="{{url('pos/editPayment/'.$posPayment->id)}}" class="edit-modal"">
                                            <span class="glyphicon glyphicon-edit"></span>
                                          </a> &nbsp;
                                          <a id="deleteIcone" href="javascript:;" class="delete-modal" PaymentId="{{$posPayment->id}}">
                                            <span class="glyphicon glyphicon-trash"></span>
                                          </a>
                                        </td>
                                     <?php endif; ?>
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
 <!-- Start Edit Modal -->

<!-- Start Delete Modal -->
<div id="deleteModal" class="modal fade" style="margin-top:3%;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
            <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Payment</h4>
      </div>
        <div class="modal-body ">
           <div class="row" style="padding-bottom:20px;"> </div>
            <h2>Are You Confirm to Delete This Record?</h2>
          <div class="modal-footer">
             <input id="DMPaymentId" type="hidden"  value=""/>
             <button type="button" class="btn btn-danger"  id="DMPayment"  data-dismiss="modal">confirm</button>
             <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
          </div>
      </div>
    </div>
  </div>
</div>
<!-- End Delete Modal -->
@include('dataTableScript')
<script type="text/javascript">
    jQuery(document).ready(function($)
    { 
      $("#posPaymentView").dataTable({
        "ordering": false,
             "oLanguage": {
            "sEmptyTable": "No Records Available",
            "sLengthMenu": "Show _MENU_",
            
            }

          });
       
    });
</script>
<script type="text/javascript">
   $("#posSalesBillNo,#clientCompanyId").prop("disabled", true);
    $("#bankStatment").hide();
  $(".paymentType").click(function(event) {
     var bankStatmentValue = $('input[name=paymentType]:checked').val();
     if(bankStatmentValue==2){
      $("#bankStatment").show();
     } else {
      $("#bankStatment").hide();
     }
  });

  $( "#bankDate" ).datepicker({
        dateFormat: "yy-mm-dd",  
        changeMonth: true,
        changeYear: true,
        maxDate: "0"
      });
$("#bankDate").datepicker().datepicker("setDate", new Date());
        $(document).ready(function(){ 
     /*Edit Modal Start*/
            $(document).on('click', '.edit-modal', function() {
                var PaymentId = $(this).attr('PaymentId');
                var csrf = "{{csrf_token()}}";
                $("#EMPaymentId").val(PaymentId);
                $.ajax({
                    url: './PaymentInfoByPaymentId',
                    type: 'POST',
                    dataType: 'json',
                    data: {id:PaymentId , _token: csrf},
                    success: function(data) {

                        $('#salesAmount').val(data['posTotalSalesAmountOfCompany']);
                        $('#serviceAmount').val(data['posTotalServiceAmountOfCompany']);
                        $('#clientCompanyId').val(data['posPaymentInfoByIds'].clientCompanyId);
                        $('#posSalesBillNo').val(data['posCollBillNo']);
                        $('#PaymentDate').val(data['posPaymentInfoByIds'].PaymentDate);
                        $('#totalAmount').val(data['posTotalAmount']);
                        $('#totalQuantity').val(data['posTotalSalesQuantityOfSalesBillNO']);
                        $('#totalBillAmount').val(data['posTotalSalesAmountOfSalesBillNO']);
                        $('#payAmount').val(data['totalSalesBillNoPaymentAmount']);
                        $('#dueAmount').val(data['posDueAmount']);
                        $('#PaymentPayAmount').val(data['posPaymentInfoByIds'].salesPayAmount);
                        $('#due').val(data['beforeDue']); 
                        $('#totalPaymentAmount').val(data['totalSalesBillPaymentAmount']);

                        $('#afDue').val(data['afterDue']);
                        $('#PaymentPayAmount').val(data['posPaymentInfoByIds'].salesPayAmount);
                        
                        if(data['posPaymentInfoByIds'].paymentType==1){
                            $('input[name=paymentType][value=1]').attr('checked','checked');
                            $("#bankStatment").hide();
                        }else if(data['posPaymentInfoByIds'].paymentType==2){
                            $('input[name=paymentType][value=2]').attr('checked','checked');
                            $("#bankStatment").show();
                        }
                        $('#bankName').val(data['posPaymentInfoByIds'].bankName);
                        $('#checkNo').val(data['posPaymentInfoByIds'].checkNo);
                        $('#bankDate').val(data['posPaymentInfoByIds'].bankDate);

                        $("#editModal").find('.modal-dialog').css('width', '55%');
                        $('#editModal').modal('show');

                      },
                      error: function(argument) {
                                //alert('response error');
                      }
                });
            });
         /*edit Modal End*/

         // After Due Amount Change Change
        $("#PaymentPayAmount").keyup(function(event) {
            var totalBfDue = parseInt($('#due').val());
            var currentPayment = parseInt($('#PaymentPayAmount').val());
            if(totalBfDue >= currentPayment){
                var afDueAmount = totalBfDue-currentPayment;
                $("#afDue").val(afDueAmount);
            } 
            else {
                alert("please input valide amount!!");
                $('#PaymentPayAmount').val('');
                $("#afDue").val('');
            }
   
}); 
/*update Start*/
        $("#updateButton").on('click', function() {
            $("#updateButton").prop("disabled", true);
            $.ajax({
                url: './posEditPaymentItem',
                type: 'POST',
                data:$('form').serialize(),
                dataType: 'json',
               
            })
            .done(function(data) {
                location.reload();
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            })
        });
    /*Update End*/
        $(document).on('click', '.delete-modal', function(){
            $("#DMPaymentId").val($(this).attr('PaymentId'));
            $('#deleteModal').modal('show');
        });
        $("#DMPayment").on('click',  function() {
            var PaymentId= $("#DMPaymentId").val();
            //alert(PaymentId);
            var csrf = "{{csrf_token()}}";
            $.ajax({
                url: './deletePayment',
                type: 'POST',
                dataType: 'json',
                data: {id:PaymentId, _token:csrf},
            })
            .done(function(data) {
                location.reload();
                window.location.href = '{{url('pos/viewSupplierPayment/')}}';
            })
            .fail(function(){
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
        });

});//END READY FUNCTION  
</script>
@endsection