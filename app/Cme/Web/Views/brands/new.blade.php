@extends('layouts.default')
@section('content')
<h1 class="page-header">Brands
  <small>Manage your brands</small>
</h1>
<div class="container">
<div class="row">
  <div class="col-md-12 well">
    <h2>Add a Brand</h2>
    @include('brands.addform')
  </div>
</div>
</div>
@stop
