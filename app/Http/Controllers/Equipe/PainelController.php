<?php

namespace App\Http\Controllers\Equipe;

use App\Enums\SituacaoCliente;
use App\Http\Controllers\Controller;
use App\Models\Atividade;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PainelController extends Controller
{
    public function __invoke(Request $request): View
    {
        $ativos = Cliente::query()->where('situacao', SituacaoCliente::Ativo);
        $hora = now()->hour;

        return view('equipe.painel', [
            'saudacao' => match (true) {
                $hora < 12 => 'Bom dia',
                $hora < 18 => 'Boa tarde',
                default => 'Boa noite',
            },
            'primeiroNome' => strtok((string) $request->user()?->name, ' '),
            'clientesAtivos' => (clone $ativos)->count(),
            'semSenha' => (clone $ativos)->whereHas('usuario', fn ($q) => $q->whereNull('password'))->count(),
            'falhasHoje' => Atividade::query()->where('acao', 'login.falhou')->where('created_at', '>=', now()->subDay())->count(),
            'clientes' => (clone $ativos)->with('usuario.convites')->latest('id')->limit(8)->get(),
            'aguardandoSenha' => (clone $ativos)
                ->whereHas('usuario', fn ($q) => $q->whereNull('password'))
                ->with('usuario.convites')
                ->orderBy('empresa')
                ->limit(8)
                ->get(),
        ]);
    }
}
