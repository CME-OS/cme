@section('content')
@extends('layouts.default')
<h1 class="page-header">Step 1:
  <small>Define Campaign</small>
</h1>
<div class="container">
<form role="form" action="/campaigns/new" method="post">
  <input type="hidden" name="step" value="2"/>
  <div class="row">
    <div class="col-md-12 well">
      <div class="form-group <?= isset($errors['type'])? 'has-error has-feedback': '' ?>">
        <label for="campaign-type">What type of campaign is this? <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['type'])? ' - '.$errors['type']->message: '' ?></span></label>
        <select name="type" id="campaign-type" class="form-control">
          <option value="default">One Off - Send campaign once</option>
          <option value="rolling">Rolling - An ongoing campaign sent on a regular interval</option>
        </select>
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['type'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['name'])? 'has-error has-feedback': '' ?>">
        <label for="campaign-name">What is the name of this campaign? <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['name'])? ' - '.$errors['name']->message: '' ?></span></label>
        <input type="text" name="name" class="form-control" id="campaign-name" value="<?= isset($input['name'])? $input['name'] : $campaign->name ?>">
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['name'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['subject'])? 'has-error has-feedback': '' ?>">
        <label for="campaign-subject">What is the subject of this campaign? <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['subject'])? ' - '.$errors['subject']->message: '' ?></span></label>
        <input type="text" name="subject" class="form-control" id="campaign-subject" value="<?= isset($input['subject'])? $input['subject'] : $campaign->subject ?>">
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['subject'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['brand_id'])? 'has-error has-feedback': '' ?>">
        <label for="campaign-brand-id">Which Brand is this campaign for? <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['brand_id'])? ' - '.$errors['brand_id']->message: '' ?></span></label>
        <div class="pull-right"><strong><a href="#" data-toggle="modal" data-target="#addBrandModal">Add a brand</a></strong></div>
        <select name="brand_id" id="campaign-brand-id" class="form-control">
          <option value="">SELECT</option>
          <?php foreach($brands as $brand): ?>
            <option value="<?= $brand->id ?>" <?= ($brand->id == (isset($input['brand_id'])? $input['brand_id'] : $campaign->brandId))? 'selected="selected"' : '' ?>>
              <?= $brand->brandName; ?>
            </option>
          <?php endforeach; ?>
        </select>
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['brand_id'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['from'])? 'has-error has-feedback': '' ?>">
        <label for="campaign-from">Send Campaign As: <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['from'])? ' - '.$errors['from']->message: '' ?></span></label>
        <input type="text" name="from" class="form-control" id="campaign-from" placeholder="<name> email@domain.com" value="<?= isset($input['from'])? $input['from'] : $campaign->from ?>">
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['from'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group <?= isset($errors['list_id'])? 'has-error has-feedback': '' ?>">
        <label for="campaign-list-id">Which list should campaign be sent to?</label>
        <div class="pull-right"><strong><a href="#" data-toggle="modal" data-target="#addListModal">Add a list</a></strong></div>
        <select name="list_id" id="campaign-list-id" class="form-control">
          <option value="">SELECT</option>
          <?php foreach($lists as $list): ?>
            <option value="<?= $list->id ?>" <?= ($list->id == (isset($input['list_id'])? $input['list_id'] : $campaign->listId))? 'selected="selected"' : '' ?>>
              <?= $list->name; ?>
            </option>
          <?php endforeach; ?>
        </select>
        <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['list_id'])? '': 'hidden' ?>" aria-hidden="true"></span>
      </div>
      <div class="form-group" id="campaign-target-div" <?= (!$campaign->listId)? 'style="display: none;"' : '' ?>>
        <label for="campaign-target">Who do you want to?</label>
        <select name="target" id="campaign-target" class="form-control">
          <option value="all">Send to all subscribers in list</option>
          <option value="custom" <?= ($campaign->filters)? 'selected="selected"' : '' ?>>Send to a subset</option>
        </select>
      </div>
      <div class="campaign-custom-target" style="<?= ($campaign->filters == null)? 'display: none;' : '' ?>min-height: 200px;">
        <a href="#" id="add-filter">Add new Filter</a>
          <table class="table" id="filter-table">
            <tr>
              <td>Field</td>
              <td>Condition</td>
              <td>Value</td>
              <td></td>
            </tr>
            <?php if($campaign->filters): ?>
            <?php $filters = json_decode($campaign->filters); ?>
            <?php $filtersCount = count($filters->filter_field); ?>
            <?php for($i = 0; $i < $filtersCount; $i++): ?>
            <?php $field = $filters->filter_field[$i]; ?>
            <?php $operator = $filters->filter_operator[$i]; ?>
            <?php $value = $filters->filter_value[$i]; ?>
            <tr class="filter-row" data-row-id="<?= $i+1; ?>">
              <td>
                <select name="filters[filter_field][]" class="filter-field">
                  <option value="">Select</option>
                  <?php foreach($filterData['columns'] as $column): ?>
                     <option value="<?= $column['value'] ?>" <?= ($field == $column['value'])? 'selected="selected"' : '' ?>><?= $column['text'] ?></option>
                  <?php endforeach ?>
                </select>
              </td>
              <td>
                <select name="filters[filter_operator][]" class="filter-operator">
                  <?php foreach($filterData['operators'][$field] as $fieldOperator): ?>
                  <option value="<?= $fieldOperator['value'] ?>" <?= ($operator == $fieldOperator['value'])? 'selected="selected"' : '' ?>><?= $fieldOperator['text'] ?></option>
                  <?php endforeach ?>
                </select>
              </td>
              <td>
                <select name="filters[filter_value][]" class="filter-value">
                  <?php foreach($filterData['values'][$field] as $fieldValue): ?>
                  <option value="<?= $fieldValue['value'] ?>" <?= ($value == $fieldValue['value'])? 'selected="selected"' : '' ?>><?= $fieldValue['text'] ?></option>
                  <?php endforeach ?>
                </select>
              </td>
              <td>
                <p class="btn remove-filter"><i class="glyphicon glyphicon-trash"></i></p>
              </td>
            </tr>
            <?php endfor; ?>
            <?php endif; ?>
          </table>
      </div>
      <button type="submit" class="btn btn-success">Next</button>
    </div>
  </div>
</form>
  <div class="filter-template" style="display:none;">
    <table>
      <tr class="template-row" data-row-id="1">
        <td>
          <select name="filters[filter_field][]" class="filter-field">
          </select>
        </td>
        <td>
          <select name="filters[filter_operator][]" class="filter-operator" style="display: none;">
          </select>
        </td>
        <td>
          <select name="filters[filter_value][]" class="filter-value" style="display: none;">
          </select>
        </td>
        <td>
          <p class="btn remove-filter"><i class="glyphicon glyphicon-trash"></i></p>
        </td>
      </tr>
    </table>
  </div>
</div>


<!--- modals -->

<div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">New Brand</h4>
      </div>
      <div class="modal-body">
        @include('brands.addform')
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addListModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">New List</h4>
      </div>
      <div class="modal-body">
        @include('lists.addform')
      </div>
    </div>
  </div>
</div>

@stop
