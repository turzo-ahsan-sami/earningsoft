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
                <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">Collection LIST</font></h1>
              </div>
              <div class="panel-body panelBodyView"> 
                <table class="table table-striped table-bordered" id="posCollectionView" style="color:black;">
                          <thead>
                            <tr>
                              <th width="32">SL#</th>
                              <th>Bill No</th>
                              <th>Collection Bill No</th>
                              <th>Collection Date</th>
                              <th>Type</th>
                              <th>Company Name</th>
                              <th>Ins.No</th>
                              <th>Paid Amount</th>
                              <th>Action</th>
                            </tr>
                            {{ csrf_field() }}
                          </thead>
                          <tbody>
                              <?php $no=0; ?>
                                @foreach($posCollectionInfo as $posCollection)
                                   <tr class="item{{$posCollection->id}}">
                                    <td class="text-center slNo">{{++$no}}</td>
                                    
                                    <td style="text-align: left; padding-left: 5px;">{{'SB000'.$posCollection->salesBillNo}}</td>
                                    <td style="text-align: left; padding-left: 5px;">{{'CB000'.$posCollection->collectionBillNo}}</td>
                                    <td>{{date('d-m-Y', strtotime($posCollection->collectionDate))}}</td>
                                      <td style="text-align: left; padding-left: 5px;">
                                      @if($posCollection->salesType==1)
                                        {{'Sales'}}
                                      @elseif($posCollection->salesType==2)
                                       {{'Service'}}
                                      @endif
                                    </td> 
                                    <td style="text-align: left; padding-left: 5px;">
                                    <?php
                                          $companyName = DB::table('pos_client')->select('clientCompanyName')->where('id',$posCollection->clientCompanyId)->first();
                                        ?>
                                    {{$companyName->clientCompanyName}}</td>
                                    <td style="text-align: center;">{{$posCollection->installmentNo}}</td>
                                    <td style="text-align: center;">{{$posCollection->salesPayAmount}}</td>
                                    <?php 
                                     $maxInstallmentNoOfBill = DB::table('pos_collection')->where('salesBillNo',$posCollection->salesBillNo)->max('installmentNo');
                                    ?>
                                    <?php if(($maxInstallmentNoOfBill > $posCollection->installmentNo) || ($posCollection->installmentNo==1)): ?>
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
                                    <a id="editIcone" href="javascript:;" class="edit-modal" collectionId="{{$posCollection->id}}">
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
<div id="editModal" class="modal fade" style="margin-top:3%;">
  <div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Update Collection</h4>
  </div>
  <div class="modal-body">
   {{-- {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!} --}}
    <input id="id" type="hidden"  value=""/>
      <div class="row">
        <div class="col-md-12">
            <div class="col-md-1"></div>
                <div class="col-md-10 fullbody">                                      
                    <div class="panel panel-default panel-border">
                        <div class="panel-heading">
                            <div class="panel-title">Collection</div>
                        </div>
                        <div class="panel-body">
                                  {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'salesForm')) !!} 

                                   <div class="row">
                                       <div class="col-md-6">   
                                          <div class="form-group">
                                              {!! Form::label('clientCompanyId', 'Company Name:', ['class' => 'col-sm-4 control-label']) !!}
                                              <div class="col-sm-8">
                                                  <?php 
                                                  $posClients = array('' => 'Please Select Client Company') + DB::table('pos_client')->pluck('clientCompanyName','id')->all(); 
                                                  ?>      
                                                  {!! Form::select('clientCompanyId', ($posClients), null, array('class'=>'form-control', 'id' => 'clientCompanyId')) !!}
                                                  <p id='clientCompanyIde' style="max-height:3px;"></p>
                                              </div>
                                          </div>

                                          <div class="form-group">
                                              {!! Form::label('posSalesBillNo', 'Bill No:', ['class' => 'col-sm-4 control-label']) !!}
                                              <div class="col-sm-8">
                                                  <?php 
                                                       $posSalesBillNo = array('' => 'Please Select Bill No') + DB::table('pos_sales')
                                                                     ->select(DB::raw("CONCAT(LPAD(salesBillNo,3,0)) AS salesBillNo"),'id')
                                                                     ->pluck('salesBillNo', 'salesBillNo')
                                                                     ->all();  
                                                  ?>      
                                                  {!! Form::select('posSalesBillNo', ($posSalesBillNo), null, array('class'=>'form-control', 'id' => 'posSalesBillNo')) !!}
                                                  <p id='posSalesBillNoe' style="max-height:3px;"></p>
                                              </div>
                                          </div>
                                              
                                          <div class="form-group">
                                              {!! Form::label('collectionDate', 'Collection Date:', ['class' => 'col-sm-4 control-label']) !!}
                                              <div class="col-sm-8">
                                                  {!! Form::text('collectionDate', null, ['class' => 'form-control', 'id' => 'collectionDate','readonly']) !!}
                                                   <p id='collectionDatee' style="max-height:3px;"></p>
                                              </div> 
                                          </div>
                                      </div>    
                                      <div class="col-md-6">
                                         <div class="form-group">
                                              {!! Form::label('salesAmount', 'Sales Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                              <div class="col-sm-8">
                                                  {!! Form::text('salesAmount', null, ['class' => 'form-control numeric', 'id' => 'salesAmount', 'readonly']) !!}
                                                   <p id='salesAmounte' style="max-height:3px; color:red;"></p>
                                              </div> 
                                          </div>
                                          <div class="form-group">
                                              {!! Form::label('serviceAmount', 'Service Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                              <div class="col-sm-8">
                                                  {!! Form::text('serviceAmount', null, ['class' => 'form-control numeric', 'id' => 'serviceAmount', 'readonly']) !!}
                                                   <p id='serviceAmounte' style="max-height:3px; color:red;"></p>
                                              </div> 
                                          </div> 
                                          <div class="form-group">
                                              {!! Form::label('totalAmount', 'Total Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                              <div class="col-sm-8">
                                                  {!! Form::text('totalAmount', null, ['class' => 'form-control numeric', 'id' => 'totalAmount', 'readonly']) !!}
                                                   <p id='totalAmounte' style="max-height:3px; color:red;"></p>
                                              </div> 
                                          </div> 
                                          <div class="form-group">
                                              {!! Form::label('totalCollectionAmount', 'Collection Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                              <div class="col-sm-8">
                                                  {!! Form::text('totalCollectionAmount', null, ['class' => 'form-control numeric', 'id' => 'totalCollectionAmount', 'readonly']) !!}
                                                   <p id='totalCollectionAmounte' style="max-height:3px; color:red;"></p>
                                              </div> 
                                          </div>   
                                          <div class="form-group">
                                              {!! Form::label('dueAmount', 'Due Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                              <div class="col-sm-8">
                                                  {!! Form::text('dueAmount', null, ['class' => 'form-control numeric', 'id' => 'dueAmount', 'readonly']) !!}
                                                   <p id='dueAmounte' style="max-height:3px; color:red;"></p>
                                              </div> 
                                          </div>   
                                       </div>                  
                                  </div> 
                                  <div class="row">
                                      <div class="col-md-6">
                                              <div class="form-group">
                                                  {!! Form::label('totalQuantity', 'Total Quantity:', ['class' => 'col-sm-4 control-label']) !!}
                                                  <div class="col-sm-8">
                                                      {!! Form::text('totalQuantity', null, ['class' => 'form-control numeric', 'id' => 'totalQuantity', 'readonly']) !!}
                                                       <p id='totalQuantitye' style="max-height:3px; color:red;"></p>
                                                  </div> 
                                              </div>

                                              <div class="form-group">
                                                  {!! Form::label('totalBillAmount', 'Total Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                                  <div class="col-sm-8">
                                                      {!! Form::text('totalBillAmount', null, ['class' => 'form-control numeric', 'id' => 'totalBillAmount', 'readonly']) !!}
                                                       <p id='totalAmounte' style="max-height:3px; color:red;"></p>
                                                  </div> 
                                              </div>

                                              <div class="form-group">
                                                  {!! Form::label('payAmount', 'Paid Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                                  <div class="col-sm-8">
                                                      {!! Form::text('payAmount', null, ['class' => 'form-control numeric', 'id' => 'payAmount','readonly']) !!}
                                                       <p id='payAmounte' style="max-height:3px;"></p>
                                                  </div> 
                                              </div>

                                              <div class="form-group">
                                                  {!! Form::label('due', 'Before Due:', ['class' => 'col-sm-4 control-label']) !!}
                                                  <div class="col-sm-8">
                                                      {!! Form::text('due', null, ['class' => 'form-control numeric', 'id' => 'due','readonly']) !!}
                                                       <p id='duee' style="max-height:3px;"></p>
                                                  </div> 
                                              </div>
                                              <div class="form-group">
                                                  {!! Form::label('collectionPayAmount', 'Pay Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                                  <div class="col-sm-8">
                                                      {!! Form::text('collectionPayAmount', null, ['class' => 'form-control numeric', 'id' => 'collectionPayAmount',]) !!}
                                                       <p id='collectionPayAmounte' style="max-height:3px;"></p>
                                                  </div> 
                                              </div>

                                              <div class="form-group">
                                                  {!! Form::label('afDue', 'After Due:', ['class' => 'col-sm-4 control-label']) !!}
                                                  <div class="col-sm-8">
                                                      {!! Form::text('afDue', null, ['class' => 'form-control numeric', 'id' => 'afDue','readonly']) !!}
                                                       <p id='duee' style="max-height:3px;"></p>
                                                  </div> 
                                              </div>

                                              <div class="form-group">
                                                  {!! Form::label('paymentType', 'Payment Type:', ['class' => 'col-sm-4 control-label']) !!}
                                                  <div class="col-sm-8">
                                                       <label><input type="radio" class="paymentType" id="cash" name="paymentType" value="1"> Cash </label>
                                                      <label><input type="radio" class="paymentType" id="bank" name="paymentType" value="2"> Bank </label>
                                                  </div> 
                                              </div>
                                              <div id="bankStatment">
                                                  <div class="form-group">
                                                      {!! Form::label('bankName', 'Bank Name:', ['class' => 'col-sm-4 control-label']) !!}
                                                      <div class="col-sm-8">
                                                          {!! Form::text('bankName', null, ['class' => 'form-control', 'id' => 'bankName']) !!}
                                                           <p id='bankNamee' style="max-height:3px;"></p>
                                                      </div> 
                                                  </div>
                                                  <div class="form-group">
                                                      {!! Form::label('checkNo', 'Check No:', ['class' => 'col-sm-4 control-label']) !!}
                                                      <div class="col-sm-8">
                                                          {!! Form::text('checkNo', null, ['class' => 'form-control', 'id' => 'checkNo']) !!}
                                                           <p id='checkNoe' style="max-height:3px;"></p>
                                                      </div> 
                                                  </div>
                                                  <div class="form-group">
                                                      {!! Form::label('bankDate', 'Date:', ['class' => 'col-sm-4 control-label']) !!}
                                                      <div class="col-sm-8">
                                                          {!! Form::text('bankDate', null, ['class' => 'form-control ', 'id' => 'bankDate','readonly','autocomplite'=>'off']) !!}
                                                           <p id='bankDatee' style="max-height:3px;"></p>
                                                      </div> 
                                                  </div>
                                              </div>
                                              <div class="modal-footer">
                                                <input id="EMcollectionId" type="hidden" name="collectionId" value="">
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
        </div>
    </div>
</div>
  <!-- End Edit Modal -->
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
                dataType: 'json',
                data: {id:collectionId, _token:csrf},
            })
            .done(function(data) {
                location.reload();
                window.location.href = '{{url('pos/viewPosCollectionList/')}}';
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