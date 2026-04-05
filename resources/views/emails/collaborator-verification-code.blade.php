<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('mail.collaborator_verification_code.subject', ['app' => config('app.name')]) }}</title>
</head>
<body style="font-family: system-ui, sans-serif; line-height: 1.5; color: #222;">
    @php
        $displayName = trim((string) ($collaboratorName ?? ''));
    @endphp
    @if ($displayName !== '')
        <p>{{ __('mail.collaborator_verification_code.greeting', ['name' => $displayName]) }}</p>
    @else
        <p>{{ __('mail.collaborator_verification_code.greeting_generic') }}</p>
    @endif
    <p>{{ __('mail.collaborator_verification_code.line1') }}</p>
    <p style="font-size: 1.5rem; font-weight: 700; letter-spacing: 0.2em;">{{ $code }}</p>
    <p style="font-size: 0.875rem; color: #555;">{{ __('mail.collaborator_verification_code.line2') }}</p>
    <p style="font-size: 0.875rem; color: #555;">{{ __('mail.collaborator_verification_code.salutation', ['app' => config('app.name')]) }}</p>
</body>
</html>
