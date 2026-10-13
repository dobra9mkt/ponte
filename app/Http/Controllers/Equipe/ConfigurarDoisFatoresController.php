<?php

namespace App\Http\Controllers\Equipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\CodigoDoisFatoresRequest;
use App\Models\Atividade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Fortify;

class ConfigurarDoisFatoresController extends Controller
{
    public function create(Request $request, EnableTwoFactorAuthentication $ativar): View|RedirectResponse
    {
        $usuario = $request->user();

        if ($usuario->hasEnabledTwoFactorAuthentication()) {
            return redirect()->route('equipe.painel');
        }

        if (empty($usuario->two_factor_secret)) {
            $ativar($usuario);
        }

        return view('equipe.configurar-2fa', [
            'qr' => 'data:image/svg+xml;base64,'.base64_encode($usuario->twoFactorQrCodeSvg()),
            'chave' => Fortify::currentEncrypter()->decrypt($usuario->two_factor_secret),
        ]);
    }

    public function store(CodigoDoisFatoresRequest $request, ConfirmTwoFactorAuthentication $confirmar): RedirectResponse
    {
        try {
            $confirmar($request->user(), preg_replace('/\D/', '', (string) $request->validated('codigo')));
        } catch (ValidationException) {
            throw ValidationException::withMessages(['codigo' => __('ponte.equipe.codigo_erro')]);
        }

        Atividade::registrar('2fa.configurado');

        return redirect()->route('equipe.conta')->with('mostrar_codigos', true);
    }
}
