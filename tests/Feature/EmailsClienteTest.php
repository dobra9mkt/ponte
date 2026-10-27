<?php

use App\Mail\AvisoEntrega;

function avisoAoCliente(string $evento, string $nome = 'Bela'): AvisoEntrega
{
    return new AvisoEntrega($evento, 'https://ponte.test/entregas/1', [
        'etapa' => 'Logo', 'frente' => 'Desenvolvimento do seu site', 'empresa' => 'Bela Bartók', 'rodada' => '1ª revisão',
        'versao' => '1', 'prazo' => '04/11/2026', 'prazo_curto' => '04/11', 'nome' => $nome,
    ]);
}

it('nova versão: assunto com a fase e o serviço, saudação pelo nome e botão Responder na Ponte', function () {
    $email = avisoAoCliente('publicada');

    $email->assertHasSubject('A 1ª versão de Logo está pronta · Desenvolvimento do seu site');
    $email->assertSeeInHtml('Olá, Bela.')
        ->assertSeeInHtml('A 1ª versão de Logo está pronta</h1>', false)
        ->assertSeeInHtml('A 1ª versão da fase Logo está pronta e aguardando a sua aprovação na Ponte.')
        ->assertSeeInHtml('Você pode aprovar ou pedir ajustes até 04/11/2026.')
        ->assertSeeInHtml('Responder na Ponte')
        ->assertSeeInText('Olá, Bela.')
        ->assertDontSeeInHtml('Revisar');
});

it('versão final: pede a autorização e o botão é Autorizar na Ponte', function () {
    $email = avisoAoCliente('publicada_final');

    $email->assertHasSubject('Falta só a sua autorização para Logo · Desenvolvimento do seu site');
    $email->assertSeeInHtml('A versão final da fase Logo está pronta e aguardando a sua autorização na Ponte.')
        ->assertSeeInHtml('Você pode autorizar até 04/11/2026.')
        ->assertSeeInHtml('Autorizar na Ponte');
});

it('lembrete: diz quando vence, com texto próprio para a versão final', function () {
    avisoAoCliente('lembrete')->assertHasSubject('Lembrete: sua resposta sobre Logo vence em 04/11 · Desenvolvimento do seu site');
    avisoAoCliente('lembrete')->assertSeeInHtml('A 1ª versão da fase Logo ainda aguarda a sua resposta na Ponte. O prazo termina em 04/11/2026.');
    avisoAoCliente('lembrete_final')->assertSeeInHtml('A versão final da fase Logo ainda aguarda a sua autorização na Ponte.')
        ->assertSeeInHtml('Autorizar na Ponte');
});

it('sem nome do contato, a saudação é só Olá', function () {
    avisoAoCliente('publicada', '')->assertSeeInHtml('<p style="margin:0 0 16px;font-size:16px;line-height:1.65;">Olá.</p>', false);
});

it('e-mail da equipe continua sem saudação', function () {
    $email = new AvisoEntrega('aprovada', 'https://ponte.test', ['etapa' => 'Logo', 'frente' => 'Site', 'empresa' => 'Bela Bartók', 'rodada' => '1ª revisão']);

    $email->assertHasSubject('Bela Bartók aprovou Logo');
    $email->assertDontSeeInHtml('Olá');
});
