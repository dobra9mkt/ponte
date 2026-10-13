<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SomenteCliente
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario === null) {
            return redirect()->guest(route('entrar'));
        }

        if ($usuario->ehEquipe()) {
            return redirect()->route('equipe.painel');
        }

        if (! $usuario->podeEntrar()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('entrar');
        }

        if ($usuario->boas_vindas_vistas_em === null && ! $request->routeIs('boas-vindas*', 'sair')) {
            return redirect()->route('boas-vindas');
        }

        return $next($request);
    }
}
