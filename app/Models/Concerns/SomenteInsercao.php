<?php

namespace App\Models\Concerns;

use LogicException;

trait SomenteInsercao
{
    protected static function bootSomenteInsercao(): void
    {
        static::updating(fn () => throw new LogicException('Registro publicado não pode ser alterado.'));
        static::deleting(fn () => throw new LogicException('Registro publicado não pode ser apagado.'));
    }
}
