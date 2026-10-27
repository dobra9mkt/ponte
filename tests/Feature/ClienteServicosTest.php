<?php

use App\Acoes\CriarFrente;
use App\Enums\EstadoEtapa;
use App\Enums\SituacaoFrente;
use App\Models\Cliente;
use App\Models\Frente;
use App\Models\Modelo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

function servicoVisivel(Cliente $cliente): Frente
{
    $frente = servicoDoCliente($cliente);
    $frente->forceFill(['publicada_em' => now()])->save();
    $frente->etapas()->whereIn('ordem', [1, 2])->update(['estado' => EstadoEtapa::EmProducao]);

    return $frente;
}

it('SEG-10 / SEG-11: cliente A recebe 404 no serviço do cliente B', function () {
    $frenteDeB = servicoVisivel(Cliente::factory()->create());
    actingAsCliente();

    $this->get(route('frentes.ver', $frenteDeB))->assertNotFound();
});

it('o dono vê o próprio serviço', function () {
    $usuario = actingAsCliente();
    $frente = servicoVisivel($usuario->cliente);

    $this->get(route('frentes.ver', $frente))->assertOk();
});

it('visitante é levado para entrar', function () {
    $frente = servicoVisivel(Cliente::factory()->create());

    $this->get(route('frentes.ver', $frente))->assertRedirect(route('entrar'));
});

it('serviço ainda não mostrado ao cliente devolve 404', function () {
    $usuario = actingAsCliente();
    $frente = servicoDoCliente($usuario->cliente);

    $this->get(route('frentes.ver', $frente))->assertNotFound();
    $this->get(route('inicio'))->assertDontSee($frente->nomeParaCliente());
});

it('o texto da fase vem recolhido em 1 linha, inteiro na página para o "Ver mais" abrir', function () {
    $usuario = actingAsCliente();
    $frente = servicoVisivel($usuario->cliente);
    $longo = str_repeat('Vamos levantar referências do seu mercado e dos concorrentes. ', 5);
    $frente->etapas()->where('ordem', 1)->update(['o_que_fazer' => $longo]);
    $frente->etapas()->where('ordem', 2)->update(['o_que_fazer' => 'Texto curto da fase.']);

    $resposta = $this->get(route('frentes.ver', $frente))->assertOk()
        ->assertSee(trim($longo))
        ->assertSee('Texto curto da fase.')
        ->assertSee(__('ponte.texto.ver_mais'));
    expect(substr_count($resposta->getContent(), 'x-data="textoRecolhivel"'))->toBe(2)
        ->and(substr_count($resposta->getContent(), 'line-clamp-1'))->toBe(2);
});

it('cabeçalho do serviço mostra as fases concluídas sem barra nem total', function () {
    $usuario = actingAsCliente();
    $frente = servicoVisivel($usuario->cliente);

    $this->get(route('frentes.ver', $frente))->assertOk()
        ->assertDontSee('role="progressbar"', false)
        ->assertDontSee('etapas concluídas')
        ->assertSeeText('Nenhuma fase concluída ainda');

    $frente->etapas()->where('ordem', 1)->update(['estado' => EstadoEtapa::Concluida]);
    $this->get(route('frentes.ver', $frente))->assertSeeText('1 fase concluída');

    $frente->etapas()->where('ordem', 2)->update(['estado' => EstadoEtapa::Concluida]);
    $this->get(route('frentes.ver', $frente))->assertSeeText('2 fases concluídas');
});

it('RN-17: o fim da linha do tempo avisa das próximas fases só enquanto houver fase por começar', function () {
    $usuario = actingAsCliente();
    $frente = servicoVisivel($usuario->cliente);
    $frente->forceFill(['prazo_final' => '2027-02-28'])->save();

    $this->get(route('frentes.ver', $frente))->assertOk()
        ->assertSee(__('fluxo.proximas_fases'))
        ->assertDontSee('Término previsto: 28/02/2027');

    $frente->etapas()->where('estado', EstadoEtapa::AFazer)->update(['oculta' => true]);
    $this->get(route('frentes.ver', $frente))->assertOk()->assertDontSee(__('fluxo.proximas_fases'));
});

it('fase oculta não aparece para o cliente e devolve 404', function () {
    $usuario = actingAsCliente();
    $frente = servicoVisivel($usuario->cliente);
    $oculta = $frente->etapas()->where('ordem', 2)->sole();
    $oculta->forceFill(['oculta' => true])->save();

    $this->get(route('frentes.ver', $frente))->assertOk()->assertSee('Pesquisa')->assertDontSee('Copy');
});

it('serviço arquivado devolve 404 ao cliente', function () {
    $usuario = actingAsCliente();
    $frente = Frente::factory()->create(['cliente_id' => $usuario->cliente_id]);
    $frente->forceFill(['situacao' => SituacaoFrente::Arquivada])->save();

    $this->get(route('frentes.ver', $frente))->assertNotFound();
    $this->get(route('inicio'))->assertDontSee($frente->nomeParaCliente());
});

it('a linha do tempo do cliente mostra as fases com a data de término', function () {
    $usuario = actingAsCliente();
    $frente = servicoVisivel($usuario->cliente);
    $frente->etapas()->first()->forceFill(['termino_em' => '2026-11-06', 'estado' => EstadoEtapa::Concluida])->save();

    $this->get(route('frentes.ver', $frente))->assertOk()->assertSee('Pesquisa')->assertSee('06/11')->assertSee('Concluída');
});

it('o cliente nunca vê a área de edição da equipe', function () {
    $usuario = actingAsCliente();
    $frente = servicoVisivel($usuario->cliente);

    $this->get(route('frentes.ver', $frente))->assertDontSee(config('ponte.caminho_equipe'));
    $this->get(rotaEquipe('frentes.ver', [$frente->cliente_id, $frente]))->assertNotFound();
});

it('sem serviços, o Início continua com a mensagem de preparação', function () {
    actingAsCliente(Cliente::factory()->comLogin()->create());

    $this->get(route('inicio'))->assertOk()->assertSee(__('ponte.inicio.preparando'));
});

it('RN-16: a equipe mostra e oculta o serviço no portal, sem enviar e-mail', function () {
    Mail::fake();
    $cliente = Cliente::factory()->comLogin()->create();
    $frente = servicoDoCliente($cliente);
    actingAsEquipe();

    $this->post(rotaEquipe('frentes.visibilidade', [$cliente, $frente]), ['mostrar' => '1'])->assertRedirect();
    expect($frente->fresh()->publicada_em)->not->toBeNull();
    actingAsCliente($cliente);
    $this->get(route('frentes.ver', $frente))->assertOk();

    actingAsEquipe();
    $this->post(rotaEquipe('frentes.visibilidade', [$cliente, $frente]), ['mostrar' => '0'])->assertRedirect();
    actingAsCliente($cliente);
    $this->get(route('frentes.ver', $frente))->assertNotFound();
    Mail::assertNothingQueued();
});

it('RN-17: a linha do tempo mostra o começo, as fases e as rodadas, sem o responsável nem rascunhos', function () {
    Storage::fake('privado');
    Mail::fake();
    $cliente = Cliente::factory()->comLogin()->create();
    $frente = servicoDoCliente($cliente, inicio: '2026-11-03');
    $frente->etapas()->first()->forceFill(['estado' => EstadoEtapa::EmProducao, 'responsavel' => 'Ana (freelancer)', 'entrada_em' => '2026-11-03', 'termino_em' => '2026-11-06'])->save();
    $aprovacao = $frente->etapas()->where('titulo', 'Aprovação do cliente')->sole();
    $card = cardMostrado($aprovacao);
    cardEmRascunho($frente->etapas()->where('titulo', 'Copy')->sole(), ['nota' => 'Rascunho interno']);

    actingAsCliente($cliente);
    $this->get(route('frentes.ver', $frente))->assertOk()
        ->assertSee('Começamos em 03/11/2026')
        ->assertSee('03/11 → 06/11')
        ->assertSee('Revisão 1 de 3')
        ->assertSee('Aguardando sua aprovação')
        ->assertSee('>'.__('fluxo.responder').'<', false)
        ->assertSee(route('entregas.ver', $card))
        ->assertDontSee('Ana (freelancer)')
        ->assertDontSee('Rascunho interno');
});

it('RN-16a: o cliente vê só as fases em andamento ou finalizadas', function () {
    $usuario = actingAsCliente();
    $frente = servicoVisivel($usuario->cliente);
    $frente->etapas()->where('ordem', 1)->update(['estado' => EstadoEtapa::Concluida]);
    $aFazer = $frente->etapas()->where('titulo', 'Desenvolvimento')->sole();

    $this->get(route('frentes.ver', $frente))->assertOk()
        ->assertSee('Pesquisa')->assertSee('Copy')
        ->assertDontSee('>Desenvolvimento</h2>', false)->assertDontSee('Preparo das imagens');

    actingAsEquipe();
    $this->post(rotaEquipe('etapas.iniciar', $aFazer));
    actingAsCliente($usuario->cliente);
    $this->get(route('frentes.ver', $frente))->assertSee('>Desenvolvimento</h2>', false);
});

it('RN-16b: com um só serviço, o cliente vai direto para a linha do tempo, sem abas', function () {
    $usuario = actingAsCliente();
    $frente = servicoVisivel($usuario->cliente);

    $this->get(route('inicio'))->assertRedirect(route('frentes.ver', $frente));
    $this->get(route('frentes.ver', $frente))->assertOk()->assertDontSee(__('fluxo.seus_projetos'));
});

it('RN-16b: com dois serviços, abas trocam de serviço e o Início abre o que tem aprovação pendente', function () {
    $usuario = actingAsCliente();
    $pendente = servicoVisivel($usuario->cliente);
    $pendente->etapas()->where('ordem', 1)->update(['estado' => EstadoEtapa::AguardandoCliente]);
    $recente = Frente::factory()->create(['cliente_id' => $usuario->cliente_id, 'nome' => 'Tráfego pago']);
    $recente->forceFill(['publicada_em' => now()])->save();

    $this->get(route('inicio'))->assertRedirect(route('frentes.ver', $pendente));
    $this->get(route('frentes.ver', $recente))->assertOk()
        ->assertSee(__('fluxo.seus_projetos'))
        ->assertSee(route('frentes.ver', $pendente))->assertSee('Tráfego pago')
        ->assertSee(__('fluxo.aprovacao_pendente'));

    $pendente->etapas()->where('ordem', 1)->update(['estado' => EstadoEtapa::EmProducao]);
    $this->get(route('inicio'))->assertRedirect(route('frentes.ver', $recente));
});

it('RN-16b: as abas não mostram serviços de outro cliente', function () {
    $outro = servicoVisivel(Cliente::factory()->create());
    $usuario = actingAsCliente();
    $frente = servicoVisivel($usuario->cliente);
    Frente::factory()->create(['cliente_id' => $usuario->cliente_id, 'publicada_em' => now()]);

    $this->get(route('frentes.ver', $frente))->assertOk()->assertDontSee(route('frentes.ver', $outro));
});

it('RN-16c: o cliente vê o nome para o cliente; a equipe continua vendo o nome do serviço', function () {
    $usuario = actingAsCliente();
    $frente = servicoVisivel($usuario->cliente);

    actingAsEquipe();
    $this->put(rotaEquipe('frentes.update', [$frente->cliente_id, $frente]), ['nome' => 'Criação de sites', 'nome_cliente' => 'Desenvolvimento do seu site'])
        ->assertSessionHasNoErrors();
    $this->get(rotaEquipe('frentes.ver', [$frente->cliente_id, $frente]))->assertSee('Criação de sites');

    actingAsCliente($usuario->cliente);
    $this->get(route('frentes.ver', $frente))->assertOk()
        ->assertSee('Desenvolvimento do seu site')->assertDontSee('Criação de sites')
        ->assertSee(__('fluxo.boas_vindas_titulo'))->assertSee('marca/dobra9-logo-branco.svg', false);
});

it('RN-16c: serviço criado do fluxo padrão herda o nome para o cliente', function () {
    $modelo = Modelo::query()->where('slug', 'criacao-de-sites')->sole();
    actingAsEquipe();
    $this->put(rotaEquipe('servicos.update', $modelo), ['nome' => $modelo->nome, 'nome_cliente' => 'Desenvolvimento do seu site'])->assertSessionHasNoErrors();
    $cliente = Cliente::factory()->create();

    $frente = app(CriarFrente::class)($cliente, $modelo->fresh(), $modelo->nome);

    expect($frente->nomeParaCliente())->toBe('Desenvolvimento do seu site')->and($frente->nome)->toBe('Criação de sites');
});

it('RN-17: rodadas de fase concluída ficam atrás de "Ver histórico"; fase em andamento fica aberta', function () {
    Storage::fake('privado');
    Mail::fake();
    $cliente = Cliente::factory()->comLogin()->create();
    $frente = servicoDoCliente($cliente);
    $fase = $frente->etapas()->where('titulo', 'Aprovação do cliente')->sole();
    $card = cardMostrado($fase);

    actingAsCliente($cliente);
    $this->get(route('frentes.ver', $frente))->assertSee(route('entregas.ver', $card))->assertDontSee(__('fluxo.ocultar_historico'));

    $this->post(route('entregas.aprovar', $card), ['confirmar' => '1']);
    $fase->forceFill(['estado' => EstadoEtapa::Concluida])->save();
    $this->get(route('frentes.ver', $frente))->assertSee(__('fluxo.ver_historico', ['qtd' => 1]))->assertSee(route('entregas.ver', $card));
});
