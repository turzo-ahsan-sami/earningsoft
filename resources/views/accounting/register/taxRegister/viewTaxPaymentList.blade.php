@extends('layouts/acc_layout')
@section('title', '| TAX Register')
@section('content')
<?php use Carbon\Carbon; ?>


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="./accViewTaxRegister" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Payment</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">TAX Payment List</font></h1>
        </div>

        <div class="panel-body panelBodyView">

        <div>

        </div>
          <table class="table table-striped table-bordered" id="otsTable" style="color: black;">
                    <thead>
                      <tr>
                        <th rowspan="2">SL#</th>
                        <th rowspan="2">Payment Date</th>
                        <th rowspan="2">VAT Amount</th>
                        <th rowspan="2" >Bank Name</th>
                        <th rowspan="2">Chalan No</th>
                        <th rowspan="2">Cheque Number</th>
                        <th rowspan="2">Bill No</th>

                        <!-- <th rowspan="2">Action</th> -->
                      </tr>


                    </thead>
                    <tbody>
                      @php $count=0; @endphp
                      @foreach($paymentLists as $paymentList)

                     <tr>
                        <td>{{++$count}}</td>
                        <td>{{$paymentList->paymentDate}}</td>
                        <td style="text-align:right;" > {{$paymentList->taxAmount}}</td>


                        <td>{{$paymentList->bankName}}</td>
                        <td>{{$paymentList->chalanNo}}</td>
                         <td>{{$paymentList->chequeNumber}}</td>

                        <td>{{$paymentList->billNo}}</td>


                        <!-- <td>
                          <a href="javascript:;" class="view-modal" paymentListViewModalID="{{$paymentList->billNo}}">
                             <i class="fa fa-eye" aria-hidden="true"></i>
                         </a>&nbsp;
                       </td> -->


                             <!--
                           <a href="javascript:;" class="pay-modal" vatBillTypeIdPayModal=""  vatAmount="">
                              <span class="glyphicon glyphicon-envelope"></span>

                          </a>&nbsp;



                               <a href="javascript:;" class="edit-modal" vatBillTypeIdeditModal="">
                                  <span class="glyphicon glyphicon-edit"></span>
                              </a>&nbsp;

                              <a href="javascript:;" class="delete-modal" accId="">
                                 <span class="glyphicon glyphicon-trash"></span>
                             </a>

                             <a >
                                <span class="glyphicon glyphicon-envelope"></span>

                            </a>&nbsp;



                                 <a >
                                    <span class="glyphicon glyphicon-edit"></span>
                                </a>&nbsp;

                                <a >
                                   <span class="glyphicon glyphicon-trash"></span>
                               </a>



                      </td> -->
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






@endsection
