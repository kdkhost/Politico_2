@php($adminSiteName = settings('site_name') ?: config('app.name'))
<footer class="app-footer admin-footer">
    <div class="float-end d-none d-sm-inline">
        <strong>Versão</strong> {{ config('app.version', '1.0.0') }}
        @if(settings('license_verified'))
            <span class="badge bg-success ms-1"><i class="fas fa-check-circle"></i> Licenciado</span>
        @endif
    </div>
    <strong>&copy; {{ date('Y') }} <a href="{{ config('app.url') }}">{{ $adminSiteName }}</a>.</strong> Todos os direitos reservados.
    <br class="d-sm-none">
    <span class="d-sm-none">
        <strong>Versão</strong> {{ config('app.version', '1.0.0') }}
    </span>
</footer>
