@extends('layouts.default')
@section('content')
  <h1 class="page-header">Brands <small>Manage your brands</small></h1>

  <hr/>

  <div class="row">
    <div class="col-sm-12">

      @if($brands)

        <p>
          <a href="/brands/new" class="btn btn-success">
            <span class="glyphicon glyphicon-plus"></span> Add a brand</a>
        </p>
        <br/>
        <table class="table table-striped table-hover">
          <thead>
          <tr>
            <th></th>
            <th></th>
          </tr>
          </thead>
          <?php foreach($brands as $brand): ?>
            <tr style="height:100px; padding:10px;">
              <td width="200">
                <?php if($brand->brandLogo != ""): ?>
                <div style="width:100px; height:50px; overflow:hidden; padding:10px 0;">
                  <img src="<?= $brand->brandLogo ?>" alt="" width="100%"/>
                </div>
                <?php endif; ?>
                <a href="{{ URL::route('brands.campaigns', $brand->id) }}">
                  <strong><?= $brand->brandName; ?></strong>
                </a>
              </td>
              <td>
                <div class="pull-right">
                <a href="{{ URL::route('brands.edit', $brand->id) }}" class="btn btn-default" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="{{ URL::route('brands.delete', $brand->id) }}" class="btn btn-danger" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>

      @else
        <div class="alert alert-info">
          <p>You do not have any brands in CME. <a href="{{ URL::route('brands.new') }}">Add your first brand now</a></p>
        </div>
      @endif

    </div>
  </div>
@stop
