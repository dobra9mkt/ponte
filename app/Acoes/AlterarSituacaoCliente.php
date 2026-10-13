<?php

namespace App\Acoes;

use App\Enums\SituacaoCliente;
use App\Models\Atividade;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AlterarSituacaoCliente
{
    public function __invoke(Cliente $cliente, SituacaoCliente $situacao): void
    {
        DB::transaction(function () use ($cliente, $situacao) {
            $cliente->forceFill(['situacao' => $situacao])->save();

            $usuario = $cliente->usuario;

            if ($usuario === null) {
                return;
            }

            $ativo = $situacao === SituacaoCliente::Ativo;
            $usuario->forceFill([
                'ativo' => $ativo,
                'remember_token' => $ativo ? $usuario->remember_token : Str::random(60),
            ])->save();

            if (! $ativo) {
                DB::table('sessions')->where('user_id', $usuario->id)->delete();
                $usuario->convites()->whereNull('usado_em')->delete();
            }
        });

        Atividade::registrar('cliente.'.$situacao->value, $cliente);
    }
}
