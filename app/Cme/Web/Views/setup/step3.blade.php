@section('content')
    @extends('layouts.setup')

    <div class="pull-right"><a href="/setup/skip"
                               class="btn btn-danger">Skip</a></div>
    <h1 class="page-header">Step 3:
        <small>Create User</small>
    </h1>
    <div class="container">
        <form role="form" action="/setup/create-user" method="post">
        <div class="row">
            <div class="col-md-12 well">
                <div class="form-group" <?= isset($errors['email'])? 'has-error has-feedback': '' ?>>
                    <label for="email">Username: <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['email'])? ' - '.$errors['email']->message: '' ?></span></label>
                    <input type="text" name="email" class="form-control" id="email" value="<?= isset($formData['email'])? $formData['email'] : '' ?>">
                    <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['email'])? '': 'hidden' ?>" aria-hidden="true"></span>
                </div>
                <div class="form-group" <?= isset($errors['password'])? 'has-error has-feedback': '' ?>>
                    <label for="password">Password: <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['password'])? ' - '.$errors['password']->message: '' ?></span></label>
                    <input type="password" name="password" class="form-control" id="password" value="<?= isset($formData['password'])? $formData['password'] : '' ?>">
                    <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['password'])? '': 'hidden' ?>" aria-hidden="true"></span>
                </div>

            </div>
        </div>
            <button type="submit" class="btn btn-lg btn-block btn-success">Create User</button>
        </form>
    </div>

@stop
