<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->timestamp('descartado_em')->nullable()->after('deleted_at');
        });

        Schema::table('frentes', function (Blueprint $table) {
            $table->timestamp('descartado_em')->nullable()->after('deleted_at');
        });

        Schema::table('etapas', function (Blueprint $table) {
            $table->timestamp('descartado_em')->nullable()->after('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('etapas', function (Blueprint $table) {
            $table->dropColumn('descartado_em');
        });

        Schema::table('frentes', function (Blueprint $table) {
            $table->dropColumn('descartado_em');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('descartado_em');
        });
    }
};
