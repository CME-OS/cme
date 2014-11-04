@extends('layouts.default')
@section('content')
<h1 class="page-header">Lists
  <small>Manage your lists</small>
</h1>
<div class="row">
  <div class="col-md-6">
    <h2>Add a List</h2>
    <form role="form" action="/lists/add" method="post">
      <div class="form-group">
        <label for="list-name">Name</label>
        <input type="text" name="name" class="form-control" id="list-name" placeholder="Name">
      </div>
      <div class="form-group">
        <label for="list-api">API EndPoint</label>
        <input type="text" name="endpoint" class="form-control" id="list-api" placeholder="http://">
      </div>
      <div class="form-group">
        <label for="list-description">Description</label>
        <textarea name="description" id="list-description" class="form-control" style="width: 100%;" cols="50" rows="4"></textarea>
      </div>
      <button type="submit" class="btn btn-default">Submit</button>
    </form>
  </div>
</div>
@stop
