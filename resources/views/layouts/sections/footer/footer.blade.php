@php
$containerFooter = (isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme background bg-black">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
      <div class="text-body">
        &copy; <script>document.write(new Date().getFullYear())</script>, made with <i class="tf-icons bx bx-heart text-danger"></i> by <a href="{{config('settings.site_name')}}" target="_blank" class="footer-link fw-semibold">{{config('settings.site_name')}}</a>
      </div>
      <div class="social-links d-flex gap-3 align-items-center">
        @if(config('settings.whatsapp'))
        <a href="{{ config('settings.whatsapp') }}" class="social-link" target="_blank" title="تواصل معنا عبر واتساب">
          <img src="{{asset('assets/img/front-pages/icons/whatsapp.svg')}}" alt="واتساب" class="social-icon" />
        </a>
        @endif

        @if(config('settings.tiktok'))
        <a href="{{ config('settings.tiktok') }}" class="social-link" target="_blank" title="تابعنا على تيك توك">
          <img src="{{asset('assets/img/front-pages/icons/tiktok.svg')}}" alt="تيك توك" class="social-icon" />
        </a>
        @endif

        @if(config('settings.facebook'))
        <a href="{{ config('settings.facebook') }}" class="social-link" target="_blank" title="تابعنا على فيسبوك">
          <img src="{{asset('assets/img/front-pages/icons/facebook.svg')}}" alt="فيسبوك" class="social-icon" />
        </a>
        @endif

        @if(config('settings.twitter'))
        <a href="{{ config('settings.twitter') }}" class="social-link" target="_blank" title="تابعنا على تويتر">
          <img src="{{asset('assets/img/front-pages/icons/twitter.svg')}}" alt="تويتر" class="social-icon" />
        </a>
        @endif

        @if(config('settings.linkedin'))
        <a href="{{ config('settings.linkedin') }}" class="social-link" target="_blank" title="تابعنا على لينكد إن">
          <img src="{{asset('assets/img/front-pages/icons/linkedin.svg')}}" alt="لينكد إن" class="social-icon" />
        </a>
        @endif
      </div>
    </div>
  </div>
</footer>
<!--/ Footer-->

<style>
.social-links {
  display: flex;
  gap: 1.5rem;
}

.social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  opacity: 0.85;
}

.social-link:hover {
  transform: translateY(-3px);
  opacity: 1;
}

.social-icon {
  width: 24px;
  height: 24px;
  filter: brightness(0) invert(1);
}

@media (max-width: 768px) {
  .social-links {
    margin-top: 1rem;
  }
  
  .social-icon {
    width: 20px;
    height: 20px;
  }
}
</style>
