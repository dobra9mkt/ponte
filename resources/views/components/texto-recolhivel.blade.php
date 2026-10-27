@props(['texto'])
<div x-data="textoRecolhivel" x-on:resize.window="medir" {{ $attributes }}>
    <p class="line-clamp-1 whitespace-pre-line text-[0.95rem]" x-ref="texto" x-bind:class="classeTexto">{{ $texto }}</p>
    <button type="button" class="link mt-1 inline-flex min-h-10 items-center text-sm font-medium" x-show="podeAlternar" x-cloak x-on:click="alternar" x-bind:aria-expanded="aberto">
        <span x-show="!aberto">{{ __('ponte.texto.ver_mais') }}</span>
        <span x-show="aberto" x-cloak>{{ __('ponte.texto.ver_menos') }}</span>
    </button>
</div>
