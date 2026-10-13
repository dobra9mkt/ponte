<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BoasVindasController extends Controller
{
    public function create(): View
    {
        return view('cliente.boas-vindas');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()?->forceFill(['boas_vindas_vistas_em' => now()])->save();

        return redirect()->route('inicio');
    }
}
