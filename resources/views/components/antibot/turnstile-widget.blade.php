@once
    @push('scripts')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endpush
@endonce

<div
    class="cf-turnstile mb-3"
    data-sitekey="{{ $siteKey }}"
    @isset($responseFieldName) data-response-field-name="{{ $responseFieldName }}" @endisset
></div>
