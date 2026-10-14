<?php

namespace App\Console\Commands;

use App\Acoes\AlertarPrazosVencidos;
use App\Acoes\EnviarLembretesPrazo;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('ponte:verificar-prazos')]
#[Description('Lembra o cliente na véspera do prazo e avisa a equipe dos prazos vencidos.')]
class VerificarPrazos extends Command
{
    public function handle(EnviarLembretesPrazo $lembrar, AlertarPrazosVencidos $alertar): int
    {
        $this->info('Lembretes enviados: '.$lembrar());
        $this->info('Prazos vencidos avisados: '.$alertar());

        return self::SUCCESS;
    }
}
