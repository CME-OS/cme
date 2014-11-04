@extends('layouts.default')
@section('content')
  <h1 class="page-header">Brands <small>Manage your brands</small></h1>
  <div class="row">
    <div class="col-md-6">
      <?php if($brands): ?>
        <a href="/brands/new">Add a brand</a>
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Name</th>
            <th></th>
          </tr>
          </thead>
          <?php foreach($brands as $brand): ?>
            <tr>
              <td><?= $brand->name; ?></td>
              <td><a href="/brands/campaigns/<?= $brand->id; ?>">View Campaigns</a></td>
            </tr>
          <?php endforeach; ?>
        </table>

      <?php else: ?>
        <div class="alert alert-info">
          <p>You do not have any brands in CME. <a href="/brands/new">Add your first brand now</a></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
@stop
