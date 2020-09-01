<table class="table table-striped table-bordered" id="vatTable" style="color: black;">
          <thead>
            <tr>
              <th rowspan="2">SL#</th>
              <th rowspan="2">Voucher Date</th>
              <th rowspan="2">Voucher No</th>
              <th rowspan="2">Account Head</th>


              <th colspan="2" rowspan="1" >Bill Information</th>
              <th rowspan="2">VAT Type</th>
              <th rowspan="2">VAT Rate (%)</th>
              <th rowspan="2">VAT Amount (Tk)</th>
              <th rowspan="2">Status</th>
              <th rowspan="2">Action</th>
            </tr>
            <tr style="border-color: white;  border-width: 1px;border-top-style: solid;">
              <th >Bill Date</th>

              <th>Bill Amount</th>
            </tr>

          </thead>
          <tbody>
        @php $count=1; @endphp
         @foreach($viewVatRegisters as $viewVatRegister)
           @if($viewVatRegister->softDel==0)
           <tr>
              <td>{{$count}}</td>
              <td>{{Carbon::parse($viewVatRegister->voucherDate)->format('d-m-Y')}}</td>
              <td >{{$viewVatRegister->voucherNo}}</td>
              <td style="text-align:left;">{{$viewVatRegister->ledger}}</td>

              <td>{{Carbon::parse($viewVatRegister->billDate)->format('d-m-Y')}}</td>

              <td style="text-align:right;">{{$viewVatRegister->billAmount}}</td>
                <td>{{$viewVatRegister->serviceName}}</td>
              <td>{{$viewVatRegister->vatInterestRate}}</td>
              <td class="amount">{{$viewVatRegister->vatAmount}}</td>
              {{-- <td>


                 <button class="btn btn-info"  style="width: 2px; line-height: 2px; text-align: center; font-size: 12px;background-color: #8F493D !important;  border: 0px solid #525659; border-radius: 10px;" disabled>
                      <span><i class="fa fa-times" aria-hidden="true"></i></span>
                  </button>

                  <button class="btn btn-info"  style="width: 2px; line-height: 2px; text-align: center; font-size: 12px;background-color: green !important;  border: 0px solid #525659; border-radius: 10px;" disabled>
                      <span><i class="fa fa-check" aria-hidden="true"></i></span>
                  </button>


              </td> --}}



               <td>

           @if($viewVatRegister->status == 0)
                <p style="color:Black;">Unpaid</p>
             @else
                  <span><i class="" aria-hidden="true" style="color:black;font-size: 1.3;">Paid</i></span>
          @endif

              </td>


              <td>
                <a href="javascript:;" class="view-modal" vatBillTypeIdViewModal="{{$viewVatRegister->id}}">
                   <i class="fa fa-eye" aria-hidden="true"></i>
               </a>&nbsp;


                 @if($viewVatRegister->status == 0)
                 <a href="javascript:;" class="pay-modal" vatBillTypeIdPayModal="{{$viewVatRegister->id}}"  vatAmount="{{$viewVatRegister->vatAmount}}">
                    <span class="glyphicon glyphicon-shopping-cart"></span>

                </a>&nbsp;



                     <a href="javascript:;" class="edit-modal" vatBillTypeIdeditModal="{{$viewVatRegister->id}}">
                        <span class="glyphicon glyphicon-edit"></span>
                    </a>&nbsp;

                    <a href="javascript:;" class="delete-modal" vatBillTypeIdDeleteModal="{{$viewVatRegister->id}}">
                       <span class="glyphicon glyphicon-trash"></span>
                   </a>
                   @else
                   <a >
                      <span class="glyphicon glyphicon-shopping-cart"></span>

                  </a>&nbsp;



                  <a href="javascript:;" class="edit-modal" vatBillTypeIdeditModal="{{$viewVatRegister->id}}">
                     <span class="glyphicon glyphicon-edit"></span>
                 </a>&nbsp;

                      <a >
                         <span class="glyphicon glyphicon-trash"></span>
                     </a>
              @endif


            </td>
           </tr>
           @endif
           @php $count++; @endphp
           @endforeach


      </tbody>
</table>
{{$viewVatRegisters->links() }}
