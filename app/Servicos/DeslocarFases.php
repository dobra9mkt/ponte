<?php

namespace App\Servicos;

use App\Models\Etapa;
use Illuminate\Support\Collection;

class DeslocarFases
{
    public function __construct(private DiasUteis $diasUteis) {}

    /**
     * Move entrada e término das fases em aberto os mesmos dias úteis (RN-13).
     *
     * @param  Collection<int, Etapa>  $fases
     */
    public function __invoke(Collection $fases, int $dias): void
    {
        $fases->reject(fn (Etapa $fase) => $fase->estado->finalizada())
            ->each(function (Etapa $fase) use ($dias) {
                $fase->entrada_em = $fase->entrada_em ? $this->diasUteis->somar($fase->entrada_em, $dias) : null;
                $fase->termino_em = $fase->termino_em ? $this->diasUteis->somar($fase->termino_em, $dias) : null;
                $fase->save();
            });
    }
}
