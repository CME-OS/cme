<?php
use Cme\Brands\Validation\AddBrandValidation;
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Redirect;

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

    $this->addBrandValidation->validate($data);

    DB::table('brands')->insert($data);

    return Redirect::route('brands');
  }

  public function campaigns($brandId)
  {
    $data['campaigns'] = DB::select(
      sprintf("SELECT * FROM campaigns WHERE brand_id=%d", $brandId)
    );

    return View::make('campaigns.list', $data);
  }
}
