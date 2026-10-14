<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Frente;
use Illuminate\View\View;

class FrenteController extends Controller
{
    public function __invoke(Frente $frente): View
    {
        return view('cliente.frente', [
            'frente' => $frente,
            'servicos' => $frente->cliente->frentesNoPortal(),
            'etapas' => $frente->etapasVisiveis()
                ->with(['entregasPublicadas' => fn ($q) => $q->with(['aprovacao', 'pedido'])->reorder('versao')])
                ->get(),
        ]);
    }
}
