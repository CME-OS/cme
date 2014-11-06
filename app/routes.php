<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');

//brands
Route::get('/brands', 'BrandsController@index');
Route::get('/brands/new', 'BrandsController@neww');
Route::post('/brands/add', 'BrandsController@add');
Route::get('/brands/campaigns/{brandId}', 'BrandsController@campaigns');

//lists
Route::get('/lists', 'ListsController@index');
Route::get('/lists/new', 'ListsController@neww');
Route::post('/lists/add', 'ListsController@add');
Route::get('/lists/view/{id}', 'ListsController@view');
Route::post('/lists/import/{type}', 'ListsController@import');

//campaigns
Route::get('/campaigns', 'CampaignsController@index');
Route::get('/campaigns/new', 'CampaignsController@neww');
Route::post('/campaigns/add', 'CampaignsController@add');
Route::get('/campaigns/edit/{id}', 'CampaignsController@edit');
Route::get('/campaigns/delete/{id}', 'CampaignsController@delete');
Route::post('/campaigns/update', 'CampaignsController@update');
Route::get('/campaigns/preview/{id}', 'CampaignsController@preview');
Route::get('/campaigns/send', 'CampaignsController@send');

//queues
Route::get('/queues', 'QueuesController@index');


Route::get('/analytics', 'AnalyticsController@index');
Route::get('/users', 'UsersController@index');
Route::post(
  '/test',
  function ()
  {

    $data = [];
    for($i = 0; $i <= 10; $i++)
    {
      $user['email']      = "test$i@example.com";
      $user['first_name'] = "First$i";
      $user['last_name']  = "Last$i";

      $data[] = $user;
    }

    $return = array_slice(
      $data,
      \Illuminate\Support\Facades\Input::get('start', 0),
      \Illuminate\Support\Facades\Input::get('limit', 1000)
    );
    return \Illuminate\Support\Facades\Response::json($return);
  }
);

Route::post(
  '/test2',
  function ()
  {

    $data = [];

    $user['email']    = "fobilow@gmail.com";
    $user['name']     = "Okechukwu";
    $user['discount'] = "50";

    $data[] = $user;

    $user['email']    = "oke.ugwu@simplifye.com";
    $user['name']     = "John";
    $user['discount'] = "50";

    $data[] = $user;

    return \Illuminate\Support\Facades\Response::json($data);
  }
);

Route::post('/ph', 'CampaignsController@getPlaceHolders');
Route::post('/ds', 'CampaignsController@getDefaultSender');
