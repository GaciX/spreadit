<?php
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
require_once(dirname(__FILE__) . '/validators.php');


Route::pattern('post_id', '[0-9]+');
Route::pattern('post_title', '[a-zA-Z0-9_-]+');
Route::pattern('section_titles', '[a-zA-Z0-9_-\n]+');

Route::get('/', 'SectionController@get');
Route::get('/spreadits', 'SectionController@getSpreadits');
Route::get('/spreadits/.json', 'SectionController@getSpreaditsJson');
Route::get('/.rss', 'FeedController@rss');
Route::get('/.atom', 'FeedController@atom');

Route::get('/about',   'PageController@about');
Route::get('/contact', 'PageController@contact');
Route::get('/threats', 'PageController@threats');
Route::get('/login',   'PageController@login');
Route::post('/login',  ['before' => 'throttle:1,1', 'uses' => 'UserController@login']);

Route::any('/logout', ['before' => 'auth', 'uses' => 'UserController@logout']);

Route::post('/register', ['before' => 'throttle:1,10', 'uses' => 'UserController@register']);

Route::get('/notifications', ['before' => 'auth', 'uses' => 'UserController@notifications']);
Route::get('/notifications/.json', ['before' => 'auth.token', 'uses' => 'UserController@notificationsJson']);

Route::get('/preferences', ['before' => 'auth', 'uses' => 'UserController@preferences']);
Route::post('/preferences', ['before' => 'auth', 'uses' => 'UserController@savePreferences']);
Route::get('/preferences/.json', ['before' => 'auth.token', 'uses' => 'UserController@preferencesJson']);

Route::group(['prefix' => '/s'], function()
{
    Route::get('/all', 'SectionController@get');

	Route::group(['prefix' => '/{section_title}'], function($section_title)
	{
		Route::get('/', 'SectionController@get');
        Route::get('/.json', 'SectionController@getJson');
        Route::get('/.rss', 'FeedController@rss'); 
        Route::get('/.atom', 'FeedController@atom'); 

		Route::get('/hot', 'SectionController@hot');
		Route::get('/hot/.json', 'SectionController@hotJson');
		Route::get('/new', 'SectionController@new_');
		Route::get('/new/.json', 'SectionController@new_Json');
		Route::get('/top/{timeframe}', 'SectionController@top');
		Route::get('/top/{timeframe}/.json', 'SectionController@topJson');
		Route::get('/controversial/{timeframe}', 'SectionController@controversial');
		Route::get('/controversial/{timeframe}/.json', 'SectionController@controversialJson');
        
        Route::get('/posts/{post_id}/{post_title?}', 'PostController@get');
    	Route::get('/posts/{post_id}/.json', 'PostController@getJson');
        Route::post('/posts/{post_id}/{post_title?}', ['before' => 'throttle:3,1', 'uses' => 'CommentController@post']);

	    Route::get('/add', 'SectionController@add');
	    Route::post('/add', ['before' => 'throttle:3,1', 'uses' => 'PostController@post']);
        Route::post('/add/.json', ['before' => 'auth.token|throttle:3,1', 'uses' => 'PostController@postJson']);
    });
});

Route::group(['prefix' => '/util'], function()
{
    Route::get('/imagewrapper', ['before' => 'throttle:2,1',  'uses' => 'UtilityController@imagewrapper']);
    Route::get('/titlefromurl', ['before' => 'throttle:6,1',  'uses' => 'UtilityController@titlefromurl']);
    Route::post('/preview',     ['before' => 'throttle:10,1', 'uses' => 'UtilityController@preview']);
    Route::get('/thumbnail',    ['before' => 'throttle:2,1',  'uses' => 'UtilityController@thumbnail']);
});


Route::group(['prefix' => '/u/{username}'], function($username)
{
    Route::get('/', 'UserController@mainVote');
    Route::get('/comments', 'UserController@comments');
    Route::get('/comments/.json', 'UserController@commentsJson');
    Route::get('/votes/comments', 'UserController@commentsVotes');
    Route::get('/votes/comments/.json', 'UserController@commentsVotesJson');
    Route::get('/posts', 'UserController@posts');
    Route::get('/posts/.json', 'UserController@postsJson');
    Route::get('/votes/posts', 'UserController@postsVotes');
    Route::get('/votes/posts/.json', 'UserController@postsVotesJson');
});

Route::group(['prefix' => '/comments'], function()
{
    Route::get('/pre/{post_id}/{parent_id}',  'CommentController@preReply');
    Route::get('/cur/{post_id}/{parent_id}',  'CommentController@curReply');
    Route::get('/post/{post_id}/{parent_id}', 'CommentController@postReply');

    Route::group(['prefix' => '/{comment_id}'], function($comment_id)
    {
        Route::get('/',        'CommentController@getRedir');
    	Route::post('/create', ['before' => 'throttle:2,1',  'uses' => 'CommentController@make']);
    	Route::post('/update', ['before' => 'throttle:10,1', 'uses' => 'CommentController@update']);
        Route::post('/delete', ['before' => 'throttle:5,1',  'uses' => 'CommentController@delete']);
    });
});

Route::group(['prefix' => '/posts/{post_id}'], function($post_id)
{
    Route::get('/', 'PostController@getRedir');
    Route::post('/update', ['before' => 'auth|throttle:5,1', 'uses' => 'PostController@update']);
    Route::post('/delete', ['before' => 'auth|throttle:5,1', 'uses' => 'PostController@delete']);

    Route::post('/tag/nsfw',       ['before' => 'auth|throttle:2,1', 'uses' => 'TagController@nsfw']);
    Route::post('/tag/sfw',        ['before' => 'auth|throttle:2,1', 'uses' => 'TagController@sfw']);
    Route::post('/tag/nsfl',       ['before' => 'auth|throttle:2,1', 'uses' => 'TagController@nsfl']);
    Route::post('/tag/sfl',        ['before' => 'auth|throttle:2,1', 'uses' => 'TagController@sfl']);
    Route::post('/tag/nsfw/.json', ['before' => 'auth|throttle:2,1', 'uses' => 'TagController@nsfwJson']);
    Route::post('/tag/sfw/.json',  ['before' => 'auth|throttle:2,1', 'uses' => 'TagController@sfwJson']);
    Route::post('/tag/nsfl/.json', ['before' => 'auth|throttle:2,1', 'uses' => 'TagController@nsflJson']);
    Route::post('/tag/sfl/.json',  ['before' => 'auth|throttle:2,1', 'uses' => 'TagController@sflJson']);
});


Route::group(['prefix' => 'vote'], function()
{
    Route::group(['before' => 'auth'], function()
    {
        //Route::get('/section/{id}',           ['before' => 'throttle:10,1', 'uses' => 'VoteController@sectionView']); //TODO
        Route::get('/section/{id}/up',          ['before' => 'throttle:10,1', 'uses' => 'VoteController@sectionUp']);
        Route::get('/section/{id}/down',        ['before' => 'throttle:10,1', 'uses' => 'VoteController@sectionDown']);
        Route::post('/section/{id}/up/.json',   ['before' => 'throttle:10,1', 'uses' => 'VoteController@sectionUpJson']);
        Route::post('/section/{id}/down/.json', ['before' => 'throttle:10,1', 'uses' => 'VoteController@sectionDownJson']);

        Route::get('/post/{id}',             ['before' => 'throttle:10,1', 'uses' => 'VoteController@postView']);
        Route::get('/post/{id}/.json',       ['before' => 'throttle:10,1', 'uses' => 'VoteController@postJson']);
        Route::get('/post/{id}/up',          ['before' => 'throttle:10,1', 'uses' => 'VoteController@postUp']);
        Route::get('/post/{id}/down',        ['before' => 'throttle:10,1', 'uses' => 'VoteController@postDown']);
        Route::post('/post/{id}/up/.json',   ['before' => 'throttle:10,1', 'uses' => 'VoteController@postUpJson']);
        Route::post('/post/{id}/down/.json', ['before' => 'throttle:10,1', 'uses' => 'VoteController@postDownJson']);

        Route::get('/comment/{id}',             ['before' => 'throttle:10,1', 'uses' => 'VoteController@commentView']);
        Route::get('/comment/{id}/.json',       ['before' => 'throttle:10,1', 'uses' => 'VoteController@commentJson']);
        Route::get('/comment/{id}/up',          ['before' => 'throttle:10,1', 'uses' => 'VoteController@commentUp']);
        Route::get('/comment/{id}/down',        ['before' => 'throttle:10,1', 'uses' => 'VoteController@commentDown']);
        Route::post('/comment/{id}/up/.json',   ['before' => 'throttle:10,1', 'uses' => 'VoteController@commentUpJson']);
        Route::post('/comment/{id}/down/.json', ['before' => 'throttle:10,1', 'uses' => 'VoteController@commentDownJson']);
	});

});

Route::group(['prefix' => '/api'], function()
{
	Route::get('/', 'SwaggerController@index');
	Route::get('/terms', 'SwaggerController@terms');
	Route::get('/license', 'SwaggerController@license');
	Route::get('/routes', 'SwaggerController@routes');
	Route::get('/routes/{type}', 'SwaggerController@getRoute');

    Route::group(['prefix' => '/auth'], function()
    {
        Route::get('/.json',   ['before' => 'throttle:5,1', 'uses' => 'Tappleby\AuthToken\AuthTokenController@index']);
        Route::post('/.json',  ['before' => 'throttle:5,1', 'uses' => 'Tappleby\AuthToken\AuthTokenController@store']);
        Route::delete('.json', ['before' => 'throttle:5,1', 'uses' => 'Tappleby\AuthToken\AuthTokenController@destroy']);
    });

    Route::group(['before' => 'auth.token', 'prefix' => 'vote'], function()
    {
        Route::post('/section/{id}/up/.json',   ['before' => 'throttle:10,1', 'uses' => 'VoteController@sectionUpJson']);
        Route::post('/section/{id}/down/.json', ['before' => 'throttle:10,1', 'uses' => 'VoteController@sectionDownJson']);

        Route::post('/post/{id}/up/.json',      ['before' => 'throttle:10,1', 'uses' => 'VoteController@postUpJson']);
        Route::post('/post/{id}/down/.json',    ['before' => 'throttle:10,1', 'uses' => 'VoteController@postDownJson']);

        Route::post('/comment/{id}/up/.json',   ['before' => 'throttle:10,1', 'uses' => 'VoteController@commentUpJson']);
        Route::post('/comment/{id}/down/.json', ['before' => 'throttle:10,1', 'uses' => 'VoteController@commentDownJson']);
	});
});

Route::group(['prefix' => '/help'], function()
{
	Route::get('/', 'HelpController@index');
	Route::get('/feeds', 'HelpController@feeds');
	Route::get('/posting', 'HelpController@posting');
	Route::get('/formatting', 'HelpController@formatting');
	Route::get('/points', 'HelpController@points');
	Route::get('/moderation', 'HelpController@moderation');
	Route::get('/anonymity', 'HelpController@anonymity');
	Route::get('/help', 'HelpController@help');
});

Route::group(['prefix' => '/theme'], function()
{
    Route::get('/',      'ThemeController@index');
    Route::get('/dark',  'ThemeController@dark');
    Route::get('/light', 'ThemeController@light');
    Route::get('/tiles', 'ThemeController@tiles');
});

Route::get('/assets/prod/{filename}', function($filename) {
    return Bust::css("/assets/prod/$filename");
});
Route::get('/assets/css/{filename}', function($filename) {
    return Bust::css("/assets/css/$filename");
});
Route::get('/assets/css/themes/{filename}', function($filename) {
    return Bust::css("/assets/css/themes/$filename");
});
Route::get('/assets/css/prefs/{filename}', function($filename) {
    return Bust::css("/assets/css/prefs/$filename");
});

App::make('cachebuster.StripSessionCookiesFilter')->addPattern('|css/|');

App::missing(function(Exception $exception)
{
    if(Request::is('*/.json')) {
		return Response::json(['error' => 'not found'], 404);
	}

	return View::make('page.system.404', [
		'message' => $exception->getMessage(),
		'sections' => Section::get()
	]);
});

App::down(function()
{
    return Response::view('page.system.maintenance', array(), 503);
});

Event::listen('auth.token.valid', function($user)
{
  Auth::setUser($user);
});

App::error(function(AuthTokenNotAuthorizedException $exception) {
    return Response::json(array('error' => $exception->getMessage()), $exception->getCode());
});

App::error(function(TooManyRequestsHttpException $exception)
{
     if(Request::is('*/.json')) {
        return Response::json(['error' => 'rate limit hit'], 404);
    }

    return View::make("page.system.429", [
        'message' => 'Calm down.',
        'sections' => Section::get()
    ]);
});