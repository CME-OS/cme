@extends('layouts.default')
@section('content')
    <h1 class="page-header"><?= $template->name ?>
        <small><a href="/templates/edit/<?= $template->id ?>">edit</a></small>
    </h1>
    <div class="row">
        <div class="col-md-8">
            <div class="well" style="min-height:800px;">
                <iframe src="/templates/content/<?= $template->id ?>"
                        frameborder="0" width="100%"
                        height="800px;"></iframe>
            </div>
        </div>
    </div>
@stop
