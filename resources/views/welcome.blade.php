@include('partials._head')
<body class="page-body">
    @include('partials._pane')
    @include('partials._header')
    <div class="page-container">
        <div class="main-content">
            @include('partials._message')
            @yield('content')
            @include('partials._footer')
        </div>
    </div>
    
</body>
</html>