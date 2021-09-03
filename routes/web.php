<?php

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

Route::get('/', 'TopController@index');
Route::get('/usage', 'TopController@usage');
Route::post('/image/speaker',    'ImageController@registerSpeakerImage');
Route::post('/image/talk',    'ImageController@registerTalkImage');
Route::post('/image/companion',    'ImageController@companion');
Route::get('/image/list',    'ImageController@imageList');
Route::post('/image/change_speaker',    'ImageController@changeSpeaker');
Route::post('/speaker_list',    'TopController@speakerList');
Route::post('/speaker_categories',    'TopController@speakerCategories');
Route::post('/speaker_filter',    'TopController@speakerFilter');
Route::get('/talk',    'TalkController@index');
Route::get('/talk/template_list',    'TalkController@templateList');
Route::get('/talk/{talk_id?}',    'TalkController@index');
Route::post('/talk',    'TalkController@create');
Route::post('/talk/update',    'TalkController@update');
Route::post('/talk/alternatives',    'TalkController@alternatives');
Route::post('/talk/alternatives_return',    'TalkController@alternativesReturn');
Route::post('/talk/candidate',    'TalkController@candidate');
Route::post('/talk/change',    'TalkController@change');
Route::post('/talk/fixed',    'TalkController@fixed');
Route::post('/talk/renew', 'TalkController@renew');
Route::get('/movie',    'MovieController@index');
Route::post('/movie',    'MovieController@create');
Route::get('/movie/download',    'MovieController@downloadMovie');
Route::get('/screenshot/download',    'MovieController@downloadScreenshot');
Route::post('/movie/send_twitter',    'MovieController@sendTwitter');
Route::get('/movie/upload_twitter',    'MovieController@uploadTwitter');
Route::get('/movie/upload',    'MovieController@upload');
Route::get('/movie/mode_list',    'MovieController@modeList');
Route::post('/movie/create_screenshot',    'MovieController@createScreenshot');
Route::post('/movie/reset',    'MovieController@reset');
Route::get('/movie/title',    'MovieController@title');
Route::get('/error/{status_code}',    'TopController@error');


if(config('app.env') === 'production'){
    URL::forceScheme('https');
}
