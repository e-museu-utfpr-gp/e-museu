@extends('layouts.admin')
@section('title', 'Item ' . $item->id)

@section('content')
    <div class="mb-auto container-fluid">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <h2 class="card-header">Mostrando Item: {{ $item->id }} - {{ $item->name }}</h2>
                    <div class="card-body d-flex">
                        <a href="{{ route('admin.items.edit', $item->id) }}" type="button" class="btn btn-warning me-1"><i
                                class="bi bi-pencil-fill"></i> Editar</a>
                        <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button id="deleteItemButton" type="submit" class="btn btn-danger"><i
                                    class="bi bi-trash-fill"></i> Excluir
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">Id</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $item->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Nome</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $item->name }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Imagem</h5>
                            <div class="card-body">
                                <img src="{{ url("storage/{$item->image}") }}" class="img-thumbnail clickable-image myImg"
                                    alt="Imagem do item">
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Descrição</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $item->description }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Detalhe</h5>
                            <div class="card-body">
                                <p class="card-text">{!! nl2br($item->detail) !!}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Data</h5>
                            <div class="card-body">
                                <p class="card-text">{{ date('d-m-Y', strtotime($item->date)) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">Código de Identificação</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $item->identification_code }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Validado</h5>
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($item->validation == 1)
                                        Sim
                                    @else
                                        Não
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Criado em</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($item->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Atualizado em</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($item->updated_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Categoria do Item</h5>
                            <div class="card-body">
                                <strong>Id: </strong>
                                <p class="ms-3">{{ $item->section->id }}</p>
                                <strong>Nome: </strong>
                                <p class="card-text">{{ $item->section->name }}</p>
                                <strong>Criado em: </strong>
                                <p class="ms-2">{{ date('d-m-Y', strtotime($item->created_at)) }}</p>
                                <strong>Atualizado em: </strong>
                                <p class="ms-2">{{ date('d-m-Y', strtotime($item->updated_at)) }}</p>
                                <div class="d-flex">
                                    <a href="{{ route('admin.sections.show', $item->section->id) }}" type="button"
                                        class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i> Visualizar</a>
                                    <a href="{{ route('admin.sections.edit', $item->section->id) }}" type="button"
                                        class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> Editar</a>
                                    <form action="{{ route('admin.sections.destroy', $item->section->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button id="deleteSectionButton" class="deleteSectionButton btn btn-danger"
                                            type="submit"><i class="bi bi-trash-fill"></i> Excluir
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Colaborador</h5>
                            <div class="card-body">
                                <strong>Id: </strong>
                                <p class="ms-3">{{ $item->proprietary->id }}</p>
                                <strong>Nome completo: </strong>
                                <p class="ms-3">{{ $item->proprietary->full_name }}</p>
                                <strong>Contato: </strong>
                                <p class="ms-3">{{ $item->proprietary->contact }}</p>
                                <strong>Bloqueado: </strong>
                                <p class="ms-3">
                                    @if ($item->proprietary->blocked == 1)
                                        Sim
                                    @else
                                        Não
                                    @endif
                                </p>
                                <strong>Criado em: </strong>
                                <p class="ms-3">{{ date('d-m-Y', strtotime($item->proprietary->created_at)) }}</p>
                                <strong>Atualizado em: </strong>
                                <p class="ms-3">{{ date('d-m-Y', strtotime($item->proprietary->updated_at)) }}</p>
                                <div class="d-flex">
                                    <a href="{{ route('admin.proprietaries.show', $item->proprietary->id) }}"
                                        type="button" class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i>
                                        Visualizar</a>
                                    <a href="{{ route('admin.proprietaries.edit', $item->proprietary->id) }}"
                                        type="button" class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i>
                                        Editar</a>
                                    <form action="{{ route('admin.proprietaries.destroy', $item->proprietary->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button id="deleteProprietaryButton"
                                            class="deleteProprietaryButton btn btn-danger" type="submit"><i
                                                class="bi bi-trash-fill"></i> Excluir
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <h5 class="card-header">História</h5>
                    <div class="card-body">
                        <p>{{ $item->history }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <h5 class="card-header d-flex justify-content-between">Informações Extra <a type="button"
                                    class="btn btn-success"
                                    href="{{ route('admin.extras.create', ['id' => $item->id, 'section' => $item->section]) }}"><i
                                        class="bi bi-plus-circle"></i> Adicionar Extra</a></h5>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach ($item->extras as $extra)
                                        <li class="list-group-item">
                                            <strong>Id: </strong>
                                            <p class="ms-3">{{ $extra->id }}</p>
                                            <strong>Curiosidade: </strong>
                                            <p class="ms-3">{{ Str::limit($extra->info, 500) }}</p>
                                            <strong>validado: </strong>
                                            <p class="ms-3">
                                                @if ($extra->validation == 1)
                                                    Sim
                                                @else
                                                    Não
                                                @endif
                                            </p>
                                            <strong>Item: </strong>
                                            <p class="ms-3">{{ $extra->item->name }}</p>
                                            <strong>Colaborador: </strong>
                                            <p class="ms-3">{{ $extra->proprietary->full_name }}</p>
                                            <strong>Criado em: </strong>
                                            <p class="ms-3">{{ date('d-m-Y', strtotime($extra->created_at)) }}</p>
                                            <strong>Atualizado em: </strong>
                                            <p class="ms-3">{{ date('d-m-Y', strtotime($extra->updated_at)) }}</p>
                                            <div class="d-flex">
                                                <a href="{{ route('admin.extras.show', $extra->id) }}" type="button"
                                                    class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i>
                                                    Visualizar</a>
                                                <a href="{{ route('admin.extras.edit', $extra->id) }}" type="button"
                                                    class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i>
                                                    Editar</a>
                                                <form action="{{ route('admin.extras.destroy', $extra->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="deleteExtraButton btn btn-danger" type="submit"><i
                                                            class="bi bi-trash-fill"></i> Excluir
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header d-flex justify-content-between">Etiquetas Relacionadas<a type="button"
                                    class="btn btn-success"
                                    href="{{ route('admin.item-tags.create', ['id' => $item->id, 'section' => $item->section]) }}"><i
                                        class="bi bi-plus-circle"></i> Adicionar Etiqueta</a></h5>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach ($item->tagItems as $tagItem)
                                        <li class="list-group-item">
                                            <strong>Id: </strong>
                                            <p class="ms-3">{{ $tagItem->id }}</p>
                                            <strong>Item: </strong>
                                            <p class="ms-3">{{ $tagItem->item->name }}</p>
                                            <strong>Etiqueta: </strong>
                                            <p class="ms-3">{{ $tagItem->Tag->name }}</p>
                                            <strong>Validado: </strong>
                                            <p class="ms-3">
                                                @if ($tagItem->validation == 1)
                                                    Sim
                                                @else
                                                    Não
                                                @endif
                                            </p>
                                            <strong>Criado em: </strong>
                                            <p class="ms-3">{{ date('d-m-Y H:i:s', strtotime($tagItem->created_at)) }}
                                            </p>
                                            <strong>Atualizado em: </strong>
                                            <p class="ms-3">{{ date('d-m-Y H:i:s', strtotime($tagItem->updated_at)) }}
                                            </p>
                                            <div class="d-flex">
                                                <a href="{{ route('admin.item-tags.show', $tagItem->id) }}"
                                                    type="button" class="btn btn-primary me-1"><i
                                                        class="bi bi-eye-fill"></i> Visualizar</a>
                                                <form action="{{ route('admin.item-tags.update', $tagItem->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-warning me-1"><i
                                                            class="bi bi-check2-circle"></i> Validar / Invalidar</a>
                                                </form>
                                                <form action="{{ route('admin.item-tags.destroy', $tagItem->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="deleteItemTagButton btn btn-danger" type="submit"><i
                                                            class="bi bi-trash-fill"></i> Excluir
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header d-flex justify-content-between">Componentes Relacionados<a
                                    type="button" class="btn btn-success"
                                    href="{{ route('admin.components.create', ['id' => $item->id, 'section' => $item->section]) }}"><i
                                        class="bi bi-plus-circle"></i> Adicionar Componente</a></h5>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach ($item->itemComponents as $itemComponent)
                                        <li class="list-group-item">
                                            <strong>Id: </strong>
                                            <p class="ms-3">{{ $itemComponent->id }}</p>
                                            <strong>Item principal: </strong>
                                            <p class="ms-3">{{ $itemComponent->item->name }}</p>
                                            <strong>Componente: </strong>
                                            <p class="ms-3">{{ $itemComponent->component->name }}</p>
                                            <strong>Validado: </strong>
                                            <p class="ms-3">
                                                @if ($itemComponent->validation == 1)
                                                    Sim
                                                @else
                                                    Não
                                                @endif
                                            </p>
                                            <strong>Criado em: </strong>
                                            <p class="ms-3">
                                                {{ date('d-m-Y H:i:s', strtotime($itemComponent->created_at)) }}</p>
                                            <strong>Atualizado em: </strong>
                                            <p class="ms-3">
                                                {{ date('d-m-Y H:i:s', strtotime($itemComponent->updated_at)) }}</p>
                                            <div class="d-flex">
                                                <a href="{{ route('admin.components.show', $itemComponent->id) }}"
                                                    type="button" class="btn btn-primary me-1"><i
                                                        class="bi bi-eye-fill"></i> Visualizar</a>
                                                <form action="{{ route('admin.components.update', $itemComponent->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-warning me-1"><i
                                                            class="bi bi-check2-circle h6"></i> Validar / Invalidar</a>
                                                </form>
                                                <form action="{{ route('admin.components.destroy', $itemComponent->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="deleteComponentButton btn btn-danger" type="submit"><i
                                                            class="bi bi-trash-fill"></i> Excluir
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('image-modal.img-modal')


@endsection
