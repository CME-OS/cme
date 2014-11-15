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
      <h3 class="heading">Last 24H for all Campaigns</h3>
      <canvas id="hourlyChart" width="500" height="200"></canvas>

      <h3 class="heading">Last Week for all Campaigns</h3>
      <canvas id="dailyChart" width="500" height="200"></canvas>
    </div>
  <?php

  foreach($hStats as $campaignId => $hourStats):

    $hourlyDataSets = array();
    $hourlyXlabels  = array();
    foreach($hourStats as $hour => $eventHStats):
      $hourlyXlabels[] = date('H', strtotime($hour));
      foreach($eventHStats as $hevent => $hcount)
      {
        if(!isset($hourlyDataSets[$hevent]))
        {
          $hourlyDataSets[$hevent] = array();
        }
        $hourlyDataSets[$hevent][] = $hcount;
      }
    endforeach;
  endforeach;
  foreach($dStats as $campaignId => $dailyStats):

    $dailyDataSets = array();
    $dailyXlabels  = array();
    foreach($dailyStats as $day => $eventStats):
      $dailyXlabels[] = date('d/m/y', strtotime($day));
      foreach($eventStats as $event => $wcount)
      {
        if(!isset($dailyDataSets[$event]))
        {
          $dailyDataSets[$event] = array();
        }
        $dailyDataSets[$event][] = $wcount;
      }
    endforeach;
  endforeach;

  $jsHourlyDataSet = array();
  $jsdailyDataSet = array();

  $colors = array(
    'queued'       => 'rgba(151,187,205,1)',
    'bounced'      => 'rgba(131,137,105,1)',
    'sent'         => 'rgba(171,187,205,1)',
    'opened'       => 'rgba(121,117,205,1)',
    'unsubscribed' => 'rgba(131,167,265,1)',
    'clicked'      => 'rgba(161,187,105,1)',
    'failed'       => 'rgba(261,127,105,1)'
  );
  foreach($hourlyDataSets as $hevent => $hourData)
  {
    $jsHourlyDataSet[] = sprintf(
      '{
          label:"%s",
           fillColor: "rgba(151,187,205,0.2)",
          strokeColor:          "%s",
          pointColor:           "%s",
          pointStrokeColor:     "#fff",
          pointHighlightFill:   "#fff",
          pointHighlightStroke: "%s",
          data: [%s]
          }',
      $event,
      $colors[$hevent],
      $colors[$hevent],
      $colors[$hevent],
      implode(',', $hourData)
    );
  }

  foreach($dailyDataSets as $event => $dayData)
  {
    $jsdailyDataSet[] = sprintf(
      '{
          label:"%s",
           fillColor: "rgba(151,187,205,0.2)",
          strokeColor:          "%s",
          pointColor:           "%s",
          pointStrokeColor:     "#fff",
          pointHighlightFill:   "#fff",
          pointHighlightStroke: "%s",
          data: [%s]
          }',
      $event,
      $colors[$event],
      $colors[$event],
      $colors[$event],
      implode(',', $dayData)
    );
  }


  ?>

    <script src="/assets/js/chartjs.min.js"></script>
    <script type="text/javascript">
      var horlydata = {
        labels:   [<?php echo '"' . implode('","', $hourlyXlabels) . '"'; ?>],
        datasets: [
          <?php echo implode(',', $jsHourlyDataSet); ?>
        ]
      };
      var dailydata = {
        labels:   [<?php echo '"' . implode('","', $dailyXlabels) . '"'; ?>],
        datasets: [
          <?php echo implode(',', $jsdailyDataSet); ?>
        ]
      };
      var options = {
        datasetFill:    false,
        legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets{[i].label}){%><%=datasets[i].label%><%}%></li><%}%></ul>"
      };
      var ctxH = document.getElementById("hourlyChart").getContext("2d");
      var hourlyLineChart = new Chart(ctxH).Line(horlydata, options);

      var ctxD= document.getElementById("dailyChart").getContext("2d");
      var dailyLineChart = new Chart(ctxD).Line(dailydata, options);

      /**var option = {

    ///Boolean - Whether grid lines are shown across the chart
    scaleShowGridLines : true,

    //String - Colour of the grid lines
    scaleGridLineColor : "rgba(0,0,0,.05)",

    //Number - Width of the grid lines
    scaleGridLineWidth : 1,

    //Boolean - Whether the line is curved between points
    bezierCurve : true,

    //Number - Tension of the bezier curve between points
    bezierCurveTension : 0.4,

    //Boolean - Whether to show a dot for each point
    pointDot : true,

    //Number - Radius of each point dot in pixels
    pointDotRadius : 4,

    //Number - Pixel width of point dot stroke
    pointDotStrokeWidth : 1,

    //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
    pointHitDetectionRadius : 20,

    //Boolean - Whether to show a stroke for datasets
    datasetStroke : true,

    //Number - Pixel width of dataset stroke
    datasetStrokeWidth : 2,

    //Boolean - Whether to fill the dataset with a colour
    datasetFill : true,

    //String - A legend template
    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets{[i].label
}){%><%=datasets[i].label%><%}%></li><%}%></ul>"

};
       */
    </script>
  <?php endif; ?>
</div>
@stop
