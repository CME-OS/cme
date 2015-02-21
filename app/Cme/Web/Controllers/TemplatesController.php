<?php
namespace Cme\Web\Controllers;

use Cme\Models\CMETemplate;
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class TemplatesController extends BaseController
{
  public function index()
  {
    $data['templates'] = CMETemplate::getAllActive();

    return View::make('templates.list', $data);
  }

  public function neww()
  {
    return View::make('templates.new');
  }

  public function add()
  {
    $data     = Input::all();
    CMETemplate::saveData($data);
    return Redirect::to('/templates');
  }

  public function edit($id)
  {
    $template = CMETemplate::find($id);
    if($template)
    {
      $data['template']      = $template;
      return View::make('templates.edit', $data);
    }

    return Redirect::route('templates');
  }

  public function preview($id)
  {
    $template         = CMETemplate::find($id);
    $data['template'] = $template;
    return View::make('templates.preview', $data);
  }

  public function content($id)
  {
    $template = CMETemplate::find($id);
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
    CMETemplate::saveData($data);

    return Redirect::to('/templates/preview/' . $data['id']);
  }


  public function delete()
  {
    $id = Route::input('id');
    if(CMETemplate::find($id))
    {
      $data['id']         = $id;
      $data['deleted_at'] = time();
      CMETemplate::saveData($data);
    }
    return Redirect::to('/templates');
  }
}
