<?php

namespace App\Acoes;

use App\Models\Atividade;
use App\Models\Etapa;
use App\Models\Frente;
use App\Servicos\DeslocarFases;
use App\Servicos\DiasUteis;
use Illuminate\Support\Facades\DB;

class SalvarEtapa
{
    public function __construct(private DiasUteis $diasUteis, private DeslocarFases $deslocar) {}

    /**
     * Mudar o término move as fases seguintes os mesmos dias úteis (RN-13).
     *
     * @param  array<string, mixed>  $dados
     */
    public function __invoke(Frente $frente, array $dados, ?Etapa $etapa = null): Etapa
    {
        $nova = $etapa === null;
        $etapa ??= new Etapa;

        DB::transaction(function () use ($frente, $dados, $etapa) {
            $terminoAntigo = $etapa->termino_em;
            $etapa->fill($dados);

            if (! $etapa->exists) {
                $etapa->forceFill(['frente_id' => $frente->id, 'ordem' => (int) $frente->etapas()->max('ordem') + 1]);
            }

            $etapa->save();

            if ($terminoAntigo && $etapa->termino_em && ! $terminoAntigo->isSameDay($etapa->termino_em)) {
                ($this->deslocar)(
                    $frente->etapas()->where('ordem', '>', $etapa->ordem)->get(),
                    $this->diasUteis->entre($terminoAntigo, $etapa->termino_em),
                );
            }
        });

        Atividade::registrar($nova ? 'etapa.adicionada' : 'etapa.alterada', $etapa, ['etapa' => $etapa->titulo]);

        return $etapa;
    }
}
