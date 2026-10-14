@php
    $grupos = [
        ['titulo' => __('equipe.clientes'), 'itens' => $clientes->map(fn ($cliente) => [
            'nome' => $cliente->empresa,
            'detalhe' => trans_choice('{0} Sem serviços|{1} 1 serviço|[2,*] :count serviços', $cliente->frentes_count),
            'data' => $cliente->deleted_at,
            'rota' => route('equipe.lixeira.cliente', $cliente),
            'descartar' => route('equipe.lixeira.cliente.descartar', $cliente),
        ])],
        ['titulo' => __('equipe.servicos'), 'itens' => $servicos->map(fn ($frente) => [
            'nome' => $frente->nome,
            'detalhe' => $frente->cliente->empresa,
            'data' => $frente->deleted_at,
            'rota' => route('equipe.lixeira.servico', $frente),
            'descartar' => route('equipe.lixeira.servico.descartar', $frente),
        ])],
        ['titulo' => __('equipe.fases'), 'itens' => $fases->map(fn ($fase) => [
            'nome' => $fase->titulo,
            'detalhe' => $fase->frente->cliente->empresa.' · '.$fase->frente->nome,
            'data' => $fase->deleted_at,
            'rota' => route('equipe.lixeira.fase', $fase),
            'descartar' => route('equipe.lixeira.fase.descartar', $fase),
        ])],
    ];
    $vazia = collect($grupos)->every(fn ($grupo) => $grupo['itens']->isEmpty());
@endphp
<x-layouts.equipe :titulo="__('equipe.menu_lixeira')" :subtitulo="__('equipe.lixeira_subtitulo')">
    @if ($vazia)
        <p class="rounded-cartao bg-white px-6 py-10 text-center text-mudo shadow-cartao">{{ __('equipe.lixeira_vazia') }}</p>
    @else
        <x-excluir class="mb-6 max-w-4xl" :acao="route('equipe.lixeira.esvaziar')" :rotulo="__('equipe.esvaziar_lixeira')" :mensagem="__('equipe.esvaziar_lixeira_confirmar')" :confirmar="__('equipe.esvaziar_lixeira')" />
    @endif

    <div class="max-w-4xl space-y-8">
        @foreach ($grupos as $grupo)
            @continue($grupo['itens']->isEmpty())
            <section>
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-mudo">{{ $grupo['titulo'] }}</h2>
                <ul class="divide-y divide-linha overflow-hidden rounded-cartao bg-white shadow-cartao">
                    @foreach ($grupo['itens'] as $item)
                        <li class="flex flex-wrap items-center gap-x-4 gap-y-2 px-5 py-4">
                            <span class="min-w-0 flex-1">
                                <span class="block truncate font-medium text-navy">{{ $item['nome'] }}</span>
                                <span class="block truncate text-sm text-mudo">{{ $item['detalhe'] }} · {{ __('equipe.excluido_em', ['data' => $item['data']->format('d/m/Y H:i')]) }}</span>
                            </span>
                            <form method="POST" action="{{ $item['rota'] }}">
                                @csrf
                                <x-botao variante="secundario" class="min-h-10 py-1.5 text-sm">{{ __('equipe.restaurar') }}</x-botao>
                            </form>
                            <x-excluir class="w-full sm:w-auto" :acao="$item['descartar']" :rotulo="__('equipe.excluir_definitivo')" :mensagem="__('equipe.excluir_definitivo_confirmar', ['item' => $item['nome']])" :confirmar="__('equipe.excluir_definitivo')" />
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>
</x-layouts.equipe>
