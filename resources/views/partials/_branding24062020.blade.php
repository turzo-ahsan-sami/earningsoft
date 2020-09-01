@php
// $userCustomerId = Auth::user()->customer_id;
// $mainUserOfCustomer = \App\User::where('customer_id', $userCustomerId)->orderBy('id', 'asc')->first();
// // $slug = Rinvex\Subscriptions\Models\PlanSubscription::where('user_id', $mainUserOfCustomer->id)->value('slug');
// $slug = DB::table('plan_subscriptions')->where('user_id', $mainUserOfCustomer->id)->value('slug');
// $modules = $mainUserOfCustomer->subscription($slug)->plan->modules;

// $user = Auth::user();
// $slug = Rinvex\Subscriptions\Models\PlanSubscription::where('user_id', $user->id)->value('slug');
// $modules = $user->subscription($slug)->plan->modules;

$modules = App\Service\UserAccessHelper::getAuthorizedModules();

@endphp


<div class="navbar-brand">
    <style type="text/css">
    #main-menu li {
        padding: 0px  5px !important;
       /* background-color: #2C2E2F;*/
    }
 /*    #main-menu li .mobileNav {
        padding: 0px  5px !important;
        background-color: #2C2E2F;
    }*/
    .navbar.horizontal-menu .navbar-inner .navbar-nav.mobile-is-visible{
        background-color: #2C2E2F;

    }
    .navbar-brand{
        font-size:14px!important;
        line-height: 18px;
    }

    #main-menu > li > ul > li {
    height: 35px !important;
}

.navbar.horizontal-menu .navbar-inner .navbar-nav a {
    padding: 7px 5px;

}
</style>
<ul id="main-menu" class="navbar-nav">
    <li class="has-sub">
        <a id="logo" style="font-size:15px !important;" href="{{url('/dashboard')}}" class="logo animated slideInDown">
            {{ config('app.name') }}
        </a>
        <ul>
            <li>
                <a href="{{ url('gnr/home') }}">
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span class="title">Configuration</span>
                </a>
            </li>
            {{-- @endif --}}

            @foreach ($modules as $module)
                @if ($module->slug == 'bil')
                <li>
                    <a class="animated fadeInLeft" href="{{ url('pos/home') }}">
                        <i class="fa fa-usd" aria-hidden="true"></i>
                        <span class="title">Billing</span>
                    </a>
                </li>
                   
                 @elseif ($module->slug == 'acc')
                    <li>
                        <a class="animated fadeInLeft" href="{{ url('acc/home') }}">
                            <i class="fa fa-tachometer" aria-hidden="true"></i>
                            <span class="title">Accounting</span>
                        </a>
                    </li>
                @elseif ($module->slug == 'inv')
                    <li>
                        <a class="animated fadeInLeft" href="{{ url('inv/home') }}">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            <span class="title">Inventory</span>
                        </a>
                    </li>
                @elseif ($module->slug == 'fams')
                    <li>
                        <a class="animated fadeInLeft" href="{{ url('fams/home') }}">
                            <i class="fa fa-tachometer" aria-hidden="true"></i>
                            <span class="title">FAMS</span>
                        </a> 
                    </li>
                    
               
                @endif
            @endforeach
            <li>
                <a class="animated fadeInLeft" href="{{ url('report/home') }}">
                    <i class="fa fa-line-chart" aria-hidden="true"></i>
                    <span class="title">Reports</span>
                </a> 
            </li>
            {{-- @if (1)
            <li>
                    <a href="@if ($hrStatus==1) {{ url('hrm/home') }} @else {{ url('#') }}@endif">
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <span class="title">HR and Payroll</span>
                    </a>
                </li>
                @endif
                <li>
                    <a href="@if ($mrfStatus==1) {{ url('mfn/home') }} @else {{ url('#') }}@endif">
                        <i class="fa fa-money" aria-hidden="true"></i>
                        <span class="title">Micro Finance</span>
                    </a>
                </li>
            </ul> --}}
        </li>
    </ul>
    <a href="#" data-toggle="settings-pane" data-animate="true">
        <i class="linecons-cog"></i>
    </a>
</div>

<!-- Mobile Toggles Links -->
<div class="nav navbar-mobile">
    <!-- This will toggle the mobile menu and will be visible only on mobile devices -->
    <div class="mobile-menu-toggle">
        <!-- This will open the popup with user profile settings, you can use for any purpose, just be creative -->
     {{--    <a href="#" data-toggle="settings-pane" data-animate="true">
            <i class="linecons-cog"></i>
        </a> --}}
     {{--    <a href="#" data-toggle="user-info-menu-horizontal">
            <i class="fa-bell-o"></i>
            <span class="badge badge-success">7</span>
        </a> --}}
        <!-- data-toggle="mobile-menu-horizontal" will show horizontal menu links only -->
        <!-- data-toggle="mobile-menu" will show sidebar menu links only -->
        <!-- data-toggle="mobile-menu-both" will show sidebar and horizontal menu links -->
        <a href="#" data-toggle="mobile-menu-horizontal">
            <i class="fa-bars"></i>
        </a>
    </div>
</div>
<div class="navbar-mobile-clear"></div>
