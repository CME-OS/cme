<?php
namespace Cme\Web\Controllers;

use CmeData\TemplateData;
use CmeKernel\Core\CmeKernel;
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
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
    return View::make('templates.new');
  }

  public function add()
  {
    $data     = Input::all();
    CmeKernel::Template()->create(TemplateData::hydrate($data));
    return Redirect::to('/templates');
  }

  public function edit($id)
  {
    $template = CmeKernel::Template()->get($id);
    if($template)
    {
      $data['template']      = $template;
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
    $data     = Input::all();
    CmeKernel::Template()->update(TemplateData::hydrate($data));
    return Redirect::to('/templates/preview/' . $data['id']);
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
