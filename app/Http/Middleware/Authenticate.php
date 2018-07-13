<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

class Authenticate extends \Illuminate\Auth\Middleware\Authenticate
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param  string[]  ...$guards
   * @return mixed
   *
   * @throws \Illuminate\Auth\AuthenticationException
   */
  public function handle($request, \Closure $next, ...$guards)
  {
    $this->authenticate($guards);

    if (Auth::guest())
    {
      if ($request->ajax())
      {
        return response()->make('Unauthorized', 401);
      }
      else
      {
        return redirect()->route('login');
      }
    }


    return $next($request);
  }
}