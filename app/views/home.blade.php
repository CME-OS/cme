@extends('layouts.default')
@section('content')
<div>

  <?php if(!$dStats): ?>
    <h1>Welcome to CME</h1>
    <p class="well">
      CME stands for Campaign Made Easy. CME allows you to manage
      and schedule campaigns across all your brands.
      CME is designed for high volume campaigns and is very
      robust
    </p>
  <?php else: ?>

    <script src="/assets/js/jqplot/jquery.jqplot.js"></script>
    <script type="text/javascript" src="/assets/js/jqplot/plugins/jqplot.highlighter.min.js"></script>
    <script type="text/javascript" src="/assets/js/jqplot/plugins/jqplot.cursor.min.js"></script>
    <script type="text/javascript" src="/assets/js/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>

  <?php

  /**
  @todo remove unnecessary loopy mess and move to controller
   **/

  /**building array with date,count for each event*/
  $hourlyDataSets = array();
  foreach($hStats as $campaignId => $hourStats):

    $hourlyXlabels  = array();
    foreach($hourStats as $hour => $eventHStats):
      $hourlyXlabels[] = date('H', strtotime($hour));
      foreach($eventHStats as $hevent => $hcount)
      {
        if(!isset($hourlyDataSets[$campaignId]))
        {
          $hourlyDataSets[$campaignId] = array();
        }
        $hourlyDataSets[$campaignId][$hevent][] = array($hour, $hcount);
      }
    endforeach;
  endforeach;
  $dailyDataSets = array();
  foreach($dStats as $campaignId => $dailyStats):

    foreach($dailyStats as $day => $eventStats):
      foreach($eventStats as $event => $wcount)
      {
        if(!isset($dailyDataSets[$campaignId]))
        {
          $dailyDataSets[$campaignId][$event] = array();
        }
        $dailyDataSets[$campaignId][$event][] = array($day, $wcount);
      }
    endforeach;
  endforeach;

  /*events line colors*/
  $colors = array(
    'sent'=>'#4d4766',
    'queued'=>'#05649a',
    'opened'=>'#00abbd',
    'clicked'=>'#9bca3c',
    'failed' => '#cc0000',
    'bounced'=>'#ff5a00',
    'unsubscribed'=>'#e91365'
  );

  /**foreach capaign we build the javascript data and output the 2 chart*/
  foreach($campaignLookUp as $cid=>$name)
  {
  $out = '';
  $hourlySeriesOptions = array();
  foreach($hourlyDataSets[$cid] as $ev=>$data)
  {
    $hourlySeriesOptions []= sprintf("
    {
             label: '%s',      // label to use in the legend for this line.
             color: '%s'       // CSS color spec to use for the line.  Determined automatically.
    }", $ev,$colors[$ev]);
    $out.='[';
    foreach($data as $d)
    {
      $out .= json_encode($d).',';
    }
    $out = trim($out, ',').'],'."\n";
  }
  $dataHourly =  trim($out, ',');

  $out = '';
  $dailySeriesOptions = array();
  foreach($dailyDataSets[$cid] as $ev=>$data)
  {
    $dailySeriesOptions[] = sprintf("
    {
             label: '%s',      // label to use in the legend for this line.
             color: '%s'       // CSS color spec to use for the line.  Determined automatically.
    }",
      $ev,
      $colors[$ev]
    );
    $out.='[';
    foreach($data as $d)
    {
      $out .= json_encode($d).',';
    }
    $out = trim($out, ',').'],'."\n";
  }
  $dataDaily =  trim($out, ',');

    ?>
    <div class="row-fluid">
      <h2 class="heading">Campaign: <?php echo $name;?></h2>
      <h3 class="heading col-md-6">Last 24H</h3>
      <h3 class="heading col-md-6">Last Week</h3>
      <div class="col-md-6">
      <div id="charthourly-<?php echo $cid;?>"  style="height:400px; "></div>
      </div>
      <div class="col-md-6" class="col-md-6" >
      <div id="chartdaily-<?php echo $cid;?>" style="height:400px; "></div>
      </div>
    </div>
    <div class="clearfix"></div>
    <script type="text/javascript">
      $.jqplot('charthourly-<?php echo $cid;?>',
        [
          <?php echo $dataHourly; ?>
        ],
        {
          animate: true,
          seriesDefaults: {
            lineWidth: 1.5, // Width of the line in pixels.
            shadow: false
            },
         series:[
          <?php echo implode(',', $hourlySeriesOptions); ?>
        ],
       axes:{
        xaxis:{
              //this force to show all 0 to 24
          ticks:["<?php echo implode('","', $hourlyXlabels); ?>"],
          renderer:$.jqplot.DateAxisRenderer,
            tickOptions:{
            formatString:'%H'
          }
        },
        yaxis:{
          tickOptions:{
            formatString:'%d'
          },
          min: 0,
          tickInterval: 1
        }
        },
          rendererOptions: {
          // Speed up the animation a little bit.
          // This is a number of milliseconds.
          // Default for bar series is 3000.
          animation: {
            speed: 2500
          }
        },
        highlighter: {
          show: true,
            sizeAdjust: 7.5
        },
        cursor: {
          show: false
        },
        legend: {
          show: true,
          location: 'ne'      // compass direction, nw, n, ne, e, se, s, sw, w.
          },
          grid: {
            drawGridLines: true,        // wether to draw lines across the grid or not.
            gridLineColor: '#cccccc',    // *Color of the grid lines.
            background: '#ffffff',      // CSS color spec for background color of grid.
            borderColor: '#999999',     // CSS color spec for border around grid.
            borderWidth: 1.0,           // pixel width of border around grid.
            shadow: false                // draw a shadow for grid.

          }
      }
      );
      $.jqplot('chartdaily-<?php echo $cid;?>',

               [
                 <?php echo $dataDaily; ?>
               ],{
          seriesDefaults: {
            lineWidth: 1.5, // Width of the line in pixels.
            shadow: false
          },
          series:[
            <?php echo implode(',', $dailySeriesOptions); ?>
          ],
          axes:{
          xaxis:{
            renderer:$.jqplot.DateAxisRenderer,
            tickOptions:{
              formatString:'%b&nbsp;%#d'
            }
          },
          yaxis:{
            tickOptions:{
              formatString:'%d'
            },min: 0,
            tickInterval: 1
          }i
        },
          animate: true,
          rendererOptions: {
            // Speed up the animation a little bit.
            // This is a number of milliseconds.
            // Default for bar series is 3000.
            animation: {
              speed: 2500
            }
          },
         highlighter: {
           show: true,
           sizeAdjust: 7.5
         },
         cursor: {
           show: false
         },
          legend: {
            show: true,
            location: 'ne',     // compass direction, nw, n, ne, e, se, s, sw, w.
            xoffset: 12,        // pixel offset of the legend box from the x (or x2) axis.
            yoffset: 12        // pixel offset of the legend box from the y (or y2) axis.
          },

               grid: {
        drawGridLines: true,        // wether to draw lines across the grid or not.
          gridLineColor: '#cccccc',    // *Color of the grid lines.
        background: '#ffffff',      // CSS color spec for background color of grid.
          borderColor: '#999999',     // CSS color spec for border around grid.
          borderWidth: 1.0,           // pixel width of border around grid.
          shadow: false                // draw a shadow for grid.

      }
        }
      );
    </script>
  <?php }//end campaign loop

  endif; ?>
</div>
@stop
