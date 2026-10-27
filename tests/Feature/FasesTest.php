<?php

use App\Enums\EstadoEtapa;
use App\Models\Atividade;
use App\Models\Cliente;
use App\Models\Etapa;
use App\Models\Frente;
use App\Models\Modelo;
use App\Models\ModeloEtapa;

beforeEach(function () {
    $this->cliente = Cliente::factory()->create();
});

it('RN-10: cadastrar serviço traz as fases do fluxo padrão', function () {
    actingAsEquipe();
    $modelo = Modelo::query()->where('slug', 'trafego-pago')->sole();

    $this->post(rotaEquipe('frentes.store', $this->cliente), ['modelo_id' => $modelo->id])->assertRedirect();

    $frente = $this->cliente->frentes()->sole();
    expect($frente)->nome->toBe('Tráfego pago (Google e Meta Ads)')->modelo_id->toBe($modelo->id)
        ->and($frente->etapas->pluck('titulo')->all())->toBe([
            'Planejamento', 'Criação dos anúncios', 'Aprovação do cliente', 'Configuração das campanhas e conversões', 'Lançamento',
        ])
        ->and($frente->etapas->every(fn (Etapa $fase) => $fase->estado === EstadoEtapa::AFazer && $fase->termino_em === null))->toBeTrue();

    $this->get(rotaEquipe('frentes.ver', [$this->cliente, $frente]))->assertOk()
        ->assertSee('Criação dos anúncios')->assertSee(__('equipe.adicionar_aprovacao'));
});

it('RN-10: "Outro" cria um serviço fora do escopo, sem fases, pelo nome', function () {
    actingAsEquipe();

    $this->post(rotaEquipe('frentes.store', $this->cliente), ['modelo_id' => ''])->assertSessionHasErrors('nome');
    $this->post(rotaEquipe('frentes.store', $this->cliente), ['modelo_id' => '', 'nome' => 'Consultoria de marca'])->assertRedirect();

    $frente = $this->cliente->frentes()->sole();
    expect($frente)->nome->toBe('Consultoria de marca')->modelo_id->toBeNull()
        ->and($frente->etapas)->toBeEmpty();
});

it('RN-10: serviço desativado não pode ser cadastrado', function () {
    actingAsEquipe();
    $modelo = Modelo::query()->firstOrFail();
    $modelo->delete();

    $this->post(rotaEquipe('frentes.store', $this->cliente), ['modelo_id' => $modelo->id])->assertSessionHasErrors('modelo_id');
});

it('RN-13: com início e duração, as fases recebem datas em dias úteis', function () {
    $modelo = Modelo::query()->where('slug', 'trafego-pago')->sole();
    $modelo->etapas->each(fn (ModeloEtapa $fase) => $fase->update(['duracao_dias_uteis' => 5]));

    $frente = servicoDoCliente($this->cliente, 'trafego-pago', '2026-11-13');
    $primeira = $frente->etapas->first();
    $segunda = $frente->etapas->get(1);

    expect($primeira->entrada_em->toDateString())->toBe('2026-11-13')
        ->and($primeira->termino_em->toDateString())->toBe('2026-11-23')
        ->and($segunda->entrada_em->toDateString())->toBe('2026-11-23')
        ->and($frente->prazo_final)->toBeNull();
});

it('RN-11: editar a fase do cliente não muda o fluxo padrão', function () {
    actingAsEquipe();
    $frente = servicoDoCliente($this->cliente);
    $fase = $frente->etapas->first();

    $this->put(rotaEquipe('etapas.update', $fase), [
        'titulo' => 'Pesquisa de concorrentes', 'responsavel' => 'Ana (freelancer)', 'o_que_fazer' => 'Levantar 5 sites',
        'entrada_em' => '2026-11-03', 'termino_em' => '2026-11-06',
    ])->assertRedirect(rotaEquipe('frentes.ver', [$this->cliente, $frente]).'#fase-'.$fase->id);

    expect($fase->fresh())->titulo->toBe('Pesquisa de concorrentes')->responsavel->toBe('Ana (freelancer)')
        ->and(ModeloEtapa::query()->where('titulo', 'Pesquisa')->exists())->toBeTrue();
});

it('RN-12: término antes da entrada é recusado', function () {
    actingAsEquipe();
    $fase = servicoDoCliente($this->cliente)->etapas->first();

    $this->put(rotaEquipe('etapas.update', $fase), ['titulo' => 'Pesquisa', 'entrada_em' => '2026-11-10', 'termino_em' => '2026-11-03'])
        ->assertSessionHasErrors('termino_em');
});

it('RN-13: mudar o término move as fases seguintes os mesmos dias úteis', function () {
    actingAsEquipe();
    $frente = Frente::factory()->create(['cliente_id' => $this->cliente->id]);
    $primeira = Etapa::factory()->create(['frente_id' => $frente->id, 'ordem' => 1, 'entrada_em' => '2026-11-03', 'termino_em' => '2026-11-05']);
    $segunda = Etapa::factory()->create(['frente_id' => $frente->id, 'ordem' => 2, 'entrada_em' => '2026-11-05', 'termino_em' => '2026-11-10']);

    $this->put(rotaEquipe('etapas.update', $primeira), ['titulo' => $primeira->titulo, 'entrada_em' => '2026-11-03', 'termino_em' => '2026-11-06']);

    expect($segunda->fresh()->entrada_em->toDateString())->toBe('2026-11-06')
        ->and($segunda->fresh()->termino_em->toDateString())->toBe('2026-11-11');
});

it('adicionar, iniciar e concluir uma fase', function () {
    actingAsEquipe();
    $frente = servicoDoCliente($this->cliente);

    $this->post(rotaEquipe('etapas.store', [$this->cliente, $frente]), ['titulo' => 'Treinamento', 'responsavel' => 'Elias'])->assertRedirect();
    $nova = $frente->etapas()->where('titulo', 'Treinamento')->sole();
    expect($nova->ordem)->toBe(7);

    $this->post(rotaEquipe('etapas.concluir', $nova))->assertForbidden();
    $this->post(rotaEquipe('etapas.iniciar', $nova))->assertRedirect();
    $this->post(rotaEquipe('etapas.concluir', $nova))->assertRedirect();
    expect($nova->fresh()->estado)->toBe(EstadoEtapa::Concluida);
    $this->post(rotaEquipe('etapas.iniciar', $nova))->assertForbidden();
});

it('RN-14a: fase concluída por engano reabre e volta para A fazer, com registro no histórico', function () {
    actingAsEquipe();
    $fase = servicoDoCliente($this->cliente)->etapas()->firstOrFail();
    $this->post(rotaEquipe('etapas.iniciar', $fase));
    $this->post(rotaEquipe('etapas.concluir', $fase));

    $this->post(rotaEquipe('etapas.voltar-a-fazer', $fase))->assertForbidden();
    $this->post(rotaEquipe('etapas.reabrir', $fase))->assertRedirect();
    expect($fase->fresh()->estado)->toBe(EstadoEtapa::EmProducao);

    $this->post(rotaEquipe('etapas.reabrir', $fase))->assertForbidden();
    $this->post(rotaEquipe('etapas.voltar-a-fazer', $fase))->assertRedirect();
    expect($fase->fresh()->estado)->toBe(EstadoEtapa::AFazer);

    $this->post(rotaEquipe('etapas.voltar-a-fazer', $fase))->assertForbidden();
    expect(Atividade::query()->where('acao', 'etapa.reaberta')->exists())->toBeTrue()
        ->and(Atividade::query()->where('acao', 'etapa.voltou_a_fazer')->exists())->toBeTrue();
});

it('RN-14a: fase com aprovação já mostrada ao cliente não volta para A fazer, mas reabre', function () {
    $fase = servicoDoCliente($this->cliente)->etapas()->firstOrFail();
    cardMostrado($fase);
    $fase->forceFill(['estado' => EstadoEtapa::EmProducao])->save();

    $this->post(rotaEquipe('etapas.voltar-a-fazer', $fase))->assertForbidden();
    expect($fase->fresh()->estado)->toBe(EstadoEtapa::EmProducao);

    $fase->forceFill(['estado' => EstadoEtapa::Concluida])->save();
    $this->post(rotaEquipe('etapas.reabrir', $fase))->assertRedirect();
    expect($fase->fresh()->estado)->toBe(EstadoEtapa::EmProducao);
});

it('RN-14a: fase só com rascunho de aprovação ainda volta para A fazer', function () {
    $fase = servicoDoCliente($this->cliente)->etapas()->firstOrFail();
    cardEmRascunho($fase);
    $this->post(rotaEquipe('etapas.iniciar', $fase));

    $this->post(rotaEquipe('etapas.voltar-a-fazer', $fase))->assertRedirect();
    expect($fase->fresh()->estado)->toBe(EstadoEtapa::AFazer);
});

it('RN-14a: os botões de voltar aparecem só quando valem', function () {
    actingAsEquipe();
    $frente = servicoDoCliente($this->cliente);
    $fase = $frente->etapas()->firstOrFail();
    $tela = fn () => $this->get(rotaEquipe('frentes.ver', [$this->cliente, $frente]));

    $tela()->assertDontSee(rotaEquipe('etapas.reabrir', $fase))->assertDontSee(rotaEquipe('etapas.voltar-a-fazer', $fase));
    $this->post(rotaEquipe('etapas.iniciar', $fase));
    $tela()->assertSee(rotaEquipe('etapas.voltar-a-fazer', $fase))->assertDontSee(rotaEquipe('etapas.reabrir', $fase));
    $this->post(rotaEquipe('etapas.concluir', $fase));
    $tela()->assertSee(rotaEquipe('etapas.reabrir', $fase))->assertDontSee(rotaEquipe('etapas.voltar-a-fazer', $fase));
});

it('RN-82: excluir fase manda para a lixeira mantendo a ordem, mesmo com aprovação publicada', function () {
    actingAsEquipe();
    $frente = servicoDoCliente($this->cliente);
    [$primeira, $segunda] = $frente->etapas->take(2)->all();
    cardMostrado($segunda);

    $this->delete(rotaEquipe('etapas.destroy', $primeira))->assertRedirect();
    $this->delete(rotaEquipe('etapas.destroy', $segunda))->assertRedirect();

    expect(Etapa::withTrashed()->find($primeira->id)->trashed())->toBeTrue()
        ->and(Etapa::withTrashed()->find($segunda->id)->trashed())->toBeTrue()
        ->and($frente->etapas()->count())->toBe(4);
});

it('a rota do serviço exige o cliente certo na URL', function () {
    actingAsEquipe();
    $frente = servicoDoCliente($this->cliente);
    $outro = Cliente::factory()->create();

    $this->get(rotaEquipe('frentes.ver', [$outro, $frente]))->assertNotFound();
});

it('RN-13: o serviço tem início e término próprios, separados das fases', function () {
    actingAsEquipe();
    $modelo = Modelo::query()->where('slug', 'criacao-de-sites')->sole();

    $this->post(rotaEquipe('frentes.store', $this->cliente), ['modelo_id' => $modelo->id, 'inicio_em' => '2026-11-02', 'prazo_final' => '2026-10-01'])
        ->assertSessionHasErrors('prazo_final');
    $this->post(rotaEquipe('frentes.store', $this->cliente), ['modelo_id' => $modelo->id, 'inicio_em' => '2026-11-02', 'prazo_final' => '2026-12-18']);
    $frente = $this->cliente->frentes()->sole();
    $pesquisa = $frente->etapas()->firstOrFail();

    $this->put(rotaEquipe('etapas.update', $pesquisa), ['titulo' => 'Pesquisa', 'entrada_em' => '2026-11-02', 'termino_em' => '2027-02-28']);
    expect($frente->fresh()->prazo_final->toDateString())->toBe('2026-12-18');

    $this->put(rotaEquipe('frentes.update', [$this->cliente, $frente]), ['nome' => $frente->nome, 'inicio_em' => '2026-11-09', 'prazo_final' => '2027-01-29']);
    expect($frente->fresh())->inicio_em->toDateString()->toBe('2026-11-09')->prazo_final->toDateString()->toBe('2027-01-29')
        ->and($pesquisa->fresh()->entrada_em->toDateString())->toBe('2026-11-02')
        ->and($pesquisa->fresh()->termino_em->toDateString())->toBe('2027-02-28');
});
