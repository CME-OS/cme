<form role="form" action="/brands/add" method="post">
  <div class="form-group <?= isset($errors['brand_logo'])? 'has-error has-feedback': '' ?>">
    <label for="brand-logo">Brand Logo URL <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['brand_logo'])? ' - '.$errors['brand_logo']->message: '' ?></span></label>
    <input type="text" name="brand_logo" class="form-control" id="brand-logo" value="<?= isset($input['brand_logo'])? $input['brand_logo'] : '' ?>">
    <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['brand_logo'])? '': 'hidden' ?>" aria-hidden="true"></span>
  </div>
  <div class="form-group <?= isset($errors['brand_name'])? 'has-error has-feedback': '' ?>">
    <label for="brand-name">Name <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['brand_name'])? ' - '.$errors['brand_name']->message: '' ?></span></label>
    <input type="text" name="brand_name" class="form-control" id="brand-name" placeholder="Brand Name" value="<?= isset($input['brand_name'])? $input['brand_name'] : '' ?>">
    <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['brand_name'])? '': 'hidden' ?>" aria-hidden="true"></span>
  </div>
  <div class="form-group <?= isset($errors['brand_sender_name'])? 'has-error has-feedback': '' ?>">
    <label for="sender-name">Default Sender Name <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['brand_sender_name'])? ' - '.$errors['brand_sender_name']->message: '' ?></span></label>
    <input type="text" name="brand_sender_name" class="form-control" id="brand-name" placeholder="Sender Name" value="<?= isset($input['brand_sender_name'])? $input['brand_sender_name'] : '' ?>">
    <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['brand_sender_name'])? '': 'hidden' ?>" aria-hidden="true"></span>
  </div>
  <div class="form-group <?= isset($errors['brand_sender_email'])? 'has-error has-feedback': '' ?>">
    <label for="brand-name">Default Sender Email <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['brand_sender_email'])? ' - '.$errors['brand_sender_email']->message: '' ?></span></label>
    <input type="email" name="brand_sender_email" class="form-control" id="sender-email" placeholder="Sender Email" value="<?= isset($input['brand_sender_email'])? $input['brand_sender_email'] : '' ?>">
    <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['brand_sender_email'])? '': 'hidden' ?>" aria-hidden="true"></span>
  </div>
  <div class="form-group <?= isset($errors['brand_domain_name'])? 'has-error has-feedback': '' ?>">
    <label for="domain-name">Domain Name <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['brand_domain_name'])? ' - '.$errors['brand_domain_name']->message: '' ?></span></label>
    <input type="text" name="brand_domain_name" class="form-control" id="domain-name" placeholder="Brand's Domain Name" value="<?= isset($input['brand_domain_name'])? $input['brand_domain_name'] : '' ?>">
    <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['brand_domain_name'])? '': 'hidden' ?>" aria-hidden="true"></span>
  </div>
  <div class="form-group <?= isset($errors['brand_unsubscribe_url'])? 'has-error has-feedback': '' ?>">
    <label for="unsubscribe-url">Unsubscribe URL <span class="text-danger" style="font-size: 11px; font-style: italic;"><?= isset($errors['brand_unsubscribe_url'])? ' - '.$errors['brand_unsubscribe_url']->message: '' ?></span></label>
    <input type="text" name="brand_unsubscribe_url" class="form-control" id="unsubscribe-url" placeholder="Unsubscribe URL" value="<?= isset($input['brand_unsubscribe_url'])? $input['brand_unsubscribe_url'] : '' ?>">
    <span class="glyphicon glyphicon-remove form-control-feedback <?= isset($errors['brand_unsubscribe_url'])? '': 'hidden' ?>" aria-hidden="true"></span>
  </div>

  <button type="submit" class="btn btn-success">Submit</button>
</form>
