@extends('layouts.default')
@section('content')
<h1 class="page-header">Lists
  <small>Manage your lists</small>
</h1>
<div class="container">
<div class="row">
  <div class="col-md-12 well">
    <h2>Add a List</h2>
    @include('lists.addform')
  </div>
</div>
</div>
@stop
