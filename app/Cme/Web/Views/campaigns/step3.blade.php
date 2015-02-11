@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="/assets/datetimepicker/css/datetimepicker.min.css"/>
<h1 class="page-header">Campaigns
  <small>Manage your campaigns</small>
</h1>
<form role="form" action="/campaigns/add" method="post">
  <input type="hidden" name="step" value="3"/>
  <input type="hidden" name="id" value="<?= $campaign->id ?>"/>
  <h2>Step 3: Schedule & Prioritize a Campaign</h2>

  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="campaign-priority">Send Priority:</label>
        <select name="send_priority" id="campaign-priority" class="form-control">
          <option value="2" <?= ($campaign->send_priority == 2)? 'selected="selected"' : '' ?>>Normal</option>
          <option value="1" <?= ($campaign->send_priority == 1)? 'selected="selected"' : '' ?>>Low</option>
          <option value="3" <?= ($campaign->send_priority == 3)? 'selected="selected"' : '' ?>>Medium</option>
          <option value="4" <?= ($campaign->send_priority == 4)? 'selected="selected"' : '' ?>>High</option>
        </select>
      </div>
      <div class="form-group">
        <label for="campaign-send-time">When do you want to send this campaign?</label>
        <div id="datetimepicker" class="input-group date">
          <input type="text" name="send_time" class="form-control" id="campaign-send-time" value="<?= date('Y-m-d H:i:s', $campaign->send_time) ?>">
          <span class="input-group-addon">
            <span class="glyphicon glyphicon-time"></span>
          </span>
        </div>
      </div>
      <div class="form-group">
        <label for="campaign-smtp-provider">SMTP Provider:</label>
        <select name="smtp_provider_id" id="campaign-smtp-provider" class="form-control">
          <option value="0" <?= ($campaign->smtp_provider_id == 0)? 'selected="selected"' : '' ?>>Use Default (AWS 1)</option>
          <option value="1" <?= ($campaign->smtp_provider_id == 1)? 'selected="selected"' : '' ?>>AWS 1</option>
          <option value="2" <?= ($campaign->smtp_provider_id == 2)? 'selected="selected"' : '' ?>>SendGrid</option>
        </select>
      </div>
    </div>
  </div>
  <a href="/campaigns/new/2" class="btn btn-default">Back</a>
  <button type="submit" class="btn btn-success">Save</button>
</form>
@stop