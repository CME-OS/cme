@extends('layouts.default')
@section('content')
<h1 class="page-header">Users
  <small>Manage your users</small>
</h1>
<div class="container">
<div class="row">
  <div class="col-md-12 well">
    <h2>Add User</h2>
    <form role="form" action="/users/add" method="post" autocomplete="off">
      <div class="form-group <?= isset($errors['email'])? 'has-error has-feedback': '' ?>">
        <label for="list-name">Email <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['email'])? ' - '.$errors['email']->message: '' ?></span></label>
        <input type="text" name="email" class="form-control" id="email" value="<?= isset($input['email'])? $input['email'] : '' ?>">
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['email'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['password'])? 'has-error has-feedback': '' ?>">
        <label for="list-api">Password <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['password'])? ' - '.$errors['password']->message: '' ?></span></label>
        <input type="password" name="password" class="form-control" id="password" value="<?= isset($input['password'])? $input['password'] : '' ?>">
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['password'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <button type="submit" class="btn btn-success">Submit</button>
    </form>
  </div>
</div>
</div>
@stop
