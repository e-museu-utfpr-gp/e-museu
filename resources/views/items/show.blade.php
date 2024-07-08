@extends('layouts.app')
@section('title', $item->name)

@php
    $hasSeries = $item->tagItems
        ->filter(function ($tagItem) {
            return $tagItem->tag->category->name == 'Série' && $tagItem->validation == true;
        })
        ->isEmpty();

    $hasComponents = $item->ItemComponents
        ->filter(function ($itemComponent) {
            return $itemComponent->validation == true && $itemComponent->component->validation == true;
        })
        ->isEmpty();

    $hasExtras = $item->extras
        ->filter(function ($extra) {
            return $extra->validation == true;
        })
        ->isEmpty();
@endphp

@section('content')
    <div class="container main-container mb-auto">
        @if (session('success'))
            <div class="success-div text-wrap fw-bold m-1 p-1">
                {{ session('success') }}
            </div>
        @endif
        @foreach ($errors->all() as $error)
            <p class="error-div text-wrap fw-bold m-1 mb-4 p-1"><i class="bi bi-exclamation-circle-fill mx-1 h5"></i>
                {{ $error }}</p>
        @endforeach
        <div class="row">
            <div class="col-md-4 order-md-2">
                <div>
                    <a class="nav-link py-3 fw-bold explore-button px-2" href="" data-bs-toggle="modal"
                        data-bs-target="#addExtraModal">
                        <i class="bi bi-patch-plus-fill h4 me-2"></i>Enviar Informação Extra
                    </a>
                </div>
                <div class="card my-2">
                    <div>
                        <h5>{{ $item->name }}</h5>
                    </div>
                    <div style="overflow:hidden;">
                        <img src="{{ url("storage/{$item->image}") }}" class="card-img-top p-1 clickable-image"
                            style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover"
                            alt="Imagem do item">
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <p class="fw-bold">Código de Identificação</p>
                            </div>
                            <div class="col-md-7">
                                <p>{{ $item->identification_code }}</p>
                            </div>
                            <div class="col-md-5">
                                <p class="fw-bold">Categoria</p>
                            </div>
                            <div class="col-md-7">
                                <a href={{ route('items.index', ['section' => $item->section->id]) }}>
                                    <p class="show-item-link">{{ $item->section->name }}</p>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <p class="fw-bold">Data</p>
                            </div>
                            <div class="col-md-7">
                                @if (\Carbon\Carbon::parse($item->date)->format('Y') != '0001')
                                    <p>{{ date('d/m/Y', strtotime($item->date)) }}</p>
                                @else
                                    <p>Desconhecida</p>
                                @endif
                            </div>
                        </div>
                        @foreach ($item->tagItems as $tagItem)
                            @if ($tagItem->validation == true && $tagItem->tag->validation == true)
                                <div class="row">
                                    <div class="col-md-5">
                                        <p class="fw-bold">{{ $tagItem->tag->category->name }}</p>
                                    </div>
                                    <div class="col-md-7">
                                        <a href={{ route('items.index', ['tag[]' => $tagItem->tag->id]) }}>
                                            <p class="show-item-link">{{ $tagItem->tag->name }}</p>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div>
                        <h5>Detalhes Técnicos</h5>
                    </div>
                    <div class="card-body">
                        <p>{!! nl2br($item->detail) !!}</p>
                    </div>
                    <div>
                        <h5>Componentes</h5>
                    </div>
                    <div class="card-body row m-1">
                        @foreach ($item->ItemComponents as $ItemComponent)
                            @if ($ItemComponent->validation == true && $ItemComponent->component->validation == true)
                                <div class="col-6 p-2">
                                    <a href={{ route('items.show', $ItemComponent->component->id) }}>
                                        <div class="card-anim component-button p-1">
                                            <div class="">
                                                <img src="{{ url("storage/{$ItemComponent->component->image}") }}"
                                                    class="component-img p-1" alt="Imagem do componente">
                                            </div>
                                            <div class="p-1">
                                                <p class="mb-1 fw-bold">
                                                    {{ Str::limit($ItemComponent->component->name, 30) }}</p>
                                                <p class="mb-0">
                                                    {{ Str::limit($ItemComponent->component->section->name) }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                        @if ($hasComponents)
                            <div class="m-4">
                                <strong>No momento este item não apresenta componentes.</strong>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h5>Créditos</h5>
                    </div>
                    <div class="row m-1">
                        <div class="col-md-5">
                            <p class="fw-bold">Adicionado por</p>
                        </div>
                        <div class="col-md-7">
                            <p>{{ $item->proprietary->full_name }}</p>
                        </div>
                        @if ($item->proprietary->is_admin)
                        <small class="fw-bold show-item-link">Acervo físico da {{$item->proprietary->full_name}}</small>
                    @endif
                    </div>
                    @if (!$hasExtras)
                        <div class="dropdown">
                            <a type="button" class="nav-link py-3 fw-bold dropdown-toggle" data-bs-toggle="collapse"
                                data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                <h5>Colaboradores <i class="bi bi-caret-down-fill"></i></h5>
                            </a>
                            <div class="collapse" id="collapseExample">
                                <ul class="list-group p-2">
                                    <div class="card-body">
                                        @foreach ($item->extras as $extra)
                                            @if ($extra->validation == true)
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <p class="fw-bold">Colaborador</p>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <p>{{ $extra->proprietary->full_name }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-8 order-md-1">
                <h1>{{ $item->name }}</h1>
                <div class="m-4">
                    <p class="fw-bold">Código de Identificação: {{ $item->identification_code }}<p>
                    <p>{{ $item->description }}</p>
                </div>
                <h3>História</h3>
                <div class="m-4">
                    @if ($item->history == null)
                        <div>
                            <strong>No momento este item não apresenta história. Nos ajude enviando uma informação extra!</strong>
                        </div>
                    @else
                        {!! nl2br($item->history) !!}
                    @endif
                </div>
                <h3>Linhas do Tempo</h3>
                @foreach ($item->tagItems as $tagItem)
                    @if ($tagItem->validation == true && $tagItem->tag->validation == true)
                        @if ($tagItem->tag->category->name == 'Série')
                            <div class="mx-4 my-5">
                                <h4 class="mb-4 fw-bold">{{ $tagItem->tag->name }}</h4>
                                <div class="timeline m-2 px-2">
                                    @foreach ($tagItem->tag->items->sortBy('date') as $timelineItem)
                                        @if ($timelineItem->validation == 1)
                                            <div class="my-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="timeline-circle me-2"></div>
                                                    @if (\Carbon\Carbon::parse($timelineItem->date)->format('Y') != '0001')
                                                        <h6 class="fw-bold timeline-item-date">
                                                            {{ date('d/m/Y', strtotime($timelineItem->date)) }}</h6>
                                                    @else
                                                        <h6 class="fw-bold timeline-item-date">Data Desconhecida</h6>
                                                    @endif
                                                </div>
                                                <div class="d-md-flex">
                                                    <div>
                                                        <img src="{{ url("storage/{$timelineItem->image}") }}"
                                                            class="card-img-top p-1 clickable-image"
                                                            style="width: 12rem; height: 12rem; object-fit: cover"
                                                            alt="Imagem do item da série">
                                                    </div>
                                                    <div class="ms-2">
                                                        <p class="fw-bold">{{ $timelineItem->name }}</p>
                                                        <p>{{ $timelineItem->description }}</p>
                                                        <a href={{ route('items.show', $timelineItem->id) }}>
                                                            <h6 class="me-4 d-flex justify-content-end card-more-details">
                                                                MAIS DETALHES ></h6>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach
                @if ($hasSeries)
                    <div class="m-4">
                        <strong>No momento este item não pertence a uma série.</strong>
                    </div>
                @endif
                <h3>Informações Extra</h3>
                @if ($item->extras->isNotEmpty() && $item->extras->contains('validation', '1'))
                    @foreach ($item->extras as $extra)
                        @if ($extra->validation == '1')
                            <div class="m-4">
                                <p>{{ $extra->info }}</p>
                                <div class="row">
                                    <p class="fw-bold col-2">Adicionado por </p>
                                    <p class="col-10">{{ $extra->proprietary->full_name }}</p>
                                </div>
                                <div class="division-line my-1"></div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="m-4">
                        <strong>Nenhuma Informação extra validada está disponível para este item. Nos ajude adicionando
                            uma!</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('image-modal.img-modal')
    @include('items.show-modals.extra-modal')

    <script>
        let checkContactRoute = "{{ route('check-contact') }}";
    </script>

    <script src="{{ asset('script/img-modal.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/img-modal.css') }}">
    <script src="{{ asset('script/popOverButton.js') }}"></script>
    <script src="{{ asset('script/assistentDialogues/showDialogue.js') }}"></script>
@endsection
