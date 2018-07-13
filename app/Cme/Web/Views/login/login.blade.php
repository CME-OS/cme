<!doctype html>
<html>
<head>
  @include('partials.head')
</head>
<body>
    <div class="login">
        <div class="row">
            <div class="col-sm-12">
                <img src="/assets/img/logo.png" alt=""/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3 class="text-center">Login to your account!</h3>
                @if($message = Session::get('message', false))
                    <div class="alert alert-danger">{{ $message }}</div>
                @endif

                <form action="/login" method="post">
                    {{ csrf_field() }}
                    <div class='form-group'>
                        <label for="email">Email:</label>
                        <input type="text" name="email" class="form-control">
                    </div>

                    <div class='form-group'>
                        <label for="password">Password:</label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Login!</button>
                    </div>

                </form>

                <p><a href="">Forgotten Password?</a></p>
            </div>
        </div>

    </div>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-backstretch/2.0.4/jquery.backstretch.min.js"></script>
    <script>
        var images = [
            'https://unsplash.com/photos/a_xa7RUKzdc/download',
            'https://unsplash.com/photos/dfZbts6B4yw/download',
            'https://unsplash.com/photos/m0l5J8Lqnzo/download',
            'https://unsplash.com/photos/cGe1PV_yEso/download'
        ];

        var index = Math.floor(Math.random() * images.length);
        $.backstretch(images[index]);
    </script>
</body>
