<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Main aggregation job - runs every 10 minutes
Schedule::command('dashboard:aggregate-stats')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Dashboard aggregation job FAILED');
    })
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Dashboard aggregation job completed successfully');
    });

// Health check - runs every 30 minutes to verify stats are fresh
Schedule::command('dashboard:health-check')
    ->everyThirtyMinutes()
    ->withoutOverlapping();
