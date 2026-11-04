<?php

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Testing\TestResponse;
use Laravel\Fortify\Fortify;
use PragmaRX\Google2FA\Google2FA;

function entrarComCodigo(User $usuario): string
{
    test()->post(rotaEquipe('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA]);
    $resposta = test()->post(rotaEquipe('2fa'), ['codigo' => (new Google2FA)->getCurrentOtp(UserFactory::SEGREDO_2FA)]);
    $valor = $resposta->getCookie('ponte_dispositivo')->getValue();
    test()->post(route('sair'));

    return $valor;
}

function entrarSoComSenha(User $usuario, string $senha = UserFactory::SENHA): TestResponse
{
    return test()->post(rotaEquipe('entrar'), ['email' => $usuario->email, 'password' => $senha]);
}

it('SEG-02a: aparelho reconhecido entra só com a senha', function () {
    $usuario = User::factory()->equipe()->create();
    $this->withCookie('ponte_dispositivo', entrarComCodigo($usuario));

    entrarSoComSenha($usuario)->assertRedirect(rotaEquipe('painel'));

    $this->assertAuthenticatedAs($usuario);
});

it('SEG-02a: o cookie vale só no caminho secreto e não aparece para scripts', function () {
    $usuario = User::factory()->equipe()->create();
    entrarSoComSenha($usuario);

    $cookie = $this->post(rotaEquipe('2fa'), ['codigo' => (new Google2FA)->getCurrentOtp(UserFactory::SEGREDO_2FA)])
        ->getCookie('ponte_dispositivo');

    expect($cookie->getPath())->toBe('/'.config('ponte.caminho_equipe'))
        ->and($cookie->isHttpOnly())->toBeTrue();
});

it('SEG-02a: aparelho sem o cookie continua pedindo o código', function () {
    $usuario = User::factory()->equipe()->create();
    entrarComCodigo($usuario);

    entrarSoComSenha($usuario)->assertRedirect(rotaEquipe('2fa'));

    $this->assertGuest();
});

it('SEG-02a: aparelho reconhecido ainda exige a senha certa', function () {
    $usuario = User::factory()->equipe()->create();
    $this->withCookie('ponte_dispositivo', entrarComCodigo($usuario));

    entrarSoComSenha($usuario, 'senha-errada-123')->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('SEG-02a: depois de 30 dias pede o código de novo', function () {
    $usuario = User::factory()->equipe()->create();
    $this->withCookie('ponte_dispositivo', entrarComCodigo($usuario));

    $this->travel(29)->days();
    entrarSoComSenha($usuario)->assertRedirect(rotaEquipe('painel'));
    $this->post(route('sair'));

    $this->travel(2)->days();
    entrarSoComSenha($usuario)->assertRedirect(rotaEquipe('2fa'));
    $this->assertGuest();
});

it('SEG-02a: trocar a senha faz o aparelho pedir o código de novo', function () {
    $usuario = User::factory()->equipe()->create();
    $this->withCookie('ponte_dispositivo', entrarComCodigo($usuario));

    $usuario->forceFill(['password' => 'outra-senha-bem-longa-123'])->save();

    entrarSoComSenha($usuario, 'outra-senha-bem-longa-123')->assertRedirect(rotaEquipe('2fa'));
    $this->assertGuest();
});

it('SEG-02a: trocar o aplicativo autenticador faz o aparelho pedir o código de novo', function () {
    $usuario = User::factory()->equipe()->create();
    $this->withCookie('ponte_dispositivo', entrarComCodigo($usuario));

    $usuario->forceFill(['two_factor_secret' => Fortify::currentEncrypter()->encrypt('JBSWY3DPEHPK3PXP')])->save();

    entrarSoComSenha($usuario)->assertRedirect(rotaEquipe('2fa'));
    $this->assertGuest();
});

it('SEG-02a: cookie de outra pessoa não dispensa o código', function () {
    $dono = User::factory()->equipe()->create();
    $outra = User::factory()->equipe()->create();
    $this->withCookie('ponte_dispositivo', entrarComCodigo($dono));

    entrarSoComSenha($outra)->assertRedirect(rotaEquipe('2fa'));

    $this->assertGuest();
});

it('SEG-02a: cookie adulterado não dispensa o código', function (string $troca) {
    $usuario = User::factory()->equipe()->create();
    [$id, , $assinatura] = explode('|', entrarComCodigo($usuario));
    $valores = ['validade' => "{$id}|9999999999|{$assinatura}", 'assinatura' => "{$id}|9999999999|falsa", 'lixo' => 'lixo'];
    $this->withCookie('ponte_dispositivo', $valores[$troca]);

    entrarSoComSenha($usuario)->assertRedirect(rotaEquipe('2fa'));

    $this->assertGuest();
})->with(['validade', 'assinatura', 'lixo']);
