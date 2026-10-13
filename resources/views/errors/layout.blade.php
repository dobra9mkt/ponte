<x-layouts.simples :titulo="$titulo">
    <h1 class="text-xl font-bold">{{ $titulo }}</h1>
    <p class="mt-2 text-mudo">{{ $texto }}</p>
    <div class="mt-6">
        <x-botao href="/" variante="secundario" largo>Ir para o início</x-botao>
    </div>
</x-layouts.simples>
