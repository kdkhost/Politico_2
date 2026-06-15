@extends('admin.layouts.master')

@section('title', 'Configuracoes - ' . config('app.name'))
@section('page_title', 'Configuracoes do Sistema')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Configuracoes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-geral" data-bs-toggle="tab" data-bs-target="#geral" type="button" role="tab">
                            <i class="fas fa-cog me-1"></i>Geral
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-contato" data-bs-toggle="tab" data-bs-target="#contato" type="button" role="tab">
                            <i class="fas fa-phone me-1"></i>Contato
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-social" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">
                            <i class="fas fa-share-alt me-1"></i>Redes Sociais
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-seo" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                            <i class="fas fa-search me-1"></i>SEO
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-tema" data-bs-toggle="tab" data-bs-target="#tema" type="button" role="tab">
                            <i class="fas fa-palette me-1"></i>Tema
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-lgpd" data-bs-toggle="tab" data-bs-target="#lgpd" type="button" role="tab">
                            <i class="fas fa-shield-alt me-1"></i>LGPD
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-seguranca" data-bs-toggle="tab" data-bs-target="#seguranca" type="button" role="tab">
                            <i class="fas fa-user-shield me-1"></i>Seguranca
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-scripts" data-bs-toggle="tab" data-bs-target="#scripts" type="button" role="tab">
                            <i class="fas fa-code me-1"></i>Scripts
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <form id="settingsForm" enctype="multipart/form-data">
                    @csrf
                    <div class="tab-content" id="settingsTabsContent">
                        <div class="tab-pane fade show active" id="geral" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_name" class="form-label">Nome do Site</label>
                                        <input type="text" id="site_name" name="site_name" class="form-control" value="{{ settings('site_name') ?? config('app.name') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_slogan" class="form-label">Slogan</label>
                                        <input type="text" id="site_slogan" name="site_slogan" class="form-control" value="{{ settings('site_slogan') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Logo</label>
                                        <input type="file" id="logo" name="logo" class="form-control" accept="image/*" data-image-size="280x120" data-upload-label="Logo do sistema" data-existing-url="{{ settings('logo') }}">
                                        @if(settings('logo'))
                                            <div class="mt-2"><img src="{{ settings('logo') }}" alt="Logo" style="max-height: 60px;"></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="admin_logo" class="form-label">Logo do Painel</label>
                                        <input type="file" id="admin_logo" name="admin_logo" class="form-control" accept="image/*" data-image-size="320x96" data-upload-label="Logo do painel administrativo" data-existing-url="{{ settings('admin_logo') }}">
                                        @if(settings('admin_logo'))
                                            <div class="mt-2"><img src="{{ settings('admin_logo') }}" alt="Logo do painel" style="max-height: 60px;"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="admin_logo_compact" class="form-label">Icone Compacto do Painel</label>
                                        <input type="file" id="admin_logo_compact" name="admin_logo_compact" class="form-control" accept="image/*" data-image-size="512x512" data-upload-label="Icone compacto do painel" data-existing-url="{{ settings('admin_logo_compact') }}">
                                        @if(settings('admin_logo_compact'))
                                            <div class="mt-2"><img src="{{ settings('admin_logo_compact') }}" alt="Icone do painel" style="max-height: 60px;"></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="favicon" class="form-label">Favicon</label>
                                        <input type="file" id="favicon" name="favicon" class="form-control" accept="image/x-icon,image/png" data-image-size="512x512" data-upload-label="Favicon" data-existing-url="{{ settings('favicon') }}">
                                        @if(settings('favicon'))
                                            <div class="mt-2"><img src="{{ settings('favicon') }}" alt="Favicon" style="max-height: 32px;"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="contato" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">E-mail de Contato</label>
                                        <input type="email" id="contact_email" name="contact_email" class="form-control" value="{{ settings('contact_email') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_phone" class="form-label">Telefone</label>
                                        <input type="text" id="contact_phone" name="contact_phone" class="form-control" value="{{ settings('contact_phone') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_address" class="form-label">Endereco</label>
                                        <input type="text" id="contact_address" name="contact_address" class="form-control" value="{{ settings('contact_address') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_whatsapp" class="form-label">WhatsApp</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                            <input type="text" id="contact_whatsapp" name="contact_whatsapp" class="form-control" value="{{ settings('contact_whatsapp') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="social" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_facebook" class="form-label"><i class="fab fa-facebook me-1"></i>Facebook</label>
                                        <input type="url" id="social_facebook" name="social_facebook" class="form-control" value="{{ settings('social_facebook') }}" placeholder="https://facebook.com/...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_instagram" class="form-label"><i class="fab fa-instagram me-1"></i>Instagram</label>
                                        <input type="url" id="social_instagram" name="social_instagram" class="form-control" value="{{ settings('social_instagram') }}" placeholder="https://instagram.com/...">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_twitter" class="form-label"><i class="fab fa-twitter me-1"></i>Twitter / X</label>
                                        <input type="url" id="social_twitter" name="social_twitter" class="form-control" value="{{ settings('social_twitter') }}" placeholder="https://twitter.com/...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_youtube" class="form-label"><i class="fab fa-youtube me-1"></i>YouTube</label>
                                        <input type="url" id="social_youtube" name="social_youtube" class="form-control" value="{{ settings('social_youtube') }}" placeholder="https://youtube.com/@...">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_linkedin" class="form-label"><i class="fab fa-linkedin me-1"></i>LinkedIn</label>
                                        <input type="url" id="social_linkedin" name="social_linkedin" class="form-control" value="{{ settings('social_linkedin') }}" placeholder="https://linkedin.com/...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="seo" role="tabpanel">
                            <div class="mb-3">
                                <label for="seo_title" class="form-label">Titulo Global (SEO)</label>
                                <input type="text" id="seo_title" name="seo_title" class="form-control" value="{{ settings('seo_title') }}" placeholder="Titulo padrao para paginas">
                            </div>
                            <div class="mb-3">
                                <label for="seo_description" class="form-label">Meta Descricao Global</label>
                                <textarea id="seo_description" name="seo_description" class="form-control" rows="3" maxlength="160" placeholder="Descricao padrao para mecanismos de busca">{{ settings('seo_description') }}</textarea>
                                <div class="form-text">Maximo de 160 caracteres. Atual: <span id="seoDescCount">{{ strlen(settings('seo_description') ?? '') }}</span></div>
                            </div>
                            <div class="mb-3">
                                <label for="seo_keywords" class="form-label">Palavras-chave Globais</label>
                                <input type="text" id="seo_keywords" name="seo_keywords" class="form-control" value="{{ settings('seo_keywords') }}" placeholder="palavra1, palavra2, palavra3">
                                <div class="form-text">Separadas por virgula.</div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tema" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="default_theme" class="form-label">Tema do Frontend</label>
                                        <select id="default_theme" name="default_theme" class="form-select">
                                            <option value="default" @selected(settings('default_theme', 'default') === 'default')>Padrao do sistema</option>
                                            <option value="premium" @selected(settings('default_theme') === 'premium')>Premium institucional</option>
                                        </select>
                                        <div class="form-text">As cores deste tema seguem a paleta primaria e secundaria definida abaixo.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="primary_color" class="form-label">Cor Primaria</label>
                                        <div class="input-group">
                                            <input type="color" id="primary_color" name="primary_color" class="form-control form-control-color" value="{{ settings('primary_color') ?? '#0d6efd' }}">
                                            <input type="text" class="form-control" id="primary_color_hex" value="{{ settings('primary_color') ?? '#0d6efd' }}" style="max-width: 100px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="secondary_color" class="form-label">Cor Secundaria</label>
                                        <div class="input-group">
                                            <input type="color" id="secondary_color" name="secondary_color" class="form-control form-control-color" value="{{ settings('secondary_color') ?? '#6c757d' }}">
                                            <input type="text" class="form-control" id="secondary_color_hex" value="{{ settings('secondary_color') ?? '#6c757d' }}" style="max-width: 100px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" id="dark_mode_default" name="dark_mode_default" class="form-check-input" value="1" {{ settings('dark_mode_default') ? 'checked' : '' }}>
                                    <label for="dark_mode_default" class="form-check-label">Modo escuro como padrao</label>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="lgpd" role="tabpanel">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" id="cookie_banner_enabled" name="cookie_banner_enabled" class="form-check-input" value="1" {{ settings('cookie_banner_enabled') ? 'checked' : '' }}>
                                    <label for="cookie_banner_enabled" class="form-check-label">Ativar banner de cookies</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="lgpd_privacy_page" class="form-label">URL da Pagina de Privacidade</label>
                                <input type="url" id="lgpd_privacy_page" name="lgpd_privacy_page" class="form-control" value="{{ settings('lgpd_privacy_page') }}" placeholder="https://...">
                            </div>
                        </div>

                        <div class="tab-pane fade" id="seguranca" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i>Configure o Google reCAPTCHA para proteger login administrativo e formularios publicos.
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="recaptcha_version" class="form-label">Versao</label>
                                        <select id="recaptcha_version" name="recaptcha_version" class="form-select">
                                            <option value="v2" @selected(settings('recaptcha_version', 'v2') === 'v2')>reCAPTCHA v2</option>
                                            <option value="v3" @selected(settings('recaptcha_version', 'v2') === 'v3')>reCAPTCHA v3</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="recaptcha_min_score" class="form-label">Score minimo v3</label>
                                        <input type="number" id="recaptcha_min_score" name="recaptcha_min_score" class="form-control" min="0.1" max="1" step="0.1" value="{{ settings('recaptcha_min_score', '0.5') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label d-block">Status</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="recaptcha_enabled" name="recaptcha_enabled" class="form-check-input" value="1" {{ settings('recaptcha_enabled') ? 'checked' : '' }}>
                                            <label for="recaptcha_enabled" class="form-check-label">Ativar reCAPTCHA</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="recaptcha_site_key" class="form-label">Site key</label>
                                        <input type="text" id="recaptcha_site_key" name="recaptcha_site_key" class="form-control" value="{{ settings('recaptcha_site_key') }}" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="recaptcha_secret_key" class="form-label">Secret key</label>
                                        <input type="password" id="recaptcha_secret_key" name="recaptcha_secret_key" class="form-control" value="" placeholder="{{ settings('recaptcha_secret_key') ? 'Chave ja configurada' : '' }}" autocomplete="new-password">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" id="recaptcha_admin_login" name="recaptcha_admin_login" class="form-check-input" value="1" {{ settings('recaptcha_admin_login') ? 'checked' : '' }}>
                                        <label for="recaptcha_admin_login" class="form-check-label">Proteger login administrativo</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" id="recaptcha_contact" name="recaptcha_contact" class="form-check-input" value="1" {{ settings('recaptcha_contact', true) ? 'checked' : '' }}>
                                        <label for="recaptcha_contact" class="form-check-label">Proteger formulario de contato</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="scripts" role="tabpanel">
                            <div class="mb-3">
                                <label for="header_scripts" class="form-label">Scripts do Cabecalho (&lt;/head&gt;)</label>
                                <textarea id="header_scripts" name="header_scripts" class="form-control font-monospace" rows="5" placeholder="Google Analytics, Facebook Pixel, etc.">{{ settings('header_scripts') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="footer_scripts" class="form-label">Scripts do Rodape (&lt;/body&gt;)</label>
                                <textarea id="footer_scripts" name="footer_scripts" class="form-control font-monospace" rows="5" placeholder="Chat widgets, tracking, etc.">{{ settings('footer_scripts') }}</textarea>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i>Estes scripts serao injetados em todas as paginas do site.
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-3 border-top pt-3">
                        <button type="submit" class="btn btn-primary" id="btnSaveSettings">
                            <i class="fas fa-save me-1"></i>Salvar Configuracoes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#seo_description').on('input', function() {
            $('#seoDescCount').text($(this).val().length);
        });

        $('#primary_color').on('input', function() {
            $('#primary_color_hex').val($(this).val());
        });
        $('#primary_color_hex').on('input', function() {
            $('#primary_color').val($(this).val());
        });
        $('#secondary_color').on('input', function() {
            $('#secondary_color_hex').val($(this).val());
        });
        $('#secondary_color_hex').on('input', function() {
            $('#secondary_color').val($(this).val());
        });

        $('#settingsForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSaveSettings');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            var formData = new FormData(this);
            $.ajax({
                url: '{{ route("admin.settings.save") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status === 'success') {
                        var savedSettings = res.data?.settings || {};

                        $.each(savedSettings, function(key, value) {
                            if (typeof value !== 'string' || value === '') {
                                return;
                            }

                            var input = $('#settingsForm').find('[name="' + key + '"]');
                            if (!input.length || input.attr('type') !== 'file') {
                                return;
                            }

                            input.attr('data-existing-url', value);
                            input[0].dataset.existingUrl = value;
                            input.val('');

                            var wrapper = input.closest('.admin-upload-enhanced');
                            if (wrapper.length && typeof window.renderAdminUploadPreview === 'function') {
                                window.renderAdminUploadPreview(wrapper, input[0]);
                            }
                        });

                        if (typeof window.applyInstantAdminBranding === 'function') {
                            window.applyInstantAdminBranding(savedSettings);
                        }

                        toastr.success(res.message || 'Configuracoes salvas com sucesso!');
                    } else {
                        toastr.error(res.message || 'Erro ao salvar configuracoes.');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    if (errors) {
                        $.each(errors, function(field, msgs) {
                            $.each(msgs, function(i, msg) {
                                toastr.error(msg);
                            });
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Erro ao salvar configuracoes.');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar Configuracoes');
                }
            });
        });
    });
</script>
@endpush
@endsection
