@extends('layouts.default')
@section('content')
<h1 class="page-header">SMTP Providers
  <small>Manage your SMTP providers</small>
</h1>

<div class="container">
<div class="row">
  <div class="col-md-12 well">
    <h2>Update SMTP Provider</h2>
    <form role="form" action="/smtp-providers/update" method="post">
      <input type="hidden" name="id" value="<?= $smtpProvider->id ?>"/>
      <div class="form-group <?= isset($errors['name'])? 'has-error has-feedback': '' ?>">
        <label for="smtp-name">Name <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['name'])? ' - '.$errors['name']->message: '' ?></span></label>
        <input type="text" name="name" class="form-control" id="smtp-name" placeholder="Name" value="<?= isset($input['name'])? $input['name'] : $smtpProvider->name ?>">
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['name'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['host'])? 'has-error has-feedback': '' ?>">
        <label for="smtp-host">Host <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['host'])? ' - '.$errors['host']->message: '' ?></span></label>
        <input type="text" name="host" class="form-control" id="smtp-host" placeholder="Host" value="<?= isset($input['host'])? $input['host'] : $smtpProvider->host ?>">
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['host'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['username'])? 'has-error has-feedback': '' ?>">
        <label for="smtp-username">Username <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['username'])? ' - '.$errors['username']->message: '' ?></span></label>
        <input type="text" name="username" class="form-control" id="smtp-username" placeholder="Username" value="<?= isset($input['username'])? $input['username'] : $smtpProvider->username ?>" >
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['username'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['password'])? 'has-error has-feedback': '' ?>">
        <label for="smtp-password">Password <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['password'])? ' - '.$errors['password']->message: '' ?></span></label>
        <input type="password" name="password" class="form-control" id="smtp-password" placeholder="Password" value="<?= isset($input['password'])? $input['password'] : '' ?>">
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['password'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['port'])? 'has-error has-feedback': '' ?>">
        <label for="smtp-port">Port <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['port'])? ' - '.$errors['port']->message: '' ?></span></label>
        <input type="text" name="port" class="form-control" id="smtp-port" placeholder="Port" value="<?= isset($input['port'])? $input['port'] : $smtpProvider->port ?>">
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['port'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <button type="submit" class="btn btn-success">Submit</button>
    </form>
  </div>
</div>
</div>
@stop
