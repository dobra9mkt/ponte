<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArquivoController extends Controller
{
    public function __invoke(Arquivo $arquivo): StreamedResponse
    {
        $previa = $arquivo->permitePrevia();

        return Storage::disk('privado')->response($arquivo->caminho, $arquivo->nome_original, [
            'Content-Type' => $arquivo->mime,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => $arquivo->mime === 'application/pdf'
                ? "default-src 'none'; object-src 'self'; style-src 'unsafe-inline'"
                : "default-src 'none'; img-src 'self'; style-src 'unsafe-inline'; sandbox",
            'Cache-Control' => 'no-store, private',
        ], $previa ? 'inline' : 'attachment');
    }
}
