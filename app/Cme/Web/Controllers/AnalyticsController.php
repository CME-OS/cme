<?php
namespace Cme\Web\Controllers;

use Illuminate\Support\Facades\View;

class AnalyticsController extends BaseController
{
  public function index()
  {
    $data = [];
    return View::make('analytics.index', $data);
  }
}
