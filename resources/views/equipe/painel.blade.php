@php
    $colunas = [
        ['titulo' => __('equipe.vencidos'), 'tom' => 'alerta', 'itens' => $vencidos->map(fn ($card) => [
            'fase' => $card->etapa, 'detalhe' => __('equipe.venceu_em', ['data' => $card->responder_ate->format('d/m')]),
        ])],
        ['titulo' => __('equipe.aguardando'), 'tom' => 'atencao', 'itens' => $aguardando->map(fn ($card) => [
            'fase' => $card->etapa, 'detalhe' => __('equipe.ate', ['data' => $card->responder_ate->format('d/m')]),
        ])],
        ['titulo' => __('equipe.ajustes'), 'tom' => 'info', 'itens' => $ajustes->map(fn ($fase) => [
            'fase' => $fase, 'detalhe' => $fase->frente->nome,
        ])],
    ];
@endphp
<x-layouts.equipe :titulo="__('equipe.menu_painel')" :subtitulo="ucfirst(now()->translatedFormat('l, j \\d\\e F'))">
    <x-slot:cabecalho>{{ $saudacao }}, {{ $primeiroNome }}</x-slot:cabecalho>

    @if ($falhasLogin > config('ponte.alerta_falhas_login_por_hora'))
        <x-aviso tipo="erro" class="mb-6">{{ __('equipe.falhas_login', ['qtd' => $falhasLogin]) }}</x-aviso>
    @endif

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        @foreach ($colunas as $coluna)
            <section class="rounded-cartao bg-white shadow-cartao">
                <header class="flex items-center justify-between gap-3 border-b border-linha px-5 py-4">
                    <h2 class="text-base font-semibold">{{ $coluna['titulo'] }}</h2>
                    <x-etiqueta :tom="$coluna['itens']->isEmpty() ? 'neutro' : $coluna['tom']">{{ $coluna['itens']->count() }}</x-etiqueta>
                </header>
                @if ($coluna['itens']->isEmpty())
                    <p class="px-5 py-6 text-sm text-mudo">{{ __('equipe.nada') }}</p>
                @else
                    <ul class="divide-y divide-linha/70">
                        @foreach ($coluna['itens'] as $item)
                            @php($fase = $item['fase'])
                            <li>
                                <a href="{{ route('equipe.frentes.ver', [$fase->frente->cliente_id, $fase->frente_id]).'#fase-'.$fase->id }}" class="flex items-start gap-3 px-5 py-3.5 transition hover:bg-off">
                                    <x-cor-cliente :cor="$fase->frente->cliente->cor" class="mt-2" />
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-[0.95rem] font-medium text-navy">{{ $fase->titulo }}</span>
                                        <span class="block truncate text-sm text-mudo">{{ $fase->frente->cliente->empresa }} · {{ $item['detalhe'] }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    </div>

    @if ($semAcesso->isNotEmpty())
        <section class="mt-8">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-mudo">{{ __('equipe.sem_acesso') }}</h2>
            <ul class="flex flex-wrap gap-2">
                @foreach ($semAcesso as $cliente)
                    <li>
                        <a href="{{ route('equipe.clientes.show', $cliente) }}" class="flex min-h-11 items-center gap-2 rounded-full border border-linha bg-white px-4 text-sm font-medium text-navy transition hover:border-tec">
                            <x-cor-cliente :cor="$cliente->cor" />{{ $cliente->empresa }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</x-layouts.equipe>
