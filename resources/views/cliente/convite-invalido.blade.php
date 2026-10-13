@php($whatsapp = config('ponte.whatsapp_dobra9'))
<x-layouts.simples titulo="Link inválido">
    <h1 class="text-xl font-bold">{{ __('ponte.convite.invalido') }}</h1>

    <div class="mt-6 space-y-3">
        @if ($whatsapp)
            <x-botao :href="'https://wa.me/'.preg_replace('/\D/', '', $whatsapp)" largo rel="noopener noreferrer" target="_blank">{{ __('ponte.convite.whatsapp') }}</x-botao>
        @endif
        <x-botao :href="route('entrar')" variante="secundario" largo>Ir para entrar</x-botao>
    </div>
</x-layouts.simples>
