@extends('layouts.default')
@section('content')
  <h1>Campaigns <small>Manage your campaigns</small></h1>

  <hr>

  <div class="row">
    <div class="col-sm-12">

      @if($campaigns)
        <p><a href="/campaigns/new" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Create a Campaign</a></p>

        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Subject</th>
              <th>List</th>
              <th>Brand</th>
              <th>Status</th>
              <th>Created</th>
              <th></th>
            </tr>
          </thead>

          <?php foreach($campaigns as $campaign): ?>
            <tr>
              <td>
                <span class="glyphicon glyphicon-envelope" style="color:royalblue;"></span>
                <strong><a href="{{ URL::route('campaign.preview', $campaign->id) }}"><?= $campaign->subject; ?></a></strong>
              </td>
              <td><?= $campaign->lists->name; ?></td>
              <td><?= $campaign->brand->brand_name; ?></td>
              <td><?= $campaign->status; ?></td>
              <td><?= date('d M Y H:i:A', $campaign->created); ?></td>
              <td>
                <div class="pull-right">
                  {{--<a href="{{ URL::route('campaign.preview', $campaign->id) }}" class="btn btn-default">Preview</a>--}}
                  <a href="{{ URL::route('campaign.copy', $campaign->id) }}" class="btn btn-default"><span class="glyphicon glyphicon-duplicate"></span></a>
                  <a href="{{ URL::route('campaign.edit', $campaign->id) }}" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="{{ URL::route('campaign.delete', $campaign->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a>
                </div>
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
