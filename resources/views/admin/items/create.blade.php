@extends('layouts.admin')
@section('title', 'Criar item')

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">Adicionar Item</h2>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome*</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição*</label>
                        <textarea type="text" class="form-control @error('description') is-invalid @enderror" id="description"
                            name="description" rows="5">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="detail" class="form-label">Detalhe</label>
                        <textarea type="text" class="form-control @error('detail') is-invalid @enderror" id="detail" name="detail"
                            rows="7">{{ old('detail') }}</textarea>
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
                                    <option selected="selected" value="">-</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}
                                        </option>
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
                                    <option selected="selected" value="">-</option>
                                    @foreach ($proprietaries as $proprietary)
                                        <option value="{{ $proprietary->id }}"
                                            {{ old('proprietary_id') == $proprietary->id ? 'selected' : '' }}>
                                            {{ $proprietary->contact }} - {{ $proprietary->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('proprietary_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Data</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                    id="date" name="date" value="{{ old('date') }}">
                                @error('date')
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
                            </div>
                            <div class="mb-3">
                                <label for="validation" class="form-label">Validado*</label>
                                <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                                    name="validation">
                                    <option value="0" {{ old('validation') == 0 ? 'selected' : '' }}>Não</option>
                                    <option value="1" {{ old('validation') == 1 ? 'selected' : '' }}>Sim</option>
                                </select>
                                @error('validation')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="history" class="form-label">História</label>
                        <textarea type="text" class="form-control @error('history') is-invalid @enderror" id="history" name="history"
                            rows="46">{{ old('history') }}</textarea>
                        @error('history')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> Adicionar
                            Item</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
