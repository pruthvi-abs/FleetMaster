/**
 * @file
 * Javascript for Wirecard module.
 */

(function ($) {
  Drupal.behaviors.wirecard = {
    attach: function (context, settings) {
      var form_id = 'commerce-wirecard-wpp-form';
      var messages = {
        'failed_to_load': Drupal.t('Failed to load the payment form.'),
        'payment_successful': Drupal.t('Your payment was successful.'),
        'payment_failed': Drupal.t('Your payment failed.'),
        'form_validation_failed': Drupal.t('Form validation failed.'),
      };
      // Wait for the WPP object for 10 seconds.
      wirecard_wait(0, 10, function(){
        $('#'+ form_id).once('commerce-wirecard-wpp', function() {
          wirecard_render_seamless_form(form_id, settings.wirecard.redirect_url, messages);
        });
      }, function (){
        $('#'+form_id).html(messages.failed_to_load);
      });
    }
  }

  /**
   * Function to render the WPP payment form.
   *
   * @param {string} form_id
   * @param {string} paymentRedirectUrl
   * @param {object} messages
   */
  function wirecard_render_seamless_form(form_id, paymentRedirectUrl, messages) {
    WPP.seamlessRender({
      wrappingDivId: form_id,
      url: paymentRedirectUrl,
      onSuccess: function () {
        $('body').delegate('#edit-continue', 'click', function(event) {
          if ($('#edit-commerce-payment-payment-method-wirecard-wppcommerce-payment-wirecard-wpp').is(':checked')) {
            event.preventDefault();
            var $form = $("#edit-continue").closest("form");
            var $submitButtons = $form.find('.checkout-continue');
            $submitButtons.attr("disabled", "disabled");
            $('#commerce-wirecard-wpp-payment-result-title').html('');
            $('#commerce-wirecard-wpp-payment-result-description').html('');

            wirecard_submit_payment(function (response) {
              $('#commerce-wirecard-wpp-payment-response').val(response['response-base64']);
              $('#commerce-wirecard-wpp-payment-response-signature').val(response['response-signature-base64']);
              $form.get(0).submit($form);
            }, function (response){
              $submitButtons.removeAttr("disabled");
            }, messages);
          }
        });
      },
      onError: function (err) {
        $('#commerce-wirecard-wpp-payment-result-title').html(messages.failed_to_load);
      }
    });
  }

  /**
   * Payment form submit handler.
   *
   * @param {function} callback on success
   * @param {function} callback on error
   * @param {object} messages
   */
  function wirecard_submit_payment(callbackSuccess, callbackError, messages) {
    WPP.seamlessSubmit({
      onSuccess: function (response) {
        displaySuccessfulPayment(response, messages);
        callbackSuccess(response);
      },
      onError: function (response) {
        displayError(response, messages);
        callbackError(response);
      }
    });

    /**
     * Callback function to display payment successful message.
     *
     * @param {string} response
     * @param {object} messages
     */
    function displaySuccessfulPayment(response, messages) {
      $('#commerce-wirecard-wpp-payment-result-title').html(messages.payment_successful);
    }
    /**
     * Callback function to display payment error message.
     *
     * @param {string} response
     * @param {object} messages
     */
    function displayError(response, messages) {
      $('#commerce-wirecard-wpp-payment-result-title').html(messages.payment_failed);
      try {
        var decode_reply = JSON.parse(atob(response['response-base64']));
        $('#commerce-wirecard-wpp-payment-result-description').html(Drupal.t(decode_reply.payment.statuses.status[0].description));
      }
      catch(err) {
        if (response.hasOwnProperty('error_1') ) {
          $('#commerce-wirecard-wpp-payment-result-description').html(Drupal.t(response['error_1']));
        }
      }
    }
  }

  /**
   * Helper function to wait until the WPP object will be loaded.
   *
   * @param {int} counter for function execution
   * @param {int} number of times to execute the fcuntion
   * @param {function} callback on success
   * @param {function} callback on reach of maximum executions
   */
  function wirecard_wait(counter, maximum_tries, callbackSuccess, callbackError) {
    if (typeof window.WPP == 'undefined') {
      counter++;
      if (counter <= maximum_tries) {
        setTimeout(function(){
          wirecard_wait(counter, maximum_tries, callbackSuccess, callbackError);
        }, 1000);
      }
      else {
        callbackError();
      }
    }
    else {
      callbackSuccess();
    }
  }

})(jQuery);
