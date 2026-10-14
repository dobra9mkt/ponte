<?php

use App\Acoes\CarregarServicosPadrao;
use App\Models\Modelo;
use App\Models\ModeloEtapa;

it('RN-10: os seis serviços vêm carregados com as fases definidas pela Rose', function () {
    expect(Modelo::query()->orderBy('nome')->pluck('nome')->all())->toBe([
        'Aplicativos Android e iPhone',
        'Criação de sites',
        'Segurança e remoção de vírus',
        'Servidores web Linux',
        'Sistemas sob medida',
        'Tráfego pago (Google e Meta Ads)',
    ]);

    $sites = Modelo::query()->where('slug', 'criacao-de-sites')->sole();
    expect($sites->etapas->pluck('titulo')->all())
        ->toBe(['Pesquisa', 'Copy', 'Preparo das imagens', 'Desenvolvimento', 'Aprovação do cliente', 'Entrega']);
});

it('RN-10a: carregar de novo não duplica nem desfaz o que a equipe editou ou desativou', function () {
    Modelo::query()->where('slug', 'trafego-pago')->sole()->update(['nome' => 'Tráfego pago']);
    Modelo::query()->where('slug', 'aplicativos')->sole()->delete();

    app(CarregarServicosPadrao::class)();

    expect(Modelo::withTrashed()->count())->toBe(6)
        ->and(Modelo::query()->where('slug', 'trafego-pago')->value('nome'))->toBe('Tráfego pago')
        ->and(Modelo::query()->where('slug', 'aplicativos')->exists())->toBeFalse();
});

it('RN-10a: a equipe cria um serviço novo e monta as fases dele', function () {
    actingAsEquipe();

    $this->post(rotaEquipe('servicos.store'), ['nome' => 'Identidade visual'])->assertRedirect();
    $modelo = Modelo::query()->where('nome', 'Identidade visual')->sole();

    $this->post(rotaEquipe('servicos.fases.store', $modelo), [
        'titulo' => 'Briefing', 'responsavel' => 'Rose', 'duracao_dias_uteis' => 3, 'o_que_fazer' => 'Conversa inicial',
    ])->assertRedirect();
    $this->post(rotaEquipe('servicos.fases.store', $modelo), ['titulo' => 'Proposta'])->assertRedirect();

    expect($modelo->etapas()->pluck('titulo')->all())->toBe(['Briefing', 'Proposta'])
        ->and($modelo->etapas()->first())->responsavel->toBe('Rose')->duracao_dias_uteis->toBe(3);

    $this->get(rotaEquipe('servicos.ver', $modelo))->assertOk()->assertSee('Briefing')->assertSee('Conversa inicial');
});

it('RN-10a: editar, reordenar e remover fases do serviço padrão', function () {
    actingAsEquipe();
    $modelo = Modelo::query()->where('slug', 'criacao-de-sites')->sole();
    [$pesquisa, $copy] = $modelo->etapas->take(2)->all();

    $this->put(rotaEquipe('servicos.fases.update', $copy), ['titulo' => 'Textos', 'responsavel' => 'Freelancer Ana'])->assertRedirect();
    $this->post(rotaEquipe('servicos.fases.mover', $copy), ['direcao' => 'subir'])->assertRedirect();
    $this->delete(rotaEquipe('servicos.fases.destroy', $pesquisa))->assertRedirect();

    expect($modelo->etapas()->pluck('titulo')->take(2)->all())->toBe(['Textos', 'Preparo das imagens'])
        ->and($modelo->etapas()->first()->ordem)->toBe(1)
        ->and(ModeloEtapa::withTrashed()->find($pesquisa->id)->trashed())->toBeTrue();
});

it('RN-10a: desativar tira o serviço da lista de cadastro, sem apagar', function () {
    actingAsEquipe();
    $modelo = Modelo::query()->where('slug', 'servidores-web-linux')->sole();

    $this->delete(rotaEquipe('servicos.destroy', $modelo))->assertRedirect(rotaEquipe('servicos.index'));

    expect(Modelo::withTrashed()->find($modelo->id)->trashed())->toBeTrue();
    $this->get(rotaEquipe('servicos.index'))->assertViewHas('modelos', fn ($modelos) => ! $modelos->contains('id', $modelo->id));
});

it('valida o nome do serviço e da fase', function () {
    actingAsEquipe();
    $modelo = Modelo::query()->firstOrFail();

    $this->post(rotaEquipe('servicos.store'), ['nome' => ''])->assertSessionHasErrors('nome');
    $this->post(rotaEquipe('servicos.fases.store', $modelo), ['titulo' => '', 'duracao_dias_uteis' => 0])
        ->assertSessionHasErrors(['titulo', 'duracao_dias_uteis']);
});
