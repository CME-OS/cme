<?php

Route::group(['before' => 'auth'], function(){
    Route::get('/', ['as' => 'home', 'uses' => 'Cme\Web\Controllers\HomeController@index']);

    //brands
    Route::get('/brands', ['as' => 'brands', 'uses' => 'Cme\Web\Controllers\BrandsController@index']);
    Route::get('/brands/new', ['as' => 'brands.new', 'uses' => 'Cme\Web\Controllers\BrandsController@neww']);
    Route::get('/brands/view/{id}', ['as' => 'brands.view', 'uses' => 'Cme\Web\Controllers\BrandsController@view']);
    Route::get('/brands/edit/{id}', ['as' => 'brands.edit', 'uses' => 'Cme\Web\Controllers\BrandsController@edit']);
    Route::get('/brands/delete/{id}', ['as' => 'brands.delete', 'uses' => 'Cme\Web\Controllers\BrandsController@delete']);

    Route::get('/brands/campaigns/{brandId}', ['as' => 'brands.campaigns', 'uses' => 'Cme\Web\Controllers\BrandsController@campaigns']);

    Route::post('/brands/add', ['as' => 'brands.add.post', 'uses' => 'Cme\Web\Controllers\BrandsController@add']);
    Route::post('/brands/update', ['as' => 'brands.update.post', 'uses' => 'Cme\Web\Controllers\BrandsController@update']);

    //lists
    Route::get('/lists', ['as' => 'lists', 'uses' => 'Cme\Web\Controllers\ListsController@index']);
    Route::get('/lists/new', ['as' => 'lists.new', 'uses' => 'Cme\Web\Controllers\ListsController@neww']);
    Route::get('/lists/view/{id}', ['as' => 'lists.view', 'uses' => 'Cme\Web\Controllers\ListsController@view']);
    Route::get('/lists/edit/{id}', ['as' => 'lists.edit', 'uses' => 'Cme\Web\Controllers\ListsController@edit']);
    Route::get('/lists/delete/{id}', ['as' => 'lists.delete', 'uses' => 'Cme\Web\Controllers\ListsController@delete']);

    Route::post('/lists/add', ['as' => 'lists.new.post', 'uses' => 'Cme\Web\Controllers\ListsController@add']);
    Route::post('/lists/update', ['as' => 'lists.update.post', 'uses' => 'Cme\Web\Controllers\ListsController@update']);
    Route::post('/lists/import/{type}', ['as' => 'lists.import.post', 'uses' => 'Cme\Web\Controllers\ListsController@import']);

    //campaigns
    Route::get('/campaigns', ['as' => 'campaigns', 'uses' => 'Cme\Web\Controllers\CampaignsController@index']);
    Route::get('/campaigns/new', ['as' => 'campaign.new', 'uses' => 'Cme\Web\Controllers\CampaignsController@neww']);
    Route::post('/campaigns/new', ['as' => 'campaign.new', 'uses' => 'Cme\Web\Controllers\CampaignsController@neww']);
    Route::get('/campaigns/edit/{id}', ['as' => 'campaign.edit', 'uses'=> 'Cme\Web\Controllers\CampaignsController@edit']);
    Route::get('/campaigns/delete/{id}', ['as' => 'campaign.delete', 'uses' => 'Cme\Web\Controllers\CampaignsController@delete']);
    Route::get('/campaigns/preview/{id}', ['as' => 'campaign.preview', 'uses' => 'Cme\Web\Controllers\CampaignsController@preview']);
    Route::get('/campaigns/send', ['as' => 'campaign.send', 'uses' => 'Cme\Web\Controllers\CampaignsController@send']);
    Route::post('/campaigns/test', ['as' => 'campaign.test', 'uses' => 'Cme\Web\Controllers\CampaignsController@test']);

    Route::post('/campaigns/add', ['as' => 'campaigns.add.post', 'uses' => 'Cme\Web\Controllers\CampaignsController@add']);
    Route::post('/campaigns/update', ['as' => 'campaigns.update.post', 'uses' => 'Cme\Web\Controllers\CampaignsController@update']);

    //queues
    Route::get('/queues', ['as' => 'queues', 'uses' => 'Cme\Web\Controllers\QueuesController@index']);

    //analytics
    Route::get('/analytics', 'Cme\Web\Controllers\AnalyticsController@index');
    Route::get('/analytics/{id}', ['as' => 'analytics.view', 'uses' => 'Cme\Web\Controllers\AnalyticsController@index']);

    //users
    Route::get('/users', 'Cme\Web\Controllers\UsersController@index');
    Route::get('/users/new', ['as' => 'users.new', 'uses' => 'Cme\Web\Controllers\UsersController@neww']);
    Route::get('/users/view/{id}', ['as' => 'users.view', 'uses' => 'Cme\Web\Controllers\UsersController@view']);
    Route::get('/users/edit/{id}', ['as' => 'users.edit', 'uses' => 'Cme\Web\Controllers\UsersController@edit']);
    Route::get('/users/delete/{id}', ['as' => 'users.delete', 'uses' => 'Cme\Web\Controllers\UsersController@delete']);

    Route::post('/users/add', ['as' => 'users.new.post', 'uses' => 'Cme\Web\Controllers\UsersController@add']);
    Route::post('/users/update', ['as' => 'users.update.post', 'uses' => 'Cme\Web\Controllers\UsersController@update']);
});

//login
Route::get('/login', ['as' => 'login', 'uses' => 'Cme\Web\Controllers\LoginController@login']);
Route::post('/login', 'Cme\Web\Controllers\LoginController@authenticate');

//tracking
Route::get('/track/open/{source}', 'TrackingController@trackOpen');
Route::get('/track/unsubscribe/{source}/{redirect}', 'TrackingController@trackUnsubscribe');



Route::post(
  '/test',
  function ()
  {

    $data = [];
    for($i = 0; $i <= 10; $i++)
    {
      $user['email']      = "cme_test$i@mailinator.com";
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

Route::post('/ph', 'Cme\Web\Controllers\CampaignsController@getPlaceHolders');
Route::post('/ds', 'Cme\Web\Controllers\CampaignsController@getDefaultSender');
Route::post('/so', 'Cme\Web\Controllers\CampaignsController@getSegmentOptions');
