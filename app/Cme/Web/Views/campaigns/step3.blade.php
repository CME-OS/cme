@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="/assets/datetimepicker/css/datetimepicker.min.css"/>
<h1 class="page-header">Step 3:
  <small>Schedule & Prioritize a Campaign</small>
</h1>
<div class="container">
<form role="form" action="/campaigns/add" method="post">
  <input type="hidden" name="step" value="3"/>
  <input type="hidden" name="id" value="<?= $campaign->id ?>"/>
  <div class="row">
    <div class="col-md-12 well">
      <div class="form-group">
        <label for="campaign-priority">Send Priority:</label>
        <select name="send_priority" id="campaign-priority" class="form-control">
          <option value="2" <?= ($campaign->sendPriority == 2)? 'selected="selected"' : '' ?>>Normal</option>
          <option value="1" <?= ($campaign->sendPriority == 1)? 'selected="selected"' : '' ?>>Low</option>
          <option value="3" <?= ($campaign->sendPriority == 3)? 'selected="selected"' : '' ?>>Medium</option>
          <option value="4" <?= ($campaign->sendPriority == 4)? 'selected="selected"' : '' ?>>High</option>
        </select>
      </div>
      <div class="form-group <?= isset($errors['send_time'])? 'has-error has-feedback': '' ?>">
        <label for="campaign-send-time">When do you want to send this campaign? <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['send_time'])? ' - '.$errors['send_time']->message: '' ?></span></label>
        <div id="datetimepicker" class="input-group date">
          <input type="text" name="send_time" class="form-control" id="campaign-send-time" value="<?= isset($input['send_time'])? $input['send_time'] : $campaign->sendTime ?>">
          <span class="input-group-addon">
            <span class="glyphicon glyphicon-time"></span>
          </span>
        </div>
      </div>
      <div class="form-group">
        <label for="campaign-smtp-provider">SMTP Provider:</label>
        <select name="smtp_provider_id" id="campaign-smtp-provider" class="form-control">
          <option value="0">Use Default</option>
          <?php foreach($smtpProviders as $provider): ?>
          <option value="<?= $provider->id; ?>" <?= (($campaign->smtpProviderId == $provider->id) || $provider->default)? 'selected="selected"' : '' ?>><?= $provider->name ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <a href="/campaigns/new/2" class="btn btn-default">Back</a>
      <button type="submit" class="btn btn-success">Save</button>
    </div>
  </div>
</form>
</div>
@stop
