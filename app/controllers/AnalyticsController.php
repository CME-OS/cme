<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

class AnalyticsController extends BaseController
{
  public function index()
  {
    $data = [];
    return View::make('analytics.index', $data);
  }
}
