@extends('layouts.default')
@section('content')
  <h1>SMTP Providers <small>Manage your SMTP Providers</small></h1>

  <hr/>

  <div class="row">
    <div class="col-sm-12">

      @if($smtpProviders)

        <p><a href="/smtp-providers/new">Add an SMTP Provider</a></p>
        <br/>
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Name</th>
            <th>Host</th>
            <th>Username</th>
            <th>Port</th>
            <th>Default</th>
            <th></th>
          </tr>
          </thead>
          <?php foreach($smtpProviders as $provider): ?>
            <tr>
              <td><?= $provider->name; ?></td>
              <td><?= $provider->host; ?></td>
              <td><?= $provider->username; ?></td>
              <td><?= $provider->port; ?></td>
              <td><?= ($provider->default)? 'Yes' : 'No'; ?></td>
              <td>
                <div class="pull-right">
                <a href="{{ URL::route('smtp-providers.edit', $provider->id) }}" class="btn btn-default">Edit</a>
                <?php if(!$provider->default): ?>
                <a href="{{ URL::route('smtp-providers.default', $provider->id) }}" class="btn btn-default">Make Default</a>
                <?php endif; ?>
                <a href="{{ URL::route('smtp-providers.delete', $provider->id) }}" class="btn btn-default">Delete</a>
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
