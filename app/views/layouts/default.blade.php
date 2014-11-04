<!doctype html>
<html>
<head>
  @include('partials.head')
</head>
<body>
@include('partials.header')
<div class="content container-fluid">
  @yield('content')
</div>
@include('partials.footer')
</body>
