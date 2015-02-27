@extends('layouts.default')
@section('content')
  <h1 class="page-header">Users <small>Manage your users</small></h1>
  <div class="row">
    <div class="col-md-12">
      <?php if($users): ?>
        <p><a href="/users/new" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add a User</a></p>
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Email</th>
            <th>Status</th>
            <th>Created</th>
            <th></th>
          </tr>
          </thead>
          <?php foreach($users as $user): ?>
            <tr>
              <td><span class="glyphicon glyphicon-user"></span>
                <a href="{{ URL::route('users.view', $user->id) }}" ><strong><?= $user->email; ?></strong></a>
              </td>
              <td><?= $user->active; ?></td>
              <td><?= $user->created_at; ?></td>
              <td>
                <div class="pull-right">
                <a href="{{ URL::route('users.edit', $user->id) }}" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="{{ URL::route('users.delete', $user->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>

      <?php else: ?>
        <div class="alert alert-info">
          <p>You do not have any users in CME. <a href="{{ URL::route('users.new') }}">Add your first User now</a></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
@stop
