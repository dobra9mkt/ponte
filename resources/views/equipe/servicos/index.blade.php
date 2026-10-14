<x-layouts.equipe :titulo="__('equipe.menu_servicos')" :subtitulo="__('equipe.servicos_subtitulo')">
    <div x-data="alternar" @if ($errors->has('nome')) x-init="abrir" @endif class="mb-6">
        <x-botao type="button" x-on:click="abrir" x-show="!aberto"><x-icone nome="mais" class="h-4 w-4" />{{ __('equipe.novo_servico') }}</x-botao>
        <form method="POST" action="{{ route('equipe.servicos.store') }}" x-show="aberto" x-cloak class="flex max-w-xl flex-wrap items-end gap-3 rounded-cartao bg-white p-5 shadow-cartao">
            @csrf
            <div class="min-w-0 flex-1">
                <x-campo nome="nome" :rotulo="__('equipe.nome_servico')" required maxlength="100" />
            </div>
            <x-botao>{{ __('equipe.criar') }}</x-botao>
            <x-botao variante="discreto" type="button" x-on:click="fechar">{{ __('equipe.cancelar') }}</x-botao>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($modelos as $modelo)
            <a href="{{ route('equipe.servicos.ver', $modelo) }}" class="group flex flex-col rounded-cartao bg-white p-5 shadow-cartao transition hover:ring-1 hover:ring-tec/40">
                <span class="flex items-start justify-between gap-3">
                    <span class="font-titulo text-[1.05rem] font-semibold text-navy">{{ $modelo->nome }}</span>
                    <x-icone nome="seta" class="mt-1 h-5 w-5 text-mudo transition group-hover:text-tec" />
                </span>
                <span class="mt-1 text-sm text-mudo">{{ trans_choice('equipe.qtd_fases', $modelo->etapas->count()) }}</span>
                @if ($modelo->etapas->isNotEmpty())
                    <ol class="mt-4 space-y-1.5 border-t border-linha pt-4 text-sm">
                        @foreach ($modelo->etapas as $fase)
                            <li class="flex gap-2.5"><span class="w-5 shrink-0 text-right tabular-nums text-mudo">{{ $loop->iteration }}</span><span class="min-w-0 text-texto">{{ $fase->titulo }}</span></li>
                        @endforeach
                    </ol>
                @endif
            </a>
        @endforeach
    </div>
</x-layouts.equipe>
