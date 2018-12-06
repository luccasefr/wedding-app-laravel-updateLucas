<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/user','UserController@get')->middleware('auth:api');
Route::post('/user','UserController@update')->middleware('auth:api');
Route::post('/user/sensible-word','SensibleWordController@create')->middleware('auth:api');
Route::get('/user/sensible-words','SensibleWordController@index')->middleware('auth:api');
Route::post('/user/gift','GiftListController@create')->middleware('auth:api');
Route::get('/user/gifts','GiftListController@getAll')->middleware('auth:api');
Route::get('/user/posts-for-aprove','PostController@forAprove')->middleware('auth:api');
Route::post('/user/event','EventController@create')->middleware('auth:api');
Route::get('/user/qrcodes','QrCodeImageController@getAll')->middleware('auth:api');
Route::get('/user/invites','InviteController@getAll')->middleware('auth:api');
Route::post('/user/tiepiece','TiepieceController@create')->middleware('auth:api');


Route::get('/events','EventController@index')->middleware('custom.auth');
Route::delete('/event/{event}','EventController@delete')->middleware('auth:api');
Route::post('/event/{event}/confirm','EventController@confirm')->middleware('auth.guest');
Route::post('/event/{event}/invite','EventController@updateInvites')->middleware('auth:api');

Route::delete('/gift/{gift}','GiftListController@delete')->middleware('auth:api');

Route::delete('/sensible-word/{sensibleWord}','SensibleWordController@delete')->middleware('auth:api');

Route::post('/guest','GuestController@create')->middleware('auth:api');
Route::get('/guests','GuestController@index')->middleware('auth:api');
Route::get('/guest','GuestController@get')->middleware('auth.guest');
Route::get('/guest/wedding','GuestController@getWedding')->middleware('auth.guest');
Route::get('/guest/{guest}/invite','InviteController@get')->middleware('auth:api');
Route::get('/guest/{guest}/qrcode','QrCodeImageController@get')->middleware('auth:api');
Route::delete('/guest/{guest}','GuestController@delete')->middleware('auth:api');
Route::get('/guest/{guest}/qrcode-pdf','QrCodeImageController@getPdf')->middleware('auth:api');
Route::post('/guest/confirm-presence','GuestController@confirm')->middleware('auth.guest');
Route::post('/guest/unconfirm-presence','GuestController@unconfirm')->middleware('auth.guest');
Route::post('/guest/{guest}/update','GuestController@update')->middleware('auth.guest');
Route::get('/guest/{guest}/profile-img','GuestController@image')->middleware('custom.auth');
Route::get('/guest/{guest}/image1','GuestController@image1')->middleware('custom.auth');
Route::get('/guest/{guest}/image2','GuestController@image2')->middleware('custom.auth');
Route::get('/guest/{guest}/image3','GuestController@image3')->middleware('custom.auth');
Route::post('/guest/{guest}/upload-photo','GuestController@uploadPhoto')->middleware('auth.guest');
Route::post('/guest/{guest}/like','GuestController@guestLike')->middleware('auth.guest');
Route::post('/guest/{match}/match','GuestController@guestMatch')->middleware('auth.guest');
Route::get('/guest/singles','GuestController@singles')->middleware('auth.guest');
Route::get('/guest/match-conversation','GuestController@guestConversations')->middleware('auth.guest');
Route::get('/guest/{match}/match-conversation','GuestController@guestConversationsMatch')->middleware('auth.guest');
Route::post('/guest/register-fcm-token','GuestController@registerFcmToken')->middleware('auth.guest');

Route::post('/puzzle/image','PuzzleImageController@create')->middleware('auth:api');
Route::delete('/puzzle/image/{image}','PuzzleImageController@delete')->middleware('auth:api');
Route::get('/puzzle/images','PuzzleImageController@index')->middleware('auth:api');

Route::post('/memory/image','MemoryGameController@create')->middleware('auth:api');
Route::delete('/memory/image/{image}','MemoryGameController@delete')->middleware('auth:api');
Route::get('/memory/images','MemoryGameController@index')->middleware('auth:api');

Route::post('/chroma/image','ChromaImageController@create')->middleware('auth:api');
Route::delete('/chroma/image/{image}','ChromaImageController@delete')->middleware('auth:api');
Route::get('/chroma/images','ChromaImageController@index')->middleware('auth:api');

Route::post('/action','ActionController@create')->middleware('auth:api');
Route::get('/actions','ActionController@index')->middleware('auth:api');

Route::post('/post','PostController@create');
Route::get('/posts','PostController@index')->middleware('custom.auth');
Route::get('/post/{post}/image','PostController@getImage')->middleware('custom.auth');
Route::delete('/post/{post}','PostController@delete')->middleware('custom.auth');
Route::post('/post/{post}/aprove','PostController@aprove')->middleware('auth:api');
Route::post('/post/{post}/like','PostController@postLike')->middleware('auth.guest');
Route::get('/post/{post}/likes','PostController@likes')->middleware('custom.auth');

Route::post('/invite','InviteController@create')->middleware('auth:api');
Route::post('/invite/text','InviteController@createText')->middleware('auth:api');
Route::post('/invite/text/{text}','InviteController@updateText')->middleware('auth:api');
Route::delete('/invite/text/{text}','InviteController@deleteText')->middleware('auth:api');
Route::put('/invite','InviteController@update')->middleware('auth:api');

Route::post('/invite/image','InviteImageController@create')->middleware('auth:api');
Route::delete('/invite/image/{image}','InviteImageController@delete')->middleware('auth:api');
Route::get('/invite/image/{image}','InviteImageController@getImage')->middleware('auth:api');
Route::post('/invite/image/{image}','InviteImageController@update')->middleware('auth:api');

Route::post('/quiz/question','QuizController@create')->middleware('auth:api');
Route::delete('/quiz/question/{question}','QuizController@delete')->middleware('auth:api');

Route::get('/songs','SongController@index')->middleware('custom.auth');
Route::post('/songs','SongController@create')->middleware('auth.guest');
Route::post('/song/{song}/like','SongController@like')->middleware('auth.guest');
Route::delete('/song/{song}','SongController@delete')->middleware('custom.auth');

Route::get('/fonts','UserController@fonts');

Route::get('/tiebuy','TiebuyController@index')->middleware('auth:api');
Route::post('/tiebuy','TiebuyController@create')->middleware('auth:guest');

Route::get('/tiepiece','TiepieceController@index')->middleware('auth:api');
Route::delete('/tiepiece/{tiepiece}','TiepieceController@delete')->middleware('auth:api');
// Route::post('/tiepiece','TiepieceController@create')->middleware('auth:api');

// Route::resource('/tiepiece', 'TiepieceController')->middleware('auth:api');