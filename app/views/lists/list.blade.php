@extends('layouts.default')
@section('content')
  <h1 class="page-header">Lists <small>Manage your lists</small></h1>
  <div class="row">
    <div class="col-md-6">
      <a href="/lists/new">Add a list</a>
      <?php if($lists): ?>
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Name</th>
            <th></th>
          </tr>
          </thead>
          <?php foreach($lists as $list): ?>
            <tr>
              <td><?= $list->name; ?></td>
              <td><a href="/lists/view/<?= $list->id; ?>">View</a></td>
            </tr>
          <?php endforeach; ?>
        </table>

      <?php else: ?>
        <div class="alert alert-info">
          <p>You do not have any lists in CME. Add your first List below</p>
        </div>
        <form role="form" action="/lists/add" method="post">
          <div class="form-group">
            <label for="brand-name">Name</label>
            <input type="text" name="name" class="form-control" id="brand-name" placeholder="Name">
          </div>
          <div class="form-group">
            <label for="list-description">Description</label>
            <textarea name="description" id="list-description" class="form-control" style="width: 100%;" cols="50" rows="4"></textarea>
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
@stop
