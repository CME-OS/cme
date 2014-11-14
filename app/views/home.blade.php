@extends('layouts.default')
@section('content')
<div>
  <h1>Welcome to CME</h1>

  <?php if (!$stats): ?>
    <p class="well">CME stands for Campaign Made Easy. CME allows you to manage
                    and schedule campaigns across all your brands.
                    CME is designed for high volume campaigns and is very
                    robust</p>
  <?php else: ?>
  <div class="row">
    <div class="col-md-6">
      <h2>Last 7 Days</h2>
      <?php foreach ($stats[7] as $campaignId => $eventStats): ?>
      <div class="well">
        <h3><?= $campaignLookUp[$campaignId] ?></h3>
        <?php foreach($eventStats as $event => $count): ?>
          <p class="badge"><?= $event ?>: <?= $count ?></p>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="col-md-6">
      <h2>Last 30 Days</h2>
      <?php foreach ($stats[30] as $campaignId => $eventStats): ?>
        <div class="well">
          <h3><?= $campaignLookUp[$campaignId] ?></h3>
          <?php foreach($eventStats as $event => $count): ?>
            <p class="badge"><?= $event ?>: <?= $count ?></p>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</div>
@stop
