<?php

use App\Models\Cliente;
use App\Models\User;

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
