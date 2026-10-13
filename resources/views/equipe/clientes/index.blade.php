<x-layouts.equipe titulo="Clientes" :subtitulo="$clientes->count().' '.($clientes->count() === 1 ? 'cliente' : 'clientes')">
    <x-slot:acoes>
        <x-botao :href="route('equipe.clientes.create')">
            <x-icone nome="mais" class="h-4 w-4" />
            Novo cliente
        </x-botao>
    </x-slot:acoes>

    @if ($clientes->isEmpty())
        <x-cartao>
            <div class="px-6 py-10 text-center">
                <p class="font-titulo font-semibold text-navy">Nenhum cliente cadastrado</p>
                <p class="mt-1 text-sm text-mudo">Cadastrar não envia nada ao cliente.</p>
            </div>
        </x-cartao>
    @else
        <div class="overflow-hidden rounded-cartao bg-white shadow-cartao">
            <div class="hidden grid-cols-[minmax(0,2fr)_minmax(0,1.3fr)_minmax(0,1.2fr)_auto] gap-4 border-b border-linha px-6 py-3 text-xs font-semibold uppercase tracking-wide text-mudo md:grid">
                <span>Empresa</span>
                <span>Contato</span>
                <span>Acesso</span>
                <span class="w-4"></span>
            </div>
            <ul class="divide-y divide-linha">
                @foreach ($clientes as $cliente)
                    <li>
                        <a href="{{ route('equipe.clientes.show', $cliente) }}" class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-x-4 gap-y-1 px-5 py-4 transition hover:bg-off md:grid-cols-[minmax(0,2fr)_minmax(0,1.3fr)_minmax(0,1.2fr)_auto] md:px-6">
                            <span class="flex min-w-0 items-center gap-3">
                                <x-cor-cliente :cor="$cliente->cor" />
                                <span class="truncate font-titulo font-semibold text-navy">{{ $cliente->empresa }}</span>
                                @unless ($cliente->estaAtivo())
                                    <x-etiqueta>{{ $cliente->situacao->rotulo() }}</x-etiqueta>
                                @endunless
                            </span>
                            <span class="col-start-1 truncate pl-6 text-sm text-mudo md:col-start-auto md:pl-0 md:text-[0.95rem]">{{ $cliente->contato_nome }}</span>
                            <span class="col-start-1 pl-6 md:col-start-auto md:pl-0"><x-acesso-cliente :cliente="$cliente" /></span>
                            <x-icone nome="seta" class="col-start-2 row-span-3 row-start-1 h-4 w-4 text-mudo md:col-start-auto md:row-span-1 md:row-start-auto" />
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</x-layouts.equipe>
