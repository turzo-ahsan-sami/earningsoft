@php
$usersManageRoutes = config('route.user');
$productsManageRoutes = config('route.product');
$frontendManageRoutes = config('route.frontend');

$usersMenuOpen = request()->is($usersManageRoutes) ? 'open' : '';
$productsMenuOpen = request()->is($productsManageRoutes) ? 'open' : '';
$frontendMenuOpen = request()->is($frontendManageRoutes) ? 'open' : '';
@endphp
<div class="sidebar">
    <nav class="sidebar-nav">

        <ul class="nav">
            <li class="nav-item">
                <a href="{{ route("admin.home") }}" class="nav-link">
                    <i class="nav-icon fas fa-fw fa-tachometer-alt">

                    </i>
                    {{ trans('global.dashboard') }}
                </a>
            </li>
            @can('users_manage')
                <li class="nav-item nav-dropdown {{ $usersMenuOpen }}">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        {{ trans('cruds.userManagement.title') }}
                    </a>
                    <ul class="nav-dropdown-items">

                        <li class="nav-item">
                            <a href="{{ route("admin.permissions.index") }}" class="nav-link {{ request()->is('admin/permissions*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-unlock-alt nav-icon">

                                </i>
                                {{ trans('cruds.permission.title') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is('admin/roles*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-briefcase nav-icon">

                                </i>
                                {{ trans('cruds.role.title') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-user nav-icon">

                                </i>
                                {{ trans('cruds.user.title') }}
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('products_manage')
                <li class="nav-item nav-dropdown {{ $productsMenuOpen }}">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fas-fw fas fa-th nav-icon">

                        </i>
                        {{ trans('cruds.productManagement.title') }}
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('modules_manage')
                            <li class="nav-item">
                                <a href="{{ route("admin.modules.index") }}" class="nav-link {{ request()->is('admin/modules*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-cubes nav-icon">

                                    </i>
                                    {{ trans('cruds.module.title') }}
                                </a>
                            </li>
                        @endcan

                        @can('planTypes_manage')
                            <li class="nav-item">
                                <a href="{{ route("admin.planTypes.index") }}" class="nav-link {{ request()->is('admin/planTypes*') ? 'active' : '' }}">
                                    <i class="fas-fw fas fa-money-check-alt nav-icon"></i>
                                    {{ trans('cruds.planType.title') }}
                                </a>
                            </li>
                        @endcan

                        @can('trainings_manage')
                            <li class="nav-item">
                                <a href="{{ route("admin.trainings.index") }}" class="nav-link {{ request()->is('admin/trainings*') ? 'active' : '' }}">
                                    <i class="fas-fw fas fa-laptop-code nav-icon">

                                    </i>
                                    {{ trans('cruds.training.title') }}
                                </a>
                            </li>
                        @endcan

                        @can('discounts_manage')
                            <li class="nav-item">
                                <a href="{{ route("admin.discounts.index") }}" class="nav-link {{ request()->is('admin/discounts*') ? 'active' : '' }}">
                                    <i class="fas-fw fas fa-tags nav-icon">

                                    </i>
                                    {{ trans('cruds.discount.title') }}
                                </a>
                            </li>
                        @endcan

                        {{-- <li class="nav-item">
                            <a href="{{ route("admin.products.index") }}" class="nav-link {{ request()->is('admin/products*') ? 'active' : '' }}">
                                <i class="fas-fw fas fa-shopping-bag nav-icon">

                                </i>
                                {{ trans('cruds.product.title') }}
                            </a>
                        </li> --}}

                        <li class="nav-item">
                            <a href="{{ route("admin.plans.index") }}" class="nav-link {{ request()->is('admin/plans*') ? 'active' : '' }}">
                                <i class="fas-fw fas fa-shopping-bag nav-icon">

                                </i>
                                {{ trans('cruds.plan.title') }}
                            </a>
                        </li>

                    </ul>
                </li>
            @endcan

            @can('frontend_manage')
                <li class="nav-item nav-dropdown {{ $frontendMenuOpen }}">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fas-fw fas fa-file-code nav-icon"></i>
                        {{ trans('cruds.frontendManagement.title') }}
                    </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a href="{{ route("admin.companies.index") }}" class="nav-link {{ request()->is('admin/companies*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-building nav-icon"></i>
                                {{ trans('cruds.company.title') }}
                            </a>
                        </li>
                    </ul>

                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a href="{{ route("admin.banners.index") }}" class="nav-link {{ request()->is('admin/banners*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-header nav-icon">

                                </i>
                                {{ trans('cruds.banner.title') }}
                            </a>
                        </li>
                    </ul>


                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a href="{{ route("admin.featureSection.index") }}" class="nav-link {{ request()->is('admin/featureSection*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-list nav-icon">

                                </i>
                                {{ trans('cruds.featureSection.title') }}
                            </a>
                        </li>
                    </ul>

                     <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a href="{{ route("admin.userReview.index") }}" class="nav-link {{ request()->is('admin/userReview*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-list nav-icon">

                                </i>
                                {{ trans('cruds.userReview.title') }}
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <li class="nav-item">
                <a href="{{ route('auth.change_password') }}" class="nav-link">
                    <i class="nav-icon fas fa-fw fa-key">

                    </i>
                    Change password
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="nav-icon fas fa-fw fa-sign-out-alt">

                    </i>
                    {{ trans('global.logout') }}
                </a>
            </li>
        </ul>

    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
