<?php

namespace App\Acoes;

use App\Models\Atividade;
use App\Models\Etapa;
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
