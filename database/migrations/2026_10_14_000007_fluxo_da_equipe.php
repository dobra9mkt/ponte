<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('email_avisos')->nullable();
        });
        DB::table('clientes')->update(['email_avisos' => DB::raw('email')]);

        Schema::table('modelos', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('modelo_etapas', function (Blueprint $table) {
            $table->string('tipo', 30)->default('trabalho_equipe')->change();
            $table->unsignedSmallInteger('duracao_dias_uteis')->nullable()->default(null)->change();
            $table->string('responsavel', 100)->nullable();
            $table->text('o_que_fazer')->nullable();
            $table->softDeletes();
        });

        Schema::table('etapas', function (Blueprint $table) {
            $table->string('tipo', 30)->default('trabalho_equipe')->change();
            $table->renameColumn('data_prevista', 'termino_em');
            $table->date('entrada_em')->nullable();
            $table->string('responsavel', 100)->nullable();
            $table->text('o_que_fazer')->nullable();
            $table->softDeletes();
        });

        Schema::table('entregas', function (Blueprint $table) {
            $table->dropUnique(['etapa_id', 'versao']);
            $table->index(['etapa_id', 'versao']);
            $table->string('situacao', 30)->default('rascunho');
            $table->date('responder_ate')->nullable()->change();
            $table->char('hash_conteudo', 64)->nullable()->change();
            $table->foreignId('publicada_por')->nullable()->change();
            $table->timestamp('publicada_em')->nullable()->change();
            $table->foreignId('criada_por')->nullable()->constrained('users');
            $table->timestamp('lembrete_enviado_em')->nullable();
            $table->timestamp('alerta_vencido_em')->nullable();
            $table->softDeletes();
            $table->index(['situacao', 'responder_ate']);
        });

        DB::table('entregas')->update(['situacao' => 'aguardando_cliente']);
        DB::table('entregas')->whereIn('id', DB::table('aprovacoes')->select('entrega_id'))->update(['situacao' => 'aprovado']);
        DB::table('entregas')->whereIn('id', DB::table('pedidos_ajuste')->select('entrega_id'))->update(['situacao' => 'ajustes_solicitados']);

        Schema::table('arquivos', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('arquivos', fn (Blueprint $table) => $table->dropSoftDeletes());

        Schema::table('entregas', function (Blueprint $table) {
            $table->dropIndex(['situacao', 'responder_ate']);
            $table->dropSoftDeletes();
            $table->dropConstrainedForeignId('criada_por');
            $table->dropColumn(['situacao', 'lembrete_enviado_em', 'alerta_vencido_em']);
        });

        Schema::table('etapas', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['entrada_em', 'responsavel', 'o_que_fazer']);
            $table->renameColumn('termino_em', 'data_prevista');
        });

        Schema::table('modelo_etapas', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['responsavel', 'o_que_fazer']);
        });
        Schema::table('modelos', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('clientes', fn (Blueprint $table) => $table->dropColumn('email_avisos'));
    }
};
