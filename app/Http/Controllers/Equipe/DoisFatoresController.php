<?php

namespace App\Http\Controllers\Equipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\CodigoDoisFatoresRequest;
use App\Models\Atividade;
use App\Models\User;
use App\Support\Autenticador;
use App\Support\LimiteLogin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;

class DoisFatoresController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($this->usuarioPendente($request) === null) {
            return redirect()->route('equipe.entrar');
        }

        return view('equipe.dois-fatores');
    }

    public function store(CodigoDoisFatoresRequest $request, LimiteLogin $limite, Autenticador $autenticador, TwoFactorAuthenticationProvider $provedor): RedirectResponse
    {
        $usuario = $this->usuarioPendente($request);

        if ($usuario === null) {
            return redirect()->route('equipe.entrar');
        }

        $chave = '2fa:'.$usuario->id;

        if ($segundos = $limite->segundosBloqueado($chave)) {
            throw ValidationException::withMessages(['codigo' => __('ponte.login.bloqueado', ['segundos' => $segundos])]);
        }

        if (! $this->codigoConfere($usuario, $request, $provedor)) {
            $limite->falhou($chave);
            Atividade::registrar('login.falhou', dados: ['area' => '2fa'], usuario: $usuario);

            throw ValidationException::withMessages(['codigo' => __('ponte.equipe.codigo_erro')]);
        }

        $limite->limpar($chave);
        $request->session()->forget('equipe_2fa');
        $autenticador->entrar($usuario);

        return redirect()->intended(route('equipe.painel'));
    }

    private function usuarioPendente(Request $request): ?User
    {
        $pendente = $request->session()->get('equipe_2fa');

        if (! is_array($pendente) || ($pendente['expira'] ?? 0) < now()->getTimestamp()) {
            return null;
        }

        $usuario = User::find($pendente['id'] ?? null);

        return $usuario?->ehEquipe() && $usuario->podeEntrar() && $usuario->hasEnabledTwoFactorAuthentication() ? $usuario : null;
    }

    private function codigoConfere(User $usuario, CodigoDoisFatoresRequest $request, TwoFactorAuthenticationProvider $provedor): bool
    {
        if ($codigo = $request->validated('codigo')) {
            return $provedor->verify(Fortify::currentEncrypter()->decrypt($usuario->two_factor_secret), preg_replace('/\D/', '', $codigo));
        }

        $recuperacao = trim((string) $request->validated('codigo_recuperacao'));

        foreach ($usuario->recoveryCodes() as $valido) {
            if ($recuperacao !== '' && hash_equals($valido, $recuperacao)) {
                $usuario->replaceRecoveryCode($valido);

                return true;
            }
        }

        return false;
    }
}
