@extends('layouts.default')
@section('content')
<script type="text/javascript" src="/assets/js/Chart.min.js"></script>
<h1>Analytics</h1>
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
    <div class="well" style="background-color: #fff; border:0;">
      <table>
        <tr>
          <td style="width:80px;"><strong>Brand:</strong></td><td><?= $campaign->brand->brand_name; ?></td>
          </tr>
        <tr>
          <td><strong>List:</strong></td><td><?= $campaign->lists->name; ?></td>
        </tr>
        <tr>
          <td><strong>Subject:</strong></td><td><?= $campaign->subject ?></td>
        </tr>
        <tr>
          <td><strong>Sent: </strong></td><td><?= date('D, M d Y H:iA', $campaign->send_time) ?></td>
        </tr>
      </table>
    </div>

    <div class="well kpi" style="background-color: #fff; border:0;">
      <div class="row">
        <div class="col-md-3">
          <div class="well queued-well text-center">
            <div style="font-size:30px;"><?= $stats['queued']['total']; ?></div>
            <span>Queued</span>
          </div>
        </div>
        <div class="col-md-3">
          <div class="well sent-well text-center">
            <div style="font-size:30px;"><?= $stats['sent']['unique']; ?></div>
            <span>Sent</span>
          </div>
        </div>
        <div class="col-md-3">
          <div class="well opened-well text-center">
            <div style="font-size:30px;"><?= $stats['opened']['unique']; ?></div>
            <span>Opened</span>
          </div>
        </div>
        <div class="col-md-3">
          <div class="well unsubscribed-well text-center">
            <div style="font-size:30px;"><?= $stats['unsubscribed']['unique']; ?></div>
            <span>Unsubscribed</span>
          </div>
        </div>
      </div>
    </div>

    <div class="well" style="background-color: #fff; border:0;">
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

      <div class="row">
        <div class="col-md-6">
          <h2>Last 10 Opens</h2>
          <table class="table table-bordered">
            <tr>
              <th>Email</th>
              <th>Date</th>
            </tr>
            <?php foreach($opens as $s): ?>
            <tr>
              <td><?= $s->email; ?></td>
              <td><?= date('D, d M Y H:iA', $s->time); ?></td>
            </tr>
            <?php endforeach; ?>
          </table>
        </div>
        <div class="col-md-6">
          <h2>Last 10 Unsubscribes</h2>
          <table class="table table-bordered">
            <tr>
              <th>Email</th>
              <th>Date</th>
            </tr>
            <?php foreach($unsubscribes as $s): ?>
            <tr>
              <td><?= $s->email; ?></td>
              <td><?= date('D, d M Y H:iA', $s->time); ?></td>
            </tr>
            <?php endforeach; ?>
          </table>
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


@stop
