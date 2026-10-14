<x-layouts.equipe :titulo="__('equipe.consulta_titulo')">
    @if ($clientes->isEmpty())
        <p class="rounded-cartao bg-white px-6 py-10 text-center text-mudo shadow-cartao">{{ __('equipe.nada') }}</p>
    @else
        <ul class="divide-y divide-linha overflow-hidden rounded-cartao bg-white shadow-cartao">
            @foreach ($clientes as $cliente)
                @php
                    $fases = $cliente->frentes->flatMap->etapas;
                    $contagem = collect([
                        App\Enums\EstadoEtapa::AguardandoCliente,
                        App\Enums\EstadoEtapa::AjustesEmAndamento,
                        App\Enums\EstadoEtapa::EmProducao,
                    ])->mapWithKeys(fn ($estado) => [$estado->value => ['estado' => $estado, 'qtd' => $fases->where('estado', $estado)->count()]])
                        ->filter(fn ($item) => $item['qtd'] > 0);
                @endphp
                <li>
                    <a href="{{ route('equipe.consulta.ver', $cliente) }}" class="flex flex-wrap items-center gap-x-6 gap-y-2 px-5 py-4 transition hover:bg-off sm:px-6">
                        <span class="flex min-w-0 flex-1 basis-56 items-center gap-3">
                            <x-cor-cliente :cor="$cliente->cor" />
                            <span class="min-w-0">
                                <span class="block truncate font-titulo font-semibold text-navy">{{ $cliente->empresa }}</span>
                                <span class="block truncate text-sm text-mudo">{{ $cliente->frentes->pluck('nome')->implode(' · ') ?: __('equipe.sem_servicos') }}</span>
                            </span>
                        </span>
                        <span class="flex flex-wrap gap-1.5 pl-6 sm:pl-0">
                            @foreach ($contagem as $item)
                                <x-etiqueta :tom="$item['estado']->tom()">{{ $item['qtd'] }} · {{ $item['estado']->rotuloEquipe() }}</x-etiqueta>
                            @endforeach
                        </span>
                        <x-icone nome="seta" class="hidden h-4 w-4 text-mudo sm:block" />
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</x-layouts.equipe>
