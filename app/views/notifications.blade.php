@extends('layout.pages')

@section('title')
    <title>spreadit.io :: notifications</title>
@stop

@section('style')
@stop

@section('content')
@foreach ($notifications as $notification)
<div class="row-fluid notification">
    <div class="span12">
    @if ($notification->type == NotificationController::COMMENT_TYPE)
        <div class="comment {{ $notification->read ? 'notification-read' : 'notification-unread' }}">
            <header>
                From <a class="username" href="/u/{{ $notification->username }}">{{ $notification->username }}</a> <span class="timeago">{{ UtilController::prettyAgo($notification->created_at) }} ago</span>
            </header>
            <div class="content">
                {{ $notification->data }}
            </div>
            <footer>
                <a href="/comments/{{ $notification->item_id }}">reply/view</a>
            </footer>
        </div>
    @elseif ($notification->type == NotificationController::POST_TYPE)
        <div class="comment {{ $notification->read ? 'notification-read' : 'notification-unread' }}">
            <header>
                From <span class="username">{{ $notification->username }}</span> <span class="timeago">{{ UtilController::prettyAgo($notification->created_at) }} ago</span>
            </header>
            <div class="content">
                {{ $notification->data }}
            </div>
            <footer>
                <a href="/comments/{{ $notification->item_id }}">reply/view</a>
            </footer>
        </div>
    @elseif ($notification->type == NotificationController::ANNOUNCEMENT_TYPE)
        <div class="announcement {{ $notification->read ? 'notification-read' : 'notification-unread' }}">
            <header>
                An announcement from spreadit
            </header>
            <div class="content">
                {{ $notification->data }}
            </div>
        </div>
    @else 
    {{-- nothing shall go here --}}
    @endif
    </div>
</div>
@endforeach

{{ $notifications->links() }}
@stop

@section('script')
@stop
