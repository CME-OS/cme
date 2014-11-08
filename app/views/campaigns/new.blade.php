@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="/assets/datetimepicker/css/datetimepicker.min.css"/>
<h1 class="page-header">Campaigns
  <small>Manage your campaigns</small>
</h1>
<form role="form" action="/campaigns/add" method="post">
  <h2>Create a Campaign</h2>

  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for="brand-name">Subject</label>
        <input type="text" name="subject" class="form-control" id="brand-name" placeholder="Subject">
      </div>
      <div class="form-group">
        <label for="sender-name">Message</label>
        <textarea name="html_content" class="form-control" id="campaign-message" cols="30" rows="10"></textarea>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="campaign-brand-id">Which Brand is this campaign for?</label>
        <select name="brand_id" id="campaign-brand-id" class="form-control">
          <option value="">SELECT</option>
          <?php foreach($brands as $brand): ?>
            <option value="<?= $brand->id ?>"><?= $brand->brand_name; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="campaign-list-id">Which list should campaign be sent to?</label>
        <select name="list_id" id="campaign-list-id" class="form-control">
          <option value="">SELECT</option>
          <?php foreach($lists as $list): ?>
            <option value="<?= $list->id ?>"><?= $list->name; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
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
      <div class="well">
         <p><strong>Available PlaceHolders</strong></p>
          <div class="placeholders"></div>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-default">Save</button>
</form>
<script src="/assets/ckeditor/ckeditor.js"></script>
<script>
  CKEDITOR.replace(
    'campaign-message'
  );
</script>
@stop
