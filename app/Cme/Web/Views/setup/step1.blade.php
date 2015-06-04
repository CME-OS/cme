@section('content')
@extends('layouts.setup')

<div class="pull-right"><a href="/setup/skip" class="btn btn-danger">Skip</a></div>
<h1 class="page-header">Step 1:
  <small>Requirements Check</small>
</h1>
<div class="container">
<form role="form" action="/setup/install" method="post">
  <div class="row">
    <div class="col-md-12">
       <table class="table table-bordered">
         <tr>
           <td>PHP Version >=5.40</td><td><?= (PHP_VERSION >= '5.4.0')? 'Ok' : PHP_VERSION; ?></td>
         </tr>
         <tr>
           <td>php_mcrypt Module</td><td><?= extension_loaded('mcrypt')? 'available': 'missing' ?></td>
         </tr>
         <tr>
           <td>php_mbstring Module</td><td><?= extension_loaded('mbstring')? 'available': 'missing' ?></td>
         </tr>
         <tr>
           <td>php_curl Module</td><td><?= extension_loaded('curl')? 'available': 'missing' ?></td>
         </tr>
         <tr>
           <td>app/storage is writable</td><td><?= is_writable(storage_path())? 'writable': 'not writable' ?></td>
         </tr>
       </table>
      <?php if($installReady): ?>
      <button type="submit" class="btn btn-lg btn-block btn-success">Proceed</button>
      <?php else: ?>
        <div class="alert alert-danger text-center">Please fix requirements highlighted in red to proceed with installation</div>
      <?php endif; ?>
    </div>
  </div>

</form>
</div>

@stop
