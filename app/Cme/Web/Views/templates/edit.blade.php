@extends('layouts.default')
@section('content')
    <h1 class="page-header">Templates
        <small>{{ $template->name }}</small>
    </h1>
<div class="container">
<div class="row">
<div class="col-sm-12 well">

<form role="form" action="/templates/update" method="post">
    <input type="hidden" name="id" value="<?= $template->id ?>"/>
    <div class="form-group <?= isset($errors['name'])? 'has-error has-feedback': '' ?>">
    <label for="template-name">Name</label>
    <input type="text" name="name" class="form-control" id="template-name" placeholder="Name" value="<?= isset($input['name'])? $input['name'] : $template->name ?>">
    </div>
    <div class="form-group <?= isset($errors['content'])? 'has-error has-feedback': '' ?>">
    <label for="template-content">Content <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['content'])? ' - '.$errors['content']->message: '' ?></span></label>
    <textarea name="content" class="form-control" id="template-content" cols="30" rows="10"><?= isset($input['content'])? $input['content'] : $template->content ?></textarea>
    </div>
    <button type="submit" class="btn btn-success">Save</button>
</form>
</div>
</div>
</div>
    <script src="/assets/ckeditor/ckeditor.js"></script>
    <script>CKEDITOR.replace('template-content');</script>
@stop
