<!doctype html>
<html>
<head>
  @include('partials.head')
</head>
<body>
<div>
  <h1 class="text-center">Welcome to CME - Please Login</h1>

  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="well well-sm">
        <form action="/login" role="form" method="post" style="width:300px;" class="center-block">
          <div class="form-group">
            <label for="username">User Name</label>
            <input type="text" name="email" class="form-control" id="username"
                   placeholder="User Name">
          </div>
          <div class="form-group">
            <label for="sender-name">Password</label>
            <input type="password" name="password" class="form-control"
                   id="password" placeholder="Password">
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
