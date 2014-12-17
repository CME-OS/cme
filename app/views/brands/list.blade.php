@extends('layouts.default')
@section('content')
  <h1>Brands <small>Manage your brands</small></h1>

  <hr/>
  
  <div class="row">
    <div class="col-sm-12">

      @if($brands)

        <p><a href="/brands/new">Add a brand</a></p>
        <br/>
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Name</th>
            <th></th>
          </tr>
          </thead>
          <?php foreach($brands as $brand): ?>
            <tr>
              <td><?= $brand->brand_name; ?></td>
              <td><a href="/brands/campaigns/<?= $brand->id; ?>">View Campaigns</a></td>
            </tr>
          <?php endforeach; ?>
        </table>

      @else
        <div class="alert alert-info">
          <p>You do not have any brands in CME. <a href="/brands/new">Add your first brand now</a></p>
        </div>
      @endif

    </div>
  </div>
@stop
