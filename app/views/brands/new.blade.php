@extends('layouts.default')
@section('content')
<h1 class="page-header">Brands
  <small>Manage your brands</small>
</h1>
<div class="row">
  <div class="col-md-6">
    <h2>Add a Brand</h2>
    <form role="form" action="/brands/add" method="post">
      <div class="form-group">
        <label for="brand-name">Name</label>
        <input type="text" name="name" class="form-control" id="brand-name" placeholder="Brand Name">
      </div>
      <div class="form-group">
        <label for="sender-name">Default Sender Name</label>
        <input type="text" name="sender_name" class="form-control" id="brand-name" placeholder="Sender Name">
      </div>
      <div class="form-group">
        <label for="brand-name">Default Sender Email</label>
        <input type="email" name="sender_email" class="form-control" id="sender-email" placeholder="Sender Email">
      </div>
      <div class="form-group">
        <label for="domain-name">Domain Name</label>
        <input type="text" name="domain_name" class="form-control" id="domain-name" placeholder="Brand's Domain Name">
      </div>
      <div class="form-group">
        <label for="unsubscribe-url">Unsubscribe URL</label>
        <input type="text" name="unsubscribe_url" class="form-control" id="unsubscribe-url" placeholder="Unsubscribe URL">
      </div>
      <div class="form-group">
        <label for="brand-logo">Brand Logo</label>
        <input type="file" name="logo" id="brand-logo">
      </div>
      <button type="submit" class="btn btn-default">Submit</button>
    </form>
  </div>
</div>
@stop
