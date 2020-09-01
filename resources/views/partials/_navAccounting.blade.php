<style type="text/css">
#main-menu li {
  padding: 0px  20px 0px 0px!important;
}

#main-menu > li#settings > ul >li > ul {
  left: 268px !important;
  top: -26px !important;
}
#main-menu li ul ul ul li  {
  padding-left: 10px !important;
}
</style>
<ul id="main-menu" class="navbar-nav">
  {{-- =========================================Starts Setting========================================= --}}
  <li id="settings">
    <a class="animated fadeInLeft" href="">
      <i class="fa fa-cog" aria-hidden="true"></i>
      <span class="title">Settings</span>
    </a>
    <ul>
      {{-- <li>
        <a class="animated fadeInLeft" href="{{url('viewAccountType/')}}">
          <i class="fa fa-cc-visa" aria-hidden="true"></i>
          <span class="title">Account Type</span>
        </a>
      </li>
      <li>
        <a class="animated fadeInLeft" href="{{url('viewVoucherType/')}}">
          <i class="fa fa-cc-mastercard" aria-hidden="true"></i>
          <span class="title">Voucher Type</span>
        </a>
      </li> --}}
      {{-- <li>
        <a class="animated fadeInLeft" href="{{url('viewMisConfiguration/')}}">
          <i class="fa fa-money" aria-hidden="true"></i>
          <span class="title">MIS Configration</span>
        </a>
      </li>
      <li>
        <a class="animated fadeInLeft" href="{{url('viewAutoVoucherConfigForAll/')}}">
          <i class="fa fa-money" aria-hidden="true"></i>
          <span class="title">Auto Voucher Configration For All</span>
        </a>
      </li>
      <li>
        <a class="animated fadeInLeft" href="{{url('viewAutoVoucherConfig/')}}">
          <i class="fa fa-money" aria-hidden="true"></i>
          <span class="title">Auto Voucher Configration For Microfinance</span>
        </a>
      </li> --}}

      {{--Hr Auto Voucher Configuration--}}
      {{-- <li class="has-sub">
        <a href="#">
          <i class="fa fa-star" aria-hidden="true"></i>
          <span class="title">Auto Voucher Configuration For Hr</span>
        </a>
        <ul>
          <li>
            <a href="">
              <i class="fa fa-h-square"></i>
              Transaction Head Configuration
            </a>
          </li>
          <li class="has-sub">
            <a href="#">
              <i class="fa fa-line-chart" aria-hidden="true"></i>
              <span class="title">Salary Auto Voucher</span>
            </a>
            <ul>
              <li>
                <a class="animated fadeInLeft" href="{{url('/hr/autoVoucherSettings')}}">
                  <span class="title">Salary Head Configuration</span>
                </a>
              </li>
              <li>
                <a class="animated fadeInLeft" href="{{url('/hr/autoVoucherTypeSettings')}}">
                  <span class="title">Salary Type Configuration</span>
                </a>
              </li>
            </ul>
          </li>
          <li class="has-sub">
            <a href="#">
              <i class="fa fa-line-chart" aria-hidden="true"></i>
              <span class="title">Final Payment Auto Voucher </span>
            </a>
            <ul>
              <li>
                <a href="">
                  <span class="title">Final Payment Head Configuration</span>
                </a>
              </li>
              <li>
                <a href="{{ url('hr/finalPaymentAutoVoucherMasterConfiguration') }}">
                  <span class="title">Final Payment Master Configuration</span>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </li> --}}
      {{--Hr Auto Voucher Configuration End--}}


      {{-- <li>
        <a href="{{url('viwRegisterTypeList/')}}">
          <i class="fa fa-money" aria-hidden="true"></i>
          <span class="title">Advance Type</span>
        </a>
      </li>
      <li>
        <a href="javascript:;">
          <span class="title">Budget</span>
        </a>
        <ul id="budget-settings">
          <li>
            <a href="javascript:;">
              <span class="title">Budget</span>
            </a>
            <ul>
              <li>
                <a href="{{url('viewBudget')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Budget</span>
                </a>
              </li>
              <li>
                <a href="{{url('#')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Asset & Liabilities</span>
                </a>
              </li>
              <li>
                <a href="{{url('accBudgetExpenditure')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Expenditure</span>
                </a>
              </li>
              <li>
                <a href="{{url('accBudgetIncome')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Income</span>
                </a>
              </li>
              <li>
                <a href="{{url('#')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">MIS</span>
                </a>
              </li>
            </ul>
          </li>

          <li>
            <a href="javascript:;">
              <span class="title">Update / Revise Budget</span>
            </a>
            <ul>
              <li>
                <a href="{{url('accRevisedBudget')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Asset & Liabilities</span>
                </a>
              </li>
              <li>
                <a href="{{url('accBudgetRevisedExpenditure')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Expenditure</span>
                </a>
              </li>
              <li>
                <a href="{{url('accBudgetRevisedIncome')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Income</span>
                </a>
              </li>
              <li>
                <a href="{{url('#')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">MIS</span>
                </a>
              </li>
            </ul>
          </li>

          <li>
            <a href="javascript:;">
              <span class="title">Approve Budget</span>
            </a>
            <ul>
              <li>
                <a href="{{url('accBudgetAssetAndLiabilityApprove')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Asset & Liabilities</span>
                </a>
              </li>
              <li>
                <a href="{{url('accBudgetExpenditureApprove')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Expenditure</span>
                </a>
              </li>
              <li>
                <a href="{{url('accBudgetIncomeApprove')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Income</span>
                </a>
              </li>
              <li>
                <a href="{{url('#')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">MIS</span>
                </a>
              </li>

              <li>
                <a href="{{url('accBudgetAssetAndLiabilityRevisedApprove')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Asset & Liabilities (Revised)</span>
                </a>
              </li>
              <li>
                <a href="{{url('accBudgetExpenditureRevisedApprove')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Expenditure (Revised)</span>
                </a>
              </li>
              <li>
                <a href="{{url('accBudgetIncomeRevisedApprove')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Income (Revised)</span>
                </a>
              </li>
              <li>
                <a href="{{url('#')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">MIS (Revised)</span>
                </a>
              </li>

            </ul>
          </li> --}}
          {{-- nav editing end --}}
                {{-- <li>
                  <a href="{{url('accBudget')}}">
                      <i class="fa fa-credit-card" aria-hidden="true"></i>
                      <span class="title">Asset & Liabilities (Budget)</span>
                  </a>
                </li> --}}
                {{-- <li>
                  <a href="{{url('accBudgetLiability')}}">
                      <i class="fa fa-credit-card" aria-hidden="true"></i>
                      <span class="title">Liability</span>
                  </a>
                </li> --}}
                {{-- <li>
                  <a href="{{url('accBudgetExpenditure')}}">
                      <i class="fa fa-credit-card" aria-hidden="true"></i>
                      <span class="title">Expenditure (Budget)</span>
                  </a>
                </li>
                <li>
                  <a href="{{url('accBudgetIncome')}}">
                      <i class="fa fa-credit-card" aria-hidden="true"></i>
                      <span class="title">Income (Budget)</span>
                  </a>
                </li>
                <li>
                  <a href="{{url('#')}}">
                      <i class="fa fa-credit-card" aria-hidden="true"></i>
                      <span class="title">MIS (Budget)</span>
                  </a>
                </li>
                <li>
                  <a href="{{url('accRevisedBudget')}}">
                      <i class="fa fa-credit-card" aria-hidden="true"></i>
                      <span class="title">Asset & Liabilities (Revised Budget)</span>
                  </a>
                </li> --}}
              {{-- </ul>
            </li>

            <li>
              <a href="javascript:;">
                <span class="title">OTS Settings</span>
              </a>
              <ul id="ots-settings">
                <li>
                  <a href="{{url('viewOtsRegisterPeriod/')}}">
                    <i class="fa fa-credit-card" aria-hidden="true"></i>
                    <span class="title">OTS Period</span>
                  </a>
                </li>
                <li>
                  <a href="{{url('OTSperiodInterestHistoryTable')}}">
                    <i class="fa fa-credit-card" aria-hidden="true"></i>
                    <span class="title">OTS Period Interest Rate</span>
                  </a>
                </li>
              </ul>
            </li> --}}

            <li>
              <a class="animated fadeInLeft" href="{{url('viewOpeningBalance/')}}">
                <i class="fa fa-money" aria-hidden="true"></i>
                <span class="title">Opening Balance</span>
              </a>
            </li>
            {{-- <li>
                <a class="animated fadeInLeft" href="{{url('viewMisConfiguration/')}}">
                    <i class="fa fa-money" aria-hidden="true"></i>
                    <span class="title">Mis Configuration</span>
                </a>
              </li> --}}
              <li>
                <a class="animated fadeInLeft" href="{{url('viewLedger/')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Chart Of Accounts</span>
                </a>
              </li>
              <li>
                <a class="animated fadeInLeft" href="{{url('viewTransactionLedger/')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">Ledger</span>
                </a>
              </li>

              {{-- <li>
                <a href="#">
                  <span class="title">Loan Register Setting</span>
                </a>
                <ul>
                  <li>
                    <a href="{{url('viewMailSetting/')}}">
                      <span class="title">Mail Setting</span>
                    </a>
                  </li>

                </ul>
              </li>
              <li>
                <a href="">
                  <span class="title">VAT Settings</span>
                </a>
                <ul>
                  <li>
                    <a href="{{url('accViewVatBillType/')}}">
                      <span class="title">VAT Bill TYPE</span>
                    </a>
                  </li>
                </ul>
              </li> --}}
              {{-- Ends VAT Settings --}}




              {{-- <li>
                <a href="javascript:;">
                  <span class="title">Holiday</span>
                </a>
                <ul>
                  <li>
                    <a href="{{ url('acc/viewHolidayCalender/') }}">
                      <span class="title">Holiday Calender</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{ url('acc/viewGovHoliday/') }}">
                      <span class="title">Gov. Holiday</span>
                    </a>
                  </li>

                  <li>
                    <a href="{{ url('acc/viewOrgBranchSamityHoliday/') }}">
                      <span class="title">Org./Branch Holiday</span>
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
                    <a href="{{ url('acc/viewDivision/') }}">
                      <span class="title">Division</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{ url('acc/viewDistrict/') }}">
                      <span class="title">District</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{ url('acc/viewUpazila/') }}">
                      <span class="title">Thana / Upazila</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{ url('acc/viewUnion/') }}">
                      <span class="title">Union</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{ url('acc/viewVillage/') }}">
                      <span class="title">Village</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{ url('acc/viewWorkingArea') }}">
                      <span class="title">Working Area</span>
                    </a>
                  </li>
                </ul>
              </li> --}}
              @php
                $v_approval_step = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->value('voucher_type_step');
              @endphp

              @if($v_approval_step !=0)
                <li>
                  <a href="{{url('viewApprovalSetting/')}}">
                    <i class="fa fa-check" aria-hidden="true"></i>
                    <span class="title">Approval Settings</span>
                  </a>   
                </li>
              @endif
            </ul>
          </li>
          {{-- =========================================End Setting========================================= --}}

          {{-- =========================================START EMPLOYEE========================================= --}}


          {{-- <li>
            <a class="animated fadeInLeft" href="javascript:;">
              <i class="linecons-cog"></i>
              <span class="title">Employee</span>
            </a>
            <ul>
              <li>
                <a href="{{url('acc/posHrEmployeeList/')}}">
                  <span class="title">Employee Information</span>
                </a>
              </li>
              <li>
                  <a href="{{ url('acc/transfer') }} ">
                      <span class="title">Transfer</span>
                  </a>
              <li>
                  <a href="{{ url('acc/resignInfo') }} ">
                      <span class="title">Resign</span>
                  </a>
              </li>
              <li>
                  <a href="{{ url('acc/terminateInfo') }} ">
                      <span class="title">Terminate</span>
                  </a>
              </li>
            </ul>
          </li> --}}

          {{-- =========================================END EMPLOYEE========================================= --}}

          {{-- =========================================Start Budget========================================= --}}
          <li>
            <a class="animated fadeInLeft" href="">
              <i class="fa fa-money" aria-hidden="true"></i>
              <span class="title">Budget</span>
            </a>
            <ul>
              <li>
                <a class="animated fadeInLeft" href="{{ url('viewBudget/') }}">
                  <i class="fa fa-cc-visa" aria-hidden="true"></i>
                  <span class="title">Budget</span>
                </a>
              </li>
              <li>
                <a class="animated fadeInLeft" href="{{ url('viewRevisedBudget/') }}">
                  <i class="fa fa-cc-mastercard" aria-hidden="true"></i>
                  <span class="title">Revise Budget</span>
                </a>
              </li>
            </ul>
          </li>
          {{-- =========================================End Budget========================================= --}}

          {{-- =========================================Starts Register========================================= --}}

          {{-- <li id="register">
            <a class="animated fadeInLeft" href="">
              <i class="fa fa-eur" aria-hidden="true"></i>
              <span class="title">Register </span>
            </a>
            <ul>
              <li>   
                <a href="#">
                  <span class="title">OTS Register</span>
                </a>
                <ul>
                  <li>
                    <a href="{{url('otsRegisterList/')}}">
                      <span class="title">OTS Account Opening</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{url('viewOtsPrincipalPayment/')}}">
                      <span class="title">OTS Account Closing</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{url('viewOtsInterest/')}}">
                      <span class="title">OTS Interest Generate</span>
                    </a>
                  </li>

                  <li>
                    <a href="{{url('viewOtsPayment/')}}">
                      <span class="title">OTS Interest Payment</span>
                    </a>
                  </li>
                </ul>
              </li>      

              <li>   
                <a href="">
                  <span class="title">FDR Register</span>
                </a>
                <ul>
                  <li>
                    <a href="{{url('fdrRegisterList/')}}">
                      <span class="title">FDR Accounts List</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{url('viewAccFdrInterest/')}}">
                      <span class="title">FDR Interest Received</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{url('viewAccFdrReceivable/')}}">
                      <span class="title">FDR Interest Receivable</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{url('viewFdrAccountClose/')}}">
                      <span class="title">FDR Encashment</span>
                    </a>
                  </li>
                </ul>
              </li>    

              <li>  
                <a href="">
                  <span class="title">Loan Register</span>
                </a>
                <ul>
                  <li>
                    <a href="{{url('viewLoanRegisterAccount/')}}">
                      <span class="title">Loan Account List</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{url('viewLoanRegisterPayment/')}}">
                      <span class="title">Loan Re-payment</span>
                    </a>
                  </li>
                </ul>
              </li>  

              <li>    
                <a href="">
                  <span class="title">Advance Register</span>
                </a>
                <ul>
                  <li>
                    <a href="{{url('viewAdvRegisterList/')}}">
                      <span class="title">Advance Payment</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{url('viewAdvanceReceivelist/')}}">
                      <span class="title">Advance Received</span>
                    </a>
                  </li>
                </ul>
              </li>   


              <li>   
                <a href="">
                  <span class="title">VAT Register</span>
                </a>
                <ul>
                  <li>
                    <a href="{{url('accViewVatRegister/')}}">
                      <span class="title">VAT GENERATE LIST</span>
                    </a>
                  </li>



                  <li>
                    <a href="{{url('accVatRegisterPaymentList/')}}">
                      <span class="title">VAT PAYMENT LIST</span>
                    </a>
                  </li>
                </ul>
              </li>   


              <li>  
                <a href="">
                  <span class="title">TAX Register</span>
                </a>
                <ul>
                  <li>
                    <a href="{{url('accViewTaxRegister/')}}">
                      <span class="title">TAX Register List</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{url('accViewTaxBillType/')}}">
                      <span class="title">View TAX Bill Type</span>
                    </a>
                  </li>

                  <li>
                    <a href="{{url('accTaxRegisterPaymentList/')}}">
                      <span class="title">TAX Payment List</span>
                    </a>
                  </li>


                </ul>
              </li>   




            </ul>
          </li> --}}

          {{-- =========================================End Register========================================= --}}

          {{-- =========================================Start Voucher========================================= --}}

          <li>
            <a class="animated fadeInLeft" href="#">
              <i class="fa fa-suitcase" aria-hidden="true"></i>
              <span class="title">Vouchers</span>
            </a>
            <ul>
              <li>
                <a class="animated fadeInLeft" href="{{url('addVoucher/')}}">
                  <i class="fa fa-cc-visa" aria-hidden="true"></i>
                  <span class="title">Add Vouchers</span>
                </a>
              </li>
              <li>
                <a class="animated fadeInLeft" href="{{url('viewVoucher/')}}">
                  <i class="fa fa-cc-visa" aria-hidden="true"></i>
                  <span class="title">Vouchers List</span>
                </a>
              </li>

            </ul>

         {{-- <ul>
            <li>
                <a class="animated fadeInLeft" href="{{url('viewVoucher/')}}">
                    <i class="fa fa-cc-visa" aria-hidden="true"></i>
                    <span class="title">Debit Vouchers</span>
                </a>
            </li>
            <li>
                <a class="animated fadeInLeft" href="{{url('viewVoucher/')}}">
                    <i class="fa fa-cc-mastercard" aria-hidden="true"></i>
                    <span class="title">Credit Vouchers</span>
                </a>
            </li>
            <li>
                <a class="animated fadeInLeft" href="{{url('viewVoucher/')}}">
                    <i class="fa fa-money" aria-hidden="true"></i>
                    <span class="title">Debit Vouchers</span>
                </a>
            </li>
            <li>
                <a class="animated fadeInLeft" href="{{url('viewVoucher/')}}">
                    <i class="fa fa-credit-card" aria-hidden="true"></i>
                    <span class="title">Debit Vouchers</span>
                </a>
            </li>

          </ul> --}}

        </li>

        {{-- =========================================End Voucher========================================= --}}

        {{-- =========================================Start Auto Voucher========================================= --}}

        {{-- <li>
          <a class="animated fadeInLeft" href="#">
            <i class="fa fa-suitcase" aria-hidden="true"></i>
            <span class="title">Auto Vouchers</span>
          </a>

          <ul> --}}
            {{-- <li>
                <a class="animated fadeInLeft" href="{{url('unauthorizedAutoVouchersList/1')}}">
                    <i class="fa fa-cc-visa" aria-hidden="true"></i>
                    <span class="title">Authorize Inventory Auto Vouchers</span>
                </a>
            </li>
            <li>
                <a class="animated fadeInLeft" href="{{url('unauthorizedAutoVouchersList/2')}}">
                    <i class="fa fa-cc-visa" aria-hidden="true"></i>
                    <span class="title">Authorize FAMS Auto Vouchers</span>
                </a>
            </li>
            <li>
                <a class="animated fadeInLeft" href="{{url('unauthorizedAutoVouchersList/3')}}">
                    <i class="fa fa-cc-visa" aria-hidden="true"></i>
                    <span class="title">Authorize Procurement Auto Vouchers</span>
                </a>
            </li>
            <li>
                <a class="animated fadeInLeft" href="{{url('unauthorizedAutoVouchersList/4')}}">
                    <i class="fa fa-cc-visa" aria-hidden="true"></i>
                    <span class="title">Authorize Accounting Auto Vouchers</span>
                </a>
            </li>
            <li>
                <a class="animated fadeInLeft" href="{{url('unauthorizedAutoVouchersList/5')}}">
                    <i class="fa fa-cc-visa" aria-hidden="true"></i>
                    <span class="title">Authorize HR and Payroll Auto Vouchers</span>
                </a>
              </li> --}}
              {{-- <li>
                <a class="animated fadeInLeft" href="{{url('hr/salaryGenerateAutoVoucher')}}">
                  <i class="fa fa-cc-visa" aria-hidden="true"></i>
                  <span class="title">Salary Generate Auto Vouchers</span>
                </a>
              </li>
              <li>
                <a class="animated fadeInLeft" href="{{url('hr/finalPaymentAutoVoucher')}}">
                  <i class="fa fa-cc-visa" aria-hidden="true"></i>
                  <span class="title">Final Payment Auto Vouchers</span>
                </a>
              </li>
            </ul>
          </li> --}}

          {{-- =========================================End Auto Voucher========================================= --}}

          {{-- =========================================Start Process========================================= --}}
          <li>
            <a class="animated fadeInLeft" href="">
              <i class="fa fa-spinner" aria-hidden="true"></i>
              <span class="title">Process</span>
            </a>
            <ul>
              {{-- <li>
                <a class="animated fadeInLeft" href="{{url('accDayEndProcess/')}}">
                  <i class="fa fa-cc-visa" aria-hidden="true"></i>
                  <span class="title">Day End</span>
                </a>
              </li> --}}
              <li>
                <a class="animated fadeInLeft" href="{{url('accMonthEndProcess/')}}">
                  <i class="fa fa-cc-mastercard" aria-hidden="true"></i>
                  <span class="title">Month End</span>
                </a>
              </li>
              <li>
                <a class="animated fadeInLeft" href="{{url('accYearEndProcess/')}}">
                  <i class="fa fa-cc-mastercard" aria-hidden="true"></i>
                  <span class="title">Year End</span>
                </a>
              </li>
              {{-- <li>
                <a class="animated fadeInLeft" href="{{url('authorizedVouchersList/')}}">
                  <i class="fa fa-cc-visa" aria-hidden="true"></i>
                  <span class="title">Authorized Vouchers</span>
                </a>
              </li> --}}
              {{-- <li>
                <a class="animated fadeInLeft" href="{{url('unauthorizedVouchersList/')}}">
                  <i class="fa fa-cc-visa" aria-hidden="true"></i>
                  <span class="title">Unauthorized Vouchers</span>
                </a>
              </li> --}}

            </ul>
          </li>
          {{-- =========================================End Process========================================= --}}

          {{-- =========================================Start Report========================================= --}}


          <li id="accounting">
            <a class="animated fadeInLeft" href="">
              <i class="fa fa-line-chart" aria-hidden="true"></i>
              <span class="title">Reports</span>
            </a>
            <ul>
              {{-- <li>
                <a href="#">
                  <i class="fa fa-bar-chart" aria-hidden="true"></i>
                  <span class="title">Chart of Accounts</span>
                </a>
              </li> --}}
              <li>
                <a href="{{url('ledgerReport/')}}">
                  <i class="fa fa-bar-chart" aria-hidden="true"></i>
                  <span class="title">Ledger Report</span>
                </a>
              </li>
             {{--  <li>
                <a href="{{url('branchWiseLedger/')}}">
                  <i class="fa fa-bar-chart" aria-hidden="true"></i>
                  <span class="title">Branch Wise Ledger Report</span>
                </a>
              </li> --}}
              {{-- <li>
                <a href="{{url('voucherRegisterReport/')}}">
                  <i class="fa fa-bar-chart" aria-hidden="true"></i>
                  <span class="title">Voucher Register Report</span>
                </a>
              </li> --}}
            {{-- <li>
                <a href="{{url('balanceSheet/')}}">
                    <i class="fa fa-align-left" aria-hidden="true"></i>
                    <span class="title">Statement of Financial Position</span>
                </a>
              </li> --}}
              <li>
                <a href="{{url('financialPositionStatement/')}}">
                  <i class="fa fa-align-left" aria-hidden="true"></i>
                  <span class="title">Statement of Financial Position</span>
                </a>
              </li>
            {{-- <li>
                <a href="{{url('receiptPaymentReport/')}}">
                    <i class="fa fa-align-left" aria-hidden="true"></i>
                    <span class="title">New Receipt Payment(testing)</span>
                </a>
              </li> --}}
              <li>
                <a href="{{url('comprehensiveIncomeStatement/')}}">
                  <i class="fa fa-area-chart" aria-hidden="true"></i>
                  <span class="title">Statement of Comprehensive Income</span>
                </a>
              </li>
            {{-- <li>
                <a href="{{url('receiptPaymentStatement/')}}">
                    <i class="fa fa-file-text" aria-hidden="true"></i>
                    <span class="title"> Receipt Payment </span>
                </a>
              </li> --}}
              <li>
                <a href="{{url('receiptPaymentReport/')}}">
                  <i class="fa fa-file-text" aria-hidden="true"></i>
                  <span class="title">Receipt Payment </span>
                </a>
              </li>
              {{-- <li>
                <a href="{{url('cashFlowStatement/')}}">
                  <i class="fa fa-pie-chart" aria-hidden="true"></i>
                  <span class="title">Cash Flow Statement</span>
                </a>
              </li> --}}
            {{-- <li>
                <a href="{{url('trialBalanceReport/')}}">
                    <i class="fa fa-pie-chart" aria-hidden="true"></i>
                    <span class="title">Trial Balance</span>
                </a>
              </li> --}}
              <li>
                <a href="{{url('trialBalance/')}}">
                  <i class="fa fa-pie-chart" aria-hidden="true"></i>
                  <span class="title">Trial Balance </span>
                </a>
              </li>
            {{--   <li>
                <a href="{{url('capitalFund/')}}">
                  <i class="fa fa-sort-amount-asc" aria-hidden="true"></i>
                  <span class="title">Capital Fund</span>
                </a>
              </li> --}}
              {{-- <li>
                <a href="#">
                  <i class="fa fa-sort-amount-asc" aria-hidden="true"></i>
                  <span class="title">Changes Equity Statement</span>
                </a>
              </li> --}}
              <li>
                <a href="{{url('cashBookReport/')}}">
                  <i class="fa fa-eur" aria-hidden="true"></i>
                  <span class="title">Cash Book Report</span>
                </a>
              </li>
              <li>
                <a href="{{url('bankBookReport/')}}">
                  <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                  <span class="title">Bank Book Report</span>
                </a>
              </li>
              <li>
                <a href="{{url('cashNbankBookReport/')}}">
                  <i class="fa fa-eur" aria-hidden="true"></i>
                  <span class="title">Cash & Bank Book Report</span>
                </a>
              </li>

              {{-- <li> 
                <a href="#">
                  <span class="title">OTS Register Report</span>
                </a>
                <ul>
                  <li>
                    <a href="{{url('viewOtsAccountOpeningReport/')}}">
                      <span class="title">Account Opening Report</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{url('viewOtsAccountClosingReport/')}}">
                      <span class="title">Account Closing Report</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{url('viewOtsInterestGenerateReport/')}}">
                      <span class="title">Interest Generate Report</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{url('viewOtsInterestPaymentReport/')}}">
                      <span class="title">Interest Payment Report</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{url('viewOtsRegisterBalanceReport/')}}">
                      <span class="title">OTS Balance Report</span>
                    </a>
                  </li>

                  <li>
                    <a href="{{url('otsAccountStatement/')}}">
                      <span class="title">OTS Account Statement</span>
                    </a>
                  </li>

                </ul>
              </li>  --}}




              {{-- Starts VAT Register Report--}}
              {{-- <li>
                <a href="#">
                  <span class="title">VAT Register Report</span>
                </a>
                <ul>
                  <li>
                    <a href="{{url('vatRegisterReport/')}}">
                      <span class="title">VAT Register Report</span>
                    </a>
                  </li>


                </ul>
              </li> --}}
              {{-- End VAT Register --}}

              {{-- Starts Advance Payment Report--}}

              {{-- <li>
                <a href="#">
                  <span class="title">Advance Payment Report</span>
                </a>
                <ul>
                  <li>
                    <a href="{{url('advancePaymentReport/')}}">
                      <span class="title">Advance Payment Report</span>
                    </a>
                  </li>
                </ul>
              </li> --}}
              {{-- End Advance Payment --}}

              {{-- <li>
                <a href="{{url('viewFdrRegisterReport/')}}">
                  <span class="title">FDR Register Report</span>
                </a>
              </li>
              <li>
                <a href="{{url('viewLoanRegisterReport/')}}">
                  <span class="title">Loan Register Report</span>
                </a>
              </li>
              <li>
                <a href="{{url('loanRegisterInstallmentReport/')}}">
                  <span class="title">Loan Register Installment Report</span>
                </a>
              </li>

              <li>
                <a href="{{url('advanceRegister/')}}">
                  <span class="title">Advance Register</span>
                </a>
              </li>

              <li>
                <a href="#">
                  <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                  <span class="title">Budget Report</span>
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                  <span class="title">Budget Variance</span>
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                  <span class="title">Branch Wise Ledger Report</span>
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                  <span class="title">Branch Sub Ledger Report</span>
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                  <span class="title">Fund Transfer Report</span>
                </a>
              </li>

              <li>
                <a class="animated fadeInLeft" href="{{url('newReportFilteringTemplate/')}}">
                  <i class="fa fa-credit-card" aria-hidden="true"></i>
                  <span class="title">New Report</span>
                </a>
              </li> --}}
            </ul>
          </li>

          {{-- =========================================END Report========================================= --}}


        </ul> <!-- End main-menu -->

        <style type="text/css">

        #main-menu > li > ul > li > ul {

          left: 270px !important;
          top: -42px !important;
        }

        #main-menu > li#accounting > ul > li > ul {

          left: 270px !important;
          top: -42px !important;
        }

        #main-menu > li#settings > ul > li > ul {

          left: 305px !important;
          top: -42px !important;
        }

        #main-menu > li#register > ul > li > ul {

          left: 268px !important;
          top: -24px !important;
        }

        #main-menu > li > ul > li {
          height: 28px !important;
        }

        #main-menu > li#settings > ul >li > ul {
          left: 300px !important;
          top: -26px !important;
        }

      </style>
