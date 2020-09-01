@extends('layouts.frontend')

@section('content')

<style type="text/css">
   body { -webkit-font-smoothing: antialiased; text-rendering: optimizeLegibility; font-family: 'Noto Sans', sans-serif; letter-spacing: 0px; font-size: 14px; color: #2e3139; font-weight: 400; line-height: 26px; }
h1, h2, h3, h4, h5, h6 { letter-spacing: 0px; font-weight: 400; color: #1c1e22; margin: 0px 0px 15px 0px; font-family: 'Noto Sans', sans-serif; }
h1 { font-size: 42px; line-height: 50px; }
h2 { font-size: 36px; line-height: 42px; }
h3 { font-size: 20px; line-height: 32px; }
h4 { font-size: 18px; line-height: 32px; }
h5 { font-size: 14px; line-height: 20px; }
h6 { font-size: 12px; line-height: 18px; }
p { margin: 0 0 20px; line-height: 1.8; }
p:last-child { margin: 0px; }
ul, ol { }
a { text-decoration: none; color: #2e3139; -webkit-transition: all 0.3s; -moz-transition: all 0.3s; transition: all 0.3s; }
a:focus, a:hover { text-decoration: none; color: #0943c6; }
.content{padding-top:80px; padding-bottom:80px};


/*------------------------
Radio & Checkbox CSS
-------------------------*/
.form-control { border-radius: 4px; font-size: 14px; font-weight: 500; width: 100%; height: 70px; padding: 14px 18px; line-height: 1.42857143; border: 1px solid #dfe2e7; background-color: #dfe2e7; text-transform: capitalize; letter-spacing: 0px; margin-bottom: 16px; -webkit-box-shadow: inset 0 0px 0px rgba(0, 0, 0, .075); box-shadow: inset 0 0px 0px rgba(0, 0, 0, .075); -webkit-appearance: none; }

input[type=radio].with-font, input[type=checkbox].with-font { border: 0; clip: rect(0 0 0 0); height: 1px; margin: -1px; overflow: hidden; padding: 0; position: absolute; width: 1px; }
input[type=radio].with-font~label:before, input[type=checkbox].with-font~label:before { font-family: FontAwesome; display: inline-block; content: "\f1db"; letter-spacing: 10px; font-size: 1.2em; color: #dfe2e7; width: 1.4em; }
input[type=radio].with-font:checked~label:before, input[type=checkbox].with-font:checked~label:before { content: "\f00c"; font-size: 1.2em; color: #0943c6; letter-spacing: 5px; }
input[type=checkbox].with-font~label:before { content: "\f096"; }
input[type=checkbox].with-font:checked~label:before { content: "\f046"; color: #0943c6; }
input[type=radio].with-font:focus~label:before, input[type=checkbox].with-font:focus~label:before, input[type=radio].with-font:focus~label, input[type=checkbox].with-font:focus~label { }

.box { background-color: #fff; border-radius: 8px; border: 2px solid #e9ebef; padding: 50px; margin-bottom: 40px; }
.box-title { margin-bottom: 30px; text-transform: uppercase; font-size: 16px; font-weight: 700; color:#2ca01c; letter-spacing: 2px; }
.plan-selection { border-bottom: 2px solid #e9ebef; padding-bottom: 25px; margin-bottom: 35px; }
.plan-selection:last-child { border-bottom: 0px; margin-bottom: 0px; padding-bottom: 0px; }
.plan-data { position: relative; }
.plan-data label { font-size: 20px; margin-bottom: 20px; font-weight: 400; }
.plan-text { padding-left:0px; }
.plan-price { position: absolute; right: 0px; color:#2ca01c; font-size: 20px; font-weight: 700; letter-spacing: -1px; line-height: 1.5; bottom: 16px; }
.term-price { bottom: 18px; }
.secure-price { bottom: 68px; }
.summary-block { border-bottom: 2px solid #d7d9de; }
.summary-block:last-child { border-bottom: 0px; }
.summary-content { padding: 28px 0px; }
.summary-price { color: #094bde; font-size: 20px; font-weight: 400; letter-spacing: -1px; margin-bottom: 0px; display: inline-block; float: right; }
.summary-small-text { font-weight: 700; font-size: 12px; color: #8f929a; }
.summary-text { margin-bottom: -10px; }
.summary-title { font-weight: 700; font-size: 14px; color: #1c1e22; }
.summary-head { display: inline-block; width: 120px; }

.widget { margin-bottom: 30px; background-color: #e9ebef; padding: 50px; border-radius: 6px; }
.widget:last-child { border-bottom: 0px; }
.widget-title { color:#2ca01c; font-size: 16px; font-weight: 700; text-transform: uppercase; margin-bottom: 25px; letter-spacing: 1px; display: table; line-height: 1; }

.btn { font-family: 'Noto Sans', sans-serif; font-size: 16px; text-transform: capitalize; font-weight: 700; padding: 12px 36px; border-radius: 4px; line-height: 2; letter-spacing: 0px; -webkit-transition: all 0.3s; -moz-transition: all 0.3s; transition: all 0.3s; word-wrap: break-word; white-space: normal !important; }
.btn-default { background-color: #0943c6; color: #fff; border: 1px solid #0943c6; }
.btn-default:hover { background-color: #063bb3; color: #fff; border: 1px solid #063bb3; }
.btn-default.focus, .btn-default:focus { background-color: #063bb3; color: #fff; border: 1px solid #063bb3; }
</style>
<section  class="ccontainer" style=""  >

  <section id="main" style="margin-top:0px">
    <div class="content">
<div class="container">
    <div class="row">
                <div class="col-lg-7 col-md-8 col-sm-7 col-xs-12">
                    <div class="box">
                        <h3 class="box-title">Checkout</h3>
                        <div class="plan-selection">
                            <div class="plan-data">
                                <!-- <input id="question1" name="question" type="radio" class="with-font" value="sel" /> -->
                                <label for="question1">{{$checkoutInfo->name}} (Monthly)</label>
                               <!--  <p class="plan-text">
                                  @php
                                                 $features = $checkoutInfo->features;
                                                 @endphp
                                                 @foreach ($features as $key => $features)
                                                 
                                                         {{$features}} |
                                                   
                                                 @endforeach
                                    </p> -->
                                <span class="plan-price">Tk.{{number_format($checkoutInfo->price,2)}}</span>
                            </div>
                        </div>
                   
                    </div>
                 <!--    <div class="box">
                        <h3 class="box-title">Training</h3>
                        <div class="plan-selection">
                            <div class="plan-data">
                                <input id="question4" name="question" type="radio" class="with-font" value="sel" />
                                <label for="question4">1 Month</label>

                                <span class="plan-price term-price">$29 / mo</span>
                            </div>
                        </div>
                       
                    </div> -->
                    <!-- <form name="listForm"> -->
                    <div class="box">
                        <h3 class="box-title">Training</h3>
                       <!--     @foreach($trainingList as $trainingLists)
                        <div class="plan-selection">
                            <div class="plan-data">
                                <input id="box1" type="checkbox" class="with-font" />
                                <label for="box1">{{$trainingLists->title}}</label>
                                <p class="plan-text">No. User:{{$trainingLists->    numberOfTrainee}}</p>
                                <span class="plan-price secure-price">Tk.{{$trainingLists-> price}} / mo</span>
                            </div>
                        </div>

                         @endforeach -->
                            <div class="form-group">
                                  
                                    <div class="col-sm-10" style="padding-left:0px">
                                        <select name="trainingId" id="trainingId" class="form-control col-sm-12">
                                            <option value="0" selected="selected">Self-training</option>
                                            @foreach($trainingList as $trainingLists)
                                            <option value="{{$trainingLists->id}}">{{$trainingLists->title}}</option>
                                            @endforeach
                                        </select>
                                        <p id='productTypeIde' style="max-height:3px;"></p>
                                    </div>
                                </div>
                         
            

                    </div>
                   <!--  </form> -->
                      <form id="payment_gw" name="payment_gw" method="POST" action="../customerPayment">
              {{csrf_field()}}
              <div class="form-group" style="padding-bottom:10px;">
                <div class="row">
                  <div class="col-md-4">
                  <input type="hidden" class="form-control" name="total_amount" id="total_amount"  value=""
                    placeholder="Amount" required="required"/>
                    <input type="hidden" name="store_id" value="ambal5c7bc49ad58ae" />
                    <input type="hidden" name="store_passwd" value="ambal5c7bc49ad58ae@ssl"/>
                    <input type="hidden" name="currency" value="BDT" />
                    <input type="hidden" name="tran_id" value="123456" />
                    <input type="hidden" name="success_url" value="#"/>
                    <input type="hidden" name="fail_url" value="http://120.50.0.141/earningsoft/public/pricing" />
                    <input type="hidden" name="cancel_url" value="http://120.50.0.141/earningsoft/public/pricing" />
                    <input type="hidden" name="version" value="" />

                    <input type="hidden" name="cus_name" value="">
                    <input type="hidden" name="cus_email" value="">
                    <input type="hidden" name="cus_phone" value="">

                    <input type="hidden" name="value_a" value="">
                    <input type="hidden" name="value_b" value="">
                    <input type="hidden" name="value_c" value="">
                    <input type="hidden" name="value_d" value="">
                   <!--  <a href="#" class="btn btn-primary btn-lg mb30" style="background-color: #2ca01c;border-color:#2ca01c">Checkout</a> -->
                    <input type="submit" class="btn btn-primary btn-lg mb30"  style="background-color: #2ca01c;border-color:#2ca01c" value="Checkout" />

                  </div>
                </div>
              </div>
         
            </form>
                    
                </div>
                <div class="col-lg-5 col-md-4 col-sm-5 col-xs-12">
                  
                    <div class="widget">
                        <h4 class="widget-title">Order Summary</h4>
                        <div class="summary-block">
                            <div class="summary-content">
                                <div class="summary-head"><h5 class="summary-title">{{$checkoutInfo->name}}</h5></div>
                                <div class="summary-price">
                                    <p class="summary-text">Tk.{{number_format($checkoutInfo->price,2)}} </p>
                                    <!-- <span class="summary-small-text pull-right">1 month</span> -->
                                </div>
                            </div>
                        </div>

                         <div class="summary-block" id="totalHide">
                            <div class="summary-content">
                                <div class="summary-head"><h5 class="summary-title">Total</h5></div>
                                <div class="summary-price">
                                    <p class="summary-text">Tk.{{number_format($checkoutInfo->price,2)}} </p>
                                    <!-- <span class="summary-small-text pull-right">1 month</span> -->
                                </div>
                            </div>
                        </div>

                             <input type="hidden" name="priceNormal" id="priceNormal" value="{{$checkoutInfo->price}}">
                         <div class="summary-block" id="state">
                            
                        </div>
   
                         <div class="summary-block" id="stateTotal">
                            
                        </div>
                    </div>
                   
                </div>
            </div>

   
   
   
   
   
</div>
</div>
      </section>
</section>


<script type="text/javascript">

var priceNormal = $('#priceNormal').val();
$("#total_amount").val(priceNormal);
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
                $("#state").html('<div class="summary-content"><div class="summary-head"><h5 class="summary-title">'+title+'</h5></div><div class="summary-price"><p class="summary-text">'+price.toFixed(2)+' </p></div></div>');
            var priceNormal = $('#priceNormal').val();
            var total_amount = Number(price) + Number(priceNormal);
            $("#total_amount").val(total_amount);


      

          $('#stateTotal').html('<div class="summary-content"><div class="summary-head"><h5 class="summary-title">Total</h5></div><div class="summary-price"><p class="summary-text">'+total_amount.toFixed(2)+'</p></div></div>');
         
           
     
         
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

</script>

 
@endsection
@section('scripts')
@parent
@endsection
