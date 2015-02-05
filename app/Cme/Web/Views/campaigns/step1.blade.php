@extends('layouts.default')
@section('content')
<h1 class="page-header">Campaigns
  <small>Manage your campaigns</small>
</h1>
<form role="form" action="/campaigns/new" method="post">
  <input type="hidden" name="step" value="1"/>
  <h2>Step 1: Define Campaign</h2>

  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="campaign-type">What type of campaign is this?</label>
        <select name="type" id="campaign-type" class="form-control">
          <option value="default">One Off - Send campaign once</option>
          <option value="rolling">Rolling - An ongoing campaign sent on a regular interval</option>
        </select>
      </div>
      <div class="form-group">
        <label for="campaign-subject">What is the subject of this campaign?</label>
        <input type="text" name="subject" class="form-control" id="campaign-subject">
      </div>
      <div class="form-group">
        <label for="campaign-brand-id">Which Brand is this campaign for?</label>
        <select name="brand_id" id="campaign-brand-id" class="form-control">
          <option value="">SELECT</option>
          <?php foreach($brands as $brand): ?>
            <option value="<?= $brand->id ?>"><?= $brand->brand_name; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="campaign-from">Send Campaign As:</label>
        <input type="text" name="from" class="form-control" id="campaign-from" placeholder="<name> email@domain.com">
      </div>
      <div class="form-group">
        <label for="campaign-list-id">Which list should campaign be sent to?</label>
        <select name="list_id" id="campaign-list-id" class="form-control">
          <option value="">SELECT</option>
          <?php foreach($lists as $list): ?>
            <option value="<?= $list->id ?>"><?= $list->name; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" id="campaign-target-div" style="display: none;">
        <label for="campaign-target">Who do you want to?</label>
        <select name="target" id="campaign-target" class="form-control">
          <option value="all">Send to all subscribers in list</option>
          <option value="custom">Send to a subset</option>
        </select>
      </div>
      <div class="campaign-custom-target" style="display:none; min-height: 200px;">
        <a href="#" id="add-filter">Add new Filter</a>
          <table class="table" id="filter-table">
            <tr>
              <td>Field</td>
              <td>Condition</td>
              <td>Value</td>
              <td></td>
            </tr>
            <tr class="filter-row template-row" data-row-id="1">
              <td>
                <select name="filter_field[]" class="filter-field">
                </select>
              </td>
              <td>
                <select name="filter_operator[]" class="filter-operator" style="display: none;">
                </select>
              </td>
              <td>
                <select name="filter_value[]" class="filter-value" style="display: none;">
                </select>
              </td>
              <td>
                <p class="btn remove-filter"><i class="glyphicon-trash"></i></p>
              </td>
            </tr>
          </table>
      </div>

    </div>
  </div>
  <button type="submit" class="btn btn-default">Next</button>
</form>

@stop
