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
<div class="row">
    <div class="col-md-12">
      <div class="" style="">
        <div class="">
          <div class="panel panel-default" style="background-color:#708090;">
              <div class="panel-heading"  style="padding-bottom:0px">
                <div class="panel-options">
                    <a href="{{url('pos/addPosCollection/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Collection</a>
                </div>
                <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">Collection Register</font></h1>
              </div>
              <div class="panel-body panelBodyView"> 
                <table class="table table-striped table-bordered" id="posCollectionView" style="color:black;">
                          <thead>
                            <tr>
                              <th width="32">SL#</th>
                              <th>Bill No</th>
                              <th>Collection Bill No</th>
                              <th>Type</th>
                              <th>Company Name</th>
                              <th>Totlal Quantity</th>
                              <th>Total Amount</th>
                              <th>Paid Amount</th>
                              <th>Total Due</th>
                            </tr>
                            {{ csrf_field() }}
                          </thead>
                          <tbody>
                              <?php $no=0; ?>
                                @foreach($posCollectionInfo as $posCollection)
                                   <tr class="item{{$posCollection->id}}">
                                    <td class="text-center slNo">{{++$no}}</td>
                                    
                                    <td style="text-align: left; padding-left: 5px;">{{'SB 000'.$posCollection->salesBillNo}}</td>
                                    <td style="text-align: left; padding-left: 5px;">{{'CB 000'.$posCollection->collectionBillNo}}</td>
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

                                          $totalSalesQuantity=$posCollectionsColl->where('salesBillNo', $posCollection->salesBillNo)->sum('totalSalesQuantity');
                                          $totalSalesAmount=$posCollectionsColl->where('salesBillNo', $posCollection->salesBillNo)->sum('totalSalesAmount');
                                          $totalPaidAmount=$posCollectionsColl->where('salesBillNo', $posCollection->salesBillNo)->sum('salesPayAmount');
                                          $totalDue =$totalSalesAmount-$totalPaidAmount; 
                                        ?>
                                    {{$companyName->clientCompanyName}}</td>
                                    <td style="text-align: center;">{{$totalSalesQuantity}}</td>
                                    <td style="text-align: right; padding-right: 5px;">{{number_format($totalSalesAmount,2)}}</td>
                                    <td style="text-align: right; padding-right: 5px;">{{number_format($totalPaidAmount,2)}}</td>
                                    <td style="text-align: right; padding-right: 5px;">{{number_format($totalDue,2)}}</td>
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

@include('dataTableScript')

@endsection