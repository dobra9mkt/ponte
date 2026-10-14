<?php

use App\Servicos\DiasUteis;
use Carbon\CarbonImmutable;

function dia(string $data): CarbonImmutable
{
    return CarbonImmutable::parse($data);
}

it('RN-13: somar pula fim de semana', function () {
    expect(app(DiasUteis::class)->somar(dia('2026-11-03'), 3)->toDateString())->toBe('2026-11-06')
        ->and(app(DiasUteis::class)->somar(dia('2026-11-06'), 1)->toDateString())->toBe('2026-11-09');
});

it('RN-13: somar pula feriados nacionais, de Belo Horizonte e Carnaval', function (string $de, int $dias, string $esperado) {
    expect(app(DiasUteis::class)->somar(dia($de), $dias)->toDateString())->toBe($esperado);
})->with([
    'Consciência Negra' => ['2026-11-19', 1, '2026-11-23'],
    'Imaculada Conceição (BH)' => ['2026-12-07', 1, '2026-12-09'],
    'Carnaval' => ['2026-02-13', 1, '2026-02-18'],
    'Natal e Ano-Novo' => ['2026-12-24', 2, '2026-12-29'],
]);

it('RN-13: somar aceita dias negativos', function () {
    expect(app(DiasUteis::class)->somar(dia('2026-11-23'), -1)->toDateString())->toBe('2026-11-19');
});

it('RN-13: conta os dias úteis entre duas datas, com sinal', function () {
    $dias = app(DiasUteis::class);

    expect($dias->entre(dia('2026-11-19'), dia('2026-11-23')))->toBe(1)
        ->and($dias->entre(dia('2026-11-23'), dia('2026-11-19')))->toBe(-1)
        ->and($dias->entre(dia('2026-11-02'), dia('2026-11-09')))->toBe(5)
        ->and($dias->entre(dia('2026-11-09'), dia('2026-11-09')))->toBe(0);
});
