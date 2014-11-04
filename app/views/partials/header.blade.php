<?php
 $path = Request::path();
?>

<div class="header container-fluid">
  <div style="padding:10px;">
    <ul class="nav nav-pills" role="tablist">
      <li role="presentation" class="<?= (Str::startsWith($path, '/'))? 'active' : '' ?>"><a href="/">Home</a></li>
      <li role="presentation" class="<?= (Str::startsWith($path, 'brands'))? 'active' : '' ?>"><a href="/brands">Brands</a></li>
      <li role="presentation" class="<?= (Str::startsWith($path, 'lists'))? 'active' : '' ?>"><a href="/lists">Lists</a></li>
      <li role="presentation" class="<?= (Str::startsWith($path, 'campaigns'))? 'active' : '' ?>"><a href="/campaigns">Campaigns</a></li>
      <li role="presentation" class="<?= (Str::startsWith($path, 'queues'))? 'active' : '' ?>"><a href="/queues">Queues</a></li>
      <li role="presentation" class="<?= (Str::startsWith($path, 'analytics'))? 'active' : '' ?>"><a href="/analytics">Analytics</a></li>
      <li role="presentation" class="<?= (Str::startsWith($path, 'users'))? 'active' : '' ?>"><a href="/users">Users</a></li>
    </ul>
  </div>
</div>

