<?php

use App\Http\Middleware\CabecalhosSeguranca;
use App\Http\Middleware\DoisFatoresObrigatorio;
use App\Http\Middleware\SomenteCliente;
use App\Http\Middleware\SomenteEquipe;
use App\Http\Middleware\VisitanteEquipe;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(CabecalhosSeguranca::class);

        $middleware->alias([
            'equipe' => SomenteEquipe::class,
            'visitante.equipe' => VisitanteEquipe::class,
            '2fa.obrigatorio' => DoisFatoresObrigatorio::class,
            'cliente' => SomenteCliente::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('entrar'));
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()?->ehEquipe() ? route('equipe.painel') : route('inicio'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['password', 'password_confirmation', 'senha_atual', 'senha_confirmacao', 'codigo', 'codigo_recuperacao']);
    })->create();
