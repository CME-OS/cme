@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="/assets/datetimepicker/css/datetimepicker.min.css"/>
<h1 class="page-header">Step 2:
  <small>Compose Campaign</small>
</h1>
<div class="container">
<form role="form" action="/campaigns/new" method="post">
  <input type="hidden" name="step" value="3"/>
  <input type="hidden" name="id" value="<?= $campaign->id ?>"/>
  <div class="row">
    <div class="col-md-12 well">
      <div class="form-group">
        <label for="campaign-template">Choose a template</label>
        <select name="template" id="campaign-template" class="form-control">
          <option value="">I don't need a template :B</option>
          <?php foreach($templates as $id => $name): ?>
          <option value="<?= $id ?>" <?= ($id == -1)? 'selected="selected"' : '' ?>>
            <?= $name; ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="campaign-subject">Subject:</label>
         <?= $campaign->subject ?>
      </div>
      <div class="form-group">
        <label for="sender-name">Message <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['html_content'])? ' - '.$errors['html_content']->message: '' ?></span></label>
        <textarea name="html_content" class="form-control" id="campaign-message" cols="30" rows="10"><?= isset($input['html_content'])? $input['html_content'] : $campaign->htmlContent ?></textarea>
      </div>
      <a href="/campaigns/new/1" class="btn btn-default">Back</a>
      <button type="submit" class="btn btn-success">Next</button>
    </div>
  </div>
</form>
</div>
<script src="/assets/ckeditor/ckeditor.js"></script>
<script>
  CKEDITOR.replace(
    'campaign-message'
  );
</script>
@stop
