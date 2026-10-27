@props(['titulo' => null, 'discreto' => false])
<x-layouts.base :titulo="$titulo" corpo="min-h-dvh bg-navy">
    <main class="flex min-h-dvh flex-col items-center justify-center px-4 py-10">
        @unless ($discreto)
            <x-marca clara class="mb-8" />
        @endunless
        <div class="w-full max-w-md rounded-cartao bg-white p-6 shadow-cartao sm:p-8">
            {{ $slot }}
        </div>
        @isset($rodape)
            <div class="mt-6 text-center text-sm text-white/70">{{ $rodape }}</div>
        @endisset
    </main>
</x-layouts.base>
