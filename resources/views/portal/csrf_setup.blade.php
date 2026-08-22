<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
(function () {
    var token = @json(csrf_token());
    window.csrfToken = token;

    function setupJqueryCsrf() {
        if (!window.jQuery || window.jQuery.__ndCsrfBound) return;
        window.jQuery.__ndCsrfBound = true;
        window.jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
    }

    setupJqueryCsrf();
    document.addEventListener('DOMContentLoaded', setupJqueryCsrf);
    var jqTimer = setInterval(function () {
        setupJqueryCsrf();
        if (window.jQuery && window.jQuery.__ndCsrfBound) clearInterval(jqTimer);
    }, 25);
    setTimeout(function () { clearInterval(jqTimer); }, 8000);

    var originalFetch = window.fetch;
    window.fetch = function (input, init) {
        init = init || {};
        var headers = new Headers(init.headers || {});
        if (!headers.has('X-CSRF-TOKEN') && !headers.has('X-XSRF-TOKEN')) {
            headers.set('X-CSRF-TOKEN', token);
            headers.set('X-Requested-With', 'XMLHttpRequest');
        }
        init.headers = headers;
        init.credentials = init.credentials || 'same-origin';
        return originalFetch.call(this, input, init);
    };
})();
</script>
