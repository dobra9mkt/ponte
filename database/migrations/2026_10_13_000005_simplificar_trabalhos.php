<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('frentes', function (Blueprint $table) {
            $table->date('inicio_em')->nullable()->change();
            $table->date('prazo_final')->nullable()->change();
            $table->dateTime('publicada_em')->nullable();
        });

        // Preserva o acompanhamento dos trabalhos que já existiam.
        DB::table('frentes')->update(['publicada_em' => now()]);
    }

    public function down(): void
    {
        Schema::table('frentes', function (Blueprint $table) {
            $table->dropColumn('publicada_em');
        });
        // Mantém datas opcionais para não inventar prazos nem apagar trabalhos no rollback.
    }
};
