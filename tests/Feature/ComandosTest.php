<?php

use App\Models\Atividade;
use App\Models\Cliente;
use App\Models\User;

it('ponte:trocar-email-equipe troca só o e-mail, mantém senha e 2FA e registra a atividade', function () {
    $usuario = User::factory()->equipe()->create(['email' => 'antigo@exemplo.com']);
    $senha = $usuario->password;
    $segredo = $usuario->two_factor_secret;

    $this->artisan('ponte:trocar-email-equipe', ['atual' => 'Antigo@Exemplo.com', 'novo' => 'Ponte@Dobra9.com.br'])
        ->assertSuccessful();

    $usuario->refresh();
    expect($usuario->email)->toBe('ponte@dobra9.com.br')
        ->and($usuario->password)->toBe($senha)
        ->and($usuario->two_factor_secret)->toBe($segredo)
        ->and(Atividade::query()->where('acao', 'equipe.email_trocado')->where('user_id', $usuario->id)->exists())->toBeTrue();
});

it('ponte:trocar-email-equipe recusa e-mail inválido, e-mail em uso e conta de cliente', function () {
    $equipe = User::factory()->equipe()->create(['email' => 'equipe@exemplo.com']);
    $cliente = Cliente::factory()->comLogin()->create();

    $this->artisan('ponte:trocar-email-equipe', ['atual' => 'equipe@exemplo.com', 'novo' => 'invalido'])->assertFailed();
    $this->artisan('ponte:trocar-email-equipe', ['atual' => 'equipe@exemplo.com', 'novo' => $cliente->email])->assertFailed();
    $this->artisan('ponte:trocar-email-equipe', ['atual' => $cliente->email, 'novo' => 'novo@exemplo.com'])->assertFailed();

    expect($equipe->fresh()->email)->toBe('equipe@exemplo.com')
        ->and($cliente->usuario->fresh()->email)->toBe($cliente->email);
});

it('ponte:criar-equipe cria a pessoa da equipe, sem 2FA até o primeiro acesso', function () {
    $this->artisan('ponte:criar-equipe', ['email' => 'Rose@Dobra9.com.br', 'nome' => 'Rose'])
        ->expectsQuestion('Senha (mínimo 10 caracteres)', 'senha-da-rose-123')
        ->assertSuccessful();

    $usuario = User::sole();
    expect($usuario->email)->toBe('rose@dobra9.com.br')
        ->and($usuario->ehEquipe())->toBeTrue()
        ->and($usuario->hasEnabledTwoFactorAuthentication())->toBeFalse();
});

it('ponte:criar-equipe recusa senha curta e e-mail de cliente', function () {
    $this->artisan('ponte:criar-equipe', ['email' => 'rose@dobra9.com.br', 'nome' => 'Rose'])
        ->expectsQuestion('Senha (mínimo 10 caracteres)', 'curta')
        ->assertFailed();

    $cliente = Cliente::factory()->comLogin()->create();
    $this->artisan('ponte:criar-equipe', ['email' => $cliente->email, 'nome' => 'Intruso'])
        ->expectsQuestion('Senha (mínimo 10 caracteres)', 'senha-bem-longa-123')
        ->assertFailed();

    expect($cliente->usuario->fresh()->ehCliente())->toBeTrue();
});
