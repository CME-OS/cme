@extends('layouts.default')
@section('content')
    <h1 class="page-header">Templates
        <small>{{ $template->name }}</small>
    </h1>
    <div class="container">
    <div class="row">
        <div class="col-sm-12 well">

            {{ Form::model($template, ['route' => 'template.update.post', 'id' => 'template-form']) }}

            <input type="hidden" name="id" value="<?= $template->id ?>"/>

            <div class="row">
                <div class="col-md-8">

                    <div class='form-group'>
                        {{ Form::label('name', 'Name') }}
                        {{ Form::text('name', null, ['class' => 'form-control']) }}
                    </div>

                    <div class='form-group'>
                        {{ Form::label('content', 'Content') }}
                        {{ Form::textarea('content', null, ['class' => 'form-control', 'id' => 'template-content']) }}
                    </div>
                  <button type="submit" class="btn btn-success">Save</button>
                </div>

            </div>
            {{ Form::close() }}
        </div>
    </div>
    </div>
    <script src="/assets/ckeditor/ckeditor.js"></script>
    <script>CKEDITOR.replace('template-content');</script>
@stop
