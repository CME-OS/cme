@extends('layouts.default')
@section('content')
<h1 class="page-header"><?= $list->name ?> List</h1>
<?php if($subscribers): ?>
<div class="row alert alert-info" style="border-radius: 0;">
  <div class="col-md-12">
    <p style="font-weight: bold; font-size:18px;">Import more subscribers</p>
    <div class="row">
      <div class="col-md-5">
        <form class="form-inline" role="form" action="/lists/import/api" method="post">
          <div class="form-group">
            <input type="hidden" name="listId" value="<?= $list->id ?>">
            <input type="text" name="endpoint" class="form-control" id="brand-name" placeholder="http://domain.com/list.php" value="<?= $list->endpoint ?>">
          </div>
          <button type="submit" class="btn btn-default">Import From API</button>
        </form>
      </div>
      <div class="col-md-5">
        <form class="form-inline" role="form" action="/lists/import/csv" method="post" enctype="multipart/form-data">
          <input type="hidden" name="listId" value="<?= $list->id ?>">
          <div class="form-group">
            <input type="file" name="listFile" id="list-csv">
          </div>
          <button type="submit" class="btn btn-default">Import From CSV</button>
        </form>
      </div>
      <div class="col-md-offset-2"></div>
    </div>
  </div>
</div>
<?php endif; ?>
<div class="row">
  <div class="col-md-12">
    <?php if($subscribers): ?>
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
    <?php else: ?>
      <div class="alert alert-info">
        <p>You do not have any subscribers in <?= $list->name ?> list</p>
      </div>
      <h2>Import From API</h2>
      <form role="form" action="/lists/import/api" method="post">
        <div class="form-group">
          <input type="hidden" name="listId" value="<?= $list->id ?>">
          <input type="text" name="endpoint" class="form-control" id="brand-name" placeholder="http://domain.com/list.php" value="<?= $list->endpoint ?>">
        </div>
        <button type="submit" class="btn btn-default">Import</button>
      </form>
      <h2>Import From CSV</h2>
      <form role="form" action="/lists/import/csv" method="post" enctype="multipart/form-data">
        <input type="hidden" name="listId" value="<?= $list->id ?>">
        <div class="form-group">
          <label for="list-csv">CSV File</label>
          <input type="file" name="listFile" id="list-csv">
        </div>
        <button type="submit" class="btn btn-default">Import</button>
      </form>
    <?php endif; ?>
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
      console.log(data);
      buildRows(data);
    });
  }

  function search(listId, q)
  {
    var postData = {
      'list_id' : listId,
      'q' : q
    };
    $.post('/lsearch', postData, function(data){
      console.log(data);

      buildRows(data);
    });
  }

  function buildRows(data)
  {
    $('#subscribers-list tbody').remove();
    $.each(data.subscribers, function(){
      var s = this;
      var row = $('<tr></tr>');
      $.each(data.columns, function(){
        var c = this.name;
        var column = $('<td>' + s[c] + '</td>');
        row.append(column);
      });
      column = $(
              '<td><a href="/lists/<?= $list->id ?>/delete-subscriber/' + s['id'] +'" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a></td>'
      );
      row.append(column);
      $('#subscribers-list').append(row);
    })
  }

  $('#list-search').on('keyup', function(){
    var v = $(this).val();
    if(v != "")
    {
      search(<?= $list->id ?>, v);
    }
    else
    {
      fetchSubscribers(<?= $list->id ?>, <?= $page ?>);
    }
  });

  fetchSubscribers(<?= $list->id ?>, <?= $page ?>);

</script>
@stop
