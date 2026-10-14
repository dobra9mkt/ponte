<?php

use App\Enums\EstadoEtapa;
use App\Enums\SituacaoCard;
use App\Mail\AvisoEntrega;
use App\Models\Arquivo;
use App\Models\Atividade;
use App\Models\Cliente;
use App\Models\Entrega;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('privado');
    Mail::fake();
    $this->travelTo('2026-11-13 10:00');
    $this->cliente = Cliente::factory()->comLogin()->create(['email_avisos' => 'aprovacoes@cliente.com.br']);
    $this->frente = servicoDoCliente($this->cliente);
    $this->fase = $this->frente->etapas()->where('titulo', 'Aprovação do cliente')->sole();
});

it('RN-70: adicionar aprovação cria um card em rascunho, só para a equipe', function () {
    $card = cardEmRascunho($this->fase, [
        'arquivos' => [UploadedFile::fake()->create('layout.fig', 20, 'application/octet-stream')],
        'links' => [['rotulo' => '', 'url' => 'https://teste.cliente.com.br'], ['rotulo' => '', 'url' => '']],
    ]);

    expect($card)->situacao->toBe(SituacaoCard::Rascunho)->versao->toBe(1)->publicada_em->toBeNull()
        ->and($card->links)->toBe([['rotulo' => 'teste.cliente.com.br', 'url' => 'https://teste.cliente.com.br']])
        ->and($card->arquivos->sole()->nome_original)->toBe('layout.fig')
        ->and($this->fase->fresh()->estado)->toBe(EstadoEtapa::AFazer);
    Storage::disk('privado')->assertExists($card->arquivos->sole()->caminho);
    expect($card->arquivos->sole()->caminho)->toEndWith('.bin');
    Mail::assertNothingQueued();

    $this->get(rotaEquipe('frentes.ver', [$this->cliente, $this->frente]))->assertOk()
        ->assertSee('1ª revisão')->assertSee(__('equipe.mostrar_cliente'))->assertSee('layout.fig');
});

it('RN-70: só links https; anexo pode sair do rascunho com exclusão suave', function () {
    $card = cardEmRascunho($this->fase, ['arquivos' => [UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')]]);
    $arquivo = $card->arquivos->sole();

    $this->put(rotaEquipe('aprovacoes.update', $card), ['nota' => 'x', 'links' => [['rotulo' => 'A', 'url' => 'http://inseguro.com']]])
        ->assertSessionHasErrors('links.0.url');
    $this->put(rotaEquipe('aprovacoes.update', $card), ['nota' => 'x', 'remover' => [$arquivo->id]])->assertSessionHasNoErrors();

    expect($card->fresh()->arquivos)->toBeEmpty()
        ->and(Arquivo::withTrashed()->find($arquivo->id)->trashed())->toBeTrue();
});

it('RN-72 / RN-73: mostrar para cliente publica, bloqueia, conta 5 dias úteis sem feriados e avisa o e-mail de avisos', function () {
    $card = cardMostrado($this->fase);

    expect($card)->situacao->toBe(SituacaoCard::AguardandoCliente)->hash_conteudo->toHaveLength(64)
        ->and($card->responder_ate->toDateString())->toBe('2026-11-23')
        ->and($this->fase->fresh()->estado)->toBe(EstadoEtapa::AguardandoCliente);
    Mail::assertQueued(AvisoEntrega::class, fn (AvisoEntrega $mail) => $mail->hasTo('aprovacoes@cliente.com.br')
        && $mail->evento === 'publicada' && $mail->dados['prazo'] === '23/11/2026' && $mail->url === route('entregas.ver', $card));
    expect(Atividade::query()->where('acao', 'card.mostrado')->exists())->toBeTrue();

    $this->put(rotaEquipe('aprovacoes.update', $card), ['nota' => 'Mudei'])->assertForbidden();
    $this->delete(rotaEquipe('aprovacoes.destroy', $card))->assertForbidden();
    expect(fn () => $card->forceFill(['nota' => 'x'])->save())->toThrow(LogicException::class)
        ->and(fn () => $card->delete())->toThrow(LogicException::class);
});

it('RN-72: sem o que revisar, o card não é mostrado e o rascunho é mantido', function () {
    $card = cardEmRascunho($this->fase);

    $this->put(rotaEquipe('aprovacoes.update', $card), ['nota' => '', 'mostrar' => '1'])->assertSessionHasErrors('nota');

    expect($card->fresh()->situacao)->toBe(SituacaoCard::Rascunho);
    Mail::assertNothingQueued();
});

it('RN-71: um card aberto por fase; excluir rascunho é exclusão suave', function () {
    $card = cardEmRascunho($this->fase);

    $this->post(rotaEquipe('aprovacoes.store', $this->fase))->assertForbidden();
    $this->post(rotaEquipe('etapas.iniciar', $this->fase));
    $this->post(rotaEquipe('etapas.concluir', $this->fase))->assertForbidden();

    $this->delete(rotaEquipe('aprovacoes.destroy', $card))->assertRedirect();
    expect(Entrega::withTrashed()->find($card->id)->trashed())->toBeTrue();
    $this->post(rotaEquipe('aprovacoes.store', $this->fase))->assertRedirect();
    expect($this->fase->entregas()->sole()->versao)->toBe(1);
});

it('RN-73: a equipe muda o prazo à mão e o lembrete volta a valer', function () {
    $card = cardMostrado($this->fase);
    $card->forceFill(['lembrete_enviado_em' => now()])->save();

    $this->put(rotaEquipe('aprovacoes.prazo', $card), ['responder_ate' => '2026-11-12'])->assertSessionHasErrors('responder_ate');
    $this->put(rotaEquipe('aprovacoes.prazo', $card), ['responder_ate' => '2026-11-30'])->assertRedirect();

    expect($card->fresh())->responder_ate->toDateString()->toBe('2026-11-30')->lembrete_enviado_em->toBeNull();
    expect(Atividade::query()->where('acao', 'card.prazo_alterado')->value('dados'))->toMatchArray(['de' => '23/11/2026', 'para' => '30/11/2026']);
});

it('RN-76: cliente aprova e a fase segue', function () {
    $card = cardMostrado($this->fase);
    actingAsCliente($this->cliente);

    $this->get(route('entregas.ver', $card))->assertOk()->assertSee('Confira o menu e as cores.');
    $this->post(route('entregas.aprovar', $card), ['confirmar' => '1'])->assertRedirect(route('frentes.ver', $this->frente));

    expect($card->fresh()->situacao)->toBe(SituacaoCard::Aprovado)
        ->and($card->aprovacao->hash_conteudo)->toBe($card->hash_conteudo)
        ->and($this->fase->fresh()->estado)->toBe(EstadoEtapa::EmProducao);
    Mail::assertQueued(AvisoEntrega::class, fn (AvisoEntrega $mail) => $mail->evento === 'aprovada');

    actingAsEquipe();
    $this->get(rotaEquipe('frentes.ver', [$this->cliente, $this->frente]))->assertSee('Aprovado em')
        ->assertSee(rotaEquipe('aprovacoes.store', $this->fase))->assertSee(__('equipe.adicionar_aprovacao'));
    $this->post(rotaEquipe('etapas.concluir', $this->fase))->assertRedirect();
    expect($this->fase->fresh()->estado)->toBe(EstadoEtapa::Concluida);
});

it('RN-76: cliente pede ajuste e a nova rodada vem numerada e com os links da anterior', function () {
    $primeiro = cardMostrado($this->fase, ['links' => [['rotulo' => 'Site de teste', 'url' => 'https://teste.cliente.com.br']]]);
    actingAsCliente($this->cliente);
    $this->post(route('entregas.ajustes', $primeiro), ['texto' => 'Trocar a foto do topo'])
        ->assertSessionHasNoErrors()->assertRedirect(route('frentes.ver', $this->frente));

    expect($primeiro->fresh()->situacao)->toBe(SituacaoCard::AjustesSolicitados)
        ->and($this->fase->fresh()->estado)->toBe(EstadoEtapa::AjustesEmAndamento);

    actingAsEquipe();
    $this->get(rotaEquipe('frentes.ver', [$this->cliente, $this->frente]))->assertSee('Trocar a foto do topo')->assertSee(__('equipe.nova_rodada'));
    $this->post(rotaEquipe('aprovacoes.store', $this->fase))->assertRedirect();

    $segundo = $this->fase->entregas()->firstOrFail();
    expect($segundo)->versao->toBe(2)->situacao->toBe(SituacaoCard::Rascunho)
        ->and($segundo->links)->toBe([['rotulo' => 'Site de teste', 'url' => 'https://teste.cliente.com.br']]);

    $this->put(rotaEquipe('aprovacoes.update', $segundo), ['nota' => 'Foto trocada', 'mostrar' => '1'])->assertSessionHasNoErrors();
    expect($this->fase->fresh()->estado)->toBe(EstadoEtapa::AguardandoCliente);
    $this->get(rotaEquipe('frentes.ver', [$this->cliente, $this->frente]))->assertSee('2ª revisão')->assertSee(__('equipe.rodadas_anteriores', ['qtd' => 1]));
});

it('segurança: cliente não vê rascunho nem responde card de outro cliente', function () {
    $rascunho = cardEmRascunho($this->fase, ['arquivos' => [UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')]]);
    actingAsCliente($this->cliente);
    $this->get(route('entregas.ver', $rascunho))->assertNotFound();
    $this->get(route('arquivos.ver', $rascunho->arquivos->sole()))->assertNotFound();

    actingAsEquipe();
    $this->put(rotaEquipe('aprovacoes.update', $rascunho), ['nota' => 'Pronto', 'mostrar' => '1']);
    actingAsCliente();
    $this->get(route('entregas.ver', $rascunho))->assertNotFound();
    $this->post(route('entregas.aprovar', $rascunho), ['confirmar' => '1'])->assertNotFound();
    expect($rascunho->fresh()->situacao)->toBe(SituacaoCard::AguardandoCliente);
});

it('segurança: cliente não usa as rotas de card da equipe', function () {
    $card = cardEmRascunho($this->fase);
    actingAsCliente($this->cliente);

    $this->post(rotaEquipe('aprovacoes.store', $this->fase))->assertNotFound();
    $this->put(rotaEquipe('aprovacoes.update', $card), ['nota' => 'x', 'mostrar' => '1'])->assertNotFound();
    $this->put(rotaEquipe('aprovacoes.prazo', $card), ['responder_ate' => '2026-12-01'])->assertNotFound();
    expect($card->fresh()->situacao)->toBe(SituacaoCard::Rascunho);
});

it('RN-17: o cliente vê "Revisão N de 3" e o aviso só na última', function () {
    $this->frente->forceFill(['publicada_em' => now()])->save();
    $card = cardMostrado($this->fase);

    foreach ([1, 2] as $rodada) {
        actingAsCliente($this->cliente);
        $this->get(route('frentes.ver', $this->frente))->assertSee("Revisão {$rodada} de 3")->assertDontSee(__('fluxo.ultima_revisao'));
        $this->post(route('entregas.ajustes', $card), ['texto' => 'Ajustar']);

        actingAsEquipe();
        $this->post(rotaEquipe('aprovacoes.store', $this->fase));
        $card = $this->fase->entregas()->firstOrFail();
        $this->put(rotaEquipe('aprovacoes.update', $card), ['nota' => 'Nova versão', 'mostrar' => '1']);
    }

    actingAsCliente($this->cliente);
    $this->get(route('frentes.ver', $this->frente))->assertSee('Revisão 3 de 3')->assertSee(__('fluxo.ultima_revisao'));
    $this->get(route('entregas.ver', $card))->assertSee('Revisão 3 de 3')->assertSee(__('fluxo.ultima_revisao'));
});

function terceiraRevisaoMostrada(): Entrega
{
    $card = cardMostrado(test()->fase);
    foreach ([1, 2] as $rodada) {
        actingAsCliente(test()->cliente);
        test()->post(route('entregas.ajustes', $card), ['texto' => 'Ajustar']);
        actingAsEquipe();
        test()->post(rotaEquipe('aprovacoes.store', test()->fase))->assertRedirect();
        $card = test()->fase->entregas()->firstOrFail();
        test()->put(rotaEquipe('aprovacoes.update', $card), ['nota' => 'Nova versão', 'mostrar' => '1']);
    }

    return $card;
}

it('RN-71: 3ª revisão aprovada encerra a fase para novos cards', function () {
    $terceira = terceiraRevisaoMostrada();
    actingAsCliente($this->cliente);
    $this->post(route('entregas.aprovar', $terceira), ['confirmar' => '1'])->assertSessionHasNoErrors();

    actingAsEquipe();
    $this->get(rotaEquipe('frentes.ver', [$this->cliente, $this->frente]))->assertDontSee(rotaEquipe('aprovacoes.store', $this->fase));
    $this->post(rotaEquipe('aprovacoes.store', $this->fase))->assertForbidden();
    $this->post(rotaEquipe('etapas.concluir', $this->fase))->assertRedirect();
    expect($this->fase->fresh()->estado)->toBe(EstadoEtapa::Concluida);
});

it('RN-71: ajustes na 3ª revisão viram a versão final, que o cliente só aprova', function () {
    $terceira = terceiraRevisaoMostrada();
    actingAsCliente($this->cliente);
    $this->post(route('entregas.ajustes', $terceira), ['texto' => 'Último ajuste'])->assertSessionHasNoErrors();

    actingAsEquipe();
    $this->get(rotaEquipe('frentes.ver', [$this->cliente, $this->frente]))->assertSee(__('equipe.enviar_versao_final'))->assertDontSee(__('equipe.nova_rodada'));
    $this->post(rotaEquipe('aprovacoes.store', $this->fase))->assertRedirect();
    $final = $this->fase->entregas()->firstOrFail();
    $this->get(rotaEquipe('frentes.ver', [$this->cliente, $this->frente]))
        ->assertSee(__('equipe.mensagem_cliente'))
        ->assertSee(__('equipe.botao_aprovacao'))->assertSee(__('fluxo.versao_final_botao'));
    $this->put(rotaEquipe('aprovacoes.update', $final), ['nota' => 'Ajustes feitos', 'texto_aprovar' => 'Autorizar publicação no servidor', 'mostrar' => '1'])->assertSessionHasNoErrors();
    $this->get(rotaEquipe('frentes.ver', [$this->cliente, $this->frente]))->assertSee('Versão final')->assertDontSee('4ª revisão');

    actingAsCliente($this->cliente);
    $this->get(route('frentes.ver', $this->frente))->assertSee('Versão final')->assertSee(__('fluxo.versao_final_celebracao'))->assertDontSee('Ajustes feitos')
        ->assertSee(__('fluxo.ver'))->assertDontSee(__('fluxo.revisar'));
    $this->get(route('entregas.ver', $final))->assertSee('Autorizar publicação no servidor')->assertSee(__('fluxo.versao_final_pendente'))
        ->assertSee(__('fluxo.versao_final_o_que_fazer'))->assertSee('Ajustes feitos')->assertDontSee(__('entregas.observacoes_dobra'))
        ->assertDontSee(__('entregas.pendente'))->assertDontSee(__('entregas.solicitar_ajustes'));
    $this->post(route('entregas.ajustes', $final), ['texto' => 'Mais um'])->assertSessionHasErrors('entrega');
    $this->post(route('entregas.aprovar', $final), ['confirmar' => '1'])->assertSessionHasNoErrors()->assertSessionHas('sucesso', __('fluxo.versao_final_registrada'));
    $this->get(route('frentes.ver', $this->frente))->assertSee('Autorizada em');
    $this->get(route('entregas.ver', $final))->assertSee('Autorizado em');
    Mail::assertQueued(AvisoEntrega::class, fn (AvisoEntrega $mail) => $mail->evento === 'autorizada');

    actingAsEquipe();
    $this->get(rotaEquipe('frentes.ver', [$this->cliente, $this->frente]))->assertSee('Autorizado em')->assertSee(__('fluxo.versao_final_autorizado'));
    $this->post(rotaEquipe('aprovacoes.store', $this->fase))->assertForbidden();
    $this->post(rotaEquipe('etapas.concluir', $this->fase))->assertRedirect();
    expect($this->fase->fresh()->estado)->toBe(EstadoEtapa::Concluida);
});

it('o pedido do cliente fica à vista e o rascunho da nova rodada só abre quando a equipe quer', function () {
    $primeiro = cardMostrado($this->fase);
    actingAsCliente($this->cliente);
    $this->post(route('entregas.ajustes', $primeiro), ['texto' => 'Trocar a foto do topo']);

    actingAsEquipe();
    $this->followingRedirects()->post(rotaEquipe('aprovacoes.store', $this->fase))
        ->assertSee('Trocar a foto do topo')->assertDontSee('Abrir 2ª revisão');

    $this->get(rotaEquipe('frentes.ver', [$this->cliente, $this->frente]))
        ->assertSee('Trocar a foto do topo')->assertSee('Abrir 2ª revisão');
});

it('o formulário de ajustes não fala em revisões que não contam', function () {
    $this->frente->forceFill(['publicada_em' => now()])->save();
    $card = cardMostrado($this->fase);
    actingAsCliente($this->cliente);

    $this->get(route('entregas.ver', $card))->assertOk()
        ->assertSee(__('entregas.enviar_ajustes'))
        ->assertDontSee('não contam como revisão')
        ->assertDontSee('ajuste extra');
});
