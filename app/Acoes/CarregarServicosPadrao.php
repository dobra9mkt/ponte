<?php

namespace App\Acoes;

use App\Models\Modelo;
use App\Models\ModeloEtapa;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Yaml\Yaml;

/**
 * Carga inicial dos serviços de dados/modelos/*.yaml. Serviços que já existem, mesmo desativados,
 * não são tocados: depois da carga, a equipe edita pela tela Serviços (RN-10a).
 */
class CarregarServicosPadrao
{
    public function __invoke(?string $somente = null): void
    {
        foreach (glob(base_path('dados/modelos/*.yaml')) ?: [] as $arquivo) {
            $slug = basename($arquivo, '.yaml');

            if (($somente !== null && $slug !== $somente) || Modelo::withTrashed()->where('slug', $slug)->exists()) {
                continue;
            }

            /** @var array{nome: string, fases: list<string>} $dados */
            $dados = Yaml::parseFile($arquivo);

            DB::transaction(fn () => $this->criar($slug, $dados));
        }
    }

    /**
     * @param  array{nome: string, fases: list<string>}  $dados
     */
    private function criar(string $slug, array $dados): void
    {
        $modelo = new Modelo;
        $modelo->fill(['nome' => $dados['nome']]);
        $modelo->forceFill(['slug' => $slug])->save();

        foreach ($dados['fases'] as $indice => $titulo) {
            $fase = new ModeloEtapa;
            $fase->fill(['titulo' => $titulo]);
            $fase->forceFill(['modelo_id' => $modelo->id, 'ordem' => $indice + 1])->save();
        }
    }
}
