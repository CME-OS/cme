@section('content')
@extends('layouts.setup')

<div class="pull-right"><a href="/setup/skip" class="btn btn-danger">Skip</a></div>
<h1 class="page-header">Step 3:
  <small>Setup Background Processes</small>
</h1>
<div class="container">
<form role="form" action="/setup/install" method="post">
  <div class="row">
    <div class="col-md-12">
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

</form>
</div>

@stop
