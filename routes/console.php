<?php

use App\Models\Convite;
use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute()->withoutOverlapping();

Schedule::command('ponte:alertar-falhas-login')->hourly();

Schedule::call(fn () => Convite::query()->where('expira_em', '<', now()->subDay())->delete())
    ->dailyAt('04:00')
    ->name('apagar-convites-vencidos');

Schedule::command('auth:clear-resets')->dailyAt('04:05');
