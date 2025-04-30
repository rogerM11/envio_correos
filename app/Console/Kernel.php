<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define las tareas programadas.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:enviar-reporte-vendedores')
                 ->everyMinute()
                 ->appendOutputTo(storage_path('logs/scheduler.log'))
                 ->withoutOverlapping();
    }

    /**
     * Registra los comandos Artisan para la aplicación.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
