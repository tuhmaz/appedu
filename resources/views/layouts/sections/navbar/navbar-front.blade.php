@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
$currentRouteName = Route::currentRouteName();
$activeRoutes = ['front-pages-pricing', 'front-pages-payment', 'front-pages-checkout', 'front-pages-help-center'];
$activeClass = in_array($currentRouteName, $activeRoutes) ? 'active' : '';
@endphp
<nav class="layout-navbar shadow-none py-0">
  <div class="container">
    <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-8">
      <div class="navbar-brand app-brand demo d-flex py-0 py-lg-2 me-4 me-xl-8">
        <button class="navbar-toggler border-0 px-0 me-4" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="ti ti-menu-2 ti-lg align-middle text-heading fw-medium"></i>
        </button>
        <a href="javascript:;" class="app-brand-link">
          <span class="app-brand-logo demo">@include('_partials.macros',['height'=>20,'withbg' => "fill: #fff;"])</span>
          <span class="app-brand-text demo menu-text fw-bold ms-2 ps-1">{{config('settings.site_name')}}</span>
        </a>
      </div>

      <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
        <button class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 scaleX-n1-rtl" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="ti ti-x ti-lg"></i>
        </button>
      </div>
      <form method="POST" action="{{ route('setDatabase') }}" id="databaseForm">
    @csrf
    <input type="hidden" name="database" id="databaseInput">

    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            اختر الدولة
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <li>
                <a class="dropdown-item" href="#" onclick="setDatabase('mysql')">
                    <img alt="Jordan" src="{{ asset('assets/img/flags/jordan.png') }}" style="width: 20px; height: 20px;">&nbsp;
                    الأردن
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#" onclick="setDatabase('subdomain1')">
                    <img alt="Saudi Arabia" src="{{ asset('assets/img/flags/saudi.png') }}" style="width: 20px; height: 20px;">&nbsp;
                    السعودية
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#" onclick="setDatabase('subdomain2')">
                    <img alt="Egypt" src="{{ asset('assets/img/flags/egypt.png') }}" style="width: 20px; height: 20px;">&nbsp;
                    مصر
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#" onclick="setDatabase('subdomain3')">
                    <img alt="Palestine" src="{{ asset('assets/img/flags/palestine.png') }}" style="width: 20px; height: 20px;">&nbsp;
                    فلسطين
                </a>
            </li>
        </ul>
    </div>
</form>

<script>
    function setDatabase(database) {
        document.getElementById('databaseInput').value = database;
        document.getElementById('databaseForm').submit();
    }
</script>





      <div class="landing-menu-overlay d-lg-none"></div>

      <ul class="navbar-nav flex-row align-items-center ms-auto">
        @if($configData['hasCustomizer'] == true)

        <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-1">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <i class='ti ti-lg'></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
            <li>
              <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                <span class="align-middle"><i class='ti ti-sun me-3'></i>Light</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                <span class="align-middle"><i class="ti ti-moon-stars me-3"></i>Dark</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                <span class="align-middle"><i class="ti ti-device-desktop-analytics me-3"></i>System</span>
              </a>
            </li>
          </ul>
        </li>

        @endif

        <li>
        @if(Auth::check())

<li class="nav-item navbar-dropdown dropdown-user dropdown">
    <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
        @php
            $randomAvatar = 'assets/img/avatars/' . rand(1, 8) . '.png';
        @endphp

        <div class="avatar avatar-online">
            <img src="{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : asset($randomAvatar) }}" alt="Avatar" class="rounded-circle">
        </div>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item mt-0" href="{{ route('users.show', Auth::user()->id) }}">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-2">
                        <div class="avatar avatar-online">
                            <img src="{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : asset($randomAvatar) }}" alt="Avatar" class="rounded-circle">
                        </div>
                    </div>

                    <div class="flex-grow-1">
                        <h6 class="mb-0">
                            {{ Auth::user()->name }}
                        </h6>
                        <small class="text-muted">Admin</small>
                    </div>
                </div>
            </a>
        </li>
        <li>
            <div class="dropdown-divider my-1 mx-n2"></div>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('users.show', Auth::user()->id) }}">
                <i class="ti ti-user me-3 ti-md"></i><span class="align-middle">My Profile</span>
            </a>
        </li>


        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
            <li>
                <a class="dropdown-item" href="{{ route('api-tokens.index') }}">
                    <i class="ti ti-key ti-md me-3"></i><span class="align-middle">API Tokens</span>
                </a>
            </li>
        @endif

        <li>
            <div class="dropdown-divider my-1 mx-n2"></div>
        </li>


        <li>
            <div class="d-grid px-2 pt-2 pb-1">
                <a class="btn btn-sm btn-danger d-flex" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <small class="align-middle">Logout</small>
                    <i class="ti ti-logout ms-2 ti-14px"></i>
                </a>
            </div>
            <form method="POST" id="logout-form" action="{{ route('logout') }}">
                @csrf
            </form>
        </li>
    </ul>
</li>

@else

<li>
    <a href="{{ route('login') }}" class="btn btn-primary">
        <span class="tf-icons ti ti-login scaleX-n1-rtl me-md-1"></span>
        <span class="d-none d-md-block">Login/Register</span>
    </a>
</li>
@endif


</li>


      </ul>

    </div>
  </div>
</nav>
