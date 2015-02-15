@extends('layouts.default')
@section('content')
<h1 class="page-header">Brands
  <small>Manage your brands</small>
</h1>
<div class="row">
  <div class="col-md-6">
    <h2>Add an SMTP Provider</h2>

    @include('partials.errors')

    <form role="form" action="/smtp-providers/add" method="post">
      <div class="form-group">
        <label for="smtp-name">Name</label>
        <input type="text" name="name" class="form-control" id="smtp-name" placeholder="Name">
      </div>
      <div class="form-group">
        <label for="smtp-host">Host</label>
        <input type="text" name="host" class="form-control" id="smtp-host" placeholder="Host">
      </div>
      <div class="form-group">
        <label for="smtp-username">Username</label>
        <input type="text" name="username" class="form-control" id="smtp-username" placeholder="Username">
      </div>
      <div class="form-group">
        <label for="smtp-password">Password</label>
        <input type="password" name="password" class="form-control" id="smtp-password" placeholder="Password">
      </div>
      <div class="form-group">
        <label for="smtp-port">Port</label>
        <input type="text" name="port" class="form-control" id="smtp-port" placeholder="Port">
      </div>
      <button type="submit" class="btn btn-default">Submit</button>
    </form>
  </div>
</div>
@stop
