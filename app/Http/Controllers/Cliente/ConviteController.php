<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\NovaSenhaRequest;
use App\Models\Atividade;
use App\Models\Convite;
use App\Models\User;
use App\Support\Autenticador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ConviteController extends Controller
{
    public function create(string $token): View
    {
        $convite = Convite::validoPeloToken($token);

        if ($convite === null) {
            return view('cliente.convite-invalido');
        }

        return view('cliente.convite', [
            'token' => $token,
            'empresa' => $convite->usuario->cliente?->empresa,
        ]);
    }

    public function store(NovaSenhaRequest $request, string $token, Autenticador $autenticador): RedirectResponse|View
    {
        $usuario = DB::transaction(function () use ($token, $request): ?User {
            $convite = Convite::validoPeloToken($token);
            $convite = $convite ? Convite::query()->whereKey($convite->id)->whereNull('usado_em')->lockForUpdate()->first() : null;

            if ($convite === null) {
                return null;
            }

            $convite->forceFill(['usado_em' => now()])->save();
            $usuario = $convite->usuario;
            $usuario->forceFill(['password' => $request->validated('password')])->save();

            return $usuario;
        });

        if ($usuario === null || ! $usuario->podeEntrar()) {
            return view('cliente.convite-invalido');
        }

        Atividade::registrar('acesso.ativado', usuario: $usuario);
        $autenticador->entrar($usuario);

        return redirect()->route('boas-vindas');
    }
}
