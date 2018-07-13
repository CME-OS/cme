<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
      \App\Cme\Cli\SendEmails::class,
      \App\Cme\Cli\QueueMessages::class,
      \App\Cme\Cli\ListImporter::class,
      \App\Cme\Cli\ListRefresher::class,
      \App\Cme\Cli\InstallDb::class,
      \App\Cme\Cli\UninstallDb::class,
      \App\Cme\Cli\UpgradeDb::class,
      \App\Cme\Cli\DbSnapshot::class,
      \App\Cme\Cli\GenerateMigrationFiles::class,
      \App\Cme\Cli\CreateUser::class,
      \App\Cme\Cli\SesTool::class,
      \App\Cme\Cli\InstallCommander::class,
      \App\Cme\Cli\Setup::class,
      //\App\Cme\Cli\GenerateInstallFiles::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
