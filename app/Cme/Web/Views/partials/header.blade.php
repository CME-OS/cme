<?php
$path = Request::path();
?>

<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container-fluid">

    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
              data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="{{ URL::route('home') }}">CME Logo</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

      <ul class="nav navbar-nav">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Campaigns <span
              class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ URL::route('campaigns') }}">Manage campaigns</a></li>
            <li><a href="{{ URL::route('campaign.new') }}">Create new campaign</a></li>
            <li class="divider"></li>
            <li><a href="{{ URL::route('templates') }}">Manage templates</a></li>
            <li><a href="{{ URL::route('template.new') }}">Create new template</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Brands <span
              class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ URL::route('brands') }}">Manage brands</a></li>
            <li><a href="{{ URL::route('brands.new') }}">Create new brand</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Lists <span
              class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ URL::route('lists') }}">Manage lists</a></li>
            <li><a href="{{ URL::route('lists.new') }}">Create new list</a></li>
          </ul>
        </li>


        <li><a href="/analytics">Analytics</a></li>
        <li><a href="/users">Users</a></li>
        <li><a href="/smtp-providers">SMTP Providers</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?= Auth::user(
            )->email ?> <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="/logout" class="navbar-link">Logout</a></li>
            <li><a href="#">Notifications <span class="badge" style="background-color:red;">1</span></a></li>
          </ul>
        </li>
      </ul>
    </div>
    <!-- /.navbar-collapse -->
  </div>
  <!-- /.container-fluid -->
</nav>
