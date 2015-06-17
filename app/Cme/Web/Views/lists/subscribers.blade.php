@extends('layouts.default')
@section('content')
<h1 class="page-header"><?= $list->name ?> List <small>(<?= number_format($list->getSize()) ?> subscribers)</small></h1>
<div class="row alert alert-info" style="border-radius: 0;">
  <div class="col-md-12">
    <a href="/lists/new-subscriber/<?= $list->id ?>" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Add a subscriber</a>
    <a onclick="$('#import-forms').slideToggle();" class="btn btn-primary"><?= ($subscribers)? "Import more subscribers" : "Import subscribers" ?></a>
    <div class="row" id="import-forms" style="display:none; margin-top:10px;">
      <div class="col-md-3">
        <form class="form-inline" role="form" action="/lists/import/api" method="post" style="padding:10px;">
          <div class="form-group">
            <input type="hidden" name="listId" value="<?= $list->id ?>">
            <input type="text" name="endpoint" class="form-control" id="brand-name" placeholder="http://domain.com/list.php" value="<?= $list->endpoint ?>">
          </div>
          <button type="submit" class="btn btn-success pull-right">Import From API</button>
        </form>
        <form class="form-inline" role="form" action="/lists/import/csv" method="post" enctype="multipart/form-data" style="padding:10px; margin-top:5px; border-top:1px solid #ccc;">
          <input type="hidden" name="listId" value="<?= $list->id ?>">
          <div class="form-group">
            <input type="file" name="listFile" id="list-csv">
          </div>
          <button type="submit" class="btn btn-info pull-right">Import From CSV</button>
          <div class="clearfix"></div>
        </form>
      </div>
      <div class="col-md-offset-9">
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">

      <div class="row">
        <div class="col-md-10">
          <form action="" style="margin-top:20px;">
            <input type="text" class="form-control" placeholder="Search this List" id="list-search"/>
          </form>
        </div>
        <div class="col-md-2">
          <div class="pull-right"><?php echo $pager->links(); ?></div>
        </div>
      </div>
      <table class="table table-striped table-hover" id="subscribers-list">
        <thead>
        <tr>
          <?php foreach($columns as $c): ?>
          <th><?= $c['name'] ?></th>
          <?php endforeach; ?>
          <th></th>
        </tr>
        </thead>
      </table>
  </div>
</div>
<script>

  function fetchSubscribers(listId, page)
  {
    var postData = {
      'list_id' : listId,
      'page' : page
    };
    $.post('/ls', postData, function(data){
      buildRows(data);
    });
  }

  function search(listId, q)
  {
    var postData = {
      'list_id' : listId,
      'q' : q
    };
    return $.post('/lsearch', postData, function(data){
      buildRows(data);
    });
  }

  function buildRows(data)
  {
    $('#subscribers-list tbody').remove();
    for(var i = 0; i < data.subscribers.length; i++)
    {
      var row = '<tr>';
      for(var x = 0; x < data.columns.length; x++)
      {
        row += '<td>' + data.subscribers[i][data.columns[x]['name']] + '</td>';
      }
      row += '<td><a href="/lists/<?= $list->id ?>/delete-subscriber/' + data.subscribers[i]['id'] +'" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a></td>';
      row += '</tr>';
      $('#subscribers-list').append(row);
    }
  }

  $('#list-search').on('keyup', function(){
    var v = $(this).val();
    if(window.cme.searchTimeout)
    {
      clearTimeout(window.cme.searchTimeout);
    }
    if(v.length >= 2)
    {
      window.cme.searchTimeout = setTimeout(function(){
        search(<?= $list->id ?>, v)
      }, 1000);
    }
    else if(v == "")
    {
      fetchSubscribers(<?= $list->id ?>, <?= $page ?>);
    }
  });

  fetchSubscribers(<?= $list->id ?>, <?= $page ?>);

</script>
@stop
