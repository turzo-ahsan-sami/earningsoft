<?php
session(['currentModule' => 'accounting']);
?>
@include('partials._head')
<body class="page-body">
    {{-- @include('partials._loading') --}}
@include('partials._pane')

<nav class="navbar horizontal-menu navbar-fixed-top navbar-minimal">
    @include('partials._minibar')
        <div class="navbar-inner">
            @include('partials._branding')
            @include('partials._navAccounting')
            @include('partials._settings')
        </div>
    </div>
</nav>
<div class="page-container">
    <div class="main-content">
        @include('partials._message')
        @include('partials._loading')
        <script type="text/javascript">
            var layoutModuleId = 4;
            $("#loadingModal").show();
            $(document).keydown(function(e) {
                if (e.keyCode == 27) return false;
            });
        </script>
        @yield('content')
        @include('partials._footer')
    </div>
</div>

{{-- <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script> --}}
{{-- <script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/TweenMax.min.js')}}"></script>
<script src="{{asset('js/resizeable.js')}}"></script>
<script src="{{asset('js/joinable.js')}}"></script>
<script src="{{asset('js/xenon-api.js')}}"></script>
<script src="{{asset('js/xenon-toggles.js')}}"></script>

<script src="{{asset('js/xenon-widgets.js')}}"></script>
<script src="{{asset('js/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{asset('js/jvectormap/regions/jquery-jvectormap-world-mill-en.js')}}"></script>
<script src="{{ asset('js/dropzone/dropzone.min.js') }}"></script>  --}}


<!-- JavaScripts initializations and stuff -->
{{-- <script src="{{ asset('js/xenon-custom.js') }}"></script> --}}

@yield('script')
</body>
</html>

<script type="text/javascript">
    $(document).ready(function() {
        $('#loadingModal').hide();
    });
</script>
