<?php
/**
 * Created by PhpStorm.
 * User: Okechukwu
 * Date: 6/5/2015
 * Time: 6:30 PM
 */
require __DIR__.'/../vendor/autoload.php';

use Cme\Helpers\InstallerHelper;

$app = new Illuminate\Foundation\Application;
$app->bindInstallPaths(require __DIR__.'/../bootstrap/paths.php');
$app->instance('app', $app);
\Illuminate\Support\Facades\Facade::clearResolvedInstances();
\Illuminate\Support\Facades\Facade::setFacadeApplication($app);

$installReady = InstallerHelper::hostMeetsRequirements();
?>

<!doctype html>
<html>
<head>
  <meta charset='utf-8'>
  <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Campaigns Made Easy</title>
  <link rel="stylesheet" href="/assets/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="/assets/css/built/theme.min.css"/>
  <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="/assets/css/cme.css"/>
  <script src="/assets/js/jquery.min.js"></script>


</head>
<body>
<div class="container">
  <div style="margin-top:30px;">
    <img src="/assets/img/logo.png" alt="" height="50px;" style="margin-left:-20px;" />
  </div>


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
            <td><?= base_path() ?> is writable</td><td class="<?= is_writable(base_path())? 'text-success': 'text-danger' ?>" style="font-weight: bold;"><?= is_writable(base_path())? 'OK': 'not writable' ?></td>
          </tr>
          <tr>
            <td><?= storage_path() ?> is writable</td><td class="<?= is_writable(storage_path())? 'text-success': 'text-danger' ?>" style="font-weight: bold;"><?= is_writable(storage_path())? 'OK': 'not writable' ?></td>
          </tr>
        </table>
        <?php if($installReady): ?>
          <a href="/setup/2" class="btn btn-lg btn-block btn-success">Proceed</a>
        <?php else: ?>
          <a href="/" class="btn btn-lg btn-block btn-success">Re-Check</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>
</body>

