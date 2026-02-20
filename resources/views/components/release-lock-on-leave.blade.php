@props(['type', 'id'])
@if (isset($type) && isset($id))
<script>
(function() {
    var formSubmitted = false;
    var url = @json(route('admin.release-lock'));
    var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    var type = @json($type);
    var id = @json($id);

    document.querySelector('form')?.addEventListener('submit', function() {
        formSubmitted = true;
    });

    function releaseLock() {
        if (formSubmitted || !token) return;
        var data = new FormData();
        data.append('_token', token);
        data.append('type', type);
        data.append('id', String(id));
        if (navigator.sendBeacon) {
            navigator.sendBeacon(url, data);
        } else {
            fetch(url, {
                method: 'POST',
                body: data,
                keepalive: true
            }).catch(function() {});
        }
    }

    window.addEventListener('pagehide', releaseLock);
    window.addEventListener('beforeunload', releaseLock);
})();
</script>
@endif
