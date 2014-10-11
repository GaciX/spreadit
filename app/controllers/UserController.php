<?php

class UserController extends BaseController
{
    protected function logout()
    {
        Auth::logout();
	    return Redirect::to('/');
    }

    protected function register()
    {
        $user = new User();
        $user->username = e(Input::get('username'));
        $user->password = Input::get('password');
        $user->password_confirmation = Input::get('password_confirmation');
        $user->captcha = Input::get('captcha');

        if($user->save()) {
            $info = Input::only('username', 'password');
        
            if(Auth::attempt($info)) {
                return Redirect::intended('/s/all/hot');
            } else {
                return Redirect::to('/login')->withErrors(['message' => 'A general error occurred, please try again.'])->withInput();
            }
        } else {
            return Redirect::to('/login')->withErrors($user->errors())->withInput();
        }
    }

    protected function validateLogin(array $data)
    {
        $data['username'] = e($data['username']);
        
        $rules = array(
            'username' => 'required|max:24',
            'password' => 'required|max:128',
        );

        return Validator::make($data, $rules);
    }

    protected function login()
    {
        $data = Input::only('username', 'password');
        $validate = $this->validateLogin($data);

        if($validate->fails()) {
            return Redirect::to('/login')->withErrors($validate->messages())->withInput();
        }

        if(!Auth::attempt($data, true)) {
            return Redirect::to('/login')->withErrors(['message' => 'wrong user id or password']);
        }

        return Redirect::to('/');
    }

    protected function notifications()
    {
		$view = View::make('notifications', [
			'sections' => Section::get(),
			'notifications' => Notification::get()
		]);

		Notification::markAllAsRead();

		return $view;
    }

    protected function notificationsJson()
    {
		$results = iterator_to_array(Notification::get());
		Notification::markAllAsRead();
		return Response::json($results);
	}
    
    protected function preferences()
    {
		return View::make('preferences', [
			'sections' => Section::get()
		]);
    }

    protected function preferencesJson()
    {
        return Response::json("moo");
    }

    protected function savePreferences()
    {
        $show_nsfw = Input::get('show_nsfw', 0);
        $show_nsfl = Input::get('show_nsfl', 0);
        User::savePreferences(Auth::id(), $show_nsfw, $show_nsfl);

        return $this->savedPreferences();
    }

    protected function savedPreferences()
    {
		return View::make('savedpreferences', [
			'sections' => Section::get()
		]);
    }

    protected function comments($username)
    {
        return View::make('user_comments', [
            'sections' => Section::get(),
            'comments' => User::comments($username),
            'username' => $username,
            'highlight' => 'comments'
        ]);
    }

    protected function commentsJson($username)
    {
        return Response::json(iterator_to_array(User::comments($username)));
    }

    protected function posts($username)
    {
        return View::make('user_posts', [
            'sections' => Section::get(),
            'posts' => User::posts($username),
            'username' => $username,
            'highlight' => 'posts'
        ]);
    }

    protected function postsJson($username)
    {
        return Response::json(iterator_to_array(User::posts($username)));
    }

    protected function postsVotes($username)
    {
        return View::make('user_posts_votes', [
            'sections' => Section::get(),
            'votes' => User::postsVotes($username),
            'username' => $username,
            'highlight' => 'pvotes'
        ]);
    }

    protected function postsVotesJson($username)
    {
        return Response::json(iterator_to_array(User::postsVotes($username)));
    }

    protected function commentsVotes($username)
    {
        return View::make('user_comments_votes', [
            'sections' => Section::get(),
            'votes' => User::commentsVotes($username),
            'username' => $username,
            'highlight' => 'cvotes'
        ]);
    }

    protected function commentsVotesJson($username)
    {
        return Response::json(iterator_to_array(User::commentsVotes($username)));
    }

    protected function mainVote($username)
    {
        $stats = User::userStats($username);
        return View::make('user_votes_page', [
            'sections' => Section::get(),
            'username' => $username,
            'stats'    => $stats,
            'highlight' => ''
        ]);
    }
}
