<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

class Setup
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   *
   * @throws \Illuminate\Auth\AuthenticationException
   */
  public function handle($request, \Closure $next)
  {
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


    return $next($request);
  }
}