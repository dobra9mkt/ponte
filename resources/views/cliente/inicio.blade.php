<x-layouts.cliente :titulo="__('fluxo.acompanhamento')">
    <p class="mb-3 text-sm font-medium text-tec">{{ $cliente?->empresa }}</p>
    <x-boas-vindas class="mb-10" />

    <section class="portal-destaque p-7 sm:p-10">
        <x-icone nome="relogio" class="mb-6 h-8 w-8 text-azul-vivo" />
        <h2 class="text-xl font-semibold text-white">{{ __('fluxo.preparando_titulo') }}</h2>
        <p class="mt-3 max-w-xl text-white/80">{{ __('ponte.inicio.preparando') }}</p>
    </section>
</x-layouts.cliente>
