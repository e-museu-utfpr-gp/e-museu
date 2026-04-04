@props([
    'action',
    'columns' => [],
])

<div class="row">
    <div class="col min-w-0">
        <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <form action="{{ $action }}" method="GET">
                    <tr>
                        @foreach ($columns as $column)
                            <th scope="col">
                                @if (filled($column['sort'] ?? null))
                                    <x-ui.buttons.submit variant="ghost" name="sort" value="{{ $column['sort'] }}">{{ $column['label'] }}</x-ui.buttons.submit>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                    <input type="hidden" name="order"
                        value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif">
                    <input type="hidden" name="search_column" value="{{ request()->query('search_column') }}">
                    <input type="hidden" name="search" value="{{ request()->query('search') }}">
                </form>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
        </div>
    </div>
</div>
