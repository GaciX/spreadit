<?php
use \Michelf\MarkdownExtra;
use \Functional as F;

class CommentController extends BaseController
{
    const NO_PARENT = 0;
    const CACHE_PATH_DATA_FROM_ID_MINS = SortController::YEAR_SECONDS;
    const CACHE_PATH_DATA_FROM_ID_NAME = 'comment_path_from_id_';
    const CACHE_NEWLIST_MINS = 1;
    const CACHE_NEWLIST_NAME = 'comment_newlist_id_';

    const MAX_MARKDOWN_LENGTH = 4000;
    const MAX_COMMENTS_PER_DAY = 30;
    const MAX_COMMENTS_TIMEOUT_SECONDS = 86400;

    public static function getPathDataFromId($comment_id)
    {
        return Cache::remember(self::CACHE_PATH_DATA_FROM_ID_NAME.$comment_id, self::CACHE_PATH_DATA_FROM_ID_MINS, function() use($comment_id)
        {
            $comment = DB::table('comments')
                ->join('posts', 'comments.post_id', '=', 'posts.id')
                ->join('sections', 'posts.section_id', '=', 'sections.id')
                ->select('posts.id', 'sections.title')
                ->where('comments.id', '=', $comment_id)
                ->first();

            $obj = new stdClass();
            $obj->section_title = $comment->title;
            $obj->post_id = $comment->id;
            return $obj;
        });
    }

    public static function getSourceFromId($id)
    {
        return Comment::findOrFail($id)->markdown;
    }

    public static function get($post_id)
    {
        $comments = Cache::remember(self::CACHE_NEWLIST_NAME.$post_id, self::CACHE_NEWLIST_MINS, function() use($post_id)
        {
            return DB::table('comments')
                ->join('users', 'comments.user_id', '=', 'users.id')
                ->select('comments.id', 'comments.user_id', 'comments.created_at', 'comments.updated_at', 'comments.upvotes', 'comments.downvotes', 'comments.parent_id', 'comments.data', 'users.username', 'users.points', 'users.id AS users_user_id')
                ->where('post_id', '=', $post_id)
                ->orderBy('id', 'asc')
                ->get();
        });

        return VoteController::applySelection($comments, VoteController::COMMENT_TYPE);
    }

    public static function update($comment_id)
    {
        if(Auth::user()->points < 1) {
            return Redirect::to('/comments/'.$comment_id)->withErrors(['You need at least one point to edit a comment']);
        }

        $comment = Comment::findOrFail($comment_id);

        if($comment->user_id != Auth::id()) {
            return Redirect::to('/comments/'.$comment_id)->withErrors(['This comment does not have the same user id as you']);
        }

        $data['user_id'] = Auth::id();
        $data['data'] = Input::only('data')['data'];
        $data['markdown'] = $data['data'];
        $data['data'] = MarkdownExtra::defaultTransform(e($data['markdown']));

        $rules = array(
            'user_id' => 'required|numeric',
            'markdown' => 'required|max:'.self::MAX_MARKDOWN_LENGTH
        );

        $validate = Validator::make($data, $rules);
        if($validate->fails()) {
            return Redirect::to('/comments/'.$comment_id)->withErrors($validate->messages())->withInput();
        }

        $history = new History([
            'data'     => $comment->data,
            'markdown' => $comment->markdown,
            'user_id'  => Auth::id(),
            'type'     => HistoryController::COMMENT_TYPE,
            'type_id'  => $comment->id
        ]);
        $history->save();

        Cache::forget(self::CACHE_NEWLIST_NAME.$comment->post_id);

        $comment->markdown = $data['markdown'];
        $comment->data = $data['data'];
        $comment->save();

        return Redirect::to('/comments/'.$comment_id);
    }

    public static function getCommentsInTimeoutRange()
    {
        return DB::table('comments')
            ->select('id')
            ->where('comments.user_id', '=', Auth::id())
            ->where('comments.created_at', '>', time() - self::MAX_COMMENTS_TIMEOUT_SECONDS)
            ->count();
    }

    public static function canPost()
    {
        return (self::getCommentsInTimeoutRange() <= self::MAX_COMMENTS_PER_DAY);
    }

    public static function post($post_id)
    {
        if(Auth::user()->points < 1) {
            return Redirect::refresh()->withErrors(['You need at least one point to post a comment']);
        }

        if(!self::canPost()) {
            return Redirect::refresh()->withErrors(['error' => 'can only post ' . self::MAX_COMMENTS_PER_DAY . ' per day'])->withInput();
        }

        $data = array_merge(
            Input::only('data', 'parent_id'),
            array(
                'user_id' => Auth::id(),
                'post_id' => $post_id
            )
        );
        $data['markdown'] = $data['data'];
        $data['data'] = MarkdownExtra::defaultTransform(e($data['markdown']));

        $rules = array(
            'user_id' => 'required|numeric',
            'parent_id' => 'required|numeric',
            'post_id' => 'required|numeric',
            'markdown' => 'required|max:'.self::MAX_MARKDOWN_LENGTH
        );

        $validate = Validator::make($data, $rules);
        if($validate->fails()) {
            return Redirect::refresh()->withErrors($validate->messages())->withInput();
        }

        $post = Post::findOrFail($data['post_id']);
        
        $notification = new Notification();
        if($data['parent_id'] != self::NO_PARENT) { 
            $parent = Comment::findOrFail($data['parent_id']);
            $notification->type = NotificationController::COMMENT_TYPE;
            $notification->user_id = $parent->user_id;
        } else {
            $notification->type = NotificationController::POST_TYPE;
            $notification->user_id = $post->user_id;
        }

        $comment = new Comment($data);
        $comment->save();
        $post->increment('comment_count');

        $notification->item_id = $comment->id;
        if($notification->user_id != Auth::id()) {
            $notification->save();
        }
                
        Cache::forget(self::CACHE_NEWLIST_NAME.$post_id);
        return Redirect::refresh();
    }
}
