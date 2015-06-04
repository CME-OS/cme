@section('content')
    @extends('layouts.setup')

    <div class="pull-right"><a href="/setup/skip"
                               class="btn btn-danger">Skip</a></div>
    <h1 class="page-header">Step 3:
        <small>Setup Background Processes</small>
    </h1>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p>CME needs to run some background processes to keep
                   everything running smoothly. Depending on what process
                   manager you have available you to please copy the config that
                   applies to you into
                   your server</p>

                <h2>Crontab</h2>

                <div>
                    <pre><?= $crontab ?></pre>
                </div>

                <h2>Monit</h2>

                <div>
                    <pre><?= $monit ?></pre>
                </div>
            </div>
        </div>
        <a href="/setup" class="btn btn-lg btn-block btn-success">Finish</a>
    </div>

@stop
