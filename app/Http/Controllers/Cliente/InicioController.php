<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Frente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InicioController extends Controller
{
    /**
     * Sem tela de lista: abre o serviço com aprovação pendente ou o mais recente (RN-16b).
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        $cliente = $request->user()?->cliente;
        $frentes = $cliente?->frentesNoPortal() ?? collect();

        if ($frentes->isEmpty()) {
            return view('cliente.inicio', ['cliente' => $cliente]);
        }

        $escolhida = $frentes->firstWhere('aguardando', true)
            ?? $frentes->filter(fn (Frente $frente) => $frente->estaAtiva())->last()
            ?? $frentes->last();

        return redirect()->route('frentes.ver', $escolhida);
    }
}
