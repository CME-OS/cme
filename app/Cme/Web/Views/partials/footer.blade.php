<script src="/assets/js/bootstrap.min.js"></script>
<script src="/assets/js/custom.js"></script>
<script src="/assets/datetimepicker/js/datetimepicker.min.js"></script>
<script>
  if(!window.cme)
  {
    window.cme = {};
  }
  function getPlaceHolders(listIdVal)
  {
    if(listIdVal != "")
    {
      window.cme.listId = listIdVal;
      console.log(listIdVal);
      $('.placeholders').html("");
      $.get('/ph', {listId : listIdVal}, function(data){
        console.log(data);
        $.each(data, function() {
          $('.placeholders').append($("<div />").text(this.name));
        });
      });
    }
  }

  function getFilterData(listIdVal)
  {
    $.ajax({
       type: 'POST',
       url: '/so',
       data: {listId : listIdVal},
       success: function(data){
         window.cme.filterdata = data;
       },
       async:false
    });
  }

  function getSegmentOptions(listIdVal)
  {
    if(!window.cme.filterdata)
    {
      getFilterData(listIdVal);
    }
    var columns = window.cme.filterdata.columns;
    generateSelect2('.filter-field', columns);
    $('.campaign-custom-target').show();
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

  function addFirstFilterRow(listIdVal)
  {
    var templateRow = $('.template-row').first().clone();
    templateRow.removeClass('template-row');
    templateRow.addClass('filter-row');
    $('#filter-table').append(templateRow);
    getSegmentOptions(listIdVal);
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
      var listIdVal = $('#campaign-list-id').val();
      if(listIdVal != '')
      {
        $('#campaign-target-div').show();
      }
      else
      {
        $('#campaign-target-div').hide();
      }

      if(document.getElementById('placeholders'))
      {
        getPlaceHolders(listIdVal)
      }

    });

    $('#campaign-target').change(function(){
      var listIdVal = $('#campaign-list-id').val();
      if ($(this).val() == 'custom')
      {
        addFirstFilterRow(listIdVal);
      }
      else
      {
        $('.campaign-custom-target').empty();
        $('.campaign-custom-target').hide();
      }
    });


    $('#add-filter').click(function(e){
      e.preventDefault();
      var rowCount = $('.filter-row').length;
      if(rowCount > 0)
      {
        var templateRow = $('.filter-row').first().clone();

        //need to change row-id
        var rowId = rowCount + 1;
        templateRow.attr('data-row-id', rowId);

        //empty select boxes (operator & value)
        templateRow.find('.filter-operator').empty()
        templateRow.find('.filter-value').empty()

        $('#filter-table').append(templateRow);
      }
      else
      {
        addFirstFilterRow($('#campaign-list-id').val());
      }
    });

    $('body').on('click', '.remove-filter', function(){
       $(this).closest('tr').remove();
    });

    $('body').on('change', '.filter-field', function(){
      //find row id
      var $this = $(this);
      var row = $($this.closest('tr'));
      //check if filterdata is set and get data if needed
      if(!window.cme.filterdata)
      {
        getFilterData($('#campaign-list-id').val());
      }
      var data = window.cme.filterdata;
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

  if(document.getElementById('campaign-template'))
  {
    console.log('Detected campaign template select field');
    $('#campaign-template').change(function(){
      //grab the content and paste into the edito
      var templateIdVal = $(this).val();
      $.post('/tc', {templateId : templateIdVal}, function(data){
        CKEDITOR.instances['campaign-message'].setData(data.template)
      });
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
