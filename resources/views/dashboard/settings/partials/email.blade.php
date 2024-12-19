<div>
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="mail_mailer" class="form-label">{{ __('Mail Mailer') }}</label>
            <select name="mail_mailer" class="form-control" id="mail_mailer">
                <option value="smtp" {{ old('mail_mailer', $settings['mail_mailer']) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                <option value="sendmail" {{ old('mail_mailer', $settings['mail_mailer']) == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                <option value="mailgun" {{ old('mail_mailer', $settings['mail_mailer']) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="mail_host" class="form-label">{{ __('Mail Host') }}</label>
            <input type="text" name="mail_host" class="form-control" id="mail_host" 
                value="{{ old('mail_host', $settings['mail_host']) }}"
                placeholder="mail.alemedu.com">
        </div>

        <div class="mb-3">
            <label for="mail_port" class="form-label">{{ __('Mail Port') }}</label>
            <select name="mail_port" class="form-control" id="mail_port">
                <option value="465" {{ old('mail_port', $settings['mail_port']) == '465' ? 'selected' : '' }}>465 (SSL)</option>
                <option value="587" {{ old('mail_port', $settings['mail_port']) == '587' ? 'selected' : '' }}>587 (TLS)</option>
                <option value="25" {{ old('mail_port', $settings['mail_port']) == '25' ? 'selected' : '' }}>25 (SMTP)</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="mail_username" class="form-label">{{ __('Mail Username') }}</label>
            <input type="email" name="mail_username" class="form-control" id="mail_username" 
                value="{{ old('mail_username', $settings['mail_username']) }}"
                placeholder="info@alemedu.com">
        </div>

        <div class="mb-3">
            <label for="mail_password" class="form-label">{{ __('Mail Password') }}</label>
            <div class="input-group">
                <input type="password" name="mail_password" class="form-control" id="mail_password" 
                    value="{{ old('mail_password', $settings['mail_password']) }}">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('mail_password')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="mb-3">
            <label for="mail_encryption" class="form-label">{{ __('Mail Encryption') }}</label>
            <select name="mail_encryption" class="form-control" id="mail_encryption">
                <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption']) == 'ssl' ? 'selected' : '' }}>SSL</option>
                <option value="tls" {{ old('mail_encryption', $settings['mail_encryption']) == 'tls' ? 'selected' : '' }}>TLS</option>
                <option value="" {{ old('mail_encryption', $settings['mail_encryption']) == '' ? 'selected' : '' }}>None</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="mail_from_address" class="form-label">{{ __('Mail From Address') }}</label>
            <input type="email" name="mail_from_address" class="form-control" id="mail_from_address" 
                value="{{ old('mail_from_address', $settings['mail_from_address']) }}"
                placeholder="noreply@alemedu.com">
        </div>

        <div class="mb-3">
            <label for="mail_from_name" class="form-label">{{ __('Mail From Name') }}</label>
            <input type="text" name="mail_from_name" class="form-control" id="mail_from_name" 
                value="{{ old('mail_from_name', $settings['mail_from_name']) }}"
                placeholder="Alemedu">
        </div>

        <div class="mb-3">
            <button type="button" class="btn btn-info me-2" onclick="testEmailSettings()">
                <i class="fas fa-paper-plane"></i> {{ __('Test Email Settings') }}
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ __('Save Settings') }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function testEmailSettings() {
    // Show loading spinner
    Swal.fire({
        title: '{{ __("Testing Email Settings") }}',
        text: '{{ __("Please wait...") }}',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Send test email
    axios.post('{{ route("settings.test.email") }}')
        .then(response => {
            Swal.fire({
                icon: 'success',
                title: '{{ __("Success!") }}',
                text: response.data.message
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: '{{ __("Error!") }}',
                text: error.response.data.message || '{{ __("Failed to send test email") }}'
            });
        });
}
</script>
@endpush
