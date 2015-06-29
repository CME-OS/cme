@extends('layouts.default')
@section('content')
  <h1 class="page-header">SMTP Providers <small>Manage your SMTP Providers</small></h1>

  <hr/>

  <div class="row">
    <div class="col-sm-12">

      @if($smtpProviders)

        <p><a href="/smtp-providers/new" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add an SMTP Provider</a></p>
        <br/>
        <table class="table table-striped table-hover">
          <thead>
          <tr>
            <th>Name</th>
            <th>Host</th>
            <th>Port</th>
            <th>Default</th>
            <th></th>
          </tr>
          </thead>
          <?php foreach($smtpProviders as $provider): ?>
            <tr>
              <td><?= $provider->name; ?></td>
              <td><?= $provider->host; ?></td>
              <td><?= $provider->port; ?></td>
              <td><?= ($provider->default)? 'Yes' : 'No'; ?></td>
              <td>
                <div class="pull-right">
                <?php if(!$provider->default): ?>
                  <a href="{{ URL::route('smtp-providers.default', $provider->id) }}" class="btn btn-info" title="Make Default"><span class="glyphicon glyphicon-check"></span></a>
                <?php endif; ?>
                  <a href="{{ URL::route('smtp-providers.edit', $provider->id) }}" class="btn btn-default" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a>
                  <a href="{{ URL::route('smtp-providers.delete', $provider->id) }}" class="btn btn-danger" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>

      @else
        <div class="alert alert-info">
          <p>You do not have any SMTP Providers in CME. <a href="{{ URL::route('smtp-providers.new') }}">Add your first SMTP Provider now</a></p>
        </div>
      @endif

    </div>
  </div>
@stop
