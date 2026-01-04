window.showDialogues = [
    {
        id: 1,
        text: "Olá, sou Ada, sua assistente virtual. Como posso te ajudar?",
        choices: [
            { text: "Disponibilizar opções", nextId: 2 },
        ]
    },
    {
        id: 2,
        text: "O que gostaria de saber?",
        choices: [
            { text: "Saber mais sobre a página atual", nextId: 3 },
            { text: "Ir para a página inicial do museu >", url: "/" },
            { text: "Explorar outros itens do museu >", url: "/items" },
            { text: "Contribuir com o museu? >", url: "/items/create" },
            { text: "Saber mais sobre o museu >", url: "/about" },
            { text: "Entrar em contato com o museu >", nextId: 14 },
        ]
    },
    {
        id: 3,
        text: "Nesta página você pode conferir todas as informações disponíveis sobre o item específico. Saiba mais sobre a aparência, história, data de lançamento, detalhes técnicos, linhas do tempo, categoria, componentes, etiquetas associadas e informações extra deste item!",
        choices: [
            { text: "Aparência", nextId: 4 },
            { text: "Data de lançamento", nextId: 5 },
            { text: "Categoria", nextId: 6 },
            { text: "Etiquetas associadas", nextId: 7 },
            { text: "Descrição breve", nextId: 8 },
            { text: "História", nextId: 9 },
            { text: "Linhas do tempo", nextId: 10 },
            { text: "Detalhes técnicos", nextId: 11 },
            { text: "Componentes", nextId: 12 },
            { text: "Informações extra", nextId: 13 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 4,
        text: "Você pode conferir a aparência do item através da imagem na parte superior da página. Você também pode aproximar a imagem um pouco mais, ao selecioná-la.",
        choices: [
            { text: "Data de lançamento", nextId: 5 },
            { text: "Categoria", nextId: 6 },
            { text: "Etiquetas associadas", nextId: 7 },
            { text: "Descrição breve", nextId: 8 },
            { text: "História", nextId: 9 },
            { text: "Linhas do tempo", nextId: 10 },
            { text: "Detalhes técnicos", nextId: 11 },
            { text: "Componentes", nextId: 12 },
            { text: "Informações extra", nextId: 13 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 5,
        text: "A data de lançamento compreende o dia, mês e ano em que o item foi disponibilizado para uso.",
        choices: [
            { text: "Aparência", nextId: 4 },
            { text: "Categoria", nextId: 6 },
            { text: "Etiquetas associadas", nextId: 7 },
            { text: "Descrição breve", nextId: 8 },
            { text: "História", nextId: 9 },
            { text: "Linhas do tempo", nextId: 10 },
            { text: "Detalhes técnicos", nextId: 11 },
            { text: "Componentes", nextId: 12 },
            { text: "Informações extra", nextId: 13 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 6,
        text: "Todo item faz parte de apenas uma categoria. Selecionando a categoria, você pode navegar para uma página listando todos os itens que fazem parte dela.",
        choices: [
            { text: "Aparência", nextId: 4 },
            { text: "Data de lançamento", nextId: 5 },
            { text: "Etiquetas associadas", nextId: 7 },
            { text: "Descrição breve", nextId: 8 },
            { text: "História", nextId: 9 },
            { text: "Linhas do tempo", nextId: 10 },
            { text: "Detalhes técnicos", nextId: 11 },
            { text: "Componentes", nextId: 12 },
            { text: "Informações extra", nextId: 13 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 7,
        text: "Além das categorias, alguns itens também recebem uma ou mais etiquetas, que têm o propósito de organizar com mais precisão os itens do nosso museu. Com este sistema, podemos filtrar itens por marca, série, e muito mais! Selecione uma etiqueta para buscar mais itens pertencentes a ela.",
        choices: [
            { text: "Aparência", nextId: 4 },
            { text: "Data de lançamento", nextId: 5 },
            { text: "Categoria", nextId: 6 },
            { text: "Descrição breve", nextId: 8 },
            { text: "História", nextId: 9 },
            { text: "Linhas do tempo", nextId: 10 },
            { text: "Detalhes técnicos", nextId: 11 },
            { text: "Componentes", nextId: 12 },
            { text: "Informações extra", nextId: 13 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 8,
        text: "A descrição breve apresenta o item de uma forma concisa, podendo considerar aspectos físicos, e também sua funcionalidade.",
        choices: [
            { text: "Aparência", nextId: 4 },
            { text: "Data de lançamento", nextId: 5 },
            { text: "Categoria", nextId: 6 },
            { text: "Etiquetas associadas", nextId: 7 },
            { text: "História", nextId: 9 },
            { text: "Linhas do tempo", nextId: 10 },
            { text: "Detalhes técnicos", nextId: 11 },
            { text: "Componentes", nextId: 12 },
            { text: "Informações extra", nextId: 13 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 9,
        text: "Nessa seção do museu contaremos mais detalhadamente a história do item. Tudo o que pudemos juntar de informações interessantes sobre sua criação e funcionamento colocaremos aqui! Caso tenha alguma informação que não tenha encontrado nesta página, e que gostaria de compartilhar conosco, por favor, envie uma informação extra utilizando o botão acima da imagem do item!",
        choices: [
            { text: "Aparência", nextId: 4 },
            { text: "Data de lançamento", nextId: 5 },
            { text: "Categoria", nextId: 6 },
            { text: "Etiquetas associadas", nextId: 7 },
            { text: "Descrição breve", nextId: 8 },
            { text: "Linhas do tempo", nextId: 10 },
            { text: "Detalhes técnicos", nextId: 11 },
            { text: "Componentes", nextId: 12 },
            { text: "Informações extra", nextId: 13 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 10,
        text: "Através das linhas do tempo você pode acompanhar a evolução de itens pertencentes a mesma etiqueta de categoria 'série'. Você pode também visualizar o item com mais detalhes selecionando a opção 'mais detalhes'.",
        choices: [
            { text: "Aparência", nextId: 4 },
            { text: "Data de lançamento", nextId: 5 },
            { text: "Categoria", nextId: 6 },
            { text: "Etiquetas associadas", nextId: 7 },
            { text: "Descrição breve", nextId: 8 },
            { text: "História", nextId: 9 },
            { text: "Detalhes técnicos", nextId: 11 },
            { text: "Componentes", nextId: 12 },
            { text: "Informações extra", nextId: 13 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 11,
        text: "Os detalhes técnicos apresentam características mais minunciosas sobre a aparência, componentes internos e especificações do item.",
        choices: [
            { text: "Aparência", nextId: 4 },
            { text: "Data de lançamento", nextId: 5 },
            { text: "Categoria", nextId: 6 },
            { text: "Etiquetas associadas", nextId: 7 },
            { text: "Descrição breve", nextId: 8 },
            { text: "História", nextId: 9 },
            { text: "Linhas do tempo", nextId: 10 },
            { text: "Componentes", nextId: 12 },
            { text: "Informações extra", nextId: 13 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 12,
        text: "Dependendo do item, ele pode ser composto por outros itens do museu. Selecionando um deles, você navega para a página do item.",
        choices: [
            { text: "Aparência", nextId: 4 },
            { text: "Data de lançamento", nextId: 5 },
            { text: "Categoria", nextId: 6 },
            { text: "Etiquetas associadas", nextId: 7 },
            { text: "Descrição breve", nextId: 8 },
            { text: "História", nextId: 9 },
            { text: "Linhas do tempo", nextId: 10 },
            { text: "Detalhes técnicos", nextId: 11 },
            { text: "Informações extra", nextId: 13 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },

    {
        id: 13,
        text: "Todas as informações que não se encaixarem nas outras seções, ou que foram adicionados por outros colaboradores, serão agrupados como 'informações extra'. Se tiver alguma informação que queria compartilhar conosco sobre o item da página, por favor, nos envie através do botão no topo da página!",
        choices: [
            { text: "Aparência", nextId: 4 },
            { text: "Data de lançamento", nextId: 5 },
            { text: "Categoria", nextId: 6 },
            { text: "Etiquetas associadas", nextId: 7 },
            { text: "Descrição breve", nextId: 8 },
            { text: "História", nextId: 9 },
            { text: "Linhas do tempo", nextId: 10 },
            { text: "Detalhes técnicos", nextId: 11 },
            { text: "Componentes", nextId: 12 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 14,
        text: "Caso tenha alguma dúvida ou outros assuntos a tratar conosco, envie um email para emuseuvirtual@gmail.com.",
        choices: [
            { text: "Voltar para as opções", nextId: 2 }
        ]
    },
];
