<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

$config   = config();
$profile  = $config->get('database.default') ;
$dbConfig = $config->get('database.connections');

$initData = new \CmeData\InitData() ;
$initData->cmeHost = $config->get('app.domain');
$initData->key = $config->get('app.key');
$initData ->dbName = $dbConfig[$profile]['database'] ;
$initData ->dbUsername = $dbConfig[$profile]['username'] ;
$initData ->dbPassword = $dbConfig[$profile]['password'] ;
$initData ->dbHost = $dbConfig[$profile]['host'] ;
\CmeKernel\Core\CmeKernel::init($initData);


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');
