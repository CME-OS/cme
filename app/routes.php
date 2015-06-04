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
    Route::get('/lists/new-subscriber/{id}', ['as' => 'lists.new-subscriber', 'uses' => 'Cme\Web\Controllers\ListsController@newSubscriber']);
    Route::get('/lists/{listId}/delete-subscriber/{id}', ['as' => 'lists.delete-subscriber', 'uses' => 'Cme\Web\Controllers\ListsController@deleteSubscriber']);

    Route::post('/lists/add', ['as' => 'lists.new.post', 'uses' => 'Cme\Web\Controllers\ListsController@add']);
    Route::post('/lists/update', ['as' => 'lists.update.post', 'uses' => 'Cme\Web\Controllers\ListsController@update']);
    Route::post('/lists/import/{type}', ['as' => 'lists.import.post', 'uses' => 'Cme\Web\Controllers\ListsController@import']);
    Route::post('/lists/add-subscriber', ['as' => 'lists.add-subscriber', 'uses' => 'Cme\Web\Controllers\ListsController@addSubscriber']);

    //campaigns
    Route::get('/campaigns', ['as' => 'campaigns', 'uses' => 'Cme\Web\Controllers\CampaignsController@index']);
    Route::get('/campaigns/new', ['as' => 'campaign.new', 'uses' => 'Cme\Web\Controllers\CampaignsController@neww']);
    Route::post('/campaigns/new', ['as' => 'campaign.new', 'uses' => 'Cme\Web\Controllers\CampaignsController@neww']);
    Route::get('/campaigns/new/{step}', ['as' => 'campaign.new-stepped', 'uses' => 'Cme\Web\Controllers\CampaignsController@neww']);
    Route::get('/campaigns/copy/{id}', ['as' => 'campaign.copy', 'uses'=> 'Cme\Web\Controllers\CampaignsController@copy']);
    Route::get('/campaigns/edit/{id}', ['as' => 'campaign.edit', 'uses'=> 'Cme\Web\Controllers\CampaignsController@edit']);
    Route::get('/campaigns/delete/{id}', ['as' => 'campaign.delete', 'uses' => 'Cme\Web\Controllers\CampaignsController@delete']);
    Route::get('/campaigns/preview/{id}', ['as' => 'campaign.preview', 'uses' => 'Cme\Web\Controllers\CampaignsController@preview']);
    Route::get('/campaigns/content/{id}', ['as' => 'campaign.content', 'uses' => 'Cme\Web\Controllers\CampaignsController@content']);
    Route::post('/campaigns/send', ['as' => 'campaign.send', 'uses' => 'Cme\Web\Controllers\CampaignsController@send']);
    Route::post('/campaigns/pause', ['as' => 'campaign.pause', 'uses' => 'Cme\Web\Controllers\CampaignsController@pause']);
    Route::post('/campaigns/resume', ['as' => 'campaign.resume', 'uses' => 'Cme\Web\Controllers\CampaignsController@resume']);
    Route::post('/campaigns/abort', ['as' => 'campaign.abort', 'uses' => 'Cme\Web\Controllers\CampaignsController@abort']);
    Route::post('/campaigns/test', ['as' => 'campaign.test', 'uses' => 'Cme\Web\Controllers\CampaignsController@test']);

    Route::post('/campaigns/add', ['as' => 'campaigns.add.post', 'uses' => 'Cme\Web\Controllers\CampaignsController@add']);
    Route::post('/campaigns/update', ['as' => 'campaigns.update.post', 'uses' => 'Cme\Web\Controllers\CampaignsController@update']);

    //templates
    Route::get('/templates', ['as' => 'templates', 'uses' => 'Cme\Web\Controllers\TemplatesController@index']);
    Route::get('/templates/new', ['as' => 'template.new', 'uses' => 'Cme\Web\Controllers\TemplatesController@neww']);
    Route::get('/templates/view/{id}', ['as' => 'template.view', 'uses' => 'Cme\Web\Controllers\TemplatesController@view']);
    Route::get('/templates/edit/{id}', ['as' => 'template.edit', 'uses' => 'Cme\Web\Controllers\TemplatesController@edit']);
    Route::get('/templates/delete/{id}', ['as' => 'template.delete', 'uses' => 'Cme\Web\Controllers\TemplatesController@delete']);
    Route::get('/templates/preview/{id}', ['as' => 'template.preview', 'uses' => 'Cme\Web\Controllers\TemplatesController@preview']);
    Route::get('/templates/content/{id}', ['as' => 'template.content', 'uses' => 'Cme\Web\Controllers\TemplatesController@content']);
    Route::post('/templates/add', ['as' => 'template.add.post', 'uses' => 'Cme\Web\Controllers\TemplatesController@add']);
    Route::post('/templates/update', ['as' => 'template.update.post', 'uses' => 'Cme\Web\Controllers\TemplatesController@update']);

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

    //SMTP Providers
    Route::get('/smtp-providers', ['as' => 'smtp-providers', 'uses' => 'Cme\Web\Controllers\SmtpProvidersController@index']);
    Route::get('/smtp-providers/new', ['as' => 'smtp-providers.new', 'uses' => 'Cme\Web\Controllers\SmtpProvidersController@neww']);
    Route::get('/smtp-providers/view/{id}', ['as' => 'smtp-providers.view', 'uses' => 'Cme\Web\Controllers\SmtpProvidersController@view']);
    Route::get('/smtp-providers/edit/{id}', ['as' => 'smtp-providers.edit', 'uses' => 'Cme\Web\Controllers\SmtpProvidersController@edit']);
    Route::get('/smtp-providers/default/{id}', ['as' => 'smtp-providers.default', 'uses' => 'Cme\Web\Controllers\SmtpProvidersController@setDefault']);
    Route::get('/smtp-providers/delete/{id}', ['as' => 'smtp-providers.delete', 'uses' => 'Cme\Web\Controllers\SmtpProvidersController@delete']);

    Route::post('/smtp-providers/add', ['as' => 'smtp-providers.add.post', 'uses' => 'Cme\Web\Controllers\SmtpProvidersController@add']);
    Route::post('/smtp-providers/update', ['as' => 'smtp-providers.update.post', 'uses' => 'Cme\Web\Controllers\SmtpProvidersController@update']);

    //AJAX
    Route::get('/ph', 'Cme\Web\Controllers\CampaignsController@getPlaceHolders');
    Route::post('/ds', 'Cme\Web\Controllers\CampaignsController@getDefaultSender');
    Route::post('/so', 'Cme\Web\Controllers\CampaignsController@getSegmentOptions');
    Route::post('/tc', 'Cme\Web\Controllers\CampaignsController@getTemplate');
    Route::post('/ls', 'Cme\Web\Controllers\ListsController@subscribers');
    Route::post('/lsearch', 'Cme\Web\Controllers\ListsController@search');
});

//login
Route::get('/login', ['as' => 'login', 'uses' => 'Cme\Web\Controllers\LoginController@login']);
Route::get('/logout', 'Cme\Web\Controllers\LoginController@logout');
Route::post('/login', 'Cme\Web\Controllers\LoginController@authenticate');

//tracking
Route::get('/track/open/{source}', 'Cme\Web\Controllers\TrackingController@trackOpen');
Route::get('/track/unsubscribe/{source}/{redirect}', 'Cme\Web\Controllers\TrackingController@trackUnsubscribe');
Route::get('/track/click/{source}/{redirect}', 'Cme\Web\Controllers\TrackingController@trackClick');

//setup
Route::get('/setup', 'Cme\Web\Controllers\SetupController@index');
Route::get('/setup/{step}', 'Cme\Web\Controllers\SetupController@index');
Route::get('/setup/skip', 'Cme\Web\Controllers\SetupController@skip');
Route::post('/setup/install', 'Cme\Web\Controllers\SetupController@install');
Route::get('/installed', 'Cme\Web\Controllers\SetupController@installed');


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
