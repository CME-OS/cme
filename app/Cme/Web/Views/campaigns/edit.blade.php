@extends('layouts.default')
@section('content')
    <link rel="stylesheet"
          href="/assets/datetimepicker/css/datetimepicker.min.css"/>

    <div class="row">
        <div class="col-sm-12">
            <h1>Edit Campaign
                <small>{{ $campaign->subject }}</small>
            </h1>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">

            {{ Form::model($campaign, ['route' => 'campaigns.update.post', 'id' => 'campaign-form']) }}

            <input type="hidden" name="id" value="<?= $campaign->id ?>"/>

            <div class="row">
                <div class="col-md-8">

                    <div class='form-group'>
                        {{ Form::label('subject', 'Subject') }}
                        {{ Form::text('subject', null, ['class' => 'form-control']) }}
                    </div>

                    <div class='form-group'>
                        {{ Form::label('html_content', 'Message') }}
                        {{ Form::textarea('html_content', null, ['class' => 'form-control', 'id' => 'campaign-message']) }}
                    </div>
                </div>

                <div class="col-md-4">

                    <div class="form-group">
                        <label for="campaign-type">What type of campaign is this?</label>
                        <select name="type" id="campaign-type" class="form-control">
                            <option value="default">One Off - Send campaign once</option>
                            <option value="rolling">Rolling - An ongoing campaign sent on a regular interval</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="campaign-brand-id">Which Brand is this
                                                       campaign for?</label>
                        <select name="brand_id" id="campaign-brand-id"
                                class="form-control">
                            <option value="">SELECT</option>
                            <?php foreach($brands as $brand): ?>
                            <option value="<?= $brand->id ?>" <?= ($brand->id == $campaign->brand_id) ? 'selected="selected"' : ''; ?>><?= $brand->brand_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="campaign-from">Send Campaign As:</label>
                        <input type="text" name="from" class="form-control"
                               id="campaign-from"
                               placeholder="<name> email@domain.com"
                               value="<?= $campaign->from; ?>">
                    </div>


                    <div class="form-group">
                        <label for="campaign-list-id">Which list should campaign
                                                      be sent to?</label>
                        <select name="list_id" id="campaign-list-id"
                                class="form-control">
                            <option value="">SELECT</option>
                            <?php foreach($lists as $list): ?>
                            <option value="<?= $list->id ?>" <?= ($list->id == $campaign->list_id) ? 'selected="selected"' : ''; ?>><?= $list->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" id="campaign-target-div" <?= (!$campaign->list_id)? 'style="display: none;"' : '' ?>>
                        <label for="campaign-target">Who do you want to?</label>
                        <select name="target" id="campaign-target" class="form-control">
                            <option value="all">Send to all subscribers in list</option>
                            <option value="custom" <?= ($campaign->filters)? 'selected="selected"' : '' ?>>Send to a subset</option>
                        </select>
                    </div>
                    <div class="campaign-custom-target" style="<?= ($campaign->filters == null)? 'display: none;' : '' ?>padding-bottom: 20px;">
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
                                    <select name="filters[filter_field][]" class="filter-field" style="width:100px;">
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
                                    <select name="filters[filter_value][]" class="filter-value" style="width:100px;">
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

                    <div class="form-group">
                        <label for="campaign-priority">Send Priority:</label>
                        <select name="send_priority" id="campaign-priority"
                                class="form-control">
                            <option value="2">Normal</option>
                            <option value="1">Low</option>
                            <option value="3">Medium</option>
                            <option value="4">High</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="campaign-send-time">When do you want to send
                                                        this campaign?</label>

                        <div id="datetimepicker" class="input-group date">
                            <input type="text" name="send_time"
                                   class="form-control" id="campaign-send-time"
                                   value="<?= $campaign->send_time ?>">
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-time"></span>
                  </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="campaign-smtp-provider">SMTP Provider:</label>
                        <select name="smtp_provider_id" id="campaign-smtp-provider" class="form-control">
                            <option value="0" <?= ($campaign->smtp_provider_id == 0)? 'selected="selected"' : '' ?>>Use Default (AWS 1)</option>
                            <option value="1" <?= ($campaign->smtp_provider_id == 1)? 'selected="selected"' : '' ?>>AWS 1</option>
                            <option value="2" <?= ($campaign->smtp_provider_id == 2)? 'selected="selected"' : '' ?>>SendGrid</option>
                        </select>
                    </div>

                    <!--<div class="well">
                        <p><strong>Available PlaceHolders</strong></p>
                        <div class="placeholders" id="placeholders">
                            <ul>
                                <?php foreach($placeholders as $placeholder): ?>
                                <li>[<?= $placeholder ?>]</li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div> -->

                    <button type="submit" class="btn btn-success">Update</button>

                </div>
            </div>

            {{ Form::close() }}

            <div class="filter-template" style="display:none;">
                <table>
                    <tr class="template-row" data-row-id="1">
                        <td>
                            <select name="filters[filter_field][]" class="filter-field" style="width: 100px;">
                            </select>
                        </td>
                        <td>
                            <select name="filters[filter_operator][]" class="filter-operator" style="display: none; width: 100px;;">
                            </select>
                        </td>
                        <td>
                            <select name="filters[filter_value][]" class="filter-value" style="display: none; width:100px;">
                            </select>
                        </td>
                        <td>
                            <p class="btn remove-filter"><i class="glyphicon glyphicon-trash"></i></p>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>

    </div>
    </div>
    <div class="clearfix"></div>
    <script src="/assets/ckeditor/ckeditor.js"></script>
    <script>CKEDITOR.replace('campaign-message');</script>
@stop
