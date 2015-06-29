@extends('layouts.default')
@section('content')
  <h1 class="page-header">Templates <small>Manage your templates</small></h1>

  <hr>

  <div class="row">
    <div class="col-sm-12">

      @if($templates)
        <p><a href="/templates/new" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Create a Template</a></p>

        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Name</th>
              <th></th>
            </tr>
          </thead>

          <?php foreach($templates as $template): ?>
            <tr>
              <td><a href="{{ URL::route('template.preview', $template->id) }}">
                  <strong><?= $template->name; ?></strong>
                </a>
              </td>
              <td>
                <div class="pull-right">
                <a href="{{ URL::route('template.edit', $template->id) }}" class="btn btn-default" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="{{ URL::route('template.delete', $template->id) }}" class="btn btn-danger" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>

      @else
        <div class="alert alert-info">
          <p>You do not have any templates in CME. <a href="/templates/new">Create your first template now</a></p>
        </div>
      @endif

    </div>
  </div>
@stop
