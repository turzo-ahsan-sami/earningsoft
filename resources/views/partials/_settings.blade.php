<ul class="nav nav-userinfo" style="float: right; margin-right: 50px;">
    <!-- Add "always-visible" to show make the search input visible -->
    {{--     <li class="search-form animated fadeInRight">
            <form method="get" action="extra-search.html">
                <input type="text" name="s" class="form-control search-field" placeholder="Type to search..." />
                <button type="submit" class="btn btn-link">
                    <i class="linecons-search"></i>
                </button>
            </form>
        </li> --}}
    @php
        $userName = Auth::user()->name;
        //if ($authUser->id == \App\ConstValue::USER_ID_SUPER_ADMIN || $authUser->getRole()->roleId == \App\ConstValue::ROLE_ID_GUEST) {
        //    $userName = Auth::user()->name;
        //    $photo = 'images/user-1.png';
        //} else {
        //    $emp = DB::table('hr_emp_general_info')->where('id', $authUser->emp_id_fk)->first();
        //    $userName = $emp->emp_name_english;
        //    $photo = '/../storage/app/public/employee-information/'.$emp->photo;
        //}
        if(Auth::user()->emp_id_fk){
            $employee = DB::table('gnr_employee')->select('image')->where('id',Auth::user()->emp_id_fk)->first(); 
        }else{
            $employee = null;
        }
       
        //dd($employee);

    @endphp
    <li class="dropdown user-profile animated fadeInRight">
        <a href="#" data-toggle="dropdown">
            @if($employee)
                @if($employee->image)
                    <img src="{{ asset("images/employee/$employee->image") }}" alt="user-image" class="img-circle img-inline userpic-32" width="32" height="36"/>
                @else
                    <img src="https://www.plaxis.com/content/uploads/2016/08/dummy-user-400x400.png" alt="user-image" class="img-circle img-inline userpic-32" width="32" height="36"/>
                @endif
            @else
                <img src="https://www.plaxis.com/content/uploads/2016/08/dummy-user-400x400.png" alt="user-image" class="img-circle img-inline userpic-32" width="32" height="36"/>
            @endif
            
            {{-- <i class="fa fa-user-circle-o" aria-hidden="true"></i> --}}
            {{-- <span>
                {{ $userName }}
            </span> --}}
            <span>
                {{ ucfirst($userName)}}
                <?php //echo $branchName = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('name'); ?>
                <i class="fa-angle-down"></i>
            </span>
        </a>
        <ul class="dropdown-menu user-profile-menu list-unstyled">
            {{-- <li>
                <a href="#settings">
                    <i class="fa-wrench"></i>
                    Settings
                </a>
            </li> --}}
             <li>
                <a href="{{ url('profile'.Auth::user()->emp_id_fk) }}">
                    <i class="fa-user"></i>
                    Profile
                </a>
            </li>
             <li>
                <a href="{{ url('subscriptionDetails') }}">
                    <i class="fa fa-bell"></i>
                    Subscription
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
           
        {{-- <li>
            <a href="#profile">
                <i class="fa-user"></i>
                Profile
            </a>
        </li> --}}
        <!-- <li class="last">
                <a href="">
                    <i class="fa-lock"></i>
                    Logout
                </a>
            </li> -->
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
    </li>
</ul>
