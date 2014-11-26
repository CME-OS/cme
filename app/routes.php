<?php

Route::group(['before' => 'auth'], function(){
    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

    //brands
    Route::get('/brands', ['as' => 'brands', 'uses' => 'BrandsController@index']);
    Route::get('/brands/new', ['as' => 'brands.new', 'uses' => 'BrandsController@neww']);
    Route::get('/brands/campaigns/{brandId}', ['as' => 'brands.campaigns', 'uses' => 'BrandsController@campaigns']);

    Route::post('/brands/add', ['as' => 'brands.add.post', 'uses' => 'BrandsController@add']);

    //lists
    Route::get('/lists', ['as' => 'lists', 'uses' => 'ListsController@index']);
    Route::get('/lists/new', ['as' => 'lists.new', 'uses' => 'ListsController@neww']);
    Route::get('/lists/view/{id}', ['as' => 'lists.view', 'uses' => 'ListsController@view']);

    Route::post('/lists/add', ['as' => 'lists.new.post', 'uses' => 'ListsController@add']);
    Route::post('/lists/import/{type}', ['as' => 'lists.import.post', 'uses' => 'ListsController@import']);

    //campaigns
    Route::get('/campaigns', ['as' => 'campaigns', 'uses' => 'CampaignsController@index']);
    Route::get('/campaigns/new', ['as' => 'campaign.new', 'uses' => 'CampaignsController@neww']);
    Route::get('/campaigns/edit/{id}', ['as' => 'campaign.edit', 'uses'=> 'CampaignsController@edit']);
    Route::get('/campaigns/delete/{id}', ['as' => 'campaign.delete', 'uses' => 'CampaignsController@delete']);
    Route::get('/campaigns/preview/{id}', ['as' => 'campaign.preview', 'uses' => 'CampaignsController@preview']);
    Route::get('/campaigns/send', ['as' => 'campaign.send', 'uses' => 'CampaignsController@send']);
    Route::post('/campaigns/test', ['as' => 'campaign.test', 'uses' => 'CampaignsController@test']);

    Route::post('/campaigns/add', ['as' => 'campaigns.add.post', 'uses' => 'CampaignsController@add']);
    Route::post('/campaigns/update', ['as' => 'campaigns.update.post', 'uses' => 'CampaignsController@update']);

    //queues
    Route::get('/queues', ['as' => 'queues', 'uses' => 'QueuesController@index']);

    //analytics
    Route::get('/analytics', 'AnalyticsController@index');

    //users
    Route::get('/users', 'UsersController@index');
});

//login
Route::get('/login', ['as' => 'login', 'uses' => 'LoginController@login']);
Route::post('/login', 'LoginController@authenticate');

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

Route::post('/ph', 'CampaignsController@getPlaceHolders');
Route::post('/ds', 'CampaignsController@getDefaultSender');
