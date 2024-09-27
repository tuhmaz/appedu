@vite([
  'resources/assets/vendor/js/dropdown-hover.js',
  'resources/assets/vendor/js/mega-dropdown.js',
  'resources/assets/vendor/libs/popper/popper.js',
  'resources/assets/vendor/js/bootstrap.js',
  'resources/assets/vendor/libs/node-waves/node-waves.js'
])

@yield('vendor-script')

@vite(['resources/assets/js/front-main.js', 'resources/js/hiding.js',])

@stack('pricing-script')

@yield('page-script')
