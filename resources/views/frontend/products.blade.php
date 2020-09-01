@extends('layouts.frontend')

@section('content')
    <section  class="ccontainer" style=""  >
    </section>
    <Section  class="ccontainer None pelling-bolt-main-design" style=""  >
        <!-- hero -->
        <section class="ccontainer None pelling-bolt-main-design__hero-txt-img newH1" style="">
            <div class="content-container">
                <div class="horizontal-image-text image-text-1x1-horizontal">
                    <div class="container-fluid">
                        <div class="row resp_grid grid-container-960">
                            <div class="span6 col-md-6  col-sm-6  col-xs-12 col-sm-push-6 bg-grey100">
                                <section class="ccontainer None hidden-xs" style="">
                                    <section class="ccontainer None ss-image-text-v hidden-xs" style="">
                                        <div class="hero-img--cloud-accountants">
                                            <img src="{{ asset('frontend/images/webicon/lockup-desktop.png') }}" class="hero-img--cloud-accountants--desktop" alt="Desktop">
                                            <img src="{{ asset('frontend/images/webicon/lockup-tablet.png') }}" class="hero-img--cloud-accountants--tablet" alt="Tablet">
                                            <img src="{{ asset('frontend/images/webicon/lockup-mobile.png') }}" class="hero-img--cloud-accountants--mobile" alt="Mobile">
                                        </div>
                                    </section>
                                </section>
                            </div>
                            <div class="span6 col-md-6  col-sm-6  col-xs-12 col-sm-pull-6">
                                <div class="ctext">
                                    <h1>ONLINE CLOUD ACCOUNTING SOFTWARE<br>
                                    </h1>
                                    <p class="hero-H1">Run your business in the cloud</p>
                                </div>
                                <section class="ccontainer" style="">
                                    <section class="ccontainer hidden-lg hidden-md hidden-sm" style="">
                                        <section class="ccontainer None ss-image-text-v hidden-lg hidden-md hidden-sm" style="">
                                            <div class="hero-img--cloud-accountants">
                                                <img src="{{ asset('frontend/images/webicon/lockup-desktop.png') }}" class="hero-img--cloud-accountants--desktop" alt="Desktop">
                                                <img src="{{ asset('frontend/images/webicon/lockup-tablet.png') }}" class="hero-img--cloud-accountants--tablet" alt="Tablet">
                                                <img src="{{ asset('frontend/images/webicon/lockup-mobile.png') }}" class="hero-img--cloud-accountants--mobile" alt="Mobile">
                                            </div>
                                        </section>
                                    </section>
                                    <div class="ctext">
                                        <p>Access what you need, when and where you need it. EarningSoft securely stores your data and keeps it up-to-date across all your devices.</p>
                                    </div>
                                    <div class="ccta cta-align-left" data-qe-id="#">
                                        <a class="ctaprimary ctaleft" href="#pricing" data-wa-link="hero-buynow" data-di-id="#hero-buynow">Buy Now<span class="visually-hidden">Buy EarningSoft Online Now</span></a>
                                    </div>
                                    <div class="ccta cta-align-left" data-qe-id="#">
                                        <a class="ctasecondary ctaleft" href="#pricing" data-wa-nav-link="op-hero-invoicing-trial" data-di-id="di-id-e1e2ce8c-8799e2cc">Free 30-day trial</a>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- 4 columns -->
        <section class="ccontainer ss-section pelling-bolt-main-design__three-cols-icons" style=" " id="features">
            <div class="content-container">
                <div class="ctext">
                    <h2 style="text-align: center;">Your business on all your devices</h2>
                </div>
                <div class="grid-image-text container-fluid g_vertical_align_image">
                    <div class="row grid-container-960">
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <div class="cimage clearfix ">
                                <!-- If modal option is selected -->
                                <img class="lazy cq-dd-image img-center" alt="il_utl_devices@2x" data-sha="bf3fd169" data-src="images/webicon/anytime-anywhere-and-on-any-device.png" src="{{ asset('frontend/images/webicon/anytime-anywhere-and-on-any-device.png') }}">
                            </div>
                            <div class="ctext">
                                <h4 style="text-align: center;">Anytime, anywhere and on any device</h4>
                                <p style="text-align: center;">Run your business on the go. EarningSoft keeps your accounts organised in the cloud. Track sales, send invoices and see how your business is doing any time and anywhere.</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <div class="cimage clearfix ">
                                <!-- If modal option is selected -->
                                <img class="lazy cq-dd-image img-center" alt="il_utl_chat_support@2x" data-sha="965ba886" data-src="{{ asset('frontend/images/webicon/share-and-collaborate.png') }}" src="{{ asset('frontend/images/webicon/share-and-collaborate.png') }}">
                            </div>
                            <div class="ctext">
                                <h4 style="text-align: center;">Share and collaborate</h4>
                                <p style="text-align: center;">Invite your accountant, bookkeeper or other users to work on your books. You can add, delete or change user access at any time.</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <div class="cimage clearfix ">
                                <!-- If modal option is selected -->
                                <img class="lazy cq-dd-image img-center" alt="il_utl_upload@2x" data-sha="18665e11" data-src="{{ asset('frontend/images/webicon/do-business-with-airtight-security.png') }}" src="{{ asset('frontend/images/webicon/do-business-with-airtight-security.png') }}">
                            </div>
                            <div class="ctext">
                                <h4 style="text-align: center;">Do business with airtight security</h4>
                                <p style="text-align: center;">EarningSoft uses advanced safeguards and encryption to keep your data private and protected.</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <div class="cimage clearfix ">
                                <!-- If modal option is selected -->
                                <img class="lazy cq-dd-image img-center" alt="il_utl_chat_support@2x" data-sha="965ba886" data-src="{{ asset('frontend/images/webicon/make-decisions-with-real-time-data.png') }}" src="{{ asset('frontend/images/webicon/make-decisions-with-real-time-data.png') }}">
                            </div>
                            <div class="ctext">
                                <h4 style="text-align: center;">Make decisions with real-time data</h4>
                                <p style="text-align: center;">Make better decisions faster with your financial data on-hand at all times. See info like bank balances and transactions, for a more up-to-date and accurate view of your business.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- pricing -->
        <section id="pricing" class="ccontainer pelling-bolt-main-design tt-pricing-cards" >
            <div class="content-container">
                <div class="pelling-bolt-main-design__pricing-cards">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12">
                                <h2 style="text-align: center;">Choose a plan to suit your business</h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tt-toggle-switch">
                                    <span class="text active">Pay monthly</span>
                                    <label class="switch">
                                        <input type="checkbox" id="choose_plan" data-di-id="#choose_plan">
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="text">Pay yearly</span> <span class="text discount">Save 10% extra</span>
                                </div>
                            </div>
                        </div>
                        <div class="row pink-line-title">
                            <div class="col-xs-12">
                                <div class="ctext">
                                    <h3 style="text-align: center;">Save <span class="discountPercentSS">90</span>% for <span class="discountMonth"></span> months</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row cards-tab-menu" data-current-tab="ss-pCard-plan">
                            <div class="col-xs-12">
                                <a href="#" data-tab="ss-pCard-plan" class="active" data-di-id="di-id-6c021e9f-f86bd3f8">Simple Start</a>
                                <a href="#" data-tab="es-pCard-plan" data-di-id="di-id-be1021b-6cb437d5">Essentials</a>
                                <a href="#" data-tab="pl-pCard-plan" data-di-id="di-id-ea076092-f6ef423b">Plus</a>
                            </div>
                            <div class="xs-tab-menu-bt">
                                <span class="prev">&lt;</span>
                                <span class="next">&gt;</span>
                            </div>
                        </div>
                        <div class="row pricing-card">
                            <div id="ss-pCard-plan" class="col-xs-12 col-md-4 sm-tab-card active">
                                <div class="cpricing-card">
                                    <div class="card-header">
                                        <div class="pricing-card-header">
                                            <div class="ctext">
                                                <h3>Simple Start</h3>
                                                <p>Start your business</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pricing-card-content" style="height: 560px;">
                                        <div class="card-price">
                                            <div class="ctext">
                                                <div class="pricing-section pricing-monthly" style="display: table; padding-bottom: 25px;">
                                                    <div class="price" style="color: #afafaf; text-align: center; float: left; padding-right: 20px; font-size: 22px; font-weight: 200 !important;"><span class="line-through" style="border-top: 1px solid #202020; margin: 0 auto; position: relative; display: block;"></span> <span class="aw" style="font-family: 'Geogrotesque','Calibri','Trebuchet MS',sans-serif !important; font-weight: 500; font-size: 22px; line-height: 48px; color: #afafaf;"><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">US$</span><span class="originalWholeSS">15</span></span><span class="as"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;"><span class="originalDecimalSS">00</span></span><span class="ct" style="vertical-align: super!important; font-size: 50%!important; position: static;"></span></div>
                                                    <div class="red-price-text" style="display: table; margin-left: 6px;"><span class="aw" style="font-family: 'Geogrotesque','Calibri','Trebuchet MS',sans-serif !important; font-weight: 500; font-size: 44px; line-height: 48px; color: #393a3d!important;"><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">US$</span><span class="discountWholeSS">7</span></span><span class="as" style="color: #393a3d!important;"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static; color: #393a3d!important;"><span class="discountDecimalSS">50</span></span> <span class="ct" style="color: #393a3d!important; vertical-align: super!important; font-size: 50%!important; position: static;"></span> <span class="per red-price-month" style="color: #393a3d!important; font-size: 15px; line-height: 20px; font-family: 'Avenir Next LT Pro','Avenir Next','Futura',sans-serif;">/mo</span></div>
                                                </div>
                                                <div class="pricing-section pricing-yearly" style="padding-bottom: 25px; display: none;">
                                                    <div class="red-price-text" style="display: table; margin-left: 6px;"><span class="aw" style="font-family: 'Geogrotesque','Calibri','Trebuchet MS',sans-serif !important; font-weight: 500; font-size: 44px; line-height: 48px; color: #393a3d!important;"><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">US$</span><span class="discountWholeSSAnnual">81</span></span><span class="as" style="color: #393a3d!important;"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static; color: #393a3d!important;"><span class="discountDecimalSSAnnual">00</span></span> <span class="ct" style="color: #393a3d!important; vertical-align: super!important; font-size: 50%!important; position: static;"></span> <span class="per red-price-month" style="color: #393a3d!important; font-size: 15px; line-height: 20px; font-family: 'Avenir Next LT Pro','Avenir Next','Futura',sans-serif;">/yr</span></div>
                                                </div>
                                            </div>
                                            <div class="ctext pricing-monthly">
                                                <p class="ccta"><a class="ctaprimary binCTASS" data-wa-link="pricing-simplestart-buynow" href="#">Buy now</a></p>
                                                <p style="text-align: left;"><a href="#" data-wa-link="pricing-simplestart-trial" class="trialCTASS ctaTrialLink">Free 30-Day Trial</a></p>
                                            </div>
                                            <div class="ctext pricing-yearly" style="display:none;">
                                                <p class="ccta"><a class="ctaprimary binCTASSAnnual" data-wa-link="pricing-simplestart-buynow" href="#">Buy now</a></p>
                                                <p style="text-align: left;"><a href="#" data-wa-link="pricing-simplestart-trial" class="trialCTASSAnnual ctaTrialLink">Free 30-Day Trial</a></p>
                                            </div>
                                            <ul class="pricing-features">
                                                <li>Track sales, expenses and profits</li>
                                                <li>Create &amp; send unlimited invoices</li>
                                                <li>Track and manage your sales tax</li>
                                                <li>Works on PC, Mac, and mobile</li>
                                                <li style="margin-bottom: 7px;">For one user, plus your accountant&nbsp;&nbsp;&nbsp;</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="es-pCard-plan" class="col-xs-12 col-md-4 sm-tab-card">
                                <div class="cpricing-card">
                                    <div class="card-header">
                                        <div class="pricing-card-header">
                                            <div class="ctext">
                                                <h3>Essentials</h3>
                                                <p>Run your business</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pricing-card-content" style="height: 560px;">
                                        <div class="card-price">
                                            <div class="ctext">
                                                <div class="pricing-section pricing-monthly" style="display: table; padding-bottom: 25px;">
                                                    <div class="price" style="color: #afafaf; text-align: center; float: left; padding-right: 20px; font-size: 22px; font-weight: 200 !important;"><span class="line-through" style="border-top: 1px solid #202020; margin: 0 auto; position: relative; display: block;"></span> <span class="aw" style="font-family: 'Geogrotesque','Calibri','Trebuchet MS',sans-serif !important; font-weight: 500; font-size: 22px; line-height: 48px; color: #afafaf;"><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">US$</span><span class="originalWholeES">23</span></span><span class="as"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;"><span class="originalDecimalES">00</span></span><span class="ct" style="vertical-align: super!important; font-size: 50%!important; position: static;"></span></div>
                                                    <div class="red-price-text" style="display: table; margin-left: 6px;"><span class="aw" style="font-family: 'Geogrotesque','Calibri','Trebuchet MS',sans-serif !important; font-weight: 500; font-size: 44px; line-height: 48px; color: #393a3d!important;"><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">US$</span><span class="discountWholeES">11</span></span><span class="as" style="color: #393a3d!important;"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static; color: #393a3d!important;"><span class="discountDecimalES">50</span></span> <span class="ct" style="color: #393a3d!important; vertical-align: super!important; font-size: 50%!important; position: static;"></span> <span class="per red-price-month" style="color: #393a3d!important; font-size: 15px; line-height: 20px; font-family: 'Avenir Next LT Pro','Avenir Next','Futura',sans-serif;">/mo</span></div>
                                                </div>
                                                <div class="pricing-section pricing-yearly" style="padding-bottom: 25px; display: none;">
                                                    <div class="red-price-text" style="display: table; margin-left: 6px;"><span class="aw" style="font-family: 'Geogrotesque','Calibri','Trebuchet MS',sans-serif !important; font-weight: 500; font-size: 44px; line-height: 48px; color: #393a3d!important;"><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">US$</span><span class="discountWholeESAnnual">124</span></span><span class="as" style="color: #393a3d!important;"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static; color: #393a3d!important;"><span class="discountDecimalESAnnual">20</span></span> <span class="ct" style="color: #393a3d!important; vertical-align: super!important; font-size: 50%!important; position: static;"></span> <span class="per red-price-month" style="color: #393a3d!important; font-size: 15px; line-height: 20px; font-family: 'Avenir Next LT Pro','Avenir Next','Futura',sans-serif;">/yr</span></div>
                                                </div>
                                            </div>
                                            <div class="ctext pricing-monthly">
                                                <p class="ccta"><a class="ctaprimary binCTAES" data-wa-link="pricing-essentials-buynow" href="#">Buy now</a></p>
                                                <p style="text-align: left;"><a href="#" data-wa-link="pricing-essentials-trials" class="trialCTAES ctaTrialLink">Free 30-Day Trial</a></p>
                                            </div>
                                            <div class="ctext pricing-yearly" style="display:none;">
                                                <p class="ccta"><a class="ctaprimary binCTAESAnnual" data-wa-link="pricing-essentials-buynow" href="#">Buy now</a></p>
                                                <p style="text-align: left;"><a href="#" data-wa-link="pricing-essentials-trials" class="trialCTAESAnnual ctaTrialLink">Free 30-Day Trial</a></p>
                                            </div>
                                            <h4>All Simple start features <span>+</span></h4>
                                            <ul class="pricing-features">
                                                <li>Manage and pay bills</li>
                                                <li>Transact in multiple currencies</li>
                                                <li>Generate sales quotes</li>
                                                <li>For three users, plus your accountant</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="pl-pCard-plan" class="col-xs-12 col-md-4 sm-tab-card">
                                <div class="cpricing-card">
                                    <div class="card-header">
                                        <div class="pricing-card-header">
                                            <div class="ctext">
                                                <h3>Plus</h3>
                                                <p>Grow your business</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pricing-card-content" style="height: 560px;">
                                        <div class="card-price">
                                            <div class="ctext">
                                                <div class="pricing-section pricing-monthly" style="display: table; padding-bottom: 25px;">
                                                    <div class="price" style="color: #afafaf; text-align: center; float: left; padding-right: 20px; font-size: 22px; font-weight: 200 !important;"><span class="line-through" style="border-top: 1px solid #202020; margin: 0 auto; position: relative; display: block;"></span> <span class="aw" style="font-family: 'Geogrotesque','Calibri','Trebuchet MS',sans-serif !important; font-weight: 500; font-size: 22px; line-height: 48px; color: #afafaf;"><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">US$</span><span class="originalWholePL">31</span></span><span class="as"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;"><span class="originalDecimalPL">00</span></span><span class="ct" style="vertical-align: super!important; font-size: 50%!important; position: static;"></span></div>
                                                    <div class="red-price-text" style="display: table; margin-left: 6px;"><span class="aw" style="font-family: 'Geogrotesque','Calibri','Trebuchet MS',sans-serif !important; font-weight: 500; font-size: 44px; line-height: 48px; color: #393a3d!important;"><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">US$</span><span class="discountWholePL">15</span></span><span class="as" style="color: #393a3d!important;"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static; color: #393a3d!important;"><span class="discountDecimalPL">50</span></span> <span class="ct" style="color: #393a3d!important; vertical-align: super!important; font-size: 50%!important; position: static;"></span> <span class="per red-price-month" style="color: #393a3d!important; font-size: 15px; line-height: 20px; font-family: 'Avenir Next LT Pro','Avenir Next','Futura',sans-serif;">/mo</span></div>
                                                </div>
                                                <div class="pricing-section pricing-yearly" style="padding-bottom: 25px; display: none;">
                                                    <div class="red-price-text" style="display: table; margin-left: 6px;"><span class="aw" style="font-family: 'Geogrotesque','Calibri','Trebuchet MS',sans-serif !important; font-weight: 500; font-size: 44px; line-height: 48px; color: #393a3d!important;"><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">US$</span><span class="discountWholePLAnnual">167</span></span><span class="as" style="color: #393a3d!important;"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static; color: #393a3d!important;"><span class="discountDecimalPLAnnual">40</span></span> <span class="ct" style="color: #393a3d!important; vertical-align: super!important; font-size: 50%!important; position: static;"></span> <span class="per red-price-month" style="color: #393a3d!important; font-size: 15px; line-height: 20px; font-family: 'Avenir Next LT Pro','Avenir Next','Futura',sans-serif;">/yr</span></div>
                                                </div>
                                            </div>
                                            <div class="ctext pricing-monthly">
                                                <p class="ccta"><a class="ctaprimary binCTAPL" data-wa-link="pricing-plus-buynow" href="#">Buy now</a></p>
                                                <p style="text-align: left;"><a href="#" data-wa-link="pricing-plus-trials" class="trialCTAPL ctaTrialLink">Free 30-Day Trial</a></p>
                                            </div>
                                            <div class="ctext pricing-yearly" style="display: none;">
                                                <p class="ccta"><a class="ctaprimary binCTAPLAnnual" data-wa-link="pricing-plus-buynow" href="#">Buy now</a></p>
                                                <p style="text-align: left;"><a href="#" data-wa-link="pricing-plus-trials" class="trialCTAPLAnnual ctaTrialLink">Free 30-Day Trial</a></p>
                                            </div>
                                            <h4>All Essentials features <span>+</span></h4>
                                            <ul class="pricing-features">
                                                <li>Track inventory</li>
                                                <li>Create purchase orders</li>
                                                <li>Track project or job profitability</li>
                                                <li>For five users, plus your accountant</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-container">
                <section class="ccontainer" style="">
                    <div class="ccta cta-align-center" data-qe-id="#">
                        <a class="ctasecondary ctacenter margint" href="pricing.html" data-di-id="di-id-e0084bc0-b6975e7c">View pricing and plans</a>
                    </div>
                </section>
            </div>
            <script type="text/javascript">
            $(function() {
                var plan_selector_toggle = $('#choose_plan');
                $(window).on('load', function(){
                    plan_selector_handler();
                });
                plan_selector_toggle.on('click', function() {
                    plan_selector_handler();
                });
                function plan_selector_handler(){
                    if (plan_selector_toggle.is(":checked")) {
                        $(".pricing-monthly").hide();
                        $(".pricing-yearly").show();
                    } else {
                        $(".pricing-monthly").show();
                        $(".pricing-yearly").hide();
                    }
                }
            });
            </script>
            <script>
            $(".cards-tab-menu").find("a").click(function() {
                $(".cards-tab-menu").find("a").removeClass("active");
                $(this).addClass("active");

                var cardTab = $(this).attr("data-tab");
                $(".sm-tab-card").removeClass("active");
                $("#" + cardTab).addClass("active");

                $(".cards-tab-menu").attr("data-current-tab", cardTab);
                hideCta();
                return false;
            });



            $(".xs-tab-menu-bt").find(".prev").click(function() {
                ctaPrev();
            });
            $(".xs-tab-menu-bt").find(".next").click(function() {
                ctaNext();
            });

            function ctaPrev() {
                //find current tab
                var currentTab = $(".cards-tab-menu").attr("data-current-tab");
                $(".cards-tab-menu").find("a").removeClass("active");
                $(".sm-tab-card").removeClass("active");

                switch (currentTab) {
                    case "ss-pCard-plan":
                    break;
                    case "es-pCard-plan":
                    $("#ss-pCard-plan").addClass("active");
                    $(".cards-tab-menu").attr("data-current-tab", "ss-pCard-plan");
                    $(".cards-tab-menu").find("a[data-tab=ss-pCard-plan]").addClass("active");
                    break;
                    case "pl-pCard-plan":
                    $("#es-pCard-plan").addClass("active");
                    $(".cards-tab-menu").attr("data-current-tab", "es-pCard-plan");
                    $(".cards-tab-menu").find("a[data-tab=es-pCard-plan]").addClass("active");
                    break;
                    default:
                    // code block
                }
                hideCta();
            };

            function ctaNext() {
                //find current tab
                var currentTab = $(".cards-tab-menu").attr("data-current-tab");
                $(".cards-tab-menu").find("a").removeClass("active");
                $(".sm-tab-card").removeClass("active");
                switch (currentTab) {
                    case "ss-pCard-plan":
                    $("#es-pCard-plan").addClass("active");
                    $(".cards-tab-menu").attr("data-current-tab", "es-pCard-plan");
                    $(".cards-tab-menu").find("a[data-tab=es-pCard-plan]").addClass("active");
                    break;
                    case "es-pCard-plan":
                    $("#pl-pCard-plan").addClass("active");
                    $(".cards-tab-menu").attr("data-current-tab", "pl-pCard-plan");
                    $(".cards-tab-menu").find("a[data-tab=pl-pCard-plan]").addClass("active");
                    break;
                    case "pl-pCard-plan":
                    break;
                    default:
                    // code block
                }
                hideCta();
            };

            function hideCta() {

                var currentTab = $(".cards-tab-menu").attr("data-current-tab");
                $(".xs-tab-menu-bt").find("span").removeClass("hide");

                if (currentTab == "ss-pCard-plan") {
                    $(".xs-tab-menu-bt").find(".prev").addClass("hide");
                }
                if (currentTab == "pl-pCard-plan") {
                    $(".xs-tab-menu-bt").find(".next").addClass("hide");
                }
            };
            </script>
        </section>
        <!-- FAQ -->
        <section class="ccontainer None ss-section pelling-bolt-main-design__faqs" style="" id="faq">
            <div class="content-container">
                <div class="ctext">
                    <h2 style="text-align: center;">FAQ</h2>
                </div>
                <section class="ccontainer None" style="">
                    <div class="content-container">
                        <div class="cfaq-qa">
                            <div>
                                <div class="llp-faq-section" data-di-id="di-id-3d8d9bfe-36e41fcb">
                                    <div class="llp-faq-head">
                                        <span class="llp-faq-close-arrow"></span>
                                        <div class="ctext">
                                            <p>What is online cloud accounting software?</p>
                                        </div>
                                    </div>
                                    <div class="llp-faq-content">
                                        <div class="ctext">
                                            <p>Cloud accounting software allows users to manage their finances from any device.</p>
                                            <p>There is nothing saved on your computer, and there are no disks to load. Log in on any device, and jump right into your EarningSoft Online account. Your data and settings are right there, stored safely in the cloud.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cfaq-qa">
                            <div>
                                <div class="llp-faq-section" data-di-id="di-id-61eed51d-ef079c03">
                                    <div class="llp-faq-head">
                                        <span class="llp-faq-close-arrow"></span>
                                        <div class="ctext">
                                            <p>What are the benefits of working in the cloud?</p>
                                        </div>
                                    </div>
                                    <div class="llp-faq-content">
                                        <div class="ctext">
                                            <p>When you work in the cloud, you’re always using the latest version of EarningSoft Online. Because you're working online, you can pick up where you left off with the latest data on all your devices.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cfaq-qa cfaq-qa-d-bottom">
                            <div>
                                <div class="llp-faq-section" data-di-id="di-id-9e25d923-6658c840">
                                    <div class="llp-faq-head">
                                        <span class="llp-faq-close-arrow"></span>
                                        <div class="ctext">
                                            <p>How do I make sure my data is secure?</p>
                                        </div>
                                    </div>
                                    <div class="llp-faq-content">
                                        <div class="ctext">
                                            <p>When you use EarningSoft Online, your data is stored on our servers in the cloud. We know that data is one of your company's most valuable assets, so we go to great lengths to protect it.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
        <!-- green bar -->
        <section class="ccontainer None pelling-bolt-main-design__green-banner beams" style="">
            <section class="ccontainer None ss-bullets-columns" style="">
                <div class="content-container">
                    <div class="ctext">
                        <h3 style="text-align: center;">See how EarningSoft can work for your business</h3>
                    </div>
                    <div class="ccta cta-align-center">
                        <a class="ctasecondary ctacenter" href="pricing.html">Free 30-day trial</a>
                    </div>
                </div>
            </section>
            <div class="ctext">
                <div class="beam-parent-container">
                    <div class="beam-11"></div>
                    <div class="beam-22"></div>
                    <div class="beam-33"></div>
                </div>
            </div>
        </section>
        <script>
        $(document).on('click', 'a[href^="#"]', function (event) {
            event.preventDefault();
            $('html, body').animate({
                scrollTop: $($.attr(this, 'href')).offset().top
            }, 500);
        });
        </script>
    </Section>
@endsection
@section('scripts')
    @parent

@endsection
