<?php

namespace App\Acoes;

use App\Models\Atividade;
use App\Models\Cliente;
use App\Models\Etapa;
use App\Models\Frente;
use App\Models\Modelo;
use App\Models\ModeloEtapa;
use App\Servicos\DiasUteis;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class CriarFrente
{
    public function __construct(private DiasUteis $diasUteis) {}

    /**
     * Serviço com as fases do fluxo padrão; sem modelo, começa sem fases (RN-10, RN-13).
     */
    public function __invoke(Cliente $cliente, ?Modelo $modelo, string $nome, ?CarbonInterface $inicio = null, ?CarbonInterface $termino = null): Frente
    {
        return DB::transaction(function () use ($cliente, $modelo, $nome, $inicio, $termino) {
            $frente = new Frente;
            $frente->fill(['nome' => $nome, 'nome_cliente' => $modelo?->nome_cliente, 'revisoes_incluidas' => 3, 'inicio_em' => $inicio, 'prazo_final' => $termino]);
            $frente->forceFill(['cliente_id' => $cliente->id, 'modelo_id' => $modelo?->id])->save();

            $this->copiarFases($frente, $modelo, $inicio);

            Atividade::registrar('frente.criada', $frente, ['frente' => $frente->nome]);

            return $frente;
        });
    }

    private function copiarFases(Frente $frente, ?Modelo $modelo, ?CarbonInterface $inicio): void
    {
        $cursor = $inicio;

        foreach ($modelo->etapas ?? [] as $indice => $padrao) {
            [$entrada, $termino] = $this->datas($padrao, $cursor);
            $cursor = $termino ?? $cursor;

            $fase = new Etapa;
            $fase->fill([
                'titulo' => $padrao->titulo,
                'responsavel' => $padrao->responsavel,
                'o_que_fazer' => $padrao->o_que_fazer,
                'entrada_em' => $entrada,
                'termino_em' => $termino,
            ]);
            $fase->forceFill(['frente_id' => $frente->id, 'ordem' => $indice + 1])->save();
        }
    }

    /** @return array{0: CarbonInterface|null, 1: CarbonInterface|null} */
    private function datas(ModeloEtapa $padrao, ?CarbonInterface $cursor): array
    {
        if ($cursor === null || $padrao->duracao_dias_uteis === null) {
            return [null, null];
        }

        return [$cursor, $this->diasUteis->somar($cursor, $padrao->duracao_dias_uteis)];
    }
}
