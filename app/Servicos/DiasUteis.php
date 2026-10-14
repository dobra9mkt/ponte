<?php

namespace App\Servicos;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class DiasUteis
{
    /** @var array<string, string> */
    private array $feriados;

    public function __construct()
    {
        $this->feriados = config()->array('feriados');
    }

    public function ehUtil(CarbonInterface $data): bool
    {
        return ! $data->isWeekend() && ! isset($this->feriados[$data->toDateString()]);
    }

    public function somar(CarbonInterface $data, int $dias): CarbonImmutable
    {
        $data = CarbonImmutable::parse($data->toDateString());
        $passo = $dias < 0 ? -1 : 1;

        for ($restantes = abs($dias); $restantes > 0;) {
            $data = $data->addDays($passo);

            if ($this->ehUtil($data)) {
                $restantes--;
            }
        }

        return $data;
    }

    /**
     * Dias úteis depois de $de até $ate (inclusive). Negativo quando $ate vem antes.
     */
    public function entre(CarbonInterface $de, CarbonInterface $ate): int
    {
        $inicio = CarbonImmutable::parse($de->toDateString());
        $fim = CarbonImmutable::parse($ate->toDateString());
        $sinal = $fim->lt($inicio) ? -1 : 1;
        [$inicio, $fim] = $sinal === 1 ? [$inicio, $fim] : [$fim, $inicio];

        $total = 0;

        for ($dia = $inicio->addDay(); $dia->lte($fim); $dia = $dia->addDay()) {
            if ($this->ehUtil($dia)) {
                $total++;
            }
        }

        return $total * $sinal;
    }
}
