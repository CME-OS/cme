/**
 * Created with JetBrains PhpStorm.
 * User: fobilow
 * Date: 16/08/2014
 * Time: 14:50
 * To change this template use File | Settings | File Templates.
 */
  // This identifies your website in the createToken call below
Stripe.setPublishableKey(stripeKey);

jQuery(function($) {

  $('body').on('click', 'a.ask-more', function(){
    $('.payment-errors').text('');
    $('.credit-card-min').hide();
    $('.credit-card-more').fadeIn('slow');
    $('.pay-btn').html('<button class="continue-btn-sm">Pay</button>');
    $('.back-to-min-btn').html('<a class="back-btn ask-less">Back</a>');
  });

  $('body').on('click', 'a.ask-less', function(){
    $('.credit-card-more').hide();
    $('.credit-card-min').fadeIn('slow');
    $('.pay-btn').html('<a class="continue-btn-sm tapup-btn ask-more">Pay</a>');
    $('.back-to-min-btn').html('<div href="#" class="back-btn" onclick="history.go(-1)">Back</div>')
  });


  $('#payment-form').submit(function(event) {
    var $form = $(this);

    var b = $('.card-num input').val().replace(' ', '').substring(0,6);
    $form.append($('<input type="hidden" name="bin" />').val(b));

    // Disable the submit button to prevent repeated clicks
    $form.find('button').prop('disabled', true);
    Stripe.card.createToken($form, stripeResponseHandler);

    $.get('/log', {payment_method:'stripe'}, function(){
      console.log('done');
    })

    // Prevent the form from submitting with the default action
    return false;
  });
});

function stripeResponseHandler(status, response) {
  var $form = $('#payment-form');
  if (response.error) {
    // Show the errors on the form
    $form.find('.payment-errors').text(response.error.message);
    $form.find('button').prop('disabled', false);
  } else {
    // response contains id and card, which contains additional card details
    var token = response.id;
    // Insert the token into the form so it gets submitted to the server
    $form.append($('<input type="hidden" name="stripeToken" />').val(token));
    // and submit
    $form.get(0).submit();
  }
};


jQuery(function($) {
  $('#paypal-form').submit(function(event) {
    // Disable the submit button to prevent repeated clicks
    $('#paypal-btn').prop('disabled', true);
    console.log('payment method: paypal');
    jQuery.ajax({
      url:    '/log',
      data: {payment_method: 'paypal'},
      success: function(result) {
                   console.log(result)
                  },
      async:   false
    });

    return true;
  });
});
