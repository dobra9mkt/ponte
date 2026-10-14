@props(['atividades', 'mostrarIp' => false])
@php
    $nomes = [
        'entrada' => 'entrou no portal',
        'login.falhou' => 'tentativa de login errada',
        'cliente.cadastrado' => 'cadastrou o cliente',
        'cliente.alterado' => 'alterou o cadastro',
        'cliente.desativado' => 'desativou o cliente',
        'cliente.ativo' => 'reativou o cliente',
        'acesso.enviado' => 'enviou o acesso',
        'acesso.ativado' => 'criou a senha pelo convite',
        'contrato.baixado' => 'baixou o contrato',
        'senha.trocada' => 'trocou a senha',
        'senha.redefinida' => 'redefiniu a senha',
        'sessoes.encerradas' => 'saiu dos outros aparelhos',
        '2fa.configurado' => 'ativou a verificação em duas etapas',
        '2fa.novos_codigos' => 'gerou novos códigos de recuperação',
        '2fa.reiniciado' => 'trocou o aplicativo autenticador',
        'frente.criada' => 'adicionou o trabalho',
        'frente.alterada' => 'alterou o trabalho',
        'frente.mostrada' => 'liberou o acompanhamento do trabalho',
        'etapa.adicionada' => 'adicionou a etapa',
        'etapa.alterada' => 'alterou a etapa',
        'etapa.removida' => 'removeu a etapa',
        'etapa.reordenada' => 'mudou a ordem da etapa',
        'etapa.ocultada' => 'ocultou do cliente a etapa',
        'etapa.mostrada' => 'mostrou ao cliente a etapa',
        'etapa.iniciada' => 'iniciou a etapa',
        'etapa.concluida' => 'concluiu a etapa',
        'modelo.alterado' => 'alterou o modelo',
    ];
@endphp
@if ($atividades->isEmpty())
    <p class="text-sm text-mudo">Nada registrado ainda.</p>
@else
    <ol class="relative space-y-4 border-l border-linha pl-5">
        @foreach ($atividades as $atividade)
            <li class="relative">
                <span @class([
                    'absolute -left-[1.6rem] top-1.5 h-2.5 w-2.5 rounded-full ring-4 ring-white',
                    'bg-alerta' => $atividade->acao === 'login.falhou',
                    'bg-tec' => $atividade->acao !== 'login.falhou',
                ]) aria-hidden="true"></span>
                <p class="text-sm leading-snug">
                    <span class="font-semibold text-navy">{{ $atividade->usuario?->name ?? 'Visitante' }}</span>
                    <span class="text-texto">{{ $nomes[$atividade->acao] ?? $atividade->acao }}</span>
                    @if ($detalhe = $atividade->dados['etapa'] ?? $atividade->dados['frente'] ?? $atividade->dados['modelo'] ?? null)
                        <span class="font-medium text-navy">{{ $detalhe }}</span>
                    @endif
                    @if ($atividade->cliente_id && $atividade->relationLoaded('cliente') && $atividade->cliente && $atividade->usuario?->ehEquipe())
                        <span class="text-mudo">· {{ $atividade->cliente->empresa }}</span>
                    @endif
                </p>
                <p class="mt-0.5 text-xs text-mudo">
                    <time datetime="{{ $atividade->created_at->toIso8601String() }}" title="{{ $atividade->created_at->format('d/m/Y H:i') }}">{{ $atividade->created_at->diffForHumans(['options' => \Carbon\Carbon::JUST_NOW]) }}</time>
                    @if ($mostrarIp && $atividade->ip)
                        · IP {{ $atividade->ip }}
                    @endif
                </p>
            </li>
        @endforeach
    </ol>
@endif
