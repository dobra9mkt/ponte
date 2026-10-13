<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Sessoes
{
    public function __construct(private readonly Request $request) {}

    public function encerrarOutras(User $usuario): void
    {
        $usuario->forceFill(['remember_token' => Str::random(60)])->save();

        DB::table('sessions')
            ->where('user_id', $usuario->id)
            ->where('id', '!=', $this->request->session()->getId())
            ->delete();
    }
}
