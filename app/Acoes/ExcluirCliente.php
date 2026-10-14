<?php

namespace App\Acoes;

use App\Models\Atividade;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

class ExcluirCliente
{
    /**
     * Vai para a lixeira com os serviços; o login para de funcionar na hora (RN-82).
     */
    public function __invoke(Cliente $cliente): void
    {
        DB::transaction(function () use ($cliente) {
            $cliente->delete();

            if ($cliente->usuario !== null) {
                DB::table('sessions')->where('user_id', $cliente->usuario->id)->delete();
            }
        });

        Atividade::registrar('cliente.excluido', $cliente, ['cliente' => $cliente->empresa]);
    }
}
