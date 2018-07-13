<?php
namespace App\Cme\Web\Controllers;

use CmeData\TemplateData;
use CmeKernel\Core\CmeKernel;
use CmeKernel\Exceptions\InvalidDataException;
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class TemplatesController extends BaseController
{
  public function index()
  {
    $data['templates'] = CmeKernel::Template()->all();
    return View::make('templates.list', $data);
  }

  public function neww()
  {
    $data = Session::get('formData', ['input' => null, 'errors' => null]);
    return View::make('templates.new', $data);
  }

  public function add()
  {
    $data = TemplateData::hydrate(Input::all());
    try
    {
      CmeKernel::Template()->create($data);
      return Redirect::to('/templates');
    }
    catch(InvalidDataException $e)
    {
      return Redirect::to('/templates/new')->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $data->getValidationErrors()
        ]
      );
    }
  }

  public function edit($id)
  {
    $template = CmeKernel::Template()->get($id);
    if($template)
    {
      $data['template'] = $template;
      $data             = array_merge(
        $data,
        Session::get('formData', ['input' => null, 'errors' => null])
      );
      return View::make('templates.edit', $data);
    }

    return Redirect::route('templates');
  }

  public function preview($id)
  {
    $data['template'] = CmeKernel::Template()->get($id);
    return View::make('templates.preview', $data);
  }

  public function content($id)
  {
    $template = CmeKernel::Template()->get($id);
    if($template)
    {
      echo $template->content;
    }
    else
    {
      //show 404 page
      echo "";
    }
  }

  public function update()
  {
    $data = TemplateData::hydrate(Input::all());
    try
    {
      CmeKernel::Template()->update($data);
      return Redirect::to('/templates/preview/' . $data->id);
    }
    catch(InvalidDataException $e)
    {
      return Redirect::to('/templates/edit/' . $data->id)->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $data->getValidationErrors()
        ]
      );
    }
  }

  public function delete()
  {
    $id = Route::input('id');
    if(CmeKernel::Template()->exists($id))
    {
      CmeKernel::Template()->delete($id);
    }
    return Redirect::to('/templates');
  }
}
