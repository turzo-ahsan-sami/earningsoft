@extends('layouts.frontend')

@section('content')
<section  class="ccontainer" style=""  >
</section>
<section  class="ccontainer" style=""  >
   <Section  class="ccontainer bg-grey ss-section" style="" id='pricing' >
      <div class=content-container>
         <div class="include-container">
            <section  class="ccontainer" style=""  >
               <Section  class="ccontainer bg-grey" style="padding-top: 40px; "  >
                  <div class=content-container>
                     <div class="ctext">
                        <h2>Buy Now and save <span class="discountPercentAllPlan"></span>% off Earningsoft.</h2>
                        <p><span class="section-subheader">Or, sign up for a Free 30-Day Trial without a credit card.</span></p>
                     </div>
                     <div class="cpricing-card-lineups pc_bg_white" id="pc-lineups">
                        <div class="row resp_grid grid-container-960">
                           @foreach($planList as $plan)
                           <div class="col-sm-4">
                              <div class="cpricing-card" id="pc-acc-enter-id">
                                 <div class="panel">
                                    <div class="pricing-card-header panel-heading bg-dark-green ">
                                       <div class="mobile-false">
                                          <div class="ctext">
                                             <p>{{$plan->name}}</p>
                                          </div>
                                       </div>
                                       <div class="mobile-true">
                                          <a class="accordion-toggle  collapsed" href="#enter-id" data-toggle="collapse" data-target="#enter-id">
                                             <div class="ctext">
                                                <p>{{$plan->name}}</p>
                                             </div>
                                             <span class="indicator chevron-r-icon-8x12"></span>
                                          </a>
                                       </div>
                                    </div>
                                    <div class="pricing-card-content w100 panel-collapse collapse" id="enter-id">
                                       <Section  class="ccontainer None" style=""  >
                                          <div class="ctext">
                                             <div class="pricing-section" style="display: table; padding-bottom: 25px;">
                                                <div class="red-price-text" style="display: table; margin-left: 6px;"><span class="aw" style="font-family: &#39;Geogrotesque&#39;,&#39;Calibri&#39;,&#39;Trebuchet MS&#39;,sans-serif !important; font-weight: 500; font-size: 44px; line-height: 48px; color: #d52b1e!important;"><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">Tk.{{$plan->price}}</span><span class="discountWholeSS"></span></span><span class="as" style="color: #d52b1e!important;"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static; color: #d52b1e!important;"><span class="discountDecimalSS"></span></span> <span class="ct" style="color: #d52b1e!important; vertical-align: super!important; font-size: 50%!important; position: static;"></span> <span class="per red-price-month" style="color: #d52b1e!important; font-size: 15px; line-height: 20px; font-family: &#39;Avenir Next LT Pro&#39;,&#39;Avenir Next&#39;,&#39;Futura&#39;,sans-serif;">/mo</span></div>
                                                <div style="text-align: center; float: left; padding: 0 40px;"><span class="per red-price-month" style="color: #d52b1e!important; font-size: 15px; line-height: 20px; font-family: &#39;Avenir Next LT Pro&#39;,&#39;Avenir Next&#39;,&#39;Futura&#39;,sans-serif;"><b>Save <span class="discountPercentSS"></span>%</b> for 6 months</span></div>
                                             </div>
                                          </div>
                                          <div class="ccta cta-align-center"  data-qe-id="15ef92cc-f880-43ae-8518-c650e1a063bf/cq-main-container/container/container_3b7/container/pricing_card_lineups/cq-pricing-card1/container/container/cta_706d">
                                             <a class="ctasecondary ctacenter  binCTASS" href="{{ url('package/buy/'. $plan->id) }}" data-wa-link='pricing-simplestart-buynow'>Buy Now</a>
                                          </div>
                                          <div class="ctext">
                                             <p style="text-align: center; font-size: 12px; color: #393a3d; line-height: 1.3; padding-top: 3px;">or<br>
                                                <a href="{{ url('package/trial/'. $plan->id) }}" data-wa-link="pricing-simplestart-trial" class="trialCTASS ctaTrialLink" style="font-size: 12px; color: #393a3d;">Free 30-Day Trial</a><br>
                                                (excludes discount)
                                             </p>
                                          </div>
                                          <div class="ctext">
                                             <p class="p1">&nbsp;</p>
                                             <p class="p1"><b>Start your business:</b></p>
                                             <ul>
                                                 @php
                                                 $features = $plan->features;
                                                 @endphp
                                                 @foreach ($features as $key => $features)
                                                     <li style="list-style-type: none; list-style-position: outside; text-indent: 0!important; border-top: 1px solid #d4d7dc !important; color: #393a3d; list-style-position: outside; padding: 8px 0; line-height: 1.3!important;">
                                                         {{$features}}
                                                     </li>
                                                 @endforeach
                                             </ul>
                                          </div>
                                       </Section>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           @endforeach

                        </div>

                       
                        <div class="component-footer">
                           <div class="ctext">
                              <p class="p1" style="font-size: 19px; line-height: 24px; padding-bottom: 40px; color: #393a3d;"><a href="#" class="breakline" data-wa-link="pricing-morefeatures" style="color: #393a3d;" adhocenable="false">See more features and compare plans</a></p>
                           </div>
                        </div>
                     </div>
                  </div>
               </Section>
            </section>
         </div>
      </div>
   </Section>
   <Section  class="ccontainer bg-white ss-section" style="" id='section-2' >
      <div class=content-container>
         <div class="ctext">
            <h2>All plans include:</h2>
         </div>
         <div class="grid-image-text container-fluid g_vertical_align_image">
            <div class="row grid-container-960">
               <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class='cimage clearfix '>
                     <!-- If modal option is selected -->
                   <!--   <img class="lazy cq-dd-image img-center" alt="icon_row_access_on_multiple_device_93x72" data-sha="d08cd6cc" data-src="{{ asset('frontend/images/webicon/icon_row_access_on_multiple_device_93x72.png') }}"/> -->


                    <i class="fas fa-mobile-alt img-center" style="font-size: 90px;"></i>
                  </div>
                  <div class="ctext">
                     <h4 style="text-align: center;">Free mobile apps</h4>
                     <p style="text-align: center;"><span class="p2">Ability to work offline is the most fundamental difference between an application and a mobile website.<sup>1</sup></span></p>
                  </div>
               </div>
               <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class='cimage clearfix '>
                     <!-- If modal option is selected -->
                   <!--   <img class="lazy cq-dd-image img-center" alt="icon_row_run_profit_loss_report_56x69" data-sha="1cfadf10" data-src="{{ asset('frontend/images/webicon/icon_row_run_profit_loss_report_56x69.png') }}"/> -->
               <!--     <i class="fal fa-file-chart-line"></i> -->
                   <i class="fa fa-bar-chart img-center" style="font-size: 90px;"></i>
                  </div>
                  <div class="ctext">
                     <h4 style="text-align: center;">Reports and Dashboards</h4>
                     <p style="text-align: center;"><span class="p2">Increase efficiencies and improve decision making with dashboards, reports and analytics.<sup>2</sup></span></p>
                  </div>
               </div>
               <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class='cimage clearfix '>
                     <!-- If modal option is selected -->
                   <!--   <img class="lazy cq-dd-image img-center" alt="icon_row_accountant_qb_expert_96x77" data-sha="0955d5b5" data-src="{{ asset('frontend/images/webicon/icon_row_accountant_qb_expert_96x77.png') }}"/> -->

                     <i class="fas fa-user-circle img-center" style="font-size: 90px;"></i>
                  </div>
                  <div class="ctext">
                     <h4 style="text-align: center;">Accountant access</h4>
                     <p style="text-align: center;"><span class="p2">software programs allow business owners to set permissions that give an outside accountant access.<sup>3</sup></span></p>
                  </div>
               </div>
            </div>
         </div>
         <div class="grid-image-text container-fluid g_vertical_align_image">
            <div class="row grid-container-960">
               <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class='cimage clearfix '>
                     <!-- If modal option is selected -->
                    <!--  <img class="lazy cq-dd-image img-center" alt="icon_row_sync_backup" data-sha="5992ed51" data-src="{{ asset('frontend/images/webicon/icon_row_sync_backup.png') }}"/> -->

                      <i class="fas fa-sync-alt img-center" style="font-size: 90px;"></i>
                  </div>
                  <div class="ctext">
                     <h4 style="text-align: center;">Automatic backups</h4>
                     <p style="text-align: center;"><span class="p2">Quick Access to Files. One of the greatest things about backing up data is the ease at which you are able to retrieve files and information<sup>4</sup></span></p>
                  </div>
               </div>
               <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class='cimage clearfix '>
                     <!-- If modal option is selected -->

                      <i class="fas fa-shield-alt img-center" style="font-size: 90px;"></i>
                  <!--    <img class="lazy cq-dd-image img-center" alt="icon_row_secure_sync_81x72" data-sha="8b62e266" data-src="{{ asset('frontend/images/webicon/icon_row_secure_sync_81x72.png') }}"/> -->
                  </div>
                  <div class="ctext">
                     <h4 style="text-align: center;">Data security</h4>
                     <p style="text-align: center;"><span class="p2">Security software will protect this data from random or targeted attack from viruses and hackers. It will also reduce the chance of falling prey<sup>5</sup></span></p>
                  </div>
               </div>
               <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class='cimage clearfix '>

                   <i class="fas fa-question-circle img-center" style="font-size: 90px;"></i>
                     <!-- If modal option is selected -->
                 <!--     <img class="lazy cq-dd-image img-center" alt="icon_row_chat_89_69" data-sha="6356ebc8" data-src="{{ asset('frontend/images/webicon/icon_row_chat_89_69.png') }}"/> -->
                  </div>
                  <div class="ctext">
                     <h4 style="text-align: center;">Free unlimited support</h4>
                     <p style="text-align: center;"><span class="p2">We keep your software up to date so you can focus on growing your business. Free Technical Support. Free access to our priority support<sup>6</sup></span></p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </Section>
   <section  class="ccontaiqner ss-disclosures" style=""  >
      <div class="cdisclosure bg-grey ">
         <a href="#" class="toggle-disclosure" data-text="Important offers, pricing details, & disclaimers" data-wa-link='op-pricing-disclaimers'>Important offers, pricing details, & disclaimers</a>
         <section class="cdisclosure-content hide-disclosure">
            <div class="cdisclosure-text">
               <p>*Receive a 50% discount off the current monthly price for EarningSoft Online Simple Start, 50% discount off the current monthly price for EarningSoft Online Essentials or 50% discount off the current monthly price for EarningSoft Online Plus for the first 6 months of service, starting from date of enrolment, followed by the then current monthly price. Your account will automatically be charged on a monthly basis until you cancel. You must select the Buy Now option and will not receive a 30-day trial. Offer valid for new EarningSoft Online customers only. No limit on the number of subscriptions ordered. You can cancel at any time. Offer cannot be combined with any other EarningSoft Online offers. Terms, conditions, features, pricing, service and support are subject to change without notice.</p>
               <ol>
                  <li>EarningSoft Online requires a computer with Internet Explorer 10, Firefox, Chrome, or Safari 6 and an Internet connection (a high-speed connection is recommended). The EarningSoft Online mobile app works with iPhone, iPad, and Android phones and tablets. EarningSoft Online is accessible on mobile browsers on iOS, Android, and Blackberry mobile devices. Devices sold separately; data plan required. Not all features are available on the mobile apps and mobile browser. EarningSoft Online mobile access is included with your EarningSoft Online subscription at no additional cost. Data access is subject to cellular/internet provider network availability and occasional downtime due to system and server maintenance and events beyond your control. Product registration required.</li>
                  <li>Data access is subject to Internet or cellular provider network availability and occasional downtime due to events beyond our control.</li>
                  <li>Requires your accountant to subscribe to EarningSoft Online Accountant, sold separately. Alternatively with EarningSoft Online Essentials and Plus you can designate your accountant as one of your users.</li>
                  <li>Microsoft Word and Excel integration requires Word and Excel 2003, 2007 or 2010.</li>
                  <li>128-bit Secure Sockets Layer (SSL) is the same encryption technology used by some of the world&#39;s top banking institutions to secure data that is sent over the Internet.</li>
                  <li>First thirty (30) days of subscription to EarningSoft Online, starting from the date of enrolment, is free. To continue using EarningSoft Online after your 30-day trial, you&#39;ll be asked to present a valid credit card for authorization, and you&#39;ll be charged the then current fee for the service(s) you&#39;ve selected. You can cancel at any time.</li>
                  <li>Support is free during the 30-day trial and included with your paid subscription to EarningSoft Online. Support available Monday to Friday 7.00am - 12.00am (GMT+8). Your subscription must be current. Intuit reserves the right to limit the length of phone calls. Terms, conditions, features, pricing, service and support are subject to change without notice.</li>
               </ol>
            </div>
         </section>
      </div>
      <section  class="ccontainer" style=""  >
      </section>
   </section>
</section>
@endsection
@section('scripts')
@parent
@endsection
