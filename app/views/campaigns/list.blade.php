@extends('layouts.default')
@section('content')
  <h1>Campaigns <small>Manage your campaigns</small></h1>

  <hr>

  <div class="row">
    <div class="col-sm-12">

      @if($campaigns)
        <p><a href="/campaigns/new">Create a Campaign</a></p>

        <table class="table table-striped">
          <thead>
            <tr>
              <th>Subject</th>
              <th>Brand</th>
              <th>Status</th>
              <th>Created</th>
              <th></th>
            </tr>
          </thead>

          <?php foreach($campaigns as $campaign): ?>
            <tr>
              <td><a href="{{ URL::route('campaign.edit', $campaign->id) }}"><?= $campaign->subject; ?></a></td>
              <td><?= $campaign->brand->brand_name; ?></td>
              <td><?= $campaign->status; ?></td>
              <td><?= date('d/m/Y H:i:s', $campaign->created); ?></td>
              <td>
                <a href="{{ URL::route('campaign.edit', $campaign->id) }}">Edit</a> |
                <a href="{{ URL::route('campaign.preview', $campaign->id) }}">Preview</a> |
                <a href="{{ URL::route('campaign.delete', $campaign->id) }}">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>

      @else
        <div class="alert alert-info">
          <p>You do not have any campaigns in CME. <a href="/campaigns/new">Create your first campaign now</a></p>
        </div>
      @endif

    </div>
  </div>
@stop
