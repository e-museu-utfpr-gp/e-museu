@extends('layouts.app')
@section('title', 'Explorar Itens')

@section('content')
    <div class="container main-container mb-auto">
        @if (request()->query('section') == '')
            <h1>Todos os itens</h1>
        @else
            <h1>Itens em {{ $sectionName }}</h1>
        @endif
        <div class="row">
            <div class="col-md-2 d-none d-md-block">
                @include('items.filter-menu')
            </div>
            <div class="col-2 d-block d-md-none">
                <button class="btn btn-primary d-md-none toggle-filter-button-mobile py-2" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                    <i class="bi bi-funnel-fill h3"></i>
                </button>

                <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel"
                    style="overflow-y: scroll;">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="sidebarLabel">Filtros</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        @include('items.filter-menu')
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="content row">
                    @foreach ($items as $item)
                        <div class="col pb-5 d-flex justify-content-center">
                            <a href={{ route('items.show', $item->id) }} class="card d-flex card-anim"
                                style="width: 18rem; height: 28rem;">
                                <img src="{{ url("storage/{$item->image}") }}" class="card-img-top p-1"
                                    style="height: 12rem; object-fit: cover;" alt="Imagem do item">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold border-dark">{{ Str::limit($item->name, 40) }}</h6>
                                    <p class="border-dark">{{ $item->identification_code }}</p>
                                    <div class="division-line my-1"></div>
                                    <div class="d-flex justify-content-between pt-1">
                                        <p class="card-subtitle border- fw-bold">{{ $item->section->name }}</p>
                                        @if (\Carbon\Carbon::parse($item->date)->format('Y') != '0001')
                                            <p class="card-subtitle">{{ date('d/m/Y', strtotime($item->date)) }}</p>
                                        @else
                                            <p class="card-subtitle">Desconhecida</p>
                                        @endif
                                    </div>
                                    <div class="division-line my-1"></div>
                                    <p class="card-text">{{ Str::limit($item->description, 100) }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                @if (!$items->first())
                    <h4 class="fw-bold">Nenhum item foi encontrado.</h4>
                @endif
            </div>
        </div>
        <div class="mx-5">
            {{ $items->links('pagination::bootstrap-5') }}
        </div>
    </div>
    <script src="{{ asset('script/assistentDialogues/indexDialogue.js') }}"></script>
@endsection
