@props([
    'createHref',
    'createLabel',
    'searchAction',
    'searchOptions',
    'searchPlaceholder',
    'booleanColumns' => [],
])

<nav {{ $attributes->class(['navbar', 'navbar-light', 'bg-light']) }}>
    <div class="container-fluid">
        <x-ui.buttons.default href="{{ $createHref }}" variant="success" icon="bi bi-plus-circle">
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
