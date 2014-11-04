<?php
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;

class QueuesController extends BaseController
{
  public function index()
  {
    $data = [
      'queueSize' => DB::select("SELECT count(*) as count FROM message_queue")[0]->count
    ];

    return View::make('queues.index', $data);
  }
}
