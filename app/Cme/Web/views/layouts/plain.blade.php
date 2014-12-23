<!doctype html>
<html>
<head>
  @include('partials.head')
</head>
<body>
@include('partials.header')
@include('partials.session-bar')
<div class="container">
  @yield('content')
</div>
@include('partials.footer-simple')
</body>
