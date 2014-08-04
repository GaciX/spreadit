@extends('layout.pages')

@section('title')
    <title>spreadit.io :: add a post to {{ $title }}</title>
@stop

@section('content')
    <h1>Posting to /s/{{ $title }}</h1>
    @if ($errors->any())
        <div class="alert alert-warning fade in">
            <div class="close" data-dismiss="alert" aria-hidden="true">&times;</div>
            <h4 class="text-center">{{ $errors->first() }}</h4>
        </div>
    @endif
    <div class="row-fluid">
        <div class="span6">
            <p>You have {{ (PostController::MAX_POSTS_PER_DAY - PostController::getPostsInTimeoutRange()) }} of {{ PostController::MAX_POSTS_PER_DAY }} posts remaining per {{ UtilController::prettyAgo(time() - PostController::MAX_POSTS_TIMEOUT_SECONDS) }}</p>
            @if ((PostController::MAX_POSTS_PER_DAY - PostController::getPostsInTimeoutRange()) > 0)
            <form id="post-form" action="{{ $formurl }}" method="post" class="flat-form flatpop-left">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <p class="text">
                    <input name="title" type="text" value="{{ Input::old('title') }}" id="title" placeholder="title" maxlength="{{ PostController::MAX_TITLE_LENGTH }}"/>
                    {{ $errors->first('title') }}
                </p>
                <p class="text">
                    <div class="row-fluid">
                        <div class="span9">
                            <input name="url" type="url" value="{{ Input::old('url') }}" id="url" placeholder="http://yoururl.com" maxlength="{{ PostController::MAX_URL_LENGTH }}"/>
                            {{ $errors->first('url') }}
                        </div>
                        <div class="span3">
                            <button type="button" id="suggest_title">Suggest Title</button>
                        </div>
                    </div>
                </p>
                <p class="text">
                    <textarea name="data" id="data" placeholder="Body of post" maxlength="{{ PostController::MAX_MARKDOWN_LENGTH }}">{{ Input::old('data') }}</textarea>
                    {{ $errors->first('data') }}
                </p>
                <div class="submit">
                    <button type="submit">Post</button>
                    <div class="ease"></div>
                </div>
            </form>
            @endif
        </div>
        <div class="span6">
            <p>
                Enter a title (required) and a url or body (or both).
<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.---.&nbsp;.---.&nbsp;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;o&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;me&nbsp;want&nbsp;cookie!<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_..-:&nbsp;&nbsp;&nbsp;o&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:-.._&nbsp;&nbsp;&nbsp;&nbsp;/<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.-''&nbsp;&nbsp;'&nbsp;&nbsp;`---'&nbsp;`---'&nbsp;"&nbsp;&nbsp;&nbsp;``-.&nbsp;&nbsp;&nbsp;&nbsp;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.'&nbsp;&nbsp;&nbsp;"&nbsp;&nbsp;&nbsp;'&nbsp;&nbsp;"&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;&nbsp;"&nbsp;&nbsp;.&nbsp;'&nbsp;&nbsp;"&nbsp;&nbsp;`.&nbsp;&nbsp;<br>
&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;'.---.,,.,...,.,.,.,..---.&nbsp;&nbsp;'&nbsp;;<br>
&nbsp;&nbsp;&nbsp;&nbsp;`.&nbsp;"&nbsp;`.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.'&nbsp;"&nbsp;.'<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`.&nbsp;&nbsp;'`.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.'&nbsp;'&nbsp;.'<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`.&nbsp;&nbsp;&nbsp;&nbsp;`-._&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_.-'&nbsp;"&nbsp;&nbsp;.'&nbsp;&nbsp;.----.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`.&nbsp;"&nbsp;&nbsp;&nbsp;&nbsp;'"--...--"'&nbsp;&nbsp;.&nbsp;'&nbsp;.'&nbsp;&nbsp;.'&nbsp;&nbsp;o&nbsp;&nbsp;&nbsp;`.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.'`-._'&nbsp;&nbsp;&nbsp;&nbsp;"&nbsp;.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"&nbsp;_.-'`.&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;o&nbsp;&nbsp;:<br>
&nbsp;&nbsp;jgs&nbsp;.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;```--.....--'''&nbsp;&nbsp;&nbsp;&nbsp;'&nbsp;`:_&nbsp;o&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:<br>
&nbsp;&nbsp;&nbsp;&nbsp;.'&nbsp;&nbsp;&nbsp;&nbsp;"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"&nbsp;&nbsp;&nbsp;;&nbsp;`.;";";";'<br>
&nbsp;&nbsp;&nbsp;;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.&nbsp;;&nbsp;.'&nbsp;;&nbsp;;&nbsp;;<br>
&nbsp;&nbsp;;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'&nbsp;&nbsp;&nbsp;"&nbsp;&nbsp;&nbsp;&nbsp;.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.-'<br>
&nbsp;&nbsp;'&nbsp;&nbsp;"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"&nbsp;&nbsp;&nbsp;'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"&nbsp;&nbsp;&nbsp;&nbsp;_.-'<br>
<br>
            </p>
        </div>
    </div>
@stop

@section('script')
<!-- todo validation -->
@stop
