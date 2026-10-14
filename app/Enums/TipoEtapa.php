<?php

namespace App\Enums;

enum TipoEtapa: string
{
    case PendenciaCliente = 'pendencia_cliente';
    case Aprovacao = 'aprovacao';
    case Escolha = 'escolha';
    case TrabalhoEquipe = 'trabalho_equipe';
    case EntregaFinal = 'entrega_final';
    case Recorrente = 'recorrente';

    public function rotulo(): string
    {
        return match ($this) {
            self::PendenciaCliente => 'Cliente envia',
            self::Aprovacao => 'Cliente aprova',
            self::Escolha => 'Cliente escolhe',
            self::TrabalhoEquipe => 'Nós fazemos',
            self::EntregaFinal => 'Entrega final',
            self::Recorrente => 'Todo mês',
        };
    }

    public function acaoDoCliente(): string
    {
        return match ($this) {
            self::PendenciaCliente => 'Envie',
            self::Aprovacao => 'Aprove',
            self::Escolha => 'Escolha',
            default => 'Veja',
        };
    }

    public function concluiPelaEquipe(): bool
    {
        return in_array($this, [self::TrabalhoEquipe, self::EntregaFinal], true);
    }

    public function iniciaPelaEquipe(): bool
    {
        return $this !== self::PendenciaCliente;
    }
}
