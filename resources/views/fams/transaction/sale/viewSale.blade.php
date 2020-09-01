@extends('layouts/fams_layout')
@section('title', '| Sales')
@section('content')
<div class="row">
<div class="col-md-12">


@if (session('saleUpdate'))
<div class="alert alert-info alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Info!</strong> {{ session('saleUpdate') }}
</div>
@endif
@if (session('saleDelete'))
<div class="alert alert-info alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Info!</strong> {{ session('saleDelete') }}
</div>
@endif
<div class="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">          
              <a href="{{url('famsAddSale/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon">
                        </i>Add Sale</a>
              
          </div>


            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px; color: white;">SALES LIST</h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            
            $("#famsSaleTable").dataTable({
              "ordering": false,
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_",
                  
                  }

                });
             
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="famsSaleTable">
                    <thead>
                      <tr>
                        <th width="30">SL#</th>
                        <th>Sales Date</th>                        
                        <th>Sales ID</th>
                        <th>Product Name</th>                        
                        <th>Product ID</th>                        
                        <th>Cost Price</th>                        
                        <th>Accumulated Dep.</th>                        
                        <th>Sale Amount</th>
                        <th colspan="2" style="padding: 0px; margin: 0px;">
                          <table  width="100%"  style="margin: 0px; padding: 0px; height: 100%;">
                            <tr style="border-bottom: 1pt solid white;">
                              <th colspan="2">Profit & Loss</th>
                            </tr>
                            <tr> 
                              <th width="100" style="border-right: 1pt solid white;">Profit</th>                            
                            <th width="100">Loss</th>
                            </tr>
                          </table> 
                        </th>
                        <th>Action</th>
                      </tr>                      
                    </thead>
                    <tbody>
                      <?php $no=0; ?>
                      @foreach($sales as $sale)
                        <tr class="item{{$sale->id}}">
                          <td class="text-center slNo">{{++$no}}</td>
                          <td style="color: black;">{{(date('d-m-Y', strtotime($sale->createdDate)))}}</td>
                          <td style="color: black;">{{$sale->saleId}}</td>
                          @php
                            $productName = DB::table('fams_product')->where('id',$sale->productId)->value('name');
                            $productCode = DB::table('fams_product')->where('id',$sale->productId)->value('productCode');
                            $productTotalCost =  (float) $sale->productTotalCost + (float) $sale->productAdditionalCharge;                         
                          @endphp
                          <td style="color: black;text-align: left;padding-left: 15px;">{{$productName}}</td>
                          <td style="color: black;">{{$prefix.$productCode}}</td>                      
                          <td style="color: black;text-align: right;padding-right: 15px;">{{$productTotalCost}}</td>                          
                          <td style="color: black;text-align: right;padding-right: 15px;">{{$sale->depGenerated}}</td>                          
                          <td style="color: black;text-align: right;padding-right: 15px;">{{$sale->amount}}</td>                          
                          <td style="color: black;text-align: right;padding-right: 15px;" width="100">{{$sale->profitAmount}}</td>                          
                          <td style="color: black;text-align: right;padding-right: 15px;" width="100">{{$sale->lossAmount}}</td>                          

                          <td  class="text-center" width="80">
                          @php
                            $saleDate = date('d-m-Y', strtotime($sale->createdDate));
                            $accDep = DB::table('fams_depreciation_details')->where('productId',$sale->productId)->sum('amount');

                            $branchName = DB::table('gnr_branch')->where('id',$sale->branchId)->value('name');                            
                            $saleByBranchName = DB::table('gnr_branch')->where('id',$sale->saleByBranchId)->value('name');
                          @endphp
                           <a href="javascript:;" class="view-modal" saleId="{{$sale->saleId}}" productName="{{$productName}}" productCode="{{$prefix.$productCode}}" productOfBranch="{{$branchName}}" saleByBranch="{{$saleByBranchName}}" saleDate="{{$saleDate}}" salePrice="{{$sale->amount}}" productCost="{{$productTotalCost}}" accDep="{{$sale->depGenerated}}" profitAmount="{{$sale->profitAmount}}" lossAmount="{{$sale->lossAmount}}" >
                               <i class="fa fa-eye" aria-hidden="true" ></i>
                           </a>&nbsp
                           <a href="javascript:;" class="edit-modal" saleRowId="{{$sale->id}}" saleId="{{$sale->saleId}}" productId="{{$sale->productId}}" productName="{{$productName}}" productCode="{{$prefix.$productCode}}" productOfBranch="{{$branchName}}" saleByBranch="{{$saleByBranchName}}" saleDate="{{$saleDate}}" salePrice="{{$sale->amount}}" productCost="{{$productTotalCost}}" accDep="{{$sale->depGenerated}}" profitAmount="{{$sale->profitAmount}}" lossAmount="{{$sale->lossAmount}}" >
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>&nbsp
                           <a href="javascript:;" class="delete-modal" saleId="{{$sale->id}}" >
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
                    <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Sale Details</h4>
                </div>
                <div class="modal-body">

                 <div class="row">
                  <div class="col-md-12">
                  <div class="col-md-6">
                    <div class="form-horizontal form-groups">

                     <div class="form-group">
                        {!! Form::label('VMsaleId', 'Sale ID:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        {!! Form::text('VMsaleId', null, ['id'=>'VMsaleId','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                        {!! Form::label('', 'Product Name:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('VMproductName', null, ['id'=>'VMproductName','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                        {!! Form::label('VMproductCode', 'Product ID:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        {!! Form::text('VMproductCode', null, ['id'=>'VMproductCode','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                      {!! Form::label('VMbranchName', 'Product of (Branch):', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        
                        {!! Form::text('VMbranchName', null, ['id'=>'VMbranchName','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>
                      </div>
                      <div class="form-group">
                        {!! Form::label('VMsaleByBranchName', 'Sale By:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        
                        {!! Form::text('VMsaleByBranchName', null, ['id'=>'VMsaleByBranchName','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                         
                        </div>             
                      
                      <div class="form-group">
                      {!! Form::label('VMsaleDate', 'Date:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('VMsaleDate', null , ['id'=>'VMsaleDate','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>                                  
                    </div> 

                    </div>  
                    </div> 

                    <div class="col-md-6">{{-- Second 6 --}}
                    <div class="form-horizontal form-groups">

                     <div class="form-group">
                        {!! Form::label('VMsalePrice', 'Sale Price:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        {!! Form::text('VMsalePrice', null, ['id'=>'VMsalePrice','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                      {!! Form::label('VMproductCost', 'Product Cost:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                        
                        {!! Form::text('VMproductCost', null, ['VMproductCost'=>'VMproductCost','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>
                      </div>
                                
                      
                      <div class="form-group">
                      {!! Form::label('VMaccDep', 'Accumulated Depreciation:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('VMaccDep',null , ['VMaccDep'=>'VMaccDep','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>                                  
                    </div> 

                    <div class="form-group">
                      {!! Form::label('VMprofitAmount', 'Profit Amount:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('VMprofitAmount',null , ['id'=>'VMprofitAmount','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>                                  
                    </div> 
                    <div class="form-group">
                      {!! Form::label('VMlossAmount', 'Loss Amount:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('VMlossAmount',null , ['id'=>'VMlossAmount','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>                                  
                    </div> 

                    </div>  
                    </div>


                 </div>
                 </div>
                 </div>               
               
                      <div class="modal-footer">
                        <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span id=""> Close</span></button>
                    </div>
                </div>
            </div>
        </div>
    

    {{-- End View Modal --}}


    {{-- Edit Modal --}}
    <div id="editModal" class="modal fade" style="margin-top:3%;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Sale Details</h4>
                </div>
                <div class="modal-body">

                 <div class="row">
                  <div class="col-md-12">
                  <div class="col-md-6">
                    <div class="form-horizontal form-groups">
                    {!! Form::open(['url' => 'famsEditSale','id'=>'updateForm']) !!}
                    {!! Form::hidden('EMsaleRowId', null,['id'=>'EMsaleRowId']) !!}
                    

                     <div class="form-group">
                        {!! Form::label('EMsaleId', 'Sale ID:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        {!! Form::text('EMsaleId', null, ['id'=>'EMsaleId','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                        {!! Form::label('EMproductName', 'Product Name:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        
                        {!! Form::text('EMproductName', null, ['EMproductName'=>'EMproductName','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                        {!! Form::label('EMproductCode', 'Product ID:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        {!! Form::text('EMproductCode', null, ['id'=>'EMproductCode','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                      {!! Form::label('EMProductOfBranch', 'Product of (Branch):', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                       
                        {!! Form::text('EMProductOfBranch', null, ['id'=>'EMProductOfBranch','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>
                      </div>
                      <div class="form-group">
                        {!! Form::label('', 'Sale By:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        
                        {!! Form::text('EMsaleByBranchName', null, ['id'=>'EMsaleByBranchName','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                         
                        </div>             
                      
                      <div class="form-group">
                      {!! Form::label('EMsaleDate', 'Date:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('EMsaleDate',null , ['id'=>'EMsaleDate','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>                                  
                    </div> 

                    </div>  
                    </div> 

                    <div class="col-md-6">{{-- Second 6 --}}
                    <div class="form-horizontal form-groups">

                     <div class="form-group">
                        {!! Form::label('EMsaleAmount', 'Sale Price:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        {!! Form::text('EMsaleAmount', null, ['id'=>'EMsaleAmount','class' => 'form-control','autocomplete'=>'off']) !!}
                        <p id='EMsaleAmounte' style="max-height:3px;"></p>
                        </div>                        
                      </div>
                      <div class="form-group">
                      {!! Form::label('EMproductCost', 'Product Cost:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                        
                        {!! Form::text('EMproductCost', null, ['id'=>'EMproductCost','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>
                      </div>
                                  
                      
                      <div class="form-group">
                      {!! Form::label('EMaccDep', 'Accumulated Depreciation:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('EMaccDep',null , ['id'=>'EMaccDep','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>                                  
                    </div> 

                    <div class="form-group">
                      {!! Form::label('EMprofitAmount', 'Profit Amount:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('EMprofitAmount',null , ['id'=>'EMprofitAmount','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>                                  
                    </div> 
                    <div class="form-group">
                      {!! Form::label('EMlossAmount', 'Loss Amount:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('EMlossAmount',null , ['id'=>'EMlossAmount','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>                                  
                    </div> 
                    

                    </div>  
                    </div>


                 </div>
                 </div>
                 </div>
               
               
                      <div class="modal-footer">
                      <button class="btn actionBtn glyphicon glyphicon-check btn-success" id="submit" type="submit"> Update</button>
                        <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                    </div>

                    {!! Form::close() !!}


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
                            {!! Form::open(['url' => 'famsDelteSale/']) !!}
                            <input type="hidden" name="saleId" id="DMsaleId">
                            
                            <button  type="submit"  class="btn actionBtn glyphicon glyphicon-check btn-success"><span id=""> Confirm</button>
                            
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                            {!! Form::close() !!}

                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- End Delete Modal --}}



<script type="text/javascript">
window.hasAnyError = 0;
  window.onerror = function(){
    hasAnyError = 1;
  }
  if (hasAnyError || !hasAnyError) {

    window.onload = function(){


    /*View Modal*/
    $(".view-modal").on('click', function() {
      
        $("#VMsaleId").val($(this).attr('saleId'));
        $("#VMproductName").val($(this).attr('productName'));
        $("#VMproductCode").val($(this).attr('productCode'));
        $("#VMbranchName").val($(this).attr('productOfBranch'));
        $("#VMsaleByBranchName").val($(this).attr('saleByBranch'));
        $("#VMsaleDate").val($(this).attr('saleDate'));
        $("#VMsalePrice").val($(this).attr('salePrice'));
        $("#VMproductCost").val($(this).attr('productCost'));
        $("#VMaccDep").val($(this).attr('accDep'));
        $("#VMprofitAmount").val($(this).attr('profitAmount'));
        $("#VMlossAmount").val($(this).attr('lossAmount'));
        $("#viewModal").modal('show');
    
    });/*End View Modal*/

    /*Edit Modal*/
    $(".edit-modal").on('click', function() {

      $("#EMsaleRowId").val($(this).attr('saleRowId'));
      $("#EMsaleId").val($(this).attr('saleId'));
      $("#EMproductId").val($(this).attr('productId'));
      $("#EMproductName").val($(this).attr('productName'));
      $("#EMproductCode").val($(this).attr('productCode'));
      $("#EMProductOfBranch").val($(this).attr('productOfBranch'));
      $("#EMsaleByBranchName").val($(this).attr('saleByBranch'));
      $("#EMsaleDate").val($(this).attr('saleDate'));
      $("#EMsaleAmount").val($(this).attr('salePrice'));
      $("#EMproductCost").val($(this).attr('productCost'));
      $("#EMaccDep").val($(this).attr('accDep'));
      $("#EMprofitAmount").val($(this).attr('profitAmount'));
      $("#EMlossAmount").val($(this).attr('lossAmount'));
      $("#editModal").modal('show');
    
    });/*End Edit Modal*/


    /*Delete Modal*/
    $(".delete-modal").on('click', function() {
      $("#DMsaleId").val($(this).attr('saleId'));      
      $("#deleteModal").modal('show');
    });
    /*End Delete Modal*/

    $("#EMsaleAmount").on('input', function() {
    this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    $("#EMsaleAmounte").hide();
  });

   $("#submit").on('click', function(event) {
    
    var amount = $("#EMsaleAmount").val();
    
    if(amount==""){
      event.preventDefault();
      $("#EMsaleAmounte").empty();
      $("#EMsaleAmounte").append('<span class="errormsg" style="color:red;">*Required</span>');
      $("#EMsaleAmounte").show();
    }
     
     
   });

   $("#famsSaleTable tr").find(".dataTables_empty").css("color","black");
  $("#famsSaleTable_info").hide();

  } /*End On Load*/


    //$("#famsWriteOffTable tr").find(".dataTables_empty").css("color","black");
    //$("#famsWriteOffTable_info").hide();
    $("#viewModal").find(".modal-dialog").css("width","80%");
    $("#editModal").find(".modal-dialog").css("width","80%");
    
  }/*End has Error*/
  
</script> 




@include('dataTableScript')


@endsection