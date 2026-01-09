window.createDialogues = [
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
            { text: "Saber mais sobre o museu >", url: "/about" },
            { text: "Entrar em contato com o museu >", nextId: 7 },
        ]
    },
    {
        id: 3,
        text: "Com está página você pode compartilhar as informações de um item seu que ainda não temos no nosso museu. Por favor, esteja ciente de que este item estará disponibilizado no nosso museu para ser visualizado por outras pessoas.",
        choices: [
            { text: "Instruções de cadastro", nextId: 4 },
            { text: "Sobre o envio de email e nome completo", nextId: 5 },
            { text: "Gostaria de remover um item do museu", nextId: 6 },
            { text: "Outras dúvidas", nextId: 7 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 4,
        text: "Acima de cada campo haverá um ícone em laranja com um 'i' no interior. Ao selecionar o ícone, instruções de cadastro para o campo específico serão disponibilizados. Por favor, leia as instruções antes de inserir os dados. Note que, os títulos de campo com um '*' são obrigatórios. Após inserir as informações, confira se cumpriu os requisitos e selecione 'enviar'!",
        choices: [
            { text: "Sobre o envio de email e nome completo", nextId: 5 },
            { text: "Gostaria de remover um item do museu", nextId: 6 },
            { text: "Outras dúvidas", nextId: 7 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 5,
        text: "Precisamos de seu email para podermos entrar em contato, caso algum imprevisto ocorra. O seu nome completo será usado para podermos creditar a sua contribuição com nosso museu. Precisamos apenas destas informações, nenhum tipo de senha é necessária!",
        choices: [
            { text: "Instruções de cadastro", nextId: 4 },
            { text: "Gostaria de remover um item do museu", nextId: 6 },
            { text: "Outras dúvidas", nextId: 7 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 6,
        text: "Caso queira remover algum item que tenha cadastrado no nosso museu, por favor, envie um email para emuseuvirtual@gmail.com, com do email que utilizou para o cadastro do item.",
        choices: [
            { text: "Instruções de cadastro", nextId: 4 },
            { text: "Sobre o envio de email e nome completo", nextId: 5 },
            { text: "Outras dúvidas", nextId: 7 },
            { text: "Voltar para as opções", nextId: 2 },
        ]
    },
    {
        id: 7,
        text: "Caso tenha alguma dúvida ou outros assuntos a tratar conosco, envie um email para emuseuvirtual@gmail.com.",
        choices: [
            { text: "Voltar para as opções", nextId: 2 }
        ]
    },
];
