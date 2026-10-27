@props(['texto', 'limite' => 180])
@php
    $longo = mb_strlen($texto) > $limite || substr_count($texto, "\n") >= 2;
@endphp
@if ($longo)
    <div x-data="alternar" {{ $attributes }}>
        <p class="line-clamp-2 whitespace-pre-line text-[0.95rem]" x-bind:class="classeTexto">{{ $texto }}</p>
        <button type="button" class="link mt-1 inline-flex min-h-10 items-center text-sm font-medium" x-on:click="alternar" x-bind:aria-expanded="aberto">
            <span x-show="!aberto">{{ __('ponte.texto.ver_mais') }}</span>
            <span x-show="aberto" x-cloak>{{ __('ponte.texto.ver_menos') }}</span>
        </button>
    </div>
@else
    <p {{ $attributes->class('whitespace-pre-line text-[0.95rem]') }}>{{ $texto }}</p>
@endif
