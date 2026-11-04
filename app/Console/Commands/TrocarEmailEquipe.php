<?php

namespace App\Console\Commands;

use App\Enums\Papel;
use App\Models\Atividade;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

#[Signature('ponte:trocar-email-equipe {atual} {novo}')]
#[Description('Troca o e-mail de entrada de uma pessoa da equipe. Senha, 2FA e histórico continuam iguais.')]
class TrocarEmailEquipe extends Command
{
    public function handle(): int
    {
        $atual = Str::lower((string) $this->argument('atual'));
        $novo = Str::lower((string) $this->argument('novo'));

        if (Validator::make(['email' => $novo], ['email' => ['required', 'email']])->fails()) {
            $this->error('O e-mail novo não é válido.');

            return self::FAILURE;
        }

        $usuario = User::query()->where('email', $atual)->where('papel', Papel::Equipe)->first();

        if ($usuario === null) {
            $this->error('Não há ninguém da equipe com o e-mail atual informado.');

            return self::FAILURE;
        }

        if (User::query()->where('email', $novo)->exists()) {
            $this->error('O e-mail novo já está em uso.');

            return self::FAILURE;
        }

        $usuario->forceFill(['email' => $novo])->save();
        Atividade::registrar('equipe.email_trocado', dados: ['de' => $atual, 'para' => $novo], usuario: $usuario);

        $this->info("Pronto. Agora a entrada é com {$novo}.");

        return self::SUCCESS;
    }
}
