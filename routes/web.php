<?php

declare(strict_types=1);

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
    Route::post('/contato/enviar', [App\Http\Controllers\Site\ContatoController::class, 'enviar'])->name('contato.enviar');

    // Newsletter
    Route::post('/newsletter/inscrever', [App\Http\Controllers\Site\NewsletterController::class, 'inscrever'])->name('newsletter.subscribe');
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

        // Dashboard
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.index');

        // License
        Route::prefix('license')->name('license.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\LicenseController::class, 'index'])->name('index');
            Route::post('/activate', [App\Http\Controllers\Admin\LicenseController::class, 'activate'])->name('activate');
            Route::post('/deactivate', [App\Http\Controllers\Admin\LicenseController::class, 'deactivate'])->name('deactivate');
            Route::post('/verify', [App\Http\Controllers\Admin\LicenseController::class, 'verify'])->name('verify');
            Route::post('/check-updates', [App\Http\Controllers\Admin\LicenseController::class, 'checkUpdates'])->name('check-updates');
            Route::post('/apply-update', [App\Http\Controllers\Admin\LicenseController::class, 'applyUpdate'])->name('apply-update');
        });

        // Settings
        Route::prefix('configuracoes')->name('settings.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('update');
            Route::post('/salvar', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('save');
            Route::post('/alternar-tema', [App\Http\Controllers\Admin\SettingController::class, 'toggleTheme'])->name('toggle-theme');
        });

        // SMTP
        Route::prefix('smtp')->name('smtp.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SmtpController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\SmtpController::class, 'update'])->name('update');
            Route::post('/salvar', [App\Http\Controllers\Admin\SmtpController::class, 'update'])->name('save');
            Route::post('/testar', [App\Http\Controllers\Admin\SmtpController::class, 'testConnection'])->name('test');
            Route::post('/testar-email', [App\Http\Controllers\Admin\SmtpController::class, 'sendTestEmail'])->name('test-email');
            Route::post('/enviar-teste', [App\Http\Controllers\Admin\SmtpController::class, 'sendTestEmail'])->name('send-test');
        });

        // Users
        Route::prefix('usuarios')->name('users.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\UserController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\UserController::class, 'list'])->name('data');
            Route::post('/criar', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
            Route::get('/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
            Route::post('/{id}/atualizar', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/bloquear', [App\Http\Controllers\Admin\UserController::class, 'block'])->name('block');
            Route::post('/{id}/desbloquear', [App\Http\Controllers\Admin\UserController::class, 'unblock'])->name('unblock');
            Route::post('/{id}/alternar-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Permissions
        Route::prefix('permissoes')->name('permissions.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\PermissionController::class, 'list'])->name('list');
            Route::get('/criar', [App\Http\Controllers\Admin\PermissionController::class, 'create'])->name('create');
            Route::post('/criar', [App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('store');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\PermissionController::class, 'edit'])->name('edit');
            Route::post('/{id}/atualizar', [App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->name('destroy');
            Route::get('/grupos', [App\Http\Controllers\Admin\PermissionController::class, 'getByGroup'])->name('grupos');

            // Profiles
            Route::get('/perfis', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profiles');
            Route::get('/perfis/listar', [App\Http\Controllers\Admin\ProfileController::class, 'list'])->name('profiles.list');
            Route::post('/perfis/criar', [App\Http\Controllers\Admin\ProfileController::class, 'store'])->name('profiles.store');
            Route::get('/perfis/{id}', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profiles.show');
            Route::post('/perfis/{id}/atualizar', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profiles.update');
            Route::delete('/perfis/{id}/excluir', [App\Http\Controllers\Admin\ProfileController::class, 'destroy'])->name('profiles.destroy');
            Route::post('/perfis/{id}/permissoes', [App\Http\Controllers\Admin\ProfileController::class, 'syncPermissions'])->name('profiles.sync-permissions');

            // Aliases para rotas usadas nas views
            Route::get('/profile/{id}', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
            Route::post('/profile/{id}/atualizar', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
            Route::post('/profile/criar', [App\Http\Controllers\Admin\ProfileController::class, 'store'])->name('profile.store');
            Route::delete('/profile/{id}/excluir', [App\Http\Controllers\Admin\ProfileController::class, 'destroy'])->name('profile.delete');

            // Permissions AJAX routes
            Route::get('/get', [App\Http\Controllers\Admin\PermissionController::class, 'getByGroup'])->name('get');
            Route::post('/salvar', [App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('save');
        });

        // Pages
        Route::prefix('paginas')->name('pages.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PageController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\PageController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\PageController::class, 'list'])->name('data');
            Route::get('/criar', [App\Http\Controllers\Admin\PageController::class, 'create'])->name('create');
            Route::post('/criar', [App\Http\Controllers\Admin\PageController::class, 'store'])->name('store');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\PageController::class, 'edit'])->name('edit');
            Route::post('/{id}/atualizar', [App\Http\Controllers\Admin\PageController::class, 'update'])->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\PageController::class, 'destroy'])->name('destroy');
        });

        // Blog
        Route::prefix('blog')->name('blog.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\BlogController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\BlogController::class, 'list'])->name('data');
            Route::get('/criar', [App\Http\Controllers\Admin\BlogController::class, 'create'])->name('create');
            Route::post('/criar', [App\Http\Controllers\Admin\BlogController::class, 'store'])->name('store');

            // Categories
            Route::get('/categorias', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories');
            Route::get('/categorias/listar', [App\Http\Controllers\Admin\CategoryController::class, 'list'])->name('categories.list');
            Route::post('/categorias/criar', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
            Route::post('/categorias/{id}/atualizar', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categorias/{id}/excluir', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

            // Tags
            Route::get('/tags', [App\Http\Controllers\Admin\TagController::class, 'index'])->name('tags');
            Route::get('/tags/listar', [App\Http\Controllers\Admin\TagController::class, 'list'])->name('tags.list');
            Route::post('/tags/criar', [App\Http\Controllers\Admin\TagController::class, 'store'])->name('tags.store');
            Route::post('/tags/{id}/atualizar', [App\Http\Controllers\Admin\TagController::class, 'update'])->name('tags.update');
            Route::delete('/tags/{id}/excluir', [App\Http\Controllers\Admin\TagController::class, 'destroy'])->name('tags.destroy');

            Route::get('/{id}', [App\Http\Controllers\Admin\BlogController::class, 'show'])->name('show');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('edit');
            Route::post('/{id}/atualizar', [App\Http\Controllers\Admin\BlogController::class, 'update'])->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\BlogController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/publicar', [App\Http\Controllers\Admin\BlogController::class, 'publish'])->name('publish');
            Route::post('/{id}/arquivar', [App\Http\Controllers\Admin\BlogController::class, 'archive'])->name('archive');
        });

        // Media
        Route::prefix('midia')->name('media.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MediaController::class, 'index'])->name('index');
            Route::get('/midia', [App\Http\Controllers\Admin\MediaController::class, 'index'])->name('midia.index');
            Route::get('/listar', [App\Http\Controllers\Admin\MediaController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\MediaController::class, 'list'])->name('data');
            Route::post('/pastas/criar', [App\Http\Controllers\Admin\MediaController::class, 'createFolder'])->name('folder.create');
            Route::post('/upload', [App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('upload');
            Route::post('/upload-multiplo', [App\Http\Controllers\Admin\MediaController::class, 'uploadMultiple'])->name('upload-multiple');
            Route::get('/browse', [App\Http\Controllers\Admin\MediaController::class, 'browse'])->name('browse');
            Route::get('/{id}', [App\Http\Controllers\Admin\MediaController::class, 'show'])->name('show');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/info', [App\Http\Controllers\Admin\MediaController::class, 'updateInfo'])->name('update-info');
            Route::post('/{id}/substituir', [App\Http\Controllers\Admin\MediaController::class, 'replaceFile'])->name('replace');
        });

        // Agenda
        Route::prefix('agenda')->name('agenda.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\EventController::class, 'index'])->name('index');
            Route::get('/eventos', [App\Http\Controllers\Admin\EventController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\EventController::class, 'list'])->name('data');
            Route::post('/criar', [App\Http\Controllers\Admin\EventController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Admin\EventController::class, 'show'])->name('show');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\EventController::class, 'edit'])->name('edit');
            Route::post('/{id}/atualizar', [App\Http\Controllers\Admin\EventController::class, 'update'])->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\EventController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/arrastar', [App\Http\Controllers\Admin\EventController::class, 'dragUpdate'])->name('drag-update');
        });

        // Financeiro
        Route::prefix('financeiro')->name('financeiro.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\FinanceiroController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\FinanceiroController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\FinanceiroController::class, 'list'])->name('data');
            Route::get('/categorias', [App\Http\Controllers\Admin\FinanceiroController::class, 'categories'])->name('categorias');
            Route::get('/criar', [App\Http\Controllers\Admin\FinanceiroController::class, 'create'])->name('create');
            Route::get('/resumo', [App\Http\Controllers\Admin\FinanceiroController::class, 'getSummary'])->name('summary');
            Route::get('/exportar', [App\Http\Controllers\Admin\FinanceiroController::class, 'export'])->name('export');
            Route::get('/{id}', [App\Http\Controllers\Admin\FinanceiroController::class, 'show'])->name('show');
            Route::post('/criar', [App\Http\Controllers\Admin\FinanceiroController::class, 'store'])->name('store');
            Route::post('/{id}/atualizar', [App\Http\Controllers\Admin\FinanceiroController::class, 'update'])->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\FinanceiroController::class, 'destroy'])->name('destroy');
        });

        // Transparencia
        Route::prefix('transparencia')->name('transparencia.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\TransparenciaController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\TransparenciaController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\TransparenciaController::class, 'list'])->name('data');
            Route::get('/criar', [App\Http\Controllers\Admin\TransparenciaController::class, 'create'])->name('create');
            Route::get('/exportar', [App\Http\Controllers\Admin\TransparenciaController::class, 'export'])->name('export');
            Route::get('/{id}', [App\Http\Controllers\Admin\TransparenciaController::class, 'show'])->name('show');
            Route::post('/criar', [App\Http\Controllers\Admin\TransparenciaController::class, 'store'])->name('store');
            Route::post('/{id}/atualizar', [App\Http\Controllers\Admin\TransparenciaController::class, 'update'])->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\TransparenciaController::class, 'destroy'])->name('destroy');
        });

        // Contatos
        Route::prefix('contatos')->name('contatos.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ContatoController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\ContatoController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\ContatoController::class, 'list'])->name('data');
            Route::get('/{id}', [App\Http\Controllers\Admin\ContatoController::class, 'show'])->name('show');
            Route::post('/{id}/responder', [App\Http\Controllers\Admin\ContatoController::class, 'reply'])->name('reply');
            Route::post('/{id}/marcar-lido', [App\Http\Controllers\Admin\ContatoController::class, 'markRead'])->name('mark-read');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\ContatoController::class, 'destroy'])->name('destroy');
            Route::get('/exportar', [App\Http\Controllers\Admin\ContatoController::class, 'export'])->name('export');

            // Aliases para rotas usadas nas views (sem 's' no nome)
            Route::get('/contato/index', [App\Http\Controllers\Admin\ContatoController::class, 'index'])->name('contato.index');
            Route::get('/contato/{id}', [App\Http\Controllers\Admin\ContatoController::class, 'show'])->name('contato.show');
            Route::post('/contato/{id}/responder', [App\Http\Controllers\Admin\ContatoController::class, 'reply'])->name('contato.reply');
            Route::post('/contato/{id}/marcar-lido', [App\Http\Controllers\Admin\ContatoController::class, 'markRead'])->name('contato.mark-read');
            Route::post('/contato/marcar-todos-lidos', [App\Http\Controllers\Admin\ContatoController::class, 'markAllRead'])->name('contato.mark-all-read');
            Route::delete('/contato/{id}/excluir', [App\Http\Controllers\Admin\ContatoController::class, 'destroy'])->name('contato.destroy');
            Route::delete('/contato/lidos/excluir', [App\Http\Controllers\Admin\ContatoController::class, 'deleteRead'])->name('contato.delete-read');
        });

        // Newsletter
        Route::prefix('newsletter')->name('newsletter.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\NewsletterController::class, 'list'])->name('list');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->name('destroy');
            Route::get('/exportar', [App\Http\Controllers\Admin\NewsletterController::class, 'export'])->name('export');
            Route::post('/enviar-campanha', [App\Http\Controllers\Admin\NewsletterController::class, 'sendCampaign'])->name('send-campaign');
        });

        // Visitas
        Route::prefix('visitas')->name('visitas.')->group(function () {
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
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\LogController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\LogController::class, 'list'])->name('list');
            Route::get('/data', [App\Http\Controllers\Admin\LogController::class, 'list'])->name('data');
            Route::delete('/limpar', [App\Http\Controllers\Admin\LogController::class, 'clear'])->name('clear');
            Route::get('/{id}', [App\Http\Controllers\Admin\LogController::class, 'show'])->name('show');
        });

        // Backup
        Route::prefix('backup')->name('backup.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\BackupController::class, 'list'])->name('list');
            Route::post('/configurar', [App\Http\Controllers\Admin\BackupController::class, 'saveConfig'])->name('config.save');
            Route::post('/restaurar', [App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('restore');
            Route::post('/criar', [App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
            Route::get('/{id}/download', [App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('delete');
        });

        // WAF
        Route::prefix('waf')->name('waf.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\WafController::class, 'index'])->name('index');
            Route::post('/configurar', [App\Http\Controllers\Admin\WafController::class, 'updateConfig'])->name('config');
            Route::post('/salvar', [App\Http\Controllers\Admin\WafController::class, 'updateConfig'])->name('save');
            Route::post('/alternar', [App\Http\Controllers\Admin\WafController::class, 'toggle'])->name('toggle');
            Route::post('/bloquear-ip', [App\Http\Controllers\Admin\WafController::class, 'blockIp'])->name('block-ip');
            Route::post('/desbloquear-ip/{id}', [App\Http\Controllers\Admin\WafController::class, 'unblockIp'])->name('unblock-ip');
            Route::post('/desbloquear', [App\Http\Controllers\Admin\WafController::class, 'unblock'])->name('unblock');
            Route::get('/logs', [App\Http\Controllers\Admin\WafController::class, 'getLogs'])->name('logs');
        });

        // Menus
        Route::prefix('menus')->name('menus.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MenuController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\MenuController::class, 'list'])->name('list');
            Route::get('/criar', [App\Http\Controllers\Admin\MenuController::class, 'create'])->name('create');
            Route::post('/criar', [App\Http\Controllers\Admin\MenuController::class, 'store'])->name('store');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\MenuController::class, 'edit'])->name('edit');
            Route::post('/{id}/atualizar', [App\Http\Controllers\Admin\MenuController::class, 'update'])->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\MenuController::class, 'destroy'])->name('destroy');
            Route::get('/{id}', [App\Http\Controllers\Admin\MenuController::class, 'show'])->name('show');
            Route::post('/{id}/itens', [App\Http\Controllers\Admin\MenuController::class, 'addItem'])->name('items.store');
            Route::post('/itens/{itemId}/atualizar', [App\Http\Controllers\Admin\MenuController::class, 'updateItem'])->name('items.update');
            Route::delete('/itens/{itemId}/excluir', [App\Http\Controllers\Admin\MenuController::class, 'deleteItem'])->name('items.destroy');
            Route::post('/reordenar', [App\Http\Controllers\Admin\MenuController::class, 'reorderItems'])->name('reorder');

            // Aliases para rotas usadas nas views
            Route::get('/itens/{itemId}', [App\Http\Controllers\Admin\MenuController::class, 'showItem'])->name('item.show');
            Route::post('/itens/{itemId}/excluir', [App\Http\Controllers\Admin\MenuController::class, 'deleteItem'])->name('item.destroy');
            Route::post('/itens/{itemId}/atualizar', [App\Http\Controllers\Admin\MenuController::class, 'updateItem'])->name('item.update');
            Route::post('/itens/criar', [App\Http\Controllers\Admin\MenuController::class, 'addItem'])->name('item.store');
        });

        // Modulos
        Route::prefix('modulos')->name('modules.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ModuleController::class, 'index'])->name('index');
            Route::post('/{id}/alternar', [App\Http\Controllers\Admin\ModuleController::class, 'toggle'])->name('toggle');
            Route::post('/{id}/configurar', [App\Http\Controllers\Admin\ModuleController::class, 'config'])->name('config');
            Route::post('/{id}/atualizar', [App\Http\Controllers\Admin\ModuleController::class, 'update'])->name('update');
        });

        // SEO
        Route::prefix('seo')->name('seo.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SeoController::class, 'index'])->name('index');
            Route::post('/analisar', [App\Http\Controllers\Admin\SeoController::class, 'analyze'])->name('analyze');
            Route::post('/gerar-sitemap', [App\Http\Controllers\Admin\SeoController::class, 'generateSitemap'])->name('sitemap');
            Route::post('/atualizar-robots', [App\Http\Controllers\Admin\SeoController::class, 'updateRobotsTxt'])->name('robots');
            Route::post('/preview-social', [App\Http\Controllers\Admin\SeoController::class, 'previewSocial'])->name('preview-social');
        });

        // Hashtags
        Route::prefix('hashtags')->name('hashtags.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\HashtagController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\HashtagController::class, 'list'])->name('list');
            Route::post('/criar', [App\Http\Controllers\Admin\HashtagController::class, 'store'])->name('store');
            Route::post('/{id}/atualizar', [App\Http\Controllers\Admin\HashtagController::class, 'update'])->name('update');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\HashtagController::class, 'destroy'])->name('destroy');
            Route::get('/buscar', [App\Http\Controllers\Admin\HashtagController::class, 'search'])->name('search');
        });

        // Notificacoes
        Route::prefix('notificacoes')->name('notificacoes.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\NotificacaoController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\NotificacaoController::class, 'list'])->name('list');
            Route::get('/poll', [App\Http\Controllers\Admin\NotificacaoController::class, 'poll'])->name('poll');
            Route::post('/{id}/marcar-lida', [App\Http\Controllers\Admin\NotificacaoController::class, 'markAsRead'])->name('mark-read');
            Route::post('/marcar-todas-lidas', [App\Http\Controllers\Admin\NotificacaoController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}/excluir', [App\Http\Controllers\Admin\NotificacaoController::class, 'destroy'])->name('destroy');
            Route::get('/nao-lidas', [App\Http\Controllers\Admin\NotificacaoController::class, 'getUnreadCount'])->name('unread-count');
        });

        // Aliases para rotas usadas nas views (fora dos grupos prefixados)
        Route::get('midia', [App\Http\Controllers\Admin\MediaController::class, 'index'])->name('midia.index');
        Route::get('contato', [App\Http\Controllers\Admin\ContatoController::class, 'index'])->name('contato.index');
        Route::get('contato/data', [App\Http\Controllers\Admin\ContatoController::class, 'list'])->name('contato.data');
        Route::get('contato/{id}', [App\Http\Controllers\Admin\ContatoController::class, 'show'])->name('contato.show');
        Route::post('contato/{id}/responder', [App\Http\Controllers\Admin\ContatoController::class, 'reply'])->name('contato.reply');
        Route::post('contato/{id}/marcar-lido', [App\Http\Controllers\Admin\ContatoController::class, 'markRead'])->name('contato.mark-read');
        Route::post('contato/marcar-todos-lidos', [App\Http\Controllers\Admin\ContatoController::class, 'markAllRead'])->name('contato.mark-all-read');
        Route::delete('contato/{id}/excluir', [App\Http\Controllers\Admin\ContatoController::class, 'destroy'])->name('contato.destroy');
        Route::delete('contato/lidos/excluir', [App\Http\Controllers\Admin\ContatoController::class, 'deleteRead'])->name('contato.delete-read');

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
