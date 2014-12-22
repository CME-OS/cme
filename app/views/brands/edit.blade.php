@extends('layouts.default')
@section('content')
<h1 class="page-header">Brands
  <small>Manage your brands</small>
</h1>
<div class="row">
  <div class="col-md-6">
    <h2>Update Brand</h2>

    @include('partials.errors')

    <form role="form" action="/brands/update" method="post">
      <input type="hidden" name="id" value="<?= $brand->id ?>"/>
      <div class="form-group">
        <label for="brand-name">Name</label>
        <input type="text" name="brand_name" class="form-control" id="brand-name" value="<?= $brand->brand_name ?>">
      </div>
      <div class="form-group">
        <label for="sender-name">Default Sender Name</label>
        <input type="text" name="brand_sender_name" class="form-control" id="brand-sender-name" value="<?= $brand->brand_sender_name ?>">
      </div>
      <div class="form-group">
        <label for="brand-name">Default Sender Email</label>
        <input type="email" name="brand_sender_email" class="form-control" id="sender-email" value="<?= $brand->brand_sender_email ?>">
      </div>
      <div class="form-group">
        <label for="domain-name">Domain Name</label>
        <input type="text" name="brand_domain_name" class="form-control" id="domain-name" value="<?= $brand->brand_domain_name ?>">
      </div>
      <div class="form-group">
        <label for="unsubscribe-url">Unsubscribe URL</label>
        <input type="text" name="brand_unsubscribe_url" class="form-control" id="unsubscribe-url" value="<?= $brand->brand_unsubscribe_url ?>">
      </div>
      <div class="form-group">
        <label for="brand-logo">Brand Logo</label>
        <input type="file" name="brand_logo" id="brand-logo">
      </div>
      <button type="submit" class="btn btn-default">Submit</button>
    </form>
  </div>
</div>
@stop
