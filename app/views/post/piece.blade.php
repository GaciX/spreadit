<div class="post-piece {{ $nsfwClasses }} {{ $selfpost }}" tabindex="1" data-post-id="{{ $post->id }}">
    <div class="post-points">
        <div class="breaker points-actions">
            <a href="{{ URL::to("/vote/post/" . $post->id . "/up") }}" {{ $bubbleText }} class="vote {{ $bubbleClasses }} {{ $upvoteClasses }}" data-id="{{ $post->id }}" data-type="post" data-updown="up" rel="nofollow"><span class="voteiconup"></span></a>
            <a href="{{ URL::to("/vote/post/" . $post->id . "/down") }}" {{ $bubbleText }} class="vote {{ $bubbleClasses }} {{ $downvoteClasses }}" data-id="{{ $post->id }}" data-type="post" data-updown="down" rel="nofollow"><span class="voteicondown"></span></a>
            <span class="upvotes">{{ $post->upvotes }}</span><span class="downvotes">{{ $post->downvotes }}</span><span class="total-points">{{ $post->upvotes - $post->downvotes }}</span>
        </div>
        <div class="breaker points-created-at">
            {{ Utility::prettyAgo($post->created_at) }}
        </div>
        <div class="breaker points-creator">
            <a class="username {{ $anonymousClasses }}" rel="nofollow" href="{{ URL::to("/u/{$post->username}") }}">{{ $post->username }}</a><span class="upoints">{{ $post->points }}</span><span class="uvotes">{{ $post->votes }}</span>
        </div>
    </div>

    @if ($selfpost)
    <a rel="nofollow" href="{{ URL::to($post->url) }}">
    @else
    <a href="{{ URL::to($post->url) }}">
    @endif
        <div class="post-thumbnail">
            <span class="thumb-small">
                <div class="thumb-img" title="{{{ $post->title }}}" {{ (empty($post->thumbnail)) ?: 'style="background-image:url(/assets/thumbs/small/'.$post->thumbnail.'.jpg)"' }}></div>
            </span>
            <span class="thumb-large">
                <div class="thumb-img" title="{{{ $post->title }}}" {{ (empty($post->thumbnail)) ?: 'style="background-image:url(/assets/thumbs/large/'.$post->thumbnail.'.jpg)"' }}></div>
            </span>
        </div>
    </a>
    <div class="post-data">
        <div class="breaker data-title-and-section">
            <a class="post-title" rel="nofollow" href="{{ $postUrl }}">{{ $post->title }}</a>
            <span class="post-title-section-separator"></span> 
            <a class="post-section" href="{{ $sectionUrl }}">{{ $post->section_title }}</a>
        </div>
        <div class="breaker data-url-and-comments">
            <span class="post-url">{{ $commentsPrettyUrl }}</span>
            <span class="post-url-comments-link-separator"></span>
            <a class="post-comments-link" href="{{ $commentsUrl }}"><span class="post-comments-count">{{ $post->comment_count }}</span></a>
        </div>
        <div class="breaker data-summary">
            @if ($selfpost)
                <a href="{{ $commentsUrl }}">
            @endif
            <span class="summary">{{{ Utility::ellipsis(Utility::prettySubstr($post->markdown, 130)) }}}</span>
            @if ($selfpost)
                </a>
            @endif
        </div>
    </div>
</div>
