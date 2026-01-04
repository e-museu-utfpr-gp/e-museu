window.aboutDialogues = [
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
            { text: "Explorar itens do museu >", url: "items" },
            { text: "Contribuir com o museu >", url: "items/create" },
            { text: "Entrar em contato com o museu >", nextId: 4 },
        ]
    },
    {
        id: 3,
        text: "Nesta página contaremos um pouco mais sobre o nosso projeto e as entidades envolvidas na criação do E-museu.",
        choices: [
            { text: "Voltar para as opções", nextId: 2 }
        ]
    },
    {
        id: 4,
        text: "Caso tenha alguma dúvida ou outros assuntos a tratar conosco, envie um email para emuseuvirtual@gmail.com.",
        choices: [
            { text: "Voltar para as opções", nextId: 2 }
        ]
    },
];
