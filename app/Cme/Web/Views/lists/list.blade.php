@extends('layouts.default')
@section('content')
  <h1 class="page-header">Lists <small>Manage your lists</small></h1>
  <div class="row">
    <div class="col-md-12">
      <?php if($lists): ?>
        <p><a href="/lists/new" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add a list</a></p>
        <table class="table table-striped table-hover">
          <thead>
          <tr>
            <th>Name</th>
            <th></th>
          </tr>
          </thead>
          <?php foreach($lists as $list): ?>
            <tr>
              <td>
                <span class="glyphicon glyphicon-list-alt" style="color:mediumseagreen;"></span>
                <a href="{{ URL::route('lists.view', $list->id) }}">
                  <strong><?= $list->name; ?></strong>
                </a> (<?= number_format($list->size, 0); ?>)
              </td>
              <td>
                <div class="pull-right">
                <a href="{{ URL::route('lists.new-subscriber', $list->id) }}" class="btn btn-default" title="Add Subscriber"><span class="glyphicon glyphicon-plus"></span></a>
                <a href="{{ URL::route('lists.edit', $list->id) }}" class="btn btn-default" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="{{ URL::route('lists.delete', $list->id) }}" class="btn btn-danger" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>

      <?php else: ?>
        <div class="alert alert-info">
          <p>You do not have any lists in CME. <a href="{{ URL::route('lists.new') }}">Add your first List now</a></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
@stop
