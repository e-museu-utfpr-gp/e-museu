<?php

declare(strict_types=1);

return [
    'disabled' => 'A tradução assistida por IA está desativada neste ambiente.',
    'not_configured' => 'A tradução por IA não está configurada (chave de API ausente).',
    'provider_error' => 'O serviço de tradução não concluiu o pedido. Tente novamente mais tarde.',
    'internal_error' => 'Ocorreu um erro ao processar o pedido de tradução. Tente novamente mais tarde.',
    'ajax_invalid_response' => 'O servidor devolveu uma resposta inesperada. Atualize a página ou tente de novo.',
    'ajax_session_expired' => 'A sua sessão expirou. Atualize a página e inicie sessão novamente.',
    'ajax_forbidden' => 'Não tem permissão para esta ação.',
    'ajax_validation' => 'O pedido não passou na validação. Verifique o formulário e tente novamente.',
    'toast_close_aria' => 'Fechar',
    'error_rate_limited' => 'O serviço de tradução por IA está temporariamente sobrecarregado ou com limite de uso '
        . 'atingido. Não é um problema no texto que está a traduzir. Espere um ou dois minutos e tente novamente. '
        . 'Se continuar a acontecer, avise quem administra o sistema.',
    'error_models_unavailable' => 'Nenhum dos modelos configurados está disponível no fornecedor (ID inválido ou sem '
        . 'capacidade / fila). Atualize as listas de modelos (separadas por vírgula) no `.env` para cada bloco de IA '
        . '(ver `config/ai.php` e `.env.example`).',
    'error_all_models_failed' => 'Não foi possível obter resposta da IA. O fornecedor pode estar a limitar ou a '
        . 'recusar pedidos ao nível da conta (p.ex. vários 429 seguidos), não necessariamente por um modelo “errado”. '
        . 'Isto não indica por si chaves API inválidas. Veja storage/logs/laravel.log (attempts) e o painel do fornecedor.',
    'error_no_models' => 'Nenhum modelo de IA está configurado. Defina as variáveis `*_MODELS` no `.env` para cada '
        . 'bloco de fornecedor ativo (ver `config/ai.php`).',
    'error_credentials' => 'O serviço de IA recusou as credenciais. Verifique as variáveis `*_API_KEY` / `*_TOKEN` no '
        . '`.env` para os blocos definidos em `config/ai.php`.',
    'error_no_source' => 'Não há texto noutros idiomas para usar como fonte.',
    'error_no_applicable' => 'Nada a traduzir para esta ação neste separador.',
    'error_payload_too_large' => 'O texto a traduzir é demasiado grande. Reduza os campos de origem '
        . 'ou aumente AI_TRANSLATION_MAX_SOURCE_CHARS.',
    'error_universal_target' => 'Não é possível traduzir para o idioma universal.',
    'error_model_empty' => 'A IA não devolveu traduções utilizáveis. Tente novamente ou ajuste o texto de origem.',
    'error_selected_provider_unavailable' => 'O fornecedor selecionado (:provider) não está disponível neste ambiente. '
        . 'Tente outro fornecedor ou use Auto.',
    'error_selected_provider_failed' => 'O fornecedor selecionado (:provider) falhou. Tente outro fornecedor ou use Auto.',
    'translate_fill' => 'Traduzir campos vazios',
    'translate_regenerate' => 'Regenerar a partir das fontes',
    'provider_auto' => 'Auto',
    'provider_default' => ':name',
    'provider_select_aria' => 'Selecionar fornecedor de IA para :locale',
    'busy' => 'A traduzir…',
    'actions_aria' => 'Ações de tradução por IA para :locale',
    'buttons_disclaimer' => 'Os botões acima usam IA para sugerir traduções. Revise o resultado antes de gravar. '
        . 'O recurso é limitado (quotas e pedidos por minuto); use com moderação.',
    'generated_with' => 'Gerado com :provider (:model).',
];
