@extends('layouts.default')
@section('content')
<h1 class="page-header">Lists
  <small>Manage your lists</small>
</h1>
<div class="container">
<div class="row">
  <div class="col-md-12 well">
    <h2>Update List - <?= $list->name ?></h2>
    <form role="form" action="/lists/update" method="post">
      <input type="hidden" name="id" value="<?= $list->id ?>"/>
      <div class="form-group <?= isset($errors['name'])? 'has-error has-feedback': '' ?>">
        <label for="list-name">Name<span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['name'])? ' - '.$errors['name']->message: '' ?></span></label>
        <input type="text" name="name" class="form-control" id="list-name" value="<?= isset($input['name'])? $input['name'] : $list->name ?>">
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['name'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['endpoint'])? 'has-error has-feedback': '' ?>">
        <label for="list-api">API EndPoint <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['endpoint'])? ' - '.$errors['endpoint']->message: '' ?></span></label>
        <input type="text" name="endpoint" class="form-control" id="list-api" value="<?= isset($input['endpoint'])? $input['endpoint'] : $list->endpoint ?>">
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['endpoint'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['refresh_interval'])? 'has-error has-feedback': '' ?>">
        <label for="list-refresh-interval">Refresh Interval (For API List Only) <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['refresh_interval'])? ' - '.$errors['refresh_interval']->message: '' ?></span></label>
        <select name="refresh_interval" id="list-refresh-interval" class="form-control">
          <option value="" <?= ($list->refreshInterval == "")? 'selected="selected"': '' ?>>Not Applicable</option>
          <option value="60" <?= ($list->refreshInterval == 60)? 'selected="selected"': '' ?>>Every minute</option>
          <option value="300" <?= ($list->refreshInterval == 300)? 'selected="selected"': '' ?>>Every 5 minutes</option>
          <option value="600" <?= ($list->refreshInterval == 600)? 'selected="selected"': '' ?>>Every 10 minutes</option>
          <option value="3600" <?= ($list->refreshInterval == 3600)? 'selected="selected"': '' ?>>Every hour</option>
        </select>
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['refresh_interval'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['description'])? 'has-error has-feedback': '' ?>">
        <label for="list-description">Description</label>
        <textarea name="description" id="list-description" class="form-control" style="width: 100%;" cols="50" rows="4"><?= isset($input['description'])? $input['description'] : $list->description ?></textarea>
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['description'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <button type="submit" class="btn btn-success">Submit</button>
    </form>
  </div>
</div>
</div>
@stop
