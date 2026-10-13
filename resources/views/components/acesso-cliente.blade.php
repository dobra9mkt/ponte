@props(['cliente'])
@php($acesso = $cliente->estadoDoAcesso())
<x-etiqueta :tom="$acesso['tom']" {{ $attributes }}>{{ $acesso['rotulo'] }}</x-etiqueta>
