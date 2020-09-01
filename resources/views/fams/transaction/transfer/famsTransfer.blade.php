@extends('layouts/fams_layout')
@section('title', '| Transfer')
@section('content')


@if (session('alreadyTransfered'))
<div class="alert alert-info alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Info!</strong> {{ session('alreadyTransfered') }}
</div>
@endif

@if (session('editTransfer'))
<div class="alert alert-info alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Info!</strong> {{ session('editTransfer') }}
</div>
@endif

@if (session('deleteTransfer'))
<div class="alert alert-info alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Info!</strong> {{ session('deleteTransfer') }}
</div>
@endif

<div class="row">
<div class="col-md-12" style="color: black;">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('famsAddTransfer/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Transfer</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px;color: white;">TRANSFER LIST</h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#gnrGrounView").dataTable().yadcf([
    
            ]);*/
            $("#famsTranferView").dataTable({
              "ordering": false,
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="famsTranferView">
            <thead>
              <tr>
                <th width="32">SL#</th>
                <th>Transfer Date</th>
                <th>Transfer ID</th>
                <th>Product Name</th>
                <th>Product ID</th>
                <th colspan="2" style="padding: 0px; margin: 0px;">
                  <table  width="100%"  style="margin: 0px; padding: 0px; height: 100%;">
                    <tr style="border-bottom: 1pt solid white;">
                      <th colspan="2">Transfer Branch</th>
                    </tr>
                    <tr> 
                      <th width="100" style="border-right: 1pt solid white;">From</th>                            
                    <th width="100">To</th>
                    </tr>
                  </table> 
                </th>

                <th colspan="3" style="padding: 0px; margin: 0px;">
                  <table  width="100%"  style="margin: 0px; padding: 0px; height: 100%;">
                    <tr style="border-bottom: 1pt solid white;">
                      <th colspan="3">Transfer Amount</th>
                    </tr>
                    <tr> 
                      <th width="100" style="border-right: 1pt solid white;">Cost Price</th>                            
                      <th width="100" style="border-right: 1pt solid white;">Acc. Dep.</th>                            
                    <th width="100">Wri. Down Value</th>
                    </tr>
                  </table> 
                </th>
                            
                <th>Action</th>   

              </tr>              
            </thead>
            <tbody>            
              <?php $no=0; ?>
                    @foreach($transfers as $transfer) 
                    @php
                      $productName = DB::table("fams_product")->where('id',$transfer->productId)->value('name');
                      $viewModalProjectFrom = DB::table('gnr_project')->where('id',$transfer->projectIdFrom)->value('name');
                      $viewModalProjectTo = DB::table('gnr_project')->where('id',$transfer->projectIdTo)->value('name');
                      $viewModalProjectTypeFrom = DB::table('gnr_project_type')->where('id',$transfer->projectTypeIdFrom)->value('name');
                      $viewModalProjectTypeTo = DB::table('gnr_project_type')->where('id',$transfer->projectTypeIdTo)->value('name');
                      $viewModalBranchFrom = DB::table('gnr_branch')->where('id',$transfer->branchIdFrom)->value('name');
                      $viewModalBranchTo = DB::table('gnr_branch')->where('id',$transfer->branchIdTo)->value('name');
                      $transferDate = date('d-m-Y', strtotime($transfer->transferDate));

                    @endphp


                    <tr class="item{{$transfer->id}}">
                        <td class="text-center slNo">{{++$no}}</td>
                        <td style="color: black;">{{date('d-m-Y', strtotime($transfer->transferDate))}}</td>

                        <td style="color: black;">{{$transfer->transferId}}</td>
                        
                        <td style="color: black; text-align: left;padding-left: 15px;">{{$productName}}</td>
                         @if ($user->branchId==$transfer->branchIdFrom)
                          <td style="color: black;">{{$prefix.$transfer->oldProductCode}}</td>
                          @else
                          <td style="color: black;">{{$prefix.$transfer->newProductCode}}</td>
                          
                        @endif
                        
                        <td style="color: black;text-align: left;padding-left: 5px;" width="100">{{$viewModalBranchFrom}}</td>
                        <td style="color: black;text-align: left;padding-left: 5px;" width="100">{{$viewModalBranchTo}}</td>


                        <td style="color: black;text-align: right;padding-right: 15px;" width="100">{{$transfer->productTotalCost}}</td>
                        <td style="color: black;text-align: right;padding-right: 15px;" width="100">{{$transfer->pastDep}}</td>
                        <td style="color: black;text-align: right;padding-right: 15px;" width="100">{{$transfer->totalTransferAmount}}</td>
                        
                        <td class="text-center" width="80">
                       

                        <a href="javascript:;" class="view-modal"  transferId="{{$transfer->transferId}}" productName="{{$productName}}" oldProductCode="{{$prefix.$transfer->oldProductCode}}" newProductCode="{{$prefix.$transfer->newProductCode}}" projectIdFrom="{{$viewModalProjectFrom}}" projectIdTo="{{$viewModalProjectTo}}"  projectTypeFrom="{{$viewModalProjectTypeFrom}}" projectTypeTo="{{$viewModalProjectTypeTo}}" branchFrom="{{$viewModalBranchFrom}}" branchTo="{{$viewModalBranchTo}}" transferDate="{{$transferDate}}" transferAmount="{{$transfer->totalTransferAmount}}" productTotalCost="{{$transfer->productTotalCost}}" pastDep="{{$transfer->pastDep}}" remainingDep="{{$transfer->depRemainning}}">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                          </a>&nbsp

                          {{-- <a href="javascript:;" class="edit-modal" transferRowId="{{$transfer->id}}" transferId="{{$transfer->transferId}}" oldProductCode="{{$transfer->oldProductCode}}" newProductCode="{{$transfer->newProductCode}}" productName="{{$productName}}" projectIdFrom="{{$viewModalProjectFrom}}" projectIdTo="{{$transfer->projectIdTo}}"  projectTypeFrom="{{$viewModalProjectTypeFrom}}" projectTypeTo="{{$transfer->projectTypeIdTo}}" branchFrom="{{$viewModalBranchFrom}}" branchTo="{{$transfer->branchIdTo}}" transferDate="{{$transferDate}}" transferAmount="{{$transfer->totalTransferAmount}}" productTotalCost="{{$transfer->productTotalCost}}" pastDep="{{$transfer->pastDep}}" remainingDep="{{$transfer->depRemainning}}">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>&nbsp --}}

                          {{-- <a href="javascript:;" id={{$transfer->id}} data-toggle="modal" data-target="#edit-modal-{{$transfer->id}}" >
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>&nbsp --}}

                          <a href="javascript:;" class="delete-modal" transferRowId="{{$transfer->id}}">
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
<div id="viewModal" class="modal fade" style="margin-top:3%;color: black;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Tranfer Details</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal form-groups">
        <div class="row">
        <div class="col-sm-6">
        <div class="form-group">
              {!! Form::label('transferId', 'Transfer ID:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('transferId', null, ['class'=>'form-control','id'=>'viewModalTransferId','readonly']) !!}
              </div>
        </div>
        <div class="form-group">
              {!! Form::label('productName', 'Product Name:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('productName', null, ['class'=>'form-control','id'=>'viewModalProductName','readonly']) !!}
              </div>
        </div>         

        <div class="form-group">
              {!! Form::label('oldProductCode', 'Old Product ID:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('oldProductCode', null, ['class'=>'form-control','id'=>'viewModalOldProductId','readonly']) !!}
              </div>
        </div> 
        <div class="form-group">
              {!! Form::label('newProductCode', 'New Product ID:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('newProductCode', null, ['class'=>'form-control','id'=>'viewModalNewProductId','readonly']) !!}
              </div>
        </div>
        <div class="form-group">
              {!! Form::label('transferDate', 'Transfer Date:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('transferDate', null, ['class'=>'form-control','id'=>'viewModalTransferDate','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('transferAmount', 'Transfer Amount:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('transferAmount', null, ['class'=>'form-control','id'=>'viewModalTransferAmount','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('productTotalCost', 'Product Total Cost:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('productTotalCost', null, ['class'=>'form-control','id'=>'viewModalProductTotalCost','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('pastDep', 'Dep. Generated:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('pastDep', null, ['class'=>'form-control','id'=>'viewModalPastDep','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('remainingDep', 'Dep. Remaining:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('remainingDep', null, ['class'=>'form-control','id'=>'viewModalRemainingDep','readonly']) !!}
              </div>
        </div>



        </div>{{-- End Col 1st 6 --}}

        <div class="col-sm-6">
        <div class="form-group">
              {!! Form::label('projectIdFrom', 'Project From:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('projectIdFrom', null, ['class'=>'form-control','id'=>'viewModalProjectIdFrom','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('projectIdTo', 'Project To:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('projectIdTo', null, ['class'=>'form-control','id'=>'viewModalProjectIdTo','readonly']) !!}
              </div>
        </div> 

        <div class="form-group">
              {!! Form::label('projectTypeIdFrom', 'Project Type From:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('projectTypeIdFrom', null, ['class'=>'form-control','id'=>'viewModalProjectTypeIdFrom','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('projectTypeIdTo', 'Project Type To:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('projectTypeIdTo', null, ['class'=>'form-control','id'=>'viewModalProjectTypeIdTo','readonly']) !!}
              </div>
        </div> 



        <div class="form-group">
              {!! Form::label('branchFrom', 'Branch From:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('branchFrom', null, ['class'=>'form-control','id'=>'viewModalBranchFrom','readonly']) !!}
              </div>
        </div> 

        <div class="form-group">
              {!! Form::label('branchTo', 'Branch To:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('branchTo', null, ['class'=>'form-control','id'=>'viewModalBranchTo','readonly']) !!}
              </div>
        </div> 



        </div>{{-- End Col 2nd 6 --}}

        </div>
              

        <div class="modal-footer">            
          {!! Form::button(' Close',['class'=>'btn btn-danger glyphicon glyphicon-remove','data-dismiss' => 'modal']); !!}           
        </div>
        </div>

      </div>
    </div>
  </div>
</div>{{-- End View Modal --}}


{{-- Edit Modal --}}
<div id="editModal" class="modal fade" style="margin-top:3%;color: black;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Tranfer Details</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal form-groups">
        <div class="row">
        {!! Form::open(['url' => 'famsEditTransfer']) !!}
        {!! Form::hidden('editModalTransferRowId',null,['id'=>'editModalTransferRowId']) !!}
        <div class="col-sm-6">
        <div class="form-group">
              {!! Form::label('transferId', 'Transfer ID:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('transferId', null, ['class'=>'form-control','id'=>'editModalTransferId','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('productName', 'Product Name:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('productName', null, ['class'=>'form-control','id'=>'editModalProductName','readonly']) !!}
              </div>
        </div> 

        <div class="form-group">
              {!! Form::label('oldProductCode', 'Old Product ID:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('oldProductCode', null, ['class'=>'form-control','id'=>'editModalOldProductId','readonly']) !!}
              </div>
        </div> 
        <div class="form-group">
              {!! Form::label('newProductCode', 'New Product ID:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('newProductCode', null, ['class'=>'form-control','id'=>'editModalNewProductId','readonly']) !!}
              </div>
        </div>
        <div class="form-group">
              {!! Form::label('transferDate', 'Transfer Date:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('transferDate', null, ['class'=>'form-control','id'=>'editModalTransferDate','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('transferAmount', 'Transfer Amount:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('transferAmount', null, ['class'=>'form-control','id'=>'editModalTransferAmount','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('productTotalCost', 'Product Total Cost:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('productTotalCost', null, ['class'=>'form-control','id'=>'editModalProductTotalCost','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('pastDep', 'Dep. Generated:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('pastDep', null, ['class'=>'form-control','id'=>'editModalPastDep','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('remainingDep', 'Dep. Remaining:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('remainingDep', null, ['class'=>'form-control','id'=>'editModalRemainingDep','readonly']) !!}
              </div>
        </div>



        </div>{{-- End Col 1st 6 --}}

        <div class="col-sm-6">
        <div class="form-group">
              {!! Form::label('projectIdFrom', 'Project From:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('projectIdFrom', null, ['class'=>'form-control','id'=>'editModalProjectIdFrom','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('projectIdTo', 'Project To:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::select('projectIdTo', $projects, null, ['class'=>'form-control','id'=>'editModalProjectIdTo']) !!}
                  <p><span id="editModalProjectIdToe" style="display: none;color: red">*Required</span></p>
              </div>
        </div> 

        <div class="form-group">
              {!! Form::label('projectTypeIdFrom', 'Project Type From:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('projectTypeIdFrom', null, ['class'=>'form-control','id'=>'editModalProjectTypeIdFrom','readonly']) !!}
              </div>
        </div>

        <div class="form-group">
              {!! Form::label('projectTypeIdTo', 'Project Type To:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::select('projectTypeIdTo', $projectTypes, null, ['class'=>'form-control','id'=>'editModalProjectTypeIdTo']) !!}
                  <p><span id="editModalProjectTypeIdToe" style="display: none;color: red">*Required</span></p>
              </div>
        </div> 



        <div class="form-group">
              {!! Form::label('branchFrom', 'Branch From:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::text('branchFrom', null, ['class'=>'form-control','id'=>'editModalBranchFrom','readonly']) !!}
              </div>
        </div> 

        <div class="form-group">
              {!! Form::label('branchTo', 'Branch To:', ['class' => 'col-sm-3 control-label']) !!}
              <div class="col-sm-9">                       
                  {!! Form::select('branchTo', $branches ,null, ['class'=>'form-control','id'=>'editModalBranchTo']) !!}
                  <p><span id="editModalBranchToe" style="display: none;color: red">*Required</span></p>
                  <p><span id="editModalSameBranche" style="display: none;color: red">*It is already in this Branch</span></p>
              </div>
        </div> 



        </div>{{-- End Col 2nd 6 --}}

        </div>
              

        <div class="modal-footer">
        {!! Form::button(' Update',['type'=>'submit','id'=>'update','class'=>'btn actionBtn glyphicon glyphicon-check btn-success edit']); !!}       
        {!! Form::button(' Close',['class'=>'btn btn-danger glyphicon glyphicon-remove','data-dismiss' => 'modal']); !!}       
                    
        </div>
        </div>
        {!! Form::close() !!}

      </div>
    </div>
  </div>
</div>{{-- End Edit Modal --}}

{{-- Delete Modal --}}
<div id="deleteModal" class="modal fade" style="margin-top:3%;color: black;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Delete!!</h4>
      </div>
      <div class="modal-body">
      {!! Form::open(['url' => 'famsDelteTransfer']) !!}
        {!! Form::hidden('deleteModalTransferRowId',null,['id'=>'deleteModalTransferRowId']) !!}
        <h1>Are You Confirm to Delete this Record??</h1>
        <div class="modal-footer">
          {!! Form::button(' Confirm',['type'=>'submit','id'=>'confirm','class'=>'btn actionBtn glyphicon glyphicon-check btn-success edit']); !!}       
          {!! Form::button(' Close',['class'=>'btn btn-danger glyphicon glyphicon-remove','data-dismiss' => 'modal']); !!}       
                    
        </div>
        {!! Form::close() !!}
      </div>
    </div>
   </div>
</div>
{{-- End Delete Modal --}}


@include('dataTableScript')

<script type="text/javascript">
window.hasAnyError = 0;
  window.onerror = function(){
    hasAnyError = 1;
  }
  if (hasAnyError || !hasAnyError) {

    window.onload = function(){

      $("#update").on('click', function(event) {
      if($("#editModalProjectIdTo").val()==""){
          event.preventDefault();
          $("#editModalProjectIdToe").show();
      }
      if($("#editModalProjectTypeIdTo").val()==""){
          event.preventDefault();
          $("#editModalProjectTypeIdToe").show();
      }
      if($("#editModalBranchTo").val()==""){
          event.preventDefault();
          $("#editModalBranchToe").show();
      }
      if($("#editModalBranchTo option:selected").text()==$("#editModalBranchFrom").val()){
          event.preventDefault();
          $("#editModalSameBranche").show();
      }    
    });

      /*View Modal*/
      $(document).on('click', '.view-modal', function() {
      $("#viewModalTransferId").val($(this).attr('transferId'));
      $("#viewModalProductName").val($(this).attr('productName'));
      $("#viewModalOldProductId").val($(this).attr('oldProductCode'));
      $("#viewModalNewProductId").val($(this).attr('newProductCode'));
      $("#viewModalProjectIdFrom").val($(this).attr('projectIdFrom'));
      $("#viewModalProjectIdTo").val($(this).attr('projectIdTo'));
      $("#viewModalProjectTypeIdFrom").val($(this).attr('projectTypeFrom'));
      $("#viewModalProjectTypeIdTo").val($(this).attr('projectTypeTo'));
      $("#viewModalBranchFrom").val($(this).attr('branchFrom'));
      $("#viewModalBranchTo").val($(this).attr('branchTo'));
      $("#viewModalTransferDate").val($(this).attr('transferDate'));
      $("#viewModalTransferAmount").val($(this).attr('transferAmount'));
      $("#viewModalProductTotalCost").val($(this).attr('productTotalCost'));
      $("#viewModalPastDep").val($(this).attr('pastDep'));
      $("#viewModalRemainingDep").val($(this).attr('remainingDep')); 
      $('#viewModal').modal('show');
    });
      /*End View MOdal*/

      /*Edit Modal*/
              $(document).on('click', '.edit-modal', function() {

      var thisModal = $(this);

      var projectId = "";

      var csrf = "<?php echo csrf_token(); ?>";

      $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                success: function( _response ){

                    $("#editModalProjectTypeIdTo").empty();
                    $("#editModalProjectTypeIdTo").prepend('<option value="">Please Select Project Type</option>');

                    $("#editModalBranchTo").empty();
                    $("#editModalBranchTo").prepend('<option value="">Please Select Branch</option>');                   

                    $.each(_response, function (key, value) {
                        {
                             if (key == "projectTypeList") {
                                $.each(value, function (key1,value1) {
                                    $('#editModalProjectTypeIdTo').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }

                            if (key == "branchList") {
                                $.each(value, function (key1,value1) {
                                    $('#editModalBranchTo').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }                            
                           
                        }
                    });

                    $("#editModalTransferRowId").val(thisModal.attr('transferRowId'));
                    $("#editModalTransferId").val(thisModal.attr('transferId'));
                    $("#editModalProductName").val(thisModal.attr('productName'));
                    $("#editModalOldProductId").val(thisModal.attr('oldProductCode'));
                    $("#editModalNewProductId").val(thisModal.attr('newProductCode'));
                    window.initialProductCode = thisModal.attr('newProductCode');
                    $("#editModalProjectIdFrom").val(thisModal.attr('projectIdFrom'));
                    $("#editModalProjectIdTo").val(thisModal.attr('projectIdTo'));
                    $("#editModalProjectTypeIdFrom").val(thisModal.attr('projectTypeFrom'));
                    $("#editModalProjectTypeIdTo").val(thisModal.attr('projectTypeTo'));
                    $("#editModalBranchFrom").val(thisModal.attr('branchFrom'));
                    $("#editModalBranchTo").val(thisModal.attr('branchTo'));
                    $("#editModalTransferDate").val(thisModal.attr('transferDate'));
                    $("#editModalTransferAmount").val(thisModal.attr('transferAmount'));
                    $("#editModalProductTotalCost").val(thisModal.attr('productTotalCost'));
                    $("#editModalPastDep").val(thisModal.attr('pastDep'));
                    $("#editModalRemainingDep").val(thisModal.attr('remainingDep')); 
                    $('#editModal').modal('show');

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/      
      
    });

      /*End Edit Modal*/

      /*Delete Modal*/
      $(document).on('click', '.delete-modal', function() {      
      $("#deleteModalTransferRowId").val($(this).attr('transferRowId')); 
      $('#deleteModal').modal('show');
    });
      /*End Delete Modal*/


      /*Filtering and Change Prodcut Code*/

          String.prototype.splice = function(idx, rem, str) {
    return this.slice(0, idx) + str + this.slice(idx + Math.abs(rem));
  };

  

    $("#editModalProjectIdTo").change(function() {


        $("#editModalProjectIdToe").hide();
        var projectId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                success: function( _response ){

                    $("#editModalProjectTypeIdTo").empty();
                    $("#editModalProjectTypeIdTo").prepend('<option selected="selected" value="">Please Select Project Type</option>');

                    $("#editModalBranchTo").empty();
                    $("#editModalBranchTo").prepend('<option selected="selected" value="">Please Select Branch</option>');                   

                    $.each(_response, function (key, value) {
                        {
                             if (key == "projectTypeList") {
                                $.each(value, function (key1,value1) {
                                    $('#editModalProjectTypeIdTo').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }

                            if (key == "branchList") {
                                $.each(value, function (key1,value1) {
                                    $('#editModalBranchTo').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }                            
                           
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/


            //Change Product Code When ProjectTo Change

            var newProductCode = $("#editModalNewProductId").val();
            if(newProductCode!=""){
                var text = newProductCode;
                var key = "project";

                if (projectId=="") {  

                    var pieces =  initialProductCode.split("-");
                    var newText = text.splice(13,2,pieces[2]);
                    text = newText;
                    var newText = text.splice(16,4,pieces[3]);
                    text = newText;                    
                    $("#editModalNewProductId").val(text);
                }

                else{

                    $.ajax({
                        type: 'post',
                        url: './famsGetInfo',
                        data: {key: key, projectId: projectId, _token: csrf},
                        dataType: 'json',
                        success: function (data) {

                            var newText = text.splice(13,2,data['project']);
                            text = newText;
                            var newText = text.splice(16,4,data['assetNo']);
                            text = newText;
                            $("#editModalNewProductId").val(text);

                        },
                        error: function(_response){
                            alert("Error");
                        }
                    });

                }

                
            }
    });/*End Change ProjectTo*/

    $("#editModalProjectTypeIdTo").change(function () {
      $("#editModalProjectTypeIdToe").hide();
    });


    /*Change Branch*/
    $("#editModalBranchTo").change(function () {
            $("#editModalBranchToe").hide();
            $("#editModalSameBranche").hide();
            var key = "branch";
            var branchId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            var text = $("#editModalNewProductId").val();

            if (text!="") {

                if (branchId=="") {
                    
                    var pieces =  initialProductCode.split("-");

                    var newText = text.splice(21,3,pieces[4]);
                    text = newText;
                    var newText = text.splice(25,4,pieces[5]);
                    text = newText;
                    $("#editModalNewProductId").val(text);
                }

                else{
                    $.ajax({
                        type: 'post',
                        url: './famsGetInfo',
                        data: {key: key, branchId: branchId, _token: csrf},
                        dataType: 'json',
                        success: function (data) {

                            var newText = text.splice(21,3,data['branch']);
                            text = newText;
                            var newText = text.splice(25,4,data['assetNo']);
                            text = newText;
                            $("#editModalNewProductId").val(text);

                        },
                        error: function(_response){
                            //alert("Error");
                        }
                    });

                }  

            }

            
        });
    /*End Change Branch*/

      /*End Filtering and Change Prodcut Code*/


    }/*End On Load*/

    $("#viewModal").find(".modal-dialog").css("width","80%");
    $("#editModal").find(".modal-dialog").css("width","80%");
    $(".dataTables_empty").css("color","black");
    $("#famsTranferView_info").hide();
  }
</script>



@endsection







