@extends('layouts.default')
@section('content')
<script type="text/javascript" src="/assets/js/Chart.min.js"></script>
<h1 class="page-header">Analytics <small>See your how your campaigns performed</small></h1>
<div class="row">
  <div class="col-sm-12">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-3">
        <form action="" class="form">
        <select name="campaignId" class="form-control" onchange="location.href='/analytics/'+$(this).val();">
          <option value="0">Select a Campaign</option>
          <?php foreach($campaigns as $id => $name): ?>
            <option value="<?= $id ?>" <?= ($id == $selectedId)? 'selected="selected"' : ''; ?>><?= $name ?></option>
          <?php endforeach; ?>
        </select>
      </form>
        </div>
    </div>

    <?php if($selectedId > 0): ?>
    <div class="" style="background-color: #fff; border:0; margin-bottom:10px; ">
      <table>
        <tr>
          <td style="width:80px;"><strong>Brand:</strong></td><td><?= $campaign->brand->brandName; ?></td>
          </tr>
        <tr>
          <td><strong>List:</strong></td>
          <td>
            <a href="/lists/view/<?= $campaign->list->id ?>"><?= $campaign->list->name; ?></a>
          </td>
        </tr>
        <tr>
          <td><strong>Subject:</strong></td><td><?= $campaign->subject ?></td>
        </tr>
        <tr>
          <td><strong>Sent: </strong></td><td><?= date('D, M d Y H:iA', $campaign->sendTime) ?></td>
        </tr>
      </table>
    </div>

    <div class="kpi" style="background-color: #fff; border:0;">
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

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Link Activity</h3>
      </div>
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
    </div>


      <div class="row">
        <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">Last 10 Opens</h3>
            </div>
              <table class="table table-bordered">
                <tr>
                  <th>Email</th>
                  <th>Date</th>
                </tr>
                <?php foreach($opens as $s): ?>
                <tr>
                  <td><?= $s['email']; ?></td>
                  <td><?= date('D, d M Y H:iA', $s['time']); ?></td>
                </tr>
                <?php endforeach; ?>
              </table>
          </div>
        </div>
        <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">Last 10 Unsubscribes</h3>
            </div>
              <table class="table table-bordered">
                <tr>
                  <th>Email</th>
                  <th>Date</th>
                </tr>
                <?php foreach($unsubscribes as $s): ?>
                <tr>
                  <td><?= $s['email']; ?></td>
                  <td><?= date('D, d M Y H:iA', $s['time']); ?></td>
                </tr>
                <?php endforeach; ?>
              </table>
          </div>
        </div>
      </div>

    <?php else: ?>
      <div class="">
        <p style="font-size:30px; color:#ccc;">Select a campaign to display performance analytics</p>
      </div>
    <?php endif; ?>
  </div>
</div>


@stop
