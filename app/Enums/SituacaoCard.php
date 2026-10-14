<?php

namespace App\Enums;

enum SituacaoCard: string
{
    case Rascunho = 'rascunho';
    case AguardandoCliente = 'aguardando_cliente';
    case Aprovado = 'aprovado';
    case AjustesSolicitados = 'ajustes_solicitados';

    public function rotulo(): string
    {
        return match ($this) {
            self::Rascunho => 'Rascunho',
            self::AguardandoCliente => 'Aguardando cliente',
            self::Aprovado => 'Aprovado',
            self::AjustesSolicitados => 'Ajustes solicitados',
        };
    }

    public function tom(): string
    {
        return match ($this) {
            self::Rascunho => 'neutro',
            self::AguardandoCliente => 'atencao',
            self::Aprovado => 'sucesso',
            self::AjustesSolicitados => 'info',
        };
    }

    public function aberto(): bool
    {
        return in_array($this, [self::Rascunho, self::AguardandoCliente], true);
    }
}
