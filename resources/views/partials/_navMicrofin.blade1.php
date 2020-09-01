<ul id="main-menu" class="navbar-nav">
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Configuration</span>
        </a>
        <ul>
            <li>
                <a href="javascript:;">
                    <span class="title">General Configuration</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('addGeneralConfiguration') }}">
                            <span class="title">General Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('addSamityConfiguration/') }}">
                            <span class="title">Samity Configuration</span>
                        </a>
                    </li>
                     <li>
                        <a href="{{ url('addOperationalPolicy/') }}">
                            <span class="title">Operational Policy</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('addDailyWeeklyConfiguration/') }}">
                            <span class="title">Daily&Weekly Configuration</span>
                        </a>
                    </li>
                     <li>
                        <a href="{{ url('addDayEndConfiguration/') }}">
                            <span class="title">Day End Configuration</span>
                        </a>
                    </li>
                     <li>
                        <a href="{{ url('addCollectionSheetConfiguration/') }}">
                            <span class="title">CollectionSheet Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('addSavingsConfiguration/') }}">
                            <span class="title">Savings Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="title">Member Configuration</span>
                        </a>
                        <ul>

                            <li>
                                <a href="{{ url('addMemberSetting/') }}">
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
                    <li>
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
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="title">Loan Configuration</span>
                        </a>
                        <ul>

                            <li>
                                <a href="{{ url('addLoanConfigurationSetting/') }}">
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
                        <a href="{{url('addTransferConfiguration/')}}">
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
                        <ul>
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
                        <ul>
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
		                        <a href="{{ url('viewLoanProduct/') }}">
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
                        <a href="{{url('addReportConfiguration/')}}">
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
            <li>
                <a href="javascript:;">
                    <span class="title">Address</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('viewDivision/') }}">
                            <span class="title">Division</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewDistrict/') }}">
                            <span class="title">District</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewUpazila/') }}">
                            <span class="title">Thana / Upazila</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewUnion/') }}">
                            <span class="title">Union</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewVillage/') }}">
                            <span class="title">Village</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewWorkingArea') }}">
                            <span class="title">Working Area</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="">
                    <span class="title">Holiday</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('viewMfnHolidayCalender/') }}">
                            <span class="title">Holiday Calender</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('viewMfnGovHoliday/') }}">
                            <span class="title">Gov. Holiday</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ url('viewMfnOrgBranchSamityHoliday/') }}">
                            <span class="title">Org./Branch/Samity Holiday</span>
                        </a>
                    </li>
                    
                </ul>
            </li>
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
        <a class="animated fadeInLeft" href="{{ url('viewEmployee/') }}">
            <span class="title">Employee</span>
        </a>
    </li>
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
                <a href="{{ url('viewMemberPrimaryProductTransfer/') }}">
                    <span class="title">Member Primary Product Transfer</span>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Savings</span>
        </a>
        <ul>
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
            
            
        </ul>
    </li>
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Loans</span>
        </a>
        <ul>
            <li>
                <a href="{{url('viewRegularLoan/')}}">
                    <span class="title">Regular Loan Accounts</span>
                </a>
            </li>
            <li>
                <a href="{{url('viewOneTimeLoan/')}}">
                    <span class="title">One Time Loan Accounts</span>
                </a>
            </li>
            <li>
                <a href="{{ url('viewMfnRegularLoanTransaction/') }}">
                    <span class="title">Regular Loan Transactions</span>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Process</span>
        </a>
        <ul>

            <li>
                <a href="{{url('viewMfnAutoProcess/')}}">
                    <span class="title">Auto Process</span>
                </a>
            </li>
            <li>
                <a href="{{url('viewMfnProcessTraAuthorization/')}}">
                    <span class="title">Tranasction Authorization</span>
                </a>
            </li>
            <li>
                <a href="{{url('viewMfnProcessTraUnauthorization/')}}">
                    <span class="title">Tranasction Unauthorization</span>
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
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Reports</span>
        </a>
        <ul>
            <li>
	            <a href="javascript:;">
	                <span class="title">PKSF-POMIS Reports</span>
	            </a>
                <ul>
                    <li>
                        <a href="{{ url('mfnReport/1.1') }}">
                            <span class="title">Samity Wise Monthly Loan & Saving Basic Collection Sheet</span>
                        </a>
                    </li>
                </ul>
	        </li>
	        <li>
	            <a href="javascript:;">
	                <span class="title">MRA Reports</span>
	            </a>
	        </li>
            <li>
                <a href="javascript:;">
                    <span class="title">3.Regular & General Report - (Branch Level)</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('loanNSavingCollection/') }}">
                            <span class="title">ftghjfgjhSamity Wise Monthly Loan & Saving Basic Collection Sheet</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
	            <a href="javascript:;">
	                <span class="title">4 Register Report - (Branch Level)</span>
	            </a>
	        </li>
	        <li>
	            <a href="javascript:;">
	                <span class="title">5 Consolidated Report</span>
	            </a>
	        </li>
	        <li>
	            <a href="{{ url('loanNSavingCollection/') }}">
	                <span class="title">6 Pass Book Report</span>
	            </a>
	        </li>
	        <li>
	            <a href="{{ url('loanNSavingCollection/') }}">
	                <span class="title">7 Branch Wise Samity List</span>
	            </a>
	        </li>
	        <li>
	            <a href="{{ url('loanNSavingCollection/') }}">
	                <span class="title">8 Samity Wise Member List</span>
	            </a>
	        </li>
	        <li>
	            <a href="{{ url('loanNSavingCollection/') }}">
	                <span class="title">10 Member Migration Balance</span>
	            </a>
	        </li>
	        <li>
	            <a href="{{ url('loanNSavingCollection/') }}">
	                <span class="title">13 Advance Due register</span>
	            </a>
	        </li>
	        <li>
	            <a href="{{ url('loanNSavingCollection/') }}">
	                <span class="title">16 Loan Statement (Recoverable Calculation)</span>
	            </a>
	        </li>
	        <li>
	            <a href="javascript:;">
	                <span class="title">17 Pass book Balancing Register Reports</span>
	            </a>
	        </li>
	        <li>
	            <a href="javascript:;">
	                <span class="title">18 Monthly Report</span>
	            </a>
	        </li>
	        <li>
	            <a href="javascript:;">
	                <span class="title">19 AGROSOR Reports</span>
	            </a>
	        </li>
	        <li>
	            <a href="{{ url('loanNSavingCollection/') }}">
	                <span class="title">20 Disaster Management Fund</span>
	            </a>
	        </li>
	        <li>
	            <a href="{{ url('loanNSavingCollection/') }}">
	                <span class="title">21 MIS and AIS Cross Check Report</span>
	            </a>
	        </li>
	        <li>
	            <a href="{{ url('loanNSavingCollection/') }}">
	                <span class="title">22 Periodical Progress Report</span>
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
    #main-menu >li > ul > li{
        height: 30px !important;
    }
</style>

<style type="text/css">
    #main-menu li {     
    	padding: 0px  20px 0px 0px!important;
	}
</style>