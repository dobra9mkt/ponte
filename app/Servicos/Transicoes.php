<?php

namespace App\Servicos;

use App\Enums\EstadoEtapa as E;
use App\Models\Etapa;
use LogicException;

/**
 * Transições permitidas de uma fase (docs/02-fluxos.md).
 */
class Transicoes
{
    /** @return array<string, list<E>> */
    private static function tabela(): array
    {
        return [
            E::AFazer->value => [E::EmProducao, E::AguardandoCliente],
            E::EmProducao->value => [E::AguardandoCliente, E::Concluida],
            E::AguardandoCliente->value => [E::EmProducao, E::AjustesEmAndamento],
            E::AjustesEmAndamento->value => [E::AguardandoCliente],
        ];
    }

    public static function permitida(E $de, E $para): bool
    {
        return in_array($para, self::tabela()[$de->value] ?? [], true);
    }

    public static function aplicar(Etapa $etapa, E $para): void
    {
        if (! self::permitida($etapa->estado, $para)) {
            throw new LogicException("Transição não permitida: de {$etapa->estado->value} para {$para->value}.");
        }

        $etapa->estado = $para;
    }
}
