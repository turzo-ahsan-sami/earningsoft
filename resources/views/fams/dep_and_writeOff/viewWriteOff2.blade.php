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
              <a href="{{url('famsWriteOff/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
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
          <table class="table table-striped table-bordered" id="famsWriteOffTable">
                    <thead>
                    
                      <tr>
                        <th width="30">SL#</th>
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
                          <td style="color: black;">{{$productCode}}</td>                      
                          <td width="150" style="color: black;text-align: right;padding-right: 15px;">{{$writeOff->productTotalCost}}</td>                         
                          <td width="150" style="color: black;text-align: right;padding-right: 15px;">{{$writeOff->depGenerated}}</td>                          
                          <td width="150" style="color: black;text-align: right;padding-right: 15px;">{{$writeOff->amount}}</td>                          

                          <td  class="text-center" width="80">
                           <a href="" data-toggle="modal" data-target="#view-modal-{{$writeOff->id}}" >
                               <i class="fa fa-eye" aria-hidden="true" ></i>
                           </a>&nbsp
                           <a href="" data-toggle="modal" data-target="#delete-modal-{{$writeOff->id}}" >
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


@foreach($writeOffs as $wrOff)
{{-- View Modal --}}
    <div id="view-modal-{{$wrOff->id}}" class="modal fade view-modal" style="margin-top:3%">
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
                        {!! Form::label('', 'Write Off ID:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        {!! Form::text('', $wrOff->writeOffId, ['class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                        {!! Form::label('', 'Product Name:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        @php
                          $productName = DB::table('fams_product')->where('id',$wrOff->productId)->value('name');
                          $productCode = DB::table('fams_product')->where('id',$wrOff->productId)->value('productCode');
                        @endphp
                        {!! Form::text('', $productName, ['class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                        {!! Form::label('', 'Product ID:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        {!! Form::text('', $productCode, ['class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                      {!! Form::label('', 'Product of (Branch):', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        @php
                          $branchName = DB::table('gnr_branch')->where('id',$wrOff->branchId)->value('name');
                        @endphp
                        {!! Form::text('', $branchName, ['class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>
                      </div>
                      <div class="form-group">
                        {!! Form::label('', 'Write Off By:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        @php
                          $writeOffBranchId = DB::table('users')->where('id',$wrOff->writeOffByUserId)->value('branchId');
                          $writeOffBranchName = DB::table('gnr_branch')->where('id',$writeOffBranchId)->value('name');
                        @endphp
                        {!! Form::text('', $writeOffBranchName, ['class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                         
                        </div>             
                      
                      <div class="form-group">
                      {!! Form::label('', 'Date:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('',date('d-m-Y', strtotime($wrOff->createdDate)) , ['class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>                                  
                    </div> 

                    </div>  
                    </div> 

                    <div class="col-md-6">{{-- Second 6 --}}
                    <div class="form-horizontal form-groups">

                     <div class="form-group">
                        {!! Form::label('', 'Disposal Amount:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        {!! Form::text('', $wrOff->amount, ['class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                                       
                      </div>
                      <div class="form-group">
                      {!! Form::label('', 'Product Cost Price:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                        
                        {!! Form::text('', $wrOff->productTotalCost, ['class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>
                      </div>
                      <div class="form-group">
                        {!! Form::label('', 'Additional Charge:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('', $wrOff->productAdditionalCharge, ['class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                        </div>                         
                        </div>             
                      
                      <div class="form-group">
                      {!! Form::label('', 'Depreciation:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                       
                        {!! Form::text('',$wrOff->depGenerated , ['class' => 'form-control','autocomplete'=>'off','readonly']) !!}
                      </div>                                  
                    </div> 

                    <div class="form-group">
                      {!! Form::label('', 'Dep. Opening Balance:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        @php
                          $depOpeningBalance = DB::table('fams_product')->where('id',$wrOff->productId)->value('depreciationOpeningBalance');                          
                        @endphp                     
                        {!! Form::text('',$depOpeningBalance , ['class' => 'form-control','autocomplete'=>'off','readonly']) !!}
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
        <div id="delete-modal-{{$wrOff->id}}" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
                    </div>
                    <div class="modal-body">
                    
                        <h2>Are You Confirm to Delete This Record?</h2>
                       

                        <div class="modal-footer">
                            {!! Form::open(['url' => 'famsDelteWriteOff/']) !!}
                            <input type="hidden" name="writeOffId" value={{$wrOff->id}}>
                            
                            <button  type="submit" class="btn actionBtn glyphicon glyphicon-check btn-success"><span id=""> Confirm</span></button>
                            
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                            {!! Form::close() !!}

                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- End Delete Modal --}}

@endforeach



</div>


@include('dataTableScript')

<script type="text/javascript">
$(document).ready(function() {
  $("#famsWriteOffTable tr").find(".dataTables_empty").css("color","black");
  $("#famsWriteOffTable_info").hide();
  $(".view-modal").find(".modal-dialog").css("width","80%");
});
  
</script>

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>

@endsection