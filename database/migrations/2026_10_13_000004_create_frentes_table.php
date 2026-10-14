<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete();
            $table->foreignId('modelo_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nome');
            $table->unsignedTinyInteger('revisoes_incluidas')->default(3);
            $table->date('inicio_em');
            $table->date('prazo_final');
            $table->string('situacao', 20)->default('ativa')->index();
            $table->dateTime('concluida_em')->nullable();
            $table->date('arquivavel_em')->nullable();
            $table->foreignId('depende_de_frente_id')->nullable()->constrained('frentes')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('frente_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('ordem');
            $table->string('titulo');
            $table->string('tipo', 30);
            $table->string('estado', 30)->default('a_fazer');
            $table->text('descricao_cliente')->nullable();
            $table->boolean('gasta_revisao')->default(false);
            $table->string('formulario', 60)->nullable();
            $table->unsignedTinyInteger('prazo_resposta_dias_uteis')->default(3);
            $table->string('confirmar_antes_de_aprovar')->nullable();
            $table->date('data_prevista')->nullable();
            $table->dateTime('aguardando_desde')->nullable();
            $table->dateTime('lembrete_1_em')->nullable();
            $table->dateTime('lembrete_2_em')->nullable();
            $table->boolean('oculta')->default(false);
            $table->text('aviso_equipe')->nullable();
            $table->timestamps();
            $table->index(['frente_id', 'ordem']);
            $table->index(['estado', 'aguardando_desde']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapas');
        Schema::dropIfExists('frentes');
    }
};
