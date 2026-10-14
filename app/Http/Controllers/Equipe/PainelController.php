<?php

namespace App\Http\Controllers\Equipe;

use App\Enums\EstadoEtapa;
use App\Enums\SituacaoCard;
use App\Enums\SituacaoCliente;
use App\Http\Controllers\Controller;
use App\Models\Atividade;
use App\Models\Cliente;
use App\Models\Entrega;
use App\Models\Etapa;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PainelController extends Controller
{
    public function __invoke(Request $request): View
    {
        $aguardando = Entrega::query()
            ->with('etapa.frente.cliente')
            ->where('situacao', SituacaoCard::AguardandoCliente)
            ->whereHas('etapa.frente.cliente')
            ->orderBy('responder_ate')
            ->get();

        return view('equipe.painel', [
            'saudacao' => $this->saudacao(),
            'primeiroNome' => strtok((string) $request->user()?->name, ' '),
            'vencidos' => $aguardando->filter->estaVencida()->values(),
            'aguardando' => $aguardando->reject->estaVencida()->values(),
            'ajustes' => Etapa::query()
                ->with('frente.cliente')
                ->whereHas('frente.cliente')
                ->where('estado', EstadoEtapa::AjustesEmAndamento)
                ->oldest('updated_at')
                ->get(),
            'semAcesso' => Cliente::query()
                ->where('situacao', SituacaoCliente::Ativo)
                ->whereHas('usuario', fn ($q) => $q->whereNull('password'))
                ->with('usuario.convites')
                ->orderBy('empresa')
                ->get(),
            'falhasLogin' => Atividade::query()->where('acao', 'login.falhou')->where('created_at', '>=', now()->subDay())->count(),
        ]);
    }

    private function saudacao(): string
    {
        return match (true) {
            now()->hour < 12 => 'Bom dia',
            now()->hour < 18 => 'Boa tarde',
            default => 'Boa noite',
        };
    }
}
