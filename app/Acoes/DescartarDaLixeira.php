<?php

namespace App\Acoes;

use App\Models\Atividade;
use App\Models\Cliente;
use App\Models\Etapa;
use App\Models\Frente;
use Illuminate\Support\Facades\DB;

/**
 * Tira o item da Lixeira para sempre, sem apagar do banco: as provas de aprovação ficam guardadas (RN-82a).
 */
class DescartarDaLixeira
{
    public function __invoke(Cliente|Frente|Etapa $item): void
    {
        $item->forceFill(['descartado_em' => now()])->save();

        Atividade::registrar($this->acao($item), $item, ['item' => $this->nome($item)]);
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
