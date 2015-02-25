@extends('layouts.default')
@section('content')
<script type="text/javascript" src="/assets/js/Chart.min.js"></script>
<script>
  var options = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke : true,

    //String - The colour of each segment stroke
    segmentStrokeColor : "#fff",

    //Number - The width of each segment stroke
    segmentStrokeWidth : 2,

    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout : 50, // This is 0 for Pie charts

    //Number - Amount of animation steps
    animationSteps : 100,

    //String - Animation easing effect
    animationEasing : "easeOutBounce",

    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate : true,

    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale : false

  };
</script>
<div>

  @if(!$stats)

  <div class="row">
    <div class="col-sm-12">
      <h1>Welcome to CME</h1>

      <p>
        CME stands for Campaign Made Easy. CME allows you to manage
        and schedule campaigns across all your brands.
        CME is designed for high volume campaigns and is very
        robust
      </p>
    </div>
  </div>


  @else
  <h1>Dashboard</h1>
  <?php ?>
<div class="row">
  <?php foreach($totalStats as $event): ?>
  <div class="col-md-3">
    <div class="well text-center <?= $event->event_type?>-well">
        <div style="font-size:30px;"><?= $event->total; ?></div>
        <span>Total <?= ucwords($event->event_type) ?></span>

    </div>
  </div>
  <?php endforeach; ?>

</div>
    <?php
      $rows = array_chunk(array_keys($stats), 2);
      foreach($rows as $rowId => $data):
    ?>
  <div class="row">
      <div class="col-md-6">
        <div class="panel panel-default">
          <?php if(isset($data[0])): $campaignId = $data[0]; ?>
          <div class="panel-heading">
            <strong>
              <a href="/analytics/<?= $campaignId ?>" title="View Analytics">
              <?= Str::limit($campaignLookUp[$campaignId], 50) ?>
            </a>
            </strong>
            <div class="pull-right">
              <a href="/campaigns/preview/<?=$campaignId ?>" class="glyphicon glyphicon-eye-open" style="text-decoration: none;" title="Preview Campaign"></a>
            </div>
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-hover table-condensed table-bordered" style="font-size:12px;">
                  <?php foreach($eventTypes as $type): ?>
                    <tr>
                      <td><?= ucwords($type) ?> </td>
                      <td><strong><?= $stats[$campaignId][$type]['unique']; ?> <?= ($type == 'opened') ? '(' . $stats[$campaignId]['opened_rate'] . ')' : '' ?></strong></td>
                    </tr>
                  <?php endforeach; ?>
                </table>
              </div>
              <div class="col-md-6">
                <canvas id="canvas-<?= $campaignId ?>" class="center-block"></canvas>
                <script>
                  var data = [
                    {
                      value: <?= $stats[$campaignId]['queued']['unique'] ?>,
                      color:"#949FB1",
                      highlight: "#A8B3C5",
                      label: "Queued"
                    },
                    {
                      value: <?= $stats[$campaignId]['sent']['unique'] ?>,
                      color: "#46BFBD",
                      highlight: "#5AD3D1",
                      label: "Sent"
                    },
                    {
                      value: <?= $stats[$campaignId]['opened']['unique'] ?>,
                      color: "#FDB45C",
                      highlight: "#FFC870",
                      label: "Opened"
                    },
                    {
                      value: <?= $stats[$campaignId]['unsubscribed']['unique'] ?>,
                      color: "#F7464A",
                      highlight: "#FF5A5E",
                      label: "Unsubscribed"
                    }
                  ];
                  var ctx = document.getElementById("canvas-<?= $campaignId ?>").getContext("2d");
                  var myDoughnutChart = new Chart(ctx).Doughnut(data,options);
                </script>
              </div>
            </div>
          </div>
         <?php else: ?>
            <div class="panel-body" style="height: 259px;">

              <div class="text-center" style="font-size:30px; margin-top:60px; ">
                <span class="glyphicon glyphicon-plus-sign" style="color: #ccc;"></span>
                <div style="font-size:20px;"><a href="/campaigns/new">Create New Campaign</a></div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <div class="col-md-6">
      <div class="panel panel-default">
        <?php if(isset($data[1])): $campaignId = $data[1]; ?>
        <div class="panel-heading">
          <strong>
            <a href="/analytics/<?= $campaignId ?>" title="View Analytics">
              <?= Str::limit($campaignLookUp[$campaignId], 50) ?>
            </a>
          </strong>
          <div class="pull-right">
            <a href="/campaigns/preview/<?=$campaignId ?>" class="glyphicon glyphicon-eye-open" style="text-decoration: none;" title="Preview Campaign"></a>
          </div>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-6">
              <table class="table table-hover table-condensed table-bordered" style="font-size:12px;">
                <?php foreach($eventTypes as $type): ?>
                <tr>
                  <td><?= ucwords($type) ?> </td>
                  <td><strong><?= $stats[$campaignId][$type]['unique']; ?> <?= ($type == 'opened') ? '(' . $stats[$campaignId]['opened_rate'] . ')' : '' ?></strong></td>
                </tr>
                <?php endforeach; ?>
              </table>
            </div>
            <div class="col-md-6">
              <canvas id="canvas-<?= $campaignId ?>" class="center-block"></canvas>
              <script>
                var data = [
                  {
                    value: <?= $stats[$campaignId]['queued']['unique'] ?>,
                    color:"#949FB1",
                    highlight: "#A8B3C5",
                    label: "Queued"
                  },
                  {
                    value: <?= $stats[$campaignId]['sent']['unique'] ?>,
                    color: "#46BFBD",
                    highlight: "#5AD3D1",
                    label: "Sent"
                  },
                  {
                    value: <?= $stats[$campaignId]['opened']['unique'] ?>,
                    color: "#FDB45C",
                    highlight: "#FFC870",
                    label: "Opened"
                  },
                  {
                    value: <?= $stats[$campaignId]['unsubscribed']['unique'] ?>,
                    color: "#F7464A",
                    highlight: "#FF5A5E",
                    label: "Unsubscribed"
                  }
                ];
                var ctx = document.getElementById("canvas-<?= $campaignId ?>").getContext("2d");
                var myDoughnutChart = new Chart(ctx).Doughnut(data,options);
              </script>
            </div>
          </div>
        </div>
        <?php else: ?>
        <div class="panel-body" style="height: 259px;">

          <div class="text-center" style="font-size:30px; margin-top:60px; ">
            <span class="glyphicon glyphicon-plus-sign" style="color: #ccc;"></span>
            <div style="font-size:20px;"><a href="/campaigns/new">Create New Campaign</a></div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>




  </div>
      <?php endforeach; ?>

  @endif
</div>
@stop
