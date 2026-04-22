<?php

declare(strict_types=1);

return [
    'disabled' => 'AI-assisted translation is disabled for this environment.',
    'not_configured' => 'AI translation is not configured (missing API key).',
    'provider_error' => 'The translation service could not complete the request. Try again later.',
    'error_rate_limited' => 'The AI translation service is temporarily busy or has reached a usage limit. This is '
        . 'not caused by your text. Please wait a minute or two and try again. If it keeps happening, contact your '
        . 'system administrator.',
    'error_models_unavailable' => 'None of the configured models are available from the provider (invalid ID or no '
        . 'capacity). Update OPENROUTER_MODELS and/or GROQ_MODELS with active IDs (e.g. openrouter.ai/models, '
        . 'console.groq.com/docs/models).',
    'error_all_models_failed' => 'The AI service did not return a usable response. The provider may be throttling or '
        . 'rejecting traffic at the account level (for example several 429s in a row), not necessarily because a '
        . 'model ID is “wrong”. This does not by itself mean your API keys are invalid. See storage/logs/laravel.log '
        . '(attempts) and your provider dashboard.',
    'error_no_models' => 'No AI models are configured. Set OPENROUTER_MODELS (and GROQ_MODELS if you use Groq).',
    'error_credentials' => 'The AI service rejected the API credentials. Check GITHUB_MODELS_TOKEN (if using GitHub Models), '
        . 'OPENROUTER_API_KEY, and GROQ_API_KEY depending on which provider failed.',
    'error_no_source' => 'There is no text in other languages to translate from.',
    'error_no_applicable' => 'Nothing to translate for this action in the current tab.',
    'error_payload_too_large' => 'The text to translate is too large. Shorten the source fields '
        . 'or raise AI_TRANSLATION_MAX_SOURCE_CHARS.',
    'error_universal_target' => 'Cannot translate into the universal locale.',
    'error_model_empty' => 'The AI returned no usable translations. Try again or adjust the source text.',
    'translate_fill' => 'Translate missing fields',
    'translate_regenerate' => 'Regenerate from sources',
    'busy' => 'Translating…',
    'actions_aria' => 'AI translation actions for :locale',
    'buttons_disclaimer' => 'The content will be generated using AI. Review before saving. '
        . 'Please use sparingly, this feature has usage limits.',
];
