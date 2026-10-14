<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modelos', function (Blueprint $table) {
            $table->string('nome_cliente', 100)->nullable()->after('nome');
        });

        Schema::table('frentes', function (Blueprint $table) {
            $table->string('nome_cliente', 100)->nullable()->after('nome');
        });

        $sites = DB::table('modelos')->where('slug', 'criacao-de-sites')->value('id');
        if ($sites !== null) {
            DB::table('modelos')->where('id', $sites)->update(['nome_cliente' => 'Desenvolvimento do seu site']);
            DB::table('frentes')->where('modelo_id', $sites)->update(['nome_cliente' => 'Desenvolvimento do seu site']);
        }
    }

    public function down(): void
    {
        Schema::table('frentes', function (Blueprint $table) {
            $table->dropColumn('nome_cliente');
        });

        Schema::table('modelos', function (Blueprint $table) {
            $table->dropColumn('nome_cliente');
        });
    }
};
