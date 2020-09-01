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
        ul.dropdown-menu.profile {
         padding: 1em;
         min-width: 189px;
          top:101%;
}
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
        margin-top: -13px;

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


@media only screen and (min-width:769px) {
  .navbar-mobile-right {
    display:none;
  
  }

    .navbar-mobile-center {
    display:none;
  
  }
  .moduleList{
    display:none;
  }

}

@media only screen and (max-width: 600px) {
    .img-responsive{
        width:20% !important;
}

@media only screen and (max-width: 768px) {
    .img-responsive{
        width:90% !important;
}
    .panel{
        padding: 4px 4px;
    }
  .navbar-mobile-right {
    display:block;
    float: left;
    padding-left: 10px;
  }

   .navbar-mobile-center {
    float: right;
    padding-right:8px;
   
  }
.navbar.horizontal-menu .navbar-inner .navbar-nav a.logo{
    display: none;
}

.navbar.horizontal-menu .navbar-inner .navbar-brand {


    display: none;
}
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


<div class="navbar-mobile-right">
   <!-- This will toggle the mobile menu and will be visible only on mobile devices -->
   <h4 >
   <a style="color:#ffffff" href="{{url('/dashboard')}}">
   {{ config('app.name') }}
   </a></h2>
</div>
<div class="navbar-mobile-center">

       @php
        $userName = Auth::user()->name;
   
        if(Auth::user()->emp_id_fk){
            $employee = DB::table('gnr_employee')->select('image')->where('id',Auth::user()->emp_id_fk)->first(); 
        }else{
            $employee = null;
        }
       
        //dd($employee);

    @endphp
   <!-- This will toggle the mobile menu and will be visible only on mobile devices -->
   <div class="dropdown">

    <a href="#" data-toggle="dropdown">
            @if($employee)
                @if($employee->image)
                    <img src="{{ asset("images/employee/$employee->image") }}" alt="user-image"  style="background-color:#ffffff" class="img-circle img-inline userpic-32" width="32" height="36"/>
                @else
                    <img src="https://www.plaxis.com/content/uploads/2016/08/dummy-user-400x400.png"  style="background-color:#ffffff" alt="user-image" class="img-circle img-inline userpic-32" width="32" height="36"/>
                @endif
            @else
                <img src="https://www.plaxis.com/content/uploads/2016/08/dummy-user-400x400.png"  style="background-color:#ffffff" alt="user-image" class="img-circle img-inline userpic-32" width="32" height="36"/>
            @endif
            
            {{-- <i class="fa fa-user-circle-o" aria-hidden="true"></i> --}}
            {{-- <span>
                {{ $userName }}
            </span> --}}
            <span style="color:#ffffff">
                {{ ucfirst($userName)}}
                <?php //echo $branchName = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('name'); ?>
                <i class="fa-angle-down"></i>
            </span>
        </a>
      <!-- <a href="#" data-toggle="dropdown">
      <img src="https://www.plaxis.com/content/uploads/2016/08/dummy-user-400x400.png" style="background-color:#ffffff"alt="user-image" class="img-circle img-inline userpic-32" width="32" height="36">
      <span style="color:#ffffff">
      Maria
      <i class="fa-angle-down"></i>
      </span>
      </a> -->
      <ul class="dropdown-menu profile" aria-labelledby="dropdownMenu1" style="margin-left: -110px">
         <li>
            <a href="{{ url('profile'.Auth::user()->emp_id_fk) }}">
            <i class="fa-user"></i>
            Profile
            </a>
         </li>
         <li>
            <a href="{{ url('/user/password/reset') }}">
            <i class="fa-cog"></i>
            Change Password
            </a>
         </li>
         <li>
            <a href="{{ url('password/reset') }}">
            <i class="fa-cog"></i>
            Forgot Password
            </a>
         </li>
          <li class="last">
                <a href="{{ url('/logout') }}"
                   onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                    <i class="fa-lock"></i>
                    Logout
                </a>

                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
      </ul>
   </div>
</div>



<!-- <div class="nav navbar-mobile">
  
    <div class="mobile-menu-toggle" style="margin-top:8px;">
       

    
        <a href="#" data-toggle="mobile-menu-horizontal">
            <i class="fa-bars"></i>
        </a>
    </div>
</div> -->
<div class="navbar-mobile-clear"></div>

<div class="row moduleList" style="margin: 0 0 1em 0;padding-left:10px;background-color:#000000">

    <div class="nav navbar-mobile">
  
    <div class="mobile-menu-toggle" style="margin-top:3px;font-size: 20px;">
       

    
        <a href="#" data-toggle="mobile-menu-horizontal" style="color:#ffffff">
            <i class="fa-bars"></i>
        </a>
    </div>
</div>

<!--      <div class="dropdown">

    <a href="#" data-toggle="dropdown" style="color:#ffffff;font-size:20px">
           <i class="fa-bars"></i>
        </a>
    
      <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
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
     
      </ul>
   </div> -->
    </div>

