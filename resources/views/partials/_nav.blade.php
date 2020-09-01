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


<ul id="main-menu" class="navbar-nav">
   {{--  <li>
        <a class="animated fadeInLeft" href="{{ url('gnr/home') }}">
            <i class="fa fa-cogs" aria-hidden="true"></i>
            <span class="title">General Settings</span>
        </a>
    </li> --}}
    @foreach ($modules as $module)
         @if ($module->slug == 'bil')
            <li>
                <a class="animated fadeInLeft" href="{{ url('pos/home') }}">
                    <i class="fa fa-usd" aria-hidden="true"></i>
                    <span class="title">Billing</span>
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
        @elseif ($module->slug == 'acc')
            <li>
                <a class="animated fadeInLeft" href="{{ url('acc/home') }}">
                    <i class="fa fa-tachometer" aria-hidden="true"></i>
                    <span class="title">Accounting</span>
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
    {{-- <li>
        <a class="animated fadeInLeft" href="{{ url('inv/home') }}@endif">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
            <span class="title">Inventory</span>
        </a>
    </li>
    <li>
        <a class="animated fadeInLeft" href="{{ url('fams/home') }}@endif">
            <i class="fa fa-tachometer" aria-hidden="true"></i>
            <span class="title">FAMS</span>
        </a> 
    </li>
    <li>
        <a class="animated fadeInLeft" href="{{ url('acc/home') }}@endif">
            <i class="fa fa-usd" aria-hidden="true"></i>
            <span class="title">Accounting</span>
        </a>
    </li>
    <li>
        <a class="animated fadeInLeft" href="{{ url('pos/home') }}@endif">
            <i class="fa fa-usd" aria-hidden="true"></i>
            <span class="title">Billing</span>
        </a>
    </li> --}}
</ul>
