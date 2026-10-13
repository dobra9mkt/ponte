@props(['rotulo', 'icone'])
<div class="flex gap-3">
    <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-campo bg-off text-tec">
        <x-icone :nome="$icone" class="h-[1.1rem] w-[1.1rem]" />
    </span>
    <div class="min-w-0">
        <dt class="text-xs font-medium uppercase tracking-wide text-mudo">{{ $rotulo }}</dt>
        <dd class="mt-0.5 font-medium text-navy">{{ $slot }}</dd>
    </div>
</div>
