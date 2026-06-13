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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\License\LicenseService;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function __construct(
        protected LicenseService $licenseService,
    ) {}

    public function index()
    {
        $status = $this->licenseService->getStatus();
        $license = (object) $status;

        return view('admin.license.index', compact('license'));
    }

    /**
     * Show public license activation form (no auth required).
     */
    public function showActivationForm()
    {
        return view('admin.license.activate');
    }

    /**
     * Public license activation (no auth required).
     */
    public function activatePublic(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'license_key' => 'required|string|max:255',
        ]);

        $result = $this->licenseService->activate(
            $validated['license_key'],
            $validated['client_name'],
            $validated['client_email'] ?? null,
        );

        if ($result['success']) {
            return redirect()->route('admin.login')
                ->with('success', 'Licença ativada com sucesso! Faça login para continuar.');
        }

        return redirect()->route('admin.license.activate-form')
            ->with('error', $result['message'] ?? 'Erro ao ativar licença.');
    }

    public function activate(Request $request)
    {
        try {
            $validated = $request->validate([
                'license_key' => 'required|string|max:255',
                'client_name' => 'required|string|max:255',
                'client_email' => 'nullable|email|max:255',
            ]);

            $result = $this->licenseService->activate(
                $validated['license_key'],
                $validated['client_name'] ?? null,
                $validated['client_email'] ?? null,
            );

            return response()->json([
                'success' => $result['success'],
                'status' => $result['success'] ? 'success' : 'error',
                'message' => $result['message'],
                'data' => $result['data'] ?? null,
                'reload' => $result['success'],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao ativar licença: ' . $e->getMessage()], 500);
        }
    }

    public function deactivate()
    {
        try {
            $result = $this->licenseService->deactivate();

            return response()->json([
                'success' => $result['success'],
                'status' => $result['success'] ? 'success' : 'error',
                'message' => $result['message'],
                'reload' => $result['success'],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao desativar licença: ' . $e->getMessage()], 500);
        }
    }

    public function verify()
    {
        try {
            $result = $this->licenseService->verify(true);

            return response()->json([
                'success' => $result['valid'],
                'status' => $result['valid'] ? 'success' : 'error',
                'message' => $result['message'],
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao verificar licença: ' . $e->getMessage()], 500);
        }
    }

    public function checkUpdates()
    {
        try {
            $result = $this->licenseService->checkForUpdates();

            return response()->json([
                'success' => true,
                'status' => 'success',
                'data' => $result,
                'message' => $result['has_update'] ? 'Atualização disponível!' : 'Nenhuma atualização disponível.',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao verificar atualizações: ' . $e->getMessage()], 500);
        }
    }

    public function applyUpdate()
    {
        try {
            $result = $this->licenseService->applyUpdate();

            return response()->json([
                'success' => $result['success'],
                'status' => $result['success'] ? 'success' : 'error',
                'message' => $result['message'],
                'reload' => $result['success'],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao aplicar atualização: ' . $e->getMessage()], 500);
        }
    }
}
