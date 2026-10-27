@props(['titulo' => null])
@php
    $cliente = auth()->user()?->cliente;
@endphp
<x-layouts.base :titulo="$titulo" corpo="portal-cliente">
    <a href="#conteudo" class="sr-only focus:not-sr-only focus:block focus:bg-white focus:p-4">{{ __('fluxo.acompanhamento') }}</a>
    <header class="border-b border-linha/70 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-3 px-5 py-4 sm:px-8">
            <a href="{{ route('inicio') }}" class="flex min-h-11 items-center">
                <x-marca />
            </a>
            <nav aria-label="{{ __('fluxo.minha_conta') }}" class="flex items-center gap-1 sm:gap-4">
                <a href="{{ route('conta') }}" aria-label="{{ __('fluxo.minha_conta') }}" class="inline-flex min-h-11 min-w-11 items-center justify-center gap-2 rounded-campo px-2 text-sm font-medium text-tec hover:bg-off"><x-icone nome="conta" class="h-5 w-5 sm:h-4 sm:w-4" /><span class="hidden sm:inline">{{ __('fluxo.minha_conta') }}</span></a>
                <form method="POST" action="{{ route('sair') }}">
                    @csrf
                    <button class="min-h-11 rounded-campo px-2 text-sm text-mudo hover:bg-off">{{ __('fluxo.sair') }}</button>
                </form>
            </nav>
        </div>
    </header>
    <main id="conteudo" class="mx-auto min-h-[75dvh] max-w-5xl px-5 py-8 sm:px-8 sm:py-12">
        <x-mensagens />
        {{ $slot }}
    </main>
    <footer class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-3 border-t border-linha/70 px-5 py-7 text-xs text-mudo sm:px-8">
        <p>{{ __('fluxo.rodape') }}</p>
        <a href="{{ route('privacidade') }}" class="inline-flex min-h-11 items-center hover:underline">{{ __('fluxo.privacidade') }}</a>
    </footer>
</x-layouts.base>
