<?php

declare(strict_types=1);

return [
    'disabled' => 'AI-assisted translation is disabled for this environment.',
    'not_configured' => 'AI translation is not configured (missing API key).',
    'provider_error' => 'The translation service could not complete the request. Try again later.',
    'internal_error' => 'Something went wrong while processing the translation request. Try again later.',
    'ajax_invalid_response' => 'The server returned an unexpected response. Refresh the page or try again.',
    'ajax_session_expired' => 'Your session has expired. Refresh the page and sign in again.',
    'ajax_forbidden' => 'You are not allowed to perform this action.',
    'ajax_validation' => 'The request could not be validated. Check the form and try again.',
    'toast_close_aria' => 'Close',
    'error_rate_limited' => 'The AI translation service is temporarily busy or has reached a usage limit. This is '
        . 'not caused by your text. Please wait a minute or two and try again. If it keeps happening, contact your '
        . 'system administrator.',
    'error_models_unavailable' => 'None of the configured models are available from the provider (invalid ID or no '
        . 'capacity). Update the comma-separated model lists in `.env` for your AI provider blocks (see `config/ai.php` '
        . 'and `.env.example`).',
    'error_all_models_failed' => 'The AI service did not return a usable response. The provider may be throttling or '
        . 'rejecting traffic at the account level (for example several 429s in a row), not necessarily because a '
        . 'model ID is “wrong”. This does not by itself mean your API keys are invalid. See storage/logs/laravel.log '
        . '(attempts) and your provider dashboard.',
    'error_no_models' => 'No AI models are configured. Set the `*_MODELS` variables in `.env` for each enabled provider '
        . 'block (see `config/ai.php`).',
    'error_credentials' => 'The AI service rejected the API credentials. Check the `*_API_KEY` / `*_TOKEN` variables in '
        . '`.env` for the provider blocks defined in `config/ai.php`.',
    'error_no_source' => 'There is no text in other languages to translate from.',
    'error_no_applicable' => 'Nothing to translate for this action in the current tab.',
    'error_payload_too_large' => 'The text to translate is too large. Shorten the source fields '
        . 'or raise AI_TRANSLATION_MAX_SOURCE_CHARS.',
    'error_universal_target' => 'Cannot translate into the universal locale.',
    'error_model_empty' => 'The AI returned no usable translations. Try again or adjust the source text.',
    'error_selected_provider_unavailable' => 'The selected provider (:provider) is not available in this environment. '
        . 'Try another provider or use Auto.',
    'error_selected_provider_failed' => 'The selected provider (:provider) failed. Try another provider or use Auto.',
    'translate_fill' => 'Translate missing fields',
    'translate_regenerate' => 'Regenerate from sources',
    'provider_auto' => 'Auto',
    'provider_default' => ':name',
    'provider_select_aria' => 'Select AI provider for :locale',
    'busy' => 'Translating…',
    'actions_aria' => 'AI translation actions for :locale',
    'buttons_disclaimer' => 'The buttons above use AI to suggest translations. Review the result before saving. '
        . 'The feature is limited (provider quotas and requests per minute); use it sparingly.',
    'generated_with' => 'Generated with :provider (:model).',
];
