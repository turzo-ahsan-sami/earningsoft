<?php

$user = Auth::user();
Session::put('userId', $user->id);
Session::put('branchId', $user->branchId);
$userId = Session::get('id');
$userId;
$branchCode = DB::table('gnr_branch')->select('branchCode')->where('id', $user->branchId)->first();
Session::put('branchCode', $branchCode->branchCode);
$gnrBranchId = Session::get('branchId');
$gnrBranchId;
$branchName = DB::table('gnr_branch')->where('id',$gnrBranchId)->value('name');

session(['currentModule' => 'microfinance']);
?>

@include('partials._head')

<body class="page-body">
    @include('partials._pane')
    <nav class="navbar horizontal-menu navbar-fixed-top navbar-minimal">
        @include('partials._minibar')
        {{--  @include('partials._noticeminibar') --}}
        <div class="navbar-inner">
         @include('partials._branding')
         @include('partials._navMicrofin')
         @include('partials._settings')
     </div>
 </nav>
 <div class="page-container">
    <div class="main-content">
        @include('partials._message')
        @include('partials._loading')
        <script type="text/javascript">
            var layoutModuleId = 6;
                // $("#loadingModal").show();
                // $(document).keydown(function(e) {
                //     if (e.keyCode == 27) return false;
                // });
            </script>
            @yield('content')
            @include('partials._footer')
        </div>
    </div>
</body>
</html>
