<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('empresa');
            $table->string('contato_nome');
            $table->string('email');
            $table->text('telefone')->nullable();
            $table->text('endereco')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('contrato_caminho')->nullable();
            $table->string('cor', 7);
            $table->string('situacao', 20)->default('ativo')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
