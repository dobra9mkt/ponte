<?php

namespace App\Http\Controllers\Equipe;

use App\Acoes\AlterarVisibilidadeFrente;
use App\Acoes\CriarFrente;
use App\Acoes\ExcluirFrente;
use App\Acoes\SalvarFrente;
use App\Http\Controllers\Controller;
use App\Http\Requests\Equipe\CriarFrenteRequest;
use App\Http\Requests\Equipe\SalvarFrenteRequest;
use App\Models\Cliente;
use App\Models\Frente;
use App\Models\Modelo;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrenteController extends Controller
{
    public function store(CriarFrenteRequest $request, Cliente $cliente, CriarFrente $criar): RedirectResponse
    {
        $modelo = Modelo::query()->with('etapas')->find($request->validated('modelo_id'));
        $data = fn (string $campo) => $request->validated($campo) ? CarbonImmutable::parse($request->validated($campo)) : null;

        $frente = $criar($cliente, $modelo, $modelo->nome ?? $request->validated('nome'), $data('inicio_em'), $data('prazo_final'));

        return redirect()->route('equipe.frentes.ver', [$cliente, $frente])->with('sucesso', __('equipe.servico_cadastrado'));
    }

    public function show(Cliente $cliente, Frente $frente): View
    {
        $frente->setRelation('cliente', $cliente)
            ->load(['etapas.entregas' => fn ($q) => $q->with(['arquivos', 'aprovacao.usuario', 'pedido.arquivos'])]);
        $frente->etapas->each->setRelation('frente', $frente);

        return view('equipe.frentes.ver', ['frente' => $frente, 'cliente' => $cliente]);
    }

    public function update(SalvarFrenteRequest $request, Cliente $cliente, Frente $frente, SalvarFrente $salvar): RedirectResponse
    {
        $salvar($frente, $request->validated());

        return redirect()->route('equipe.frentes.ver', [$cliente, $frente])->with('sucesso', __('equipe.salvo'));
    }

    public function visibilidade(Request $request, Cliente $cliente, Frente $frente, AlterarVisibilidadeFrente $alterar): RedirectResponse
    {
        $mostrar = $request->boolean('mostrar');
        $alterar($frente, $mostrar);

        return redirect()->route('equipe.frentes.ver', [$cliente, $frente])->with('sucesso', __($mostrar ? 'equipe.servico_mostrado' : 'equipe.servico_ocultado'));
    }

    public function destroy(Cliente $cliente, Frente $frente, ExcluirFrente $excluir): RedirectResponse
    {
        $excluir($frente);

        return redirect()->route('equipe.clientes.show', $cliente)->with('sucesso', __('equipe.servico_excluido', ['servico' => $frente->nome]));
    }
}
