<?php

namespace App\Acoes;

use App\Mail\ConviteAcesso;
use App\Models\Atividade;
use App\Models\Cliente;
use App\Models\Convite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EnviarAcesso
{
    public function __invoke(Cliente $cliente): void
    {
        $usuario = $cliente->usuario()->firstOrFail();
        $token = Str::random(64);

        DB::transaction(function () use ($usuario, $token) {
            $usuario->convites()->whereNull('usado_em')->delete();

            $convite = new Convite;
            $convite->forceFill([
                'user_id' => $usuario->id,
                'token_hash' => Convite::hashDoToken($token),
                'expira_em' => now()->addHours(config('ponte.convite_validade_horas')),
                'enviado_por' => auth()->id(),
            ])->save();
        });

        Mail::to($usuario->email)->send(new ConviteAcesso($cliente, $token));

        Atividade::registrar('acesso.enviado', $cliente);
    }
}
