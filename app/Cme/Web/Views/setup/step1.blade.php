@section('content')
@extends('layouts.setup')

<div class="pull-right"><a href="/setup/skip" class="btn btn-danger">Skip</a></div>
<h1 class="page-header">Step 1:
  <small>Requirements Check</small>
</h1>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <?php if(!$installReady): ?>
      <div class="alert alert-danger text-center">Please fix requirements highlighted in red to proceed with installation</div>
      <?php endif; ?>
       <table class="table table-bordered">
         <tr>
           <td>PHP Version >=5.40</td><td class="<?= (PHP_VERSION >= '5.4.0')? 'text-success': 'text-danger' ?>" style="font-weight: bold;"><?= (PHP_VERSION >= '5.4.0')? 'OK ('.PHP_VERSION.')' : PHP_VERSION; ?></td>
         </tr>
         <tr>
           <td>php_mcrypt Module</td><td class="<?= extension_loaded('mcrypt')? 'text-success': 'text-danger' ?>" style="font-weight: bold;"><?= extension_loaded('mcrypt')? 'OK': 'Missing' ?></td>
         </tr>
         <tr>
           <td>php_mbstring Module</td><td class="<?= extension_loaded('mbstring')? 'text-success': 'text-danger' ?>" style="font-weight: bold;"><?= extension_loaded('mbstring')? 'OK': 'Missing' ?></td>
         </tr>
         <tr>
           <td>php_curl Module</td><td class="<?= extension_loaded('curl')? 'text-success': 'text-danger' ?>" style="font-weight: bold;"><?= extension_loaded('curl')? 'OK': 'Missing' ?></td>
         </tr>
         <tr>
           <td>app/storage is writable</td><td class="<?= is_writable(storage_path())? 'text-success': 'text-danger' ?>" style="font-weight: bold;"><?= is_writable(storage_path())? 'OK': 'not writable' ?></td>
         </tr>
       </table>
      <?php if($installReady): ?>
      <a href="/setup/2" class="btn btn-lg btn-block btn-success">Proceed</a>
      <?php else: ?>
        <a href="/setup" class="btn btn-lg btn-block btn-success">Re-Check</a>
      <?php endif; ?>
    </div>
  </div>
</div>

@stop
