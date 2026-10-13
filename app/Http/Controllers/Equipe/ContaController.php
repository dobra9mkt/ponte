<?php

namespace App\Http\Controllers\Equipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmarSenhaRequest;
use App\Http\Requests\TrocarSenhaRequest;
use App\Models\Atividade;
use App\Support\Sessoes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;

class ContaController extends Controller
{
    public function show(Request $request): View
    {
        $usuario = $request->user();

        return view('equipe.conta', [
            'usuario' => $usuario,
            'codigos' => session('mostrar_codigos') ? $usuario->recoveryCodes() : null,
            'acessos' => Atividade::query()->with('usuario')
                ->where('user_id', $usuario->id)
                ->whereIn('acao', ['entrada', 'login.falhou'])
                ->latest('id')
                ->limit(10)
                ->get(),
        ]);
    }

    public function senha(TrocarSenhaRequest $request, Sessoes $sessoes): RedirectResponse
    {
        $usuario = $request->user();
        $usuario->forceFill(['password' => $request->validated('password')])->save();
        $sessoes->encerrarOutras($usuario);
        Atividade::registrar('senha.trocada');

        return back()->with('sucesso', __('ponte.senha.trocada'));
    }

    public function codigos(ConfirmarSenhaRequest $request): RedirectResponse
    {
        return back()->with('mostrar_codigos', true);
    }

    public function novosCodigos(ConfirmarSenhaRequest $request, GenerateNewRecoveryCodes $gerar): RedirectResponse
    {
        $gerar($request->user());
        Atividade::registrar('2fa.novos_codigos');

        return back()->with('mostrar_codigos', true);
    }

    public function trocarAplicativo(ConfirmarSenhaRequest $request): RedirectResponse
    {
        $request->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        Atividade::registrar('2fa.reiniciado');

        return redirect()->route('equipe.2fa.configurar');
    }
}
