@extends('layouts.app')
@section('title', 'Registrar Item')

@section('content')
    <div class="container main-container mb-auto">
        <h1>Colabore com um item</h1>
        <p class="ms-4 fw-bold">Possui algum item que gostaria de expor no nosso museu? Por favor, sinta se livre para
            cadastra-lo por aqui. Para quaiquer dúvidas temos a assistente virtual, ou se prefirir, entre em contato através
            do email emuseuvirtual@gmail.com.</p>
        <div class="ms-4 mb-4">
            @foreach ($errors->all() as $error)
                <p class="error-div text-wrap fw-bold m-1 p-1"><i class="bi bi-exclamation-circle-fill mx-1 h5"></i>
                    {{ $error }}</p>
            @endforeach
            @if (session('success'))
                <div class="success-div text-wrap fw-bold m-1 p-1">
                    {{ session('success') }}
                </div>
            @endif
        </div>
        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div>
                        <label for="name">
                            <h5>Nome do Item*
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="O nome oficial do item, se disponível. Caso não tenha essa informação, um nome descritivo também é válido.">
                                    <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div">
                            <input class="form-control me-2 input-form  @error('name') is-invalid @enderror" type="text"
                                name="name" id="name" autocomplete="off" placeholder="" value="{{ old('name') }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="section_id">
                                <h5>Categoria*
                                    <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                        data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                        data-bs-content="De qual categoria, o item a ser cadastrado faz parte?">
                                        <i class="bi bi-info-circle-fill h4 ms-1"
                                            style="color: #ED6E38; cursor: pointer;"></i>
                                    </button>
                                </h5>
                            </label>
                            <div class="input-div rounded-top">
                                <select required class="form-select me-2 input-form  @error('section') is-invalid @enderror"
                                    name="section_id" id="section_id">
                                    <option selected="selected" value="">-</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="date">
                                <h5>Data de Lançamento
                                    <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                        data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                        data-bs-content="Data em que o item foi lançado e disponibilizado para uso. Ao não ter certeza sobre o dia e mês exatos, pode-se inserir dia 01 e mês 01.">
                                        <i class="bi bi-info-circle-fill h4 ms-1"
                                            style="color: #ED6E38; cursor: pointer;"></i>
                                    </button>
                                </h5>
                            </label>
                            <div class="input-div">
                                <input class="form-control me-2 input-form  @error('date') is-invalid @enderror"
                                    type="date" name="date" placeholder="" value="{{ old('date') }}">
                                @error('date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="description">
                            <h5>Descrição Breve*
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="Forneça uma breve descrição do item, abordando sua função e funcionamento, se relevante.">
                                    <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div">
                            <textarea class="form-control me-2 input-form  @error('description') is-invalid @enderror" type="text"
                                name="description" placeholder="" rows="6" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="detail">
                            <h5>Detalhes Técnicos
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="Por favor, forneça detalhes técnicos mais aprofundados sobre o item a ser cadastrado, incluindo informações sobre sua aparência física e outras características técnicas relevantes.">
                                    <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div">
                            <textarea class="form-control me-2 input-form  @error('detail') is-invalid @enderror" type="text" name="detail"
                                placeholder="" rows="6">{{ old('detail') }}</textarea>
                            @error('detail')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="history">
                            <h5>História
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="Gostaríamos de conhecer a história por trás deste item. Quais foram os motivos ou circunstâncias que levaram à sua criação?">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div">
                            <textarea class="form-control me-2 input-form  @error('history') is-invalid @enderror" type="text" name="history"
                                placeholder="" rows="24">{{ old('history') }}</textarea>
                            @error('history')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="image">
                            <h5>Imagem*
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="Uma imagem do item, nítida e representativa do item sendo cadastrado.">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div nav-link">
                            <input
                                class="form-control me-2 image-form input-form p-2  @error('image') is-invalid @enderror"
                                type="file" name="image" placeholder="" required>
                            @error('image')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="e-mail">
                            <h5>E-mail*
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="Por favor forneça-nos seu e-mail para que possamos entrar em contato no futuro, caso seja necessário.">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div">
                            <input class="form-control me-2 input-form  @error('contact') is-invalid @enderror"
                                type="email" name="contact" id="contact" placeholder=""
                                value="{{ old('contact') }}" onchange="checkContact()" onkeyup="checkContact()"
                                required>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="warning-div px-1 mx-5 mb-3" id="contact-warning" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>Colaborador não encontrado! Adicionaremos
                            aos nossos registros para futuras contribuições.
                        </div>
                        <div class="success-div px-1 mx-5 mb-3" id="contact-success" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>Colaborador encontrado. Bem vindo de
                            volta!
                        </div>
                    </div>
                    <div>
                        <label for="full_name">
                            <h5>Nome Completo*
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="Por favor, informe seu nome completo. Precisamos dessas informações para que possamos creditá-lo quando seu item for disponibilizado para exibição em nosso museu.">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div">
                            <input class="form-control me-2 input-form  @error('full_name') is-invalid @enderror"
                                type="text" name="full_name" id="full_name" placeholder=""
                                value="{{ old('full_name') }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <div class="d-flex justify-content-between">
                            <h5>Etiquetas
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="As etiquetas servem para identificarmos com mais facilidade o seu item. Marca é um dos exemplos de etiqueta disponíveis.">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                            <h4 class="me-2" id="tag-count-text">0/10</h4>
                        </div>
                        <div class="tagContainer mb-4">
                            <div class="tags ms-3" id="tags">
                                <p class="text-center p-1 empty-text" id="tag-empty-text">Este item ainda não apresenta
                                    etiquetas.</p>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <div class="warning-div px-1 mx-5 mb-3" id="tag-full-text" hidden>
                                    <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>Limite de etiquetas atingido.
                                </div>
                                <button class="button nav-link px-2 pb-2" data-bs-toggle="modal"
                                    data-bs-target="#addTagModal" id="add-tag-button" type="button">
                                    <i class="bi bi-plus h3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between">
                            <h5>Informação Extra
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="Possui alguma curiosidade sobre o item a ser cadastrado que gostaria de compartilhar?">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                            <h4 class="me-2" id="extra-count-text">0/10</h4>
                        </div>
                        <div class="extraContainer mb-4">
                            <div class="extras ms-3" id="extras">
                                <p class="text-center p-1 empty-text" id="extra-empty-text">Este item ainda não apresenta
                                    informações extra.</p>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <div class="warning-div px-1 mx-5 mb-3" id="extra-full-text" hidden>
                                    <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>Limite de curiosidades atingido.
                                </div>
                                <button class="button nav-link px-2 pb-2" data-bs-toggle="modal"
                                    data-bs-target="#addExtraModal" id="add-extra-button" type="button">
                                    <i class="bi bi-plus h3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between">
                            <h5>Componentes
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="O item a ser cadastrado é composto por outros componentes? Se você tiver essa informação, por favor, sinta-se à vontade para nos informar.">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                            <h4 class="me-2" id="component-count-text">0/10</h4>
                        </div>
                        <div class="componentContainer mb-4">
                            <div class="components ms-3" id="components">
                                <p class="text-center p-1 empty-text" id="component-empty-text">Este item ainda não
                                    apresenta componentes.</p>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <div class="warning-div px-1 mx-5 mb-3" id="component-full-text" hidden>
                                    <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>Limite de componentes atingido.
                                </div>
                                <button class="button nav-link px-2 pb-2" data-bs-toggle="modal"
                                    data-bs-target="#addComponentModal" type="button" id="add-component-button">
                                    <i class="bi bi-plus h3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col d-flex align-items-center justify-content-end">
                        <button class="button nav-link py-2 px-3 fw-bold" type="submit">Enviar</button>
                    </div>
                </div>
            </div>
        </form>
    </diV>

    @include('items.create-modals.component-modal')

    @include('items.create-modals.extra-modal')

    @include('items.create-modals.tag-modal')

    <script type="text/javascript">
        let checkContactRoute = "{{ route('check-contact') }}";

        $(document).ready(function() {
            @if (session()->has('success'))
                sessionStorage.clear();
            @endif

            if (sessionStorage.getItem("itemCreateForm") === null) {
                return;
            } else {
                @if (session()->has('errors'))
                    getSessionStorage();
                    return;
                @endif

                if (confirm(
                        "Deseja recuperar as etiquetas, curiosidades extras e componentes inseridos anteriormente?"
                    )) {
                    getSessionStorage();
                }
            }
            checkContact();
        });

        function checkContact() {
            $.ajax({
                type: "GET",
                url: checkContactRoute,
                data: {
                    contact: $('#contact').val()
                },
                success: function(data) {
                    if (data == false) {
                        $('#contact-warning').prop("hidden", false);
                        $('#contact-success').prop("hidden", true);
                        return;
                    } else {
                        if ($('#contact').val() != '') {
                            $('#contact-warning').prop("hidden", true);
                            $('#contact-success').prop("hidden", false);
                            $('#full_name').val(data.full_name);
                        } else {
                            $('#contact-success').prop("hidden", true);
                            $('#contact-warning').prop("hidden", true);
                        }
                        return;
                    }
                }
            });
        }

        function getSessionStorage() {
            tagCount = parseInt(sessionStorage.getItem("tagCount"));
            extraCount = parseInt(sessionStorage.getItem("extraCount"));
            componentCount = parseInt(sessionStorage.getItem("componentCount"));

            if (tagCount > 0) {
                for (let i = 0; i < tagCount; i++) {
                    let tagCategoryText = sessionStorage.getItem("tag" + tagIds + "categoryText");
                    let tagCategoryVal = sessionStorage.getItem("tag" + tagIds + "categoryVal");
                    let tagName = sessionStorage.getItem("tag" + tagIds + "name");

                    tagBuilder(tagCategoryText, tagCategoryVal, tagName, tagIds);

                    tagIds++;
                }
                checkTags();
            }

            if (extraCount > 0) {
                for (let i = 0; i < extraCount; i++) {
                    let extraInfo = sessionStorage.getItem("extra" + extraIds + "info");

                    extraBuilder(extraInfo, extraIds);

                    extraIds++;
                }
                checkExtras();
            }

            if (componentCount > 0) {
                for (let i = 0; i < componentCount; i++) {
                    let componentCategoryText = sessionStorage.getItem("component" + componentIds + "categoryText");
                    let componentCategoryVal = sessionStorage.getItem("component" + componentIds + "categoryVal");
                    let componentName = sessionStorage.getItem("component" + componentIds + "name");

                    componentBuilder(componentCategoryText, componentCategoryVal, componentName, componentIds);

                    componentIds++;
                }
                checkComponents();
            }
        }
    </script>

    <script src="{{ asset('script/popOverButton.js') }}"></script>
    <script src="{{ asset('script/assistentDialogues/createDialogue.js') }}"></script>
@endsection
