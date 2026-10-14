<?php

namespace App\Http\Controllers\Equipe;

use App\Acoes\DescartarDaLixeira;
use App\Acoes\RestaurarDaLixeira;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Etapa;
use App\Models\Frente;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Só aparece o item de cima: serviço de cliente excluído e fase de serviço excluído voltam junto com ele (RN-82).
 */
class LixeiraController extends Controller
{
    public function index(): View
    {
        return view('equipe.lixeira', [
            'clientes' => Cliente::onlyTrashed()->whereNull('descartado_em')->withCount('frentes')->latest('deleted_at')->get(),
            'servicos' => Frente::onlyTrashed()->whereNull('descartado_em')->whereHas('cliente')->with('cliente')->latest('deleted_at')->get(),
            'fases' => Etapa::onlyTrashed()->whereNull('descartado_em')->whereHas('frente.cliente')->with('frente.cliente')->latest('deleted_at')->get(),
        ]);
    }

    public function cliente(Cliente $cliente, RestaurarDaLixeira $restaurar): RedirectResponse
    {
        $restaurar->cliente($cliente);

        return redirect()->route('equipe.clientes.show', $cliente)->with('sucesso', __('equipe.restaurado'));
    }

    public function servico(Frente $frente, RestaurarDaLixeira $restaurar): RedirectResponse
    {
        $restaurar->frente($frente);

        return redirect()->route('equipe.frentes.ver', [$frente->cliente_id, $frente])->with('sucesso', __('equipe.restaurado'));
    }

    public function fase(Etapa $etapa, RestaurarDaLixeira $restaurar): RedirectResponse
    {
        $restaurar->etapa($etapa);

        return redirect()->to(route('equipe.frentes.ver', [$etapa->frente->cliente_id, $etapa->frente_id]).'#fase-'.$etapa->id)
            ->with('sucesso', __('equipe.restaurado'));
    }

    public function descartarCliente(Cliente $cliente, DescartarDaLixeira $descartar): RedirectResponse
    {
        return $this->descartado($descartar, $cliente);
    }

    public function descartarServico(Frente $frente, DescartarDaLixeira $descartar): RedirectResponse
    {
        return $this->descartado($descartar, $frente);
    }

    public function descartarFase(Etapa $etapa, DescartarDaLixeira $descartar): RedirectResponse
    {
        return $this->descartado($descartar, $etapa);
    }

    public function esvaziar(DescartarDaLixeira $descartar): RedirectResponse
    {
        $descartar->esvaziar();

        return redirect()->route('equipe.lixeira')->with('sucesso', __('equipe.lixeira_esvaziada'));
    }

    private function descartado(DescartarDaLixeira $descartar, Cliente|Frente|Etapa $item): RedirectResponse
    {
        $descartar($item);

        return redirect()->route('equipe.lixeira')->with('sucesso', __('equipe.excluido_definitivo'));
    }
}
