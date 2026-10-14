<?php

use App\Acoes\CarregarServicosPadrao;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        app(CarregarServicosPadrao::class)();
    }

    public function down(): void {}
};
