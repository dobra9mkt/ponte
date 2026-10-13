<?php

namespace App\Http\Controllers\Cliente;

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
        return view('cliente.entrar');
    }

    public function store(EntrarRequest $request, Autenticador $autenticador): RedirectResponse
    {
        $usuario = $autenticador->conferir($request->validated('email'), $request->validated('password'), Papel::Cliente);
        $autenticador->entrar($usuario, $request->boolean('lembrar'));

        return redirect()->intended(route('inicio'));
    }

    public function destroy(Autenticador $autenticador): RedirectResponse
    {
        $ehEquipe = auth()->user()?->ehEquipe();
        $autenticador->sair();

        return $ehEquipe ? redirect()->route('equipe.entrar') : redirect()->route('entrar');
    }
}
