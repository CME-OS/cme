@extends('layouts.default')
@section('content')
<h1 class="page-header">Templates
  <small>Manage your templates</small>
</h1>
<form role="form" action="/templates/add" method="post">
  <h2>Create a Template</h2>

  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for="template-name">Name</label>
        <input type="text" name="name" class="form-control" id="template-name" placeholder="Name">
      </div>
      <div class="form-group">
        <label for="template-content">Content</label>
        <textarea name="content" class="form-control" id="template-content" cols="30" rows="10"></textarea>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-default">Save</button>
</form>
<script src="/assets/ckeditor/ckeditor.js"></script>
<script>
  CKEDITOR.replace(
    'template-content'
  );
</script>
@stop
