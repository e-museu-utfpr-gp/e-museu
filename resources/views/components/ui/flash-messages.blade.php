@props([
    'variant' => 'admin',
])

@if (session('success'))
    @if ($variant === 'app')
        <div class="success-div text-wrap fw-bold m-1 p-1">
            {{ session('success') }}
        </div>
    @else
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
@endif

@foreach ($errors->all() as $error)
    @if ($variant === 'app')
        <p class="error-div text-wrap fw-bold m-1 mb-4 p-1"><i class="bi bi-exclamation-circle-fill mx-1 h5"></i>
            {{ $error }}</p>
    @else
        <div class="alert alert-danger" role="alert">
            {{ $error }}
        </div>
    @endif
@endforeach
