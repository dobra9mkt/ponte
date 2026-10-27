<?php

namespace App\Acoes;

use App\Models\Atividade;
use App\Models\Etapa;
use App\Models\Frente;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrganizarEtapas
{
    public function mover(Etapa $etapa, string $direcao): void
    {
        self::trocarOrdem($etapa, Etapa::query()->where('frente_id', $etapa->frente_id), $direcao);

        Atividade::registrar('etapa.reordenada', $etapa, ['etapa' => $etapa->titulo]);
    }

    /**
     * Grava a ordem vinda do arrastar. Só aceita a lista completa das fases do próprio serviço,
     * para que IDs de outro serviço nunca sejam tocados.
     *
     * @param  list<int>  $ids
     */
    public function ordenar(Frente $frente, array $ids): bool
    {
        return DB::transaction(function () use ($frente, $ids) {
            $fases = $frente->etapas()->lockForUpdate()->get()->keyBy('id');

            if (count($ids) !== $fases->count() || array_diff($ids, $fases->keys()->all()) !== []) {
                return false;
            }

            foreach ($ids as $indice => $id) {
                $fases[$id]->forceFill(['ordem' => $indice + 1])->save();
            }

            Atividade::registrar('fases.reordenadas', $frente);

            return true;
        });
    }

    public function alternarVisibilidade(Etapa $etapa): void
    {
        $etapa->forceFill(['oculta' => ! $etapa->oculta])->save();

        Atividade::registrar($etapa->oculta ? 'etapa.ocultada' : 'etapa.mostrada', $etapa, ['etapa' => $etapa->titulo]);
    }

    /**
     * Vai para a lixeira mantendo a ordem, para voltar ao mesmo lugar (RN-82).
     */
    public function remover(Etapa $etapa): void
    {
        $etapa->delete();

        Atividade::registrar('etapa.excluida', $etapa, ['etapa' => $etapa->titulo]);
    }

    /**
     * @template TModel of Model
     *
     * @param  TModel  $item
     * @param  Builder<TModel>  $irmaos
     */
    public static function trocarOrdem(Model $item, Builder $irmaos, string $direcao): void
    {
        $subir = $direcao === 'subir';
        $vizinho = $irmaos->where('ordem', $subir ? '<' : '>', $item->getAttribute('ordem'))
            ->orderBy('ordem', $subir ? 'desc' : 'asc')
            ->first();

        if ($vizinho === null) {
            return;
        }

        DB::transaction(function () use ($item, $vizinho) {
            $ordem = $item->getAttribute('ordem');
            $item->forceFill(['ordem' => $vizinho->getAttribute('ordem')])->save();
            $vizinho->forceFill(['ordem' => $ordem])->save();
        });
    }

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $irmaos
     */
    public static function renumerar(Builder $irmaos): void
    {
        $irmaos->orderBy('ordem')->get()->values()->each(
            fn (Model $item, int $indice) => $item->forceFill(['ordem' => $indice + 1])->save()
        );
    }
}
