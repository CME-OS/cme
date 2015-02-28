@extends('layouts.default')
@section('content')
<h1 class="page-header">Lists
  <small>Manage your lists</small>
</h1>
<div class="container">
<div class="row">
  <div class="col-md-12 well">
    <h2>Add a Subscriber</h2>
    <form role="form" action="/lists/add-subscriber" method="post">
      <input type="hidden" name="id" value="<?= $id ?>"/>
      <div class="form-group">
        <label for="subscriber-email">Email</label>
        <input type="text" name="email" class="form-control" id="subscriber-email" placeholder="">
      </div>
      <?php foreach($columns as $column): ?>
        <?php if($column['value'] != 'date_created'): ?>
      <div class="form-group">
        <label for="subscriber-<?= $column['value']; ?>"><?= \Illuminate\Support\Str::title(str_replace('_', ' ', $column['value'])); ?></label>
        <input type="text" name="<?= $column['value'] ?>" class="form-control" id="subscriber-<?= $column['value']; ?>" placeholder="">
      </div>
      <?php endif; ?>
      <?php endforeach; ?>
      <button type="submit" class="btn btn-success">Submit</button>
    </form>
  </div>
</div>
</div>
@stop
