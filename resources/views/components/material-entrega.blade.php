@props(['entrega', 'rota' => 'arquivos.ver'])
<ul class="divide-y divide-linha/70 rounded-campo border border-linha bg-white">
    @foreach ($entrega->arquivos as $arquivo)
        <li class="flex flex-wrap items-center gap-3 px-4 py-3">
            <x-icone nome="nota" class="h-5 w-5 text-tec" />
            <span class="min-w-0 flex-1 break-words text-sm font-medium text-navy">{{ $arquivo->nome_original }}</span>
            <a href="{{ route($rota, $arquivo) }}" @if ($arquivo->permitePrevia()) target="_blank" rel="noopener" @endif class="inline-flex min-h-11 items-center gap-1.5 rounded-campo px-3 text-sm font-semibold text-tec hover:bg-azul-claro">
                <x-icone :nome="$arquivo->permitePrevia() ? 'olho' : 'baixar'" class="h-4 w-4" />{{ __($arquivo->permitePrevia() ? 'entregas.abrir' : 'entregas.baixar') }}
            </a>
        </li>
    @endforeach
    @foreach ($entrega->links as $link)
        <li class="flex flex-wrap items-center gap-3 px-4 py-3">
            <x-icone nome="link" class="h-5 w-5 text-tec" />
            <span class="min-w-0 flex-1">
                <span class="block break-words text-sm font-medium text-navy">{{ $link['rotulo'] }}</span>
                <span class="block break-all text-xs text-mudo">{{ $link['url'] }}</span>
            </span>
            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center gap-1.5 rounded-campo px-3 text-sm font-semibold text-tec hover:bg-azul-claro">
                <x-icone nome="seta" class="h-4 w-4" />{{ __('entregas.abrir_link') }}
            </a>
        </li>
    @endforeach
</ul>
