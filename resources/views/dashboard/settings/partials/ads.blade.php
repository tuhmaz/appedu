<form action="{{ route('settings.update') }}" method="POST">
  @csrf
  @method('PUT')
  <div class="mb-3">
    <label for="google_ads_desktop_classes" class="form-label">{{ __('Google Ads Desktop Code Classes') }}</label>
    <textarea class="form-control" id="google_ads_desktop_classes" name="google_ads_desktop_classes" rows="5">{{ $settings['google_ads_desktop_classes'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_desktop_classes_2" class="form-label">{{ __('Google Ads Desktop Code classes 2') }}</label>
    <textarea class="form-control" id="google_ads_desktop_classes_2" name="google_ads_desktop_classes_2" rows="5">{{ $settings['google_ads_desktop_classes_2'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_desktop_subject" class="form-label">{{ __('Google Ads Desktop Code subject') }}</label>
    <textarea class="form-control" id="google_ads_desktop_subject" name="google_ads_desktop_subject" rows="5">{{ $settings['google_ads_desktop_subject'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_desktop_subject_2" class="form-label">{{ __('Google Ads Desktop Code subject 2') }}</label>
    <textarea class="form-control" id="google_ads_desktop_subject_2" name="google_ads_desktop_subject_2" rows="5">{{ $settings['google_ads_desktop_subject_2'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_desktop_article" class="form-label">{{ __('Google Ads Desktop Code article') }}</label>
    <textarea class="form-control" id="google_ads_desktop_article" name="google_ads_desktop_article" rows="5">{{ $settings['google_ads_desktop_article'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_desktop_article_2" class="form-label">{{ __('Google Ads Desktop Code article 2') }}</label>
    <textarea class="form-control" id="google_ads_desktop_article_2" name="google_ads_desktop_article_2" rows="5">{{ $settings['google_ads_desktop_article_2'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_desktop_news" class="form-label">{{ __('Google Ads Desktop Code news') }}</label>
    <textarea class="form-control" id="google_ads_desktop_news" name="google_ads_desktop_news" rows="5">{{ $settings['google_ads_desktop_news'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_desktop_news_2" class="form-label">{{ __('Google Ads Desktop Code news 2') }}</label>
    <textarea class="form-control" id="google_ads_desktop_news_2" name="google_ads_desktop_news_2" rows="5">{{ $settings['google_ads_desktop_news_2'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_mobile_1" class="form-label">{{ __('Google Ads Mobile Code classes') }}</label>
    <textarea class="form-control" id="google_ads_mobile_1" name="google_ads_mobile_classes" rows="5">{{ $settings['google_ads_mobile_classes'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_mobile_2" class="form-label">{{ __('Google Ads Mobile Code classes 2') }}</label>
    <textarea class="form-control" id="google_ads_mobile_classes_2" name="google_ads_mobile_classes_2" rows="5">{{ $settings['google_ads_mobile_classes_2'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_mobile_subject" class="form-label">{{ __('Google Ads Mobile Code subject') }}</label>
    <textarea class="form-control" id="google_ads_mobile_subject" name="google_ads_mobile_subject" rows="5">{{ $settings['google_ads_mobile_subject'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_mobile_subject_2" class="form-label">{{ __('Google Ads Mobile Code subject 2') }}</label>
    <textarea class="form-control" id="google_ads_mobile_subject_2" name="google_ads_mobile_subject_2" rows="5">{{ $settings['google_ads_mobile_subject_2'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_mobile_news" class="form-label">{{ __('Google Ads Mobile Code news') }}</label>
    <textarea class="form-control" id="google_ads_mobile_news" name="google_ads_mobile_news" rows="5">{{ $settings['google_ads_mobile_news'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="google_ads_mobile_news_2" class="form-label">{{ __('Google Ads Mobile Code news 2') }}</label>
    <textarea class="form-control" id="google_ads_mobile_news_2" name="google_ads_mobile_news_2" rows="5">{{ $settings['google_ads_mobile_news_2'] ?? '' }}</textarea>
  </div>
  <button type="submit" class="btn btn-primary">{{ __('Save Settings') }}</button>
</form>
