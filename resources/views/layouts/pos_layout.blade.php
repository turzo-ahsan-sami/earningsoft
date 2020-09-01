<?php 

    $user = Auth::user();
    Session::put('branchId', $user->branchId);
    $gnrBranchId = Session::get('branchId');
    $gnrBranchId;
    $branchName = DB::table('gnr_branch')->where('id',$gnrBranchId)->value('name');
    session(['currentModule' => 'pos']);
?>

@include('partials._head')
<body class="page-body">
    @include('partials._pane')
    <nav class="navbar horizontal-menu navbar-fixed-top navbar-minimal">
        @include('partials._minibar')
        <div class="navbar-inner">
            @include('partials._branding')

            @include('partials._navPos')

            @include('partials._settings')
        </div>
    </nav>
    <div class="page-container">
        <div class="main-content">
            @include('partials._message')
            
            @yield('content')

            @include('partials._footer')
        </div>
    </div>

</body>

</html>