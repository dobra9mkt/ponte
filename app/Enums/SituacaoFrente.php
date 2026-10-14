<?php

namespace App\Enums;

enum SituacaoFrente: string
{
    case Ativa = 'ativa';
    case Concluida = 'concluida';
    case Arquivada = 'arquivada';

    public function rotulo(): string
    {
        return match ($this) {
            self::Ativa => 'Em andamento',
            self::Concluida => 'Concluído',
            self::Arquivada => 'Arquivado',
        };
    }
}
