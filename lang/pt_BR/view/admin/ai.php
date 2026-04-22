<?php

declare(strict_types=1);

return [
    'disabled' => 'A tradução assistida por IA está desativada neste ambiente.',
    'not_configured' => 'A tradução por IA não está configurada (chave de API ausente).',
    'provider_error' => 'O serviço de tradução não concluiu o pedido. Tente novamente mais tarde.',
    'error_rate_limited' => 'O serviço de tradução por IA está temporariamente sobrecarregado ou com limite de uso '
        . 'atingido. Não é um problema no texto que está a traduzir. Espere um ou dois minutos e tente novamente. '
        . 'Se continuar a acontecer, avise quem administra o sistema.',
    'error_models_unavailable' => 'Nenhum dos modelos configurados está disponível no fornecedor (ID inválido ou sem '
        . 'fornecedores). Atualize OPENROUTER_MODELS e/ou GROQ_MODELS com IDs ativos (ex.: openrouter.ai/models, '
        . 'console.groq.com/docs/models).',
    'error_all_models_failed' => 'Não foi possível obter resposta da IA. O fornecedor pode estar a limitar ou a '
        . 'recusar pedidos ao nível da conta (p.ex. vários 429 seguidos), não necessariamente por um modelo “errado”. '
        . 'Isto não indica por si chaves API inválidas. Veja storage/logs/laravel.log (attempts) e o painel do fornecedor.',
    'error_no_models' => 'Nenhum modelo de IA está configurado. Defina OPENROUTER_MODELS (e GROQ_MODELS se usar Groq).',
    'error_credentials' => 'O serviço de IA recusou as credenciais. Verifique GITHUB_MODELS_TOKEN (se usar GitHub Models), '
        . 'OPENROUTER_API_KEY e GROQ_API_KEY conforme o fornecedor que falhou.',
    'error_no_source' => 'Não há texto noutros idiomas para usar como fonte.',
    'error_no_applicable' => 'Nada a traduzir para esta ação neste separador.',
    'error_payload_too_large' => 'O texto a traduzir é demasiado grande. Reduza os campos de origem '
        . 'ou aumente AI_TRANSLATION_MAX_SOURCE_CHARS.',
    'error_universal_target' => 'Não é possível traduzir para o idioma universal.',
    'error_model_empty' => 'A IA não devolveu traduções utilizáveis. Tente novamente ou ajuste o texto de origem.',
    'translate_fill' => 'Traduzir campos vazios',
    'translate_regenerate' => 'Regenerar a partir das fontes',
    'busy' => 'A traduzir…',
    'actions_aria' => 'Ações de tradução por IA para :locale',
    'buttons_disclaimer' => 'O conteúdo será gerado com IA. Revisar antes de salvar. '
        . 'Use com moderação, o recurso tem limites de uso.',
];
