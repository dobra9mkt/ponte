@props(['titulo' => null, 'corpo' => ''])
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta name="theme-color" content="#071e3d">
    <title>{{ $titulo ? $titulo.' · ' : '' }}Ponte · Dobra 9</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ $corpo }}">
    {{ $slot }}
</body>
</html>
