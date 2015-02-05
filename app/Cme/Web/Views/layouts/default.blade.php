<!doctype html>
<html>
<head>
  @include('partials.head')
</head>
<body>
@include('partials.header')
<div class="content container-fluid" style="margin-bottom: 50px;">
  @yield('content')
</div>
@include('partials.footer')
</body>
