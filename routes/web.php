<?php

use App\Http\Controllers\Cliente;
use App\Http\Controllers\Equipe;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/entrar', [Cliente\EntrarController::class, 'create'])->name('entrar');
    Route::post('/entrar', [Cliente\EntrarController::class, 'store']);

    Route::get('/esqueci-a-senha', [Cliente\SenhaController::class, 'pedir'])->name('senha.pedir');
    Route::post('/esqueci-a-senha', [Cliente\SenhaController::class, 'enviar'])->middleware('throttle:senha');
    Route::get('/nova-senha/{token}', [Cliente\SenhaController::class, 'redefinir'])->name('senha.redefinir');
    Route::post('/nova-senha', [Cliente\SenhaController::class, 'salvar'])->middleware('throttle:senha')->name('senha.salvar');

    Route::get('/convite/{token}', [Cliente\ConviteController::class, 'create'])->middleware('throttle:convite')->name('convite');
    Route::post('/convite/{token}', [Cliente\ConviteController::class, 'store'])->middleware('throttle:convite');
});

Route::post('/sair', [Cliente\EntrarController::class, 'destroy'])->middleware('auth')->name('sair');
Route::view('/privacidade', 'privacidade')->name('privacidade');

Route::middleware(['cliente', 'throttle:geral'])->group(function () {
    Route::get('/boas-vindas', [Cliente\BoasVindasController::class, 'create'])->name('boas-vindas');
    Route::post('/boas-vindas', [Cliente\BoasVindasController::class, 'store'])->name('boas-vindas.concluir');

    Route::get('/', Cliente\InicioController::class)->name('inicio');

    Route::get('/conta', [Cliente\ContaController::class, 'show'])->name('conta');
    Route::put('/conta/senha', [Cliente\ContaController::class, 'senha'])->name('conta.senha');
    Route::post('/conta/sair-de-todos', [Cliente\ContaController::class, 'sairDeTodos'])->name('conta.sair-todos');
});

if (filled(config('ponte.caminho_equipe'))) {
    Route::prefix(config('ponte.caminho_equipe'))->name('equipe.')->group(function () {
        Route::middleware('visitante.equipe')->group(function () {
            Route::get('/entrar', [Equipe\EntrarController::class, 'create'])->name('entrar');
            Route::post('/entrar', [Equipe\EntrarController::class, 'store']);
            Route::get('/dois-fatores', [Equipe\DoisFatoresController::class, 'create'])->name('2fa');
            Route::post('/dois-fatores', [Equipe\DoisFatoresController::class, 'store']);
        });

        Route::middleware(['equipe', 'throttle:geral'])->group(function () {
            Route::get('/configurar-2fa', [Equipe\ConfigurarDoisFatoresController::class, 'create'])->name('2fa.configurar');
            Route::post('/configurar-2fa', [Equipe\ConfigurarDoisFatoresController::class, 'store']);
        });

        Route::middleware(['equipe', '2fa.obrigatorio', 'throttle:geral'])->group(function () {
            Route::get('/', Equipe\PainelController::class)->name('painel');

            Route::get('/clientes', [Equipe\ClienteController::class, 'index'])->can('viewAny', App\Models\Cliente::class)->name('clientes.index');
            Route::get('/clientes/novo', [Equipe\ClienteController::class, 'create'])->can('create', App\Models\Cliente::class)->name('clientes.create');
            Route::post('/clientes', [Equipe\ClienteController::class, 'store'])->can('create', App\Models\Cliente::class)->name('clientes.store');
            Route::get('/clientes/{cliente}', [Equipe\ClienteController::class, 'show'])->can('view', 'cliente')->name('clientes.show');
            Route::get('/clientes/{cliente}/editar', [Equipe\ClienteController::class, 'edit'])->can('update', 'cliente')->name('clientes.edit');
            Route::put('/clientes/{cliente}', [Equipe\ClienteController::class, 'update'])->can('update', 'cliente')->name('clientes.update');
            Route::post('/clientes/{cliente}/enviar-acesso', [Equipe\ClienteAcessoController::class, 'enviar'])->can('enviarAcesso', 'cliente')->name('clientes.enviar-acesso');
            Route::post('/clientes/{cliente}/desativar', [Equipe\ClienteAcessoController::class, 'desativar'])->can('alterarSituacao', 'cliente')->name('clientes.desativar');
            Route::post('/clientes/{cliente}/reativar', [Equipe\ClienteAcessoController::class, 'reativar'])->can('alterarSituacao', 'cliente')->name('clientes.reativar');
            Route::get('/clientes/{cliente}/contrato', [Equipe\ClienteAcessoController::class, 'contrato'])->can('baixarContrato', 'cliente')->name('clientes.contrato');

            Route::get('/conta', [Equipe\ContaController::class, 'show'])->name('conta');
            Route::put('/conta/senha', [Equipe\ContaController::class, 'senha'])->name('conta.senha');
            Route::post('/conta/codigos', [Equipe\ContaController::class, 'codigos'])->name('conta.codigos');
            Route::post('/conta/novos-codigos', [Equipe\ContaController::class, 'novosCodigos'])->name('conta.novos-codigos');
            Route::post('/conta/trocar-aplicativo', [Equipe\ContaController::class, 'trocarAplicativo'])->name('conta.trocar-aplicativo');
        });
    });
}
