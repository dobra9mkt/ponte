@props(['titulo' => null])
<section {{ $attributes->class('rounded-cartao bg-white p-5 shadow-cartao sm:p-6') }}>
    @if ($titulo)
        <h2 class="mb-4 text-lg font-semibold">{{ $titulo }}</h2>
    @endif
    {{ $slot }}
</section>
