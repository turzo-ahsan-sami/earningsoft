@extends('layouts.frontend')
@section('content')


<section  class="ccontainer" style="" >
<section id="main" style="margin-top:0px">
<div class="container">

  {{-- Heading  --}}
  <div class="py-5 text-center">    
    <h3>Checkout</h3>
  </div>
  {{-- // Heading  --}}

  {{-- Plan Info  --}}
  <div class="row">
    
    {{-- Order Summary  --}}
    <div class="col-md-5 order-md-2 mb-4">
      <p style="border-radius: 0px;background-color: #2ca01c;border-color:#2ca01c" class="btn btn-success btn-lg btn-block">Order Summary</p>
      
      <ul class="list-group mb-3">
        
        <li class="list-group-item" style="display: flex; justify-content: space-between" id="plan_total"></li>

        <li class="list-group-item" style="display: flex; justify-content: space-between" id="plan_training"></li>

        <li class="list-group-item" style="display: flex; justify-content: space-between" id="plan_discount"></li>

        <li class="list-group-item" style="display: flex; justify-content: space-between" id="purchase_total"></li>
                
      </ul>   
    </div>
    {{-- End of Order Summary  --}}


    <div class="col-md-7 order-md-1">

      {{-- Checkout Info  --}}
      <div class="list-group">
        <div class="list-group-item">
          <div class="list-group-item-heading">
            <div class="row">
              <div class="col-xs-6">
                <h3>{{$checkoutInfo->name}}</h3>
              </div>
              <div class="col-xs-6"> 
                <h3> Tk.{{number_format($checkoutInfo->price,2)}} / {{ ucfirst($checkoutInfo->invoice_interval) }} </h3>
              </div>
            </div>
          </div>
        </div>
      </div>
      {{-- End of Checkout Info  --}}    
      
      {{-- Interval  --}}
      <div class="list-group" style="margin-top: 10px">
        <div class="list-group-item">
          <div class="list-group-item-heading">
            <div class="row">
              <div class="col-md-8 mb-8">

                    @if($checkoutInfo->invoice_interval == 'month')
                      <label for="state">Month</label>
                      <select class="custom-select d-block w-100" name="monthId" id="monthId">                          
                          <option value="1">1 Month</option>
                          <option value="6">6 Months</option>
                          <option value="12">12 Months</option>
                        </select>
                    @else
                      <label for="state">Year</label>
                      <select class="custom-select d-block w-100" name="yearId" id="yearId">                          
                          <option value="1">1 Year</option>
                          <option value="2">2 Years</option>
                          <option value="3">3 Years</option>
                          <option value="4">4 Years</option>
                          <option value="5">5 Years</option>
                          <option value="10">10 Years</option>
                        </select>
                    @endif
                    
              </div>
            </div>
          </div>
        </div>
      </div>
      {{-- End of Interval  --}}
      

      {{-- Training  --}}
      <div class="list-group" style="margin-top: 10px">
        <div class="list-group-item">
          <div class="list-group-item-heading">
            <div class="row">        
              <div class="col-md-8 mb-8">
                <label for="state">Traning</label>     
                <select name="trainingId" id="trainingId" class="custom-select d-block w-100">
                    <option value="0" selected="selected">Self-training</option>
                    @foreach($trainingList as $training)
                      <option value="{{$training->id}}">{{$training->title}}</option>
                    @endforeach
                </select>                    
              </div>        
            </div>
          </div>
        </div>
      </div>
      {{-- End of Training  --}}
      
      {{-- Discount  --}}
      <div class="list-group" style="margin-top: 10px">
        <div class="list-group-item">
          <div class="list-group-item-heading">
            <div class="row">        
              <div class="col-md-8 mb-8">
                <label for="state">Discount</label>     
                <select name="discountId" id="discountId" class="custom-select d-block w-100">
                    <option value="0" selected="selected">No Discount</option>
                    @foreach($discountList as $discount)
                      <option value="{{$discount->id}}">{{$discount->title}}</option>
                    @endforeach
                </select>                    
              </div>        
            </div>
          </div>
        </div>
      </div>
      {{-- End of Discount  --}}


      <input type="hidden" class="form-control" name="total_amount_month" id="total_amount_month"  value="" />

      <input type="hidden" name="plan_id" id="plan_id" value="{{$checkoutInfo->id}}">
      <input type="hidden" name="plan_name" id="plan_name" value="{{$checkoutInfo->name}}">
      <input type="hidden" name="plan_price" id="plan_price" value="{{$checkoutInfo->price}}">
      <input type="hidden" name="plan_period" id="plan_period" value="{{$checkoutInfo->invoice_period}}">
      <input type="hidden" name="plan_interval" id="plan_interval" value="{{$checkoutInfo->invoice_interval}}">

      {{-- FORM  --}}
      <form id="payment_gw" name="payment_gw" method="POST" action="../customerPayment">
          {{csrf_field()}}
          <div class="form-group" style="padding-bottom:10px;">
            <div class="row">
              <div class="col-md-12">

            <!--     <input type="hidden" class="form-control" name="total_amount" id="invoiceTotal"  value="" placeholder="Amount" required="required"/> -->
                
                <input type="hidden" name="planId" value="{{$planId}}" />
                {{-- <input type="hidden" name="invoicePeriod" id="invoicePeriod" />
                <input type="hidden" name="invoiceInterval" id="invoiceInterval" /> --}}
                <input type="hidden" name="invoiceTotal" id="invoiceTotal" />
                
                <input type="hidden" name="store_id" value="ambal5c7bc49ad58ae" />
                <input type="hidden" name="store_passwd" value="ambal5c7bc49ad58ae@ssl"/>
                <input type="hidden" name="currency" value="BDT" />
                <input type="hidden" name="tran_id" value="123456" />

                <!--  <input type="hidden" name="success_url" value="http://120.50.0.141/earningsoft/public/customer/successPayment"/> -->
                <!--  <input type="hidden" name="fail_url" value="http://120.50.0.141/earningsoft/public/customer/customerPaymentFail" />
                <input type="hidden" name="cancel_url" value="http://120.50.0.141/earningsoft/public/customer/customerPaymentFail" /> -->

                <input type="hidden" name="version" value="" />

                <input type="hidden" name="cus_name" value="">
                <input type="hidden" name="cus_email" value="">
                <input type="hidden" name="cus_phone" value="">

                <input type="hidden" name="value_a" value="">
                <input type="hidden" name="value_b" value="">
                <input type="hidden" name="value_c" value="">
                <input type="hidden" name="value_d" value="">
                <!--  <a href="#" class="btn btn-primary btn-lg mb30" style="background-color: #2ca01c;border-color:#2ca01c">Checkout</a> -->

                <hr class="mb-4">
                <input type="submit" class="btn btn-primary btn-lg btn-block"  style="background-color: #2ca01c;border-color:#2ca01c" value="Continue to checkout" />

              </div>
            </div>
          </div>         
      </form>
      {{-- End FORM  --}}
          
    </div>

  </div>

</div>
</section>
</section>





{{-- SCRIPTS  --}}

<script type="text/javascript">

  $("#plan_training").hide();
  $("#plan_discount").hide();
 
  $( "#invoicePeriod" ).val('');
  $( "#invoiceInterval" ).val('');
  $( "#invoiceTotal" ).val('');

  var plan_id = $('#plan_id').val();
  var plan_name = $('#plan_name').val();
  var plan_price = $('#plan_price').val();
  var plan_period = $('#plan_period').val();
  var plan_interval = $('#plan_interval').val();

  var planTotal = Number(plan_price) * Number(plan_period);
  var trainingPrice = 0;
  
  var discountId = 0;
  var discountTitle = '';
  var discountType = '';
  var discountValue = 0;
  var discountPrice = 0;
  
  var purchaseTotal = 0;

  calculateTotal();


  $('#yearId').on('change',function(){  
    var yearId = $('#yearId').val();
    setCookie('invoice_period', yearId, 1);        
    planTotal = Number(yearId) * Number(plan_price);
    calculateTotal();
  }); 

  $('#monthId').on('change',function(){      
      var monthId = $('#monthId').val();
      setCookie('invoice_period', monthId, 1);
      planTotal = Number(monthId) * Number(plan_price);
      calculateTotal();         
  });

  $('#trainingId').on('change',function(){
    var trainingId = $(this).val();  
    $("#plan_training").hide();

    if(trainingId == 0){
      trainingPrice = 0;
      calculateTotal();
    }

    if(trainingId > 0){
      $("#plan_training").show();
      $.ajax({
        url:'../getTraining',
        type: 'GET',
        data: { 
          trainingId : trainingId 
        },
        dataType: 'json',
        success: function(data) {
          var title =  data['title'];
          trainingPrice = data['price'];
          $("#plan_training").html('<span>' + title + '</span> <strong>(+) ' + trainingPrice.toLocaleString() + '.00 Tk.</strong>');
          calculateTotal();     
        }
      });
    }       
  });
  
  $('#discountId').on('change',function(){
    discountId = $(this).val();  
    $("#plan_discount").hide();
    
    if(discountId == 0){
      discountPrice = 0;
      calculateTotal();
    }

    if(discountId > 0){
      $("#plan_discount").show();
      $.ajax({
        url:'../getDiscount',
        type: 'GET',
        data: { 
          discountId : discountId 
        },
        dataType: 'json',
        success: function(data) {
          discountTitle =  data['title'];
          discountType = data['discount_type'];
          discountValue = data['value'];
          // discountPrice = (type == 'percentage') ?  (Number(planTotal) * Number(value)/100) : value;
          // $("#plan_discount").html('<span>' + title + '</span> <strong>' + discountPrice.toLocaleString() + '.00 Tk.</strong>');
          calculateTotal();     
        }
      });
    }
  });

  function calculateTotal(){
    
    if(discountId > 0){
      discountPrice = (discountType == 'percentage') ?  (Number(planTotal) * Number(discountValue)/100) : discountValue;
      $("#plan_discount").html('<span>' + discountTitle + '</span> <strong>(-) ' + discountPrice.toLocaleString() + '.00 Tk.</strong>');
    }
    
    purchaseTotal = Number(planTotal) + Number(trainingPrice) - Number(discountPrice);
    $("#invoiceTotal").val(purchaseTotal);    
    
    $("#plan_total").html('<span>' + plan_name + '</span> <strong>' + planTotal.toLocaleString() + '.00 Tk.</strong>');
    $("#purchase_total").html('<span>' + 'Total' + '</span> <strong>' + purchaseTotal.toLocaleString() + '.00 Tk.</strong>');

    console.log($("#invoiceTotal").val());
  }

  
  //define a function to set cookies
  function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
  }


</script>




 


@endsection
