<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('group_middleware', [
            \App\Http\Middleware\MiddleWareOne::class,
            \App\Http\Middleware\MiddleWareTwo::class,
            \App\Http\Middleware\DownForMaintenanceMW::class,
        ]);

        $middleware->append([
            \App\Http\Middleware\ReportExceptionMW::class,
            \App\Http\Middleware\PromotionMW::class,
        ]);

        $middleware->alias([
            'maintenance' => \App\Http\Middleware\DownForMaintenanceMW::class,
            'sessionUserAccount' => \App\Http\Middleware\SessionUserAccountMW::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();


    

    