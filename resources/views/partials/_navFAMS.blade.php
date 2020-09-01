
<style type="text/css">
    #main-menu li {

    padding: 0  20px 0 0 !important;
    }

    #main-menu > li#settings > ul >li > ul {
            left: 268px !important;
            top: -26px !important;
    }
</style>

<ul id="main-menu" class="navbar-nav">

    {{-- <li id="settings">
        <a class="animated fadeInLeft" href="">
            <i class="linecons-cog"></i>
            <span class="title">Settings</span>
        </a>
        <ul>
            <li>
                <a href="javascript:;">
                    <span class="title">Holiday</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('fams/viewHolidayCalender/') }}">
                            <span class="title">Holiday Calender</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('fams/viewGovHoliday/') }}">
                            <span class="title">Gov. Holiday</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('fams/viewOrgBranchSamityHoliday/') }}">
                            <span class="title">Org./Branch/Samity Holiday</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <span class="title">Address</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('fams/viewDivision/') }}">
                            <span class="title">Division</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('fams/viewDistrict/') }}">
                            <span class="title">District</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('fams/viewUpazila/') }}">
                            <span class="title">Thana / Upazila</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('fams/viewUnion/') }}">
                            <span class="title">Union</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('fams/viewVillage/') }}">
                            <span class="title">Village</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('fams/viewWorkingArea') }}">
                            <span class="title">Working Area</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </li> --}}

    {{-- =========================================START EMPLOYEE========================================= --}}


    {{-- <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Employee</span>
        </a>
        <ul>
            <li>
                <a href="{{url('fams/posHrEmployeeList/')}}">
                    <span class="title">Employee Information</span>
                </a>
            </li>
            <li>
                <a href=" url('fams/transfer')?>">
                    <span class="title">Transfer</span>
                </a>
            <li>
                <a href=" url('fams/resignInfo')?>">
                    <span class="title">Resign</span>
                </a>
            </li>
            <li>
                <a href=" url('fams/terminateInfo')?>">
                    <span class="title">Terminate</span>
                </a>
            </li>
        </ul>
    </li> --}}

    {{-- =========================================END EMPLOYEE========================================= --}}

    <li id="navProduct">
        <a class="animated fadeInLeft" href="">
            <i class="fa fa-cubes" aria-hidden="true"></i>
            <span class="title">Product </span>
        </a>
        <ul >
            <li>
                <a href="">
                    <span class="title">Product setting</span>
                </a>
                <ul >
                    <li>
                        <a href="{{url('viewFamsProduct/')}}">
                            <span class="title">Product</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{url('viewFramsPgroup/')}}">
                            <span class="title">Group</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewFramsPctg/')}}">
                            <span class="title">Category</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewFamsPsubCtg/')}}">
                            <span class="title">Product Subcategory</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewFamsPtype/')}}">
                            <span class="title">Product Type</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewFamsPname/')}}">
                            <span class="title">Product Name</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewFamsPbrand/')}}">
                            <span class="title">Product Brand</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewFamsPmodel/')}}">
                            <span class="title">Product Model</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewFamsPsize/')}}">
                            <span class="title">Product Size</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewFamsPcolor/')}}">
                            <span class="title">Product Color</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewFamsPUom/')}}">
                            <span class="title">Product UOM</span>
                        </a>
                    </li>
                    {{-- <li>
                        <a href="{{url('viewFamsProductPrefix/')}}">
                            <span class="title">Product Prefix</span>
                        </a>
                    </li> --}}
                    <li>
                        <a href="{{url('famsAdditionalProduct/')}}">
                            <span class="title">Additional Product</span>
                        </a>
                    </li>

                     <li>
                        <a href="{{url('viewFamsProductPrefix/')}}">
                            <span class="title">Product Prefix</span>
                        </a>
                    </li>

                </ul>
            </li>
        </ul>
    </li>
    <li id="translation">
        <a class="animated fadeInLeft" href="">
            <i class="fa fa-exchange" aria-hidden="true"></i>
            <span class="title">Transaction </span>
        </a>
        <ul>
            <li>
                <a href="">
                    <span class="title">Requisition</span>
                </a>
                <ul>
                    {{-- <li>
                        <a href="{{url('viewFamsEmpRequiItem/')}}">
                            <span class="title">Employee Requisition</span>
                        </a>
                    </li> --}}
                    <li>
                        <a href="{{url('viewFamsBrnRequiItem/')}}">
                            <span class="title">Branch Requisition</span>
                        </a>
                    </li>

                </ul>
            </li>
            {{-- <li>
                <a href="{{url('purchase/')}}">
                    <span class="title">Purchase</span>
                 </a>
            </li> --}}
            <li>
                <a href="">
                    <span class="title">Issue</span>
                </a>
                <ul>
                    <li>
                        <a href="{{url('famsIssue/')}}">
                            <span class="title">Issue</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('famsIssueReturned/')}}">
                            <span class="title">Issue Return</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="">
                    <span class="title">Transfer</span>
                </a>
                <ul>
                    <li>
                        <a href="{{url('famsViewTransfer/')}}">
                            <span class="title">Transfer</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="">
                    <span class="title">Use</span>
                </a>
                <ul>
                    <li>
                        <a href="{{url('famsViewUse/')}}">
                            <span class="title">Use</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewFamsUseReturn/')}}">
                            <span class="title">Use Return</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{url('famsAdditionalCharge/')}}">
                    <span class="title">Additional Charge</span>
                </a>
            </li>
            <li>
                <a href="{{url('famsViewSale/')}}">
                    <span class="title">Sales</span>
                </a>
            </li>
            <li>
                        <a href="{{url('famsDep/')}}">
                            <span class="title">Depreciation</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('famsViewWriteOff/')}}">
                            <span class="title">Write Off</span>
                        </a>
                    </li>
        </ul>
    </li>

    <li>
        <a class="animated fadeInLeft" href="">
            <i class="fa fa-files-o" aria-hidden="true"></i>
            <span class="title">Reports</span>
        </a>
        <ul>
            <li>
                <a href="{{url('famsAssetRegisterReport/')}}">
                    <span class="title">Fixed Asset Register</span>
                </a>
            </li>
            <li>
                <a href="{{url('famsFixedAssetsScheduleReport/')}}">
                    <span class="title">Fixed Assets Schedule</span>
                </a>
            </li>
            <li>
                <a href="{{url('famsFixedAssetsDepReport/')}}">
                    <span class="title">Fixed Assets Depreciation Report</span>
                </a>
            </li>
            <li>
                <a href="{{url('famsFixedAssetsTransferReport/')}}">
                    <span class="title">Fixed Assets Transfer Report</span>
                </a>
            </li>
            <li>
                <a href="{{url('famsFixedAssetsSalesReport/')}}">
                    <span class="title">Fixed Assets Sales Report</span>
                </a>
            </li>
            <li>
                <a href="{{url('famsFixedAssetsWtiteOffReport/')}}">
                    <span class="title">Fixed Assets Write Off Report</span>
                </a>
            </li>
            <li>
                <a href="{{url('famsFixedAssetsPurchaseReport/')}}">
                    <span class="title">Fixed Assets Purchase Report</span>
                </a>
            </li>
            <li>
                <a href="{{url('famsRegisterUseReport/')}}">
                    <span class="title">Register Use Report</span>
                </a>
            </li>
            <li>
                <a href="{{url('famsFixedAssetsIdPrintReport/')}}">
                    <span class="title">Fixed Asset's ID Print Report</span>
                </a>
            </li>
        </ul>
    </li>
</ul>
<style type="text/css">
    #main-menu > li > ul >li > ul{
    left: 270px !important;
    top: -42px !important;
}
    #main-menu > li#navProduct > ul >li > ul{
    left: 268px !important;
    top: -26px !important;
}
    #main-menu > li#translation > ul >li > ul{
    left: 268px !important;
    top: -26px !important;
}

#main-menu >li > ul > li{
    height: 28px !important;
}
</style>
