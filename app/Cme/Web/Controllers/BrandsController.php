<?php
namespace Cme\Web\Controllers;

use Cme\Brands\Validation\AddBrandValidation;
use Cme\Models\CMEBrand;
use CmeData\BrandData;
use CmeKernel\Core\CmeKernel;
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
    $result         = CmeKernel::Brand()->all();
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
    CmeKernel::Brand()->create(BrandData::hydrate($data));
    return Redirect::route('brands');
  }

  public function edit($id)
  {
    $data['brand'] = CmeKernel::Brand()->get($id);
    return View::make('brands.edit', $data);
  }

  public function update()
  {
    $data = Input::all();
    CmeKernel::Brand()->update(BrandData::hydrate($data));
    return Redirect::to('/brands/edit/' . $data['id'])->with(
      'msg',
      'Brand has been updated'
    );
  }

  public function delete($id)
  {
    CmeKernel::Brand()->delete($id);
    return Redirect::to('/brands')->with('msg', 'Brand has been deleted');
  }

  public function campaigns($brandId)
  {
    $data['campaigns'] = CmeKernel::Brand()->campaigns($brandId);
    $data['labelClasses'] = [
      'Pending' => 'label-default',
      'Queuing' => 'label-info',
      'Queued'  => 'label-info',
      'Sending' => 'label-primary',
      'Sent'    => 'label-success',
      'Paused'  => 'label-warning',
      'Aborted' => 'label-danger'
    ];
    return View::make('campaigns.list', $data);
  }
}
