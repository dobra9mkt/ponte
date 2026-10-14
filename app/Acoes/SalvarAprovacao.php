<?php

namespace App\Acoes;

use App\Enums\SituacaoCard;
use App\Models\Entrega;
use App\Servicos\ArquivosEntrega;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalvarAprovacao
{
    public function __construct(private ArquivosEntrega $arquivos) {}

    /**
     * @param  array<string, mixed>  $dados  nota, links e ids dos arquivos a remover
     * @param  list<UploadedFile>  $uploads
     */
    public function __invoke(Entrega $card, array $dados, array $uploads): void
    {
        $novos = $this->arquivos->preparar($uploads);

        try {
            DB::transaction(function () use ($card, $dados, $novos) {
                $card = Entrega::query()->lockForUpdate()->findOrFail($card->id);

                if ($card->situacao !== SituacaoCard::Rascunho) {
                    throw ValidationException::withMessages(['card' => __('equipe.card_desatualizado')]);
                }

                $card->fill(['nota' => $dados['nota'] ?? null, 'links' => $dados['links'] ?? [], 'texto_aprovar' => $dados['texto_aprovar'] ?? null])->save();
                $card->arquivos()->whereIn('id', $dados['remover'] ?? [])->get()->each->delete();
                $this->arquivos->registrar($card, $novos);
            });
        } catch (\Throwable $erro) {
            $this->arquivos->descartar($novos);
            throw $erro;
        }
    }
}
