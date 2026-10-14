<x-layouts.cliente :titulo="$etapa->titulo">
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('frentes.ver', $frente) }}" class="mb-6 inline-flex min-h-11 items-center gap-1 text-sm font-medium text-tec hover:underline"><x-icone nome="voltar" class="h-4 w-4" />{{ $frente->nomeParaCliente() }}</a>
        @if ($podeResponder)
            <x-aprovacao-cliente :entrega="$entrega" />
        @else
        <section class="portal-cartao overflow-hidden">
            <div class="border-b border-linha/70 bg-white p-6 sm:p-9">
                <x-etiqueta :tom="$etapa->estado->tom()">{{ $etapa->estado->rotuloCliente() }}</x-etiqueta>
                <h1 class="mt-5 break-words text-2xl font-semibold tracking-tight sm:text-3xl">{{ $etapa->titulo }}</h1>
                <p class="mt-3 text-sm text-mudo">{{ __('ponte.etapas.prevista', ['data' => $etapa->termino_em?->format('d/m/Y') ?? __('fluxo.a_combinar')]) }}</p>
                @if ($etapa->descricao_cliente)<p class="mt-6 whitespace-pre-line">{{ $etapa->descricao_cliente }}</p>@endif
                @if ($entrega && ! $podeResponder)
                    <x-botao variante="secundario" :href="route('entregas.ver', $entrega)" class="mt-6">{{ __('entregas.ver_detalhes') }}</x-botao>
                @endif
            </div>
            <div class="bg-off/40 p-6 sm:p-9">
                @if ($etapa->aviso_equipe)
                    <h2 class="mb-3 text-base font-semibold">{{ __('ponte.etapas.aviso_da_equipe') }}</h2>
                    <p class="whitespace-pre-line">{{ $etapa->aviso_equipe }}</p>
                @elseif ($etapa->estado->finalizada())
                    <h2 class="text-base font-semibold">{{ __('fluxo.etapa_concluida') }}</h2>
                    <p class="mt-2 text-sm text-mudo">{{ __('fluxo.etapa_concluida_texto') }}</p>
                @elseif ($etapa->estado === App\Enums\EstadoEtapa::EmProducao)
                    <p class="text-mudo">{{ __('ponte.etapas.em_producao') }}</p>
                @elseif ($etapa->estado === App\Enums\EstadoEtapa::AFazer)
                    <p class="text-mudo">{{ __('ponte.etapas.proxima') }}</p>
                @endif
            </div>
        </section>
        @endif
    </div>
</x-layouts.cliente>
