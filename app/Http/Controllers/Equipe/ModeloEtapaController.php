<?php

namespace App\Http\Controllers\Equipe;

use App\Acoes\OrganizarEtapas;
use App\Http\Controllers\Controller;
use App\Http\Requests\Equipe\MoverRequest;
use App\Http\Requests\Equipe\SalvarModeloEtapaRequest;
use App\Models\Atividade;
use App\Models\Modelo;
use App\Models\ModeloEtapa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ModeloEtapaController extends Controller
{
    public function store(SalvarModeloEtapaRequest $request, Modelo $modelo): RedirectResponse
    {
        $fase = new ModeloEtapa;
        $fase->fill($request->validated());
        $fase->forceFill(['modelo_id' => $modelo->id, 'ordem' => (int) $modelo->etapas()->max('ordem') + 1])->save();

        return $this->voltar($modelo, $fase, __('equipe.fase_adicionada'));
    }

    public function update(SalvarModeloEtapaRequest $request, ModeloEtapa $modeloEtapa): RedirectResponse
    {
        $modeloEtapa->update($request->validated());

        return $this->voltar($modeloEtapa->modelo, $modeloEtapa, __('equipe.salvo'));
    }

    public function destroy(ModeloEtapa $modeloEtapa): RedirectResponse
    {
        DB::transaction(function () use ($modeloEtapa) {
            $modeloEtapa->delete();
            OrganizarEtapas::renumerar(ModeloEtapa::query()->where('modelo_id', $modeloEtapa->modelo_id));
        });

        return $this->voltar($modeloEtapa->modelo, null, __('equipe.fase_removida'));
    }

    public function mover(MoverRequest $request, ModeloEtapa $modeloEtapa): RedirectResponse
    {
        OrganizarEtapas::trocarOrdem(
            $modeloEtapa,
            ModeloEtapa::query()->where('modelo_id', $modeloEtapa->modelo_id),
            $request->string('direcao')->toString(),
        );

        return $this->voltar($modeloEtapa->modelo, $modeloEtapa);
    }

    private function voltar(Modelo $modelo, ?ModeloEtapa $fase, ?string $mensagem = null): RedirectResponse
    {
        Atividade::registrar('servico.alterado', $modelo, ['servico' => $modelo->nome]);

        $resposta = redirect()->to(route('equipe.servicos.ver', $modelo).($fase ? '#fase-'.$fase->id : ''));

        return $mensagem ? $resposta->with('sucesso', $mensagem) : $resposta;
    }
}
