@extends('layout.pages')

@section('title')
    <title>spreadit.io :: preferences</title>
@stop
@section('description')
    <meta name="description" content="check & change your user preferences here">
@stop

@section('content')
    <div class="span10">
        <form method="post">
            show nsfw: {{ Form::checkbox('show_nsfw', '', Auth::user()->show_nsfw) }}
            <br>
            show nsfl: {{ Form::checkbox('show_nsfl', '', Auth::user()->show_nsfl) }}
            <br>
            <button type="submit">Save</button>

            <hr>
            change colorscheme <a href="/color">here</a>
        </form>
    </div>
</div>
@stop
