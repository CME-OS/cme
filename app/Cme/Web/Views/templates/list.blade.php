@extends('layouts.default')
@section('content')
  <h1>Templates <small>Manage your templates</small></h1>

  <hr>

  <div class="row">
    <div class="col-sm-12">

      @if($templates)
        <p><a href="/templates/new">Create a Template</a></p>

        <table class="table table-striped">
          <thead>
            <tr>
              <th>Name</th>
              <th>Created</th>
              <th></th>
            </tr>
          </thead>

          <?php foreach($templates as $template): ?>
            <tr>
              <td><a href="{{ URL::route('template.preview', $template->id) }}"><?= $template->name; ?></a></td>
              <td><?= date('d/m/Y H:i:s', $template->created); ?></td>
              <td>
                <div class="pull-right">
                <a href="{{ URL::route('template.edit', $template->id) }}" class="btn btn-default">Edit</a>
                <a href="{{ URL::route('template.preview', $template->id) }}" class="btn btn-default">Preview</a>
                <a href="{{ URL::route('template.delete', $template->id) }}" class="btn btn-default">Delete</a>
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
