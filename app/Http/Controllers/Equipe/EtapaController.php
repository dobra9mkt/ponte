<?php

namespace App\Http\Controllers\Equipe;

use App\Acoes\ConcluirEtapa;
use App\Acoes\IniciarEtapa;
use App\Acoes\OrganizarEtapas;
use App\Acoes\SalvarEtapa;
use App\Http\Controllers\Controller;
use App\Http\Requests\Equipe\MoverRequest;
use App\Http\Requests\Equipe\SalvarEtapaRequest;
use App\Models\Cliente;
use App\Models\Etapa;
use App\Models\Frente;
use Illuminate\Http\RedirectResponse;

class EtapaController extends Controller
{
    use VoltaParaFase;

    public function store(SalvarEtapaRequest $request, Cliente $cliente, Frente $frente, SalvarEtapa $salvar): RedirectResponse
    {
        return $this->voltarParaFase($salvar($frente, $request->validated()), __('equipe.fase_adicionada'));
    }

    public function update(SalvarEtapaRequest $request, Etapa $etapa, SalvarEtapa $salvar): RedirectResponse
    {
        $salvar($etapa->frente, $request->validated(), $etapa);

        return $this->voltarParaFase($etapa, __('equipe.salvo'));
    }

    public function destroy(Etapa $etapa, OrganizarEtapas $organizar): RedirectResponse
    {
        $organizar->remover($etapa);

        return redirect()->route('equipe.frentes.ver', [$etapa->frente->cliente_id, $etapa->frente_id])->with('sucesso', __('equipe.fase_excluida'));
    }

    public function mover(MoverRequest $request, Etapa $etapa, OrganizarEtapas $organizar): RedirectResponse
    {
        $organizar->mover($etapa, $request->string('direcao')->toString());

        return $this->voltarParaFase($etapa);
    }

    public function visibilidade(Etapa $etapa, OrganizarEtapas $organizar): RedirectResponse
    {
        $organizar->alternarVisibilidade($etapa);

        return $this->voltarParaFase($etapa);
    }

    public function iniciar(Etapa $etapa, IniciarEtapa $iniciar): RedirectResponse
    {
        $iniciar($etapa);

        return $this->voltarParaFase($etapa);
    }

    public function concluir(Etapa $etapa, ConcluirEtapa $concluir): RedirectResponse
    {
        $concluir($etapa);

        return $this->voltarParaFase($etapa);
    }
}
