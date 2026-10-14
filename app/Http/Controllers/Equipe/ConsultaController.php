<?php

namespace App\Http\Controllers\Equipe;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\View\View;

/**
 * Aba Clientes: somente visualização (RN-81).
 */
class ConsultaController extends Controller
{
    public function index(): View
    {
        return view('equipe.consulta.index', [
            'clientes' => Cliente::query()->with('frentes.etapas')->orderBy('empresa')->get(),
        ]);
    }

    public function show(Cliente $cliente): View
    {
        $cliente->load(['frentes' => fn ($q) => $q->with('etapas.entregasPublicadas.aprovacao')->oldest('id')]);

        return view('equipe.consulta.ver', ['cliente' => $cliente]);
    }
}
