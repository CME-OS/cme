<?php
namespace App\Cme\Web\Controllers;

use CmeKernel\Core\CmeKernel;
use Illuminate\Support\Facades\View;

class QueuesController extends BaseController
{
  public function index()
  {
    $data = [
      'queueSize' => CmeKernel::Queues()->getMessageQueueSize()
    ];

    return View::make('queues.index', $data);
  }
}
