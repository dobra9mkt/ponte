<?php

namespace App\Console\Commands;

use App\Enums\Papel;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

#[Signature('ponte:criar-equipe {email} {nome}')]
#[Description('Cria (ou redefine a senha de) uma pessoa da equipe. O 2FA é configurado no primeiro acesso.')]
class CriarEquipe extends Command
{
    public function handle(): int
    {
        $email = Str::lower((string) $this->argument('email'));
        $senha = (string) $this->secret('Senha (mínimo 10 caracteres)');

        $validacao = Validator::make(
            ['email' => $email, 'password' => $senha],
            ['email' => ['required', 'email'], 'password' => ['required', Password::defaults()]],
        );

        if ($validacao->fails()) {
            foreach ($validacao->errors()->all() as $erro) {
                $this->error($erro);
            }

            return self::FAILURE;
        }

        $usuario = User::query()->firstWhere('email', $email);

        if ($usuario && ! $usuario->ehEquipe()) {
            $this->error('Este e-mail pertence a um cliente.');

            return self::FAILURE;
        }

        $usuario ??= new User;
        $usuario->fill(['name' => (string) $this->argument('nome'), 'email' => $email, 'password' => $senha]);
        $usuario->forceFill(['papel' => Papel::Equipe, 'ativo' => true])->save();

        $this->info('Pronto. Entre pelo caminho da equipe e configure o 2FA.');

        return self::SUCCESS;
    }
}
