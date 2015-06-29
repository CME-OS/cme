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

  <div class="container">
    <h1 class="text-center" style="font-size:40px; margin-top:40px;">Start sending amazing campaigns in 3 easy steps!</h1>
    <div class="row wizard" style="margin-top:80px;">
      <div class="col-sm-4">
        <div class="text-center" style="background:url('/assets/img/icon_email_list.png') no-repeat center top; padding-top:140px; <?= ($state->enableList)? '' : 'opacity: 0.5' ?>">
           <p style="font-size: 30px;">1. Create a List</p>
           <p>Import your list from a CSV file or an API</p>
           <a <?= ($state->enableList)? 'href="/lists/new"' : '' ?> class="btn <?= ($state->enableList)? 'btn-cme' : ((!$state->listCompleted)? 'btn-default' : 'btn-success').' disabled' ?>"><?= (!$state->listCompleted)? 'Create a List' : 'Completed!' ?></a>
        </div>

      </div>

      <div class="col-sm-4">
        <div class="text-center" style="background:url('/assets/img/icon_brand.png') no-repeat center top; padding-top:140px; <?= ($state->enableBrand)? '' : 'opacity: 0.5' ?>">
          <p style="font-size: 30px;">2. Create a Brand</p>
          <p>To help keep things organized.</p>
          <a <?= ($state->enableBrand)? 'href="/brands/new"' : '' ?> class="btn <?= ($state->enableBrand)? 'btn-cme' : ((!$state->brandCompleted)? 'btn-default' : 'btn-success').' disabled'  ?>"><?= (!$state->brandCompleted)? 'Create a Brand' : 'Completed!' ?></a>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="text-center" style="background:url('/assets/img/icon_campaign.png') no-repeat center top; padding-top:140px; <?= ($state->enableCampaign)? '' : 'opacity: 0.5' ?>">
          <p style="font-size: 30px;">3. Create a campaign</p>
          <p>Send emails and see results</p>
          <a <?= ($state->enableCampaign)? 'href="/campaigns/new"' : '' ?> class="btn <?= ($state->enableCampaign)? 'btn-cme' : 'btn-default disabled' ?>">Create a Campaign</a>
        </div>
      </div>

    </div>
  </div>

  @else
  <h1>Dashboard</h1>
  <?php ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="pull-left"><strong>Overview</strong></div>
        <div class="pull-right">This Month</div>
        <div class="clearfix"></div>
      </div>
      <div class="panel-body">
        <div class="row" style="color:#fff;">
          <?php foreach($totalStats as $event): ?>
          <div class="col-md-3">
            <div class="well text-center <?= $event->event_type?>-well">
              <div style="font-size:30px;"><?= $event->total; ?></div>
              <span>Total <?= ucwords($event->event_type) ?></span>

            </div>
          </div>
          <?php endforeach; ?>

        </div>
      </div>
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
              <?= Str::limit($campaignLookUp[$campaignId]->name, 50) ?>
            </a>
            </strong>
            <div class="pull-right">
              <span style="margin-right:10px;"><?= date('M d Y', $campaignLookUp[$campaignId]->sendTime) ?></span>
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
              <?= Str::limit($campaignLookUp[$campaignId]->name, 50) ?>
            </a>
          </strong>
          <div class="pull-right">
            <span style="margin-right:10px;"><?= date('M d Y', $campaignLookUp[$campaignId]->sendTime) ?></span>
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
