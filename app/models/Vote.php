<?php
use \Functional as F;

class Vote extends BaseModel
{
    protected $table = 'votes';
    protected $guarded = array('id');

    const COMMENT_TYPE = 0;
    const POST_TYPE = 1;
    const SECTION_TYPE = 2;
    const UP = 1;
    const DOWN = -1;
    const VOTES_PAGE_RESULTS = 25;
    const COMMENT_PAGE_RESULTS = 25;

    protected static $errors = [
        'same_stored' => 'vote is same as stored value',
        'reverse' => 'vote cannot be reversed',
        'lackingpoints' => 'vote cannot be completed as you do not have enough points',
        'anonymous' => 'votes cannot come from an anon user, please register',
        'systemerror' => 'general system error occurred'
    ];

    public static function applySelection($items, $type)
    {
        $votes = self::getMatchingVotes($type, $items);

        F\each($items, function($v) use($votes) {
            $v->selected = isset($votes[$v->id]) ? $votes[$v->id] : 0;
            return $v;
        });

        return $items;
    }

    public static function getMatchingVotes($type, $items)
    {
        //requires to be logged in
        if(!Auth::check()) return array();

        $items_to_check = F\map($items, function($m) { return $m->id; });

        if(count($items_to_check) == 0) {
            $items_to_check = array(0);
        }

        $votes = DB::table('votes')
            ->select('item_id', 'updown')
            ->where('user_id', '=', Auth::id())
            ->where('type', '=', $type)
            ->whereIn('item_id', $items_to_check)
            ->get();

        $rval = array();
        foreach($votes as $i) {
            $rval[$i->item_id] = $i->updown;
        }

        return $rval;
    }

    protected function checkVote($type, $type_id)
    {
        return DB::table('votes')
            ->select('updown')
            ->where('type',    '=', $type)
            ->where('item_id', '=', $type_id)
            ->where('user_id', '=', Auth::id())
            ->orderBy('id', 'asc')
            ->get();
    }

    protected function alreadyExists($check)
    {
        $check = $check[0];
        if($check->updown == self::UP) {
            return ['success'=>false, 'errors'=>array(self::$errors['same_stored'])];
        } else {
            return ['success'=>false, 'errors'=>array(self::$errors['reverse'])];
        }
    }

    public function applyVote($user, $type, $type_id, $updown)
    {
        //deal with item table
        $item = "";
        switch($type) {
            case self::POST_TYPE:
                $item = Post::findOrFail($type_id);
                break;
            case self::COMMENT_TYPE:
                $item = Comment::findOrFail($type_id);
                Cache::forget(Comment::CACHE_NEWLIST_NAME.$item->post_id);
                break;
            case self::SECTION_TYPE:
                $item = Section::findOrFail($type_id);
                break;
            default:
                throw new UnexpectedValueException("type: $type not enumerated");
        }

        //increment our total vote counter
        $user->increment('votes');

        //decrement one point for voting
        $user->decrement('points');

        //double decrement for self upvote
        if($type == self::POST_TYPE || $type = self::COMMENT_TYPE) {
            if($item->user_id == Auth::id() && $updown == self::UP) {
                $user->decrement('points');
            }
        }

        //upvote/downvote the item itself
        if($updown == self::UP) {
            $item->increment('upvotes');
        } else if($updown == self::DOWN) {
            $item->increment('downvotes');
        }

        //upvote/downvote user who posted (ignore for sections)
        if($type == self::POST_TYPE || $type == self::COMMENT_TYPE) {
            $rec_user = User::findOrFail($item->user_id);

            if($updown == self::UP) {
                $rec_user->increment('points');
            } else if($updown == self::DOWN) {
                $rec_user->decrement('points');
            }
        }


        //deal with votes table
        $vote = new Vote(array(
            'type'    => $type,
            'user_id' => Auth::id(),
            'item_id' => $type_id,
            'updown'  => $updown
        ));
        $vote->save();

    }

    public static function getPostVotes($type_id)
    {
        return DB::table('votes')
            ->select('votes.updown', 'votes.created_at', 'votes.user_id', 'users.username', 'users.points', 'users.votes')
            ->join('users', 'users.id', '=', 'votes.user_id')
            ->where('votes.type', '=', self::POST_TYPE)
            ->where('votes.item_id', '=', $type_id)
            ->simplePaginate(self::VOTES_PAGE_RESULTS);
    }


    public static function getCommentVotes($type_id)
    {
        return DB::table('votes')
            ->select('votes.updown', 'votes.created_at', 'votes.user_id', 'users.username', 'users.points', 'users.votes')
            ->join('users', 'users.id', '=', 'votes.user_id')
            ->where('votes.type', '=', self::COMMENT_TYPE)
            ->where('votes.item_id', '=', $type_id)
            ->simplePaginate(self::COMMENT_PAGE_RESULTS);
    }

    protected function action($type, $type_id, $updown)
    {
        $check = $this->checkVote($type, $type_id);

        if(count($check) > 0) {
            return self::alreadyExists($check);
        }

        $user = User::findOrFail(Auth::id());
        
        if($user->points < 1) {
            return ['success' => false, 'errors' => array(self::$errors['lackingpoints'])];
        }

        if($user->anonymous == 1) {
            return ['success' => false, 'errors' => array(self::$errors['anonymous'])];
        }

        try {
            $this->applyVote($user, $type, $type_id, $updown);
        } catch (Exception $e) {
            Log::error($e);
            return ['success' => false, 'errors' => array(self::$errors['systemerror'])];
        }

        return ['success' => true, 'errors' => []];
    }
} 
