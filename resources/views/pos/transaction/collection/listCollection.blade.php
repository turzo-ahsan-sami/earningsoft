@extends('layouts/pos_layout')
@section('title', '| Collection')
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
                    <a href="{{url('pos/addPosCollection/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Collection</a>
                </div>
                <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">COLLECTION LIST</font></h1>
              </div>
             
              <div class="panel-body panelBodyView"> 
                <table class="table table-striped table-bordered" id="posCollectionView" style="color:black;">
                          <thead>
                            <tr>
                              <th width="80">SL#</th>
                              <th width="25%">Customer Name</th>
                              <th>Bill No</th>
                            {{--   <th>Collection Bill No</th> --}}
                              <th>Collection Date</th>
                              <th>Payment Type</th>
                              <th width="15%">Total Sales Amount</th>
                              <th width="15%">Paid Amount</th>
                              <th>Action</th>
                            </tr>
                            {{ csrf_field() }}
                          </thead>
                          <tbody>
                               <?php $no=0; ?>
                                @foreach($posCollectionInfo as $posCollection)
                               
                                <tr class="item{{$posCollection->id}}">
                                    <td class="text-center">{{++$no}}</td>
                                      <td style="text-align: left; padding-left: 5px;">{{$posCollection['customer']['name']}}</td> 
                                    <td style="text-align: center;">{{'SB000'.$posCollection->salesBillNo}}</td>
                                    <td>{{date('d-m-Y', strtotime($posCollection->collectionDate))}}</td>
                                    <td style="text-align: left; padding-left: 5px;">{{$posCollection['payment']['name']}}</td> 
                                    <td style="text-align: right; padding-right: 5px;">{{round($posCollection['sales']['grossTotal'])}}</td>
                                     <td style="text-align: right; padding-right: 5px;">{{$posCollection->collectionAmount}}</td> 
                                      
                                      @php 
                                        $lastId = App\pos\PosCollection::where('salesBillNo',$posCollection->salesBillNo)->where('companyId',Auth::user()->company_id_fk)->orderBy('collectionDate','desc')->max('id'); 
                                      @endphp
                                      <?php if(($lastId > $posCollection->id) || ($posCollection->id==1)): ?>
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
                                          <a id="editIcone" href="{{url('pos/editPosCollection/'.$posCollection->id)}}" class="edit-modal"">
                                            <span class="glyphicon glyphicon-edit"></span>
                                          </a> &nbsp;
                                          <a id="deleteIcone" href="javascript:;" class="delete-modal" collectionId="{{$posCollection->id}}">
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
            <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Collection</h4>
      </div>
        <div class="modal-body ">
           <div class="row" style="padding-bottom:20px;"> </div>
            <h2>Are You Confirm to Delete This Record?</h2>
          <div class="modal-footer">
             <input id="DMcollectionId" type="hidden"  value=""/>
             <button type="button" class="btn btn-danger"  id="DMcollection"  data-dismiss="modal">confirm</button>
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
      
      $("#posCollectionView").dataTable({
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
                var collectionId = $(this).attr('collectionId');
                var csrf = "{{csrf_token()}}";
                $("#EMcollectionId").val(collectionId);
                $.ajax({
                    url: './collectionInfoByCollectionId',
                    type: 'POST',
                    dataType: 'json',
                    data: {id:collectionId , _token: csrf},
                    success: function(data) {

                        $('#salesAmount').val(data['posTotalSalesAmountOfCompany']);
                        $('#serviceAmount').val(data['posTotalServiceAmountOfCompany']);
                        $('#clientCompanyId').val(data['posCollectionInfoByIds'].clientCompanyId);
                        $('#posSalesBillNo').val(data['posCollBillNo']);
                        $('#collectionDate').val(data['posCollectionInfoByIds'].collectionDate);
                        $('#totalAmount').val(data['posTotalAmount']);
                        $('#totalQuantity').val(data['posTotalSalesQuantityOfSalesBillNO']);
                        $('#totalBillAmount').val(data['posTotalSalesAmountOfSalesBillNO']);
                        $('#payAmount').val(data['totalSalesBillNoCollectionAmount']);
                        $('#dueAmount').val(data['posDueAmount']);
                        $('#collectionPayAmount').val(data['posCollectionInfoByIds'].salesPayAmount);
                        $('#due').val(data['beforeDue']); 
                        $('#totalCollectionAmount').val(data['totalSalesBillCollectionAmount']);

                        $('#afDue').val(data['afterDue']);
                        $('#collectionPayAmount').val(data['posCollectionInfoByIds'].salesPayAmount);
                        
                        if(data['posCollectionInfoByIds'].paymentType==1){
                            $('input[name=paymentType][value=1]').attr('checked','checked');
                            $("#bankStatment").hide();
                        }else if(data['posCollectionInfoByIds'].paymentType==2){
                            $('input[name=paymentType][value=2]').attr('checked','checked');
                            $("#bankStatment").show();
                        }
                        $('#bankName').val(data['posCollectionInfoByIds'].bankName);
                        $('#checkNo').val(data['posCollectionInfoByIds'].checkNo);
                        $('#bankDate').val(data['posCollectionInfoByIds'].bankDate);

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
        $("#collectionPayAmount").keyup(function(event) {
            var totalBfDue = parseInt($('#due').val());
            var currentCollection = parseInt($('#collectionPayAmount').val());
            if(totalBfDue >= currentCollection){
                var afDueAmount = totalBfDue-currentCollection;
                $("#afDue").val(afDueAmount);
            } 
            else {
                alert("please input valide amount!!");
                $('#collectionPayAmount').val('');
                $("#afDue").val('');
            }
   
}); 
/*update Start*/
        $("#updateButton").on('click', function() {
            $("#updateButton").prop("disabled", true);
            $.ajax({
                url: './posEditCollectionItem',
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
            $("#DMcollectionId").val($(this).attr('collectionId'));
            $('#deleteModal').modal('show');
        });
        $("#DMcollection").on('click',  function() {
            var collectionId= $("#DMcollectionId").val();
            var csrf = "{{csrf_token()}}";
            $.ajax({
                url: './deleteCollection',
                type: 'POST',
                data: {id:collectionId, _token:csrf},
                dataType: 'json',
            })
            .done(function(data) {
                location.reload();
                window.location.href = '{{url('pos/listCollection/')}}';
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