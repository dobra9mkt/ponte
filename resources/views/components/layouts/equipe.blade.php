@props(['titulo' => null, 'subtitulo' => null, 'voltar' => null])
@php
    $usuario = auth()->user();
    $itens = [
        ['rota' => 'equipe.painel', 'ativo' => 'equipe.painel', 'nome' => 'Painel', 'icone' => 'painel'],
        ['rota' => 'equipe.clientes.index', 'ativo' => 'equipe.clientes.*', 'nome' => 'Clientes', 'icone' => 'clientes'],
    ];
    $iniciais = collect(explode(' ', (string) $usuario?->name))->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
@endphp
<x-layouts.base :titulo="$titulo" corpo="bg-off">
    <div x-data="menu" class="min-h-dvh">
        <header class="sticky top-0 z-30 flex items-center justify-between bg-navy px-4 py-2 text-white lg:hidden">
            <a href="{{ route('equipe.painel') }}" class="flex min-h-11 items-center font-titulo font-bold">Ponte</a>
            <button type="button" class="flex min-h-11 min-w-11 items-center justify-center rounded-campo hover:bg-white/10" x-on:click="alternar" x-bind:aria-expanded="aberto" aria-controls="barra-lateral" aria-label="Menu">
                <x-icone nome="menu" class="h-6 w-6" />
            </button>
        </header>

        <div class="fixed inset-0 z-40 bg-navy/60 lg:hidden" x-show="aberto" x-cloak x-on:click="fechar"></div>

        <aside id="barra-lateral" x-bind:class="classeBarra" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-navy text-white transition-transform duration-200 lg:translate-x-0">
            <div class="flex items-center justify-between px-5 pb-6 pt-6">
                <a href="{{ route('equipe.painel') }}" class="flex items-center gap-3">
                    <img src="{{ asset('marca/dobra9-simbolo.svg') }}" alt="" class="h-9 w-9 rounded-lg bg-white p-1">
                    <span class="leading-tight">
                        <span class="block font-titulo text-lg font-bold">Ponte</span>
                        <span class="block text-xs text-white/60">Dobra 9 · Equipe</span>
                    </span>
                </a>
                <button type="button" class="flex min-h-11 min-w-11 items-center justify-center rounded-campo hover:bg-white/10 lg:hidden" x-on:click="fechar" aria-label="Fechar menu">
                    <x-icone nome="fechar" class="h-5 w-5" />
                </button>
            </div>

            <nav class="flex-1 space-y-1 px-3" aria-label="Principal">
                @foreach ($itens as $item)
                    @php($ativo = request()->routeIs($item['ativo']))
                    <a href="{{ route($item['rota']) }}" @class([
                        'flex min-h-11 items-center gap-3 rounded-campo px-3 text-[0.95rem] font-medium transition',
                        'bg-white/12 text-white shadow-[inset_3px_0_0_var(--color-azul-vivo)]' => $ativo,
                        'text-white/75 hover:bg-white/8 hover:text-white' => ! $ativo,
                    ]) @if ($ativo) aria-current="page" @endif>
                        <x-icone :nome="$item['icone']" class="h-5 w-5" />
                        {{ $item['nome'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/10 p-3">
                <a href="{{ route('equipe.conta') }}" @class(['flex min-h-11 items-center gap-3 rounded-campo px-3 py-2 transition hover:bg-white/8', 'bg-white/12' => request()->routeIs('equipe.conta*')])>
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-azul-vivo/20 text-xs font-bold text-azul-vivo">{{ $iniciais }}</span>
                    <span class="min-w-0 leading-tight">
                        <span class="block truncate text-sm font-medium">{{ $usuario?->name }}</span>
                        <span class="block text-xs text-white/60">Minha conta</span>
                    </span>
                </a>
                <form method="POST" action="{{ route('sair') }}">
                    @csrf
                    <button class="flex min-h-11 w-full items-center gap-3 rounded-campo px-3 text-sm text-white/70 transition hover:bg-white/8 hover:text-white">
                        <x-icone nome="sair" class="h-5 w-5" />
                        Sair
                    </button>
                </form>
            </div>
        </aside>

        <div class="lg:pl-64">
            <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-10 lg:py-10">
                @if ($titulo)
                    <div class="mb-6 flex flex-wrap items-end justify-between gap-4 lg:mb-8">
                        <div class="min-w-0">
                            @if ($voltar)
                                <a href="{{ $voltar['url'] }}" class="mb-2 inline-flex items-center gap-1 text-sm font-medium text-tec hover:underline">
                                    <x-icone nome="voltar" class="h-4 w-4" />
                                    {{ $voltar['nome'] }}
                                </a>
                            @endif
                            <h1 class="text-2xl font-bold lg:text-[1.75rem]">{{ $cabecalho ?? $titulo }}</h1>
                            @if ($subtitulo)
                                <p class="mt-1 text-mudo">{{ $subtitulo }}</p>
                            @endif
                        </div>
                        @isset($acoes)
                            <div class="flex flex-wrap items-center gap-2">{{ $acoes }}</div>
                        @endisset
                    </div>
                @endif

                <x-mensagens />
                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.base>
