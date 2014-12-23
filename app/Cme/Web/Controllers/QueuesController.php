<?php
namespace Cme\Web\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class QueuesController extends BaseController
{
  public function index()
  {
    $data = [
      'queueSize' => DB::select(
        "SELECT count(*) as count FROM message_queue"
      )[0]->count
    ];

    return View::make('queues.index', $data);
  }
}
