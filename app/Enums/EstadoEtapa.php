<?php

namespace App\Enums;

enum EstadoEtapa: string
{
    case AFazer = 'a_fazer';
    case EmProducao = 'em_producao';
    case AguardandoCliente = 'aguardando_cliente';
    case Recebida = 'recebida';
    case AjustesEmAndamento = 'ajustes_em_andamento';
    case Aprovada = 'aprovada';
    case Concluida = 'concluida';

    public function rotuloCliente(): string
    {
        return match ($this) {
            self::AFazer => 'Próxima',
            self::EmProducao => 'Em andamento',
            self::AguardandoCliente => 'Aguardando sua aprovação',
            self::Recebida => 'Recebido',
            self::AjustesEmAndamento => 'Ajustes em andamento',
            self::Aprovada => 'Aprovada',
            self::Concluida => 'Concluída',
        };
    }

    public function rotuloEquipe(): string
    {
        return match ($this) {
            self::AFazer => 'A fazer',
            self::EmProducao => 'Em andamento',
            self::AguardandoCliente => 'Aguardando cliente',
            self::Recebida => 'Material recebido',
            self::AjustesEmAndamento => 'Ajustes',
            self::Aprovada => 'Aprovada',
            self::Concluida => 'Concluída',
        };
    }

    public function tom(): string
    {
        return match ($this) {
            self::AFazer => 'neutro',
            self::AguardandoCliente => 'atencao',
            self::Aprovada, self::Concluida => 'sucesso',
            default => 'info',
        };
    }

    public function finalizada(): bool
    {
        return in_array($this, [self::Aprovada, self::Concluida], true);
    }

    public function jaFoiAoCliente(): bool
    {
        return ! in_array($this, [self::AFazer, self::EmProducao], true);
    }
}
