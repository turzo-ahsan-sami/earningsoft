<header id="main_header">
    <section  class="ccontainer" style=""  >
        <div class="include-container">
            <Section  class="ccontainer None" style=""  >

                <header id="global-header" class="non-sticky-header">
                    <div class="header">
                        <div class="container-fluid">
                            <div class="container">
                                <div class="flex-parent">
                                    <section class="hamburger-menu hidden-md">
                                        <a id="drawer-link" class="drawer-closed" href="#sidr-menu"></a>
                                    </section>
                                    <section class="logo">
                                        <a href="{{ url('/') }}">
                                             <!--   <span class="visually-hidden-text global-sprite ba-quickbooks-logo"></span> -->
                                             @php

                                             $logo = App\Admin\Company::first();
                                             @endphp
                                        <span class="logoHeader"><img  style="display:inline-block;
                                          width: 120px;
                                          height: auto;
                                          background-position: 0;
                                          margin-bottom: 5px;
                                          vertical-align: middle;"src="{{ asset("images/company/$logo->logo") }}"></span>

                                        </a>
                                    </section>
                                    <section class="menus menus-left hidden-sm hidden-xs" style="padding-left: 50px;">
                                        <ul>
                                            <li>
                                                <!-- <a class="submenu-header-link" data-wa-link="menu-br-produtos" href="{{ url('products') }}">Products
                                                </a> -->
                                                <a class="submenu-header-link" data-wa-link="menu-br-produtos" href="{{ url('pricing') }}">Products
                                                </a>
                                                <div class="submenu-header-div">
                                                    <span class="submenu-text">Products</span><span class="submenu-arrow down"></span>
                                                </div>
                                            <!--     <ul>
                                                    <li><a href="#" data-wa-link="hdr-nav-smb-features">Features</a></li>
                                                    <li><a data-wa-link="hdr-nav-smb-cloudaccounting" href="#">Cloud Accounting</a></li>
                                                    <li><a data-wa-link="hdr-nav-smb-invoicing" href="#">Invoicing</a></li>
                                                    <li><a data-wa-link="hdr-nav-smb-invoicing" href="#">Project Profitability</a></li>
                                                </ul> -->
                                            </li>
                                            <li>
                                                <a class="submenu-header-link pricing-click-tracking" data-wa-link="hdr-nav-pricing-heading" href="{{ url('pricing') }}">Pricing
                                                </a>
                                                <div class="submenu-header-div">
                                                    <a href="pricing.html" data-wa-link="hdr-nav-pricing-heading"><span class="submenu-text pricing-click-tracking">Pricing</span>
                                                    </a>
                                                </div>
                                            </li>

                                            <li>
                                                <a class="submenu-header-link" data-wa-link="hdr-nav-support-heading" href="learn-support.html">Learn & Support
                                                </a>
                                                <div class="submenu-header-div">
                                                    <a href="#" data-wa-link="hdr-nav-pricing-heading"><span class="submenu-text">Learn & Support</span><span class="submenu-arrow down"></span></a>
                                                </div>
                                            </li>
                                        </ul>
                                    </section>
                                    @if (Auth::user())
                                        @if (Auth::user()->roles->contains('name', 'customer'))
                                        <section class="menus menus-right">
                                            <ul>
                                                
                                                <li class="sign-in-list-item has-divider-before has-divider-after"><a id="SignIn" href="{{ url('dashboard') }}">Go to Dashboard</a></li>
                                            </ul>
                                        </section>
                                        <section class="mobile-buttons hidden">
                                            <ul>
                                                <li class="mobile-button-list-item hide-mobile-button-list-item">
                                                    <a id="SignIn" class="mobile-button-link ctaprimary" href="{{ url('/dashboard') }}">Go to Dashboard
                                                    </a>
                                                </li>
                                            </ul>
                                        </section>
                                        
                                        @endif
                                    @else
                                        <section class="menus menus-right">
                                            <ul>
                                                <li class="free-trial-list-item has-divider-before"><a id="FreeTrial" href="{{ url('pricing') }}">Free Trial</a></li>
                                                <li class="sign-in-list-item has-divider-before has-divider-after"><a id="SignIn" href="{{ url('customer/signin') }}">Sign In</a></li>
                                            </ul>
                                        </section>
                                        <section class="mobile-buttons hidden">
                                            <ul>
                                                <li class="mobile-button-list-item">
                                                    <a id="FreeTrial" class="mobile-button-link ctatertiary2" href="{{ url('pricing') }}">Free Trial
                                                    </a>
                                                </li>
                                                <li class="mobile-button-list-item hide-mobile-button-list-item">
                                                    <a id="SignIn" class="mobile-button-link ctaprimary" href="#">Sign In
                                                    </a>
                                                </li>
                                            </ul>
                                        </section>
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            </Section>
        </div>
    </section>
</header>
