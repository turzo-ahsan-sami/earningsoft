<ul id="main-menu" class="navbar-nav">

    <style type="text/css">

        #main-menu > li#settings > ul >li > ul {
            left: 268px !important;
            top: -26px !important;
        }

    </style>

    <style type="text/css">
    
    @media only screen and (max-width: 768px) {
    .mobile-submenuli{
       margin-left: 60px;
       z-index: 1000
}
    
}
</style>

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
                        <a href="{{ url('inv/viewHolidayCalender/') }}">
                            <span class="title">Holiday Calender</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('inv/viewGovHoliday/') }}">
                            <span class="title">Gov. Holiday</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('inv/viewOrgBranchSamityHoliday/') }}">
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
                        <a href="{{ url('inv/viewDivision/') }}">
                            <span class="title">Division</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('inv/viewDistrict/') }}">
                            <span class="title">District</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('inv/viewUpazila/') }}">
                            <span class="title">Thana / Upazila</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('inv/viewUnion/') }}">
                            <span class="title">Union</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('inv/viewVillage/') }}">
                            <span class="title">Village</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('inv/viewWorkingArea') }}">
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
                <a href="{{url('inv/posHrEmployeeList/')}}">
                    <span class="title">Employee Information</span>
                </a>
            </li>
            <li>
                <a href="< url('inv/transfer')?>">
                    <span class="title">Transfer</span>
                </a>
            <li>
                <a href="< url('inv/resignInfo')?>">
                    <span class="title">Resign</span>
                </a>
            </li>
            <li>
                <a href="< url('inv/terminateInfo')?>">
                    <span class="title">Terminate</span>
                </a>
            </li>
        </ul>
    </li> --}}

    {{-- =========================================END EMPLOYEE========================================= --}}

    <li id="product">
        <a class="animated fadeInLeft" href="">
            <i class="linecons-cog"></i>
            <span class="title">Product</span>
        </a>
        <ul>
            <li>
                <a href="">
                    <span class="title">Product setting</span>
                </a>
                <ul>
                    <li>
                        <a href="{{url('viewProduct/')}}">
                            <span class="title">Product</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewProductGroup/')}}">
                            <span class="title">Group</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewProductCategory/')}}">
                            <span class="title">Category</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewProductSubCategory/')}}">
                            <span class="title">Product Subcategory</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewProductBrand/')}}">
                            <span class="title">Product Brand</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewProductModel/')}}">
                            <span class="title">Product Model</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewProductSize/')}}">
                            <span class="title">Product Size</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewProductColor/')}}">
                            <span class="title">Product Color</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewProductUom/')}}">
                            <span class="title">Product UOM</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    <li id="translation">
        <a class="animated fadeInLeft" href="">
            <i class="linecons-cog"></i>
            <span class="title">Transaction</span>
        </a>
        <ul>
            <li>
                <a href="">
                    <span class="title">Requisition</span>
                </a>
                <ul>
                    {{-- <li>
                        <a href="{{url('viewInvEmpRequiItem/')}}">
                            <span class="title">Employee Requisition</span>
                        </a>
                    </li> --}}
                    <li>
                        <a href="{{url('viewInvBrnRequiItem/')}}">
                            <span class="title">Branch Requisition</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="">
                    <span class="title">Purchase</span>
                </a>
                <ul>
                    <li>
                        <a href="{{url('viewInvPurchaseList/')}}">
                            <span class="title">Purchase</span>
                         </a>
                    </li>
                    <li>
                        <a href="{{url('viewInvPurchaseReturnList/')}}">
                            <span class="title">Purchase Return</span>
                         </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="">
                    <span class="title">Issue</span>
                </a>
                <ul>
                <?php if($gnrBranchId==1 || $branchName=='Head Office'): ?>
                    <li>
                        <a href="{{url('viewIssue/')}}">
                            <span class="title">Issue</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($gnrBranchId!==1 || $branchName!=='Head Office'): ?>
                    <li>
                        <a href="{{url('viewInvissueReturn/')}}">
                            <span class="title">Issue Return</span>
                        </a>
                    </li>
                <?php endif; ?>
                </ul>
            </li>
            <?php if($gnrBranchId!==1 || $branchName!=='Head Office'): ?>
            <li>
                <a href="">
                    <span class="title">Transfer</span>
                </a>
                <ul>
                    <li>
                        <a href="{{url('transfer/')}}">
                            <span class="title">Transfer</span>
                        </a>
                    </li>

                </ul>
            </li>
            <?php endif; ?>
            <li>
                <a href="">
                    <span class="title">Use</span>
                </a>
                <ul>
                    <li>
                        <a href="{{url('viewUse/')}}">
                            <span class="title">Use</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('viewUseReturn/')}}">
                            <span class="title">Use Return</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    <li id="report">
        <a class="animated fadeInLeft" href="">
        <i class="linecons-cog"></i>
            <span class="title">Reports</span>
        </a>
        <ul>
            <li>
                <a href="">
                    <span class="title">Stock</span>
                </a>
                <ul>
                    <?php if($gnrBranchId==1 || $branchName=='Head Office'): ?>
                        <li>
                            <a href="{{url('invStockReport/')}}">
                                <span class="title">Stock</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="{{url('invBranchStockReport/')}}">
                            <span class="title"><?php if($gnrBranchId==1 || $branchName=='Head Office'): ?> Branch <?php endif; ?>  Stock</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <ul>
            <li>
                <a href="">
                    <span class="title">Stock</span>
                </a>
                <ul>
                    <?php if($gnrBranchId==1 || $branchName=='Head Office'): ?>
                        <li>
                            <a href="{{url('invStockReport/')}}">
                                <span class="title">Head Office Stock</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="{{url('invBranchStockReport/')}}">
                            <span class="title"><?php if($gnrBranchId==1 || $branchName=='Head Office'): ?> Branch <?php endif; ?>  Stock</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="">
                    <span class="title">Stock Amount</span>
                </a>
                <ul>
                    <?php if($gnrBranchId==1 || $branchName=='Head Office'): ?>
                        <li>
                            <a href="{{url('invStockAmountReport/')}}">
                                <span class="title">Head Office Stock Amount</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="{{url('invBranchStockAmountReport/')}}">
                            <span class="title"><?php if($gnrBranchId==1 || $branchName=='Head Office'): ?> Branch <?php endif; ?>  Stock Amount</span>
                        </a>
                    </li>
                </ul>
            </li>


             <li>

             <a href="">
                    <span class="title">Stock Purchase Report</span>
                </a>

               <ul>
                    <?php if($gnrBranchId==1 || $branchName=='Head Office'): ?>

                    <?php endif; ?>
                    <li>
                        <a href="{{url('invPurchaseReport/')}}">
                            <span class="title"><?php if($gnrBranchId==1 || $branchName=='Head Office'): ?> Purchase Report<?php endif; ?></span>
                        </a>
                    </li>

                </ul>
                <ul>
                    <?php if($gnrBranchId==1 || $branchName=='Head Office'): ?>

                    <?php endif; ?>
                    <li>
                        <a href="{{url('invPurchaseDetailsReport/')}}">
                <span class="title"><?php if($gnrBranchId==1 || $branchName=='Head Office'): ?> Purchase Details Report <?php endif; ?> </span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="">
                    <span class="title">Use Report</span>
                </a>
                <ul>
                    <li>
                        <a href="{{url('viewUseReport/')}}">
                            <span class="title">Use Report</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="">
                    <span class="title">Issue Report</span>
                </a>
                <ul>
                    <li>
                        <a href="{{url('viewIssueReport/')}}">
                            <span class="title">Issue Report</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{url('viewIssueDetailsReport/')}}">
                            <span class="title">Issue Details Report</span>
                        </a>
                    </li>

                </ul>
            </li>
        </ul>
    </li>

</ul>

<style type="text/css">
    #main-menu > li > ul >li > ul{
    left: 270px !important;
    top: -42px !important;
}

    #main-menu > li#product > ul >li > ul{
     left: 268px !important;
     top: -26px !important;
}
    #main-menu > li#translation > ul >li > ul{
    left: 268px !important;
    top: -26px !important;
}

    #main-menu > li#report > ul >li > ul{
    left: 268px !important;
    top: -26px !important;
}

#main-menu >li > ul > li{
    height: 26px !important;
}
</style>
<style type="text/css">
    #main-menu li {
    padding: 0px  20px 0px 0px!important;
    }
</style>
