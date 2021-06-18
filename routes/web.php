<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');
Route::get('/signup','UsersController@create')->name('signup');
Route::resource('users','UsersController');
Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destroy')->name('logout');
//粉丝页面路由
Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');
//关注和取消路由
Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');
//显示文章和相应的评论
Route::get('/status/show/{status_id}', function (\App\Status $status) {
    $status->load('comments.owner');
    $comments = $status->getComments();
    $comments['root'] = $comments[$status['id']];
    unset($comments[$status['id']]);
    return view('status.show', compact('status', 'comments'));
});

//用户进行评论
Route::post('status/{status_id}/comments', function (\App\Status $status) {
    $status->comments()->create([
        'status_id' => $status['id'],
        'content' => request('content'),
        'user_id' => \Auth::id(),
        'parent_id' => request('parent_id', null),
    ]);
    return back();
});