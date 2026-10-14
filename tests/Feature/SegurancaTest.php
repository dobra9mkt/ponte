<?php

use App\Mail\AlertaFalhasLogin;
use App\Models\Atividade;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Mail;

it('SEG-31: toda resposta tem os cabeçalhos de segurança', function (string $url) {
    $resposta = $this->get($url);

    $resposta->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
        ->assertHeader('Strict-Transport-Security');

    $csp = $resposta->headers->get('Content-Security-Policy');
    expect($csp)->toContain("script-src 'self'")
        ->toContain("frame-ancestors 'none'")
        ->not->toContain('unsafe-inline')
        ->not->toContain('unsafe-eval');
})->with(['/entrar', '/privacidade', '/nao-existe', '/convite/qualquer']);

it('SEG-32: páginas não têm script nem estilo inline', function () {
    $html = $this->get(route('entrar'))->getContent();

    expect($html)->not->toMatch('/<script(?![^>]*\bsrc=)[^>]*>/i')
        ->not->toMatch('/\sstyle=/i')
        ->not->toMatch('/\son[a-z]+=/i');
});

it('SEG-14: Atividade não pode ser alterada nem apagada', function () {
    $atividade = Atividade::registrar('teste');

    expect(fn () => $atividade->forceFill(['acao' => 'outra'])->save())->toThrow(LogicException::class);
    expect(fn () => $atividade->delete())->toThrow(LogicException::class);
    expect(Atividade::sole()->acao)->toBe('teste');
});

it('SEG-12: enviar papel=equipe na conta do cliente não muda o papel', function () {
    $usuario = actingAsCliente();

    $this->put(route('conta.senha'), [
        'senha_atual' => UserFactory::SENHA,
        'password' => 'nova-senha-bem-longa',
        'password_confirmation' => 'nova-senha-bem-longa',
        'papel' => 'equipe',
        'cliente_id' => 999,
    ])->assertSessionHasNoErrors();

    $usuario->refresh();
    expect($usuario->ehCliente())->toBeTrue()->and($usuario->cliente_id)->not->toBe(999);
});

it('SEG-05: avisa a equipe com mais de 20 falhas de login na última hora', function () {
    Mail::fake();

    foreach (range(1, 21) as $i) {
        Atividade::registrar('login.falhou');
    }

    $this->artisan('ponte:alertar-falhas-login')->assertSuccessful();
    Mail::assertQueued(AlertaFalhasLogin::class, fn ($email) => $email->hasTo('equipe@dobra9.com.br') && $email->quantidade === 21);
});

it('SEG-05: sem muitas falhas, nenhum alerta', function () {
    Mail::fake();
    Atividade::registrar('login.falhou');

    $this->artisan('ponte:alertar-falhas-login')->assertSuccessful();
    Mail::assertNothingQueued();
});

it('robots.txt bloqueia tudo', function () {
    expect(file_get_contents(public_path('robots.txt')))->toContain('Disallow: /');
});

it('erro mostra página genérica sem detalhes técnicos', function () {
    config(['app.debug' => false]);

    $this->get('/nao-existe')->assertNotFound()->assertSee('Página não encontrada')->assertDontSee('Symfony');
});

it('SEG-52: a privacidade avisa que entradas e ações ficam registradas com IP e navegador', function () {
    $this->get('/privacidade')->assertOk()->assertSee('endereço de internet (IP)')->assertSee('tipo de navegador');
});
