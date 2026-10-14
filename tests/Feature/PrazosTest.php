<?php

use App\Mail\AvisoEntrega;
use App\Models\Cliente;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('privado');
    Mail::fake();
    config(['ponte.emails_equipe' => ['equipe@dobra9.com.br']]);
    $this->travelTo('2026-11-13 10:00');
    $this->cliente = Cliente::factory()->comLogin()->create();
    $this->fase = servicoDoCliente($this->cliente)->etapas()->where('titulo', 'Aprovação do cliente')->sole();
    $this->card = cardMostrado($this->fase);
    Mail::fake();
});

function verificarPrazos(): void
{
    test()->artisan('ponte:verificar-prazos')->assertSuccessful();
}

it('RN-74: lembrete ao cliente 1 dia útil antes do vencimento (pulando o feriado de 20/11), uma vez', function () {
    $this->travelTo('2026-11-18 08:00');
    verificarPrazos();
    Mail::assertNothingQueued();

    $this->travelTo('2026-11-19 08:00');
    verificarPrazos();
    Mail::assertQueued(AvisoEntrega::class, fn (AvisoEntrega $mail) => $mail->evento === 'lembrete' && $mail->hasTo($this->cliente->email));
    expect($this->card->fresh()->lembrete_enviado_em)->not->toBeNull();

    verificarPrazos();
    Mail::assertQueuedCount(1);
});

it('RN-75: prazo vencido avisa a equipe uma vez e aparece no painel', function () {
    $this->travelTo('2026-11-24 08:00');
    verificarPrazos();

    Mail::assertQueued(AvisoEntrega::class, fn (AvisoEntrega $mail) => $mail->evento === 'vencido' && $mail->hasTo('equipe@dobra9.com.br'));
    expect($this->card->fresh()->alerta_vencido_em)->not->toBeNull();

    verificarPrazos();
    Mail::assertQueued(AvisoEntrega::class, fn (AvisoEntrega $mail) => $mail->evento === 'vencido', 1);

    actingAsEquipe();
    $this->get(rotaEquipe('painel'))->assertOk()->assertSee(__('equipe.vencidos'))->assertSee(__('equipe.venceu_em', ['data' => '23/11']));
});

it('card respondido não recebe lembrete nem alerta', function () {
    actingAsCliente($this->cliente);
    $this->post(route('entregas.aprovar', $this->card), ['confirmar' => '1']);
    Mail::fake();

    $this->travelTo('2026-11-21 08:00');
    verificarPrazos();
    $this->travelTo('2026-11-25 08:00');
    verificarPrazos();

    Mail::assertNothingQueued();
});
