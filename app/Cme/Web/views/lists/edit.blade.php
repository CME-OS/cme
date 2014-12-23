@extends('layouts.default')
@section('content')
<h1 class="page-header">Lists
  <small>Manage your lists</small>
</h1>
<div class="row">
  <div class="col-md-6">
    <h2>Update List - <?= $list->name ?></h2>
    <form role="form" action="/lists/update" method="post">
      <input type="hidden" name="id" value="<?= $list->id ?>"/>
      <div class="form-group">
        <label for="list-name">Name</label>
        <input type="text" name="name" class="form-control" id="list-name" value="<?= $list->name ?>">
      </div>
      <div class="form-group">
        <label for="list-api">API EndPoint</label>
        <input type="text" name="endpoint" class="form-control" id="list-api" value="<?= $list->endpoint ?>">
      </div>
      <div class="form-group">
        <label for="list-refresh-interval">Refresh Interval (For API List Only)</label>
        <select name="refresh_interval" id="list-refresh-interval" class="form-control">
          <option value="" <?= ($list->refresh_interval == "")? 'selected="selected"': '' ?>>Not Applicable</option>
          <option value="60" <?= ($list->refresh_interval == 60)? 'selected="selected"': '' ?>>Every minute</option>
          <option value="300" <?= ($list->refresh_interval == 300)? 'selected="selected"': '' ?>>Every 5 minutes</option>
          <option value="600" <?= ($list->refresh_interval == 600)? 'selected="selected"': '' ?>>Every 10 minutes</option>
          <option value="3600" <?= ($list->refresh_interval == 3600)? 'selected="selected"': '' ?>>Every hour</option>
        </select>
      </div>
      <div class="form-group">
        <label for="list-description">Description</label>
        <textarea name="description" id="list-description" class="form-control" style="width: 100%;" cols="50" rows="4"><?= $list->description ?></textarea>
      </div>
      <button type="submit" class="btn btn-default">Submit</button>
    </form>
  </div>
</div>
@stop
