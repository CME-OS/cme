<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new Cme\Cli\QueueMessages);
Artisan::add(new Cme\Cli\ListImporter);
Artisan::add(new Cme\Cli\ListRefresher);
Artisan::add(new Cme\Cli\InstallDb);
Artisan::add(new Cme\Cli\UninstallDb);
Artisan::add(new Cme\Cli\UpgradeDb);
Artisan::add(new Cme\Cli\DbSnapshot);
Artisan::add(new Cme\Cli\GenerateMigrationFiles);
Artisan::add(new Cme\Cli\CreateUser);
Artisan::add(new Cme\Cli\SesTool);
Artisan::add(new Cme\Cli\InstallCommander);
Artisan::add(new Cme\Cli\Setup);
Artisan::add(new Cme\Cli\GenerateInstallFiles(App::make('config')));
