@props(['titulo' => null])
@php($cliente = auth()->user()?->cliente)
<x-layouts.base :titulo="$titulo">
    <header class="bg-navy text-white" x-data="menu">
        <div class="mx-auto flex max-w-[720px] items-center justify-between gap-4 px-4 py-3">
            <a href="{{ route('inicio') }}" class="flex min-h-11 min-w-0 items-center gap-3">
                <img src="{{ asset('marca/dobra9-simbolo.svg') }}" alt="Dobra 9" class="h-8 w-8 shrink-0 rounded-lg bg-white p-1">
                <span class="truncate font-titulo font-semibold">{{ $cliente?->empresa }}</span>
            </a>
            <button type="button" class="flex min-h-11 min-w-11 items-center justify-center rounded-campo hover:bg-white/10" x-on:click="alternar" x-bind:aria-expanded="aberto" aria-controls="menu-cliente" aria-label="Menu">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
        <nav id="menu-cliente" class="border-t border-white/10" x-show="aberto" x-cloak x-on:click.outside="fechar">
            <ul class="mx-auto max-w-[720px] px-2 py-2">
                <li><a href="{{ route('inicio') }}" class="flex min-h-11 items-center rounded-campo px-3 hover:bg-white/10">Início</a></li>
                <li><a href="{{ route('conta') }}" class="flex min-h-11 items-center rounded-campo px-3 hover:bg-white/10">Minha conta</a></li>
                <li>
                    <form method="POST" action="{{ route('sair') }}">
                        @csrf
                        <button class="flex min-h-11 w-full items-center rounded-campo px-3 text-left hover:bg-white/10">Sair</button>
                    </form>
                </li>
            </ul>
        </nav>
    </header>
    <main class="mx-auto max-w-[720px] px-4 py-6 sm:py-8">
        <x-mensagens />
        {{ $slot }}
    </main>
    <footer class="mx-auto max-w-[720px] px-4 pb-8 text-center text-sm text-mudo">
        <a href="{{ route('privacidade') }}" class="link">Privacidade</a>
    </footer>
</x-layouts.base>
