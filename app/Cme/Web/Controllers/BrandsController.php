<?php
namespace Cme\Web\Controllers;

use Cme\Brands\Validation\AddBrandValidation;
use Cme\Models\CMEBrand;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class BrandsController extends BaseController
{
  /**
   * @var AddBrandValidation
   */
  private $addBrandValidation;

  /**
   * @param AddBrandValidation $addBrandValidation
   */
  public function __construct(AddBrandValidation $addBrandValidation)
  {
    $this->addBrandValidation = $addBrandValidation;
  }

  public function index()
  {
    $result = CMEBrand::getAllActive();

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

    $this->addBrandValidation->validate($data);

    $data['brand_created'] = time();
    DB::table('brands')->insert($data);

    return Redirect::route('brands');
  }

  public function edit($id)
  {
    $data['brand'] = CMEBrand::find($id);

    return View::make('brands.edit', $data);
  }

  public function update()
  {
    $data = Input::all();
    DB::table('brands')->where('id', '=', $data['id'])
      ->update($data);

    return Redirect::to('/brands/edit/' . $data['id'])->with(
      'msg',
      'Brand has been updated'
    );
  }

  public function delete($id)
  {
    $data['brand_deleted_at'] = time();
    DB::table('brands')->where('id', '=', $id)
      ->update($data);

    return Redirect::to('/brands')->with('msg', 'Brand has been deleted');
  }

  public function campaigns($brandId)
  {
    $brand             = CMEBrand::find($brandId);
    $data['campaigns'] = $brand->campaigns;

    return View::make('campaigns.list', $data);
  }
}
