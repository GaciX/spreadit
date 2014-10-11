<?php $selfpost = empty($post->url); ?>
<div class="post-piece {{ UtilityController::nsfClasses($post) }} {{ $selfpost ? 'self' : 'link' }}" tabindex="1">
    <div class="post-points">
        <div class="breaker points-actions">
            <a href="{{ URL::to("/vote/post/" . $post->id . "/up") }}" {{ UtilityController::bubbleText() }} class="vote {{ UtilityController::bubbleClasses() }} {{ UtilityController::upvoteClasses($post) }}" data-id="{{ $post->id }}" data-type="post" data-updown="up" rel="nofollow"><span class="voteiconup"></span></a>
            <a href="{{ URL::to("/vote/post/" . $post->id . "/down") }}" {{ UtilityController::bubbleText() }} class="vote {{ UtilityController::bubbleClasses() }} {{ UtilityController::downvoteClasses($post) }}" data-id="{{ $post->id }}" data-type="post" data-updown="down" rel="nofollow"><span class="voteicondown"></span></a>
            <span class="upvotes">{{ $post->upvotes }}</span><span class="downvotes">{{ $post->downvotes }}</span><span class="total-points">{{ $post->upvotes - $post->downvotes }}</span>
        </div>
        <div class="breaker points-created-at">
            {{ Utility::prettyAgo($post->created_at) }}
        </div>
        <div class="breaker points-creator">
            <a class="username {{ UtilityController::anonymousClasses($post) }}" rel="nofollow" href="{{ URL::to("/u/{$post->username}") }}">{{ $post->username }}</a><span class="upoints">{{ $post->points }}</span><span class="uvotes">{{ $post->votes }}</span>
        </div>
    </div>
    <div class="post-thumbnail">
        @if (!empty($post->thumbnail))
            @if ($selfpost)
            <a rel="nofollow" href="{{ URL::to($post->url) }}">
            @else
            <a href="{{ URL::to($post->url) }}">
            @endif
                <img alt="{{{ $post->title }}}" src="/assets/thumbs/{{ $post->thumbnail }}.jpg">
            </a>
                

        @endif
    </div>
    <div class="post-data">
        <div class="breaker data-title-and-section">
            <a rel="nofollow" href="{{ UtilityController::postUrl($post) }}">{{ $post->title }}</a> | <a class="post-section" href="{{ UtilityController::sectionUrl($post) }}">{{ $post->section_title }}</a>
        </div>
        <div class="breaker data-url-and-comments">
            <span class="post-url">{{ UtilityController::commentsPrettyUrl($post) }}</span> :: <a href="{{ UtilityController::commentsUrl($post) }}">view comments({{ $post->comment_count }})</a>
        </div>
        <div class="breaker data-summary">
            @if ($selfpost)
                <a href="{{ UtilityController::commentsUrl($post) }}">
            @endif
            <span class="summary">{{{ Utility::ellipsis(Utility::prettySubstr($post->markdown, 130)) }}}</span>
            @if ($selfpost)
                </a>
            @endif
        </div>
    </div>
</div>
