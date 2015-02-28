@section('content')
@extends('layouts.setup')
<div class="pull-right"><a href="/setup/skip" class="btn btn-danger">Skip</a></div>
<h1 class="page-header">Install CME:
  <small>Campaigns Made Easy</small>
</h1>
<div class="container">
<form role="form" action="/setup/install" method="post">
  <div class="row">
    <div class="col-md-12 well">
      <div class="form-group">
        <label for="install-dbName">Database Name:</label>
        <input type="text" name="dbName" class="form-control" id="install-dbName" value="cme">
      </div>
      <div class="form-group">
        <label for="install-dbHost">Database Host:</label>
        <input type="text" name="dbHost" class="form-control" id="install-dbHost" value="localhost">
      </div>
      <div class="form-group">
        <label for="install-dbUser">Database Username:</label>
        <input type="text" name="dbUser" class="form-control" id="install-dbUser" value="">
      </div>
      <div class="form-group">
        <label for="install-dbPass">Database Password:</label>
        <input type="password" name="dbPass" class="form-control" id="install-dbPass" value="">
      </div>
    </div>
  </div>
  <div class="alert alert-info">To send emails, you must link CME to your Amazon Web Service account. If you don't have these details you can leave them blank</div>
  <div class="row">
    <div class="col-md-12 well">
      <div class="form-group">
        <label for="install-awsKey">AWS Key:</label>
        <input type="text" name="awsKey" class="form-control" id="install-awskey" value="">
      </div>
      <div class="form-group">
        <label for="install-awsSecret">AWS Secret:</label>
        <input type="password" name="awsSecret" class="form-control" id="install-awsSecret" value="">
      </div>
      <div class="form-group">
        <label for="install-awsRegion">AWS Region:</label>
        <input type="text" name="awsRegion" class="form-control" id="install-awsRegion" value="">
      </div>
      <button type="submit" class="btn btn-lg btn-block btn-success">Install</button>
    </div>
  </div>

</form>
</div>

@stop
