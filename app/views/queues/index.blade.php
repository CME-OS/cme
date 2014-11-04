@extends('layouts.default')
@section('content')
<div>
  <h1 class="page-header">Queues</h1>

  <div class="row">
    <div class="col-md-6">
      <table class="table table-hover table-bordered">
        <tr>
          <td>No of Messages in Queue</td>
          <td><?= $queueSize ?></td>
        </tr>
        <tr>
          <td>No of Messages Sent</td>
          <td><?= $queueSize ?></td>
        </tr>
        <tr>
          <td>No of Messages Pending</td>
          <td><?= $queueSize ?></td>
        </tr>
      </table>
    </div>
  </div>

</div>
@stop
