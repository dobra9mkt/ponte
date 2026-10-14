<?php

use App\Http\Controllers\ArquivoController;
use App\Http\Controllers\Cliente;
use App\Http\Controllers\Equipe;
use App\Models\Modelo;
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
    Route::get('/servicos/{frente}', Cliente\FrenteController::class)->can('view', 'frente')->name('frentes.ver');
    Route::get('/entregas/{entrega}', [Cliente\EntregaController::class, 'show'])->can('view', 'entrega')->name('entregas.ver');
    Route::post('/entregas/{entrega}/aprovar', [Cliente\EntregaController::class, 'aprovar'])->can('responder', 'entrega')->name('entregas.aprovar');
    Route::post('/entregas/{entrega}/ajustes', [Cliente\EntregaController::class, 'ajustes'])->can('responder', 'entrega')->name('entregas.ajustes');
    Route::put('/entregas/{entrega}/rascunho', [Cliente\EntregaController::class, 'rascunho'])->can('responder', 'entrega')->name('entregas.rascunho');
    Route::get('/arquivos/{arquivo}', ArquivoController::class)->can('view', 'arquivo')->name('arquivos.ver');

    Route::get('/conta', [Cliente\ContaController::class, 'show'])->name('conta');
    Route::put('/conta/senha', [Cliente\ContaController::class, 'senha'])->name('conta.senha');
    Route::get('/conta/contrato', [Cliente\ContaController::class, 'contrato'])->middleware('throttle:10,1')->name('conta.contrato');
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
            Route::delete('/clientes/{cliente}', [Equipe\ClienteController::class, 'destroy'])->can('delete', 'cliente')->name('clientes.destroy');
            Route::post('/clientes/{cliente}/enviar-acesso', [Equipe\ClienteAcessoController::class, 'enviar'])->can('enviarAcesso', 'cliente')->name('clientes.enviar-acesso');
            Route::post('/clientes/{cliente}/desativar', [Equipe\ClienteAcessoController::class, 'desativar'])->can('alterarSituacao', 'cliente')->name('clientes.desativar');
            Route::post('/clientes/{cliente}/reativar', [Equipe\ClienteAcessoController::class, 'reativar'])->can('alterarSituacao', 'cliente')->name('clientes.reativar');
            Route::get('/clientes/{cliente}/contrato', [Equipe\ClienteAcessoController::class, 'contrato'])->can('baixarContrato', 'cliente')->name('clientes.contrato');

            Route::scopeBindings()->prefix('/clientes/{cliente}/servicos')->group(function () {
                Route::post('/', [Equipe\FrenteController::class, 'store'])->can('update', 'cliente')->name('frentes.store');
                Route::get('/{frente}', [Equipe\FrenteController::class, 'show'])->can('view', 'frente')->name('frentes.ver');
                Route::put('/{frente}', [Equipe\FrenteController::class, 'update'])->can('update', 'frente')->name('frentes.update');
                Route::post('/{frente}/visibilidade', [Equipe\FrenteController::class, 'visibilidade'])->can('update', 'frente')->name('frentes.visibilidade');
                Route::delete('/{frente}', [Equipe\FrenteController::class, 'destroy'])->can('delete', 'frente')->name('frentes.destroy');
                Route::post('/{frente}/fases', [Equipe\EtapaController::class, 'store'])->can('update', 'frente')->name('etapas.store');
            });

            Route::put('/fases/{etapa}', [Equipe\EtapaController::class, 'update'])->can('update', 'etapa')->name('etapas.update');
            Route::delete('/fases/{etapa}', [Equipe\EtapaController::class, 'destroy'])->can('delete', 'etapa')->name('etapas.destroy');
            Route::post('/fases/{etapa}/mover', [Equipe\EtapaController::class, 'mover'])->can('update', 'etapa')->name('etapas.mover');
            Route::post('/fases/{etapa}/visibilidade', [Equipe\EtapaController::class, 'visibilidade'])->can('update', 'etapa')->name('etapas.visibilidade');
            Route::post('/fases/{etapa}/iniciar', [Equipe\EtapaController::class, 'iniciar'])->can('iniciar', 'etapa')->name('etapas.iniciar');
            Route::post('/fases/{etapa}/concluir', [Equipe\EtapaController::class, 'concluir'])->can('concluir', 'etapa')->name('etapas.concluir');
            Route::post('/fases/{etapa}/aprovacoes', [Equipe\AprovacaoController::class, 'store'])->can('adicionarAprovacao', 'etapa')->name('aprovacoes.store');

            Route::put('/aprovacoes/{entrega}', [Equipe\AprovacaoController::class, 'update'])->can('editar', 'entrega')->name('aprovacoes.update');
            Route::delete('/aprovacoes/{entrega}', [Equipe\AprovacaoController::class, 'destroy'])->can('editar', 'entrega')->name('aprovacoes.destroy');
            Route::put('/aprovacoes/{entrega}/prazo', [Equipe\AprovacaoController::class, 'prazo'])->can('alterarPrazo', 'entrega')->name('aprovacoes.prazo');
            Route::get('/arquivos/{arquivo}', ArquivoController::class)->can('view', 'arquivo')->name('arquivos.ver');

            Route::get('/consulta', [Equipe\ConsultaController::class, 'index'])->can('viewAny', App\Models\Cliente::class)->name('consulta.index');
            Route::get('/consulta/{cliente}', [Equipe\ConsultaController::class, 'show'])->can('view', 'cliente')->name('consulta.ver');

            Route::get('/servicos', [Equipe\ModeloController::class, 'index'])->can('viewAny', Modelo::class)->name('servicos.index');
            Route::post('/servicos', [Equipe\ModeloController::class, 'store'])->can('create', Modelo::class)->name('servicos.store');
            Route::get('/servicos/{modelo}', [Equipe\ModeloController::class, 'show'])->can('update', 'modelo')->name('servicos.ver');
            Route::put('/servicos/{modelo}', [Equipe\ModeloController::class, 'update'])->can('update', 'modelo')->name('servicos.update');
            Route::delete('/servicos/{modelo}', [Equipe\ModeloController::class, 'destroy'])->can('delete', 'modelo')->name('servicos.destroy');
            Route::post('/servicos/{modelo}/fases', [Equipe\ModeloEtapaController::class, 'store'])->can('update', 'modelo')->name('servicos.fases.store');
            Route::put('/servicos-fases/{modeloEtapa}', [Equipe\ModeloEtapaController::class, 'update'])->can('update', 'modeloEtapa')->name('servicos.fases.update');
            Route::delete('/servicos-fases/{modeloEtapa}', [Equipe\ModeloEtapaController::class, 'destroy'])->can('delete', 'modeloEtapa')->name('servicos.fases.destroy');
            Route::post('/servicos-fases/{modeloEtapa}/mover', [Equipe\ModeloEtapaController::class, 'mover'])->can('update', 'modeloEtapa')->name('servicos.fases.mover');

            Route::get('/lixeira', [Equipe\LixeiraController::class, 'index'])->can('viewAny', App\Models\Cliente::class)->name('lixeira');
            Route::post('/lixeira/clientes/{cliente}', [Equipe\LixeiraController::class, 'cliente'])->withTrashed()->can('restore', 'cliente')->name('lixeira.cliente');
            Route::post('/lixeira/servicos/{frente}', [Equipe\LixeiraController::class, 'servico'])->withTrashed()->can('restore', 'frente')->name('lixeira.servico');
            Route::post('/lixeira/fases/{etapa}', [Equipe\LixeiraController::class, 'fase'])->withTrashed()->can('restore', 'etapa')->name('lixeira.fase');
            Route::delete('/lixeira', [Equipe\LixeiraController::class, 'esvaziar'])->can('viewAny', App\Models\Cliente::class)->name('lixeira.esvaziar');
            Route::delete('/lixeira/clientes/{cliente}', [Equipe\LixeiraController::class, 'descartarCliente'])->withTrashed()->can('restore', 'cliente')->name('lixeira.cliente.descartar');
            Route::delete('/lixeira/servicos/{frente}', [Equipe\LixeiraController::class, 'descartarServico'])->withTrashed()->can('restore', 'frente')->name('lixeira.servico.descartar');
            Route::delete('/lixeira/fases/{etapa}', [Equipe\LixeiraController::class, 'descartarFase'])->withTrashed()->can('restore', 'etapa')->name('lixeira.fase.descartar');

            Route::get('/conta', [Equipe\ContaController::class, 'show'])->name('conta');
            Route::put('/conta/senha', [Equipe\ContaController::class, 'senha'])->name('conta.senha');
            Route::post('/conta/codigos', [Equipe\ContaController::class, 'codigos'])->name('conta.codigos');
            Route::post('/conta/novos-codigos', [Equipe\ContaController::class, 'novosCodigos'])->name('conta.novos-codigos');
            Route::post('/conta/trocar-aplicativo', [Equipe\ContaController::class, 'trocarAplicativo'])->name('conta.trocar-aplicativo');
        });
    });
}
