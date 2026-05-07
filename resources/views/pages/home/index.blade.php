<x-layouts.app :title="__('view.home.title')">
    @include('pages.home._partials.headline')

    <div class="container main-container mb-auto">
        <div class="alert alert-warning mt-3" role="alert">
            <h5 class="mb-2">Deploy debug (temporary)</h5>
            <p class="mb-2">Compare SHA12 with GitHub Actions log (`DEBUG BEARER_SHA12`).</p>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Present</th>
                            <th>Len</th>
                            <th>SHA12</th>
                            <th>Preview</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deployDebug as $key => $data)
                            <tr>
                                <td><code>{{ $key }}</code></td>
                                <td>{{ $data['present'] ? 'yes' : 'no' }}</td>
                                <td>{{ $data['len'] }}</td>
                                <td><code>{{ $data['sha12'] }}</code></td>
                                <td><code>{{ $data['preview'] }}</code></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @include('pages.home._partials.about', ['items' => $items])
        @include('pages.home._partials.exploration-cards')
    </div>

    <x-ui.image-modal />
</x-layouts.app>
