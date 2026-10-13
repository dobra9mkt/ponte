<?php

namespace App\Console\Commands;

use App\Mail\AlertaFalhasLogin as EmailAlerta;
use App\Models\Atividade;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('ponte:alertar-falhas-login')]
#[Description('Avisa a equipe quando há muitas tentativas de login erradas na última hora.')]
class AlertarFalhasLogin extends Command
{
    public function handle(): int
    {
        $falhas = Atividade::query()
            ->where('acao', 'login.falhou')
            ->where('created_at', '>=', now()->subHour())
            ->pluck('ip');

        $destinatarios = config('ponte.emails_equipe');

        if ($falhas->count() > config('ponte.alerta_falhas_login_por_hora') && $destinatarios !== []) {
            Mail::to($destinatarios)->send(new EmailAlerta($falhas->count(), $falhas->filter()->unique()->take(10)->values()->all()));
        }

        return self::SUCCESS;
    }
}
