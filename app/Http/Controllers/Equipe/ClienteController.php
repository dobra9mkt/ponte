<?php

namespace App\Http\Controllers\Equipe;

use App\Acoes\ExcluirCliente;
use App\Acoes\SalvarCliente;
use App\Http\Controllers\Controller;
use App\Http\Requests\Equipe\SalvarClienteRequest;
use App\Models\Atividade;
use App\Models\Cliente;
use App\Models\Modelo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(): View
    {
        return view('equipe.clientes.index', [
            'clientes' => Cliente::query()->with('usuario.convites')->orderBy('empresa')->get(),
        ]);
    }

    public function create(): View
    {
        return view('equipe.clientes.form', ['cliente' => new Cliente]);
    }

    public function store(SalvarClienteRequest $request, SalvarCliente $salvar): RedirectResponse
    {
        $cliente = $salvar($request->safe()->except('contrato'), contrato: $request->file('contrato'));

        return redirect()->route('equipe.clientes.show', $cliente)->with('sucesso', __('ponte.clientes.salvo'));
    }

    public function show(Cliente $cliente): View
    {
        return view('equipe.clientes.show', [
            'cliente' => $cliente->load(['usuario', 'frentes' => fn ($consulta) => $consulta->with('etapas')->latest('id')]),
            'servicos' => Modelo::query()->orderBy('nome')->get(),
            'atividades' => Atividade::query()->with('usuario')->where('cliente_id', $cliente->id)->latest('id')->limit(30)->get(),
            'totalAtividades' => Atividade::query()->where('cliente_id', $cliente->id)->count(),
        ]);
    }

    public function edit(Cliente $cliente): View
    {
        return view('equipe.clientes.form', ['cliente' => $cliente]);
    }

    public function update(SalvarClienteRequest $request, Cliente $cliente, SalvarCliente $salvar): RedirectResponse
    {
        $salvar($request->safe()->except('contrato'), $cliente, $request->file('contrato'));

        return redirect()->route('equipe.clientes.show', $cliente)->with('sucesso', __('ponte.clientes.salvo'));
    }

    public function destroy(Cliente $cliente, ExcluirCliente $excluir): RedirectResponse
    {
        $excluir($cliente);

        return redirect()->route('equipe.clientes.index')->with('sucesso', __('equipe.cliente_excluido', ['cliente' => $cliente->empresa]));
    }
}
