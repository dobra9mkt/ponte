@props(['clara' => false])
<span {{ $attributes->class('flex flex-col items-start gap-[7px]') }}>
    <img src="{{ asset($clara ? 'marca/dobra9-logo-branco.svg' : 'marca/dobra9-logo.svg') }}" alt="Dobra 9" width="221" height="24" class="h-6 w-auto">
    <span @class(['whitespace-nowrap font-titulo text-[0.6rem] font-medium uppercase leading-none tracking-[0.28em]', 'text-[#cfdaea]' => $clara, 'text-mudo' => ! $clara])>{{ __('ponte.assinatura') }}</span>
</span>
