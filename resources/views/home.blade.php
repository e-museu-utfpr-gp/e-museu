@extends('layouts.app')
@section('title', 'Home')

@section('content')
    <div class="container-fluid headline">
        <img class="img-headline" src="/img/banner.png" alt="">
        <div class="container headline-content">
            <div class="row">
                <div class="col-md-6">
                    <p class="h1 fw-bold text-shadow">SEJA BEM-VINDO AO E-MUSEU</p>
                    <p class="h2 text-shadow">SEU MUSEU VIRTUAL DE INFORMÁTICA</p>
                    <h6 class="text-shadow">
                        Explore e descubra o que o mundo dos computadores, periféricos, componentes eletrônicos e muitos
                        outros dispositivos têm a oferecer!
                    </h6>
                </div>
            </div>
        </div>
    </div>
    <div class="container main-container mb-auto">
        <h1>SOBRE O NOSSO MUSEU</h1>
        <div class="row">
            <div class="col-md-6">
                <p class="p-4">
                    Desde muito tempo atrás, o homem, por ser curioso e ter interesse no passado, coleciona objetos que
                    fazem parte da cultura e do passado de um povo. Muitas vezes, esses objetos não possuem valor monetário,
                    mas um valor histórico imensurável. Por conta disso, foram criados os museus, locais
                    sem fins lucrativos que tem como objetivo apresentar, toda ou em partes, a história e a cultura de um
                    povo.
                    <br>
                    O objetivo do nosso projeto é de aprender sobre o passado e compreender o presente, tanto em questões
                    culturais
                    como também em questões tecnológicas, de forma a evitar erros cometidos. Nosso museu de informática
                    existe para preservar a história de diversos itens de informática, e então, mostrar para as pessoas o
                    passado dos computadores, ensinar
                    e registrar tudo o que foi criado de mais marcante até o momento atual.
                </p>
            </div>
            <div class="col-md-6 ">
                <div id="carouselExampleControls" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @php
                            $imageActive = true;
                        @endphp
                        @foreach ($items as $item)
                            <div class="carousel-item @if ($imageActive) active @endif">
                                <img src="storage/{{ $item->image }}" class="p-4 clickable-image"
                                    style="aspect-ratio: 3 / 2; width: 100%; max-height: 100%; object-fit: cover"
                                    alt="">
                            </div>
                            @php
                                $imageActive = false;
                            @endphp
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 ">
                    <img src="/img/unicentro-utfpr-logos.png" class="p-4 clickable-image"
                        style="width: 100%; max-height: 100%;" alt="">
                </div>
                <div class="col-md-6">
                    <p class="p-4">
                        A Universidade Tecnológica Federal do Paraná (UTFPR) e a Universidade Estadual do Centro-Oeste
                        (Unicentro) possuem projetos relacionados a lixo eletrônico. A <a
                            href="https://www.utfpr.edu.br">UTFPR</a>
                        possui o projeto Tecno-Lixo:
                        Oficina do
                        Aprender e a <a href="https://www3.unicentro.br">Unicentro</a> o projeto E-Lixo. Os projetos recebem
                        peças de
                        computadores que as pessoas não
                        utilizam
                        mais. Para que as informações obre essas peças não sejam perdidas, a ideia para este museu emergiu.
                        <br>
                        Nosso museu conta com itens adicionados ao acervo pelas próprias pessoas responsáveis pelo projeto,
                        e
                        também, por pessoas que tenham interesse em colaborar conosco. Você também pode fazer parte!
                        <br>
                        <br>
                        <strong>Se tiver quaisquer dúvidas em relação ao nosso site, temos uma assistente virtual que pode
                            te
                            auxiliar.
                            Apenas clique (ou toque) sobre ela para interagir com o recurso.</strong>
                        <br>
                        <br>
                        Logo abaixo disponilizamos alguns pontos de partida para começar a explorar nosso site. Boa visita!
                    </p>
                </div>
            </div>
            <h3>Opções de páginas para começar sua exploração</h3>
            <div class="row p-4">
                <div class="col-md-4 ">
                    <div class="col-md pb-1 d-flex justify-content-center">
                        <a href="{{ route('items.index') }}" class="card d-flex card-anim">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <img class="p-2" src="/img/compass.png" style="width: 7rem;" alt="...">
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="card-title fw-bold">Explorar itens do museu</h6>
                                        <p>Se deseja explorar os itens do nosso scervo virtual, esta é a opção que procura!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 ">
                    <div class="col-md pb-1 d-flex justify-content-center">
                        <a href="{{ route('items.create') }}" class="card d-flex card-anim">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-4">
                                        <img class="p-2" src="/img/form.png" style="width: 7rem;" alt="...">
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="card-title fw-bold">Cadastrar um item</h6>
                                        <p>Quer colaborar com o nosso museu enviando um item ao nosso acervo virtual?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 ">
                    <div class="col-md pb-1 d-flex justify-content-center">
                        <a href="{{ route('about') }}" class="card d-flex card-anim">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-4">
                                        <img class="p-2" src="/img/info.png" style="width: 7rem;" alt="...">
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="card-title fw-bold">Sobre o museu</h6>
                                        <p>Gostaria de saber mais sobre o nosso projeto e as entidades envolvidas na criação
                                            dele?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('image-modal.img-modal')
    <script src="{{ asset('script/img-modal.js') }}"></script>
    <script src="{{ asset('script/img-modal.js') }}"></script>
    <script src="{{ asset('script/assistentDialogues/homeDialogue.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/img-modal.css') }}">
@endsection
