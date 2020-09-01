<?php
use App\Traits\GetSoftwareDate;
?>
<ul id="main-menu" class="navbar-nav">
    <li id="config">
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Configuration</span>
        </a>
        <ul>
            @if (Auth::user()->branchId==1)
            <li>
                <a href="javascript:;">
                    <span class="title">General Configuration</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('viewGeneralConfiguration') }}">
                            <span class="title">General Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewSamityConfiguration/') }}">
                            <span class="title">Samity Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewOperationalPolicy/') }}">
                            <span class="title">Operational Policy</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewDailyWeeklyConfiguration/') }}">
                            <span class="title">Daily & Weekly Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewDayEndConfiguration/') }}">
                            <span class="title">Day End Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewCollectionSheetConfiguration/') }}">
                            <span class="title">CollectionSheet Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewSavingsConfiguration/') }}">
                            <span class="title">Savings Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewReportSignature/') }}">
                            <span class="title">HO Level Report Signature</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="title">Member Configuration</span>
                        </a>
                        <ul id="member-configuration">

                            <li>
                                <a href="{{ url('viewMemberSetting/') }}">
                                    <span class="title">Member Setting</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewMemberType/') }}">
                                    <span class="title">Member Type</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewPrimaryProduct/') }}">
                                    <span class="title">Primary Product</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewMaritalStatus/') }}">
                                    <span class="title">Marital Status</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewRelationship/') }}">
                                    <span class="title">Relationship</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewProfession/') }}">
                                    <span class="title">Profession</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewDesignation/') }}">
                                    <span class="title">Designation</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    {{-- <li>
                        <a href="javascript:;">
                            <span class="title">Savings Configuration</span>
                        </a>
                        <ul>
                            <li>
                                <a href="{{url('viewMfnSavingsDepositType/')}}">
                                    <span class="title">Savings Deposit Type</span>
                                </a>

                            </li>
                            <li>
                                <a href="{{url('viewMfnSavingsCollectionFrequency/')}}">
                                    <span class="title">Savings Collection Frequency</span>
                                </a>

                            </li>
                            <li>
                                <a href="{{url('viewMfnSavingsInterestCalFrequency/')}}">
                                    <span class="title">Savings Interest Calculation Frequency</span>
                                </a>

                            </li>
                            <li>
                                <a href="{{url('viewMfnSavingsInterestCalMethod/')}}">
                                    <span class="title">Savings Interest Calculation Method</span>
                                </a>

                            </li>
                            <li>
                                <a href="{{url('viewMfnSavingsMonthlyCollectionType/')}}">
                                    <span class="title">Savings Monthly Collection Type</span>
                                </a>

                            </li>
                        </ul>
                    </li> --}}
                    <li>
                        <a href="javascript:;">
                            <span class="title">Loan Configuration</span>
                        </a>
                        <ul id="loanconfiguration">
                            <li>
                                <a href="{{ url('viewLoanConfigurationSetting/') }}">
                                    <span class="title">Loan Configuration Setting</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewLoanPurposeCategory/') }}">
                                    <span class="title">Loan Purpose Category</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewLoanPurpose/') }}">
                                    <span class="title">Loan Purposes</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewLoanSubPurpose/') }}">
                                    <span class="title">Loan Sub Purposes</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewLoanInterestCalculationMethod/') }}">
                                    <span class="title">Loan Interest Calculation Method</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewLoanRepayPeriod/') }}">
                                    <span class="title">Loan Repay Period</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewSettingsCategoryType/') }}">
                                    <span class="title">Category Type</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewFundingOrganizationList/') }}">
                                    <span class="title">Funding Organization</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewYearEligibleWriteOffList/') }}">
                                    <span class="title">Years to Eligible Write-Off</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewInsuranceCalculateMethodList/') }}">
                                    <span class="title">Insurance Calculation Method</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewRepaymentFrequencyList/') }}">
                                    <span class="title">Repayment Frequency</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewInterestPaymentList/') }}">
                                    <span class="title">Interest Payment Mode</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewMonthlyRepaymentModeList/') }}">
                                    <span class="title">Mode of Monthly Repayment</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewExtraOptionList/') }}">
                                    <span class="title">Extra Options</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewGracePeriodList/') }}">
                                    <span class="title">Grace Period</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{url('viewTransferConfiguration/')}}">
                            <span class="title">Transfer Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="title">Holiday Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="title">User Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="title">Day End Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="title">Month End Configuration</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="javascript:;">
                    <span class="title">Product Configuration</span>
                </a>
                <ul>
                    <li>
                        <a href="javascript:;">
                            <span class="title">Savings Product</span>
                        </a>
                        <ul id="savingsproduct">
                            <li>
                                <a href="{{url('viewMfnSavingsProductList/')}}">
                                    <span class="title">Savings Product</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="title">Loan Product</span>
                        </a>
                        <ul id="loanproduct">
                            <li>
                                <a href="{{ url('viewLoanProductList/') }}">
                                    <span class="title">Loan Product Type</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewLoanProductCategory/') }}">
                                    <span class="title">Loan Product Category</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('mfn/viewLoanProduct/') }}">
                                    <span class="title">Loan Products</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <span class="title">Report Configuration</span>
                </a>
                <ul>
                    <li>
                        <a href="{{url('viewReportConfiguration/')}}">
                            <span class="title">Report Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="title">Organizational Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="title">Collection Sheet</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="title">Signature</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            <li>
                <a href="javascript:;">
                    <span class="title">Address</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mfn/viewDivision/') }}">
                            <span class="title">Division</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/viewDistrict/') }}">
                            <span class="title">District</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/viewUpazila/') }}">
                            <span class="title">Thana / Upazila</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/viewUnion/') }}">
                            <span class="title">Union</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/viewVillage/') }}">
                            <span class="title">Village</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/viewWorkingArea') }}">
                            <span class="title">Working Area</span>
                        </a>
                    </li>
                </ul>
            </li>
            @if (Auth::user()->branchId==1)

            <li>
                <a href="javascript:;">
                    <span class="title">Holiday</span>
                </a>
                <ul>
                    <li >
                        <a class="holidaySubLink" href="{{ url('mfn/viewHolidayCalender/') }}">
                            <span class="title">Holiday Calender</span>
                        </a>
                    </li>
                    <li >
                        <a class="holidaySubLink" href="{{ url('mfn/viewGovHoliday/') }}">
                            <span class="title">Fixed Gov. Holiday</span>
                        </a>
                    </li>

                    <li >
                        <a class="holidaySubLink" href="{{ url('mfn/viewOrgBranchSamityHoliday/') }}">
                            <span class="title">Org./Branch/Samity Holiday</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="{{ url('viewBranchProduct/') }}">
                    <span class="title">Product Assign</span>
                </a>
            </li>
            <li>
                <a href="{{ url('viewEmployeePosition/') }}">
                    <span class="title">Employee Designation</span>
                </a>
            </li>
            @endif

            {{-- this section is temporaryly done to update savings information from head office, it will be removed adfer tasks done --}}
            @if (GetSoftwareDate::getOpeningInformationActive()!=1 && Auth::user()->id==1)
            <li>
                <a href="{{ url('viewMfnOpeningSavingsBalance/') }}">
                    <span class="title">Savings Opening Balance</span>
                </a>
            </li>
            @endif
            {{-- end this section is temporaryly done to update savings information from head office, it will be removed adfer tasks done --}}

            <?php if(GetSoftwareDate::getOpeningInformationActive()==1 || Auth::user()->id==1): ?>
            <li>
                <a href="">
                    <span class="title">Opening Balance</span>
                </a>
                <ul>
                    <li>
                        <a href="">
                            <span class="title">Members</span>
                        </a>
                        <ul id="members">
                            <li>
                                <a href="{{ url('addOpeningBalanceMember/') }}">
                                    <span class="title">Add Member</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="">
                            <span class="title">Loan</span>
                        </a>
                        <ul  id="loan">
                            <li>
                                <a href="{{ url('addOpeningBalanceLoan/') }}">
                                    <span class="title">Add Regular Loan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('addOpeningBalanceOneTimeLoan/') }}">
                                    <span class="title">Add One Time Loan</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="">
                            <span class="title">Savings</span>
                        </a>
                        <ul id="savings">
                            <li>
                                <a href="{{ url('viewMfnOpeningSavingsAccounts/') }}">
                                    <span class="title">Accounts</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('viewMfnOpeningSavingsBalance/') }}">
                                    <span class="title">Balance</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <?php endif; ?>
            <?php //if(GetSoftwareDate::getOpeningInformationActive()==1): ?>
            @if (GetSoftwareDate::getOpeningInformationActive()==1 || Auth::user()->id==1)
            <li>
                <a href="">
                    <span class="title">Opening Information</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mfn/branchOpeningLoanInformation/') }}">
                            <span class="title">Loan Information</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/branchOpeningSavingsInformation/') }}">
                            <span class="title">Savings Information</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/branchOpeningMemberSamityInformation/') }}">
                            <span class="title">Member & Samity Information</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/branchOpeningTotalSamityInformationList/') }}">
                            <span class="title">Samity Info (Funding Org.)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/branchOpeningCategorySamityInformation/') }}">
                            <span class="title">Samity Info (Category)</span>
                        </a>
                    </li>

                </ul>
            </li>
            @endif
            <?php //endif; ?>
            <li>
                <a class="animated fadeInLeft" href="javascript:;">
                    <span class="title">MRA Report Information</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mraReportInformationList/') }}">
                            <span class="title">MRA Report Information List</span>
                        </a>
                    </li>
                </ul>
            </li>
            @if (Auth::user()->username=='superadmin' || Auth::user()->username=='00007-002-00006-0708')
            <li>
                <a href="javascript:;">
                    <span class="title">Staff Info</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mfn/pksfPomisThreeStaffReportLoanProduct/') }}">
                            <span class="title">Staff Info Product Wise</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/pksfPomisThreeStaffReportLoanProductCategory/') }}">
                            <span class="title">Staff Info Loan Category Wise</span>
                        </a>
                    </li>
                </ul>
            </li>


          {{--   <li>
                <a href="javascript:;">
                    <span class="title">Loan Purpose</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mfn/pksfPomisThreeStaffReportLoanProduct/') }}">
                            <span class="title">Loan Purpose Category</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/pksfPomisThreeStaffReportLoanProductCategory/') }}">
                            <span class="title">Loan Purpose</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('mfn/pksfPomisThreeStaffReportLoanProductCategory/') }}">
                            <span class="title">Loan Sub Purpose</span>
                        </a>
                    </li>
                </ul>
            </li> --}}
            @endif
        </ul>
    </li>
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Samity</span>
        </a>
        <ul>
            <li>
                <a href="{{ url('viewSamity/') }}">
                    <span class="title">Samity</span>
                </a>
            </li>
            <li>
                <a href="">
                    <span class="title">Samity  Groups</span>
                </a>
            </li>
            <li>
                <a href="">
                    <span class="title">Samity Sub-Groups</span>
                </a>
            </li>
            <li>
                <a href="javascript:;">
                    <span class="title">Samity Transfer</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('viewSamityTransfer/') }}">
                            <span class="title">Branch Samity Transfer</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="title">Member Samity Transfer</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ url('viewSamityFieldOfficerChange/') }}">
                    <span class="title">Samity Field Officer Change</span>
                </a>
            </li>
            <li>
                <a href="{{ url('viewSamityDayChange/') }}">
                    <span class="title">Samity Day Change</span>
                </a>
            </li>
            <li>
                <a href="{{ url('viewSamityClosing/') }}">
                    <span class="title">Samity Closing</span>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Employee</span>
        </a>
        <ul>
            <li>
                <a href="{{url('mfn/posHrEmployeeList/')}}">
                    <span class="title">Employee Information</span>
                </a>
            </li>
            <li>
                <a href="<?= url('mfn/transfer')?>">
                    <span class="title">Transfer</span>
                </a>
            <li>
                <a href="<?= url('mfn/resignInfo')?>">
                    <span class="title">Resign</span>
                </a>
            </li>
            <li>
                <a href="<?= url('mfn/terminateInfo')?>">
                    <span class="title">Terminate</span>
                </a>
            </li>
        </ul>
    </li>

    @if (GetSoftwareDate::getOpeningInformationActive()!=1)

    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Members</span>
        </a>
        <ul>
            <li>
                <a href="{{ url('viewMember/') }}">
                    <span class="title">Member Information</span>
                </a>
            </li>
            <li>
                <a href="{{ url('viewMemberSamityTransfer/') }}">
                    <span class="title">Member Samity Transfer</span>
                </a>
            </li>
            <li>
                <a href="{{ url('viewMemberPrimaryProductTransfer/') }}">
                    <span class="title">Member Primary Product Transfer</span>
                </a>
            </li>
            <li>
                <a href="{{ url('viewMemberClosing/') }}">
                    <span class="title">Member Closing</span>
                </a>
            </li>
            @if(Auth::user()->id==1 || Auth::user()->id==57)
            <li>
                <a href="{{ url('memberInactiveList/') }}">
                    <span class="title">Inactive Member List</span>
                </a>
            </li>
            @endif

        </ul>
    </li>
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Savings</span>
        </a>
        <ul>
            <li>
                <a href="{{url('addMfnWeeklySavingsAccount/')}}">
                    <span class="title">Weekly Savings Account</span>
                </a>
            </li>

            <li>
                <a href="{{url('viewMfnSavingsAccountList/')}}">
                    <span class="title">Savings Account</span>
                </a>
            </li>

            <li>
                <a href="{{url('viewMfnSavingsDeposit/')}}">
                    <span class="title">Savings Deposit</span>
                </a>

            </li>
            <li>
                <a href="{{url('viewMfnSavingsWithdraw/')}}">
                    <span class="title">Savings Withdraw</span>
                </a>

            </li>
            <li>
                <a href="{{url('viewMfnSavingsClosing/')}}">
                    <span class="title">Savings Closing</span>
                </a>

            </li>
            <li>
                <a href="{{url('viewMfnSavingsStatus/')}}">
                    <span class="title">Savings Status</span>
                </a>

            </li>
            <li>
                <a href="{{url('viewMfnSavingsSamityList/')}}">
                    <span class="title">Savings Interest Calculation</span>
                </a>

            </li>


        </ul>
    </li>
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Loans</span>
        </a>
        <ul>
            <li>
                <a href="{{url('mfn/viewRegularLoan/')}}">
                    <span class="title">Regular Loan Accounts</span>
                </a>
            </li>
            <li>
                <a href="{{url('mfn/viewOneTimeLoan/')}}">
                    <span class="title">One Time Loan Accounts</span>
                </a>
            </li>
            <li>
                <a href="{{ url('viewMfnRegularLoanTransaction/') }}">
                    <span class="title">Regular Loan Transactions</span>
                </a>
            </li>
            <li>
                <a href="{{ url('viewMfnOneTimeLoanTransaction/') }}">
                    <span class="title">One Time Loan Transactions</span>
                </a>
            </li>
            <li>
                <a href="{{ url('viewMfnDueLoanTransaction/') }}">
                    <span class="title">Due Loan Transactions</span>
                </a>
            </li>
            <li>
                <a href="{{url('viewLoanSchedule/')}}">
                    <span class="title">Loan Reschedule</span>
                </a>
            </li>
            <li>
                <a href="{{url('viewMfnLoanWaiver/')}}">
                    <span class="title">Loan Waiver for Death Members</span>
                </a>
            </li>
            <li>
                <a href="{{url('viewMfnLoanRebates/')}}">
                    <span class="title">Loan Rebate</span>
                </a>
            </li>
            <li>
                <a href="{{url('mfn/viewLoanWriteOffEligibleList/')}}">
                    <span class="title">Loan Write Off Eligible List</span>
                </a>
            </li>
            <li>
                <a href="{{url('mfn/viewWriteOff/')}}">
                    <span class="title">Loan Write Off</span>
                </a>
            </li>
            <li>
                <a href="{{url('mfn/viewWriteOffCollection/')}}">
                    <span class="title">Loan Write Off Collection</span>
                </a>
            </li>
        </ul>
    </li>

    @endif {{-- end if for opening date --}}
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Process</span>
        </a>
        <ul>
            @if (GetSoftwareDate::getOpeningInformationActive()!=1)
            <li>
                <a href="{{url('viewMfnAutoProcess/')}}">
                    <span class="title">Auto Process</span>
                </a>
            </li>
            <li>
                <a href="{{url('viewMfnProcessTraAuthorization/')}}">
                    <span class="title">Transaction Authorization</span>
                </a>
            </li>
            <li>
                <a href="{{url('viewMfnProcessTraUnauthorization/')}}">
                    <span class="title">Transaction Unauthorization</span>
                </a>
            </li>
            @endif
            <li>
                <a href="{{url('viewMfnDayEndProcess/')}}">
                    <span class="title">Day End Process</span>
                </a>
            </li>
            <li>
                <a href="{{url('viewMfnMonthEndProcess/')}}">
                    <span class="title">Month End Process</span>
                </a>
            </li>
            <li>
                <a href="{{url('PassBookBalance')}}">
                    <span class="title">Pass Book Balance</span>
                </a>
            </li>
            {{-- <li>
                <a href="">
                    <span class="title">Test</span>
                </a>
                <ul>
                    <li>
                        <a href="{{url('viewInvEmpRequiItem/')}}">
                            <span class="title">Test</span>
                        </a>
                    </li>
                </ul>
            </li> --}}

        </ul>
    </li>

    <!-- ==============================================Starts Reports Nav============================================== -->

    <li id="report-menu">
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Reports</span>
        </a>
        <ul>
            <li>
                <a href="javascript:;">
                    <span class="title">1. PKSF-POMIS Reports</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('./mfn/pksfPomisReport/pomis1') }}">
                            <span class="title">1.1 PKSF POMIS-1 Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/pksfPomisReport/pomis2') }}">
                            <span class="title">1.2 PKSF POMIS-2 Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/pksfPomisReport/pomis2a') }}">
                            <span class="title">1.3 PKSF POMIS-2A Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/pksfPomisReport/pomis3') }}">
                            <span class="title">1.4 PKSF POMIS-3 Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('pksfPomis3AReport') }}">
                            <span class="title">1.5 PKSF POMIS-3(A) (Half Yearly) Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/pksfPomisReport/pomis5') }}">
                            <span class="title">1.6 PKSF POMIS-5(A) Report</span>
                        </a>
                    </li>
                    {{--
                    <li>
                        <a href="{{ url('mfnReport/1.4') }}">
                            <span class="title">PKSF POMIS-3 Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/1.5') }}">
                            <span class="title">PKSF POMIS-3A Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/1.6') }}">
                            <span class="title">PKSF POMIS-5a Report</span>
                        </a>
                    </li> --}}
                </ul>
            </li>

         {{--    <li>
                <a href="javascript:;">
                    <span class="title">2 PRA Reports</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mfn/pra/pra1') }}">
                            <span class="title">2.1 PRA-1</span>
                        </a>
                    </li>
                </ul>
            </li> --}}

            <li>
                <a href="javascript:;">
                    <span class="title">2. MRA Reports</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mraPeriodInformationReport') }}">
                            <span class="title">2.1 MRA DBMS-1</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('mraDbms2Report') }}">
                            <span class="title">2.2 MRA DBMS-2</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('MraMfiThreeAReport') }}">
                            <span class="title">2.3 MRA DBMS-3</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('MraDBMSFourA') }}">
                            <span class="title">2.4 MRA DBMS-4(A)</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('MraDBMSFourB') }}">
                            <span class="title">2.5 MRA DBMS-4(B)</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('MraDBMSFourC') }}">
                            <span class="title">2.6 MRA DBMS-4(C)</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('MraDBMSFourD') }}">
                            <span class="title">2.7 MRA DBMS-4(D)</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('MraDBMSFourE') }}">
                            <span class="title">2.8 MRA DBMS-4(E)</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('mfn/mraReport/dbms5') }}">
                            <span class="title">2.9 DBMS-5</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('mfn/mraReport/dbms6') }}">
                            <span class="title">2.10 DBMS-6</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('underPreparationReport') }}">
                            <span class="title">2.11 DBMS-7 (Yearly)</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('underPreparationReport') }}">
                            <span class="title">2.12 DBMS-8 (Yearly)</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('mfn/mraReport/mraLlpSixReport') }}">
                            <span class="title">2.16 MRA-LLP-06</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ url('mfn/mraReport/mraMonthlyReport') }}">
                            <span class="title">2.17 MRA Monthly & Half Yearly Report</span>
                        </a>
                    </li>
                </ul>
            </li>
            {{-- <li>
                <a href="javascript:;">
                    <span class="title">MRA Reports</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mfnReport/2.1') }}">
                            <span class="title">MRA-MFI-01</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.2') }}">
                            <span class="title">MRA-MFI-02</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.3') }}">
                            <span class="title">MRA-MFI-03/A</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.4') }}">
                            <span class="title">MRA-MFI-03/B</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.5') }}">
                            <span class="title">MRA-MFI-04/A</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.6') }}">
                            <span class="title">MRA-MFI-04/B</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.7') }}">
                            <span class="title">MRA-MFI-05</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.8') }}">
                            <span class="title">MRA-MFI-06</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.9') }}">
                            <span class="title">MRA-CDB-02/A</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.10') }}">
                            <span class="title">MRA-CDB-03/A</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.11') }}">
                            <span class="title">MRA-LLP-01</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.12') }}">
                            <span class="title">MRA-LLP-02</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.13') }}">
                            <span class="title">MRA-LLP-03</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.14') }}">
                            <span class="title">MRA-LLP-04</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.15') }}">
                            <span class="title">MRA-LLP-05</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.16') }}">
                            <span class="title">MRA-LLP-06</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/2.17') }}">
                            <span class="title">MRA-Monthly Report</span>
                        </a>
                    </li>

                </ul>
            </li> --}}
            <li>
                <a href="javascript:;">
                    <span class="title">3. Regular & General Report - (Branch Level)</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('dailyCollectionReport') }}">
                            <span title="title">3.1 Daily Collection Component Wise</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('underPreparationReport') }}">
                            <span title="title">3.2 Unauthorized Daily Recoverable & Collection Component Wise</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('BranchManagerReport') }}">
                            <span class="title">3.3 Branch Manager Report (Field Officer & Component Wise)
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfn/regularNgeneral/creditOfficerReport') }}">
                            <span class="title">3.4 Credit Officer Report (Samity & Component Wise)
                            </span>
                        </a>
                    </li>
                   {{--  <li>
                        <a href="{{ url('underPreparationReport') }}">
                            <span class="title">3.5 Loan Classification
                            </span>
                        </a>
                    </li> --}}
                    <li>
                        <a href="{{ url('mfn/regularNGeneralReports/loanClassificationReport') }}">
                            <span title="title">3.5 Loan Classification Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('loanNSavingCollection/') }}">
                            <span class="title">3.6 Samity Wise Monthly Loan & Saving Basic Collection Sheet</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnPeriodicCollectionComponentReport') }}">
                            <span class="title">3.7 Periodic Collection Component Wise</span>
                        </a>
                    </li>
                    {{-- <li>
                        <a href="{{ url('dailyRecoverableReportMemberWise/') }}">
                            <span title="title">3.13 Daily Recoverable Report (Member Wise)</span>
                        </a>
                    </li> --}}
                    <li>
                        <a href="{{ url('memberLedgerReport') }}">
                            <span title="title">3.8 Member Ledger Report</span>
                        </a>
                    </li>
                    <!--<li>
                        <a href="{{ url('mfnReport/3.1') }}">
                            <span class="title"> Daily Collection Component Wise</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/3.2') }}">
                            <span class="title"> Unauthorized Daily Recoverable & Collection Component Wise</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/3.3') }}">
                            <span class="title">Branch Managers report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/3.4') }}">
                            <span class="title">Field Officer Report  (Samity & Component Wise)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/3.6') }}">
                            <span class="title"> Loan Classification & DMR</span>
                        </a>
                    </li>-->

                    <!--<li>
                        <a href="{{ url('mfnReport/3.8') }}">
                            <span class="title"> Samity Wise Monthly Loan & Saving Collection Sheet</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/3.9') }}">
                            <span class="title"> Samity Wise Monthly Loan & Saving Working Sheet</span>
                        </a>
                    </li> -->


                    {{-- <li>
                        <a href="{{ url('mfnFieldOfficerReport') }}">
                            <span class="title">
                            Field Officer Report (Samity & Component Wise)
                            </span>
                        </a>
                    </li> --}}


                    {{-- <li>
                      <a href="{{ url('mfnFieldreport1_new') }}">
                        <span title="title">
                          Field Officer Report (Samity & Component Wise) #Atiq
                        </span>
                      </a>
                  </li> --}}


                     <!--<li>
                        <a href="{{ url('mfnReport/3.11') }}">
                            <span class="title"> Samity Wise Monthly Loan & Saving Basic Collection Sheet Print</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/3.12') }}">
                            <span class="title"> Monthly Loan & Savings Collection Sheet</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/3.13') }}">
                            <span class="title"> Daily Recoverable Report (Member Wise)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/3.14') }}">
                            <span class="title">Manual Loan & Savings Collection Sheet</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/3.15') }}">
                            <span class="title"> Write Off Collection Sheet</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/3.16') }}">
                            <span class="title">Consolidated Branch Manager Report</span>
                        </a>
                    </li>-->
                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="title">4. Register Report - (Branch Level)</span>
                </a>
                <ul>
                    <li>
                        <a href="#">
                            <span class="title">4.1 Regular</span>
                        </a>
                        <ul  id="register">
                            <li>
                                <a href="{{ url('mfn/registerReport/dueRegister') }}">
                                    <span class="title">4.1.1 Due Register</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('DueCollectionRegister') }}">
                                    <span class="title"> 4.1.2 Due Collection Register </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('mfn/registerReport/memberCencellationRegister') }}">
                                    <span class="title"> 4.1.3 Cencellation Register </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('DualLoneeInformation') }}">
                                    <span class="title"> 4.1.4 Dual Loanee Information </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('TransferRegisterReport') }}">
                                    <span class="title"> 4.1.5 Transfer Register </span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ url('SavingsCollectionRegisterReport') }}">
                                    <span class="title"> 4.1.6 Savings Collection Register </span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ url('mfn/registerReport/savingInterestInformationRegister') }}">
                                    <span class="title"> 4.1.7 Saving Interest Information Report(Financial Year) </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('underPreparationReport') }}">
                                    <span class="title"> 4.1.8 Saving Interest Information Report(On Closing) </span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ url('underPreparationReport') }}">
                                    <span class="title"> 4.1.9 Saving Provision Information Report(Member Wise) </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('dailyRecoverableAndCollectionRegister') }}">
                                    <span class="title"> 4.1.10 Daily Recoverable and Collection Register </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('LoanWaiverReportForDeathMembers') }}">
                                    <span class="title"> 4.1.11 Loan Waiver Report For Death Members </span>
                                </a>
                            </li>






                        </ul>
                    </li>



                    <li>
                        <a href="#">
                            <span class="title">4.2 Topsheet</span>
                        </a>
                        <ul  id="register">
                            <li>
                                <a href="{{ url('mfn/registerReport/dueRegister') }}">
                                    <span class="title">4.2.1 Due Register (Topsheet)</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('mfn/registerReport/admissionRegister') }}">
                                    <span class="title">4.2.2 Admission Register</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ url('mfn/registerReport/savingRefundRegister') }}">
                                    <span class="title"> 4.2.3 Savings Refund Register </span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ url('mfn/registerReport/loanDisbursementRegister') }}">
                                    <span class="title"> 4.2.4 Loan Disbursement Register Register </span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ url('mfn/registerReport/fullyPaidLoanRegister') }}">
                                    <span class="title"> 4.2.5 Fully Paid Loan Register </span>
                                </a>
                            </li>





                        </ul>
                    </li>
                </ul>
            </li>




            <li>
              <a href="javascript:;">
                  <span class="title">5. Pass Book Balancing Register Report</span>
              </a>
              <ul>
                  <li>
                      <a href="{{ url('PassBookBalancingRegisterReport') }}">
                          <span title="title">5.1 Member wise Pass book Balancing Register Report</span>
                      </a>
                  </li>

              </ul>

              <ul>
                  <li>
                      <a href="{{ url('CreditOfficeWisePassBookBalancingRegisterReport') }}">
                          <span title="title">5.2 Credit Officer wise Pass book Balancing Register Report</span>
                      </a>
                  </li>

              </ul>

              <ul>
                  <li>
                      <a href="{{ url('BranchWisePassBookBalancingRegisterReport') }}">
                          <span title="title">5.3 Branch wise Pass book Balancing Register Report</span>
                      </a>
                  </li>

              </ul>

              <ul>
                  <li>
                      <a href="{{ url('PassBookCheckingReports') }}">
                          <span title="title">5.4 Pass book checking Report</span>
                      </a>
                  </li>

              </ul>
          </li>


          <li>
              <a href="javascript:;">
                  <span class="title">6. Monthly Report</span>
              </a>
              <ul>
                  <li>
                      <a href="{{ url('monthlyTargetsAndAchievementReport') }}">
                          <span title="title">6.1 Monthly Targets And Achievement Report</span>
                      </a>
                  </li>

              </ul>
              <ul>
                  <li>
                      <a href="{{ url('underPreparationReport') }}">
                          <span title="title">6.2 Monthly Report Member Security Fund</span>
                      </a>
                  </li>

              </ul>
              <ul>
                  <li>
                      <a href="{{ url('DistrictAndUpazilaWiseCumulativeLoanDisbursementReportForm') }}">
                          <span title="title">6.3 District and Upazila wise cumulative loan disbursement report</span>
                      </a>
                  </li>

              </ul>
          </li>


          <li>
              <a href="javascript:;">
                  <span class="title">7. Agrosor Report</span>
              </a>
              <ul>
                  <li>
                      <a href="{{ url('MonthlyStatementAgroshorReport') }}">
                          <span title="title">7.1 Monthly statement agroshor reports(format-01)</span>
                      </a>
                  </li>

              </ul>
              <ul>
                  <li>
                      <a href="{{ url('AgroshorActivities') }}">
                          <span title="title">7.2 Half Yearly Purpose Wise Report of Agroshor Activities</span>
                      </a>
                  </li>

              </ul>
              <ul>
                  <li>
                      <a href="{{ url('AgrasorEnterpreneur') }}">
                          <span title="title">7.3 Half Yearly Statement of Employment Created by Agrasor Enterpreneur (Currrent Members)</span>
                      </a>
                  </li>

              </ul>
              <ul>
                  <li>
                      <a href="{{ url('AgrasorEnterpreneurDropOut') }}">
                          <span title="title">7.4 Half Yearly Statement of Employment Created by Agrasor Enterpreneur (Dropout Members)</span>
                      </a>
                  </li>

              </ul>
              <ul>
                  <li>
                      <a href="{{ url('AgrasorEnterpreneurCurrentRegister') }}">
                          <span title="title">7.5 Half Yearly Register of Employment Created by Agrasor Enterpreneur (Currrent Members)</span>
                      </a>
                  </li>

              </ul>
              <ul>
                  <li>
                      <a href="{{ url('AgrasorEnterpreneurDropoutRegister') }}">
                          <span title="title">7.6 Half Yearly Register of Employment Created by Agrasor Enterpreneur (Dropout Members)</span>
                      </a>
                  </li>

              </ul>
          </li>



          <li>
              <a href="javascript:;">
                  <span class="title">8. PRA Reports</span>
              </a>
              <ul>
                  <li>
                      <a href="{{ url('underPreparationReport') }}">
                          <span title="title">8.1 Program Data</span>
                      </a>
                  </li>

              </ul>
              <ul>
                  <li>
                      <a href="{{ url('underPreparationReport') }}">
                          <span title="title">8.2 Financial Data</span>
                      </a>
                  </li>

              </ul>
              <ul>
                  <li>
                      <a href="{{ url('underPreparationReport') }}">
                          <span title="title">8.3 Balance Sheet</span>
                      </a>
                  </li>

              </ul>
              <ul>
                  <li>
                      <a href="{{ url('underPreparationReport') }}">
                          <span title="title">8.4 Income-Expenditure Statement</span>
                      </a>
                  </li>

              </ul>
              <ul>
                  <li>
                      <a href="{{ url('underPreparationReport') }}">
                          <span title="title">8.5 Reciept & Payment Statement</span>
                      </a>
                  </li>

              </ul>
              <ul>
                  <li>
                      <a href="{{ url('underPreparationReport') }}">
                          <span title="title">8.6 Imputed Cost</span>
                      </a>
                  </li>

              </ul>

              <ul>
                  <li>
                      <a href="{{ url('underPreparationReport') }}">
                          <span title="title">8.7 Basic Data</span>
                      </a>
                  </li>

              </ul>

              <ul>
                  <li>
                      <a href="{{ url('underPreparationReport') }}">
                          <span title="title">8.8 Upazilla Loan</span>
                      </a>
                  </li>

              </ul>

              <ul>
                  <li>
                      <a href="{{ url('underPreparationReport') }}">
                          <span title="title">8.9 Employment Creation</span>
                      </a>
                  </li>

              </ul>
          </li>

          <li>
            <a href="javascript:;">
                <span class="title">9. Consolidated Report</span>
            </a>
            <ul>
                <li>
                    <a href="{{ url('ConsolidatedBalancingReport') }}">
                        <span title="title">9.1 Consolidated Balancing Report (Branch wise)</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('mfn/ratioAnalysisStatement') }}">
                        <span title="title">9.2 Ratio Analysis Statement</span>
                    </a>
                </li>

            </ul>
        </li>

            <!--<li>
                <a href="javascript:;">
                    <span class="title">Register Report - (Branch Level)</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mfnReport/4.1') }}">
                            <span class="title">Admission register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.2') }}">
                            <span class="title">Savings refund register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.3') }}">
                            <span class="title">Loan disbursement register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.4') }}">
                            <span class="title">Fully paid loan register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.5') }}">
                            <span class="title">Member cancellation register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.6') }}">
                            <span class="title">Member Wise Subsidiary Loan and Savings Ledger</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.7') }}">
                            <span class="title">Inactive Member Register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.8') }}">
                            <span class="title">Saving Interest Information Report(Financial Year)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.9') }}">
                            <span class="title">Saving Interest Register Report(On Closing)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.10') }}">
                            <span class="title">Saving Interest Provision Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.11') }}">
                            <span class="title">Due Register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.12') }}">
                            <span class="title">Daily Recoverable and Collection Register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.13') }}">
                            <span class="title">Daily Recoverable and Collection Register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.14') }}">
                            <span class="title">Written Off Register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.15') }}">
                            <span class="title">Written Off Collection Register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.16') }}">
                            <span class="title">Dual Loanee Register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.17') }}">
                            <span class="title">Loan Waiver For Death Members</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.18') }}">
                            <span class="title">Rebate Register Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.19') }}">
                            <span class="title">Due Collection Register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.20') }}">
                            <span class="title">Loan Adjustment Resister Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.21') }}">
                            <span class="title">Transfer Register Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.22') }}">
                            <span class="title">Holiday Due Register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.23') }}">
                            <span class="title">Loan Disbursement & Recovery</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.24') }}">
                            <span class="title">Loan Proposal Register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/4.25') }}">
                            <span class="title">FDR Register</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <span class="title">Consolidated Report</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mfnReport/5.1') }}">
                            <span class="title">Consolidated Balancing</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/5.2') }}">
                            <span class="title">Ratio analysis statement</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/5.3') }}">
                            <span class="title">Consolidated ratio analysis</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ url('mfnReport/6') }}">
                    <span class="title">Pass Book Report</span>
                </a>
            </li>
            <li>
                <a href="{{ url('mfnReport/7') }}">
                    <span class="title">Branch Wise Samity List</span>
                </a>
            </li>
            <li>
                <a href="{{ url('mfnReport/8') }}">
                    <span class="title">Samity Wise Member List</span>
                </a>
            </li>-->

            <li>
                <a href="javascript:;">
                    <span class="title">10. Others</span>
                </a>
                <ul>
                  <li>
                    <a href="{{ url('mfn/memberMigrationBalance/') }}">
                        <span class="title">10.1 Member Migration Balance</span>
                    </a>
                </li>

                <li>
                  <a href="{{ url('LoanStatementRecoverableCalculationReport') }}">
                      <span class="title">10.2 Loan Statement (Recoverable Calculation)</span>
                  </a>
              </li>

              <li>
                  <a href="{{ url('LoanWriteOffReport') }}">
                      <span class="title">10.3 Loan Write off Report</span>
                  </a>
              </li>

              <li>
                  <a href="{{ url('LoanRebateReport') }}">
                      <span class="title">10.4 Loan Rebate Report</span>
                  </a>
              </li>

          {{-- <li>
              <a href="{{ url('TransferRegisterReport') }}">
                  <span class="title">Transfer Register Report</span>
              </a>
          </li> --}}

          <li>
              <a href="{{ url('BranchWiseSamityReport') }}">
                  <span class="title">10.5 Branch Wise Samity Report</span>
              </a>
          </li>

          <li>
              <a href="{{ url('AdvanceDueListReport') }}">
                  <span class="title">10.6 Advance Due List</span>
              </a>
          </li>
      </ul>
  </li>


          {{-- <li>
              <a href="#">
                  <span class="title">17 Pass Book Balancing Register Reports</span>
              </a>
              <ul>
                  <li>
                      <a href="#">
                          <span title="title">17.1 Member wise Pass book Balancing Register Report</span>
                      </a>
                  </li>
              </ul>
          </li> --}}








            <!--<li>
                <a href="{{ url('mfnReport/13') }}">
                    <span class="title">Advance Due register</span>
                </a>
            </li>
            <li>
                <a href="{{ url('mfnReport/16') }}">
                    <span class="title">Loan Statement (Recoverable Calculation)</span>
                </a>
            </li>
            <li>
                <a href="javascript:;">
                    <span class="title">Pass book Balancing Register Reports</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mfnReport/17.1') }}">
                            <span class="title">Member wise Pass Book Balancing Register Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/17.2') }}">
                            <span class="title">Credit Officer wise Pass Book Balancing Register Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/17.3') }}">
                            <span class="title">Branch wise Pass Book Balancing Register Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/17.4') }}">
                            <span class="title">Pass Book Checking Report</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <span class="title">Monthly Report</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mfnReport/18.1') }}">
                            <span class="title">MSP/DPS Register Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/18.2') }}">
                            <span class="title">Monthly Progress Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/18.3') }}">
                            <span class="title">Monthly Purpose Wise Loan Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/18.5') }}">
                            <span class="title">Monthly Target Achievement Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/18.6') }}">
                            <span class="title">Monthly Branch Manager Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/18.10') }}">
                            <span class="title">District and Upazila wise cumulative loan disbursement report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/18.11') }}">
                            <span class="title">Loan Installment Passing Report</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <span class="title">AGROSOR Reports</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('mfnReport/19.1') }}">
                            <span class="title">Monthly Statement of Agroshor Report(format-01)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/19.2') }}">
                            <span class="title">Half Yearly Purpose Wise Report of Agroshor Activities</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/19.3') }}">
                            <span class="title">Half Yearly Statement Of Employment Created by Agrosor Entrepreneur</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('mfnReport/19.4') }}">
                            <span class="title">Employment Register Reports (Format- 4)</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ url('mfnReport/20') }}">
                    <span class="title">Disaster Management Fund</span>
                </a>
            </li>
            <li>
                <a href="{{ url('mfnReport/21') }}">
                    <span class="title">MIS and AIS Cross Check Report</span>
                </a>
            </li>
            <li>
                <a href="{{ url('mfnReport/22') }}">
                    <span class="title">Periodical Progress Report</span>
                </a>
            </li>-->
        </ul>
    </li>
    <!-- ==============================================Ends Reports Nav============================================== -->
</ul>
<style type="text/css">
#main-menu > li > ul >li > ul{
    left: 270px !important;
    top: -15px !important;
    z-index: 0;
    position: relative;
}

#main-menu > li#report-menu > ul >li >ul{
    left: -379px !important;
    top: -25px !important;
}

#main-menu > li#config > ul >li >ul{
    left: 286px !important;
    top: -27px !important;
}

/*register sub menu*/
#main-menu > li > ul >li >ul > li > ul#register{
    left: 10px !important;
    background-color: #ddd;
}

#main-menu > li > ul >li >ul > li > ul#register li{
    /*padding: 15px*/
    margin-left:5px;
}


#main-menu > li > ul >li >ul > li > ul#member-configuration{
    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#member-configuration li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}
#main-menu > li > ul >li >ul > li > ul#loanconfiguration{

    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#loanconfiguration li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}
#main-menu > li > ul >li >ul > li > ul#savingsproduct{

    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#savingsproduct li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}
#main-menu > li > ul >li >ul > li > ul#loanproduct{

    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#loanproduct li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}
#main-menu > li > ul >li >ul > li > ul#members{

    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#members li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}
#main-menu > li > ul >li >ul > li > ul#loan{

    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#loan li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}
#main-menu > li > ul >li >ul > li > ul#savings{

    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#savings li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}

#main-menu >li > ul > li{
    height: 30px !important;
}
</style>
<style type="text/css">
#main-menu li {
    padding: 0px  20px 0px 0px!important;
}
</style>

<script type="text/javascript">
    $(".holidaySubLink").click(function(){

        var url = $(".holidaySubLink").attr('href');

        var arr = url.split('public/');
        var arrSplit = arr[1].split('/');
        var prefix = arrSplit[0];
        // alert(prefix);

        // $.ajax({
        //     type: 'post',
        //     url: '/getPrefix',
        //     dataType: 'json',
        //     data: {data: prefix},
        //     success: function(data){
        //         console.log(data);
        //         },
        //     error: function(response){
        //             alert('Error'+response);
        //         }
        // });
    });



</script>
