<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Update Kategori Risiko daily at 8:00 AM
        $schedule->command('kategori:update')
            ->dailyAt('08:00')
            ->timezone('Asia/Kuala_Lumpur')
            ->withoutOverlapping()
            ->onOneServer();

        // Check and trigger daily alerts at 8:00 AM
        $schedule->command('alerts:check-daily')
            ->dailyAt('08:00')
            ->timezone('Asia/Kuala_Lumpur')
            ->withoutOverlapping()
            ->onOneServer()
            ->emailOutputOnFailure(config('mail.admin_email'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
