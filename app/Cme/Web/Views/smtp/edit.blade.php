@extends('layouts.default')
@section('content')
<h1 class="page-header">SMTP Providers
  <small>Manage your SMTP providers</small>
</h1>

<div class="container">
<div class="row">
  <div class="col-md-12 well">
    <h2>Update SMTP Provider</h2>

    @include('partials.errors')

    <form role="form" action="/smtp-providers/update" method="post">
      <input type="hidden" name="id" value="<?= $smtpProvider->id ?>"/>
      <div class="form-group">
        <label for="smtp-name">Name</label>
        <input type="text" name="name" class="form-control" id="smtp-name" placeholder="Name" value="<?= $smtpProvider->name ?>">
      </div>
      <div class="form-group">
        <label for="smtp-host">Host</label>
        <input type="text" name="host" class="form-control" id="smtp-host" placeholder="Host" value="<?= $smtpProvider->host ?>">
      </div>
      <div class="form-group">
        <label for="smtp-username">Username</label>
        <input type="text" name="username" class="form-control" id="smtp-username" placeholder="Username" value="<?= $smtpProvider->username ?>">
      </div>
      <div class="form-group">
        <label for="smtp-password">Password</label>
        <input type="password" name="password" class="form-control" id="smtp-password" placeholder="Password">
      </div>
      <div class="form-group">
        <label for="smtp-port">Port</label>
        <input type="text" name="port" class="form-control" id="smtp-port" placeholder="Port" value="<?= $smtpProvider->port ?>">
      </div>
      <button type="submit" class="btn btn-success">Submit</button>
    </form>
  </div>
</div>
</div>
@stop
