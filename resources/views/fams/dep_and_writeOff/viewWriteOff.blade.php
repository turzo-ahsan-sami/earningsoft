@extends('layouts/fams_layout')
@section('title', '| Write Off')
@section('content')
@include('successMsg')
<div class="row">
<div class="col-md-12">


@if (session('writeOffDelete'))
<div class="alert alert-info alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Info!</strong> {{ session('writeOffDelete') }}
</div>
@endif
<div class="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">          
              <a href="{{url('famsWriteOff/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon">
                        </i>Add Wtite Off</a>
              
          </div>


            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px; color: white;">WRITE OFF LIST</h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
           <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            
            $("#famsWriteOffTable").dataTable({
              "ordering": false,
              "orderCellsTop": true,
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_",
                  "sInfo": ""
                  }

                });
             
          });
          </script> 
        </div>
          <table class="table table-striped table-bordered" id="famsWriteOffTable" style="color:black;">
                    <thead>

                    
                      <tr>
                        <th width="32">SL#</th>
                        <th>Write Off Date</th>                        
                        <th>Write Off ID</th>
                        <th>Product Name</th>                        
                        <th>Product ID Number</th>                        
                        <th colspan="3" style="padding: 0px; margin: 0px;">
                          <table  width="100%"  style="margin: 0px; padding: 0px; height: 100%;">
                            <tr style="border-bottom: 1pt solid white;">
                              <th colspan="3">Product Information</th>
                            </tr>
                            <tr> 
                              <th width="33%" style="border-right: 1pt solid white;">Cost Price(TK)</th>
                            <th width="33%" style="border-right: 1pt solid white;">Accum. Dep. Amount(TK)</th>
                            <th width="33%">Disposal Amount</th>
                            </tr>
                          </table>   
                        </th>
                        
                        <th>Action</th>
                      </tr>                      
                    </thead>
                    <tbody>
                      <?php $no=0; ?>
                      @foreach($writeOffs as $writeOff)
                        <tr class="item{{$writeOff->id}}">
                          <td class="text-center slNo">{{++$no}}</td>
                          <td style="color: black;">{{(date('d-m-Y', strtotime($writeOff->createdDate)))}}</td>
                          <td style="color: black;">{{$writeOff->writeOffId}}</td>
                          @php
                            $productName = DB::table('fams_product')->where('id',$writeOff->productId)->value('name');
                            $productCode = DB::table('fams_product')->where('id',$writeOff->productId)->value('productCode');
                          @endphp
                          <td style="color: black;text-align: left;padding-left: 15px;">{{$productName}}</td>
                          <td style="color: black;">{{$prefix.$productCode}}</td>                      
                          <td width="150" style="color: black;text-align: right;padding-right: 15px;">{{$writeOff->productTotalCost}}</td>                         
                          <td width="150" style="color: black;text-align: right;padding-right: 15px;">{{$writeOff->depGenerated}}</td>                          
                          <td width="150" style="color: black;text-align: right;padding-right: 15px;">{{$writeOff->amount}}</td>                          

                          <td  class="text-center" width="80">
                          @php
                            $productBanchName = DB::table('gnr_branch')->where('id',$writeOff->branchId)->value('name');

                            $writeOffBranchId = DB::table('users')->where('id',$writeOff->writeOffByUserId)->value('branchId');
                            $writeOffBranchName = DB::table('gnr_branch')->where('id',$writeOffBranchId)->value('name');

                            $depOpeningBalance = DB::table('fams_product')->where('id',$writeOff->productId)->value('depreciationOpeningBalance');

                          @endphp
                           <a href="javascript:;" class="view-modal" writeOffId="{{$writeOff->writeOffId}}" productName="{{$productName}}" productCode="{{$prefix.$productCode}}" productOfBranch="{{$productBanchName}}" writeOffByBranch="{{$writeOffBranchName}}" writeOffDate="{{(date('d-m-Y', strtotime($writeOff->createdDate)))}}" disposalAmount="{{$writeOff->amount}}" productCostPrice="{{$writeOff->productTotalCost}}" productAddCharge="{{$writeOff->productAdditionalCharge}}" accDep="{{$writeOff->depGenerated}}" depOpeningBal="{{$depOpeningBalance}}" >
                               <i class="fa fa-eye" aria-hidden="true" ></i>
                           </a>&nbsp
                           <a href="javascript:;" class="delete-modal" writeOffId="{{$writeOff->id}}">
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
                    <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Write Off Details</h4>
                </div>
                <div class="modal-body">

                 <div class="row">
                  <div class="col-md-12">
                  <div class="col-md-6">
                    <div class="form-horizontal form-groups">

                     <div class="form-group">
                        {!! Form::label('VMwriteOffId', 'Write Off ID:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        {!! Form::text('VMwriteOffId', null, ['id'=>'VMwriteOffId','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                        {!! Form::label('VMproductName', 'Product Name:', ['class' => 'col-sm-4 control-label']) !!}
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
                        {!! Form::label('VMwriteOffByBranchName', 'Write Off By (Branch):', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('VMwriteOffByBranchName', null, ['id'=>'VMwriteOffByBranchName','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                         
                        </div>             
                      
                      <div class="form-group">
                      {!! Form::label('VMdate', 'Date:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('VMdate',null , ['id'=>'VMdate','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>                                  
                    </div> 

                    </div>  
                    </div> 

                    <div class="col-md-6">{{-- Second 6 --}}
                    <div class="form-horizontal form-groups">

                     <div class="form-group">
                        {!! Form::label('VMdisposalAmount', 'Disposal Amount:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        {!! Form::text('VMdisposalAmount', null, ['id'=>'VMdisposalAmount','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                      {!! Form::label('VMproductCostPrice', 'Product Cost Price:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                        
                        {!! Form::text('VMproductCostPrice', null, ['id'=>'VMproductCostPrice','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>
                      </div>
                      <div class="form-group">
                        {!! Form::label('VMproductAddCharge', 'Additional Charge:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('VMproductAddCharge',null, ['id'=>'VMproductAddCharge','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                         
                        </div>             
                      
                      <div class="form-group">
                      {!! Form::label('VMaccmDep', 'Depreciation:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('VMaccmDep', null , ['id'=>'VMaccmDep','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>                                  
                    </div> 

                    <div class="form-group">
                      {!! Form::label('VMdepOpeningBal', 'Dep. Opening Balance:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                                            
                        {!! Form::text('VMdepOpeningBal',null , ['id'=>'VMdepOpeningBal','class' => 'form-control','autocomplete'=>'off','readonly']) !!}
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
                            {!! Form::open(['url' => 'famsDelteWriteOff/']) !!}
                            <input type="hidden" name="writeOffId" id="DMwriteOffId">
                            
                            <button  type="submit" class="btn actionBtn glyphicon glyphicon-check btn-success"> Confirm</button>
                            
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                            {!! Form::close() !!}

                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- End Delete Modal --}}



</div>


@include('dataTableScript')

 <script type="text/javascript">
window.hasAnyError = 0;
  window.onerror = function(){
    hasAnyError = 1;
  }
  if (hasAnyError || !hasAnyError) {

    window.onload = function(){

    /*View Modal*/
    $(".view-modal").on('click', function() {
      if(hasAccess('viewFamsWriteOffDetails')){

      $("#VMwriteOffId").val($(this).attr('writeOffId'));
      $("#VMproductName").val($(this).attr('productName'));
      $("#VMproductCode").val($(this).attr('productCode'));
      $("#VMbranchName").val($(this).attr('productOfBranch'));
      $("#VMwriteOffByBranchName").val($(this).attr('writeOffByBranch'));
      $("#VMdate").val($(this).attr('writeOffDate'));
      $("#VMdisposalAmount").val($(this).attr('disposalAmount'));
      $("#VMproductCostPrice").val($(this).attr('productCostPrice'));
      $("#VMproductAddCharge").val($(this).attr('productAddCharge'));
      $("#VMaccmDep").val($(this).attr('accDep'));
      $("#VMdepOpeningBal").val($(this).attr('depOpeningBal'));
      
      $("#viewModal").modal('show');
      } 
    });/*End View Modal*/


    /*Delete Modal*/
    $(".delete-modal").on('click', function() {
      if(hasAccess('famsDelteWriteOff')){      
      $("#DMwriteOffId").val($(this).attr('writeOffId'));      
      $("#deleteModal").modal('show');
    }
    });
    /*End Delete Modal*/ 

  } /*End On Load*/
    $("#famsWriteOffTable tr").find(".dataTables_empty").css("color","black");
    $("#famsWriteOffTable_info").hide();
    $("#viewModal").find(".modal-dialog").css("width","80%");
    
  }/*End has Error*/
  
</script> 


<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>

@endsection