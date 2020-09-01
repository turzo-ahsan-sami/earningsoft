@extends('layouts/pos_layout')
@section('title', '| Voucher Setting List')
@section('content')
   
<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                            <a href="{!! url('/pos/addVoucherSetting'); !!}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Voucher Setting</a>
                        </div>

                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">VOUCHER SETTING LIST</font></h1>
                    </div>
                    <div class="panel-body panelBodyView">
                        
                        <table class="table table-striped table-bordered" id="supplierView" style="color:black;">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Supplier</th>
                                    <th>Purchase</th>
                                    <th>Purchase Return</th>
                                    <th>Sales</th>
                                    <th>Sales Return</th>
                                    <th>Vat</th>
                                    @if($voucherSettingCollections['inventory'] != null && $voucherSettingCollections['cost_of_good_sold'] != null)
                                    <th>Inventory</th>
                                    <th>Cost Of Good Sold</th>
                                    @endif
                                    <th>Action</th>
                                </tr>
                            </thead>
                            @if($voucherSettingCollections)
                            <tbody>
                                <tr>
                                    <td>{{ $voucherSettingCollections['customer'] }}</td>
                                    <td>{{ $voucherSettingCollections['supplier'] }}</td>
                                    <td>{{ $voucherSettingCollections['purchase'] }}</td>
                                    <td>{{ $voucherSettingCollections['purchaseReturn'] }}</td>
                                    <td>{{ $voucherSettingCollections['sales'] }}</td>
                                    <td>{{ $voucherSettingCollections['salesReturn'] }}</td>
                                    <td>{{ $voucherSettingCollections['vat'] }}</td>
                                    @if($voucherSettingCollections['inventory'] != null && $voucherSettingCollections['cost_of_good_sold'] != null)
                                    <td>{{ $voucherSettingCollections['inventory'] }}</td>
                                    <td>{{ $voucherSettingCollections['cost_of_good_sold'] }}</td>
                                    @endif
                                    <td> 
                                        <a href="{!! url('/pos/editVoucherSetting'); !!}" class="edit-modal">
                                        <span class="glyphicon glyphicon-edit"></span></a>&nbsp
                                    </td>
                                </tr>
                            </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('pos/supplier/supplierDetails')
@include('dataTableScript')
@endsection
