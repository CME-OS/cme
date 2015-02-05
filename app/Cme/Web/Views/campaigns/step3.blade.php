@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="/assets/datetimepicker/css/datetimepicker.min.css"/>
<h1 class="page-header">Campaigns
  <small>Manage your campaigns</small>
</h1>
<form role="form" action="/campaigns/add" method="post">
  <h2>Step 3: Schedule & Prioritize a Campaign</h2>

  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="campaign-from">Send Campaign As:</label>
        <input type="text" name="from" class="form-control" id="campaign-from" placeholder="<name> email@domain.com">
      </div>
      <div class="form-group">
        <label for="campaign-priority">Send Priority:</label>
        <select name="send_priority" id="campaign-priority" class="form-control">
          <option value="2">Normal</option>
          <option value="1">Low</option>
          <option value="3">Medium</option>
          <option value="4">High</option>
        </select>
      </div>
      <div class="form-group">
        <label for="campaign-send-time">When do you want to send this campaign?</label>
        <div id="datetimepicker" class="input-group date">
          <input type="text" name="send_time" class="form-control" id="campaign-send-time">
          <span class="input-group-addon">
            <span class="glyphicon glyphicon-time"></span>
          </span>
        </div>
      </div>
      <div class="form-group">
        <label for="campaign-smtp-provider">SMTP Provider:</label>
        <select name="smpt_provider_id" id="campaign-smtp-provider" class="form-control">
          <option value="2">Use Default (AWS 1)</option>
          <option value="2">AWS 1</option>
          <option value="1">SendGrid</option>
        </select>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-danger">Back</button>
  <button type="submit" class="btn btn-default">Save</button>
</form>
@stop
