<!-- set fixed position by adding class "navbar-fixed-top" -->
<nav class="navbar horizontal-menu navbar-fixed-top navbar-minimal">

    {{--@include('partials._eventGreetings')--}}
    @include('partials._minibar')
    <div class="navbar-inner">
{{--            <div class="row">
                <div class="col-md-3 col-sm-2 col-xl-2">
                    @include('partials._branding')          
                </div>  
                <div class="col-md-7 col-sm-8 col-xl-8">
                     @include('partials._nav')       
                </div> 
                <div class="col-md-2 col-sm-2 col-xl-2">
                     @include('partials._settings')      
                </div>       
            </div> --}}
            @include('partials._branding')          
            @include('partials._nav')       
            @include('partials._settings')

        </div>
    </div>
</nav>

