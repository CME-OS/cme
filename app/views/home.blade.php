@extends('layouts.default')
@section('content')
<div>
  <h1>Welcome to CME</h1>

  <?php if(!$dStats): ?>
    <p class="well">
      CME stands for Campaign Made Easy. CME allows you to manage
      and schedule campaigns across all your brands.
      CME is designed for high volume campaigns and is very
      robust
    </p>
  <?php else: ?>
    <div class="row">
      <div class="col-md-6">
        <h2>Last 24 Hours</h2>
        <?php foreach($hStats as $campaignId => $dailyStats): ?>
          <div class="well">
            <h3><?= $campaignLookUp[$campaignId] ?></h3>
            <table class="table">
              <tr>
                <th>Date</th>
                <?php foreach($eventTypes as $type): ?>
                  <th><?= $type ?></th>
                <?php endforeach ?>
              </tr>
              <?php foreach($dailyStats as $day => $eventStats): ?>
                <tr>
                  <td><?= $day ?></td>
                  <?php foreach($eventStats as $count): ?>
                    <td><?= $count ?></td>
                  <?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </table>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="col-md-6">
        <h2>Last 7 Days</h2>
        <?php foreach($dStats as $campaignId => $dailyStats): ?>
          <div class="well">
            <h3><?= $campaignLookUp[$campaignId] ?></h3>
            <table class="table">
              <tr>
                <th>Date</th>
                <?php foreach($eventTypes as $type): ?>
                  <th><?= $type ?></th>
                <?php endforeach ?>
              </tr>
              <?php foreach($dailyStats as $day => $eventStats): ?>
                <tr>
                  <td><?= $day ?></td>
                  <?php foreach($eventStats as $count): ?>
                    <td><?= $count ?></td>
                  <?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </table>
          </div>
        <?php endforeach; ?>

      </div>
    </div>
  <?php endif; ?>

</div>
@stop
