@extends('layouts.default')
@section('content')
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
    <?php foreach($stats as $campaignId => $data): ?>
      <div class="col-md-2">
        <div class="panel panel-default">
          <div class="panel-heading">
            <a href="/analytics/<?= $campaignId ?>" title="<?= $campaignLookUp[$campaignId] ?>">
              <?= Str::limit($campaignLookUp[$campaignId], 20) ?>
            </a>
          </div>
          <div class="panel-body">
            <table class="table table-striped table-hover table-condensed" style="">
              <?php foreach($eventTypes as $type): ?>
                <tr>
                  <td><?= ucwords($type) ?> </td>
                  <td><strong><?= $data[$type]; ?> <?= ($type == 'opened') ? '(' . $data['opened_rate'] . ')' : '' ?></strong></td>
                </tr>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  @endif
</div>
@stop
