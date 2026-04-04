@props([
    'createHref',
    'createLabel',
    'searchAction',
    'searchOptions',
    'searchPlaceholder',
    'booleanColumns' => [],
])

<nav {{ $attributes->class(['navbar', 'navbar-light', 'bg-light', 'admin-index-toolbar']) }}>
    <div class="container-fluid d-flex flex-nowrap align-items-center gap-2">
        <x-ui.buttons.default href="{{ $createHref }}" variant="success" icon="bi bi-plus-circle" class="flex-shrink-0">
            {{ $createLabel }}
        </x-ui.buttons.default>
        <x-admin.search-form
            :action="$searchAction"
            :options="$searchOptions"
            :placeholder="$searchPlaceholder"
            :buttonLabel="__('view.shared.buttons.search')"
            :booleanColumns="$booleanColumns"
        />
    </div>
</nav>
