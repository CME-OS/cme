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
    <div class="well graph" style="background-color: #fff; border:0;">
      <div class="row">
         <div class="col-md-8 col-md-offset-2">
           <canvas id="canvas" height="450" width="800" class="center-block"></canvas>
         </div>
      </div>

    </div>
    <?php else: ?>
      <div class="well">
        <p class="text-center" style="font-size:40px;">Select a campaign to display performance analytics</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
  var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
  var lineChartData = {
    labels : ["January","February","March","April","May","June","July"],
    datasets : [
      {
        label: "My First dataset",
        fillColor : "rgba(220,220,220,0.2)",
        strokeColor : "rgba(220,220,220,1)",
        pointColor : "rgba(220,220,220,1)",
        pointStrokeColor : "#fff",
        pointHighlightFill : "#fff",
        pointHighlightStroke : "rgba(220,220,220,1)",
        data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
      },
      {
        label: "My Second dataset",
        fillColor : "rgba(151,187,205,0.2)",
        strokeColor : "rgba(151,187,205,1)",
        pointColor : "rgba(151,187,205,1)",
        pointStrokeColor : "#fff",
        pointHighlightFill : "#fff",
        pointHighlightStroke : "rgba(151,187,205,1)",
        data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
      }
    ]
  }
  window.onload = function(){
    var ctx = document.getElementById("canvas").getContext("2d");
    window.myLine = new Chart(ctx).Line(lineChartData, {
      responsive: true
    });
  }
</script>
@stop
