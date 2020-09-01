<style type="text/css">
    
    @media only screen and (max-width: 768px) {
    .mobile-submenuli{
       margin-left: 60px;
       z-index: 1000
}
.mobile-purchaseul{
    line-height: 16px;
}
    
}
</style>

@php
    $companyId = Auth::user()->company_id_fk;
    $checkCompany = App\gnr\GnrCompany::where('id', $companyId)->select('business_type', 'stock_type')->first();
@endphp

<ul id="main-menu" class="navbar-nav">
    <li id="pos-settings">
        <a class="animated fadeInLeft" href="">
            <i class="linecons-cog"></i>
            <span class="title">Settings</span>
        </a>
        <ul>
            <li>
                <a href="{{url('pos/products/')}}">
                    <span class="title">Products</span>
                </a>
            </li>
            <li>
                 <a href="{{url('pos/customers/')}}">
                    <span class="title">Customers</span>
                </a>
            </li>
            <li>
                <a href="{{url('pos/suppliers/')}}">
                    <span class="title">Suppliers</span>
                </a>
            </li>
            <li>
                <a href="{{url('pos/Payments')}}">
                    <span class="title">Payments</span>
                </a>
            </li>
            <li>
                <a href="{{url('pos/voucherSettingList')}}">
                    <span class="title">Voucher Configuration</span>
                </a>
            </li>

            @if($checkCompany->business_type == 'manufacture' && $checkCompany->stock_type == 1)
            <li>
                <a href="{{url('pos/otherCostList')}}">
                    <span class="title">Other Cost</span>
                </a>
            </li>

            <li>
                <a href="{{url('pos/costSheetList')}}">
                    <span class="title">Cost Sheet</span>
                </a>
            </li>
            @endif

        </ul>
    </li>

    {{-- =========================================START EMPLOYEE========================================= --}}


    {{-- <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Employee</span>
        </a>
        <ul>
            <li>
                <a href="{{url('pos/posHrEmployeeList/')}}">
                    <span class="title">Employee Information</span>
                </a>
            </li>
            <li>
                <a href="{{ url('pos/transfer') }}">
                    <span class="title">Transfer</span>
                </a>
            <li>
                <a href="{{ url('pos/resignInfo') }}">
                    <span class="title">Resign</span>
                </a>
            </li>
            <li>
                <a href="{{ url('pos/terminateInfo') }}">
                    <span class="title">Terminate</span>
                </a>
            </li>
        </ul>
    </li> --}}

    {{-- =========================================END EMPLOYEE========================================= --}}

    {{-- <li id="process">
        <a class="animated fadeInLeft" href="">
            <i class="linecons-cog"></i>
            <span class="title">Process</span>
        </a>
        <ul>
            <li>
                <a href="">Day End</a>
                <ul>
                    <li>
                        <a href="{{url('pos/posDayEndList/')}}">
                            <span class="title">Day End List</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="">Month End</a>
                <ul>
                    <li>
                        <a href="{{url('pos/posMonthEndList/')}}">
                            <span class="title">Month End List</span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </li> --}}
    <li id="transaction">
        <a class="animated fadeInLeft" href="">
            <i class="fa fa-usd"></i>
            <span class="title">Transaction</span>
        </a>
        <ul>
            <li>
                <a href="">
                    <span class="title">Sales</span>
                </a>
                <ul>
                    <li class="mobile-submenuli">
                        <a href="{{url('pos/sales/')}}">
                            <span class="title">Sales</span>
                        </a>
                    </li>
                    <li  class="mobile-submenuli">
                        <a href="{{url('pos/salesReturn/')}}">
                            <span class="title">Sales Return</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="">
                    <span class="title">Purchase</span>
                </a>
                 <ul class="mobile-purchaseul">
                    <li  class="mobile-submenuli">
                        <a href="{{url('pos/purchase')}}">
                            <span class="title">Purchase</span>
                        </a>
                    </li>
                    <li  class="mobile-submenuli">
                        <a href="{{url('pos/purchaseReturn')}}">
                            <span class="title">Purchase Return</span>
                        </a>
                    </li>
                </ul>

            </li>
            <li>
                <a href="{{url('pos/order')}}">
                    <span class="title">Order</span>
                </a>
            </li> 
            <li>
                <a href="{{url('pos/listCollection')}}">
                    <span class="title">Collection</span>
                </a>
            </li>
            <li>
                <a href="{{url('pos/viewSupplierPayment')}}">
                    <span class="title">Payment</span>
                </a>
            </li>

        </ul>
    </li>
    <li id="report">
        <a class="animated fadeInLeft" href="">
        <i class="fa fa-line-chart"></i>
            <span class="title">Reports</span>
        </a>
        <ul>
            <!-- <li>
                <a href="{{url('pos/posPurchaseReport/')}}">
                    <span class="title">Purchase Report</span>
                </a>
            </li> -->
            <!-- <li>
                <a href="{{url('pos/posPurchaseReturnReport/')}}">
                    <span class="title">Purchase Return Report</span>
                </a>
            </li> -->
            <li>
                <a href="">
                    <span class="title">Purchase</span>
                </a>
                <ul>
                    <li  class="mobile-submenuli">
                        <a href="{{url('pos/posPurchaseReport/')}}">
                            <span class="title">Purchase Report</span>
                        </a>
                    </li>
                    <li  class="mobile-submenuli">
                        <a href="{{url('pos/posPurchaseReturnReport/')}}">
                            <span class="title">Purchase Return Report</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- <li>
                <a href="{{url('pos/posSalesNServiceReport/')}}">
                    <span class="title">Sales Report</span>
                </a>
            </li> -->
            <li>
                <a href="">
                    <span class="title">Sales</span>
                </a>
                <ul>
                    <li  class="mobile-submenuli">
                        <a href="{{url('pos/posSalesNServiceReport/')}}">
                            <span class="title">Sales Report</span>
                        </a>
                    </li>
                    <li  class="mobile-submenuli">
                        <a href="{{url('pos/posSalesReturnServiceReport/')}}">
                            <span class="title">Sales Return Report</span>
                        </a>
                    </li>
                    <li  class="mobile-submenuli">
                        <a href="{{url('pos/posSalesWiseProfitReport/')}}">
                            <span class="title">Sales Wise Profit Report</span>
                        </a>
                    </li>
                    <li  class="mobile-submenuli">
                        <a href="{{url('pos/posInvoiceWiseProfitReport/')}}">
                            <span class="title">Invoice Wise Profit Report</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- <li>
                <a href="">
                    <span class="title">Collection</span>
                </a>
                <ul>
                    <li>
                        <a href="{{url('pos/posCollectionClientReport/')}}">
                            <span class="title">Client Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('pos/posCollectionReport/')}}">
                            <span class="title">Collection Report</span>
                        </a>
                    </li>
                </ul>
            </li> -->
        </ul>
    </li>

</ul>

<style type="text/css">

    #main-menu > li > ul >li > ul{
    left: 270px !important;
    top: -42px !important;
}

    #main-menu > li#report > ul >li > ul{
        left: 267px !important;
        top: -26px !important;
}
    #main-menu > li#pos-settings > ul >li > ul{
        left: 267px !important;
        top: -26px !important;
}

#main-menu > li#process > ul >li > ul{
        left: 267px !important;
        top: -26px !important;
}
#main-menu > li#transaction > ul >li > ul{
        left: 267px !important;
        top: -26px !important;
}

#main-menu >li > ul > li{
    height: 29px !important;
}
</style>


<style type="text/css">
    #main-menu li {
    padding: 0px  20px 0px 0px!important;
   
    }
</style>
