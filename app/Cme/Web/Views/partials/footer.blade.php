<div class="footer container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <p>&copy; Campaign Made Easy <?= date('Y') ?>  -
        <a href="/terms-and-conditions">Terms and Conditions</a> -
        <a href="/privacy-policy">Privacy Policy</a> -
        <a href="/cookie-policy">Cookie Policy</a>
      </p>
      <p><small>We use cookies to help provide you with the best possible online experience. By using this site, you consent that we may store and access cookies on your device.</small></p>
    </div>
  </div>
</div>

<script src="/assets/js/bootstrap.min.js"></script>
<script src="/assets/js/custom.js"></script>
<script src="/assets/datetimepicker/js/datetimepicker.min.js"></script>
<script>
  window.cme = {};
  function getPlaceHolders(listIdVal)
  {
    if(listIdVal != "")
    {
      console.log(listIdVal);
      $('.placeholders').html("");
      $.post('/ph', {listId : listIdVal}, function(data){
        console.log(data);
        $.each(data, function() {
          $('.placeholders').append($("<div />").text(this.name));
        });
      });
    }
  }

  function getSegmentOptions(listIdVal)
  {
    $.post('/so', {listId : listIdVal}, function(data){
      var columns = data.columns;
      window.cme.filterdata = data;
      generateSelect2('.filter-field', columns);
      $('.campaign-custom-target').show();
    });
  }

  function generateSelect(parentRow, target, data)
  {
    var targetX = parentRow.find(target);
    targetX.empty();
    buildSelectOptions(targetX, [{value: "", text : "Select"}]);
    buildSelectOptions(targetX, data);
    targetX.show();
  }

  function generateSelect2(target, data)
  {
    $(target).empty();
    buildSelectOptions(target, [{value: "", text : "Select"}]);
    buildSelectOptions(target, data);
    $(target).show();
  }

  function buildSelectOptions(target, data)
  {
    $.each(data, function (i, item) {
      $(target).append($('<option>', {
        value: item.value,
        text : item.text
      }));
    });
  }


  function getDefaultSender(brandIdVal)
  {
    $.post('/ds', {brandId : brandIdVal}, function(data){
        $('#campaign-from').val(data);
    });
  }

  if(document.getElementById('campaign-list-id'))
  {
    $('#campaign-brand-id').change(function(){
      var brandIdVal = $(this).val();
      getDefaultSender(brandIdVal);
    });

    $('#campaign-list-id').change(function(){
      if($('#campaign-list-id').val() != '')
      {
        $('#campaign-target-div').show();
        $('#campaign-target').change(
          function()
          {
            if ($(this).val() == 'custom')
            {
              getSegmentOptions($('#campaign-list-id').val());
            }
            else
            {
              $('.campaign-custom-target').hide();
            }
          }
        );
      }
      else
      {
        $('#campaign-target-div').hide();
      }
    });


    $('#add-filter').click(function(e){
      e.preventDefault();
      var templateRow = $('.template-row').first().clone();
      templateRow.removeClass('template-row');

      //need to change row-id
      var rowId = $('.filter-row').length + 1;
      templateRow.attr('data-row-id', rowId);

      //empty select boxes (operator & value)
      templateRow.find('.filter-operator').empty()
      templateRow.find('.filter-value').empty()

      $('#filter-table').append(templateRow);
    });

    $('body').on('click', '.remove-filter', function(){
       $(this).closest('tr').remove();
    });

    $('body').on('change', '.filter-field', function(){
      //find row id
      var $this = $(this);
      var row = $($this.closest('tr'));
      var data = window.cme.filterdata;
      //check if filterdata is set and get data if needed

      var fieldValue = $this.val();
      if(fieldValue != "")
      {
        var operatorOptions = data.operators[fieldValue];
        var valueOptions = data.values[fieldValue];

        generateSelect(row, '.filter-operator', operatorOptions);
        generateSelect(row, '.filter-value', valueOptions);
      }
    });

  }

  $(function() {
    $('#datetimepicker').datetimepicker({
      useSeconds: true,
      useCurrent: true,
      format : 'YYYY-MM-DD H:mm:ss'
    });


    $('#send-campaign-btn').click(function(){
      $('#send-form').submit()
    });

    $('#save-campaign-btn').click(function(){
      console.log('saving');
      $('#campaign-form').submit();
    });

    $('#clone-campaign-btn').click(function(){
      console.log('clone');
    });


  });
</script>
