<?php
namespace App\Cme\Web\Controllers;

use App\Cme\Brands\Validation\AddBrandValidation;
use App\Cme\Models\CMEBrand;
use CmeData\BrandData;
use CmeKernel\Core\CmeKernel;
use CmeKernel\Exceptions\InvalidDataException;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class BrandsController extends BaseController
{
  public function index()
  {
    $result         = CmeKernel::Brand()->all();
    $data['brands'] = $result;
    return View::make('brands.list', $data);
  }

  public function neww()
  {
    $data = Session::get('formData', ['input' => null, 'errors' => null]);
    return View::make('brands.new', $data);
  }

  public function add()
  {
    dd(Input::all());
    $brandData = BrandData::hydrate(Input::all());
    try
    {
      CmeKernel::Brand()->create($brandData);
      return Redirect::route('brands');
    }
    catch(InvalidDataException $e)
    {
      return Redirect::route('brands.new')->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $brandData->getValidationErrors()
        ]
      );
    }
  }

  public function edit($id)
  {
    $data['brand']  = CmeKernel::Brand()->get($id);
    $data = array_merge(
      $data,
      Session::get('formData', ['input' => null, 'errors' => null])
    );

    return View::make('brands.edit', $data);
  }

  public function update()
  {
    $brandData = BrandData::hydrate(Input::all());
    try
    {
      CmeKernel::Brand()->update($brandData);
      return Redirect::to('/brands/edit/' . $brandData->id)->with(
        'msg',
        'Brand has been updated'
      );
    }
    catch(InvalidDataException $e)
    {
      return Redirect::to('brands/edit/' . $brandData->id)->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $brandData->getValidationErrors()
        ]
      );
    }
  }

  public function delete($id)
  {
    CmeKernel::Brand()->delete($id);
    return Redirect::to('/brands')->with('msg', 'Brand has been deleted');
  }

  public function campaigns($brandId)
  {
    $data['campaigns']    = CmeKernel::Brand()->campaigns($brandId);
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
