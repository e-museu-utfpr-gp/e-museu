<div class="explore-menu-div sticky-top-2 d-none d-md-block" id="sub-menu">
    <div class="container d-flex">
        <div class="col-3 m-0">
            <label class="fw-bold" for="search">Buscar por nome</label>
            <form action="{{ route('items.index') }}" method="GET" class="d-flex">
                <input name="section" value="{{ request()->query('section') }}" hidden>
                <div class="input-div m-0 mt-1">
                    <input class="form-control input-form" type="text" name="search" id="search" placeholder="">
                </div>
                <button class="button nav-link px-3 fw-bold"><i class="h4 bi bi-search"></i></button>
            </form>
        </div>
        <div class="d-flex col-9">
            <div class="left-arrow menu-arrows px-4 h3 d-flex align-items-center">
                <i class="bi bi-chevron-left"></i>
            </div>
            <div class="d-flex explore-menu-options">
                <a href="{{ route('items.index') }}" class="explore-menu-option">
                    <div
                        class="nav-link menu-option py-4 px-4 fw-bold @if (request()->query('section') == '') menu-option-active @endif">
                        Todos
                    </div>
                </a>
                @foreach ($sections as $section)
                    <a href="{{ route('items.index', ['section' => $section->id]) }}" class="explore-menu-option">
                        <div
                            class="nav-link menu-option py-4 px-4 fw-bold @if (request()->query('section') == $section->id) menu-option-active @endif">
                            {{ $section->name }}
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="right-arrow menu-arrows px-4 h3 d-flex align-items-center">
                <i class="bi bi-chevron-right"></i>
            </div>
        </div>
    </div>
</div>


<button class="button navbar-toggler p-1 nav-link justify-content-between d-flex d-md-none py-3" type="button"
    data-bs-toggle="collapse" data-bs-target="#categoriesToggle" aria-controls="categoriesToggle" aria-expanded="false"
    aria-label="Toggle navigation">
    @if (request()->query('section') == '')
        <h4 class="ms-3 fw-bold">Todos</h4>
    @else
        @foreach ($sections as $section)
            @if ($section->id == request()->query('section'))
                <h4 class="ms-3 fw-bold">{{ $section->name }}</h4>
            @endif
        @endforeach
    @endif
    <i class="bi bi-caret-down-fill me-3"></i>
</button>
<div class="collapse flex-grow-0" id="categoriesToggle">
    <div class="explore-menu-div-mobile mt-0 sticky-top" id="sub-menu">
        <label class="fw-bold" for="search">Buscar por nome</label>
        <form action="{{ route('items.index') }}" method="GET" class="row">
            <input name="section" value="{{ request()->query('section') }}" hidden>
            <div class="input-div m-0 mt-2 col-10">
                <input class="form-control" type="text" name="search" id="search" placeholder="">
            </div>
            <button class="button nav-link mt-1 px-3 fw-bold col-2"><i class="h4 bi bi-search"></i></button>
        </form>
        <div class="division-line my-1"></div>
        <a href="{{ route('items.index', ['section' => '']) }}" class="explore-menu-option">
            <div
                class="nav-link menu-option py-4 px-4 fw-bold @if (request()->query('section') == '') menu-option-active @endif">
                Todos
            </div>
        </a>
        @foreach ($sections as $section)
            <a href="{{ route('items.index', ['section' => $section->id]) }}" class="explore-menu-option">
                <div
                    class="nav-link menu-option py-4 px-4 fw-bold @if (request()->query('section') == $section->id) menu-option-active @endif">
                    {{ $section->name }}
                </div>
            </a>
        @endforeach
    </div>
</div>




<script type="text/javascript">
    const options = document.querySelector(".explore-menu-options");
    const option = document.querySelectorAll(".explore-menu-option");

    const leftArrow = document.querySelector(".left-arrow");
    const rightArrow = document.querySelector(".right-arrow");

    $(document).ready(function() {
        options.scrollLeft = localStorage.getItem("scrollPosition");
    });

    rightArrow.addEventListener("click", () => {
        options.scrollLeft += 300;
    });

    leftArrow.addEventListener("click", () => {
        options.scrollLeft -= 300;
    });

    option.forEach((option) => {
        option.addEventListener("click", () => {
            localStorage.setItem("scrollPosition", options.scrollLeft);
        });
    });

    $(document).onBeforeUnload(function() {
        options.scrollLeft = 0;
    });
    onbeforeunload
</script>
