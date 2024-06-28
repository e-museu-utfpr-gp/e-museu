@extends('layouts.admin')
@section('title', 'Editar item ' . $item->id)

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">Editar Item: {{ $item->id }} - {{ $item->name }}</h2>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome*</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ $item->name }}">
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição*</label>
                        <textarea type="text" class="form-control @error('description') is-invalid @enderror" id="description"
                            name="description" rows="5">{{ $item->description }}</textarea>
                        @error('description')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="detail" class="form-label">Detalhe</label>
                        <textarea type="text" class="form-control @error('detail') is-invalid @enderror" id="detail" name="detail"
                            rows="7">{{ $item->detail }}</textarea>
                        @error('detail')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="section_id" class="form-label">Categoria do Item*</label>
                                <select class="form-select @error('section_id') is-invalid @enderror" id="section_id"
                                    name="section_id">
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            @if ($item->section_id == $section->id) selected @endif>{{ $section->name }}</option>
                                    @endforeach
                                </select>
                                @error('section_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="proprietary_id" class="form-label">Colaborador*</label>
                                <select class="form-select @error('proprietary_id') is-invalid @enderror"
                                    id="proprietary_id" name="proprietary_id">
                                    @foreach ($proprietaries as $proprietary)
                                        <option value="{{ $proprietary->id }}"
                                            @if ($item->proprietary_id == $proprietary->id) selected @endif>{{ $proprietary->contact }} -
                                            {{ $proprietary->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('proprietary_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Data</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                    id="date" name="date" value="{{ $item->date }}">
                                @error('date')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="identification_code" class="form-label">Código de Identificação*</label>
                                <input type="text"
                                    class="form-control @error('identification_code') is-invalid @enderror"
                                    id="identification_code" name="identification_code"
                                    value="{{ $item->identification_code }}">
                                @error('identification_code')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="validation" class="form-label">Validado*</label>
                                <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                                    name="validation">
                                    <option value="0" @if ($item->validation == 0) selected @endif>Não</option>
                                    <option value="1" @if ($item->validation == 1) selected @endif>Sim</option>
                                </select>
                                @error('validation')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Imagem*</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image">
                                @error('image')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                                <img src="{{ url("storage/{$item->image}") }}" class="img-thumbnail clickable-image"
                                    alt="Imagem anterior do item">
                                <p>Imagem anterior</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="history" class="form-label">História</label>
                        <textarea type="text" class="form-control @error('history') is-invalid @enderror" id="history" name="history"
                            rows="46">{{ $item->history }}</textarea>
                        @error('history')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-pencil-fill"></i> Enviar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @include('image-modal.img-modal')
    <script src="{{ asset('script/img-modal.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/img-modal.css') }}">
@endsection
