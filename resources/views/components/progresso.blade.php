@props(['feitas', 'total'])
@php($porcento = $total > 0 ? (int) round($feitas / $total * 100) : 0)
<div {{ $attributes->class('h-2 overflow-hidden rounded-full bg-off') }} role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $porcento }}" aria-label="{{ __('fluxo.etapas') }}" aria-valuetext="{{ __('fluxo.progresso', ['feitas' => $feitas, 'total' => $total]) }}">
    <svg class="block h-full w-full" viewBox="0 0 100 2" preserveAspectRatio="none" aria-hidden="true"><rect width="{{ $porcento }}" height="2" class="fill-teal"/></svg>
</div>
