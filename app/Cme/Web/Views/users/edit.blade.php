@extends('layouts.default')
@section('content')
<h1 class="page-header">Users
  <small>Manage your users</small>
</h1>
<div class="row">
  <div class="col-md-6">
    <h2>Update User</h2>
    <form role="form" action="/lists/update" method="post">
      <input type="hidden" name="id" value="<?= $user->id ?>"/>
      <div class="form-group">
        <label for="list-name">Email</label>
        <input type="text" name="email" class="form-control" id="email" value="<?= $user->email ?>">
      </div>
      <div class="form-group">
        <label for="list-api">Password</label>
        <input type="password" name="password" class="form-control" id="password" value="">
      </div>
      <button type="submit" class="btn btn-default">Submit</button>
    </form>
  </div>
</div>
@stop
