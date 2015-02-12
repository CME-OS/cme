@extends('layouts.default')
@section('content')
    <link rel="stylesheet"
          href="/assets/datetimepicker/css/datetimepicker.min.css"/>
    <h1 class="page-header"><?= $campaign->subject ?>
        <small><a href="/campaigns/edit/<?= $campaign->id ?>">edit</a></small>
    </h1>
    <div class="row">
        <div class="col-md-8">
            <div class="well" style="min-height:800px;">
                <iframe src="/campaigns/content/<?= $campaign->id ?>"
                        frameborder="0" width="100%"
                        height="800px;"></iframe>
            </div>
        </div>
        <div class="col-md-4">
            <p style="font-size:16px;">Status: <?= $campaign->status ?></p>
            <?php if($campaign->status == 'Sending'): ?>
            <p>Sent so far: 50</p>
            <?php endif; ?>

            <div style="margin-bottom: 10px;">
                <form action="/campaigns/test" class="form-inline"
                      method="post">
                    <input type="hidden" name="id"
                           value="<?= $campaign->id ?>"/>

                    <div class="form-group">
                        <input type="text" name="test_email"
                               class="form-control" id="campaign-test"
                               placeholder="Enter email to send test to">
                    </div>
                    <button type="submit" class="btn btn-primary">Test
                    </button>
                </form>
            </div>
            <?php if(in_array($campaign->status, ['Pending', 'Aborted']) && $campaign->tested > 0): ?>
            <form action="/campaigns/send" class="form-inline"
                  method="post">
                <input type="hidden" name="id"
                       value="<?= $campaign->id ?>"/>
                <button type="submit" class="btn btn-success">Queue Campaign
                </button>
            </form>
            <?php endif; ?>
            <?php if(in_array($campaign->status, ['Queued', 'Sending', 'Paused'])): ?>
            <?php $action = ($campaign->status == 'Paused')? 'resume' : 'pause'; ?>
            <p><form action="/campaigns/<?= $action ?>" class="form-inline"
                  method="post">
                <input type="hidden" name="id"
                       value="<?= $campaign->id ?>"/>
                <button type="submit" class="btn btn-warning"><?= ucwords($action) ?> Campaign
                </button>
            </form>
            </p>
            <p>
            <form action="/campaigns/abort" class="form-inline"
                  method="post">
                <input type="hidden" name="id"
                       value="<?= $campaign->id ?>"/>
                <button type="submit" class="btn btn-danger">Abort Campaign
                </button>
            </form>
            </p>
            <?php endif; ?>
            <hr/>

            <div style="width:350px;">
                <table class="table">
                    <tr>
                        <!-- if within 24hours be clever with time (e.g in 15 mins) -->
                        <td>Send Time:</td>
                        <td><?= date('d/M/Y H:i:s', $campaign->send_time) ?></td>
                    </tr>
                    <tr>
                        <td>Recipients:</td>
                        <td><?= \Cme\Helpers\ListHelper::count($campaign->list_id) ?>
                            /<?= \Cme\Helpers\ListHelper::count($campaign->list_id) ?></td>
                    </tr>
                    <tr>
                        <td>Brand:</td>
                        <td><?= $campaign->brand->brand_name; ?></td>
                    </tr>
                    <tr>
                        <td>List:</td>
                        <td><?= $campaign->lists->name; ?></td>
                    </tr>
                    <tr>
                        <td>Campaign Type:</td>
                        <td><?= $campaign->type ?></td>
                    </tr>
                    <?php if($campaign->type == 'rolling'): ?>
                    <tr>
                        <td>Frequency:</td>
                        <td>Every <?= $campaign->frequency ?> days</td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Priority:</td>
                        <td><?php echo $campaign->send_priority ?></td>
                    </tr>
                    <tr>
                        <td>SMTP Provider:</td>
                        <td><?php echo $campaign->smtp_provider_id ?></td>
                    </tr>
                </table>
            </div>

            <?php if($campaign->filters): ?>
            <div style="width: 350px;">
                <?php $filters = json_decode($campaign->filters); ?>
                <?php $filtersCount = count($filters->filter_field); ?>
                <table class="table">
                    <tr>
                        <th>This campaign will be sent to subscribers that meet the following conditions</th>
                    </tr>
                    <?php for($i = 0; $i < $filtersCount; $i++): ?>
                    <tr>
                        <?php
                        $field = $filters->filter_field[$i];
                        $operator = $filters->filter_operator[$i];
                        $value = $filters->filter_value[$i];
                        ?>
                        <td><?= ($i + 1) . '. ' . $field . ' ' . $operator . ' <u>' . $value . '</u>' ?></td>
                    </tr>
                    <?php endfor; ?>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
@stop
