<footer class="landing-footer bg-body footer-text">
  <div class="footer-top position-relative overflow-hidden z-1">
    <img src="{{ asset('assets/img/front-pages/backgrounds/footer-bg-' . $configData['style'] . '.webp') }}"
         alt="footer bg"
         class="footer-bg banner-bg-img z-n1 lazyload"
         loading="lazy"
         data-app-light-img="front-pages/backgrounds/footer-bg-light.webp"
         data-app-dark-img="front-pages/backgrounds/footer-bg-dark.webp"
         width="100%" height="auto"/>

    <div class="container">
      <div class="row gx-0 gy-6 g-lg-10">
        <div class="col-lg-5">
          <a href="javascript:;" class="app-brand-link mb-6 d-flex align-items-center">
            <span class="app-brand-logo">
              <img src="{{ asset('storage/' . config('settings.site_logo')) }}"
                   alt="LogoWebsite"
                   style="max-width: 50px; height: auto;"
                   loading="lazy" />
            </span>
            <span class="app-brand-text footer-link fw-bold ms-2 ps-1">
              {{ config('settings.site_name') }}
            </span>
          </a>
          <p class="footer-text footer-logo-description mb-6">
            {{ config('settings.site_description') }}
          </p>
        </div>

        <div class="col-lg-3 col-md-4">
          <h6 class="footer-title mb-6">Download our app</h6>
          <a href="javascript:void(0);" class="d-block mb-4">
            <img src="{{ asset('assets/img/front-pages/landing-page/apple-icon.webp') }}"
                 alt="apple icon"
                 loading="lazy"
                 width="150" height="auto" />
          </a>
          <a href="javascript:void(0);" class="d-block">
            <img src="{{ asset('assets/img/front-pages/landing-page/google-play-icon.webp') }}"
                 alt="google play icon"
                 loading="lazy"
                 width="150" height="auto" />
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="footer-bottom py-3 py-md-5">
    <div class="container d-flex flex-wrap justify-content-between flex-md-row flex-column text-center text-md-start">
      <div class="mb-2 mb-md-0">
        <span class="footer-bottom-text">©
          <script>document.write(new Date().getFullYear());</script>
        </span>

        <a href="{{ config('settings.site_name') }}" target="_blank" class="fw-medium text-white">
          {{ config('settings.site_name') }}
        </a>,
        <span class="footer-bottom-text">{{ __('All rights reserved') }}.</span>
      </div>

      <div>
        <a href="{{ config('settings.whatsapp') }}" target="_blank">
          <img src="{{ asset('assets/img/front-pages/icons/whatsapp.svg') }}"
               alt="whatsapp icon"
               loading="lazy" width="30" height="30" />
        </a>
        <a href="{{ config('settings.tiktok') }}" class="me-3" target="_blank">
          <img src="{{ asset('assets/img/front-pages/icons/tiktok.svg') }}"
               alt="tiktok icon"
               loading="lazy" width="30" height="30" />
        </a>
        <a href="{{ config('settings.facebook') }}" class="me-3" target="_blank">
          <img src="{{ asset('assets/img/front-pages/icons/facebook.svg') }}"
               alt="facebook icon"
               loading="lazy" width="30" height="30" />
        </a>
        <a href="{{ config('settings.twitter') }}" class="me-3" target="_blank">
          <img src="{{ asset('assets/img/front-pages/icons/twitter.svg') }}"
               alt="twitter icon"
               loading="lazy" width="30" height="30" />
        </a>
        <a href="{{ config('settings.linkedin') }}" target="_blank">
          <img src="{{ asset('assets/img/front-pages/icons/linkedin.svg') }}"
               alt="linkedin icon"
               loading="lazy" width="30" height="30" />
        </a>
      </div>
    </div>
  </div>
</footer>
