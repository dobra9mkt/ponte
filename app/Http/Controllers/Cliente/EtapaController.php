<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Etapa;
use App\Servicos\RespostaEntrega;
use Illuminate\View\View;

class EtapaController extends Controller
{
    public function __invoke(Etapa $etapa, RespostaEntrega $resposta): View
    {
        $entrega = $etapa->entregasPublicadas()->with('arquivos')->first()?->setRelation('etapa', $etapa);

        return view('cliente.etapa', [
            'etapa' => $etapa,
            'frente' => $etapa->frente,
            'entrega' => $entrega,
            'podeResponder' => $entrega && $resposta->podeResponder($entrega, $etapa),
        ]);
    }
}
