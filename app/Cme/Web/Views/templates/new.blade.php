@extends('layouts.default')
@section('content')
<h1 class="page-header">Templates
  <small>Manage your templates</small>
</h1>
  <div class="container">
    <form role="form" action="/templates/add" method="post">
  <div class="row">
    <div class="col-md-12 well">
      <h2>Create a Template</h2>
      <div class="form-group">
        <label for="template-name">Name</label>
        <input type="text" name="name" class="form-control" id="template-name" placeholder="Name">
      </div>
      <div class="form-group">
        <label for="template-content">Content</label>
        <textarea name="content" class="form-control" id="template-content" cols="30" rows="10"></textarea>
      </div>
      <button type="submit" class="btn btn-success">Save</button>
    </div>
  </div>

</form>
</div>
<script src="/assets/ckeditor/ckeditor.js"></script>
<script>
  CKEDITOR.replace(
    'template-content'
  );
</script>
@stop
