@extends('layouts.admin')
@section('title', 'Criar componente')

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.components.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">Relacionar Item e Componente</h2>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="section_id" class="form-label">Categoria do Item</label>
                                <select class="form-select @error('section_id') is-invalid @enderror" id="section_id"
                                    name="section_id">
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('section_id', request()->query('section')) == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}</option>
                                    @endforeach
                                </select>
                                @error('section_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item_id" class="form-label">Item</label>
                                <select class="form-select @error('item_id') is-invalid @enderror" id="item_id"
                                    name="item_id">
                                    <option value="">-</option>
                                </select>
                                @error('item_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="component_section_id" class="form-label">Categoria do Componente</label>
                                <select class="form-select @error('component_section_id') is-invalid @enderror"
                                    id="component_section_id" name="component_section_id">
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('component_section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}</option>
                                    @endforeach
                                </select>
                                @error('component_section_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="component_id" class="form-label">Componente</label>
                                <select class="form-select @error('component_id') is-invalid @enderror" id="component_id"
                                    name="component_id">
                                    <option value="">-</option>
                                </select>
                                @error('component_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="validation" class="form-label">Validado</label>
                        <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                            name="validation">
                            <option value="0" {{ old('validation') == 0 ? 'selected' : '' }}>Não</option>
                            <option value="1" {{ old('validation') == 1 ? 'selected' : '' }}>Sim</option>
                        </select>
                        @error('validation')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> Adicionar
                            Componente</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            function getItems() {
                var sectionId = $('#section_id').val();
                $.ajax({
                    url: "/get-items",
                    type: "GET",
                    data: {
                        section: sectionId
                    },
                    success: function(data) {
                        $('#item_id').empty();
                        $.each(data, function(index, item) {
                            $('#item_id').append('<option value="' + item.id + '">' + item
                                .name + '</option>');
                        });
                        @if (request()->has('id'))
                            selectOriginalItem();
                        @endif
                    }
                });
            }

            getItems();

            $('#section_id').on('change', function() {
                getItems();
            });

            function selectOriginalItem() {
                var originalItemId = {{ request()->query('id') }}
                $('#item_id').val(originalItemId);
            }
        });
    </script>

@endsection
