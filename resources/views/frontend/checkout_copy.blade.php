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
        
        <li class="list-group-item" style="display: flex; justify-content: space-between;" id="summary-block">
          <div>
            <h6 class="my-0">{{$checkoutInfo->name}}</h6>         
          </div>
          <span class="text-muted">{{number_format($checkoutInfo->price,2)}}</span>
        </li>

        <li class="list-group-item" style="display: flex; justify-content: space-between;" id="totalHide">
          <span>Total (TK)</span>
          <strong>Tk.{{number_format($checkoutInfo->price,2)}}</strong>
        </li>

        <li class="list-group-item" style="display: flex; justify-content: space-between" id="state"></li>
        <li class="list-group-item" style="display: flex; justify-content: space-between" id="stateTotal"></li>
        <li class="list-group-item" style="display: flex; justify-content: space-between" id="monthTotal"></li>
                
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
      

      {{-- Traininfg  --}}
      <div class="list-group" style="margin-top: 10px">
        <div class="list-group-item">
          <div class="list-group-item-heading">
            <div class="row">        
              <div class="col-md-8 mb-8">
                <label for="state">Traning</label>     
                <select name="trainingId" id="trainingId" class="custom-select d-block w-100">
                    <option value="0" selected="selected">Self-training</option>
                    @foreach($trainingList as $trainingLists)
                      <option value="{{$trainingLists->id}}">{{$trainingLists->title}}</option>
                    @endforeach
                </select>                    
              </div>        
            </div>
          </div>
        </div>
      </div>
      {{-- End of Traininfg  --}}


      <input type="hidden" class="form-control" name="total_amount_month" id="total_amount_month"  value="" />
      <input type="hidden" name="priceNormal" id="priceNormal" value="{{$checkoutInfo->price}}">

      {{-- FORM  --}}
      <form id="payment_gw" name="payment_gw" method="POST" action="../customerPayment">
          {{csrf_field()}}
          <div class="form-group" style="padding-bottom:10px;">
            <div class="row">
              <div class="col-md-12">

                <input type="hidden" class="form-control" name="total_amount" id="total_amount"  value="" placeholder="Amount" required="required"/>
                <input type="hidden" name="planId" value="{{$planId}}" />
                
                <input type="hidden" name="invoicePeriod" id="invoicePeriod" />
                <input type="hidden" name="invoiceInterval" id="invoiceInterval" />
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

  var priceNormal = $('#priceNormal').val();

  $( "#total_amount" ).val(priceNormal);
  $( "#state" ).hide();
  $( "#stateTotal" ).hide();
  $( "#totalHide" ).show();
  $( "#monthTotal" ).hide();

  $( "#invoicePeriod" ).val('');
  $( "#invoiceInterval" ).val('');
  $( "#invoiceTotal" ).val('');

  $('#yearId').on('change',function(){  
     if($(this).val()== 0){
      $( "#summary-block" ).show();
      $( "#totalHide" ).show();
      $( "#state" ).hide();
      $( "#stateTotal" ).hide();
      
      var priceNormal = $('#priceNormal').val();
      var yearId = 1;      
    }

    if($(this).val() != 0){
      $( "#monthTotal" ).show();
      $( "#totalHide" ).hide();
      $( "#state" ).hide();
      $( "#stateTotal" ).hide();


      var priceNormal = $('#priceNormal').val();
      var yearId = $('#yearId').val();
    }
    
    var total_amount_month = Number(yearId) * Number(priceNormal);
    
    $("#total_amount").val(total_amount_month);
    $("#total_amount_month").val(total_amount_month);
    $('#monthTotal').html('<span>Total (TK)</span><strong>Tk. '+total_amount_month.toLocaleString()+'.00</strong>');        
    
    $( "#invoicePeriod" ).val(yearId);
    $( "#invoiceInterval" ).val('year');
    $( "#invoiceTotal" ).val(total_amount_month);

    setCookie('invoice_period', yearId, 1);          
  });

  $('#monthId').on('change',function(){  
     if($(this).val()== 0){
      $( "#summary-block" ).show();
      $( "#totalHide" ).show();
      $( "#state" ).hide();
      $( "#stateTotal" ).hide();
      
      var priceNormal = $('#priceNormal').val();

      $("#total_amount").val(priceNormal);
    }

    if($(this).val() != 0){
      $( "#monthTotal" ).show();
      $( "#totalHide" ).hide();
      $( "#state" ).hide();
      $( "#stateTotal" ).hide();

      var priceNormal = $('#priceNormal').val();
      var monthId = $('#monthId').val();
      var total_amount_month = Number(monthId) * Number(priceNormal);
      
      $("#total_amount").val(total_amount_month);
      $("#total_amount_month").val(total_amount_month);
      $('#monthTotal').html('<span>Total (TK)</span><strong>Tk. '+ total_amount_month.toLocaleString()+'.00</strong>');       

      $( "#invoicePeriod" ).val(monthId);
      $( "#invoiceInterval" ).val('month');
      $( "#invoiceTotal" ).val(total_amount_month); 

      setCookie('invoice_period', monthId, 1);
    }      
  });

  $('#trainingId').on('change',function(){

    
      //alert(priceNormal); 
      if($(this).val()== 0){
          $( "#summary-block" ).show();
          $( "#totalHide" ).show();
          $( "#state" ).hide();
          $( "#stateTotal" ).hide();
          var priceNormal = $('#priceNormal').val();
          
          $("#total_amount").val(priceNormal);
      }

      if($(this).val() != 0){
          $( "#totalHide" ).hide();
          $( "#monthTotal" ).hide();
          $( "#state" ).show();
          $( "#stateTotal" ).show();
          $( "#summary-block" ).show();
        
          var trainingId = $(this).val();

        

          $.ajax({
          url:'../getTraining',
          type: 'GET',
          data: {trainingId:trainingId},
          dataType: 'json',
          success: function(data) {

            var title =  data['title'];
            var price = data['price'];
              $("#state").html('<div><h6 class="my-0">'+title+'</h6></div><span class="text-muted">'+price.toFixed(2)+'</span>');
              var priceNormal = $('#priceNormal').val();
              var total_amount_month = $('#total_amount_month').val();

                if($("#total_amount_month").val()!= 0){

              var total_amount = Number(price)+Number(total_amount_month);
              $("#total_amount").val(total_amount);
              $('#stateTotal').html('<span>Total (TK)</span><strong>Tk.'+total_amount.toFixed(2)+'</strong>')

                }else{
                var total_amount = Number(price) +Number(priceNormal);
              $("#total_amount").val(total_amount);
              $('#stateTotal').html('<span>Total (TK)</span><strong>Tk.'+total_amount.toFixed(2)+'</strong>')

                }
            //var monthTotal = 10;
              //alert(monthTotal);
          
      
          
          }
      });

            

    }
        
  })

  function checkTotal() {
      document.listForm.total.value = '';
      var sum = 0;
      for (i=0;i<document.listForm.choice.length;i++) {
        if (document.listForm.choice[i].checked) {
          sum = sum + parseInt(document.listForm.choice[i].value);
        }
      }
      document.listForm.total.value = sum;
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
