@extends('layouts.default')
@section('content')
<h1 class="page-header"><?= $listName ?> List
</h1>
<div class="row">
  <div class="col-md-12">
    <?php if($subscribers): ?>
      <form action="">
        <input type="text" class="form-control" placeholder="Search this List"/>
      </form>
      <table class="table table-striped">
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
            <td><?= $subscriber->$c; ?></td>
            <?php endforeach; ?>
            <td>
              <a href="">View</a> |
              <a href="">Delete</a> |
              <a href="">Unsubscribe</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <div class="alert alert-info">
        <p>You do not have any subscribers in <?= $listName ?> list</p>
      </div>
      <h2>Import From API</h2>
      <form role="form" action="/lists/import/api" method="post">
        <div class="form-group">
          <label for="brand-name">Name</label>
          <input type="hidden" name="listId" value="<?= $listId ?>" >
          <input type="text" name="endpoint" class="form-control" id="brand-name" placeholder="http://domain.com/list.php">
        </div>
        <button type="submit" class="btn btn-default">Import</button>
      </form>
      <h2>Import From CSV</h2>
      <form role="form" action="/lists/import/csv" method="post">
        <div class="form-group">
          <label for="list-csv">CSV File</label>
          <input type="file" name="logo" id="list-csv">
        </div>
        <button type="submit" class="btn btn-default">Import</button>
      </form>
    <?php endif; ?>
  </div>
</div>
@stop
