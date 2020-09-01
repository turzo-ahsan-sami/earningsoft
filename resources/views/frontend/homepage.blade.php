@extends('layouts.frontend')

@section('content')
    <section  class="ccontainer" style=""  >
        <div class="ccontainer chero-layers chero-styles-9959 chero-layers-2-cta percent-header-image ">
            <!--bg layer -->
            <div class="bg-layer  ">
                <div class="bg-img  bg-xs-center bg-sm-center bg-md-center "></div>
            </div>
            <!--overlay layer-->
            <div class="overlay-layer">
                <div class="container-fluid">
                    <div class="overlay-bg   bg-black "></div>
                </div>
            </div>
            <!--content layer-->
            <div class="content-layer">
                <div class="container-fluid">
                    <div class="row resp_grid grid-container-960">
                        <div class="span6 col-md-6  col-sm-7  col-xs-12 ">
                            <Section  class="ccontainer ccontainer-hero-content-cta-text" style=""  >
                                <div class="ctext">
                                    <div class="visible-xs"><br>
                                        <br>
                                        <br>
                                        <br>
                                    </div>
                                    <h1 style="text-align: left; font-size: 12px; line-height: 1.5; letter-spacing: 2.5px;"><span class="text-white">{{ $bannerInfo->banner_text1}}<br>
                                        &nbsp;</span>
                                    </h1>
                                    <h1 style="text-align: left; font-size: 28px; line-height: 1.3;"><span class="text-white">{{ $bannerInfo->banner_text2}}</span></h1>
                                    <h4 style="text-align: left;"><span class="text-white"><br>{{ $bannerInfo->banner_text3}}
                                         <br class="hidden-xs">
                                       
                                        &nbsp;</span>
                                    </h4>
                                    <div style="padding-top: 10px;">
                                        <div class="hidden-xs">
                                            <div style="padding: 0 5px; display: inline-block;"><a class="ctasecondary ctacenter" href="#" data-wa-link="hero-buynow">{{ $bannerInfo->button_text1}}<span class="discountPercent"></span>%<span class="visually-hidden">Buy EarningSoft Online Now and Save</span></a></div>
                                            <div style="padding: 0 5px; display: inline-block;"><a class="ctatertiary2 ctacenter" href="{{ url('pricing')}}" data-wa-link="hero-trial">{{ $bannerInfo->button_text2}}<span class="visually-hidden">Buy EarningSoft Online Now and Save</span></a></div>
                                        </div>
                                        <div class="visible-xs" style="text-align: center;">
                                            <div style="padding-bottom: 10px;"><a class="ctasecondary ctacenter" href="#" data-wa-link="hero-buynow">{{ $bannerInfo->button_text1}} <span class="discountPercent"></span>%<span class="visually-hidden">Buy EarningSoft Online Now and Save</span></a></div>
                                            <div><a class="ctatertiary2 ctacenter" href="{{ url('pricing')}}" data-wa-link="hero-trial">{{ $bannerInfo->button_text2}}<span class="visually-hidden">Buy EarningSoft Online Now and Save</span></a></div>
                                        </div>
                                    </div>
                                </div>
                                <Section  class="ccontainer hidden-lg hidden-md hidden-sm hidden-xs" style=""  >
                                    <div class="ccta cta-align-left"  data-qe-id="3f672903-bd5b-4549-a05d-b243712eebed"  >
                                        <a class="ctaprimary ctaleft  " href="{{ url('pricing')}}" data-wa-link='hero-buynow'>Buy Now & Save <span class="discountPercent"></span>%<span class='visually-hidden'>Buy EarningSoft Online Now and Save</span></a>
                                    </div>
                                    <Section  class="ccontainer" style="padding-top: 10px; padding-right: 0px; padding-bottom: 0px; "  >
                                        <div class="ctext">
                                            <p style="color: #ffffff;"><b><span>or</span> <a adhocenable="false" style="color: #2EACF3;" href="{{ url('pricing')}}" data-wa-link="hero-trial">Try free for 30 days</a></b></p>
                                        </div>
                                    </Section>
                                </Section>
                            </Section>
                        </div>
                        <div class="hidden-md hidden-sm hidden-xs ">
                            <section  class="ccontainer" style=""  >
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
        .chero-styles-9959 .bg-layer {
            background-color: #393a3d;
        }
        .chero-styles-9959 .overlay-bg {
            opacity: 0.25;
        }
        @media only screen and (max-width: 767px) {
            .chero-styles-9959 .bg-img {
                background-image: url("{{ asset("images/banner/$bannerInfo->mini_image") }}");width:480px;height:400px;
            }
            .chero-styles-9959 .overlay-bg {
                width:100%;
            }
        }
        @media only screen and (min-width: 768px) and (max-width: 991px) {
            .chero-styles-9959 .bg-img {
                background-image: url("{{ asset("images/banner/$bannerInfo->banner_image") }}");width:992px;height:400px;
            }
            .chero-styles-9959 .overlay-bg {
                width:0%;
            }
        }
        @media only screen and (min-width: 992px) {
            .chero-styles-9959 .bg-img {
                background-image: url("{{ asset("images/banner/$bannerInfo->banner_image") }}");width:1600px;height:400px;
            }
            .chero-styles-9959 .overlay-bg {
                width:0%;
            }
        }
        </style>
        <div class="ctext">
            <p class='edit-only'>Drag Hero Here.</p>
        </div>
    </section>
    <section  class="ccontainer" style=""  >
        <Section  class="ccontainer bg-white ss-section" style="" id='section-1' >
            <div class=content-container>
                <div class="ctext">
                    <h2>Earningsoft lets you see how<br>
                        your business is doing instantly. &nbsp;
                    </h2>
                    <p><span class="section-subheader">Learn more about our features</span></p>
                </div>
                 @foreach($featureSections as $featureSection)
                <div class="horizontal-image-text image-text-1x1-horizontal">
                    <div class="container-fluid">
                        <div class="row resp_grid grid-container-960">
                            <div class="span7 col-md-7  col-sm-7  col-xs-12 ">
                                <div class='cimage clearfix '>
                                    <!-- If modal option is selected -->
                                    <img class="lazy cq-dd-image img-left image-scale" alt="img_row_cloud_all_devices_577_371" data-sha="ed1f9d40" data-src="{{ asset("images/featureSection/$featureSection->image") }}"/>
                                </div>
                                <section  class="ccontainer" style=""  >
                                </section>
                            </div>
                            <div class="span5 col-md-5  col-sm-5  col-xs-12 ">
                                <div class="ctext">
                                    <p class="h1b">{{$featureSection->name}}</p>
                                    <p class="p2">{{$featureSection->description}}</p>
                                </div>
                                <Section  class="ccontainer None hidden-sm hidden-xs" style="padding-left: 40px; "  >
                                    <div class="ccta cta-align-left"  data-qe-id="0f841425-d340-4bc4-9220-01a3f5839c4b/cq-main-container/container/container_0/container/horizontal_imagetext2/cq-container0/container/cta"  >
                                        <a class="ctasecondary ctaleft  " href="#" data-wa-link='features-cloudaccounting'>Learn More</a>
                                    </div>
                                </Section>
                            </div>
                        </div>
                    </div>
                </div>
                 @endforeach
                <div class="ctext">
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                </div>
   
                <div class="ctext">
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                </div>
                <div class="ccta cta-align-center"  data-qe-id="0f841425-d340-4bc4-9220-01a3f5839c4b/cq-main-container/container/container_0/container/cta"  >
                    <a class="ctaprimary ctacenter" href="#" data-wa-link='features-all'>See All Features</a>
                </div>
                <div class="ctext">
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                </div>
            </div>
        </Section>


                          <!-- pricing -->
            <section id="pricing" class="ccontainer pelling-bolt-main-design tt-pricing-cards" >
                <div class="content-container">
                    <div class="pelling-bolt-main-design__pricing-cards">
                        <div class="container">
                            <div class="row">
                                <div class="col-xs-12">
                                    <h2 style="text-align: center;">There’s a EarningSoft for every business</h2>
                                </div>
                            </div>
                         
                       
                          <!--   <div class="row cards-tab-menu" data-current-tab="ss-pCard-plan">
                                <div class="col-xs-12">
                                    <a href="#" data-tab="ss-pCard-plan" class="active" data-di-id="di-id-6c021e9f-f86bd3f8">Simple Start</a>
                                    <a href="#" data-tab="es-pCard-plan" data-di-id="di-id-be1021b-6cb437d5">Essentials</a>
                                    <a href="#" data-tab="pl-pCard-plan" data-di-id="di-id-ea076092-f6ef423b">Plus</a>
                                </div>
                                <div class="xs-tab-menu-bt">
                                    <span class="prev">&lt;</span>
                                    <span class="next">&gt;</span>
                                </div>
                            </div> -->
                            <div class="row pricing-card" style="border: 0;background: transparent;box-shadow: none;">
                              @foreach($planList as $plan)
                                <div id="ss-pCard-plan" class="col-xs-12 col-md-4 sm-tab-card active" style="border-left:none">
                                    <div class="cpricing-card">
                                        <div class="card-header" style="background-color:#ffffff">
                                            <div class="pricing-card-header" style="padding-left:0px !important;border-bottom: 0;">
                                                <div class="ctext">
                                                    <h3>{{$plan->name}}</h3>
                                                    <p>Start your business</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-card-content" style="height: 560px;">
                                            <div class="card-price">
                                                <div class="ctext">
                                                    <div class="pricing-section pricing-monthly" style="display: table; padding-bottom: 25px;">
                                                     <!--    <div class="price" style="color: #afafaf; text-align: center; float: left; padding-right: 20px; font-size: 22px; font-weight: 200 !important;"><span class="line-through" style="border-top: 1px solid #202020; margin: 0 auto; position: relative; display: block;"></span> <span class="aw" style="font-family: 'Geogrotesque','Calibri','Trebuchet MS',sans-serif !important; font-weight: 500; font-size: 22px; line-height: 48px; color: #afafaf;"><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">US$</span><span class="originalWholeSS">15</span></span><span class="as"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;"><span class="originalDecimalSS">00</span></span><span class="ct" style="vertical-align: super!important; font-size: 50%!important; position: static;"></span></div> -->
                                                        <div class="red-price-text" style="display: table; margin-left: 6px;"><span class="aw" style="font-family: 'Geogrotesque','Calibri','Trebuchet MS',sans-serif !important; font-weight: 500; font-size: 44px; line-height: 48px; color: #393a3d!important;"><!-- <span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">US$</span> --><span class="discountWholeSS">Tk.{{$plan->price}}</span></span><span class="as" style="color: #393a3d!important;"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static; color: #393a3d!important;"><!-- <span class="discountDecimalSS">50</span> --></span> <span class="ct" style="color: #393a3d!important; vertical-align: super!important; font-size: 50%!important; position: static;"></span> <span class="per red-price-month" style="color: #393a3d!important; font-size: 15px; line-height: 20px; font-family: 'Avenir Next LT Pro','Avenir Next','Futura',sans-serif;">/mo</span></div>
                                                    </div>
                                                    <div class="pricing-section pricing-yearly" style="padding-bottom: 25px; display: none;">
                                                        <div class="red-price-text" style="display: table; margin-left: 6px;"><span class="aw" style="font-family: 'Geogrotesque','Calibri','Trebuchet MS',sans-serif !important; font-weight: 500; font-size: 44px; line-height: 48px; color: #393a3d!important;"><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static;">US$</span><span class="discountWholeSSAnnual">81</span></span><span class="as" style="color: #393a3d!important;"></span><span class="ac" style="vertical-align: super!important; font-size: 50%!important; position: static; color: #393a3d!important;"><span class="discountDecimalSSAnnual">00</span></span> <span class="ct" style="color: #393a3d!important; vertical-align: super!important; font-size: 50%!important; position: static;"></span> <span class="per red-price-month" style="color: #393a3d!important; font-size: 15px; line-height: 20px; font-family: 'Avenir Next LT Pro','Avenir Next','Futura',sans-serif;">/yr</span></div>
                                                    </div>
                                                </div>
                                                <div class="ctext pricing-monthly">
                                                    <p class="ccta"><a class="ctaprimary binCTASS" data-wa-link="pricing-simplestart-buynow" href="{{ url('package/buy/'. $plan->id) }}">Buy now</a></p>
                                                    <p style="text-align: left;"><a href="{{ url('package/trial/'. $plan->id) }}" data-wa-link="pricing-simplestart-trial" class="trialCTASS ctaTrialLink">Free 30-Day Trial</a></p>
                                                </div>
                                                <div class="ctext pricing-yearly" style="display:none;">
                                                    <p class="ccta"><a class="ctaprimary binCTASSAnnual" data-wa-link="pricing-simplestart-buynow" href="#">Buy now</a></p>
                                                    <p style="text-align: left;"><a href="{{ url('package/trial/'. $plan->id) }}" data-wa-link="pricing-simplestart-trial" class="trialCTASSAnnual ctaTrialLink">Free 30-Day Trial</a></p>
                                                </div>
                                                <ul class="pricing-features">
                                                 @php
                                                 $features = $plan->features;
                                                 @endphp
                                                 @foreach ($features as $key => $features)
                                                    <li>{{$features}}</li>
                                                       @endforeach
                                                 
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                  @endforeach
                            
                              
                            </div>
                        </div>
                    </div>
                </div>
      

            <div class="ccta cta-align-center"  data-qe-id="0f841425-d340-4bc4-9220-01a3f5839c4b/cq-main-container/container/container_3/container/cta"  >
                    <a class="ctaprimary ctacenter  " href="#" data-wa-link='getstarted-pricing-cta'>See Plans & Pricing</a>
                </div>

           
            </section>
        <Section  class="ccontainer hidden-xs resp_img" style="height:436px;" >
            <div class="bg bg-charcoal-grey">
                <img class="center" alt="" data-sha="44aa214d" src="{{ asset('/frontend/images/home/img_row_testimonial_moltenwonky_1600_436.jpg') }}" style=""/>
            </div>
            <div class="grid-horizontal-image-text ifull-bleed grid_h_stars_text" id="gridh99">
                <div class="container-fluid">
                    <div class="row grid-container-960">
                        <div class="cleft col-md-5 col-md-offset-1 col-sm-6  col-xs-12 ">
                        </div>
                        <div class="cright col-md-5  col-sm-6  col-xs-12 ">
                            <div class="ctext">
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <div class="ctestimonial ">
                                    <div class="ctestimonial_rating"><span class="rating rating-4">&nbsp;</span></div>
                                </div>
                                <p style="text-align: center;"><span class="h2" style="color: #FFFFFF;">&quot;{{$userReviewsLatest->comment ?? ''}}&quot;</span></p>
                                <p>&nbsp;</p>
                                <p style="text-align: center; color: #FFFFFF;">{{$userReviewsLatest->name ?? ''}}</p>
                                <p>&nbsp;</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>
            #gridh99 {
                background-color: None;
            }
            </style>
        </Section>
        <Section  class="ccontainer" style=""  >
            <div class="grid-horizontal-image-text ifull-bleed " id="gridh81">
                <div class="container-fluid">
                    <div class="row grid-container-960">
                     @foreach($userReviews as $userReview)
                        <div class="cleft col-md-6  col-sm-6  col-xs-12 ">
                            <div class="ctestimonial testimonial_mini">
                                <div class="row" >
                                    <div class="ctestimonial_mini_image_section col-md-4 col-sm-4 col-xs-12">
                                        <div class='cimage clearfix '>
                                            <!-- If modal option is selected -->
                                            <img class="lazy cq-dd-image img-left img-circle" alt="img_row_testimonial-ashread_140_140" data-sha="819f5927" data-src="{{ asset('/frontend/images/home/img_row_testimonial-ashread_140_140.jpg') }}"/>
                                        </div>
                                    </div>
                                    <div class="ctestimonial_reviews_section col-md-8 col-sm-8 col-xs-12">
                                        <div class="ctestimonial_review">
                                            "{{$userReview->comment}}"
                                        </div>
                                        <div class="ctestimonial_author">
                                           -{{$userReview->name}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <style>
                            @media (max-width: 767px){
                                .testimonial_mini .row{
                                    margin-left:0px;
                                    margin-right:0px;
                                    padding:0px 20px 0px 20px;
                                }
                            }
                            </style>
                        </div>
                         @endforeach

                    
                    <!--   <div class="cright col-md-6  col-sm-6  col-xs-12 ">
                            <div class="ctestimonial testimonial_mini">
                                <div class="row" >
                                    <div class="ctestimonial_mini_image_section col-md-4 col-sm-4 col-xs-12">
                                        <div class='cimage clearfix '>
                                          
                                            <img class="lazy cq-dd-image img-left img-circle" alt="img_row_testimonial-barkingmad_140_140" data-sha="df38ff03" data-src="{{ asset('/frontend/images/home/img_row_testimonial-barkingmad_140_140.jpg') }}"/>
                                        </div>
                                    </div>
                                    <div class="ctestimonial_reviews_section col-md-8 col-sm-8 col-xs-12">
                                        <div class="ctestimonial_review">
                                            "We use the Earningsoft app when we’re visiting customers... we can now send a quote or invoice on the spot."
                                        </div>
                                        <div class="ctestimonial_author">
                                            - Amanda, Barking Mad
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <style>
                            @media (max-width: 767px){
                                .testimonial_mini .row{
                                    margin-left:0px;
                                    margin-right:0px;
                                    padding:0px 20px 0px 20px;
                                }
                            }
                            </style>
                        </div> -->
                    </div>
                </div>
            </div>
            <style>
            #gridh81 {
                background-color: #393a3d;
            }
            </style>
        </Section>

  
    <!--     <Section  class="ccontainer bg-grey ss-section" style="" id='section-4' >
            <div class=content-container>
                <div class="ctext">
                    <h2>Get Started with Earningsoft today!</h2>
                </div>


                <div class="ccta cta-align-center"  data-qe-id="0f841425-d340-4bc4-9220-01a3f5839c4b/cq-main-container/container/container_3/container/cta"  >
                    <a class="ctaprimary ctacenter  " href="#" data-wa-link='getstarted-pricing-cta'>See Plans & Pricing</a>
                </div>
                <div class="ctext">
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                </div>
            </div>
        </Section> -->
        <div class="ctext">
            <style>
            html, body {
                overflow-x: initial;
            }
            section#main {
                overflow: hidden;
            }
            </style>
        </div>
    </section>
@endsection
@section('scripts')
@parent

@endsection
