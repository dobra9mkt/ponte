<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Clientes descartados antes de RN-82a liberar o e-mail: solta o e-mail deles agora.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->join('clientes', 'clientes.id', '=', 'users.cliente_id')
            ->whereNotNull('clientes.descartado_em')
            ->where('users.email', 'not like', 'descartado-%')
            ->select('users.id', 'users.email')
            ->get()
            ->each(fn ($usuario) => DB::table('users')->where('id', $usuario->id)
                ->update(['email' => mb_substr('descartado-'.$usuario->id.':'.$usuario->email, 0, 255)]));
    }

    public function down(): void {}
};
