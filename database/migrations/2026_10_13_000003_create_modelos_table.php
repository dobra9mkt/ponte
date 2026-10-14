<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modelos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->unsignedTinyInteger('revisoes_incluidas')->default(3);
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('modelo_etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modelo_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('ordem');
            $table->string('titulo');
            $table->string('tipo', 30);
            $table->text('descricao_cliente')->nullable();
            $table->unsignedSmallInteger('duracao_dias_uteis')->default(0);
            $table->unsignedTinyInteger('prazo_resposta_dias_uteis')->default(3);
            $table->boolean('gasta_revisao')->default(false);
            $table->string('formulario', 60)->nullable();
            $table->string('confirmar_antes_de_aprovar')->nullable();
            $table->timestamps();
            $table->index(['modelo_id', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modelo_etapas');
        Schema::dropIfExists('modelos');
    }
};
