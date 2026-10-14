<?php

namespace App\Http\Controllers\Equipe;

use App\Acoes\AdicionarAprovacao;
use App\Acoes\AlterarPrazo;
use App\Acoes\ExcluirRascunho;
use App\Acoes\MostrarParaCliente;
use App\Acoes\SalvarAprovacao;
use App\Http\Controllers\Controller;
use App\Http\Requests\Equipe\AlterarPrazoRequest;
use App\Http\Requests\Equipe\SalvarAprovacaoRequest;
use App\Models\Entrega;
use App\Models\Etapa;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;

class AprovacaoController extends Controller
{
    use VoltaParaFase;

    public function store(Etapa $etapa, AdicionarAprovacao $adicionar): RedirectResponse
    {
        $card = $adicionar($etapa);

        return $this->voltarParaFase($etapa)->with('card_aberto', $card->id);
    }

    public function update(SalvarAprovacaoRequest $request, Entrega $entrega, SalvarAprovacao $salvar, MostrarParaCliente $mostrar): RedirectResponse
    {
        $salvar($entrega, $request->dadosDoCard(), array_values($request->file('arquivos', [])));

        if (! $request->boolean('mostrar')) {
            return $this->voltarParaFase($entrega->etapa, __('equipe.rascunho_salvo'));
        }

        $mostrar($entrega);

        return $this->voltarParaFase($entrega->etapa, __('equipe.card_mostrado'));
    }

    public function destroy(Entrega $entrega, ExcluirRascunho $excluir): RedirectResponse
    {
        $excluir($entrega);

        return $this->voltarParaFase($entrega->etapa, __('equipe.rascunho_excluido'));
    }

    public function prazo(AlterarPrazoRequest $request, Entrega $entrega, AlterarPrazo $alterar): RedirectResponse
    {
        $alterar($entrega, CarbonImmutable::parse($request->validated('responder_ate')));

        return $this->voltarParaFase($entrega->etapa, __('equipe.prazo_alterado'));
    }
}
