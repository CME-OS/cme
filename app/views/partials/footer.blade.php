<div class="footer container-fluid">
  <div>
    <p>&copy; Campaign Made Easy <?= date('Y') ?>  -
      <a href="/terms-and-conditions">Terms and Conditions</a> -
      <a href="/privacy-policy">Privacy Policy</a> -
      <a href="/cookie-policy">Cookie Policy</a>
    </p>
    <p><small>We use cookies to help provide you with the best possible online experience. By using this site, you consent that we may store and access cookies on your device.</small></p>
  </div>
</div>
<?php if(App::environment() == 'production'): ?>
<!-- Google Analytics Code Here -->
<?php endif; ?>
<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>

