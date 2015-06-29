<!doctype html>
<html>
<head>
  @include('partials.head')
</head>
<body>
@include('partials.header')
<div class="content container-fluid">
  <?php if($state->showWizardButton):?>
  <div class="pull-right" style="margin-top:30px;"><a href="/" class="btn btn-danger">Return to Getting Started</a></div>
  <?php endif; ?>
  @yield('content')
    <div class="push"></div>
</div>
@include('partials.footer')
</body>
