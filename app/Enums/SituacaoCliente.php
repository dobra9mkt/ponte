<?php

namespace App\Enums;

enum SituacaoCliente: string
{
    case Ativo = 'ativo';
    case Desativado = 'desativado';
    case Arquivado = 'arquivado';

    public function rotulo(): string
    {
        return match ($this) {
            self::Ativo => 'Ativo',
            self::Desativado => 'Desativado',
            self::Arquivado => 'Arquivado',
        };
    }
}
