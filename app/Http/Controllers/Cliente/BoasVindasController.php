<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BoasVindasController extends Controller
{
    public function create(Request $request): View
    {
        $whatsapp = preg_replace('/\D/', '', (string) config('ponte.whatsapp_dobra9'));

        return view('cliente.boas-vindas', [
            'nome' => Str::before(trim((string) $request->user()?->cliente?->contato_nome), ' '),
            'whatsapp' => $whatsapp !== '' ? 'https://wa.me/'.$whatsapp : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()?->forceFill(['boas_vindas_vistas_em' => now()])->save();

        return redirect()->route('inicio');
    }
}
