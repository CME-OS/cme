@section('content')
@extends('layouts.setup')
<div class="container">
  <div class="row">
    <div class="col-md-12 text-center">
      <h1>Congratulations! You have successfully installed CME</h1>
      <p>Now Proceed to Login. A quick account was created for you</p>
      <p><strong>Username:</strong> admin</p>
      <p><strong>Password:</strong> admin</p>
      <p class="text-danger"><strong>Make sure you delete this user or change the password</strong></p>
      <a href="/login" class="btn btn-success">Take me to Login</a>
    </div>
  </div>
  </div>

</div>

@stop
