@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="/assets/datetimepicker/css/datetimepicker.min.css"/>
<h1 class="page-header">Campaigns
  <small>Manage your campaigns</small>
</h1>
<form role="form" action="/campaigns/new" method="post">
  <input type="hidden" name="step" value="3"/>
  <input type="hidden" name="id" value="<?= $campaign->id ?>"/>
  <h2>Step 2: Compose Campaign</h2>

  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="brand-name">Subject</label>
        <input type="text" name="subject" class="form-control" id="campaign-subject" value="<?= $campaign->subject ?>">
      </div>
      </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="sender-name">Message</label>
        <textarea name="html_content" class="form-control" id="campaign-message" cols="30" rows="10"><?= $campaign->subject ?></textarea>
      </div>
    </div>
    <div class="col-md-6">
      <div class="">
        <p><strong>Available PlaceHolders</strong></p>
        <div class="placeholders">
          <ul>
            <?php foreach($placeholders as $placeholder): ?>
            <li>[<?= $placeholder ?>]</li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <a href="/campaigns/new/1" class="btn btn-default">Back</a>
  <button type="submit" class="btn btn-success">Next</button>
</form>
<script src="/assets/ckeditor/ckeditor.js"></script>
<script>
  CKEDITOR.replace(
    'campaign-message'
  );
</script>
@stop
