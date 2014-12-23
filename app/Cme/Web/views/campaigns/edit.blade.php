@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="/assets/datetimepicker/css/datetimepicker.min.css"/>

<div class="row">
  <div class="col-sm-12">
    <h1>Edit Campaign <small>{{ $campaign->subject }}</small></h1>
    <hr>

    <form action="/campaigns/send" id="send-form">
      <input type="hidden" name="id" value="<?= $campaign->id ?>"/>
    </form>

    <div class="well">
      <div class="row">
          <div class="col-md-4 col-md-offset-8">
            <div class="row">
               <div class="col-md-6">
                 <div class="btn-group pull-right">
                   <button type="button" class="btn btn-default" id="clone-campaign-btn">Clone</button>
                   <button type="button" class="btn btn-success" id="save-campaign-btn">Save</button>
                   <button type="button" class="btn btn-danger" id="send-campaign-btn ">Send</button>
                 </div>
               </div>
              <div class="col-md-6">
                <form action="/campaigns/test" class="form-inline" method="post">
                  <input type="hidden" name="id" value="<?= $campaign->id ?>"/>
                  <input type="text" name="test_email" class="form-control" id="campaign-test" placeholder="Enter email to send test to">
                  <input type="submit" class="btn btn-primary" value="Test"/>
                </form>
              </div>
            </div>

          </div>
        </div>
        </div>
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
                <label for="campaign-brand-id">Which Brand is this campaign for?</label>
                <select name="brand_id" id="campaign-brand-id" class="form-control">
                  <option value="">SELECT</option>
                  <?php foreach($brands as $brand): ?>
                    <option value="<?= $brand->id ?>" <?= ($brand->id == $campaign->brand_id) ? 'selected="selected"' : ''; ?>><?= $brand->brand_name; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label for="campaign-list-id">Which list should campaign be sent to?</label>
                <select name="list_id" id="campaign-list-id" class="form-control">
                  <option value="">SELECT</option>
                  <?php foreach($lists as $list): ?>
                    <option value="<?= $list->id ?>" <?= ($list->id == $campaign->list_id) ? 'selected="selected"' : ''; ?>><?= $list->name; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label for="campaign-from">Send Campaign As:</label>
                <input type="text" name="from" class="form-control" id="campaign-from" placeholder="<name> email@domain.com" value="<?= $campaign->from; ?>">
              </div>

              <div class="form-group">
                <label for="campaign-priority">Send Priority:</label>
                <select name="send_priority" id="campaign-priority" class="form-control">
                  <option value="2">Normal</option>
                  <option value="1">Low</option>
                  <option value="3">Medium</option>
                  <option value="4">High</option>
                </select>
              </div>

              <div class="form-group">
                <label for="campaign-send-time">When do you want to send this campaign?</label>
                <div id="datetimepicker" class="input-group date">
                  <input type="text" name="send_time" class="form-control" id="campaign-send-time" value="<?= $campaign->send_time ?>">
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-time"></span>
                  </span>
                </div>
              </div>

              <div class="well">
                <p><strong>Available PlaceHolders</strong></p>
                <div class="placeholders"></div>
              </div>

            </div>
          </div>

        {{ Form::close() }}
      </div>
    </div>

  </div>
</div>
<div class="clearfix"></div>
<script src="/assets/ckeditor/ckeditor.js"></script>
<script>CKEDITOR.replace('campaign-message');</script>
@stop
