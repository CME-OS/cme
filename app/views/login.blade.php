<!doctype html>
<html>
<head>
  @include('partials.head')
</head>
<body>
    <div class="login">
        <div class="row">
            <div class="col-sm-4">
                <img src="/assets/img/main-logo.png" alt=""/>
            </div>

            <div class="col-sm-8">
                <h3>Login to your account!</h3>
                <?php //var_dump($message);die; ?>
                @if($message = Session::get('message', false))
                    <div class="alert alert-danger">{{ $message }}</div>
                @endif

                {{ Form::open(['route' => 'login']) }}

                    <div class='form-group'>
                        {{ Form::label('email', 'Email:') }}
                        {{ Form::text('email', null, ['class' => 'form-control']) }}
                    </div>

                    <div class='form-group'>
                        {{ Form::label('password', 'Password:') }}
                        {{ Form::password('password', ['class' => 'form-control']) }}
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Login!</button>
                    </div>

                {{ Form::close() }}

                <p><a href="">Forgotten Password?</a></p>
            </div>
        </div>

    </div>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-backstretch/2.0.4/jquery.backstretch.min.js"></script>
    <script>$.backstretch("https://unsplash.imgix.net/reserve/nE6neNVdRPSIasnmePZe_IMG_1950.jpg");
</script>
</body>
