
<?php 
session(['currentModule' => 'fams']);
?>

@include('partials._head')
<body class="page-body">
    @include('partials._pane')
    <!-- set fixed position by adding class "navbar-fixed-top" -->
<nav class="navbar horizontal-menu navbar-fixed-top navbar-minimal">
    @include('partials._minibar')

    <div class="navbar-inner">
        @include('partials._branding')
        @include('partials._navFAMS')
        @include('partials._settings')

    </div>
</nav>
    <div class="page-container">
        <div class="main-content">
            @include('partials._message')
            <script type="text/javascript">
                var layoutModuleId = 2;
            </script>
            @yield('content')
            @include('partials._footer')
        </div>
    </div>
</body>
</html>