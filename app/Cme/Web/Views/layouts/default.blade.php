<!doctype html>
<html>
<head>
  @include('partials.head')
</head>
<body>
@include('partials.header')
<div class="content container-fluid" style="margin-bottom: 50px;">
  <?php if($state->showWizardButton):?>
  <div class="pull-right" style="margin-top:30px;"><a href="/" class="btn btn-danger">Return to Getting Started</a></div>
  <?php endif; ?>
  @yield('content')
</div>
@include('partials.footer')
</body>
