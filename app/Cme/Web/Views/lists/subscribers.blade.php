@extends('layouts.default')
@section('content')
<h1 class="page-header"><?= $list->name ?> List</h1>
<?php if($subscribers): ?>
<div class="row alert alert-info" style="border-radius: 0;">
  <div class="col-md-12">
    <p style="font-weight: bold; font-size:18px;">Import more subscribers</p>
    <div class="row">
      <div class="col-md-3">
        <form class="form-inline" role="form" action="/lists/import/api" method="post">
          <div class="form-group">
            <input type="hidden" name="listId" value="<?= $list->id ?>">
            <input type="text" name="endpoint" class="form-control" id="brand-name" placeholder="http://domain.com/list.php" value="<?= $list->endpoint ?>">
          </div>
          <button type="submit" class="btn btn-default">Import From API</button>
        </form>
      </div>
      <div class="col-md-3">
        <form class="form-inline" role="form" action="/lists/import/csv" method="post" enctype="multipart/form-data">
          <input type="hidden" name="listId" value="<?= $list->id ?>">
          <div class="form-group">
            <input type="file" name="listFile" id="list-csv">
          </div>
          <button type="submit" class="btn btn-default">Import From CSV</button>
        </form>
      </div>
      <div class="col-md-offset-6"></div>
    </div>
  </div>
</div>
<?php endif; ?>
<div class="row">
  <div class="col-md-12">
    <?php if($subscribers): ?>
      <div class="row">
        <div class="col-md-6">
          <form action="" style="margin-top:20px;">
            <input type="text" class="form-control" placeholder="Search this List"/>
          </form>
        </div>
        <div class="col-md-2 col-md-offset-4">
          <div class="pull-right"><?php //echo $subscribers->links(); ?></div>
        </div>
      </div>
      <table class="table table-striped table-hover">
        <thead>
        <tr>
          <?php foreach($columns as $c): ?>
          <th><?= $c ?></th>
          <?php endforeach; ?>
          <th></th>
        </tr>
        </thead>
        <?php foreach($subscribers as $subscriber): ?>
          <tr>
          <?php foreach($columns as $c): ?>
            <td><?= $subscriber->{camel_case($c)}; ?></td>
            <?php endforeach; ?>
            <td>
              <a href="/lists/<?= $list->id ?>/delete-subscriber/<?= $subscriber->id ?>" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <div class="alert alert-info">
        <p>You do not have any subscribers in <?= $list->name ?> list</p>
      </div>
      <h2>Import From API</h2>
      <form role="form" action="/lists/import/api" method="post">
        <div class="form-group">
          <input type="hidden" name="listId" value="<?= $list->id ?>">
          <input type="text" name="endpoint" class="form-control" id="brand-name" placeholder="http://domain.com/list.php" value="<?= $list->endpoint ?>">
        </div>
        <button type="submit" class="btn btn-default">Import</button>
      </form>
      <h2>Import From CSV</h2>
      <form role="form" action="/lists/import/csv" method="post" enctype="multipart/form-data">
        <input type="hidden" name="listId" value="<?= $list->id ?>">
        <div class="form-group">
          <label for="list-csv">CSV File</label>
          <input type="file" name="listFile" id="list-csv">
        </div>
        <button type="submit" class="btn btn-default">Import</button>
      </form>
    <?php endif; ?>
  </div>
</div>
@stop
