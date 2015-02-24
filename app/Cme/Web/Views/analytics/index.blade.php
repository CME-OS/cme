@extends('layouts.default')
@section('content')
<script type="text/javascript" src="/assets/js/Chart.min.js"></script>
<h1 class="page-header">Analytics
  <small>See how your campaigns are performing</small>
</h1>
<div class="row">
  <div class="col-sm-12">

    <div class="well">
      <form action="" class="form">
        <select name="campaignId" class="form-control" onchange="location.href='/analytics/'+$(this).val();">
          <option value="0">Select a Campaign</option>
          <?php foreach($campaigns as $id => $subject): ?>
            <option value="<?= $id ?>" <?= ($id == $selectedId)? 'selected="selected"' : ''; ?>><?= $subject ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>
    <?php if($selectedId > 0): ?>
    <div class="well kpi" style="background-color: #fff; border:0;">
      <div class="row">
        <div class="col-md-3">
          <div class="panel panel-info">
            <div class="panel-heading">
              Queued
            </div>
            <div class="panel-body">
              <p class="text-center" style="font-size:40px;"><?= $stats['queued']; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="panel panel-success">
            <div class="panel-heading">
              Sent
            </div>
            <div class="panel-body">
              <p class="text-center" style="font-size:40px;"><?= $stats['sent']; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="panel panel-info">
            <div class="panel-heading">
              Opened
            </div>
            <div class="panel-body">
              <p class="text-center" style="font-size:40px;"><?= $stats['opened']; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="panel panel-danger">
            <div class="panel-heading">
              Unsubscribed
            </div>
            <div class="panel-body">
              <p class="text-center" style="font-size:40px;"><?= $stats['unsubscribed']; ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div>
      <h2>Link Activity</h2>
      <table class="table table-bordered">
        <tr>
          <th>Link</th>
          <th>Unique</th>
          <th>Total</th>
        </tr>
        <?php foreach($clicks as $link => $data): ?>
        <tr>
          <td><?= $link  ?></td>
          <td><?= $data['unique']; ?></td>
          <td><?= $data['total']; ?></td>
        </tr>
        <?php endforeach; ?>
      </table>

      <h2>Last 10 Opens</h2>
      <table class="table table-bordered">
        <tr>
          <th>Email</th>
        </tr>
        <?php foreach($opens as $s): ?>
        <tr>
          <td><?= $s->email; ?></td>
        </tr>
        <?php endforeach; ?>
      </table>


      <h2>Last 10 Unsubscribes</h2>
      <table class="table table-bordered">
        <tr>
          <th>Email</th>
        </tr>
        <?php foreach($unsubscribes as $s): ?>
        <tr>
          <td><?= $s->email; ?></td>
        </tr>
        <?php endforeach; ?>
      </table>

    </div>

    <?php else: ?>
      <div class="well">
        <p class="text-center" style="font-size:40px;">Select a campaign to display performance analytics</p>
      </div>
    <?php endif; ?>
  </div>
</div>


@stop
