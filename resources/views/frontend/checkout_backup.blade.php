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
.box-title { margin-bottom: 30px; text-transform: uppercase; font-size: 16px; font-weight: 700; color: #094bde; letter-spacing: 2px; }
.plan-selection { border-bottom: 2px solid #e9ebef; padding-bottom: 25px; margin-bottom: 35px; }
.plan-selection:last-child { border-bottom: 0px; margin-bottom: 0px; padding-bottom: 0px; }
.plan-data { position: relative; }
.plan-data label { font-size: 20px; margin-bottom: 15px; font-weight: 400; }
.plan-text { padding-left: 35px; }
.plan-price { position: absolute; right: 0px; color: #094bde; font-size: 20px; font-weight: 700; letter-spacing: -1px; line-height: 1.5; bottom: 43px; }
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
.widget-title { color: #094bde; font-size: 16px; font-weight: 700; text-transform: uppercase; margin-bottom: 25px; letter-spacing: 1px; display: table; line-height: 1; }

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
                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                    <div class="box">
                        <h3 class="box-title">Select Your Hosting Plan</h3>
                        <div class="plan-selection">
                            <div class="plan-data">
                                <input id="question1" name="question" type="radio" class="with-font" value="sel" />
                                <label for="question1">Personal</label>
                                <p class="plan-text">
                                    1 install | 25K visits/month | 10 GB local storage</p>
                                <span class="plan-price">$29 / mo</span>
                            </div>
                        </div>
                        <div class="plan-selection">
                            <div class="plan-data">
                                <input id="question2" name="question" type="radio" class="with-font" value="sel" />
                                <label for="question2">Profesional</label>
                                <p class="plan-text">
                                    Up to 10 installs | 100K visits/month | 20 GB local storage</p>
                                <span class="plan-price">$99 / mo</span>
                            </div>
                        </div>
                        <div class="plan-selection">
                            <div class="plan-data">
                                <input id="question3" name="question" type="radio" class="with-font" value="sel" />
                                <label for="question3">Business</label>
                                <p class="plan-text">Up to 25 installs | 400K visits/month | 30 GB local storage</p>
                                <span class="plan-price">$249 / mo</span>
                            </div>
                        </div>
                    </div>
                    <div class="box">
                        <h3 class="box-title">Select term length</h3>
                        <div class="plan-selection">
                            <div class="plan-data">
                                <input id="question4" name="question" type="radio" class="with-font" value="sel" />
                                <label for="question4">1 Month</label>
                                <span class="plan-price term-price">$29 / mo</span>
                            </div>
                        </div>
                        <div class="plan-selection">
                            <div class="plan-data">
                                <input id="question5" name="question" type="radio" class="with-font" value="sel" />
                                <label for="question5">12 Month</label>
                                <span class="plan-price term-price">$348 / mo</span>
                            </div>
                        </div>
                        <div class="plan-selection">
                            <div class="plan-data">
                                <input id="question6" name="question" type="radio" class="with-font" value="sel" />
                                <label for="question6">24 Month</label>
                                <span class="plan-price term-price">$696 / mo</span>
                            </div>
                        </div>
                    </div>
                    <div class="box">
                        <h3 class="box-title">Secure your site</h3>
                        <div class="plan-selection">
                            <div class="plan-data">
                                <input id="box1" type="checkbox" class="with-font" />
                                <label for="box1">Add malware scan and removal</label>
                                <p class="plan-text">Nam sodales exviverra pretium erat ifermeoin accumsan ligula duiin ornare tortor euismod nece.</p>
                                <span class="plan-price secure-price">$229 / mo</span>
                            </div>
                        </div>
                        <div class="plan-selection">
                            <div class="plan-data">
                                <input id="box2" type="checkbox" class="with-font" />
                                <label for="box2">Add standard SSL Certificate</label>
                                <p class="plan-text">Class aptent taciti sociosqu ad litora torquent perconu bia nostrper inceptos himenelquet dui.</p>
                                <span class="plan-price secure-price">$429 / mo</span>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn btn-primary btn-lg mb30">Continue With Plans</a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12">
                  
                    <div class="widget">
                        <h4 class="widget-title">Order Summary</h4>
                        <div class="summary-block">
                            <div class="summary-content">
                                <div class="summary-head"><h5 class="summary-title">Personal</h5></div>
                                <div class="summary-price">
                                    <p class="summary-text">$29 / mo</p>
                                    <span class="summary-small-text pull-right">1 month</span>
                                </div>
                            </div>
                        </div>
                        <div class="summary-block">
                            <div class="summary-content">
                               <div class="summary-head"> <h5 class="summary-title">Website Security 
Essential</h5></div>
                                <div class="summary-price">
                                    <p class="summary-text">$229 / mo</p>
                                    <span class="summary-small-text pull-right">1 month</span>
                                </div>
                            </div>
                        </div>
                        <div class="summary-block">
                            <div class="summary-content">
                               <div class="summary-head"> <h5 class="summary-title">Total</h5></div>
                                <div class="summary-price">
                                    <p class="summary-text">$258 / mo</p>
                                    <span class="summary-small-text pull-right">1 month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
   <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  text-center mt20">
              Created for <a href="https://goo.gl/XwHgxp" target="_blank">easetemplate</a>
              </div></div>
   
   
   
   
   
</div>
</div>
      </section>
</section>
 
@endsection
@section('scripts')
@parent
@endsection
