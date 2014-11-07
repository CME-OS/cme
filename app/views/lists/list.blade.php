@extends('layouts.default')
@section('content')
  <h1 class="page-header">Lists <small>Manage your lists</small></h1>
  <div class="row">
    <div class="col-md-6">
      <?php if($lists): ?>
        <a href="/lists/new">Add a list</a>
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Name</th>
            <th>Size</th>
            <th></th>
          </tr>
          </thead>
          <?php foreach($lists as $list): ?>
            <tr>
              <td><?= $list->name; ?></td>
              <td><?= number_format($list->size, 0); ?></td>
              <td><a href="/lists/view/<?= $list->id; ?>">View</a></td>
            </tr>
          <?php endforeach; ?>
        </table>

      <?php else: ?>
        <div class="alert alert-info">
          <p>You do not have any lists in CME. <a href="/lists/new">Add your first List now</a></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
@stop
