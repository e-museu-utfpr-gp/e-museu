<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('mail.item_contribution_received.subject', ['app' => config('app.name'), 'item' => $itemDisplayName]) }}</title>
</head>
<body style="font-family: system-ui, sans-serif; line-height: 1.5; color: #222;">
    <p>{{ __('mail.item_contribution_received.greeting', ['name' => $collaboratorName]) }}</p>
    <p>{{ __('mail.item_contribution_received.line1', ['item' => $itemDisplayName]) }}</p>
    <p style="font-size: 0.875rem; color: #555;">{{ __('mail.item_contribution_received.line2') }}</p>
    <p style="font-size: 0.875rem; color: #555;">{{ __('mail.item_contribution_received.salutation', ['app' => config('app.name')]) }}</p>
</body>
</html>
