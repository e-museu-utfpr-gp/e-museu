<h3>{{ __('view.home.exploration_title') }}</h3>
<div class="row p-4">
    <div class="col-md-4 ">
        <div class="col-md pb-1 d-flex justify-content-center">
            <a href="{{ route('catalog.items.index') }}" class="card d-flex card-anim">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img class="p-2" src="/img/compass.png" style="width: 7rem;" alt="...">
                        </div>
                        <div class="col-md-8">
                            <h6 class="card-title fw-bold">{{ __('view.home.cards.explore.title') }}</h6>
                            <p>{{ __('view.home.cards.explore.description') }}
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-md-4 ">
        <div class="col-md pb-1 d-flex justify-content-center">
            <a href="{{ route('catalog.items.create') }}" class="card d-flex card-anim">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-4">
                            <img class="p-2" src="/img/form.png" style="width: 7rem;" alt="...">
                        </div>
                        <div class="col-md-8">
                            <h6 class="card-title fw-bold">{{ __('view.home.cards.contribute.title') }}</h6>
                            <p>{{ __('view.home.cards.contribute.description') }}
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
                            <h6 class="card-title fw-bold">{{ __('view.home.cards.about.title') }}</h6>
                            <p>{{ __('view.home.cards.about.description') }}
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
