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
        var listIdVal = $(this).val();
        getPlaceHolders(listIdVal);
      });

      getPlaceHolders($('#campaign-list-id').val());
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
