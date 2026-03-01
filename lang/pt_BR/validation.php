<?php

/*
| Laravel framework language file. Keys and structure are defined by the framework.
| Do not rename keys. Keep this file in sync with the same file in other locales (e.g. en).
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Validation messages (framework)
    |--------------------------------------------------------------------------
    |
    | Messages shown when validation fails. Each key maps to a Validator rule
    | (required, email, min, etc.). Placeholders: :attribute, :min, :max,
    | :value, :other, :values, :date, :format, etc.
    | Keep this file in sync with lang/en/validation.php (same keys).
    |
    */

    'accepted' => 'O campo :attribute deve ser aceito.',
    'accepted_if' => 'O :attribute deve ser aceito quando :other for :value.',
    'active_url' => 'O campo :attribute não é uma URL válida.',
    'after' => 'O campo :attribute deve ser uma data posterior a :date.',
    'after_or_equal' => 'O campo :attribute deve ser uma data posterior ou igual a :date.',
    'alpha' => 'O campo :attribute só pode conter letras.',
    'alpha_dash' => 'O campo :attribute só pode conter letras, números e traços.',
    'alpha_num' => 'O campo :attribute só pode conter letras e números.',
    'any_of' => 'O campo :attribute é inválido.',
    'array' => 'O campo :attribute deve ser uma matriz.',
    'ascii' => 'O campo :attribute só pode conter caracteres alfanuméricos e símbolos de um byte.',
    'before' => 'O campo :attribute deve ser uma data anterior :date.',
    'before_or_equal' => 'O campo :attribute deve ser uma data anterior ou igual a :date.',
    'between' => [
        'numeric' => 'O campo :attribute deve ser entre :min e :max.',
        'file' => 'O campo :attribute deve ser entre :min e :max kilobytes.',
        'string' => 'O campo :attribute deve ser entre :min e :max caracteres.',
        'array' => 'O campo :attribute deve ter entre :min e :max itens.',
    ],
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
    'can' => 'O campo :attribute contém um valor não autorizado.',
    'confirmed' => 'O campo confirmação de :attribute não confere.',
    'contains' => 'O campo :attribute está sem um valor obrigatório.',
    'current_password' => 'A senha está incorreta.',
    'date' => 'O campo :attribute não é uma data válida.',
    'date_equals' => 'O campo :attribute deve ser uma data igual a :date.',
    'date_format' => 'O campo :attribute não corresponde ao formato :format.',
    'decimal' => 'O campo :attribute deve ter :decimal casas decimais.',
    'declined' => 'O :attribute deve ser recusado.',
    'declined_if' => 'O :attribute deve ser recusado quando :other for :value.',
    'different' => 'Os campos :attribute e :other devem ser diferentes.',
    'digits' => 'O campo :attribute deve ter :digits dígitos.',
    'digits_between' => 'O campo :attribute deve ter entre :min e :max dígitos.',
    'dimensions' => 'O campo :attribute tem dimensões de imagem inválidas.',
    'distinct' => 'O campo :attribute tem um valor duplicado.',
    'doesnt_contain' => 'O campo :attribute não pode conter nenhum dos seguintes: :values.',
    'doesnt_end_with' => 'O campo :attribute não pode terminar com um dos seguintes: :values.',
    'doesnt_start_with' => 'O :attribute não pode começar com um dos seguintes: :values.',
    'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'encoding' => 'O campo :attribute deve estar codificado em :encoding.',
    'ends_with' => 'O campo :attribute deve terminar com um dos seguintes: :values',
    'enum' => 'O :attribute selecionado é inválido.',
    'exists' => 'O campo :attribute selecionado é inválido.',
    'extensions' => 'O campo :attribute deve ter uma das seguintes extensões: :values.',
    'file' => 'O campo :attribute deve ser um arquivo.',
    'filled' => 'O campo :attribute deve ter um valor.',
    'gt' => [
        'numeric' => 'O campo :attribute deve ser maior que :value.',
        'file' => 'O campo :attribute deve ser maior que :value kilobytes.',
        'string' => 'O campo :attribute deve ser maior que :value caracteres.',
        'array' => 'O campo :attribute deve conter mais de :value itens.',
    ],
    'gte' => [
        'numeric' => 'O campo :attribute deve ser maior ou igual a :value.',
        'file' => 'O campo :attribute deve ser maior ou igual a :value kilobytes.',
        'string' => 'O campo :attribute deve ser maior ou igual a :value caracteres.',
        'array' => 'O campo :attribute deve conter :value itens ou mais.',
    ],
    'hex_color' => 'O campo :attribute deve ser uma cor hexadecimal válida.',
    'image' => 'O campo :attribute deve ser uma imagem.',
    'in' => 'O campo :attribute selecionado é inválido.',
    'in_array' => 'O campo :attribute não existe em :other.',
    'in_array_keys' => 'O campo :attribute deve conter pelo menos uma das seguintes chaves: :values.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'ip' => 'O campo :attribute deve ser um endereço de IP válido.',
    'ipv4' => 'O campo :attribute deve ser um endereço IPv4 válido.',
    'ipv6' => 'O campo :attribute deve ser um endereço IPv6 válido.',
    'json' => 'O campo :attribute deve ser uma string JSON válida.',
    'list' => 'O campo :attribute deve ser uma lista.',
    'lowercase' => 'O campo :attribute deve estar em minúsculas.',
    'lt' => [
        'numeric' => 'O campo :attribute deve ser menor que :value.',
        'file' => 'O campo :attribute deve ser menor que :value kilobytes.',
        'string' => 'O campo :attribute deve ser menor que :value caracteres.',
        'array' => 'O campo :attribute deve conter menos de :value itens.',
    ],
    'lte' => [
        'numeric' => 'O campo :attribute deve ser menor ou igual a :value.',
        'file' => 'O campo :attribute deve ser menor ou igual a :value kilobytes.',
        'string' => 'O campo :attribute deve ser menor ou igual a :value caracteres.',
        'array' => 'O campo :attribute não deve conter mais que :value itens.',
    ],
    'mac_address' => 'O campo :attribute deve ser um endereço MAC válido.',
    'max' => [
        'numeric' => 'O campo :attribute não pode ser superior a :max.',
        'file' => 'O campo :attribute não pode ser superior a :max kilobytes.',
        'string' => 'O campo :attribute não pode ser superior a :max caracteres.',
        'array' => 'O campo :attribute não pode ter mais do que :max itens.',
    ],
    'max_digits' => 'O campo :attribute não pode ser superior a :max dígitos',
    'mimes' => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'mimetypes' => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'min' => [
        'numeric' => 'O campo :attribute deve ser pelo menos :min.',
        'file' => 'O campo :attribute deve ter pelo menos :min kilobytes.',
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
        'array' => 'O campo :attribute deve ter pelo menos :min itens.',
    ],
    'missing_with' => 'O campo :attribute não deve estar presente quando houver :values.',
    'min_digits' => 'O campo :attribute deve ter pelo menos :min dígitos',
    'missing' => 'O campo :attribute deve estar ausente.',
    'missing_if' => 'O campo :attribute deve estar ausente quando :other for :value.',
    'missing_unless' => 'O campo :attribute deve estar ausente a menos que :other seja :value.',
    'missing_with_all' => 'O campo :attribute deve estar ausente quando :values estiverem presentes.',
    'not_in' => 'O campo :attribute selecionado é inválido.',
    'multiple_of' => 'O campo :attribute deve ser um múltiplo de :value.',
    'not_regex' => 'O campo :attribute possui um formato inválido.',
    'numeric' => 'O campo :attribute deve ser um número.',
    'password' => [
        'letters' => 'O campo :attribute deve conter pelo menos uma letra.',
        'mixed' => 'O campo :attribute deve conter pelo menos uma letra maiúscula e uma letra minúscula.',
        'numbers' => 'O campo :attribute deve conter pelo menos um número.',
        'symbols' => 'O campo :attribute deve conter pelo menos um símbolo.',
        'uncompromised' => 'A senha que você inseriu em :attribute está em um vazamento de dados.'
            .' Por favor escolha uma senha diferente.',
    ],
    'present' => 'O campo :attribute deve estar presente.',
    'present_if' => 'O campo :attribute deve estar presente quando :other for :value.',
    'present_unless' => 'O campo :attribute deve estar presente a menos que :other seja :value.',
    'present_with' => 'O campo :attribute deve estar presente quando :values estiver presente.',
    'present_with_all' => 'O campo :attribute deve estar presente quando :values estiverem presentes.',
    'regex' => 'O campo :attribute tem um formato inválido.',
    'required' => 'O campo :attribute é obrigatório.',
    'required_array_keys' => 'O campo :attribute deve conter entradas para: :values.',
    'required_if' => 'O campo :attribute é obrigatório quando :other for :value.',
    'required_if_accepted' => 'O campo :attribute é obrigatório quando :other for aceito.',
    'required_if_declined' => 'O campo :attribute é obrigatório quando :other for recusado.',
    'required_unless' => 'O campo :attribute é obrigatório exceto quando :other for :values.',
    'required_with' => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_with_all' => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_without' => 'O campo :attribute é obrigatório quando :values não está presente.',
    'required_without_all' => 'O campo :attribute é obrigatório quando nenhum dos :values estão presentes.',
    'prohibited' => 'O campo :attribute é proibido.',
    'prohibited_if' => 'O campo :attribute é proibido quando :other for :value.',
    'prohibited_if_accepted' => 'O campo :attribute é proibido quando :other for aceito.',
    'prohibited_if_declined' => 'O campo :attribute é proibido quando :other for recusado.',
    'prohibited_unless' => 'O campo :attribute é proibido exceto quando :other for :values.',
    'prohibits' => 'O campo :attribute proíbe :other de estar presente.',
    'same' => 'Os campos :attribute e :other devem corresponder.',
    'size' => [
        'numeric' => 'O campo :attribute deve ser :size.',
        'file' => 'O campo :attribute deve ser :size kilobytes.',
        'string' => 'O campo :attribute deve ser :size caracteres.',
        'array' => 'O campo :attribute deve conter :size itens.',
    ],
    'starts_with' => 'O campo :attribute deve começar com um dos seguintes valores: :values',
    'string' => 'O campo :attribute deve ser uma string.',
    'timezone' => 'O campo :attribute deve ser uma zona válida.',
    'unique' => 'O campo :attribute já está sendo utilizado.',
    'uploaded' => 'Ocorreu uma falha no upload do campo :attribute.',
    'uppercase' => 'O campo :attribute deve estar em maiúsculas.',
    'url' => 'O campo :attribute tem um formato inválido.',
    'ulid' => 'O campo :attribute deve ser um ULID válido.',
    'uuid' => 'O campo :attribute deve ser um UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom messages per attribute and rule
    |--------------------------------------------------------------------------
    |
    | Use the "attribute_name.rule_name" convention to override the default
    | message for a specific field only.
    | E.g.: 'email.required' => 'The email is required.'
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'image' => [
            'mimes' => 'A extensão da imagem deve ser: jpeg, png, jpg ou webp.',
            'max' => 'A imagem deve ter no máximo 10 MB.',
        ],
    ],

    'catalog' => [
        'item_component_different' => 'O item e o componente precisam ser diferentes.',
        'item_tag_different' => 'O item e a etiqueta precisam ser diferentes.',
        'components_items_different' => 'Os itens precisam ser diferentes.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Friendly attribute names
    |--------------------------------------------------------------------------
    |
    | Replaces the :attribute placeholder in messages with a readable name
    | (e.g. "email" becomes "Email address"). Add your form field names here
    | for clearer validation messages.
    |
    */

    'attributes' => [
        'address' => 'endereço',
        'age' => 'idade',
        'body' => 'conteúdo',
        'cell' => 'célula',
        'city' => 'cidade',
        'country' => 'país',
        'date' => 'data',
        'day' => 'dia',
        'excerpt' => 'resumo',
        'first_name' => 'primeiro nome',
        'gender' => 'gênero',
        'marital_status' => 'estado civil',
        'profession' => 'profissão',
        'nationality' => 'nacionalidade',
        'hour' => 'hora',
        'last_name' => 'sobrenome',
        'message' => 'mensagem',
        'minute' => 'minuto',
        'mobile' => 'celular',
        'month' => 'mês',
        'name' => 'nome',
        'zipcode' => 'cep',
        'company_name' => 'razão social',
        'neighborhood' => 'bairro',
        'number' => 'número',
        'password' => 'senha',
        'phone' => 'telefone',
        'second' => 'segundo',
        'sex' => 'sexo',
        'state' => 'estado',
        'street' => 'rua',
        'subject' => 'assunto',
        'text' => 'texto',
        'time' => 'hora',
        'title' => 'título',
        'username' => 'usuário',
        'year' => 'ano',
        'description' => 'descrição',
        'password_confirmation' => 'confirmação da senha',
        'current_password' => 'senha atual',
        'complement' => 'complemento',
        'modality' => 'modalidade',
        'category' => 'categoria',
        'blood_type' => 'tipo sanguíneo',
        'birth_date' => 'data de nascimento',
        'category_id' => 'categoria',
        'validation' => 'validação',
        'item_id' => 'id do item',
        'component_id' => 'id do componente',
        'tag_id' => 'id da etiqueta',
        'section_id' => 'categoria do item',
        'full_name' => 'nome completo',
        'contact' => 'e-mail',
        'info' => 'curiosidade',
        'image' => 'imagem',
        'identification_code' => 'código de identificação',
        'proprietary_id' => 'proprietário',
        'detail' => 'detalhe',
        'history' => 'história',
        'is_admin' => 'administrador',
    ],

];
