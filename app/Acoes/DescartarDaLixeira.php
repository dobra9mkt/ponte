<?php

namespace App\Acoes;

use App\Models\Atividade;
use App\Models\Cliente;
use App\Models\Etapa;
use App\Models\Frente;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Tira o item da Lixeira para sempre, sem apagar do banco: as provas de aprovação ficam guardadas (RN-82a).
 */
class DescartarDaLixeira
{
    public function __invoke(Cliente|Frente|Etapa $item): void
    {
        DB::transaction(function () use ($item) {
            $item->forceFill(['descartado_em' => now()])->save();

            if ($item instanceof Cliente) {
                $this->liberarEmail($item);
            }

            Atividade::registrar($this->acao($item), $item, ['item' => $this->nome($item)]);
        });
    }

    /** O e-mail volta a poder ser cadastrado; o original fica no login antigo, como prova (RN-82a). */
    private function liberarEmail(Cliente $cliente): void
    {
        $usuario = $cliente->usuario;
        if ($usuario === null || str_starts_with($usuario->email, 'descartado-')) {
            return;
        }

        $usuario->forceFill(['email' => User::emailDescartado($usuario->id, $usuario->email)])->save();
    }

    public function esvaziar(): void
    {
        DB::transaction(function () {
            foreach ([Cliente::class, Frente::class, Etapa::class] as $classe) {
                $classe::onlyTrashed()->whereNull('descartado_em')->get()->each(fn ($item) => $this($item));
            }
        });
    }

    private function acao(Cliente|Frente|Etapa $item): string
    {
        return match (true) {
            $item instanceof Cliente => 'cliente.descartado',
            $item instanceof Frente => 'frente.descartada',
            default => 'etapa.descartada',
        };
    }

    private function nome(Cliente|Frente|Etapa $item): string
    {
        return match (true) {
            $item instanceof Cliente => $item->empresa,
            $item instanceof Frente => $item->nome,
            default => $item->titulo,
        };
    }
}
