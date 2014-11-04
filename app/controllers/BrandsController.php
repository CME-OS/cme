<?php
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Redirect;

class BrandsController extends BaseController
{
  public function index()
  {
    $result = DB::select("SELECT * FROM brands");

    $data['brands'] = $result;

    return View::make('brands.list', $data);
  }

  public function neww()
  {
    return View::make('brands.new');
  }

  public function add()
  {
    $data = Input::all();
    DB::table('brands')->insert($data);

    return Redirect::to('/brands');
  }

  public function campaigns()
  {
    $brandId = Route::input('brandId');
    $result = DB::select(
      sprintf("SELECT * FROM campaigns WHERE brand_id=%d", $brandId)
    );

    var_dump($result);
  }
}
