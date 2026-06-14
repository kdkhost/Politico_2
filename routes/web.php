<?php

declare(strict_types=1);

/**
 * @autor marcelo-brad rj
 * @contato Tel: +55 (21) 98132-5441
 * @contato Email: contato@kdkhost.com.br
 * @contato Telegram: @MARCELO_BRAD
 * @contato Instagram: @marcelobradrj
 * @contato WhatsApp: 5521981325441
 */

use Illuminate\Support\Facades\Route;

// ========== INSTALL ROUTES (must be before catch-all) ==========
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [App\Http\Controllers\InstallerController::class, 'index'])->name('index');
    Route::get('/requisitos', [App\Http\Controllers\InstallerController::class, 'requirements'])->name('requirements');
    Route::post('/requisitos', [App\Http\Controllers\InstallerController::class, 'checkRequirements']);
    Route::get('/banco', [App\Http\Controllers\InstallerController::class, 'database'])->name('database');
    Route::post('/banco', [App\Http\Controllers\InstallerController::class, 'configureDatabase']);
    Route::get('/migrate', [App\Http\Controllers\InstallerController::class, 'showMigrate'])->name('migrate');
    Route::post('/migrate', [App\Http\Controllers\InstallerController::class, 'runMigrate'])->name('migrate.run');
    Route::get('/admin', [App\Http\Controllers\InstallerController::class, 'admin'])->name('admin');
    Route::post('/admin', [App\Http\Controllers\InstallerController::class, 'createAdmin']);
    Route::get('/finalizar', [App\Http\Controllers\InstallerController::class, 'finish'])->name('finish');
    Route::post('/finalizar', [App\Http\Controllers\InstallerController::class, 'complete']);
});

// ========== SITE / FRONTEND ROUTES ==========
Route::name('site.')->group(function () {
    Route::get('/', [App\Http\Controllers\Site\HomeController::class, 'index'])->name('home');
    Route::get('/sitemap.xml', [App\Http\Controllers\Site\SeoController::class, 'sitemap'])->name('sitemap');
    Route::get('/robots.txt', [App\Http\Controllers\Site\SeoController::class, 'robots'])->name('robots');
    Route::get('/biografia', [App\Http\Controllers\Site\BiografiaController::class, 'index'])->name('biografia');
    Route::get('/agenda', [App\Http\Controllers\Site\AgendaController::class, 'index'])->name('agenda');
    Route::get('/agenda/eventos', [App\Http\Controllers\Site\AgendaController::class, 'eventos'])->name('agenda.eventos');

    // Blog
    Route::get('/blog', [App\Http\Controllers\Site\BlogController::class, 'index'])->name('blog');
    Route::get('/blog/{slug}', [App\Http\Controllers\Site\BlogController::class, 'show'])->name('blog.show');
    Route::get('/categoria/{slug}', [App\Http\Controllers\Site\BlogController::class, 'byCategory'])->name('blog.categoria');
    Route::get('/tag/{slug}', [App\Http\Controllers\Site\BlogController::class, 'byTag'])->name('blog.tag');

    // Noticias
    Route::get('/noticias', [App\Http\Controllers\Site\NoticiasController::class, 'index'])->name('noticias');

    // Projetos e Propostas
    Route::get('/projetos', [App\Http\Controllers\Site\ProjetosController::class, 'index'])->name('projetos');
    Route::redirect('/proposta', '/propostas', 301);
    Route::get('/propostas', [App\Http\Controllers\Site\PropostasController::class, 'index'])->name('propostas');

    // Transparencia
    Route::get('/transparencia', [App\Http\Controllers\Site\TransparenciaController::class, 'index'])->name('transparencia');
    Route::get('/transparencia/{id}', [App\Http\Controllers\Site\TransparenciaController::class, 'show'])->name('transparencia.show');

    // Equipe
    Route::get('/equipe', [App\Http\Controllers\Site\EquipeController::class, 'index'])->name('equipe');

    // Galeria e Videos
    Route::get('/galeria', [App\Http\Controllers\Site\GaleriaController::class, 'index'])->name('galeria');
    Route::get('/videos', [App\Http\Controllers\Site\VideosController::class, 'index'])->name('videos');

    // Documentos
    Route::get('/documentos', [App\Http\Controllers\Site\DocumentosController::class, 'index'])->name('documentos');

    // Contato
    Route::get('/contato', [App\Http\Controllers\Site\ContatoController::class, 'index'])->name('contato');
    Route::post('/contato/enviar', [App\Http\Controllers\Site\ContatoController::class, 'enviar'])->middleware('throttle:5,1')->name('contato.enviar');

    // Newsletter
    Route::post('/newsletter/inscrever', [App\Http\Controllers\Site\NewsletterController::class, 'inscrever'])->middleware('throttle:3,1')->name('newsletter.subscribe');
    Route::get('/newsletter/confirmar/{token}', [App\Http\Controllers\Site\NewsletterController::class, 'confirmar'])->name('newsletter.confirm');
    Route::get('/newsletter/cancelar/{token}', [App\Http\Controllers\Site\NewsletterController::class, 'cancelar'])->name('newsletter.cancel');

    // Imprensa
    Route::get('/imprensa', [App\Http\Controllers\Site\ImprensaController::class, 'index'])->name('imprensa');

    // FAQ
    Route::get('/faq', [App\Http\Controllers\Site\FaqController::class, 'index'])->name('faq');

    // Privacidade e Termos
    Route::get('/privacidade', [App\Http\Controllers\Site\PrivacidadeController::class, 'index'])->name('privacidade');
    Route::get('/termos', [App\Http\Controllers\Site\TermosController::class, 'index'])->name('termos');
    Route::get('/acessibilidade', [App\Http\Controllers\Site\AcessibilidadeController::class, 'index'])->name('acessibilidade');
});

// ========== ADMIN ROUTES ==========
Route::prefix('admin')->name('admin.')->group(function () {

    // Public license activation (no auth required)
    Route::get('/licenca', [App\Http\Controllers\Admin\LicenseController::class, 'showActivationForm'])->name('license.activate-form');
    Route::post('/licenca', [App\Http\Controllers\Admin\LicenseController::class, 'activatePublic'])->name('license.activate.public');
    Route::get('/license/activate', fn () => redirect()->route('admin.license.activate-form'))->name('license.activate-get');

    // Auth routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.submit');
        Route::get('/recuperar-senha', [App\Http\Controllers\Admin\AuthController::class, 'showForgotForm'])->name('forgot');
        Route::post('/recuperar-senha', [App\Http\Controllers\Admin\AuthController::class, 'sendResetLink'])->name('forgot.submit');
        Route::get('/resetar-senha/{token}', [App\Http\Controllers\Admin\AuthController::class, 'showResetForm'])->name('reset');
        Route::post('/resetar-senha', [App\Http\Controllers\Admin\AuthController::class, 'resetPassword'])->name('reset.submit');
    });

    // Authenticated admin routes (with license check)
    Route::middleware(['auth', 'admin', 'check.license'])->group(function () {
        Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::post('/me/avatar', [App\Http\Controllers\Admin\UserController::class, 'updateAvatar'])->name('profile.avatar');

        // Dashboard
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard.index');

        // License
        Route::prefix('license')->name('license.')->middleware('permission:license.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\LicenseController::class, 'index'])->name('index');
            Route::post('/activate', [App\Http\Controllers\Admin\LicenseController::class, 'activate'])->middleware('permission:license.edit')->name('activate');
            Route::post('/deactivate', [App\Http\Controllers\Admin\LicenseController::class, 'deactivate'])->middleware('permission:license.edit')->name('deactivate');
            Route::post('/verify', [App\Http\Controllers\Admin\LicenseController::class, 'verify'])->middleware('permission:license.edit')->name('verify');
            Route::post('/check-updates', [App\Http\Controllers\Admin\LicenseController::class, 'checkUpdates'])->middleware('permission:license.view')->name('check-updates');
            Route::post('/apply-update', [App\Http\Controllers\Admin\LicenseController::class, 'applyUpdate'])->middleware('permission:license.edit')->name('apply-update');
        });

        // Settings
        Route::prefix('configuracoes')->name('settings.')->middleware('permission:settings.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\SettingController::class, 'update'])->middleware('permission:settings.edit')->name('update');
            Route::post('/salvar', [App\Http\Controllers\Admin\SettingController::class, 'update'])->middleware('permission:settings.edit')->name('save');
            Route::post('/alternar-tema', [App\Http\Controllers\Admin\SettingController::class, 'toggleTheme'])->middleware('permission:settings.edit')->name('toggle-theme');
        });

        // SMTP
        Route::prefix('smtp')->name('smtp.')->middleware('permission:smtp.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SmtpController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\SmtpController::class, 'update'])->middleware('permission:smtp.edit')->name('update');
            Route::post('/salvar', [App\Http\Controllers\Admin\SmtpController::class, 'update'])->middleware('permission:smtp.edit')->name('save');
            Route::post('/testar', [App\Http\Controllers\Admin\SmtpController::class, 'testConnection'])->middleware('permission:smtp.view')->name('test');
            Route::post('/testar-email', [App\Http\Controllers\Admin\SmtpController::class, 'sendTestEmail'])->middleware('permission:smtp.view')->name('test-email');
            Route::post('/enviar-teste', [App\Http\Controllers\Admin\SmtpController::class, 'sendTestEmail'])->middleware('permission:smtp.view')->name('send-test');
        });

        // Users
        Route::prefix('usuarios')->name('users.')->middleware('permission:users.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\UserController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\UserController::class, 'list'])->name('data');
            Route::post('/stop-impersonation', [App\Http\Controllers\Admin\UserController::class, 'stopImpersonation'])->name('stop-impersonation');
            Route::post('/criar', [App\Http\Controllers\Admin\UserController::class, 'store'])->middleware('permission:users.create')->name('store');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\UserController::class, 'edit'])->middleware('permission:users.edit')->name('edit');
            Route::get('/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
            Route::match(['post', 'put', 'patch'], '/{id}/atualizar', [App\Http\Controllers\Admin\UserController::class, 'update'])->middleware('permission:users.edit')->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->middleware('permission:users.delete')->name('destroy');
            Route::post('/{id}/bloquear', [App\Http\Controllers\Admin\UserController::class, 'block'])->middleware('permission:users.edit')->name('block');
            Route::post('/{id}/desbloquear', [App\Http\Controllers\Admin\UserController::class, 'unblock'])->middleware('permission:users.edit')->name('unblock');
            Route::post('/{id}/alternar-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->middleware('permission:users.edit')->name('toggle-status');
            Route::post('/{id}/login-as', [App\Http\Controllers\Admin\UserController::class, 'loginAs'])->middleware('permission:users.impersonar')->name('login-as');
        });

        // Permissions
        Route::prefix('permissoes')->name('permissions.')->middleware('permission:permissions.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\PermissionController::class, 'list'])->name('list');
            Route::get('/criar', [App\Http\Controllers\Admin\PermissionController::class, 'create'])->middleware('permission:permissions.create')->name('create');
            Route::post('/criar', [App\Http\Controllers\Admin\PermissionController::class, 'store'])->middleware('permission:permissions.create')->name('store');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\PermissionController::class, 'edit'])->middleware('permission:permissions.edit')->name('edit');
            Route::match(['post', 'put', 'patch'], '/{id}/atualizar', [App\Http\Controllers\Admin\PermissionController::class, 'update'])->middleware('permission:permissions.edit')->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->middleware('permission:permissions.delete')->name('destroy');
            Route::get('/grupos/{groupId?}', [App\Http\Controllers\Admin\PermissionController::class, 'getByGroup'])->name('grupos');

            // Profiles
            Route::get('/perfis', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profiles');
            Route::get('/perfis/listar', [App\Http\Controllers\Admin\ProfileController::class, 'list'])->name('profiles.list');
            Route::get('/perfis/criar', [App\Http\Controllers\Admin\ProfileController::class, 'create'])->middleware('permission:permissions.create')->name('profiles.create');
            Route::post('/perfis/criar', [App\Http\Controllers\Admin\ProfileController::class, 'store'])->middleware('permission:permissions.create')->name('profiles.store');
            Route::get('/perfis/{id}/editar', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->middleware('permission:permissions.edit')->name('profiles.edit');
            Route::get('/perfis/{id}', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profiles.show');
            Route::match(['post', 'put', 'patch'], '/perfis/{id}/atualizar', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->middleware('permission:permissions.edit')->name('profiles.update');
            Route::delete('/perfis/{id}/excluir', [App\Http\Controllers\Admin\ProfileController::class, 'destroy'])->middleware('permission:permissions.delete')->name('profiles.destroy');
            Route::post('/perfis/{id}/permissoes', [App\Http\Controllers\Admin\ProfileController::class, 'syncPermissions'])->middleware('permission:permissions.edit')->name('profiles.sync-permissions');

            // Aliases legados para rotas usadas nas views; remover na v1.1.0.
            Route::get('/profile/{id}', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
            Route::match(['post', 'put', 'patch'], '/profile/{id}/atualizar', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->middleware('permission:permissions.edit')->name('profile.update');
            Route::post('/profile/criar', [App\Http\Controllers\Admin\ProfileController::class, 'store'])->middleware('permission:permissions.create')->name('profile.store');
            Route::delete('/profile/{id}/excluir', [App\Http\Controllers\Admin\ProfileController::class, 'destroy'])->middleware('permission:permissions.delete')->name('profile.delete');

            // Permissions AJAX routes
            Route::get('/get/{groupId?}', [App\Http\Controllers\Admin\PermissionController::class, 'getByGroup'])->name('get');
            Route::post('/salvar', [App\Http\Controllers\Admin\PermissionController::class, 'store'])->middleware('permission:permissions.create')->name('save');
        });

        // Pages
        Route::prefix('paginas')->name('pages.')->middleware('permission:pages.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PageController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\PageController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\PageController::class, 'list'])->name('data');
            Route::get('/criar', [App\Http\Controllers\Admin\PageController::class, 'create'])->middleware('permission:pages.create')->name('create');
            Route::post('/criar', [App\Http\Controllers\Admin\PageController::class, 'store'])->middleware('permission:pages.create')->name('store');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\PageController::class, 'edit'])->middleware('permission:pages.edit')->name('edit');
            Route::match(['post', 'put', 'patch'], '/{id}/atualizar', [App\Http\Controllers\Admin\PageController::class, 'update'])->middleware('permission:pages.edit')->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\PageController::class, 'destroy'])->middleware('permission:pages.delete')->name('destroy');
        });

        // Blog
        Route::prefix('blog')->name('blog.')->middleware('permission:blog.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\BlogController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\BlogController::class, 'list'])->name('data');
            Route::get('/criar', [App\Http\Controllers\Admin\BlogController::class, 'create'])->middleware('permission:blog.create')->name('create');
            Route::post('/criar', [App\Http\Controllers\Admin\BlogController::class, 'store'])->middleware('permission:blog.create')->name('store');

            // Categories
            Route::get('/categorias', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories');
            Route::get('/categorias/listar', [App\Http\Controllers\Admin\CategoryController::class, 'list'])->name('categories.list');
            Route::get('/categorias/criar', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->middleware('permission:blog.create')->name('categories.create');
            Route::post('/categorias/criar', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->middleware('permission:blog.create')->name('categories.store');
            Route::get('/categorias/{id}/editar', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->middleware('permission:blog.edit')->name('categories.edit');
            Route::match(['post', 'put', 'patch'], '/categorias/{id}/atualizar', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->middleware('permission:blog.edit')->name('categories.update');
            Route::delete('/categorias/{id}/excluir', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->middleware('permission:blog.delete')->name('categories.destroy');

            // Tags
            Route::get('/tags', [App\Http\Controllers\Admin\TagController::class, 'index'])->name('tags');
            Route::get('/tags/listar', [App\Http\Controllers\Admin\TagController::class, 'list'])->name('tags.list');
            Route::get('/tags/criar', [App\Http\Controllers\Admin\TagController::class, 'create'])->middleware('permission:blog.create')->name('tags.create');
            Route::post('/tags/criar', [App\Http\Controllers\Admin\TagController::class, 'store'])->middleware('permission:blog.create')->name('tags.store');
            Route::get('/tags/{id}/editar', [App\Http\Controllers\Admin\TagController::class, 'edit'])->middleware('permission:blog.edit')->name('tags.edit');
            Route::match(['post', 'put', 'patch'], '/tags/{id}/atualizar', [App\Http\Controllers\Admin\TagController::class, 'update'])->middleware('permission:blog.edit')->name('tags.update');
            Route::delete('/tags/{id}/excluir', [App\Http\Controllers\Admin\TagController::class, 'destroy'])->middleware('permission:blog.delete')->name('tags.destroy');

            Route::get('/{id}', [App\Http\Controllers\Admin\BlogController::class, 'show'])->name('show');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\BlogController::class, 'edit'])->middleware('permission:blog.edit')->name('edit');
            Route::match(['post', 'put', 'patch'], '/{id}/atualizar', [App\Http\Controllers\Admin\BlogController::class, 'update'])->middleware('permission:blog.edit')->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\BlogController::class, 'destroy'])->middleware('permission:blog.delete')->name('destroy');
            Route::post('/{id}/publicar', [App\Http\Controllers\Admin\BlogController::class, 'publish'])->middleware('permission:blog.edit')->name('publish');
            Route::post('/{id}/arquivar', [App\Http\Controllers\Admin\BlogController::class, 'archive'])->middleware('permission:blog.edit')->name('archive');
        });

        // Media
        Route::prefix('midia')->name('media.')->middleware('permission:midia.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MediaController::class, 'index'])->name('index');
            Route::get('/midia', [App\Http\Controllers\Admin\MediaController::class, 'index'])->name('midia.index');
            Route::get('/listar', [App\Http\Controllers\Admin\MediaController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\MediaController::class, 'list'])->name('data');
            Route::post('/pastas/criar', [App\Http\Controllers\Admin\MediaController::class, 'createFolder'])->middleware('permission:midia.create')->name('folder.create');
            Route::post('/upload', [App\Http\Controllers\Admin\MediaController::class, 'upload'])->middleware('permission:midia.create')->name('upload');
            Route::post('/upload-multiplo', [App\Http\Controllers\Admin\MediaController::class, 'uploadMultiple'])->middleware('permission:midia.create')->name('upload-multiple');
            Route::get('/browse', [App\Http\Controllers\Admin\MediaController::class, 'browse'])->name('browse');
            Route::get('/{id}', [App\Http\Controllers\Admin\MediaController::class, 'show'])->name('show');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\MediaController::class, 'destroy'])->middleware('permission:midia.delete')->name('destroy');
            Route::post('/{id}/info', [App\Http\Controllers\Admin\MediaController::class, 'updateInfo'])->middleware('permission:midia.edit')->name('update-info');
            Route::post('/{id}/substituir', [App\Http\Controllers\Admin\MediaController::class, 'replaceFile'])->middleware('permission:midia.edit')->name('replace');
        });

        // Agenda
        Route::prefix('agenda')->name('agenda.')->middleware('permission:agenda.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\EventController::class, 'index'])->name('index');
            Route::get('/eventos', [App\Http\Controllers\Admin\EventController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\EventController::class, 'list'])->name('data');
            Route::get('/events', [App\Http\Controllers\Admin\EventController::class, 'list'])->name('events');
            Route::get('/criar', [App\Http\Controllers\Admin\EventController::class, 'create'])->middleware('permission:agenda.create')->name('create');
            Route::post('/criar', [App\Http\Controllers\Admin\EventController::class, 'store'])->middleware('permission:agenda.create')->name('store');
            Route::get('/{id}', [App\Http\Controllers\Admin\EventController::class, 'show'])->name('show');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\EventController::class, 'edit'])->middleware('permission:agenda.edit')->name('edit');
            Route::match(['post', 'put', 'patch'], '/{id}/atualizar', [App\Http\Controllers\Admin\EventController::class, 'update'])->middleware('permission:agenda.edit')->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\EventController::class, 'destroy'])->middleware('permission:agenda.delete')->name('destroy');
            Route::post('/{id}/arrastar', [App\Http\Controllers\Admin\EventController::class, 'dragUpdate'])->middleware('permission:agenda.edit')->name('drag-update');
        });

        // Financeiro
        Route::prefix('financeiro')->name('financeiro.')->middleware('permission:financeiro.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\FinanceiroController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\FinanceiroController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\FinanceiroController::class, 'list'])->name('data');
            Route::get('/categorias', [App\Http\Controllers\Admin\FinanceiroController::class, 'categories'])->name('categorias');
            Route::get('/criar', [App\Http\Controllers\Admin\FinanceiroController::class, 'create'])->middleware('permission:financeiro.create')->name('create');
            Route::get('/resumo', [App\Http\Controllers\Admin\FinanceiroController::class, 'getSummary'])->name('summary');
            Route::get('/exportar', [App\Http\Controllers\Admin\FinanceiroController::class, 'export'])->name('export');
            Route::post('/categorias/criar', [App\Http\Controllers\Admin\FinanceiroController::class, 'storeCategory'])->middleware('permission:financeiro.create')->name('categorias.store');
            Route::match(['post', 'put', 'patch'], '/categorias/{id}/atualizar', [App\Http\Controllers\Admin\FinanceiroController::class, 'updateCategory'])->middleware('permission:financeiro.edit')->name('categorias.update');
            Route::delete('/categorias/{id}/excluir', [App\Http\Controllers\Admin\FinanceiroController::class, 'destroyCategory'])->middleware('permission:financeiro.delete')->name('categorias.destroy');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\FinanceiroController::class, 'edit'])->middleware('permission:financeiro.edit')->name('edit');
            Route::get('/{id}', [App\Http\Controllers\Admin\FinanceiroController::class, 'show'])->name('show');
            Route::post('/criar', [App\Http\Controllers\Admin\FinanceiroController::class, 'store'])->middleware('permission:financeiro.create')->name('store');
            Route::match(['post', 'put', 'patch'], '/{id}/atualizar', [App\Http\Controllers\Admin\FinanceiroController::class, 'update'])->middleware('permission:financeiro.edit')->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\FinanceiroController::class, 'destroy'])->middleware('permission:financeiro.delete')->name('destroy');
        });

        // Transparencia
        Route::prefix('transparencia')->name('transparencia.')->middleware('permission:transparencia.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\TransparenciaController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\TransparenciaController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\TransparenciaController::class, 'list'])->name('data');
            Route::get('/criar', [App\Http\Controllers\Admin\TransparenciaController::class, 'create'])->middleware('permission:transparencia.create')->name('create');
            Route::get('/exportar', [App\Http\Controllers\Admin\TransparenciaController::class, 'export'])->name('export');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\TransparenciaController::class, 'edit'])->middleware('permission:transparencia.edit')->name('edit');
            Route::get('/{id}', [App\Http\Controllers\Admin\TransparenciaController::class, 'show'])->name('show');
            Route::post('/criar', [App\Http\Controllers\Admin\TransparenciaController::class, 'store'])->middleware('permission:transparencia.create')->name('store');
            Route::match(['post', 'put', 'patch'], '/{id}/atualizar', [App\Http\Controllers\Admin\TransparenciaController::class, 'update'])->middleware('permission:transparencia.edit')->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\TransparenciaController::class, 'destroy'])->middleware('permission:transparencia.delete')->name('destroy');
        });

        // Contatos
        Route::prefix('contatos')->name('contatos.')->middleware('permission:contato.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ContatoController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\ContatoController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\ContatoController::class, 'list'])->name('data');
            Route::get('/exportar', [App\Http\Controllers\Admin\ContatoController::class, 'export'])->name('export');
            Route::get('/{id}', [App\Http\Controllers\Admin\ContatoController::class, 'show'])->name('show');
            Route::post('/{id}/responder', [App\Http\Controllers\Admin\ContatoController::class, 'reply'])->middleware('permission:contato.edit')->name('reply');
            Route::post('/{id}/marcar-lido', [App\Http\Controllers\Admin\ContatoController::class, 'markRead'])->middleware('permission:contato.edit')->name('mark-read');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\ContatoController::class, 'destroy'])->middleware('permission:contato.delete')->name('destroy');

            // Aliases legados para rotas usadas nas views; remover na v1.1.0.
            Route::get('/contato/index', [App\Http\Controllers\Admin\ContatoController::class, 'index'])->name('contato.index');
            Route::post('/contato/marcar-todos-lidos', [App\Http\Controllers\Admin\ContatoController::class, 'markAllRead'])->middleware('permission:contato.edit')->name('contato.mark-all-read');
            Route::delete('/contato/lidos/excluir', [App\Http\Controllers\Admin\ContatoController::class, 'deleteRead'])->middleware('permission:contato.delete')->name('contato.delete-read');
            Route::get('/contato/{id}', [App\Http\Controllers\Admin\ContatoController::class, 'show'])->name('contato.show');
            Route::post('/contato/{id}/responder', [App\Http\Controllers\Admin\ContatoController::class, 'reply'])->middleware('permission:contato.edit')->name('contato.reply');
            Route::post('/contato/{id}/marcar-lido', [App\Http\Controllers\Admin\ContatoController::class, 'markRead'])->middleware('permission:contato.edit')->name('contato.mark-read');
            Route::delete('/contato/{id}/excluir', [App\Http\Controllers\Admin\ContatoController::class, 'destroy'])->middleware('permission:contato.delete')->name('contato.destroy');
        });

        // Newsletter
        Route::prefix('newsletter')->name('newsletter.')->middleware('permission:newsletter.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\NewsletterController::class, 'list'])->name('list');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->middleware('permission:newsletter.delete')->name('destroy');
            Route::get('/exportar', [App\Http\Controllers\Admin\NewsletterController::class, 'export'])->name('export');
            Route::post('/enviar-campanha', [App\Http\Controllers\Admin\NewsletterController::class, 'sendCampaign'])->middleware('permission:newsletter.edit')->name('send-campaign');
        });

        // Visitas
        Route::prefix('visitas')->name('visitas.')->middleware('permission:visitas.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\VisitaController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\VisitaController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\VisitaController::class, 'list'])->name('data');
            Route::get('/grafico', [App\Http\Controllers\Admin\VisitaController::class, 'chartData'])->name('chart-data');
            Route::get('/top-paginas', [App\Http\Controllers\Admin\VisitaController::class, 'getTopPages'])->name('top-pages');
            Route::get('/fontes', [App\Http\Controllers\Admin\VisitaController::class, 'getTrafficSources'])->name('traffic-sources');
            Route::get('/geo', [App\Http\Controllers\Admin\VisitaController::class, 'getGeoStats'])->name('geo');
            Route::get('/exportar', [App\Http\Controllers\Admin\VisitaController::class, 'export'])->name('export');
        });

        // Logs
        Route::prefix('logs')->name('logs.')->middleware('permission:logs.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\LogController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\LogController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\LogController::class, 'list'])->name('data');
            Route::delete('/limpar', [App\Http\Controllers\Admin\LogController::class, 'clear'])->middleware('permission:logs.delete')->name('clear');
            Route::get('/{id}', [App\Http\Controllers\Admin\LogController::class, 'show'])->name('show');
        });

        // Backup
        Route::prefix('backup')->name('backup.')->middleware('permission:backup.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\BackupController::class, 'list'])->name('list');
            Route::post('/configurar', [App\Http\Controllers\Admin\BackupController::class, 'saveConfig'])->middleware('permission:backup.edit')->name('config.save');
            Route::post('/restaurar', [App\Http\Controllers\Admin\BackupController::class, 'restore'])->middleware('permission:backup.edit')->name('restore');
            Route::get('/criar', [App\Http\Controllers\Admin\BackupController::class, 'createForm'])->middleware('permission:backup.create')->name('create.form');
            Route::post('/criar', [App\Http\Controllers\Admin\BackupController::class, 'create'])->middleware('permission:backup.create')->name('create');
            Route::get('/{id}/download', [App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\BackupController::class, 'destroy'])->middleware('permission:backup.delete')->name('delete');
        });

        // WAF
        Route::prefix('waf')->name('waf.')->middleware('permission:waf.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\WafController::class, 'index'])->name('index');
            Route::post('/configurar', [App\Http\Controllers\Admin\WafController::class, 'updateConfig'])->middleware('permission:waf.edit')->name('config');
            Route::post('/salvar', [App\Http\Controllers\Admin\WafController::class, 'updateConfig'])->middleware('permission:waf.edit')->name('save');
            Route::post('/alternar', [App\Http\Controllers\Admin\WafController::class, 'toggle'])->middleware('permission:waf.edit')->name('toggle');
            Route::post('/bloquear-ip', [App\Http\Controllers\Admin\WafController::class, 'blockIp'])->middleware('permission:waf.edit')->name('block-ip');
            Route::post('/desbloquear-ip/{id}', [App\Http\Controllers\Admin\WafController::class, 'unblockIp'])->middleware('permission:waf.edit')->name('unblock-ip');
            Route::post('/desbloquear', [App\Http\Controllers\Admin\WafController::class, 'unblock'])->middleware('permission:waf.edit')->name('unblock');
            Route::get('/logs', [App\Http\Controllers\Admin\WafController::class, 'getLogs'])->name('logs');
        });

        // Menus
        Route::prefix('menus')->name('menus.')->middleware('permission:menus.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MenuController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\MenuController::class, 'list'])->name('list');
            Route::get('/criar', [App\Http\Controllers\Admin\MenuController::class, 'create'])->middleware('permission:menus.create')->name('create');
            Route::post('/criar', [App\Http\Controllers\Admin\MenuController::class, 'store'])->middleware('permission:menus.create')->name('store');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\MenuController::class, 'edit'])->middleware('permission:menus.edit')->name('edit');
            Route::match(['post', 'put', 'patch'], '/{id}/atualizar', [App\Http\Controllers\Admin\MenuController::class, 'update'])->middleware('permission:menus.edit')->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\MenuController::class, 'destroy'])->middleware('permission:menus.delete')->name('destroy');
            Route::get('/{id}', [App\Http\Controllers\Admin\MenuController::class, 'show'])->name('show');
            Route::post('/{id}/itens', [App\Http\Controllers\Admin\MenuController::class, 'addItem'])->middleware('permission:menus.create')->name('items.store');
            Route::match(['post', 'put', 'patch'], '/itens/{itemId}/atualizar', [App\Http\Controllers\Admin\MenuController::class, 'updateItem'])->middleware('permission:menus.edit')->name('items.update');
            Route::delete('/itens/{itemId}/excluir', [App\Http\Controllers\Admin\MenuController::class, 'deleteItem'])->middleware('permission:menus.delete')->name('items.destroy');
            Route::post('/reordenar', [App\Http\Controllers\Admin\MenuController::class, 'reorderItems'])->middleware('permission:menus.edit')->name('reorder');

            // Aliases legados para rotas usadas nas views; remover na v1.1.0.
            Route::get('/itens/{itemId}', [App\Http\Controllers\Admin\MenuController::class, 'showItem'])->name('item.show');
            Route::post('/itens/{itemId}/excluir', [App\Http\Controllers\Admin\MenuController::class, 'deleteItem'])->middleware('permission:menus.delete')->name('item.destroy');
            Route::match(['post', 'put', 'patch'], '/itens/{itemId}/atualizar', [App\Http\Controllers\Admin\MenuController::class, 'updateItem'])->middleware('permission:menus.edit')->name('item.update');
            Route::post('/itens/criar', [App\Http\Controllers\Admin\MenuController::class, 'addItem'])->middleware('permission:menus.create')->name('item.store');
        });

        // Modulos
        Route::prefix('modulos')->name('modules.')->middleware('permission:modules.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ModuleController::class, 'index'])->name('index');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\ModuleController::class, 'edit'])->middleware('permission:modules.edit')->name('edit');
            Route::post('/{id}/alternar', [App\Http\Controllers\Admin\ModuleController::class, 'toggle'])->middleware('permission:modules.edit')->name('toggle');
            Route::post('/{id}/configurar', [App\Http\Controllers\Admin\ModuleController::class, 'config'])->middleware('permission:modules.edit')->name('config');
            Route::match(['post', 'put', 'patch'], '/{id}/atualizar', [App\Http\Controllers\Admin\ModuleController::class, 'update'])->middleware('permission:modules.edit')->name('update');
        });

        // SEO
        Route::prefix('seo')->name('seo.')->middleware('permission:seo.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SeoController::class, 'index'])->name('index');
            Route::post('/analisar', [App\Http\Controllers\Admin\SeoController::class, 'analyze'])->name('analyze');
            Route::post('/gerar-sitemap', [App\Http\Controllers\Admin\SeoController::class, 'generateSitemap'])->middleware('permission:seo.edit')->name('sitemap');
            Route::post('/atualizar-robots', [App\Http\Controllers\Admin\SeoController::class, 'updateRobotsTxt'])->middleware('permission:seo.edit')->name('robots');
            Route::post('/preview-social', [App\Http\Controllers\Admin\SeoController::class, 'previewSocial'])->name('preview-social');
        });

        // Hashtags
        Route::prefix('hashtags')->name('hashtags.')->middleware('permission:hashtags.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\HashtagController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\HashtagController::class, 'list'])->name('list');
            Route::get('/criar', [App\Http\Controllers\Admin\HashtagController::class, 'create'])->middleware('permission:hashtags.create')->name('create');
            Route::post('/criar', [App\Http\Controllers\Admin\HashtagController::class, 'store'])->middleware('permission:hashtags.create')->name('store');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\HashtagController::class, 'edit'])->middleware('permission:hashtags.edit')->name('edit');
            Route::match(['post', 'put', 'patch'], '/{id}/atualizar', [App\Http\Controllers\Admin\HashtagController::class, 'update'])->middleware('permission:hashtags.edit')->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\HashtagController::class, 'destroy'])->middleware('permission:hashtags.delete')->name('destroy');
            Route::get('/buscar', [App\Http\Controllers\Admin\HashtagController::class, 'search'])->name('search');
        });

        // Notificacoes
        Route::prefix('notificacoes')->name('notificacoes.')->middleware('permission:notificacoes.view')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\NotificacaoController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\NotificacaoController::class, 'list'])->name('list');
            Route::get('/poll', [App\Http\Controllers\Admin\NotificacaoController::class, 'poll'])->name('poll');
            Route::post('/{id}/marcar-lida', [App\Http\Controllers\Admin\NotificacaoController::class, 'markAsRead'])->middleware('permission:notificacoes.edit')->name('mark-read');
            Route::post('/marcar-todas-lidas', [App\Http\Controllers\Admin\NotificacaoController::class, 'markAllAsRead'])->middleware('permission:notificacoes.edit')->name('mark-all-read');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\NotificacaoController::class, 'destroy'])->middleware('permission:notificacoes.delete')->name('destroy');
            Route::get('/nao-lidas', [App\Http\Controllers\Admin\NotificacaoController::class, 'getUnreadCount'])->name('unread-count');
        });

        // Aliases legados para rotas usadas nas views; remover na v1.1.0.
        Route::get('midia', [App\Http\Controllers\Admin\MediaController::class, 'index'])->name('midia.index');
        Route::get('contato', [App\Http\Controllers\Admin\ContatoController::class, 'index'])->name('contato.index');
        Route::get('contato/data', [App\Http\Controllers\Admin\ContatoController::class, 'list'])->name('contato.data');
        Route::post('contato/marcar-todos-lidos', [App\Http\Controllers\Admin\ContatoController::class, 'markAllRead'])->middleware('permission:contato.edit')->name('contato.mark-all-read');
        Route::delete('contato/lidos/excluir', [App\Http\Controllers\Admin\ContatoController::class, 'deleteRead'])->middleware('permission:contato.delete')->name('contato.delete-read');
        Route::get('contato/{id}', [App\Http\Controllers\Admin\ContatoController::class, 'show'])->name('contato.show');
        Route::post('contato/{id}/responder', [App\Http\Controllers\Admin\ContatoController::class, 'reply'])->middleware('permission:contato.edit')->name('contato.reply');
        Route::post('contato/{id}/marcar-lido', [App\Http\Controllers\Admin\ContatoController::class, 'markRead'])->middleware('permission:contato.edit')->name('contato.mark-read');
        Route::delete('contato/{id}/excluir', [App\Http\Controllers\Admin\ContatoController::class, 'destroy'])->middleware('permission:contato.delete')->name('contato.destroy');

        // Documentação
        Route::get('docs', function () {
            return view('admin.docs.index');
        })->name('docs');
    });
});

// ========== CATCH-ALL (must be LAST) ==========
Route::name('site.')->group(function () {
    Route::get('/{slug}', [App\Http\Controllers\Site\PageController::class, 'show'])->name('pagina');
});
