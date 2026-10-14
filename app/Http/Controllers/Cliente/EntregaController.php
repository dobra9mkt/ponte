<?php

namespace App\Http\Controllers\Cliente;

use App\Acoes\AprovarEntrega;
use App\Acoes\PedirAjuste;
use App\Acoes\SalvarRascunhoAjuste;
use App\Http\Controllers\Controller;
use App\Http\Requests\AprovarEntregaRequest;
use App\Http\Requests\PedirAjusteRequest;
use App\Http\Requests\SalvarRascunhoRequest;
use App\Models\Entrega;
use App\Servicos\RespostaEntrega;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EntregaController extends Controller
{
    public function show(Entrega $entrega, RespostaEntrega $resposta): View
    {
        $etapa = $entrega->etapa;
        $frente = $etapa->frente;
        $etapa->setRelation('frente', $frente);
        $entrega->load(['arquivos', 'aprovacao', 'pedidos.arquivos']);
        $versoes = $etapa->entregasPublicadas()->with(['aprovacao', 'pedido'])->get();

        return view('cliente.entrega', [
            'entrega' => $entrega,
            'etapa' => $etapa,
            'frente' => $frente,
            'versoes' => $versoes,
            'atual' => $versoes->first(),
            'podeResponder' => $resposta->podeResponder($entrega, $etapa),
        ]);
    }

    public function aprovar(AprovarEntregaRequest $request, Entrega $entrega, AprovarEntrega $aprovar): RedirectResponse
    {
        $aprovar($entrega, $request->boolean('conferi'));

        return redirect()->route('frentes.ver', $entrega->etapa->frente_id)->with('sucesso', $entrega->ehVersaoFinal($entrega->etapa->frente->revisoes_incluidas) ? __('fluxo.versao_final_registrada') : __('entregas.aprovada'));
    }

    public function ajustes(PedirAjusteRequest $request, Entrega $entrega, PedirAjuste $pedir): RedirectResponse
    {
        $pedir($entrega, $request->safe()->except('imagens'), array_values($request->file('imagens', [])));

        $extra = $request->validated('modo') === 'extra';

        return redirect()->route('frentes.ver', $entrega->etapa->frente_id)
            ->with('sucesso', __($extra ? 'entregas.extra_enviado' : 'entregas.ajuste_enviado'));
    }

    public function rascunho(SalvarRascunhoRequest $request, Entrega $entrega, SalvarRascunhoAjuste $salvar): JsonResponse|RedirectResponse
    {
        $rascunho = $salvar($entrega, $request->validated('texto'), (int) $request->validated('revisao'));

        if ($request->expectsJson()) {
            return response()->json(['revisao' => $rascunho->revisao]);
        }

        return back()->with('sucesso', __('entregas.rascunho_salvo'));
    }
}
