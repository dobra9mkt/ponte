<?php

namespace App\Http\Controllers\Equipe;

use App\Enums\Papel;
use App\Http\Controllers\Controller;
use App\Http\Requests\EntrarRequest;
use App\Support\Autenticador;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EntrarController extends Controller
{
    public function create(): View
    {
        return view('equipe.entrar');
    }

    public function store(EntrarRequest $request, Autenticador $autenticador): RedirectResponse
    {
        $usuario = $autenticador->conferir($request->validated('email'), $request->validated('password'), Papel::Equipe);

        if ($usuario->hasEnabledTwoFactorAuthentication()) {
            $request->session()->regenerate();
            $request->session()->put('equipe_2fa', ['id' => $usuario->id, 'expira' => now()->addMinutes(5)->getTimestamp()]);

            return redirect()->route('equipe.2fa');
        }

        $autenticador->entrar($usuario);

        return redirect()->route('equipe.2fa.configurar');
    }
}
