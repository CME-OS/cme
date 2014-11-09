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

  <figure style="width: 400px; height: 300px;" id="homechart"></figure>

  <script src="/assets/js/d3.min.js"></script>
  <script src="/assets/js/xcharts.min.js"></script>
  <script type="text/javascript">var data = {
      "xScale": "time",
      "yScale": "linear",
      "type": "line",
      "main": [
        {
          "className": ".pizza",
          "data": [
            {
              "x": "2012-11-05",
              "y": 1
            },
            {
              "x": "2012-11-06",
              "y": 6
            },
            {
              "x": "2012-11-07",
              "y": 13
            },
            {
              "x": "2012-11-08",
              "y": -3
            },
            {
              "x": "2012-11-09",
              "y": -4
            },
            {
              "x": "2012-11-10",
              "y": 9
            },
            {
              "x": "2012-11-11",
              "y": 6
            }
          ]
        }
      ]
    };
    var opts = {
      "dataFormatX": function (x) { return d3.time.format('%Y-%m-%d').parse(x); },
      "tickFormatX": function (x) { return d3.time.format('%A')(x); }
    };
    var myChart = new xChart('line', data, '#homechart', opts);


  </script>
</div>
@stop
