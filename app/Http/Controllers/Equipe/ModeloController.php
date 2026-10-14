<?php

namespace App\Http\Controllers\Equipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Equipe\SalvarModeloRequest;
use App\Models\Atividade;
use App\Models\Modelo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ModeloController extends Controller
{
    public function index(): View
    {
        return view('equipe.servicos.index', [
            'modelos' => Modelo::query()->with('etapas')->orderBy('nome')->get(),
        ]);
    }

    public function store(SalvarModeloRequest $request): RedirectResponse
    {
        $modelo = new Modelo;
        $modelo->fill($request->validated());
        $modelo->forceFill(['slug' => Str::slug($modelo->nome).'-'.Str::lower(Str::random(6))])->save();

        Atividade::registrar('servico.criado', $modelo, ['servico' => $modelo->nome]);

        return redirect()->route('equipe.servicos.ver', $modelo);
    }

    public function show(Modelo $modelo): View
    {
        return view('equipe.servicos.ver', ['modelo' => $modelo->load('etapas')]);
    }

    public function update(SalvarModeloRequest $request, Modelo $modelo): RedirectResponse
    {
        $modelo->update($request->validated());

        Atividade::registrar('servico.alterado', $modelo, ['servico' => $modelo->nome]);

        return redirect()->route('equipe.servicos.ver', $modelo)->with('sucesso', __('equipe.salvo'));
    }

    public function destroy(Modelo $modelo): RedirectResponse
    {
        $modelo->delete();

        Atividade::registrar('servico.desativado', $modelo, ['servico' => $modelo->nome]);

        return redirect()->route('equipe.servicos.index')->with('sucesso', __('equipe.servico_desativado', ['servico' => $modelo->nome]));
    }
}
