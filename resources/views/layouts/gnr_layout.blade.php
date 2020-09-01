<?php 
session(['currentModule' => 'general']);
?>
@include('partials._head')
<body class="page-body">
    @include('partials._pane')
    <nav class="navbar horizontal-menu navbar-fixed-top navbar-minimal">
        @include('partials._minibar')
        <div class="navbar-inner">
         @include('partials._branding')
         @include('partials._navGnr')
         @include('partials._settings')
     </div>
 </nav>
 <div class="page-container">
    <div class="main-content">
        @include('partials._message')
        <script type="text/javascript">
            var layoutModuleId = 7;
        </script>
        @yield('content')
        @include('partials._footer')
        @yield('script')
    </div>
</div>
</body>
</html>