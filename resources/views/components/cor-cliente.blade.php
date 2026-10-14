@props(['cor'])
<svg {{ $attributes->class('h-3 w-3 shrink-0') }} viewBox="0 0 12 12" aria-hidden="true"><circle cx="6" cy="6" r="6" fill="{{ preg_match('/^#[0-9a-fA-F]{6}$/', (string) $cor) ? $cor : '#0f4c81' }}"/></svg>
