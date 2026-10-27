@props(['titulo' => null, 'discreto' => false])
<x-layouts.base :titulo="$titulo" corpo="min-h-dvh bg-navy">
    <main class="flex min-h-dvh flex-col items-center justify-center px-4 py-10">
        @unless ($discreto)
            <div class="mb-8 flex flex-col items-start gap-[7px]">
                <img src="{{ asset('marca/dobra9-logo-branco.svg') }}" alt="Dobra 9" width="221" height="24" class="h-6 w-auto">
                <span class="whitespace-nowrap font-titulo text-[0.6rem] font-medium uppercase leading-none tracking-[0.28em] text-[#cfdaea]">{{ __('ponte.assinatura') }}</span>
            </div>
        @endunless
        <div class="w-full max-w-md rounded-cartao bg-white p-6 shadow-cartao sm:p-8">
            {{ $slot }}
        </div>
        @isset($rodape)
            <div class="mt-6 text-center text-sm text-white/70">{{ $rodape }}</div>
        @endisset
    </main>
</x-layouts.base>
