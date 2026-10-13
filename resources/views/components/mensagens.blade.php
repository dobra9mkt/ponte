@if (session('sucesso'))
    <x-aviso tipo="sucesso" class="mb-6">{{ session('sucesso') }}</x-aviso>
@endif
