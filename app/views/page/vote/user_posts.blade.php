@extends('layout.user')

@section('title')
    <title>spreadit.io :: {{ $username }}'s post votes</title>
@stop
@section('description')
    <meta name="description" content="let's spy on {{ $username }}'s post votes.. for science!">
@stop

@section('content')
<table>
    <thead>
        <tr>
            <td style="width: 20%">timestamp</td>
            <td style="width: 20%">when</td>
            <td style="width: 20%">username</td>
            <td style="width: 20%">vote</td>
            <td style="width: 20%">post</td>
        </tr>
    </thead>
    <tbody>
    @foreach ($votes as $vote)
        <tr>
            <td>{{ $vote->created_at }}</td>
            <td>{{ Utility::prettyAgo($vote->created_at) }}</td>
            <td><a class="username" href="/u/{{ $vote->username }}">{{ $vote->username }}</a><span class="upoints">{{ $vote->points }}</span><span class="uvotes">{{ $vote->votes }}</span></td>
            <td><span class="{{ $vote->updown == Constant::VOTE_UP ? 'voteiconup' : 'voteicondown' }}"></span> <span class="upvotes">{{ $vote->upvotes }}</span><span class="downvotes">{{ $vote->downvotes}}</span><span class="total-votes">{{ ($vote->upvotes - $vote->downvotes) }}</span></td>
            <td><a href="/s/{{ $vote->section_title }}/posts/{{ $vote->id }}">{{ $vote->title }}</a></td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $votes->links() }}
@stop
