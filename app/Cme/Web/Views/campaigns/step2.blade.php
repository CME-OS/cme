@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="/assets/datetimepicker/css/datetimepicker.min.css"/>
<h1 class="page-header">Campaigns
  <small>Manage your campaigns</small>
</h1>
<form role="form" action="/campaigns/new" method="post">
  <input type="hidden" name="step" value="2"/>
  <h2>Step 2: Compose Campaign</h2>

  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="brand-name">Subject</label>
        <input type="text" name="subject" class="form-control" id="campaign-subject" placeholder="Subject">
      </div>
      </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="sender-name">Message</label>
        <textarea name="html_content" class="form-control" id="campaign-message" cols="30" rows="10"></textarea>
      </div>
    </div>
    <div class="col-md-6">
      <div class="">
        <p><strong>Available PlaceHolders</strong></p>
        <div class="placeholders">
          <ul>
            <li>placeholder1</li>
            <li>placeholder2</li>
            <li>placeholder3</li>
            <li>placeholder4</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-danger">Back</button>
  <button type="submit" class="btn btn-default">Next</button>
</form>
<script src="/assets/ckeditor/ckeditor.js"></script>
<script>
  CKEDITOR.replace(
    'campaign-message'
  );
</script>
@stop
