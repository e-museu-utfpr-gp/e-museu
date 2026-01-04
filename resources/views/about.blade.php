@extends('layouts.app')
@section('title', 'Sobre')

@section('content')
    <div class="container main-container mb-auto">
        <h1>Sobre o Museu Virtual</h1>
        <div class="row">
            <div class="col-md-6">
                <p class="p-4">
                    O nosso museu virtual foi criado graças ao apoio da UTFPR e da Unicentro, através dos projetos Tecnolixo
                    e E-Lixo. Esses projetos visam coletar itens de informática descartados, com o intuito de preservar o
                    valor histórico que esses aparelhos representam. A seguir, você conhecerá mais sobre esses projetos e as
                    pessoas envolvidas.
                </p>
                <p class="ms-4 fw-bold">Para quaiquer dúvidas, entre em contato: emuseuvirtual@gmail.com.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h3>Projeto Tecno-lixo</h3>
                <p class="p-4">
                    O Tecno-lixo se trata de um projeto de extensão da <a target="_blank" href="https://www.utfpr.edu.br">Universidade
                        Tecnológica Federal do Paraná - Câmpus
                        Guarapuava</a>, sob a coordenação da professora Sediane Carmem Lunardi Hernandes e da
                    professora Sílvia do Nascimento Rosa. As atividades do Tecno-lixo iniciaram em 2019, mas durante a
                    pandemia o projeto foi desativado. Atualmente, o projeto se encontra ativo.
                    <br>
                    O projeto nasceu de um sonho antigo em trabalhar com a reutilização de peças antigas de
                    computador. O projeto foi tomando forma e hoje trabalhamos a conscientização sobre o descarte correto do
                    lixo eletrônico por meio de oficinas de conscientização. Assim, conseguimos reutilizar muitas peças de
                    computadores que não teriam serventia alguma.
                    <a target="_blank" href="https://www.utfpr.edu.br"><button class="nav-link p-3 fw-bold explore-button mt-3">ACESSAR UTFPR</button></a>
                </p>
            </div>
            <div class="col-md-6 ">
                <img src="/img/tecno-lixo-3.jpg" class="p-4 clickable-image"
                    style="aspect-ratio: 3 / 2; width: 100%; max-height: 100%; object-fit: cover" alt="">
            </div>
        </div>
        <h3>Projeto E-lixo</h3>
        <div class="row">
            <div class="col-md-6 ">
                <img src="/img/e-lixo-1.jpg" class="p-4 clickable-image"
                    style="aspect-ratio: 3 / 2; width: 100%; max-height: 100%; object-fit: cover" alt="">
            </div>
            <div class="col-md-6">
                <p class="p-4">
                    O projeto E-lixo busca promover ações para a arrecadação do lixo eletrônico da comunidade interna da <a
                        href="https://www3.unicentro.br" target="_blank">Unicentro</a>.
                    Desta forma realizar a triagem do material recolhido. E, através do trabalho de recuperação dos
                    equipamentos que foram triados, proporcionar o treinamento dos acadêmicos do curso de Ciência da
                    Computação na manutenção de computadores, complementando a formação oferecida pelo curso. Essa é uma
                    forma de disponibilizar propostas de utilização do material descartado na triagem dos computadores. Uma
                    das contribuições para a comunidade é a conscientização sobre a importância de destinar corretamente o
                    de informática, despertando o senso de responsabilidade ambiental. Também, é importante destacar que é
                    realizado o encaminhamento, para empresa específica de reciclagem, do material que não pôde ser
                    utilizado no programa.
                    <a target="_blank" href="https://www3.unicentro.br"><button class="nav-link p-3 fw-bold explore-button mt-3">ACESSAR UNICENTRO</button></a>
                </p>
            </div>
        </div>
        <h3>Galeria de fotos</h3>
        <div class="row">
            <div class="col-md-4">
                <img class="p-4 clickable-image" src="/img/e-lixo-1.jpg"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover" alt="Imagem da galeria">
            </div>
            <div class="col-md-4">
                <img class="p-4 clickable-image" src="/img/e-lixo-2.jpg"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover" alt="Imagem da galeria">
            </div>
            <div class="col-md-4">
                <img class="p-4 clickable-image" src="/img/tecno-lixo-2.jpg"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover" alt="Imagem da galeria">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <img class="p-4 clickable-image" src="/img/tecno-lixo-1.jpg"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover" alt="Imagem da galeria">
            </div>
            <div class="col-md-4">
                <img class="p-4 clickable-image" src="/img/tecno-lixo-4.jpg"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover" alt="Imagem da galeria">
            </div>
            <div class="col-md-4">
                <img class="p-4 clickable-image" src="/img/tecno-lixo-5.jpg"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover" alt="Imagem da galeria">
            </div>
        </div>
    </div>

    @include('image-modal.img-modal')
@endsection
