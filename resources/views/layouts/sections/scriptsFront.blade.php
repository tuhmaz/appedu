@vite([
  'resources/assets/vendor/js/bootstrap.js','resources/assets/vendor/js/hiding.js'
])

@yield('vendor-script')

 

@stack('pricing-script')

@yield('page-script')
