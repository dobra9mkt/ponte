<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etapas', fn (Blueprint $table) => $table->boolean('interna')->default(false));
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etapa_id')->constrained();
            $table->unsignedInteger('versao');
            $table->string('titulo');
            $table->text('nota')->nullable();
            $table->json('links');
            $table->string('confirmacao')->nullable();
            $table->boolean('gasta_revisao');
            $table->date('responder_ate');
            $table->char('hash_conteudo', 64);
            $table->foreignId('publicada_por')->constrained('users');
            $table->timestamp('publicada_em');
            $table->timestamps();
            $table->unique(['etapa_id', 'versao']);
        });
        Schema::create('pedidos_ajuste', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id')->constrained();
            $table->foreignId('frente_id')->constrained();
            $table->foreignId('enviado_por')->constrained('users');
            $table->text('texto');
            $table->string('tipo', 20);
            $table->unsignedInteger('numero_revisao')->nullable();
            $table->boolean('conta_como_revisao')->default(false);
            $table->string('situacao_extra', 30)->nullable();
            $table->foreignId('correcao_nossa_por')->nullable()->constrained('users');
            $table->timestamp('correcao_nossa_em')->nullable();
            $table->text('correcao_nossa_motivo')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('navegador')->nullable();
            $table->timestamps();
        });
        Schema::create('arquivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id')->nullable()->constrained();
            $table->foreignId('pedido_ajuste_id')->nullable()->constrained('pedidos_ajuste');
            $table->string('nome_original');
            $table->string('caminho')->unique();
            $table->string('mime');
            $table->unsignedBigInteger('tamanho');
            $table->char('sha256', 64);
            $table->foreignId('enviado_por')->constrained('users');
            $table->timestamps();
        });
        Schema::create('aprovacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id')->unique()->constrained();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('aprovado_em');
            $table->char('hash_conteudo', 64);
            $table->boolean('confirmacao_cumprida');
            $table->string('ip', 45)->nullable();
            $table->string('navegador')->nullable();
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('rascunhos_ajuste', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('texto')->nullable();
            $table->unsignedInteger('revisao')->default(0);
            $table->timestamps();
            $table->unique(['entrega_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rascunhos_ajuste');
        Schema::dropIfExists('aprovacoes');
        Schema::dropIfExists('arquivos');
        Schema::dropIfExists('pedidos_ajuste');
        Schema::dropIfExists('entregas');
        Schema::table('etapas', fn (Blueprint $table) => $table->dropColumn('interna'));
    }
};
